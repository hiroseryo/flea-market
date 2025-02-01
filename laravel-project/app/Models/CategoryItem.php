<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryItem extends Pivot
{
    use HasFactory;

    protected $table = 'category_item';

    protected $fillable = [
        'category_id',
        'item_id',
    ];
}
