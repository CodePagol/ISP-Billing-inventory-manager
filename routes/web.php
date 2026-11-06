<?php

use Illuminate\Support\Facades\Route;
use Plugins\InventoryManager\Livewire\InventoryDashboard;
use Plugins\InventoryManager\Livewire\ItemManager;
use Plugins\InventoryManager\Livewire\StockMovementManager;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin/inventory')
    ->name('admin.inventory.')
    ->group(function () {
        Route::get('/', InventoryDashboard::class)->name('dashboard');
        Route::get('/items', ItemManager::class)->name('items');
        Route::get('/movements', StockMovementManager::class)->name('movements');
    });
