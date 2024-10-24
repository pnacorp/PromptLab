<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Prompt;
use Illuminate\Http\Request;

class CheckoutController extends Controller {

    public function order() {
        $user       = auth()->user();

        $cartItems   = Cart::cartItems();
        $cartTotal = $cartItems->sum('price');

        if (!$cartTotal) {
            $notify[] = ['error', 'Your cart is empty'];
            return back()->withNotify($notify)->withInput();
        }

        $grandTotal = $cartTotal;
        $coupon = session()->get('coupon');

        $discount = 0;
        $couponId = 0;

        if ($coupon) {
            $couponId   = $coupon['coupon_id'];
            $coupon     = Coupon::where('id', $couponId)->first();

            if (!$coupon) {
                $notify[] = ['error', 'The coupon does not exist'];
                return back()->withNotify($notify)->withInput();
            }

            $discount   = discountAmount($cartTotal, $coupon);
            $grandTotal = $cartTotal - $discount;
        }

        $order                  = new Order();
        $order->user_id         = $user->id;
        $order->order_no        = getTrx();
        $order->subtotal        = $cartTotal;
        $order->discount        = $discount;
        $order->total           = $grandTotal;
        $order->coupon_id       = $couponId;
        $order->coupon_code     = @$coupon->code;
        $order->save();

        session()->forget('coupon');

        foreach ($cartItems as $cart) {
            $orderDetail             = new OrderDetails();
            $orderDetail->order_id   = $order->id;
            $orderDetail->prompt_id  = $cart->prompt_id;
            $orderDetail->seller_id  = $cart->prompt->user_id;
            $orderDetail->price      = $cart->prompt->price;
            $orderDetail->save();
            $cart->delete();
        }

        $notify[] = ['success', 'Order placed successfully'];
        return redirect()->route('user.deposit.index', encrypt($order->id))->withNotify($notify);
    }

    public function applyCoupon(Request $request) {
        if (session('coupon')) {
            return response()->json(['error' => 'A coupon has already been applied. Please remove previous coupon to apply a new coupon.']);
        }

        $coupon = Coupon::active()->where('code', $request->coupon)->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->first();

        if (!$coupon) {
            return response()->json(['error' => 'Invalid coupon code provided']);
        }

        $subtotal = Cart::subTotal();

        if ($coupon->min_order > $subtotal) {
            return response()->json(['error' => 'Sorry, you have to order a minimum amount of ' . showAmount($coupon->min_order)]);
        }

        $discount = discountAmount($subtotal, $coupon);

        $coupon = [
            'coupon_id'     => $coupon->id,
            'code'          => $coupon->code,
            'discount'      => $discount,
        ];

        session()->put('coupon', $coupon);

        return response()->json([
            'success'   => 'Coupon applied successfully',
            'coupon'    => $coupon
        ]);
    }

    public function removeCoupon() {
        session()->forget('coupon');
        return response()->json(['success' => 'Coupon removed successfully']);
    }
}
