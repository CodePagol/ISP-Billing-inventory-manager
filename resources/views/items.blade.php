<div>
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color:#1a1f36;">
                    <i class="bi bi-box-seam me-2 text-primary"></i>{{ __('Item & Stock Catalog') }}
                </h4>
                <p class="text-muted small mb-0">{{ __('Manage equipment, routers, ONUs, cables, and assignments') }}</p>
            </div>
            <div class="d-flex gap-2">
                <button wire:click="openCreateItem" class="btn btn-primary rounded-3 px-4 fw-semibold shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Add New Item') }}
                </button>
                <a href="{{ route('admin.inventory.dashboard') }}" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold">
                    <i class="bi bi-speedometer2 me-1"></i> {{ __('Dashboard') }}
                </a>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body py-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted mb-1">{{ __('Search Items') }}</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control rounded-end-3" placeholder="{{ __('Model, Serial, MAC, Brand, Location...') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">{{ __('Category') }}</label>
                        <select wire:model.live="filterCategory" class="form-select form-select-sm rounded-3">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">{{ __('Status') }}</label>
                        <select wire:model.live="filterStatus" class="form-select form-select-sm rounded-3">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="in_stock">{{ __('In Stock') }}</option>
                            <option value="deployed">{{ __('Deployed / In Use') }}</option>
                            <option value="under_repair">{{ __('Under Repair') }}</option>
                            <option value="damaged">{{ __('Damaged') }}</option>
                            <option value="retired">{{ __('Retired') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">{{ __('Type') }}</label>
                        <select wire:model.live="filterTracking" class="form-select form-select-sm rounded-3">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="serialized">{{ __('Serialized') }}</option>
                            <option value="bulk">{{ __('Bulk (Quantity)') }}</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <button wire:click="$set('search', ''); $set('filterCategory', ''); $set('filterStatus', ''); $set('filterTracking', '');" class="btn btn-sm btn-light w-100 rounded-3 text-muted" title="{{ __('Reset filters') }}">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted small text-uppercase">
                            <th>{{ __('Item & Category') }}</th>
                            <th>{{ __('Identifiers / Serial') }}</th>
                            <th>{{ __('Stock / Qty') }}</th>
                            <th>{{ __('Current Location / Destination') }}</th>
                            <th>{{ __('Price & Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->title }}</div>
                                    <div class="small text-muted">
                                        <i class="bi {{ $item->category?->icon ?? 'bi-tag' }} text-primary me-1"></i>
                                        <span>{{ $item->category?->name }}</span>
                                        @if($item->brand)
                                            &bull; <span>{{ $item->brand }}</span>
                                        @endif
                                        @if($item->expense)
                                            &bull; <span class="badge bg-light text-dark border"><i class="bi bi-wallet2 text-danger me-1"></i>{{ $item->expense->reference_no }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($item->tracking_type === 'serialized')
                                        @if($item->serial_number)
                                            <div><span class="badge bg-light text-dark border">SN: <code>{{ $item->serial_number }}</code></span></div>
                                        @endif
                                        @if($item->mac_address)
                                            <div><small class="text-muted">MAC: <code>{{ $item->mac_address }}</code></small></div>
                                        @endif
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ __('Bulk Managed') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ number_format($item->quantity, $item->tracking_type === 'bulk' ? 2 : 0) }}</span>
                                    <small class="text-muted">{{ $item->category?->unit ?? 'pcs' }}</small>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ $item->current_location_name }}
                                    </div>
                                    @if($item->assigned_customer_id && $item->customer)
                                        <small class="text-primary d-block">
                                            <i class="bi bi-person me-1"></i>{{ $item->customer->customer_name }} ({{ $item->customer->customer_unique_id }})
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">৳ {{ number_format($item->purchase_price, 2) }}</div>
                                    <small class="text-muted">{{ $item->purchase_date ? $item->purchase_date->format('d M, Y') : '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->status_color }} rounded-pill px-3 py-1">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        {{-- Deploy Button --}}
                                        @if($item->status === 'in_stock' || ($item->tracking_type === 'bulk' && $item->quantity > 0))
                                            <button wire:click="openDeploy({{ $item->id }})" class="btn btn-outline-primary" title="{{ __('Withdraw / Deploy') }}">
                                                <i class="bi bi-arrow-up-right-circle"></i>
                                            </button>
                                        @endif

                                        {{-- Return Button --}}
                                        @if($item->status === 'deployed' || $item->status === 'under_repair')
                                            <button wire:click="openReturn({{ $item->id }})" class="btn btn-outline-success" title="{{ __('Return to Stock') }}">
                                                <i class="bi bi-arrow-down-left-circle"></i>
                                            </button>
                                        @endif

                                        {{-- Report Damaged Button --}}
                                        @if($item->status !== 'damaged' && $item->status !== 'retired')
                                            <button wire:click="openDamage({{ $item->id }})" class="btn btn-outline-danger" title="{{ __('Report Damaged / Fault') }}">
                                                <i class="bi bi-exclamation-triangle"></i>
                                            </button>
                                        @endif

                                        {{-- History Button --}}
                                        <button wire:click="viewHistory({{ $item->id }})" class="btn btn-outline-secondary" title="{{ __('Audit History') }}">
                                            <i class="bi bi-clock-history"></i>
                                        </button>

                                        {{-- Edit Button --}}
                                        <button wire:click="openEditItem({{ $item->id }})" class="btn btn-outline-dark" title="{{ __('Edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Delete Button --}}
                                        <button wire:confirm="{{ __('Are you sure you want to delete this item?') }}" wire:click="deleteItem({{ $item->id }})" class="btn btn-outline-danger" title="{{ __('Delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted opacity-50"></i>
                                    {{ __('No inventory items found matching your criteria.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($items->hasPages())
                <div class="card-footer bg-white py-3 border-0">
                    {{ $items->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- =========================================================================
         MODAL 1: ADD / EDIT ITEM
         ========================================================================= --}}
    @if($showItemModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="bi bi-box-seam me-2 text-primary"></i>
                            {{ $editItemId ? __('Edit Inventory Item') : __('Add New Item to Inventory') }}
                        </h5>
                        <button type="button" wire:click="$set('showItemModal', false)" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="saveItem">
                        <div class="modal-body">
                            {{-- Link with Expense Purchase Option --}}
                            @if(!$editItemId)
                                <div class="p-3 mb-3 rounded-3 bg-light border border-primary border-opacity-25">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label small fw-bold text-primary mb-0">
                                            <i class="bi bi-wallet2 text-danger me-1"></i> {{ __('Link with Expense Management (Optional)') }}
                                        </label>
                                        <small class="text-muted">{{ __('Auto-fill from Item Purchase Expense') }}</small>
                                    </div>
                                    <select wire:model.live="selectedExpenseId" class="form-select form-select-sm rounded-3">
                                        <option value="">{{ __('-- Select Expense Record (Optional) --') }}</option>
                                        @foreach($recentExpenses as $exp)
                                            <option value="{{ $exp->id }}">
                                                {{ $exp->reference_no }} &bull; {{ $exp->title }} (৳ {{ number_format($exp->amount, 2) }} - {{ $exp->expense_date?->format('d M Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="row g-3">
                                {{-- Category --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Category') }} <span class="text-danger">*</span></label>
                                    <select wire:model.live="category_id" class="form-select rounded-3 @error('category_id') is-invalid @enderror">
                                        <option value="">{{ __('-- Select Category --') }}</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }} ({{ ucfirst($cat->tracking_type) }})</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Tracking Type --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Tracking Method') }}</label>
                                    <select wire:model.live="tracking_type" class="form-select rounded-3">
                                        <option value="serialized">{{ __('Serialized (Individual Serial/MAC - Routers, ONUs, Switches)') }}</option>
                                        <option value="bulk">{{ __('Bulk Stock (Quantity/Meters - Fiber cable, RJ45)') }}</option>
                                    </select>
                                </div>

                                {{-- Title / Model Name --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Item Title / Model Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="title" class="form-control rounded-3 @error('title') is-invalid @enderror" placeholder="{{ __('e.g. TP-Link Archer C6 AC1200') }}">
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Brand --}}
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">{{ __('Brand / Manufacturer') }}</label>
                                    <input type="text" wire:model="brand" class="form-control rounded-3" placeholder="{{ __('e.g. TP-Link, BDCOM, Cisco') }}">
                                </div>

                                {{-- Model No --}}
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">{{ __('Model No') }}</label>
                                    <input type="text" wire:model="model_no" class="form-control rounded-3" placeholder="{{ __('e.g. Archer-C6-V3') }}">
                                </div>

                                @if($tracking_type === 'serialized')
                                    {{-- Serial Number --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">{{ __('Serial Number (S/N)') }}</label>
                                        <input type="text" wire:model="serial_number" class="form-control rounded-3 @error('serial_number') is-invalid @enderror" placeholder="{{ __('Unique Serial Number') }}">
                                        @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- MAC Address --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">{{ __('MAC Address') }}</label>
                                        <input type="text" wire:model="mac_address" class="form-control rounded-3" placeholder="{{ __('e.g. 00:1A:2B:3C:4D:5E') }}">
                                    </div>
                                @else
                                    {{-- Quantity --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">{{ __('Stock Quantity') }} <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" wire:model="quantity" class="form-control rounded-3 @error('quantity') is-invalid @enderror" placeholder="{{ __('e.g. 305 (meters) or 100 (pcs)') }}">
                                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                @endif

                                {{-- Purchase Price --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">{{ __('Purchase Price (৳)') }}</label>
                                    <input type="number" step="0.01" wire:model="purchase_price" class="form-control rounded-3" placeholder="0.00">
                                </div>

                                {{-- Purchase Date --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">{{ __('Purchase Date') }}</label>
                                    <input type="date" wire:model="purchase_date" class="form-control rounded-3">
                                </div>

                                {{-- Warranty Months --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">{{ __('Warranty (Months)') }}</label>
                                    <input type="number" wire:model="warranty_months" class="form-control rounded-3" placeholder="{{ __('e.g. 12') }}">
                                </div>

                                {{-- Supplier --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Supplier / Vendor') }}</label>
                                    <input type="text" wire:model="supplier" class="form-control rounded-3" placeholder="{{ __('e.g. Computer City, Tech Land') }}">
                                </div>

                                {{-- Storage Location --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Warehouse / Store Location') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="current_location_name" class="form-control rounded-3 @error('current_location_name') is-invalid @enderror" placeholder="{{ __('e.g. Head Office Shelf A, Store Room') }}">
                                    @error('current_location_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Notes --}}
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">{{ __('Notes / Specifications') }}</label>
                                    <textarea wire:model="notes" rows="2" class="form-control rounded-3" placeholder="{{ __('Additional details or specs...') }}"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showItemModal', false)" class="btn btn-light rounded-3 px-3">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">{{ __('Save Item') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================================
         MODAL 2: DEPLOY / WITHDRAW ITEM
         ========================================================================= --}}
    @if($showDeployModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="bi bi-arrow-up-right-circle text-primary me-2"></i>{{ __('Withdraw / Deploy Item') }}
                        </h5>
                        <button type="button" wire:click="$set('showDeployModal', false)" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="submitDeploy">
                        <div class="modal-body">
                            <div class="row g-3">
                                {{-- Destination Type --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Deploy To (Target Type)') }}</label>
                                    <select wire:model.live="deploy_type" class="form-select rounded-3">
                                        <option value="customer">{{ __('Customer House / Office') }}</option>
                                        <option value="pop_point">{{ __('POP Room / Distribution Point') }}</option>
                                        <option value="server_room">{{ __('Server Room / Data Center Rack') }}</option>
                                        <option value="staff">{{ __('Field Staff / Technician Handover') }}</option>
                                        <option value="other">{{ __('Other Location') }}</option>
                                    </select>
                                </div>

                                {{-- Withdraw Date --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Withdrawal Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="deploy_date" class="form-control rounded-3 @error('deploy_date') is-invalid @enderror">
                                    @error('deploy_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Customer Search (if deploy_type === 'customer') --}}
                                @if($deploy_type === 'customer')
                                    <div class="col-12 position-relative">
                                        <label class="form-label small fw-semibold">{{ __('Search & Select Customer') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" wire:model.live="customerSearch" class="form-control rounded-start-3" placeholder="{{ __('Type customer name, ID, or mobile...') }}">
                                            @if($deploy_customer_id)
                                                <button type="button" wire:click="clearCustomer" class="btn btn-outline-danger">{{ __('Clear') }}</button>
                                            @endif
                                        </div>

                                        {{-- Suggestions Dropdown --}}
                                        @if($customerSuggestions->isNotEmpty())
                                            <div class="list-group position-absolute w-100 shadow-lg mt-1 rounded-3 z-3 bg-white" style="max-height: 200px; overflow-y: auto;">
                                                @foreach($customerSuggestions as $sug)
                                                    <a href="javascript:void(0)" wire:click="selectCustomer({{ $sug->id }})" class="list-group-item list-group-item-action py-2">
                                                        <div class="fw-bold text-dark">{{ $sug->customer_name }} <span class="badge bg-secondary">{{ $sug->customer_unique_id }}</span></div>
                                                        <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $sug->mobile }} &bull; {{ $sug->address }}</small>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Destination / Note --}}
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">{{ __('Destination Title / Description') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="deploy_destination" class="form-control rounded-3 @error('deploy_destination') is-invalid @enderror" placeholder="{{ __('e.g. Mirpur POP-2 Rack 3, or Customer Address') }}">
                                    @error('deploy_destination') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Assigned Technician --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Handed Over / Technician') }}</label>
                                    <select wire:model="deploy_technician_id" class="form-select rounded-3">
                                        <option value="">{{ __('-- Select Staff / Tech --') }}</option>
                                        @foreach($technicians as $tech)
                                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Deploy Quantity --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Quantity') }}</label>
                                    <input type="number" step="0.01" wire:model="deploy_quantity" class="form-control rounded-3 @error('deploy_quantity') is-invalid @enderror">
                                    @error('deploy_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Purpose / Remarks --}}
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">{{ __('Purpose / Reason') }}</label>
                                    <textarea wire:model="deploy_purpose" rows="2" class="form-control rounded-3" placeholder="{{ __('e.g. New connection setup, line extension...') }}"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showDeployModal', false)" class="btn btn-light rounded-3 px-3">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">{{ __('Confirm Withdraw & Deploy') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================================
         MODAL 3: REPORT DAMAGED / BROKEN
         ========================================================================= --}}
    @if($showDamageModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="bi bi-exclamation-octagon text-danger me-2"></i>{{ __('Report Damaged / Faulty Item') }}
                        </h5>
                        <button type="button" wire:click="$set('showDamageModal', false)" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="submitDamage">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Status Type') }}</label>
                                    <select wire:model="damage_status" class="form-select rounded-3">
                                        <option value="damaged">{{ __('Damaged / Broken (নষ্ট হয়ে গেছে)') }}</option>
                                        <option value="under_repair">{{ __('Under Repair (মেরামতে পাঠানো হয়েছে)') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Date of Incident') }} <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="damage_date" class="form-control rounded-3">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">{{ __('Fault Description / Reason') }} <span class="text-danger">*</span></label>
                                    <textarea wire:model="damage_reason" rows="3" class="form-control rounded-3 @error('damage_reason') is-invalid @enderror" placeholder="{{ __('Explain how it got damaged: e.g. Thunder / power surge burnt power adapter, water damage, port dead...') }}"></textarea>
                                    @error('damage_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showDamageModal', false)" class="btn btn-light rounded-3 px-3">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-danger rounded-3 px-4 fw-semibold">{{ __('Confirm Damage Report') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================================
         MODAL 4: RETURN TO STOCK
         ========================================================================= --}}
    @if($showReturnModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-success">
                            <i class="bi bi-arrow-down-left-circle text-success me-2"></i>{{ __('Return Item to Stock') }}
                        </h5>
                        <button type="button" wire:click="$set('showReturnModal', false)" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="submitReturn">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Return Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="return_date" class="form-control rounded-3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('Return Location / Shelf') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="return_location" class="form-control rounded-3" placeholder="{{ __('e.g. Main Store Shelf B') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">{{ __('Condition / Notes') }}</label>
                                    <textarea wire:model="return_notes" rows="2" class="form-control rounded-3" placeholder="{{ __('e.g. Customer disconnected service, router is in working condition.') }}"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showReturnModal', false)" class="btn btn-light rounded-3 px-3">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">{{ __('Confirm Return') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================================
         MODAL 5: ITEM AUDIT TIMELINE
         ========================================================================= --}}
    @if($showHistoryModal && $viewingItem)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h5 class="modal-title fw-bold text-dark">
                                <i class="bi bi-clock-history me-2 text-primary"></i>{{ $viewingItem->title }}
                            </h5>
                            <small class="text-muted">
                                @if($viewingItem->serial_number) SN: <code>{{ $viewingItem->serial_number }}</code> &bull; @endif
                                {{ $viewingItem->category?->name }} &bull;
                                <span class="badge bg-{{ $viewingItem->status_color }}">{{ $viewingItem->status_label }}</span>
                            </small>
                        </div>
                        <button type="button" wire:click="$set('showHistoryModal', false)" class="btn-close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-signpost-split text-primary me-2"></i>{{ __('Assignments & Withdrawals History') }}</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light small">
                                    <tr>
                                        <th>{{ __('Withdrawn Date') }}</th>
                                        <th>{{ __('Destination / Customer') }}</th>
                                        <th>{{ __('Technician') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Return Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($viewingItem->assignments as $asg)
                                        <tr class="small">
                                            <td>{{ $asg->withdraw_date ? $asg->withdraw_date->format('d M, Y') : '-' }}</td>
                                            <td>
                                                <span class="fw-semibold">{{ $asg->destination_title }}</span>
                                                @if($asg->purpose)
                                                    <div class="text-muted" style="font-size:11px;">{{ $asg->purpose }}</div>
                                                @endif
                                            </td>
                                            <td>{{ $asg->technician?->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $asg->status === 'deployed' ? 'primary' : ($asg->status === 'returned' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($asg->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $asg->return_date ? $asg->return_date->format('d M, Y') : '-' }}
                                                @if($asg->return_notes)
                                                    <div class="text-muted" style="font-size:11px;">{{ $asg->return_notes }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted small py-3">{{ __('No deployment records yet.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text text-primary me-2"></i>{{ __('Stock Activity Log') }}</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped small align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Timestamp') }}</th>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Change') }}</th>
                                        <th>{{ __('Details / Notes') }}</th>
                                        <th>{{ __('By') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($viewingItem->stockLogs as $log)
                                        <tr>
                                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                            <td><span class="badge bg-secondary">{{ strtoupper($log->type) }}</span></td>
                                            <td class="fw-bold {{ $log->quantity_change < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $log->quantity_change > 0 ? '+' : '' }}{{ $log->quantity_change }}
                                            </td>
                                            <td>{{ $log->note }}</td>
                                            <td>{{ $log->user?->name ?? 'System' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted small py-3">{{ __('No stock logs recorded.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" wire:click="$set('showHistoryModal', false)" class="btn btn-secondary rounded-3 px-4">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

