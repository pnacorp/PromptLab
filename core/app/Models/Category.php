<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use GlobalStatus;



    public function prompts()
    {
        return $this->hasMany(Prompt::class);
    }

    public function scopeHasPrompts($query)
    {
        return $query->whereHas('prompts', function ($q) {
            $q->approved();
        });
    }

}
