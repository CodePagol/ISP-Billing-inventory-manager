# Inventory & Asset Manager Plugin for ISP Mikrotik Billing

A comprehensive hardware stock, asset lifecycle, customer deployment, and damage tracking plugin for **ISP Mikrotik Billing** (Laravel 12 / Livewire 4 / Filament 5).

---

## 🌟 Key Features

* **Dual Tracking System:**
  * **Serialized Tracking:** Unique Serial Number & MAC tracking for Wi-Fi Routers, ONUs, Switches, Media Converters.
  * **Bulk Stock Tracking:** Quantity tracking for Optical Fiber Cable (meters), Patch Cords, RJ45 connectors, Tools.
* **Seamless Expense Management Link:**
  * Auto-import / link items directly from **Expense Management** (`item_purchase` category). Auto-fills title, purchase cost, date, and reference vouchers.
* **Customer & Network Deployment (Withdrawal Tracking):**
  * Instant search and assign hardware to specific customers (`CustomersInfo`) or network infrastructure (POP points, server room racks, staff).
  * Records exact withdrawal date, technician name, destination, and assignment status.
* **Damaged / Faulty Hardware Reporting:**
  * Record damaged or faulty devices with incident dates, fault descriptions (thunder/power surge, burned adapter, broken fiber), and status (`damaged` or `under_repair`).
* **Return to Stock Workflow:**
  * When a customer disconnects or a device is repaired, return it back to warehouse inventory with condition notes.
* **Full Audit & Movement Trail:**
  * Comprehensive timeline log of every single stock in, stock out, deployment, damage, and return.

---

## 🚀 Installation

1. Copy or clone this repository into the `plugins/` directory of your ISP Mikrotik Billing project:
   ```bash
   git clone <REPO_URL> plugins/inventory-manager
   ```

2. Run the plugin migration:
   ```bash
   php artisan migrate --path=plugins/inventory-manager/database/migrations
   ```

3. Ensure the plugin is enabled in `config/plugins.php` (enabled by default when using `'enabled' => '*'`).

4. Done! Navigate to **Accounts & Billing &rarr; Inventory & Stock** in the admin sidebar.

---

## 📁 Plugin Architecture

```
plugins/inventory-manager/
├── Plugin.php                      # Main Plugin Entry Point & Hooks
├── plugin.json                     # Plugin Metadata & Configuration
├── config/
│   └── inventory-manager.php       # Plugin settings
├── database/
│   └── migrations/                 # Categories, Items, Assignments, Logs
├── src/
│   ├── Models/                     # Category, Item, Assignment, StockLog
│   └── Livewire/                   # Dashboard, ItemManager, StockMovement
└── resources/
    └── views/                      # Blade UI views
```

---

## 📜 License
Proprietary / MIT - Developed for ISP Mikrotik Billing.
