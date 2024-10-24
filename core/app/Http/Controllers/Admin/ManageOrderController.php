<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class ManageOrderController extends Controller
{
    public function index($userId = null)
    {
        $pageTitle = 'All Orders';
        $orders    = $this->orderData(userId:$userId);
        return view('admin.order.index', compact('pageTitle', 'orders'));
    }

    public function pending()
    {
        $pageTitle = "Pending Orders";
        $orders = $this->orderData('pending');
        return view('admin.order.index', compact('pageTitle', 'orders'));
    }
    public function approved($userId = null)
    {
        $pageTitle = "Approved Orders";
        $orders = $this->orderData('completed',userId:$userId);
        return view('admin.order.index', compact('pageTitle', 'orders'));
    }
    public function canceled()
    {
        $pageTitle = "Cancel Orders";
        $orders = $this->orderData('canceled');
        return view('admin.order.index', compact('pageTitle', 'orders'));
    }

    protected function orderData($scope = null, $userId = null)
    {
        if ($scope) {
            $orders = Order::$scope();
        } else {
            $orders = Order::query();
        }

        if ($userId) {
            $orders = $orders->where('user_id',$userId);
        }

        return $orders->searchable(['order_no', 'user:username'])->with('orderDetails.prompt', 'user')->orderBy('id', 'DESC')->paginate(getPaginate());
    }

    public function details($id)
    {
        $pageTitle = 'Order Details';
        $order       = Order::with('user', 'orderDetails.prompt')->findOrFail($id);

        return view('admin.order.details', compact('pageTitle', 'order'));
    }

}
