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
        'batch_id',
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
        'warranty_status',
        'status',
        'damage_type',
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

    public static array $damageTypes = [
        'burned_surge'     => '⚡ Power Surge / Burned',
        'lightning'        => '🌩️ Lightning / Thunder',
        'fiber_cut'        => '✂️ Fiber Cable Cut / Broken Port',
        'water_damage'     => '💧 Water Damage',
        'motherboard_dead' => '🪦 Motherboard / Chipset Dead',
        'adapter_dead'     => '🔌 Power Adapter Dead',
        'firmware_brick'   => '⚠️ Firmware / Software Brick',
        'other'            => '❓ Other Fault',
    ];

    public function getDamageTypeLabelAttribute(): string
    {
        return self::$damageTypes[$this->damage_type] ?? ($this->damage_type ? ucfirst(str_replace('_', ' ', $this->damage_type)) : '');
    }

    public function getWarrantyBadgeAttribute(): string
    {
        if ($this->warranty_status === 'under_warranty') {
            return '<span class="badge badge-subtle-success rounded-pill fw-semibold" style="font-size:11px;"><i class="bi bi-shield-check me-1"></i>Under Warranty</span>';
        }
        if ($this->warranty_status === 'out_of_warranty') {
            return '<span class="badge badge-subtle-secondary rounded-pill fw-semibold" style="font-size:11px;"><i class="bi bi-shield-x me-1"></i>Out of Warranty</span>';
        }
        if ($this->warranty_status === 'sent_for_rma') {
            return '<span class="badge badge-subtle-warning rounded-pill fw-semibold" style="font-size:11px;"><i class="bi bi-arrow-repeat me-1"></i>Sent for RMA</span>';
        }
        if ($this->warranty_status === 'void') {
            return '<span class="badge badge-subtle-danger rounded-pill fw-semibold" style="font-size:11px;"><i class="bi bi-shield-slash me-1"></i>Warranty Void</span>';
        }
        return '';
    }

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

    public function activeAssignment()
    {
        return $this->hasOne(InventoryAssignment::class, 'item_id')->where('status', 'deployed')->latestOfMany();
    }

    public function isSerialized(): bool
    {
        return $this->tracking_type === 'serialized';
    }

    public function isBulk(): bool
    {
        return $this->tracking_type === 'bulk';
    }

    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }

    public function scopeDeployed($query)
    {
        return $query->where('status', 'deployed');
    }

    public function scopeDamaged($query)
    {
        return $query->where('status', 'damaged');
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

