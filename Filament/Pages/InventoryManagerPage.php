<?php

namespace Plugins\InventoryManager\Filament\Pages;

use Filament\Pages\Page;

class InventoryManagerPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationLabel = 'InventoryManager';
    protected static ?string $title           = 'InventoryManager';
    protected static ?string $navigationGroup = 'InventoryManager';
    protected static string  $view            = 'inventory-manager::page';

    // public static function canAccess(): bool
    // {
    //     return auth()->check();
    // }
}