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
                            <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-circle fs-5"></i>
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
                            <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-send-check fs-5"></i>
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
                            <div class="rounded-3 p-2 bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-exclamation-triangle fs-5"></i>
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
                            <div class="rounded-3 p-2 bg-purple bg-opacity-10 text-purple" style="color:#9333ea;">
                                <i class="bi bi-cash-stack fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">৳ {{ number_format($totalValuation, 2) }}</h3>
                        <small class="text-muted">{{ __('Across :count active assets', ['count' => $totalItems]) }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Categories Stock Overview --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-grid me-2 text-primary"></i>{{ __('Category-wise Stock Breakdown') }}
                </h6>
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
                            <div class="p-3 rounded-4 border bg-light bg-opacity-50 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark">
                                        <i class="bi {{ $cat->icon ?? 'bi-box' }} text-primary me-1"></i> {{ $cat->name }}
                                    </span>
                                    <span class="badge bg-secondary rounded-pill">{{ ucfirst($cat->tracking_type) }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mb-1">
                                    <span>{{ __('In Stock:') }}</span>
                                    <span class="fw-bold text-success">{{ $cat->in_stock_items }} {{ $cat->unit }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mb-1">
                                    <span>{{ __('Deployed:') }}</span>
                                    <span class="fw-bold text-primary">{{ $cat->deployed_items }} {{ $cat->unit }}</span>
                                </div>
                                @if($cat->damaged_items > 0)
                                    <div class="d-flex justify-content-between text-muted small mb-1">
                                        <span>{{ __('Damaged:') }}</span>
                                        <span class="fw-bold text-danger">{{ $cat->damaged_items }} {{ $cat->unit }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-3 text-center text-muted">
                            {{ __('No categories found.') }}
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

