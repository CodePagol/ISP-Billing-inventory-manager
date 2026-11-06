<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Inventory Categories (e.g. ONU, Router, Switch, Fiber Cable, Media Converter)
        if (! Schema::hasTable('inventory_categories')) {
            Schema::create('inventory_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->enum('tracking_type', ['serialized', 'bulk'])->default('serialized');
                $table->string('unit')->default('pcs'); // pcs, meter, box, packet
                $table->string('icon')->default('bi-box-seam');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Inventory Items (Assets and Bulk stock)
        if (! Schema::hasTable('inventory_items')) {
            Schema::create('inventory_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('inventory_categories')->cascadeOnDelete();
                $table->unsignedBigInteger('expense_id')->nullable()->index(); // linked IspExpense
                $table->string('title'); // e.g. "TP-Link Archer C6 AC1200", "BDCOM XPON ONU"
                $table->string('brand')->nullable();
                $table->string('model_no')->nullable();
                $table->enum('tracking_type', ['serialized', 'bulk'])->default('serialized');
                $table->string('serial_number')->nullable()->index(); // Unique serial for router/onu
                $table->string('mac_address')->nullable()->index();
                $table->decimal('quantity', 12, 2)->default(1); // Bulk quantity (or 1 for serialized)
                $table->decimal('purchase_price', 12, 2)->default(0);
                $table->date('purchase_date')->nullable();
                $table->string('supplier')->nullable();
                $table->integer('warranty_months')->default(0);
                $table->date('warranty_expiry')->nullable();
                $table->enum('status', ['in_stock', 'deployed', 'under_repair', 'damaged', 'retired'])->default('in_stock');
                $table->string('current_location_type')->default('warehouse'); // warehouse, customer, pop_point, server_room, staff
                $table->string('current_location_name')->default('Main Store'); // e.g. "Head Office Store", "Dhanmondi POP", "Client: Rahim"
                $table->unsignedBigInteger('assigned_customer_id')->nullable()->index();
                $table->unsignedBigInteger('assigned_user_id')->nullable()->index(); // staff who holds it
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 3. Inventory Assignments (Withdraw, Deploy, Customer/POP Assignment history)
        if (! Schema::hasTable('inventory_assignments')) {
            Schema::create('inventory_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
                $table->enum('assigned_type', ['customer', 'pop_point', 'server_room', 'staff', 'other'])->default('customer');
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('customer_unique_id')->nullable();
                $table->string('customer_name')->nullable();
                $table->unsignedBigInteger('assigned_to_user_id')->nullable()->index(); // technician/staff
                $table->string('destination_title'); // "Customer: John Doe (CUST-001)" or "Mirpur POP 2"
                $table->decimal('quantity', 12, 2)->default(1);
                $table->date('withdraw_date');
                $table->date('return_date')->nullable();
                $table->enum('status', ['deployed', 'returned', 'damaged', 'lost'])->default('deployed');
                $table->text('purpose')->nullable(); // reason for withdrawal
                $table->text('return_notes')->nullable();
                $table->unsignedBigInteger('action_by')->nullable(); // who performed withdraw
                $table->timestamps();
            });
        }

        // 4. Inventory Stock Logs (Full audit trail of in, out, damage, repair)
        if (! Schema::hasTable('inventory_stock_logs')) {
            Schema::create('inventory_stock_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
                $table->enum('type', ['purchase', 'withdraw', 'return', 'damaged', 'repaired', 'adjustment'])->default('purchase');
                $table->decimal('quantity_change', 12, 2); // e.g. +10 or -1
                $table->decimal('balance_after', 12, 2);
                $table->string('reference_no')->nullable();
                $table->text('note')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_logs');
        Schema::dropIfExists('inventory_assignments');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_categories');
    }
};

