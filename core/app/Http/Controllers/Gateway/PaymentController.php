<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function deposit($hash) {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        $pageTitle = 'Complete Payment';

        $order = Order::findOrFail($id);

        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'order', 'pageTitle'));
    }

    public function depositInsert(Request $request) {
        $request->validate([
            'gateway' => 'required',
            'currency' => 'required',
            'order_id' => 'required',
        ]);

        $user = auth()->user();

        $order = Order::where('id', $request->order_id)->where('user_id', $user->id)->firstOrFail();

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $order->total || $gate->max_amount < $order->total) {
            $notify[] = ['error', 'The order amount exceeds the limit for this payment gateway. Please try using a different one.'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($order->total * $gate->percent_charge / 100);
        $payable = $order->total + $charge;
        $finalAmount = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->order_id = $request->order_id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $order->total;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->success_url = urlPath('user.purchase.details', $order->order_no);
        $data->failed_url = urlPath('user.deposit.history');
        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function appDepositConfirm($hash) {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm() {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit, $isManual = null) {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $order = $deposit->order;
            $order->payment_status = Status::ORDER_PAYMENT_COMPLETED;
            $order->status = Status::ORDER_COMPLETED;
            $order->save();

            $trx = $deposit->trx;

            foreach ($order->orderDetails as $orderDetail) {
                $prompt = $orderDetail->prompt;
                $commission = calculateCommission($orderDetail->price);
                $orderDetail->charge = $commission;
                $orderDetail->save();
                self::paySeller($orderDetail->price, $commission, $prompt, $trx);
            }

            $user = User::find($deposit->user_id);

            $methodName = $deposit->methodName();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Payment Via ' . $methodName;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'payment';
            $transaction->save();

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Payment successful via ' . $methodName;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'prompt_title'    => $prompt->title,
                'prompt_price'    => showAmount($prompt->price, currencyFormat: false),
                'order_no'        => $order->order_no,
                'method_name'     => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
                'amount'          => showAmount($deposit->amount, currencyFormat: false),
                'charge'          => showAmount($deposit->charge, currencyFormat: false),
                'rate'            => showAmount($deposit->rate, currencyFormat: false),
                'trx'             => $deposit->trx,
                'post_balance'    => showAmount($user->balance, currencyFormat: false)
            ]);
        }
    }

    public function manualDepositConfirm() {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Payment';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request) {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $order = Order::find($data->order_id);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Payment request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'order_no'        => $order->order_no,
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount, currencyFormat: false),
            'amount' => showAmount($data->amount, currencyFormat: false),
            'charge' => showAmount($data->charge, currencyFormat: false),
            'rate' => showAmount($data->rate, currencyFormat: false),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have payment request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }

    private static function paySeller($amount, $commissionAmount, $prompt, $trx) {
        $seller = $prompt->user;

        $seller->balance += $amount;
        $seller->total_sold += $amount;
        $seller->save();

        $transaction = new Transaction();
        $transaction->user_id = $seller->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $seller->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Payment received for sale';
        $transaction->trx = $trx;
        $transaction->remark = 'sell';
        $transaction->save();

        $seller->balance -= $commissionAmount;
        $seller->save();

        $transaction = new Transaction();
        $transaction->user_id = $seller->id;
        $transaction->amount = $commissionAmount;
        $transaction->post_balance = $seller->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '-';
        $transaction->details = 'Commission for sale';
        $transaction->trx = $trx;
        $transaction->remark = 'sell_commission';
        $transaction->save();

        notify($seller, 'NEW_SELL', [
            'prompt_title'  => $prompt->title,
            'prompt_price'  => showAmount($prompt->price, currencyFormat: false),
            'amount'        => showAmount($commissionAmount, currencyFormat: false),
            'trx'           => $transaction->trx,
            'commission'    => $commissionAmount,
            'post_balance'  => showAmount($seller->balance, currencyFormat: false)
        ]);
    }
}
