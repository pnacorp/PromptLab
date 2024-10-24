<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrderDetails extends Model
{
    use HasFactory;

    public function prompt()
    {
        return $this->belongsTo(Prompt::class, 'prompt_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'prompt_id', 'prompt_id')
                    ->where('user_id', $this->order->user_id);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn() => $this->badgeData(),
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
}
