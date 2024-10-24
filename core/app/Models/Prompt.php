<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Prompt extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'prompt_id');
    }

    public function toolVersion()
    {
        return $this->belongsTo(ToolVersion::class);
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function promptImages()
    {
        return $this->hasMany(PromptImage::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'prompt_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'prompt_id', 'user_id');
    }

    public function promptStatusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::PROMPT_APPROVED) {
                $html = '<span class="badge custom--badge badge--success">' . trans("Approved") . '</span>';
            } elseif ($this->status == Status::PROMPT_PENDING) {
                $html = '<span class="badge custom--badge badge--warning">' . trans("Pending") . '</span>';
            } elseif ($this->status == Status::PROMPT_REJECTED) {
                $html = '<span class="badge custom--badge badge--danger">' . trans("Rejected") . '</span>';
            }
            return $html;
        });
    }


    public function getSalesCountAttribute(): int
    {
        return $this->orderDetails()
            ->whereHas('order', function ($query) {
                $query->where('status', Status::ORDER_COMPLETED);
            })
            ->count();
    }

    //Scope
    public function scopePending($query)
    {
        return $query->where('status', Status::PROMPT_PENDING);
    }
    public function scopeApproved($query)
    {
        return $query->where('status', Status::PROMPT_APPROVED);
    }
    public function scopeRejected($query)
    {
        return $query->where('status', Status::PROMPT_REJECTED);
    }

    public function scopeTrending($query)
    {
        return $query->withCount(['orderDetails' => function($q){
            $q->whereHas('order', function($order){
                $order->where('status', Status::ORDER_COMPLETED);
            })
            ->whereDate('created_at', '>=' ,now()->subDays(15));
        }])
        ->orderBy('order_details_count', 'DESC');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', Status::PROMPT_FEATURED);
    }

    public function scopeSimilarPrompts($query, $prompt) {
        return $query->approved()
        ->where('id', '!=', $prompt->id)
        ->where(function ($query) use ($prompt) {
            $query->where('category_id', $prompt->category_id)
                ->orWhere('tool_id', $prompt->tool_id);
        })
        ->inRandomOrder();
    }

    public function getAvgRating() {
        return $this->reviews()->avg('rating') ?? 0;
    }
}
