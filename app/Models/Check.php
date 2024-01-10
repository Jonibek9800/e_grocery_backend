<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Check extends Model
{


    use HasFactory;

    protected $guarded = ["id"];

    public function details(): HasMany
    {
        return $this->hasMany(CheckDetails::class, 'check_id', 'id');
    }
}
