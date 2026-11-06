<?php

namespace Plugins\InventoryManager;

use App\Support\AbstractPlugin;
use App\Support\Hooks;
use Filament\Navigation\NavigationItem;
use Illuminate\Foundation\Application;
use Livewire\Livewire;
use Plugins\InventoryManager\Livewire\InventoryDashboard;
use Plugins\InventoryManager\Livewire\ItemManager;
use Plugins\InventoryManager\Livewire\StockMovementManager;
use Plugins\InventoryManager\Models\InventoryCategory;

class Plugin extends AbstractPlugin
{
    public function getId(): string      { return 'inventory-manager'; }
    public function getName(): string    { return 'Inventory & Asset Manager'; }
    public function getVersion(): string { return '1.0.0'; }
    public function getAuthor(): string  { return 'Code Pagol Ltd'; }

    public function getDescription(): string
    {
        return 'Complete ISP inventory, asset tracking, client deployments, damage reporting, and expense linking.';
    }

    public function register(Application $app): void
    {
        // Register PSR-4 autoloader for plugin namespace
        spl_autoload_register(function ($class) {
            $prefix = 'Plugins\\InventoryManager\\';
            $baseDir = __DIR__ . '/src/';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }

    public function boot(Application $app): void
    {
        parent::boot($app);

        // Register Livewire components (using standard hyphen and dot notation without colons)
        if (class_exists(Livewire::class)) {
            Livewire::component('inventory-dashboard', InventoryDashboard::class);
            Livewire::component('inventory-items', ItemManager::class);
            Livewire::component('inventory-movements', StockMovementManager::class);

            Livewire::component('inventory.dashboard', InventoryDashboard::class);
            Livewire::component('inventory.items', ItemManager::class);
            Livewire::component('inventory.movements', StockMovementManager::class);
        }
    }

    public function getMigrationPaths(): array
    {
        return [$this->pluginPath('database/migrations')];
    }

    public function getRouteFiles(): array
    {
        return [$this->pluginPath('routes/web.php')];
    }

    public function getViewNamespaces(): array
    {
        return ['inventory-manager' => $this->pluginPath('resources/views')];
    }

    public function getConfigFiles(): array
    {
        return [$this->pluginPath('config/inventory-manager.php') => 'inventory-manager'];
    }

    protected function registerHooks(): void
    {
        // 1. Add Navigation to Admin Sidebar
        Hooks::filter('sidebar.nav.items', function (array $items) {
            $items[] = [
                'id'       => 'inventory-management',
                'title'    => 'Inventory & Stock',
                'icon'     => 'bi bi-boxes',
                'section'  => 'Accounts & Billing',
                'route'    => 'admin.inventory.dashboard',
                'children' => [
                    [
                        'title' => 'Dashboard',
                        'route' => 'admin.inventory.dashboard',
                        'icon'  => 'bi bi-speedometer2',
                    ],
                    [
                        'title' => 'Item Catalog & Stock',
                        'route' => 'admin.inventory.items',
                        'icon'  => 'bi bi-box-seam',
                    ],
                    [
                        'title' => 'Deploy & Withdrawals',
                        'route' => 'admin.inventory.movements',
                        'icon'  => 'bi bi-clock-history',
                    ],
                ],
            ];
            return $items;
        });
    }

    public function install(): void
    {
        // Seed default inventory categories
        $defaults = [
            ['name' => 'ONU / XPON', 'slug' => 'onu-xpon', 'tracking_type' => 'serialized', 'unit' => 'pcs', 'icon' => 'bi-hdd-network'],
            ['name' => 'Wi-Fi Router', 'slug' => 'wifi-router', 'tracking_type' => 'serialized', 'unit' => 'pcs', 'icon' => 'bi-router'],
            ['name' => 'Network Switch', 'slug' => 'network-switch', 'tracking_type' => 'serialized', 'unit' => 'pcs', 'icon' => 'bi-hdd-rack'],
            ['name' => 'Optical Fiber Cable', 'slug' => 'fiber-cable', 'tracking_type' => 'bulk', 'unit' => 'meter', 'icon' => 'bi-diagram-3'],
            ['name' => 'Media Converter (MC)', 'slug' => 'media-converter', 'tracking_type' => 'serialized', 'unit' => 'pcs', 'icon' => 'bi-cpu'],
            ['name' => 'SFP Module', 'slug' => 'sfp-module', 'tracking_type' => 'serialized', 'unit' => 'pcs', 'icon' => 'bi-plug'],
            ['name' => 'Patch Cord & Connectors', 'slug' => 'patch-cord', 'tracking_type' => 'bulk', 'unit' => 'pcs', 'icon' => 'bi-bezier2'],
            ['name' => 'Tools & Accessories', 'slug' => 'tools-accessories', 'tracking_type' => 'bulk', 'unit' => 'pcs', 'icon' => 'bi-tools'],
        ];

        foreach ($defaults as $cat) {
            InventoryCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
