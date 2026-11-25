<?php

namespace Plugins\InventoryManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('inventory_categories')) {
            return;
        }

        $categories = [
            [
                'name'          => 'ONU / XPON',
                'slug'          => 'onu-xpon',
                'tracking_type' => 'serialized',
                'unit'          => 'pcs',
                'icon'          => 'bi-hdd-network',
                'description'   => 'XPON / EPON / GPON Optical Network Units for customer fiber connections.',
            ],
            [
                'name'          => 'Wi-Fi Router',
                'slug'          => 'wifi-router',
                'tracking_type' => 'serialized',
                'unit'          => 'pcs',
                'icon'          => 'bi-router',
                'description'   => 'Single band and dual-band Wi-Fi customer routers and wireless access points.',
            ],
            [
                'name'          => 'Network Switch',
                'slug'          => 'network-switch',
                'tracking_type' => 'serialized',
                'unit'          => 'pcs',
                'icon'          => 'bi-hdd-rack',
                'description'   => 'Distribution switches, PoE switches, and managed core switches.',
            ],
            [
                'name'          => 'Optical Fiber Cable',
                'slug'          => 'fiber-cable',
                'tracking_type' => 'bulk',
                'unit'          => 'meter',
                'icon'          => 'bi-diagram-3',
                'description'   => '1-Core, 2-Core, 4-Core drop cables and multi-core backbone fiber.',
            ],
            [
                'name'          => 'Media Converter (MC)',
                'slug'          => 'media-converter',
                'tracking_type' => 'serialized',
                'unit'          => 'pcs',
                'icon'          => 'bi-cpu',
                'description'   => 'Optical-to-Ethernet media converters (Tx/Rx pairs).',
            ],
            [
                'name'          => 'Patch Cord & Connectors',
                'slug'          => 'patch-cord',
                'tracking_type' => 'bulk',
                'unit'          => 'pcs',
                'icon'          => 'bi-bezier2',
                'description'   => 'SC/UPC and SC/APC fiber patch cords, fast connectors, and adapters.',
            ],
            [
                'name'          => 'Tools & Accessories',
                'slug'          => 'tools-accessories',
                'tracking_type' => 'bulk',
                'unit'          => 'pcs',
                'icon'          => 'bi-tools',
                'description'   => 'Fusion splicers, optical power meters (OPM), visual fault locators (VFL), and fiber cleavers.',
            ],
            [
                'name'          => 'SFP Module',
                'slug'          => 'sfp-module',
                'tracking_type' => 'serialized',
                'unit'          => 'pcs',
                'icon'          => 'bi-plug',
                'description'   => '1.25G / 10G optical SFP and SFP+ transceivers (single-mode / multi-mode).',
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('inventory_categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                array_merge($cat, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
