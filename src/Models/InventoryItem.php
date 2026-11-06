<?php

namespace Plugins\InventoryManager\Models;

use App\Models\CustomersInfo;
use App\Models\IspExpense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $table = 'inventory_items';

    protected $fillable = [
        'category_id',
        'expense_id',
        'title',
        'brand',
        'model_no',
        'tracking_type',
        'serial_number',
        'mac_address',
        'quantity',
        'purchase_price',
        'purchase_date',
        'supplier',
        'warranty_months',
        'warranty_expiry',
        'status',
        'current_location_type',
        'current_location_name',
        'assigned_customer_id',
        'assigned_user_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity'       => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'purchase_date'  => 'date',
        'warranty_expiry'=> 'date',
    ];

    public static array $statuses = [
        'in_stock'     => 'In Stock',
        'deployed'     => 'Deployed / In Use',
        'under_repair' => 'Under Repair',
        'damaged'      => 'Damaged',
        'retired'      => 'Retired / Disposed',
    ];

    public static array $statusColors = [
        'in_stock'     => 'success',
        'deployed'     => 'primary',
        'under_repair' => 'warning',
        'damaged'      => 'danger',
        'retired'      => 'secondary',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(IspExpense::class, 'expense_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomersInfo::class, 'assigned_customer_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(InventoryAssignment::class, 'item_id')->latest();
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(InventoryStockLog::class, 'item_id')->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'secondary';
    }
}

