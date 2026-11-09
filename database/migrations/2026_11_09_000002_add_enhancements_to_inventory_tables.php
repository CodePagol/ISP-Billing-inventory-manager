<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_items', 'damage_type')) {
                    $table->string('damage_type')->nullable()->after('status');
                }
                if (!Schema::hasColumn('inventory_items', 'warranty_status')) {
                    $table->string('warranty_status')->default('valid')->after('warranty_expiry'); // valid, expired, claimed, replaced, scrapped
                }
                if (!Schema::hasColumn('inventory_items', 'batch_id')) {
                    $table->string('batch_id')->nullable()->index()->after('category_id');
                }
            });
        }

        if (Schema::hasTable('inventory_assignments')) {
            Schema::table('inventory_assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_assignments', 'challan_no')) {
                    $table->string('challan_no')->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('inventory_assignments', 'replacement_for_item_id')) {
                    $table->unsignedBigInteger('replacement_for_item_id')->nullable()->after('item_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->dropColumn(['damage_type', 'warranty_status', 'batch_id']);
            });
        }

        if (Schema::hasTable('inventory_assignments')) {
            Schema::table('inventory_assignments', function (Blueprint $table) {
                $table->dropColumn(['challan_no', 'replacement_for_item_id']);
            });
        }
    }
};
