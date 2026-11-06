<?php

namespace Plugins\InventoryManager\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStockLog extends Model
{
    use HasFactory;

    protected $table = 'inventory_stock_logs';

    protected $fillable = [
        'item_id',
        'type',
        'quantity_change',
        'balance_after',
        'reference_no',
        'note',
        'user_id',
    ];

    protected $casts = [
        'quantity_change' => 'decimal:2',
        'balance_after'   => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

