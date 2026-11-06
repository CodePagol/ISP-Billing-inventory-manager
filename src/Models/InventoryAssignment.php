<?php

namespace Plugins\InventoryManager\Models;

use App\Models\CustomersInfo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAssignment extends Model
{
    use HasFactory;

    protected $table = 'inventory_assignments';

    protected $fillable = [
        'item_id',
        'assigned_type',
        'customer_id',
        'customer_unique_id',
        'customer_name',
        'assigned_to_user_id',
        'destination_title',
        'quantity',
        'withdraw_date',
        'return_date',
        'status',
        'purpose',
        'return_notes',
        'action_by',
    ];

    protected $casts = [
        'quantity'      => 'decimal:2',
        'withdraw_date' => 'date',
        'return_date'   => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomersInfo::class, 'customer_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function actionUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}

