<?php

namespace Plugins\InventoryManager\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Plugins\InventoryManager\Models\InventoryCategory;

class CategoryManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterTracking = '';

    public bool $showModal = false;
    public ?int $editCategoryId = null;

    public string $name = '';
    public string $tracking_type = 'serialized';
    public string $unit = 'pcs';
    public string $icon = 'bi-box-seam';
    public string $description = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTracking(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->editCategoryId = null;
        $this->name = '';
        $this->tracking_type = 'serialized';
        $this->unit = 'pcs';
        $this->icon = 'bi-box-seam';
        $this->description = '';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $cat = InventoryCategory::findOrFail($id);
        $this->editCategoryId = $cat->id;
        $this->name           = $cat->name;
        $this->tracking_type  = $cat->tracking_type;
        $this->unit           = $cat->unit;
        $this->icon           = $cat->icon ?? 'bi-box-seam';
        $this->description    = $cat->description ?? '';
        $this->showModal      = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editCategoryId = null;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function save(): void
    {
        abortIfNoAccess(['Super Admin', 'Admin', 'Accountant'], ['site-setup', 'manage-expenses']);

        $this->validate([
            'name'          => 'required|string|min:2|max:100',
            'tracking_type' => 'required|in:serialized,bulk',
            'unit'          => 'required|string|max:30',
            'icon'          => 'nullable|string|max:50',
            'description'   => 'nullable|string|max:500',
        ]);

        $baseSlug = Str::slug($this->name);
        if (empty($baseSlug)) {
            $baseSlug = 'category-' . time();
        }

        $slug = $baseSlug;
        $counter = 1;
        while (InventoryCategory::where('slug', $slug)
            ->when($this->editCategoryId, fn($q) => $q->where('id', '!=', $this->editCategoryId))
            ->exists()) {
            $slug = "{$baseSlug}-" . (++$counter);
        }

        $data = [
            'name'          => trim($this->name),
            'slug'          => $slug,
            'tracking_type' => $this->tracking_type,
            'unit'          => trim($this->unit) ?: 'pcs',
            'icon'          => trim($this->icon) ?: 'bi-box-seam',
            'description'   => trim($this->description) ?: null,
        ];

        if ($this->editCategoryId) {
            $cat = InventoryCategory::findOrFail($this->editCategoryId);
            $cat->update($data);
            flash()->success(__('Category updated successfully.'));
        } else {
            InventoryCategory::create($data);
            flash()->success(__('Category created successfully.'));
        }

        $this->showModal = false;
        $this->editCategoryId = null;
    }

    public function delete(int $id): void
    {
        abortIfNoAccess(['Super Admin', 'Admin', 'Accountant'], ['site-setup', 'manage-expenses']);

        $cat = InventoryCategory::findOrFail($id);

        if ($cat->items()->exists()) {
            flash()->error(__('Cannot delete category because it has active inventory items.'));
            return;
        }

        $name = $cat->name;
        $cat->delete();
        flash()->success(__('Category [:name] deleted successfully.', ['name' => $name]));
    }

    public function render()
    {
        abortIfNoAccess(['Super Admin', 'Admin', 'Accountant'], ['site-setup', 'manage-expenses']);

        $query = InventoryCategory::withCount([
            'items as total_items',
            'items as in_stock_items' => function ($q) {
                $q->where('status', 'in_stock');
            },
            'items as deployed_items' => function ($q) {
                $q->where('status', 'deployed');
            },
            'items as damaged_items' => function ($q) {
                $q->where('status', 'damaged');
            },
        ]);

        if (!empty($this->search)) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%")
                    ->orWhere('unit', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if (!empty($this->filterTracking)) {
            $query->where('tracking_type', $this->filterTracking);
        }

        $categories = $query->orderBy('name')->paginate(12);

        return view('inventory-manager::categories', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
