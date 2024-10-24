<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function prompt()
    {
        return $this->belongsTo(Prompt::class, 'prompt_id');
    }

    public static function subtotal()
    {
        return self::where('user_id', auth()->id())
        ->whereHas('prompt', function ($prompt) {
            $prompt->approved();
        })
        ->sum('price');
    }

    public static function cartItems()
    {
        return self::where('user_id', auth()->id())
        ->whereHas('prompt', function ($prompt) {
            $prompt->approved();
        })
        ->get();
    }

}
