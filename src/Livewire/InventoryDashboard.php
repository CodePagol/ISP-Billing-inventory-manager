<?php

namespace Plugins\InventoryManager\Livewire;

use Livewire\Component;
use Plugins\InventoryManager\Models\InventoryAssignment;
use Plugins\InventoryManager\Models\InventoryCategory;
use Plugins\InventoryManager\Models\InventoryItem;
use Plugins\InventoryManager\Models\InventoryStockLog;

class InventoryDashboard extends Component
{
    public function render()
    {
        abortIfNoAccess(['Super Admin', 'Admin', 'Accountant'], ['site-setup', 'manage-expenses']);

        $totalItems = InventoryItem::count();
        $inStockCount = InventoryItem::where('status', 'in_stock')->sum('quantity');
        $deployedCount = InventoryItem::where('status', 'deployed')->sum('quantity');
        $damagedCount = InventoryItem::where('status', 'damaged')->sum('quantity');
        $underRepairCount = InventoryItem::where('status', 'under_repair')->sum('quantity');

        $totalValuation = InventoryItem::whereIn('status', ['in_stock', 'deployed'])
            ->selectRaw('SUM(purchase_price * (CASE WHEN tracking_type = "bulk" THEN quantity ELSE 1 END)) as val')
            ->value('val') ?? 0;

        $categories = InventoryCategory::withCount([
            'items as total_items',
            'items as in_stock_items' => function ($q) {
                $q->where('status', 'in_stock');
            },
            'items as deployed_items' => function ($q) {
                $q->where('status', 'deployed');
            },
            'items as damaged_items' => function ($q) {
                $q->where('status', 'damaged');
            },
        ])->get();

        $recentAssignments = InventoryAssignment::with(['item.category', 'customer', 'technician'])
            ->latest()
            ->take(8)
            ->get();

        $recentDamaged = InventoryItem::with('category')
            ->where('status', 'damaged')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('inventory-manager::dashboard', [
            'totalItems'        => $totalItems,
            'inStockCount'      => (float) $inStockCount,
            'deployedCount'     => (float) $deployedCount,
            'damagedCount'      => (float) $damagedCount,
            'underRepairCount'  => (float) $underRepairCount,
            'totalValuation'    => (float) $totalValuation,
            'categories'        => $categories,
            'recentAssignments' => $recentAssignments,
            'recentDamaged'     => $recentDamaged,
        ])->layout('layouts.app');
    }
}

