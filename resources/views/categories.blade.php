<div>
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color:#1a1f36;">
                    <i class="bi bi-tags me-2 text-primary"></i>{{ __('Inventory Categories') }}
                </h4>
                <p class="text-muted small mb-0">{{ __('Manage equipment types, tracking methods, units, and categories') }}</p>
            </div>
            <div class="d-flex gap-2">
                <button wire:click="openCreate" class="btn btn-primary rounded-3 px-4 fw-semibold shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Add New Category') }}
                </button>
                <a href="{{ route('admin.inventory.items') }}" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold">
                    <i class="bi bi-box-seam me-1"></i> {{ __('Item Catalog') }}
                </a>
                <a href="{{ route('admin.inventory.dashboard') }}" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold">
                    <i class="bi bi-speedometer2 me-1"></i> {{ __('Dashboard') }}
                </a>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control rounded-end-3" placeholder="{{ __('Search category name, unit, description...') }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <select wire:model.live="filterTracking" class="form-select form-select-sm rounded-3">
                            <option value="">{{ __('All Tracking Types') }}</option>
                            <option value="serialized">{{ __('Serialized (Routers, ONUs, Switches)') }}</option>
                            <option value="bulk">{{ __('Bulk Stock (Cables, Connectors)') }}</option>
                        </select>
                    </div>
                    @if($search || $filterTracking)
                        <div class="col-auto">
                            <button wire:click="$set('search', ''); $set('filterTracking', '');" class="btn btn-sm btn-light rounded-3 text-muted">
                                <i class="bi bi-x-circle me-1"></i>{{ __('Reset') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Categories Grid --}}
        <div class="row g-3">
            @forelse($categories as $cat)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(37, 99, 235, 0.12) !important;">
                                            <i class="bi {{ $cat->icon ?? 'bi-box-seam' }} fs-5" style="color: #2563eb !important;"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $cat->name }}</h6>
                                            <span class="badge bg-light text-muted border text-uppercase" style="font-size: 9px;">{{ $cat->slug }}</span>
                                        </div>
                                    </div>
                                    @if($cat->tracking_type === 'serialized')
                                        <span class="badge badge-subtle-primary rounded-pill fw-semibold" style="font-size: 10px; padding: 3px 8px;">
                                            {{ __('Serialized') }}
                                        </span>
                                    @else
                                        <span class="badge badge-subtle-success rounded-pill fw-semibold" style="font-size: 10px; padding: 3px 8px;">
                                            {{ __('Bulk') }}
                                        </span>
                                    @endif
                                </div>

                                @if($cat->description)
                                    <p class="text-muted small mb-3" style="font-size: 11.5px; line-height: 1.4;">
                                        {{ Str::limit($cat->description, 80) }}
                                    </p>
                                @endif

                                <div class="bg-light rounded-3 p-2 mb-3">
                                    <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                        <span>{{ __('Default Unit:') }}</span>
                                        <span class="badge bg-white text-dark border fw-bold">{{ $cat->unit ?? 'pcs' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                        <span>{{ __('In Stock:') }}</span>
                                        <span class="fw-bold text-success">{{ $cat->in_stock_qty }} {{ $cat->unit }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                        <span>{{ __('Deployed:') }}</span>
                                        <span class="fw-bold text-primary">{{ $cat->deployed_qty }} {{ $cat->unit }}</span>
                                    </div>
                                    @if($cat->under_repair_qty > 0)
                                        <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                            <span>{{ __('Under Repair:') }}</span>
                                            <span class="fw-bold text-warning">{{ $cat->under_repair_qty }} {{ $cat->unit }}</span>
                                        </div>
                                    @endif
                                    @if($cat->damaged_qty > 0)
                                        <div class="d-flex justify-content-between align-items-center text-muted small">
                                            <span>{{ __('Damaged:') }}</span>
                                            <span class="fw-bold text-danger">{{ $cat->damaged_qty }} {{ $cat->unit }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <span class="small text-muted fw-semibold">
                                    <i class="bi bi-box me-1"></i>{{ $cat->total_qty }} {{ $cat->unit }} ({{ $cat->total_items }} {{ __('Entries') }})
                                </span>
                                <div class="btn-group btn-group-sm">
                                    <button wire:click="openEdit({{ $cat->id }})" class="btn btn-outline-secondary rounded-start-3" title="{{ __('Edit Category') }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($cat->total_items == 0)
                                        <button wire:confirm="{{ __('Are you sure you want to delete this category?') }}" wire:click="delete({{ $cat->id }})" class="btn btn-outline-danger rounded-end-3" title="{{ __('Delete Category') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-outline-secondary rounded-end-3 opacity-50" disabled title="{{ __('Cannot delete category with items') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center text-muted card border-0 shadow-sm rounded-4">
                    <i class="bi bi-tags fs-1 d-block mb-2 text-muted opacity-50"></i>
                    {{ __('No inventory categories found matching your criteria.') }}
                </div>
            @endforelse
        </div>

        @if($categories->hasPages())
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        @endif

    </div>

    {{-- =========================================================================
         MODAL: ADD / EDIT CATEGORY
         ========================================================================= --}}
    @if($showModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="bi bi-tags me-2 text-primary"></i>
                            {{ $editCategoryId ? __('Edit Category') : __('Add New Category') }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">{{ __('Category Name') }} <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control rounded-3 @error('name') is-invalid @enderror" placeholder="{{ __('e.g. OLT, Server, Splitter, Drop Wire, UPS') }}" autofocus>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">{{ __('Tracking Method') }} <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="rounded-3 p-2 text-center" style="cursor: pointer; {{ $tracking_type === 'serialized' ? 'background: rgba(44, 123, 229, 0.08) !important; border: 1.5px solid #2c7be5 !important;' : 'background: #f8fafc !important; border: 1px solid #cbd5e1 !important;' }}" wire:click="$set('tracking_type', 'serialized')">
                                            <input type="radio" wire:model="tracking_type" value="serialized" id="track_ser" class="form-check-input me-1">
                                            <label for="track_ser" class="small fw-bold mb-0 d-block cursor-pointer text-dark">{{ __('Serialized') }}</label>
                                            <small class="text-muted d-block" style="font-size: 10px;">{{ __('Routers, ONUs, S/N, MAC') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="rounded-3 p-2 text-center" style="cursor: pointer; {{ $tracking_type === 'bulk' ? 'background: rgba(44, 123, 229, 0.08) !important; border: 1.5px solid #2c7be5 !important;' : 'background: #f8fafc !important; border: 1px solid #cbd5e1 !important;' }}" wire:click="$set('tracking_type', 'bulk')">
                                            <input type="radio" wire:model="tracking_type" value="bulk" id="track_bulk" class="form-check-input me-1">
                                            <label for="track_bulk" class="small fw-bold mb-0 d-block cursor-pointer text-dark">{{ __('Bulk Stock') }}</label>
                                            <small class="text-muted d-block" style="font-size: 10px;">{{ __('Cables, RJ45, Quantity/Meters') }}</small>
                                        </div>
                                    </div>
                                </div>
                                @error('tracking_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">{{ __('Default Unit of Measurement') }} <span class="text-danger">*</span></label>
                                <input type="text" wire:model="unit" class="form-control rounded-3 @error('unit') is-invalid @enderror" placeholder="{{ __('e.g. pcs, meter, box, packet, roll, set') }}">
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach(['pcs', 'meter', 'roll', 'box', 'packet', 'set', 'feet', 'bundle'] as $presetUnit)
                                        <button type="button" wire:click="setUnit('{{ $presetUnit }}')" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0" style="font-size: 10.5px;">
                                            {{ $presetUnit }}
                                        </button>
                                    @endforeach
                                </div>
                                @error('unit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">{{ __('Icon (Bootstrap Icon)') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi {{ $icon ?: 'bi-box-seam' }} fs-5"></i></span>
                                    <input type="text" wire:model="icon" class="form-control rounded-end-3" placeholder="{{ __('e.g. bi-router, bi-hdd-network') }}">
                                </div>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    @foreach([
                                        'bi-box-seam', 'bi-hdd-network', 'bi-router', 'bi-hdd-rack', 'bi-server',
                                        'bi-diagram-3', 'bi-cpu', 'bi-plug', 'bi-bezier2', 'bi-tools',
                                        'bi-battery-charging', 'bi-camera-video', 'bi-lightning-charge',
                                        'bi-broadcast-pin', 'bi-shield-check', 'bi-tag'
                                    ] as $presetIcon)
                                        <button type="button" wire:click="setIcon('{{ $presetIcon }}')" class="btn btn-sm {{ $icon === $presetIcon ? 'btn-primary' : 'btn-light border' }} rounded-3 p-1 px-2" title="{{ $presetIcon }}">
                                            <i class="bi {{ $presetIcon }}"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">{{ __('Description (Optional)') }}</label>
                                <textarea wire:model="description" rows="2" class="form-control rounded-3" placeholder="{{ __('Short description of this category...') }}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="closeModal" class="btn btn-light rounded-3 px-3">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
                                <i class="bi bi-check-lg me-1"></i> {{ $editCategoryId ? __('Update Category') : __('Save Category') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
