<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $table = 'products';

    // protected $fillable = ['poster_path']; // разрешение на добаление

    protected $guarded = ['id']; // только для защиты полей

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    public function inFavorite()
    {
        return $this->hasOne(WishList::class, 'id', 'product_id');
    }
}
