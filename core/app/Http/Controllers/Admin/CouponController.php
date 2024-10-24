<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function index(Request $request) {
        $pageTitle    = 'All Coupons';
        $coupons      = Coupon::query();

        if ($request->search) {
            $coupons->where('code', 'LIKE', "%$request->search%");
        }

        $coupons = $coupons->latest()->paginate(getPaginate());
        return view('admin.coupon.index', compact('pageTitle', 'coupons'));
    }

    public function store(Request $request, $id = 0) {

        $request->validate([
            'code'          => 'required|max:40|unique:coupons,code,' . $id,
            'amount'        => 'required|numeric|gt:0',
            "start_date"    => 'required|date_format:Y-m-d',
            "end_date"      => 'required:Y-m-d',
            'discount_type' => 'required|in:1,2',
            'min_order'     => 'required|numeric|gt:0',
        ]);

        if($request->discount_type == 2 && $request->amount > 100){
            $notify[]=['error','Amount must not be greater then 100'];
            return back()->withNotify($notify)->withInput();
        }

        if($id){
            $coupon         = Coupon::findOrFail($request->id);
            $coupon->status = $request->status ? 1 : 0;
            $notification   = 'Coupon updated successfully.';
        }else{
            $coupon         = new Coupon();
            $notification   = 'Coupon added successfully.';
        }

        $coupon->code          = $request->code;
        $coupon->amount        = $request->amount;
        $coupon->start_date    = $request->start_date;
        $coupon->end_date      = $request->end_date;
        $coupon->discount_type = $request->discount_type;
        $coupon->min_order     = $request->min_order;
        $coupon->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Coupon::changeStatus($id);
    }
}
