<?php

namespace Plugins\InventoryManager\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Plugins\InventoryManager\Models\InventoryAssignment;
use Plugins\InventoryManager\Models\InventoryStockLog;

class StockMovementManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $activeTab = 'assignments'; // 'assignments' or 'logs'
    public string $search = '';
    public string $filterStatus = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    public bool $showPrintChallan = false;
    public ?InventoryAssignment $printAssignment = null;

    public function printChallan(int $assignmentId): void
    {
        $this->printAssignment = InventoryAssignment::with(['item.category', 'customer', 'technician', 'actionUser'])->find($assignmentId);
        $this->showPrintChallan = true;
    }

    public function closePrintChallan(): void
    {
        $this->showPrintChallan = false;
        $this->printAssignment = null;
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        abortIfNoAccess(['Super Admin', 'Admin', 'Accountant'], ['site-setup', 'manage-expenses']);

        if ($this->activeTab === 'assignments') {
            $query = InventoryAssignment::with(['item.category', 'customer', 'technician', 'actionUser'])
                ->latest('withdraw_date');

            if ($this->search) {
                $s = trim($this->search);
                $query->where(function ($q) use ($s) {
                    $q->where('destination_title', 'like', "%{$s}%")
                        ->orWhere('customer_name', 'like', "%{$s}%")
                        ->orWhere('customer_unique_id', 'like', "%{$s}%")
                        ->orWhereHas('item', function ($iq) use ($s) {
                            $iq->where('title', 'like', "%{$s}%")
                                ->orWhere('serial_number', 'like', "%{$s}%")
                                ->orWhere('mac_address', 'like', "%{$s}%");
                        });
                });
            }

            if ($this->filterStatus) {
                $query->where('status', $this->filterStatus);
            }

            if ($this->filterDateFrom) {
                $query->whereDate('withdraw_date', '>=', $this->filterDateFrom);
            }
            if ($this->filterDateTo) {
                $query->whereDate('withdraw_date', '<=', $this->filterDateTo);
            }

            $records = $query->paginate(20);
        } else {
            $query = InventoryStockLog::with(['item.category', 'user'])
                ->latest('id');

            if ($this->search) {
                $s = trim($this->search);
                $query->where(function ($q) use ($s) {
                    $q->where('note', 'like', "%{$s}%")
                        ->orWhere('reference_no', 'like', "%{$s}%")
                        ->orWhereHas('item', function ($iq) use ($s) {
                            $iq->where('title', 'like', "%{$s}%")
                                ->orWhere('serial_number', 'like', "%{$s}%");
                        });
                });
            }

            if ($this->filterDateFrom) {
                $query->whereDate('created_at', '>=', $this->filterDateFrom);
            }
            if ($this->filterDateTo) {
                $query->whereDate('created_at', '<=', $this->filterDateTo);
            }

            $records = $query->paginate(20);
        }

        return view('inventory-manager::movements', [
            'records' => $records,
        ])->layout('layouts.app');
    }
}

