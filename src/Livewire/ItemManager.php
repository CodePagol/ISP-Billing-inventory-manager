<?php

namespace Plugins\InventoryManager\Livewire;

use App\Models\CustomersInfo;
use App\Models\IspExpense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Plugins\InventoryManager\Models\InventoryAssignment;
use Plugins\InventoryManager\Models\InventoryCategory;
use Plugins\InventoryManager\Models\InventoryItem;
use Plugins\InventoryManager\Models\InventoryStockLog;

class ItemManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    // Filters & Search
    public string $search = '';
    public string $filterCategory = '';
    public string $filterStatus = '';
    public string $filterTracking = '';

    // Modals visibility
    public bool $showItemModal = false;
    public bool $showDeployModal = false;
    public bool $showDamageModal = false;
    public bool $showReturnModal = false;
    public bool $showHistoryModal = false;

    // Item Form
    public ?int $editItemId = null;
    public ?int $selectedExpenseId = null;
    public bool $auto_create_expense = false;
    public string $entryMode = 'single'; // 'single' or 'batch'
    public string $batch_serials = '';
    public ?int $category_id = null;
    public string $title = '';
    public string $brand = '';
    public string $model_no = '';
    public string $tracking_type = 'serialized';
    public string $serial_number = '';
    public string $mac_address = '';
    public float $quantity = 1;
    public float $purchase_price = 0;
    public string $purchase_date = '';
    public string $supplier = '';
    public int $warranty_months = 0;
    public string $warranty_status = 'under_warranty';
    public string $current_location_name = 'Main Store';
    public string $notes = '';

    // Deploy / Withdraw Form
    public ?int $actionItemId = null;
    public string $deploy_type = 'customer';
    public ?int $deploy_customer_id = null;
    public string $customerSearch = '';
    public Collection $customerSuggestions;
    public ?int $deploy_technician_id = null;
    public string $deploy_destination = '';
    public float $deploy_quantity = 1;
    public string $deploy_date = '';
    public string $deploy_purpose = '';

    // Damage Form
    public string $damage_status = 'damaged'; // 'damaged' or 'under_repair'
    public string $damage_type = 'burned_surge';
    public string $damage_reason = '';
    public string $damage_date = '';
    public bool $replace_with_new = false;   // internal alias
    public bool $replace_for_customer = false; // blade uses this name
    public ?int $replacement_item_id = null;

    // Return Form
    public string $return_date = '';
    public string $return_notes = '';
    public string $return_location = 'Main Store';

    // History Modal
    public ?InventoryItem $viewingItem = null;

    // Challan Print
    public ?InventoryAssignment $printAssignment = null;

    public function boot(): void
    {
        $this->customerSuggestions = collect();
    }

    public function mount(): void
    {
        $this->purchase_date = now()->format('Y-m-d');
        $this->deploy_date = now()->format('Y-m-d');
        $this->damage_date = now()->format('Y-m-d');
        $this->return_date = now()->format('Y-m-d');
        $this->customerSuggestions = collect();

        // Default category
        $firstCat = InventoryCategory::first();
        if ($firstCat) {
            $this->category_id = $firstCat->id;
        }
    }

    public function updatedCustomerSearch(): void
    {
        if (strlen(trim($this->customerSearch)) < 2) {
            $this->customerSuggestions = collect();
            return;
        }

        $term = trim($this->customerSearch);
        $this->customerSuggestions = CustomersInfo::query()
            ->where('customer_name', 'like', "%{$term}%")
            ->orWhere('customer_unique_id', 'like', "%{$term}%")
            ->orWhere('mobile', 'like', "%{$term}%")
            ->take(8)
            ->get(['id', 'customer_unique_id', 'customer_name', 'mobile', 'address']);
    }

    public function selectCustomer(int $id): void
    {
        $cust = CustomersInfo::find($id);
        if ($cust) {
            $this->deploy_customer_id = $cust->id;
            $this->customerSearch = "{$cust->customer_name} ({$cust->customer_unique_id})";
            $this->deploy_destination = "Customer: {$cust->customer_name} ({$cust->customer_unique_id}) - {$cust->address}";
        }
        $this->customerSuggestions = collect();
    }

    public function clearCustomer(): void
    {
        $this->deploy_customer_id = null;
        $this->customerSearch = '';
        $this->customerSuggestions = collect();
    }

    public function updatedSelectedExpenseId($value): void
    {
        if (! $value) {
            return;
        }

        $expense = IspExpense::find($value);
        if ($expense) {
            if (empty($this->title)) {
                $this->title = $expense->title;
            }
            if ($this->purchase_price <= 0) {
                $this->purchase_price = (float) $expense->amount;
            }
            if ($expense->expense_date) {
                $this->purchase_date = Carbon::parse($expense->expense_date)->format('Y-m-d');
            }
            if (! empty($expense->reference_no)) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Expense Ref: " . $expense->reference_no;
            }
        }
    }

    public function updatedCategoryId($value): void
    {
        $cat = InventoryCategory::find($value);
        if ($cat) {
            $this->tracking_type = $cat->tracking_type;
            if ($this->tracking_type === 'serialized') {
                $this->quantity = 1;
            }
        }
    }

    public function openCreateItem(): void
    {
        $this->reset([
            'editItemId', 'selectedExpenseId', 'title', 'brand', 'model_no',
            'serial_number', 'mac_address', 'notes', 'supplier', 'batch_serials'
        ]);
        $this->entryMode = 'single';
        $this->auto_create_expense = true;
        $this->quantity = 1;
        $this->purchase_price = 0;
        $this->warranty_months = 0;
        $this->warranty_status = 'under_warranty';
        $this->current_location_name = 'Main Store';
        $this->purchase_date = now()->format('Y-m-d');

        $firstCat = InventoryCategory::first();
        if ($firstCat) {
            $this->category_id = $firstCat->id;
            $this->tracking_type = $firstCat->tracking_type;
        }

        $this->showItemModal = true;
    }

    public function updatedBatchSerials(): void
    {
        $lines = array_filter(array_map('trim', explode("\n", $this->batch_serials)));
        if (!empty($lines)) {
            $this->quantity = count($lines);
        }
    }

    public function openEditItem(int $id): void
    {
        $item = InventoryItem::findOrFail($id);
        $this->editItemId            = $item->id;
        $this->entryMode             = 'single';
        $this->auto_create_expense   = false;
        $this->category_id           = $item->category_id;
        $this->selectedExpenseId     = $item->expense_id;
        $this->title                 = $item->title;
        $this->brand                 = $item->brand ?? '';
        $this->model_no              = $item->model_no ?? '';
        $this->tracking_type         = $item->tracking_type;
        $this->serial_number         = $item->serial_number ?? '';
        $this->mac_address           = $item->mac_address ?? '';
        $this->quantity              = (float) $item->quantity;
        $this->purchase_price        = (float) $item->purchase_price;
        $this->purchase_date         = $item->purchase_date ? Carbon::parse($item->purchase_date)->format('Y-m-d') : '';
        $this->supplier              = $item->supplier ?? '';
        $this->warranty_months       = (int) $item->warranty_months;
        $this->warranty_status       = $item->warranty_status ?? 'under_warranty';
        $this->current_location_name = $item->current_location_name ?? 'Main Store';
        $this->notes                 = $item->notes ?? '';

        $this->showItemModal = true;
    }

    public function saveItem(): void
    {
        abortIfNoAccess(['Super Admin', 'Admin', 'Accountant'], ['site-setup', 'manage-expenses']);

        // 1. Batch Serialized Entry
        if ($this->entryMode === 'batch' && $this->tracking_type === 'serialized') {
            $this->validate([
                'category_id'           => 'required|exists:inventory_categories,id',
                'title'                 => 'required|string|max:255',
                'brand'                 => 'nullable|string|max:100',
                'model_no'              => 'nullable|string|max:100',
                'purchase_price'        => 'nullable|numeric|min:0',
                'purchase_date'         => 'nullable|date',
                'supplier'              => 'nullable|string|max:150',
                'warranty_months'       => 'nullable|integer|min:0',
                'current_location_name' => 'required|string|max:150',
                'batch_serials'         => 'required|string',
            ]);

            $serials = array_values(array_filter(array_map('trim', explode("\n", $this->batch_serials))));
            if (empty($serials)) {
                $this->addError('batch_serials', 'Please enter at least one serial number.');
                return;
            }

            // Check duplicate serials
            $existing = InventoryItem::whereIn('serial_number', $serials)->pluck('serial_number')->toArray();
            if (!empty($existing)) {
                $this->addError('batch_serials', 'The following serial number(s) already exist: ' . implode(', ', array_slice($existing, 0, 3)));
                return;
            }

            $batchId = 'BATCH-' . strtoupper(\Illuminate\Support\Str::random(8));
            $warrantyExpiry = null;
            if ($this->warranty_months > 0 && $this->purchase_date) {
                $warrantyExpiry = Carbon::parse($this->purchase_date)->addMonths($this->warranty_months)->format('Y-m-d');
            }

            DB::transaction(function () use ($serials, $batchId, $warrantyExpiry) {
                $expenseId = $this->selectedExpenseId;

                if ($this->auto_create_expense && !$expenseId) {
                    $totalAmount = (float) $this->purchase_price * count($serials);
                    $expense = IspExpense::create([
                        'category'     => 'item_purchase',
                        'title'        => "Item Purchase: {$this->title} (" . count($serials) . " pcs)",
                        'description'  => "Batch purchase from {$this->supplier}. Serials count: " . count($serials),
                        'amount'       => $totalAmount,
                        'expense_date' => $this->purchase_date ?: now(),
                        'added_by'     => auth()->id(),
                    ]);
                    $expenseId = $expense->id;
                }

                foreach ($serials as $sn) {
                    $item = InventoryItem::create([
                        'category_id'           => $this->category_id,
                        'batch_id'              => $batchId,
                        'expense_id'            => $expenseId ?: null,
                        'title'                 => $this->title,
                        'brand'                 => $this->brand ?: null,
                        'model_no'              => $this->model_no ?: null,
                        'tracking_type'         => 'serialized',
                        'serial_number'         => $sn,
                        'quantity'              => 1,
                        'purchase_price'        => $this->purchase_price,
                        'purchase_date'         => $this->purchase_date ?: null,
                        'supplier'              => $this->supplier ?: null,
                        'warranty_months'       => $this->warranty_months,
                        'warranty_expiry'       => $warrantyExpiry,
                        'warranty_status'       => 'under_warranty',
                        'status'                => 'in_stock',
                        'current_location_type' => 'warehouse',
                        'current_location_name' => $this->current_location_name,
                        'notes'                 => $this->notes ?: null,
                        'created_by'            => auth()->id(),
                    ]);

                    InventoryStockLog::create([
                        'item_id'         => $item->id,
                        'type'            => 'purchase',
                        'quantity_change' => 1,
                        'balance_after'   => 1,
                        'reference_no'    => $expenseId ? 'EXP-' . $expenseId : null,
                        'note'            => "Batch purchase ({$sn}). Supplier: {$this->supplier}",
                        'user_id'         => auth()->id(),
                    ]);
                }
            });

            flash()->success("Successfully added " . count($serials) . " serialized items to inventory!");
            $this->showItemModal = false;
            return;
        }

        // 2. Single Item Entry
        $rules = [
            'category_id'           => 'required|exists:inventory_categories,id',
            'title'                 => 'required|string|max:255',
            'brand'                 => 'nullable|string|max:100',
            'model_no'              => 'nullable|string|max:100',
            'tracking_type'         => 'required|in:serialized,bulk',
            'quantity'              => 'required|numeric|min:0.01',
            'purchase_price'        => 'nullable|numeric|min:0',
            'purchase_date'         => 'nullable|date',
            'supplier'              => 'nullable|string|max:150',
            'warranty_months'       => 'nullable|integer|min:0',
            'current_location_name' => 'required|string|max:150',
            'notes'                 => 'nullable|string|max:2000',
        ];

        if ($this->tracking_type === 'serialized') {
            $uniqueRule = 'nullable|string|max:100|unique:inventory_items,serial_number';
            if ($this->editItemId) {
                $uniqueRule .= ',' . $this->editItemId;
            }
            $rules['serial_number'] = $uniqueRule;
            $this->quantity = 1;
        }

        $this->validate($rules);

        $warrantyExpiry = null;
        if ($this->warranty_months > 0 && $this->purchase_date) {
            $warrantyExpiry = Carbon::parse($this->purchase_date)->addMonths($this->warranty_months)->format('Y-m-d');
        }

        $expenseId = $this->selectedExpenseId;
        if ($this->auto_create_expense && !$expenseId && !$this->editItemId) {
            $totalAmount = (float) $this->purchase_price * (float) $this->quantity;
            $expense = IspExpense::create([
                'category'     => 'item_purchase',
                'title'        => "Item Purchase: {$this->title}",
                'description'  => "Purchased from {$this->supplier}. Quantity: {$this->quantity}",
                'amount'       => $totalAmount,
                'expense_date' => $this->purchase_date ?: now(),
                'added_by'     => auth()->id(),
            ]);
            $expenseId = $expense->id;
        }

        $data = [
            'category_id'           => $this->category_id,
            'expense_id'            => $expenseId ?: null,
            'title'                 => $this->title,
            'brand'                 => $this->brand ?: null,
            'model_no'              => $this->model_no ?: null,
            'tracking_type'         => $this->tracking_type,
            'serial_number'         => $this->serial_number ?: null,
            'mac_address'           => $this->mac_address ?: null,
            'quantity'              => $this->quantity,
            'purchase_price'        => $this->purchase_price,
            'purchase_date'         => $this->purchase_date ?: null,
            'supplier'              => $this->supplier ?: null,
            'warranty_months'       => $this->warranty_months,
            'warranty_expiry'       => $warrantyExpiry,
            'warranty_status'       => $this->warranty_status ?: 'under_warranty',
            'current_location_name' => $this->current_location_name,
            'notes'                 => $this->notes ?: null,
            'created_by'            => auth()->id(),
        ];

        DB::transaction(function () use ($data) {
            if ($this->editItemId) {
                $item = InventoryItem::findOrFail($this->editItemId);
                $item->update($data);
                flash()->success("Item [{$item->title}] updated successfully.");
            } else {
                $data['status'] = 'in_stock';
                $data['current_location_type'] = 'warehouse';
                $item = InventoryItem::create($data);

                // Audit log
                InventoryStockLog::create([
                    'item_id'         => $item->id,
                    'type'            => 'purchase',
                    'quantity_change' => $item->quantity,
                    'balance_after'   => $item->quantity,
                    'reference_no'    => $item->expense_id ? 'EXP-' . $item->expense_id : null,
                    'note'            => "Initial stock added / purchased. Location: {$item->current_location_name}",
                    'user_id'         => auth()->id(),
                ]);

                flash()->success("Item [{$item->title}] added to inventory successfully.");
            }
        });

        $this->showItemModal = false;
    }

    // ── Deploy / Withdraw ────────────────────────────────────────────────────

    public function openDeploy(int $itemId): void
    {
        $item = InventoryItem::findOrFail($itemId);
        if ($item->status !== 'in_stock' && $item->tracking_type === 'serialized') {
            flash()->warning("This serialized item is already [{$item->status_label}].");
            return;
        }

        $this->actionItemId = $item->id;
        $this->deploy_type = 'customer';
        $this->deploy_customer_id = null;
        $this->customerSearch = '';
        $this->deploy_technician_id = auth()->id();
        $this->deploy_destination = '';
        $this->deploy_quantity = $item->tracking_type === 'serialized' ? 1 : min(1, (float) $item->quantity);
        $this->deploy_date = now()->format('Y-m-d');
        $this->deploy_purpose = '';
        $this->customerSuggestions = collect();

        $this->showDeployModal = true;
    }

    public function submitDeploy(): void
    {
        $item = InventoryItem::findOrFail($this->actionItemId);

        $this->validate([
            'deploy_type'        => 'required|in:customer,pop_point,server_room,staff,other',
            'deploy_destination' => 'required|string|max:255',
            'deploy_date'        => 'required|date',
            'deploy_quantity'    => 'required|numeric|min:0.01|max:' . $item->quantity,
        ]);

        if ($this->deploy_type === 'customer' && ! $this->deploy_customer_id) {
            flash()->error('Please search and select a customer.');
            return;
        }

        $customer = $this->deploy_customer_id ? CustomersInfo::find($this->deploy_customer_id) : null;

        $createdAssign = null;
        DB::transaction(function () use ($item, $customer, &$createdAssign) {
            // 1. Create Assignment record
            $challanNo = InventoryAssignment::generateChallanNo();
            $createdAssign = InventoryAssignment::create([
                'challan_no'          => $challanNo,
                'item_id'             => $item->id,
                'assigned_type'       => $this->deploy_type,
                'customer_id'         => $customer?->id,
                'customer_unique_id'  => $customer?->customer_unique_id,
                'customer_name'       => $customer?->customer_name,
                'assigned_to_user_id' => $this->deploy_technician_id,
                'destination_title'   => $this->deploy_destination,
                'quantity'            => $this->deploy_quantity,
                'withdraw_date'       => $this->deploy_date,
                'status'              => 'deployed',
                'purpose'             => $this->deploy_purpose ?: 'Deployed to ' . $this->deploy_destination,
                'action_by'           => auth()->id(),
            ]);

            // 2. Update Item Status & Location
            if ($item->tracking_type === 'serialized') {
                $item->status = 'deployed';
                $item->current_location_type = $this->deploy_type;
                $item->current_location_name = $this->deploy_destination;
                $item->assigned_customer_id = $customer?->id;
                $item->assigned_user_id = $this->deploy_technician_id;
                $item->save();

                $balanceAfter = 0;
            } else {
                $newQty = max(0, $item->quantity - $this->deploy_quantity);
                $item->quantity = $newQty;
                if ($newQty == 0) {
                    $item->status = 'deployed';
                }
                $item->save();

                $balanceAfter = $newQty;
            }

            // 3. Stock Audit Log
            InventoryStockLog::create([
                'item_id'         => $item->id,
                'type'            => 'withdraw',
                'quantity_change' => -$this->deploy_quantity,
                'balance_after'   => $balanceAfter,
                'reference_no'    => $createdAssign->challan_no,
                'note'            => "Withdrawn / Deployed to: {$this->deploy_destination}. Purpose: {$this->deploy_purpose}",
                'user_id'         => auth()->id(),
            ]);
        });

        if ($createdAssign) {
            $this->printAssignment = $createdAssign->load(['item.category', 'customer', 'technician', 'actionUser']);
        }

        flash()->success("Item successfully withdrawn and assigned to {$this->deploy_destination}!");
        $this->showDeployModal = false;
    }

    // ── Report Damaged ────────────────────────────────────────────────────────

    public function openDamage(int $itemId): void
    {
        $this->actionItemId = $itemId;
        $this->damage_status = 'damaged';
        $this->damage_type = 'burned_surge';
        $this->damage_reason = '';
        $this->damage_date = now()->format('Y-m-d');
        $this->warranty_status = 'under_warranty';
        $this->replace_with_new = false;
        $this->replace_for_customer = false;
        $this->replacement_item_id = null;
        // Load viewingItem so the blade can check assigned_customer_id for replacement section
        $this->viewingItem = InventoryItem::with(['category', 'customer'])->find($itemId);
        $this->showDamageModal = true;
    }

    public function submitDamage(): void
    {
        $this->validate([
            'damage_status'       => 'required|in:damaged,under_repair',
            'damage_type'         => 'required|string|max:100',
            'damage_reason'       => 'required|string|max:1000',
            'damage_date'         => 'required|date',
            'warranty_status'     => 'required|in:under_warranty,out_of_warranty,sent_for_rma,void',
            'replacement_item_id' => ($this->replace_for_customer || $this->replace_with_new) ? 'required|exists:inventory_items,id' : 'nullable',
        ]);

        $item = InventoryItem::findOrFail($this->actionItemId);

        // Sync replace_for_customer → replace_with_new (blade uses replace_for_customer name)
        $doReplace = $this->replace_for_customer || $this->replace_with_new;

        DB::transaction(function () use ($item, $doReplace) {
            $prevStatus = $item->status;
            $item->status = $this->damage_status;
            $item->damage_type = $this->damage_type;
            $item->warranty_status = $this->warranty_status;
            $item->notes = ($item->notes ? $item->notes . "\n" : '') .
                "[" . $this->damage_date . "] Marked as " . strtoupper($this->damage_status) . " ({$this->damage_type}): " . $this->damage_reason;
            $item->save();

            // If it had an active assignment, mark assignment as damaged
            $activeAssignment = $item->assignments()->where('status', 'deployed')->first();
            if ($activeAssignment) {
                $activeAssignment->status = 'damaged';
                $activeAssignment->return_date = $this->damage_date;
                $activeAssignment->return_notes = "Fault/Damage reported ({$this->damage_type}): " . $this->damage_reason;
                $activeAssignment->save();
            }

            InventoryStockLog::create([
                'item_id'         => $item->id,
                'type'            => 'damaged',
                'quantity_change' => 0,
                'balance_after'   => $item->quantity,
                'note'            => "Marked as " . ucfirst($this->damage_status) . " ({$this->damage_type}). Reason: " . $this->damage_reason,
                'user_id'         => auth()->id(),
            ]);

            // If replacement was selected:
            if (($this->replace_for_customer || $this->replace_with_new) && $this->replacement_item_id) {
                $replacement = InventoryItem::findOrFail($this->replacement_item_id);
                $replacement->status = 'deployed';
                $replacement->current_location_type = $item->current_location_type;
                $replacement->current_location_name = $item->current_location_name;
                $replacement->assigned_customer_id = $item->assigned_customer_id;
                $replacement->assigned_user_id = $item->assigned_user_id;
                $replacement->save();

                $challan = InventoryAssignment::generateChallanNo();
                $assign = InventoryAssignment::create([
                    'challan_no'              => $challan,
                    'item_id'                 => $replacement->id,
                    'replacement_for_item_id' => $item->id,
                    'assigned_type'           => $item->current_location_type,
                    'customer_id'             => $item->assigned_customer_id,
                    'customer_unique_id'      => $item->customer?->customer_unique_id,
                    'customer_name'           => $item->customer?->customer_name,
                    'assigned_to_user_id'     => auth()->id(),
                    'destination_title'       => $item->current_location_name,
                    'quantity'                => 1,
                    'withdraw_date'           => $this->damage_date,
                    'status'                  => 'deployed',
                    'purpose'                 => "Replacement for damaged [{$item->title} - SN: {$item->serial_number}]",
                    'action_by'               => auth()->id(),
                ]);

                InventoryStockLog::create([
                    'item_id'         => $replacement->id,
                    'type'            => 'withdraw',
                    'quantity_change' => -1,
                    'balance_after'   => 0,
                    'note'            => "Immediate replacement for damaged Item #{$item->id} ({$item->serial_number})",
                    'user_id'         => auth()->id(),
                ]);

                $this->printAssignment = $assign->load(['item.category', 'customer', 'technician', 'actionUser']);
            }
        });

        flash()->warning("Item [{$item->title}] reported as " . ucfirst($this->damage_status) . ".");
        $this->showDamageModal = false;
    }

    public function getAvailableReplacementItemsProperty()
    {
        if (!$this->actionItemId) {
            return collect();
        }
        $item = InventoryItem::find($this->actionItemId);
        if (!$item) {
            return collect();
        }
        return InventoryItem::where('category_id', $item->category_id)
            ->where('status', 'in_stock')
            ->where('id', '!=', $item->id)
            ->get();
    }

    public function printChallan(int $assignmentId): void
    {
        $this->printAssignment = InventoryAssignment::with(['item.category', 'customer', 'technician', 'actionUser'])->findOrFail($assignmentId);
    }

    public function closePrintChallan(): void
    {
        $this->printAssignment = null;
    }

    // ── Return to Stock ───────────────────────────────────────────────────────

    public function openReturn(int $itemId): void
    {
        $this->actionItemId = $itemId;
        $this->return_date = now()->format('Y-m-d');
        $this->return_notes = '';
        $this->return_location = 'Main Store';
        $this->showReturnModal = true;
    }

    public function submitReturn(): void
    {
        $this->validate([
            'return_date'     => 'required|date',
            'return_location' => 'required|string|max:150',
            'return_notes'    => 'nullable|string|max:1000',
        ]);

        $item = InventoryItem::findOrFail($this->actionItemId);

        DB::transaction(function () use ($item) {
            // Close active assignment if any
            $activeAssignment = $item->assignments()->where('status', 'deployed')->first();
            if ($activeAssignment) {
                $activeAssignment->status = 'returned';
                $activeAssignment->return_date = $this->return_date;
                $activeAssignment->return_notes = $this->return_notes ?: 'Returned to ' . $this->return_location;
                $activeAssignment->save();
            }

            $item->status = 'in_stock';
            $item->current_location_type = 'warehouse';
            $item->current_location_name = $this->return_location;
            $item->assigned_customer_id = null;
            $item->assigned_user_id = null;
            $item->save();

            InventoryStockLog::create([
                'item_id'         => $item->id,
                'type'            => 'return',
                'quantity_change' => 0,
                'balance_after'   => $item->quantity,
                'note'            => "Returned to stock at {$this->return_location}. Notes: {$this->return_notes}",
                'user_id'         => auth()->id(),
            ]);
        });

        flash()->success("Item [{$item->title}] returned to stock at {$this->return_location}!");
        $this->showReturnModal = false;
    }

    // ── View History ─────────────────────────────────────────────────────────

    public function viewHistory(int $itemId): void
    {
        $this->viewingItem = InventoryItem::with(['category', 'expense', 'assignments.customer', 'assignments.technician', 'stockLogs.user'])
            ->findOrFail($itemId);

        $this->showHistoryModal = true;
    }

    public function deleteItem(int $id): void
    {
        abortIfNoAccess(['Super Admin'], ['site-setup']);

        $item = InventoryItem::findOrFail($id);
        $title = $item->title;
        $item->delete();

        flash()->success("Item [{$title}] deleted successfully.");
    }

    public function render()
    {
        abortIfNoAccess(['Super Admin', 'Admin', 'Accountant'], ['site-setup', 'manage-expenses']);

        $query = InventoryItem::with(['category', 'expense', 'customer'])
            ->latest('id');

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('serial_number', 'like', "%{$s}%")
                    ->orWhere('mac_address', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%")
                    ->orWhere('model_no', 'like', "%{$s}%")
                    ->orWhere('supplier', 'like', "%{$s}%")
                    ->orWhere('current_location_name', 'like', "%{$s}%");
            });
        }

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterTracking) {
            $query->where('tracking_type', $this->filterTracking);
        }

        $items = $query->paginate(15);
        $categories = InventoryCategory::orderBy('name')->get();
        $recentExpenses = IspExpense::where('category', 'item_purchase')->latest()->take(25)->get();
        $technicians = User::orderBy('name')->get(['id', 'name']);

        return view('inventory-manager::items', [
            'items'          => $items,
            'categories'     => $categories,
            'recentExpenses' => $recentExpenses,
            'technicians'    => $technicians,
        ])->layout('layouts.app');
    }
}


