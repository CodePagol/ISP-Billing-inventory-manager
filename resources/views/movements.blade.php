<div>
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color:#1a1f36;">
                    <i class="bi bi-clock-history me-2 text-primary"></i>{{ __('Stock Movements & Deployments Log') }}
                </h4>
                <p class="text-muted small mb-0">{{ __('Audit history of item withdrawals, client assignments, damages, and returns') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.inventory.items') }}" class="btn btn-primary rounded-3 px-3 fw-semibold shadow-sm">
                    <i class="bi bi-box-seam me-1"></i> {{ __('Item Catalog') }}
                </a>
                <a href="{{ route('admin.inventory.dashboard') }}" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold">
                    <i class="bi bi-speedometer2 me-1"></i> {{ __('Dashboard') }}
                </a>
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-pills mb-4 gap-2">
            <li class="nav-item">
                <button wire:click="switchTab('assignments')" class="nav-link rounded-3 px-4 fw-semibold {{ $activeTab === 'assignments' ? 'active bg-primary' : 'bg-white text-muted shadow-sm' }}">
                    <i class="bi bi-arrow-right-circle me-1"></i> {{ __('Assignments & Deployments') }}
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="switchTab('logs')" class="nav-link rounded-3 px-4 fw-semibold {{ $activeTab === 'logs' ? 'active bg-primary' : 'bg-white text-muted shadow-sm' }}">
                    <i class="bi bi-journal-text me-1"></i> {{ __('All Stock Logs (Audit Trail)') }}
                </button>
            </li>
        </ul>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body py-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold text-muted mb-1">{{ __('Search Records') }}</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control rounded-end-3" placeholder="{{ __('Customer, Serial, Model, Note, Destination...') }}">
                        </div>
                    </div>
                    @if($activeTab === 'assignments')
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold text-muted mb-1">{{ __('Status') }}</label>
                            <select wire:model.live="filterStatus" class="form-select form-select-sm rounded-3">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="deployed">{{ __('In Use / Deployed') }}</option>
                                <option value="returned">{{ __('Returned') }}</option>
                                <option value="damaged">{{ __('Damaged') }}</option>
                                <option value="lost">{{ __('Lost') }}</option>
                            </select>
                        </div>
                    @endif
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">{{ __('From Date') }}</label>
                        <input type="date" wire:model.live="filterDateFrom" class="form-control form-control-sm rounded-3">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">{{ __('To Date') }}</label>
                        <input type="date" wire:model.live="filterDateTo" class="form-control form-control-sm rounded-3">
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <button wire:click="$set('search', ''); $set('filterStatus', ''); $set('filterDateFrom', ''); $set('filterDateTo', '');" class="btn btn-sm btn-light w-100 rounded-3 text-muted" title="{{ __('Reset') }}">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="table-responsive">
                @if($activeTab === 'assignments')
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small text-uppercase">
                                <th>{{ __('Withdrawn Date') }}</th>
                                <th>{{ __('Item & Serial') }}</th>
                                <th>{{ __('Destination / Customer') }}</th>
                                <th>{{ __('Technician') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Return Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $row)
                                <tr>
                                    <td class="small fw-semibold text-dark">
                                        {{ $row->withdraw_date ? $row->withdraw_date->format('d M, Y') : '-' }}
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $row->item?->title }}</div>
                                        <div class="small text-muted">
                                            @if($row->item?->serial_number)
                                                SN: <code>{{ $row->item->serial_number }}</code> &bull;
                                            @endif
                                            <span>{{ $row->item?->category?->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $row->destination_title }}</div>
                                        @if($row->purpose)
                                            <small class="text-muted">{{ Str::limit($row->purpose, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $row->technician?->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ number_format($row->quantity, 0) }}</span>
                                        <small class="text-muted">{{ $row->item?->category?->unit ?? 'pcs' }}</small>
                                    </td>
                                    <td>
                                        @if($row->status === 'deployed')
                                            <span class="badge bg-primary rounded-pill">{{ __('In Use') }}</span>
                                        @elseif($row->status === 'returned')
                                            <span class="badge bg-success rounded-pill">{{ __('Returned') }}</span>
                                        @elseif($row->status === 'damaged')
                                            <span class="badge bg-danger rounded-pill">{{ __('Damaged') }}</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">{{ ucfirst($row->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">
                                        {{ $row->return_date ? $row->return_date->format('d M, Y') : '-' }}
                                        @if($row->return_notes)
                                            <div class="fst-italic text-secondary" style="font-size:11px;">{{ Str::limit($row->return_notes, 40) }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        {{ __('No assignment records found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small text-uppercase">
                                <th>{{ __('Timestamp') }}</th>
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('Action / Type') }}</th>
                                <th>{{ __('Qty Change') }}</th>
                                <th>{{ __('Balance After') }}</th>
                                <th>{{ __('Details / Notes') }}</th>
                                <th>{{ __('Performed By') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $log)
                                <tr>
                                    <td class="small text-muted">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $log->item?->title }}</div>
                                        @if($log->item?->serial_number)
                                            <small class="text-muted">SN: <code>{{ $log->item->serial_number }}</code></small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary rounded-pill px-3">{{ strtoupper($log->type) }}</span>
                                    </td>
                                    <td class="fw-bold {{ $log->quantity_change < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $log->quantity_change > 0 ? '+' : '' }}{{ $log->quantity_change }}
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $log->balance_after }}</td>
                                    <td class="small text-muted">{{ $log->note }}</td>
                                    <td class="small text-muted">{{ $log->user?->name ?? 'System' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        {{ __('No audit logs found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
            @if($records->hasPages())
                <div class="card-footer bg-white py-3 border-0">
                    {{ $records->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

