<?php

namespace Plugins\InventoryManager\Livewire;

use Livewire\Component;
use Plugins\InventoryManager\Models\InventoryAssignment;
use Plugins\InventoryManager\Models\InventoryCategory;
use Plugins\InventoryManager\Models\InventoryItem;
use Plugins\InventoryManager\Models\InventoryStockLog;

class InventoryDashboard extends Component
{
    public string $stockFilter = 'all'; // 'all' or 'active'

    public function render()
    {
        abortIfNoAccess(['Super Admin', 'Admin', 'Accountant'], ['site-setup', 'manage-expenses']);

        $totalItems = InventoryItem::count();
        $inStockCount = (float) InventoryItem::where('status', 'in_stock')->sum('quantity');
        
        $deployedSerialized = (float) InventoryItem::where('tracking_type', 'serialized')
            ->where('status', 'deployed')
            ->sum('quantity');
        $deployedBulk = (float) InventoryAssignment::whereHas('item', fn($q) => $q->where('tracking_type', 'bulk'))
            ->where('status', 'deployed')
            ->sum('quantity');
        $deployedCount = $deployedSerialized + $deployedBulk;

        $damagedCount = (float) InventoryItem::where('status', 'damaged')->sum('quantity');
        $underRepairCount = (float) InventoryItem::where('status', 'under_repair')->sum('quantity');

        $totalValuation = InventoryItem::whereIn('status', ['in_stock', 'deployed'])
            ->selectRaw('SUM(purchase_price * (CASE WHEN tracking_type = "bulk" THEN quantity ELSE 1 END)) as val')
            ->value('val') ?? 0;

        $categories = InventoryCategory::all()->map(function ($cat) {
            $inStockQty = (float) InventoryItem::where('category_id', $cat->id)
                ->where('status', 'in_stock')
                ->sum('quantity');

            $depSerialized = (float) InventoryItem::where('category_id', $cat->id)
                ->where('tracking_type', 'serialized')
                ->where('status', 'deployed')
                ->sum('quantity');

            $depBulk = (float) InventoryAssignment::whereHas('item', fn($q) => $q->where('category_id', $cat->id)->where('tracking_type', 'bulk'))
                ->where('status', 'deployed')
                ->sum('quantity');

            $deployedQty = $depSerialized + $depBulk;

            $underRepairQty = (float) InventoryItem::where('category_id', $cat->id)
                ->where('status', 'under_repair')
                ->sum('quantity');

            $damagedQty = (float) InventoryItem::where('category_id', $cat->id)
                ->where('status', 'damaged')
                ->sum('quantity');

            $retiredQty = (float) InventoryItem::where('category_id', $cat->id)
                ->where('status', 'retired')
                ->sum('quantity');

            $totalQty = $inStockQty + $deployedQty + $underRepairQty + $damagedQty + $retiredQty;

            $cat->in_stock_qty     = $inStockQty;
            $cat->deployed_qty     = $deployedQty;
            $cat->under_repair_qty = $underRepairQty;
            $cat->damaged_qty      = $damagedQty;
            $cat->retired_qty      = $retiredQty;
            $cat->total_qty        = $totalQty;
            $cat->total_items      = InventoryItem::where('category_id', $cat->id)->count();

            return $cat;
        })->sortByDesc(function ($cat) {
            return ($cat->total_qty > 0 ? 1000000 + $cat->total_qty : 0);
        })->values();

        $recentAssignments = InventoryAssignment::with(['item.category', 'customer', 'technician'])
            ->latest()
            ->take(8)
            ->get();

        $recentDamaged = InventoryItem::with('category')
            ->where('status', 'damaged')
            ->latest('updated_at')
            ->take(5)
            ->get();

        $activeCategoriesCount = $categories->where('total_qty', '>', 0)->count();
        $totalCategoriesCount = $categories->count();

        $displayCategories = $this->stockFilter === 'active'
            ? $categories->where('total_qty', '>', 0)
            : $categories;

        return view('inventory-manager::dashboard', [
            'totalItems'             => $totalItems,
            'inStockCount'           => (float) $inStockCount,
            'deployedCount'          => (float) $deployedCount,
            'damagedCount'           => (float) $damagedCount,
            'underRepairCount'       => (float) $underRepairCount,
            'totalValuation'         => (float) $totalValuation,
            'categories'             => $displayCategories,
            'activeCategoriesCount'  => $activeCategoriesCount,
            'totalCategoriesCount'   => $totalCategoriesCount,
            'recentAssignments'      => $recentAssignments,
            'recentDamaged'          => $recentDamaged,
        ])->layout('layouts.app');
    }
}

