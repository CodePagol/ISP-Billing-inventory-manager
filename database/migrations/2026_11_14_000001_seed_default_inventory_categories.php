<?php

use Illuminate\Database\Migrations\Migration;
use Plugins\InventoryManager\Database\Seeders\InventoryCategorySeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seederPath = dirname(__DIR__) . '/seeders/InventoryCategorySeeder.php';
        if (file_exists($seederPath)) {
            require_once $seederPath;
            $seeder = new InventoryCategorySeeder();
            $seeder->run();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep categories intact during rollback to protect operational data
    }
};
