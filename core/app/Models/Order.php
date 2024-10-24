<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Deposit::class);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn() => $this->badgeData(),
        );
    }

    public function paymentStatusBadge(): Attribute
    {
        return new Attribute(
            get: fn() => $this->paymentBadgeData(),
        );
    }

    public function badgeData()
    {
        $html = '';
        if ($this->status == Status::ORDER_PENDING) {
            $html = '<span><span class="badge custom--badge badge--warning">' . trans('Pending');
        } elseif ($this->status == Status::ORDER_COMPLETED) {
            $html = '<span><span class="badge custom--badge badge--success">' . trans('Completed');
        } elseif ($this->status == Status::ORDER_CANCEL) {
            $html = '<span><span class="badge custom--badge badge--danger">' . trans('Cancelled');
        }
        return $html;
    }

    public function paymentBadgeData()
    {
        $html = '';
        if ($this->payment_status == Status::ORDER_PAYMENT_PENDING) {
            $html = '<span><span class="badge custom--badge badge--warning">' . trans('Pending');
        } elseif ($this->payment_status == Status::ORDER_PAYMENT_COMPLETED) {
            $html = '<span><span class="badge custom--badge badge--success">' . trans('Paid');
        } elseif ($this->payment_status == Status::ORDER_PAYMENT_REJECTED) {
            $html = '<span><span class="badge custom--badge badge--danger">' . trans('Rejected');
        }
        return $html;
    }

    //Scope
    public function scopePending($query)
    {
        return $query->where('status', Status::ORDER_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', Status::ORDER_COMPLETED)->where('payment_status', Status::ORDER_PAYMENT_COMPLETED);
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', Status::ORDER_CANCEL)->where('payment_status', Status::ORDER_PAYMENT_REJECTED);
    }


    public static function topSellers($limit = null) {
        $topSellers = User::select(
            'users.id',
            'users.username',
            'users.image',
            DB::raw('COUNT(order_details.id) as total_sales'),
        )
            ->join('prompts', 'users.id', '=', 'prompts.user_id')
            ->join('order_details', 'prompts.id', '=', 'order_details.prompt_id')
            ->withCount(['follows as following_count', 'followers as followers_count'])
            ->groupBy('users.id')
            ->orderBy('total_sales', 'desc');

        if ($limit) {
            return $topSellers->limit($limit)->get();
        }

        $topSellers = $topSellers->paginate(getPaginate(30));

        $topSellers->each(function ($user) {
            $user->follower = $user->followers_count;
            $user->following = $user->following_count;
        });

        return $topSellers;
    }


}
