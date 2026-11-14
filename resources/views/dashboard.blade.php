<div>
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color:#1a1f36;">
                    <i class="bi bi-boxes me-2 text-primary"></i>{{ __('Inventory & Asset Management') }}
                </h4>
                <p class="text-muted small mb-0">{{ __('Track routers, ONUs, cables, deployments, and damage status') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.inventory.items') }}" class="btn btn-primary rounded-3 px-3 fw-semibold shadow-sm">
                    <i class="bi bi-box-seam me-1"></i> {{ __('Manage All Items') }}
                </a>
                <a href="{{ route('admin.inventory.movements') }}" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold shadow-sm">
                    <i class="bi bi-clock-history me-1"></i> {{ __('Movement History') }}
                </a>
                <a href="{{ route('admin.expenses') }}" class="btn btn-outline-danger rounded-3 px-3 fw-semibold shadow-sm">
                    <i class="bi bi-wallet2 me-1"></i> {{ __('Expense Manager') }}
                </a>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="row g-3 mb-4">
            {{-- In Stock --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-left: 4px solid #16a34a !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">{{ __('Available In Stock') }}</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(22, 163, 74, 0.12) !important;">
                                <i class="bi bi-check-circle-fill fs-5" style="color: #16a34a !important;"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-success mb-1">{{ number_format($inStockCount) }}</h3>
                        <small class="text-muted">{{ __('Ready for deployment') }}</small>
                    </div>
                </div>
            </div>

            {{-- Deployed / In Use --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-left: 4px solid #2563eb !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">{{ __('Deployed / In Use') }}</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(37, 99, 235, 0.12) !important;">
                                <i class="bi bi-send-check-fill fs-5" style="color: #2563eb !important;"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-primary mb-1">{{ number_format($deployedCount) }}</h3>
                        <small class="text-muted">{{ __('Active at clients, POPs, racks') }}</small>
                    </div>
                </div>
            </div>

            {{-- Damaged / Repair --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%); border-left: 4px solid #dc2626 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">{{ __('Damaged / Faulty') }}</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(220, 38, 38, 0.12) !important;">
                                <i class="bi bi-exclamation-triangle-fill fs-5" style="color: #dc2626 !important;"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-danger mb-1">{{ number_format($damagedCount) }}</h3>
                        <small class="text-muted">{{ number_format($underRepairCount) }} {{ __('currently under repair') }}</small>
                    </div>
                </div>
            </div>

            {{-- Total Valuation --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #faf5ff 0%, #ffffff 100%); border-left: 4px solid #9333ea !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">{{ __('Total Valuation') }}</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(147, 51, 234, 0.12) !important;">
                                <i class="bi bi-cash-stack fs-5" style="color: #9333ea !important;"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">৳ {{ number_format($totalValuation, 2) }}</h3>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted">{{ __('Across :count active assets', ['count' => $totalItems]) }}</small>
                            <a href="{{ route('admin.expenses') }}?filterCategory=item_purchase" class="text-decoration-none small fw-semibold" style="color:#9333ea; font-size:11px;" title="{{ __('View Equipment Purchase Expenses') }}">
                                {{ __('Expenses') }} &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Categories Stock Overview --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-grid me-2 text-primary"></i>{{ __('Category-wise Stock Breakdown') }}
                    </h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" wire:click="$set('stockFilter', 'active')" class="btn {{ $stockFilter === 'active' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-start-pill px-3" style="font-size: 11.5px;">
                            <i class="bi bi-funnel me-1"></i>{{ __('Active Stock') }} ({{ $activeCategoriesCount }})
                        </button>
                        <button type="button" wire:click="$set('stockFilter', 'all')" class="btn {{ $stockFilter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-end-pill px-3" style="font-size: 11.5px;">
                            {{ __('All Categories') }} ({{ $totalCategoriesCount }})
                        </button>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.inventory.categories') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-tags me-1"></i>{{ __('Manage Categories') }}
                    </a>
                    <a href="{{ route('admin.inventory.items') }}" class="btn btn-sm btn-light rounded-pill px-3">
                        {{ __('View All Items') }} &rarr;
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    @forelse($categories as $cat)
                        <div class="col-md-4 col-lg-3">
                            <div class="p-3 rounded-4 border {{ $cat->total_qty > 0 ? 'bg-white shadow-sm border-primary border-opacity-25' : 'bg-light bg-opacity-40' }} h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold text-dark text-truncate" title="{{ $cat->name }}">
                                            <i class="bi {{ $cat->icon ?? 'bi-box-seam' }} text-primary me-1"></i> {{ $cat->name }}
                                        </span>
                                        @if($cat->tracking_type === 'serialized')
                                            <span class="badge rounded-pill fw-semibold border" style="background: rgba(37, 99, 235, 0.1) !important; color: #1d4ed8 !important; border-color: rgba(37, 99, 235, 0.25) !important; font-size: 9.5px; padding: 3px 8px;">
                                                {{ __('Serialized') }}
                                            </span>
                                        @else
                                            <span class="badge rounded-pill fw-semibold border" style="background: rgba(22, 163, 74, 0.1) !important; color: #15803d !important; border-color: rgba(22, 163, 74, 0.25) !important; font-size: 9.5px; padding: 3px 8px;">
                                                {{ __('Bulk') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Total Stock Pill --}}
                                    <div class="d-flex justify-content-between align-items-center bg-light rounded-3 px-2 py-1 mb-2 small">
                                        <span class="text-muted fw-semibold">{{ __('Total Stock:') }}</span>
                                        <span class="fw-bold {{ $cat->total_qty > 0 ? 'text-dark' : 'text-muted' }}">{{ $cat->total_qty }} {{ $cat->unit }}</span>
                                    </div>

                                    @if($cat->total_qty > 0)
                                        {{-- Status Breakdown --}}
                                        <div class="d-flex justify-content-between text-muted small mb-1">
                                            <span>{{ __('In Stock:') }}</span>
                                            <span class="fw-bold text-success">{{ $cat->in_stock_qty }} {{ $cat->unit }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted small mb-1">
                                            <span>{{ __('Deployed:') }}</span>
                                            <span class="fw-bold text-primary">{{ $cat->deployed_qty }} {{ $cat->unit }}</span>
                                        </div>
                                        @if($cat->under_repair_qty > 0)
                                            <div class="d-flex justify-content-between text-muted small mb-1">
                                                <span>{{ __('Under Repair:') }}</span>
                                                <span class="fw-bold text-warning">{{ $cat->under_repair_qty }} {{ $cat->unit }}</span>
                                            </div>
                                        @endif
                                        @if($cat->damaged_qty > 0)
                                            <div class="d-flex justify-content-between text-muted small mb-1">
                                                <span>{{ __('Damaged:') }}</span>
                                                <span class="fw-bold text-danger">{{ $cat->damaged_qty }} {{ $cat->unit }}</span>
                                            </div>
                                        @endif
                                        @if($cat->retired_qty > 0)
                                            <div class="d-flex justify-content-between text-muted small mb-1">
                                                <span>{{ __('Retired:') }}</span>
                                                <span class="fw-bold text-secondary">{{ $cat->retired_qty }} {{ $cat->unit }}</span>
                                            </div>
                                        @endif
                                    @else
                                        {{-- Empty category subtle info --}}
                                        <div class="text-center py-2 text-muted small">
                                            <small class="opacity-75">{{ __('No equipment recorded') }}</small>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2 mt-2 border-top text-end">
                                    @if($cat->total_qty > 0)
                                        <a href="{{ route('admin.inventory.items') }}?filterCategory={{ $cat->id }}" class="text-decoration-none small text-primary fw-semibold" style="font-size: 11px;">
                                            {{ __('View Items') }} &rarr;
                                        </a>
                                    @else
                                        <a href="{{ route('admin.inventory.items') }}" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0 text-muted" style="font-size: 10px;">
                                            <i class="bi bi-plus-lg me-1"></i>{{ __('Add Item') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-3 text-center text-muted">
                            {{ __('No categories found matching your filter.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tables row --}}
        <div class="row g-4">
            {{-- Recent Assignments / Withdrawals --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-arrow-right-circle text-primary me-2"></i>{{ __('Recent Deployments & Withdrawals') }}
                        </h6>
                        <a href="{{ route('admin.inventory.movements') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            {{ __('All Movements') }}
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small text-uppercase">
                                    <th>{{ __('Item') }}</th>
                                    <th>{{ __('Destination / Customer') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAssignments as $assign)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $assign->item?->title }}</div>
                                            @if($assign->item?->serial_number)
                                                <small class="text-muted">SN: <code>{{ $assign->item->serial_number }}</code></small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark">{{ $assign->destination_title }}</div>
                                            @if($assign->technician)
                                                <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $assign->technician->name }}</small>
                                            @endif
                                        </td>
                                        <td class="small text-muted">
                                            {{ $assign->withdraw_date ? \Carbon\Carbon::parse($assign->withdraw_date)->format('d M, Y') : '-' }}
                                        </td>
                                        <td>
                                            @if($assign->status === 'deployed')
                                                <span class="badge bg-primary rounded-pill">{{ __('In Use') }}</span>
                                            @elseif($assign->status === 'returned')
                                                <span class="badge bg-success rounded-pill">{{ __('Returned') }}</span>
                                            @elseif($assign->status === 'damaged')
                                                <span class="badge bg-danger rounded-pill">{{ __('Damaged') }}</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">{{ ucfirst($assign->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            {{ __('No withdrawal or deployment records found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Damaged Items Attention Card --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-danger">
                            <i class="bi bi-exclamation-octagon text-danger me-2"></i>{{ __('Damaged / Faulty Assets') }}
                        </h6>
                        <span class="badge bg-danger rounded-pill">{{ $damagedCount }} {{ __('Issues') }}</span>
                    </div>
                    <div class="card-body pt-0">
                        @forelse($recentDamaged as $dmg)
                            <div class="p-3 mb-2 rounded-3 border border-danger border-opacity-25 bg-danger bg-opacity-10">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="fw-bold text-dark">{{ $dmg->title }}</div>
                                    <span class="badge bg-danger">{{ __('Damaged') }}</span>
                                </div>
                                <div class="small text-muted mb-1">
                                    @if($dmg->serial_number)
                                        <span>SN: <code>{{ $dmg->serial_number }}</code></span> &bull;
                                    @endif
                                    <span>{{ $dmg->category?->name }}</span>
                                </div>
                                <div class="small text-danger fst-italic">
                                    {{ Str::limit($dmg->notes ?? 'No fault reason specified', 80) }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-shield-check text-success fs-1 mb-2 d-block"></i>
                                <span class="fw-semibold">{{ __('No damaged items reported.') }}</span>
                                <p class="small text-muted">{{ __('All deployed and in-stock assets are operational.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

