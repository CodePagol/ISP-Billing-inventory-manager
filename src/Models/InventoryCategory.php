<?php

namespace Plugins\InventoryManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCategory extends Model
{
    use HasFactory;

    protected $table = 'inventory_categories';

    protected $fillable = [
        'name',
        'slug',
        'tracking_type',
        'unit',
        'icon',
        'description',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'category_id');
    }
}

