@if($printAssignment)
<div class="modal fade show d-block" style="background: rgba(0,0,0,0.6);" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-3 d-flex justify-content-between align-items-center">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 text-xs">
                    <i class="bi bi-printer me-1"></i> {{ __('Print Delivery Challan / Gate Pass') }}
                </span>
                <button type="button" wire:click="closePrintChallan" class="btn-close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                {{-- Printable Printable Paper Container --}}
                <div id="challan-print-area" class="p-4 bg-white border rounded-3" style="color: #000; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                    
                    {{-- ISP Header --}}
                    <div class="row align-items-center border-bottom pb-3 mb-3">
                        <div class="col-7">
                            <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px;">
                                {{ siteUrlSettings('site_name') ?? config('app.name', 'ISP Billing') }}
                            </h4>
                            <div class="small text-muted">
                                {{ siteUrlSettings('site_title') ?? 'Internet Service Provider' }}
                            </div>
                            @if(siteUrlSettings('contact_address'))
                                <div class="text-xs text-muted">{{ siteUrlSettings('contact_address') }}</div>
                            @endif
                            @if(siteUrlSettings('contact_phone'))
                                <div class="text-xs text-muted">Tel: {{ siteUrlSettings('contact_phone') }} | Email: {{ siteUrlSettings('contact_email') }}</div>
                            @endif
                        </div>
                        <div class="col-5 text-end">
                            <span class="badge bg-dark text-white text-uppercase px-3 py-2 fs-11 tracking-wider mb-2 d-inline-block">
                                {{ __('GATE PASS / DELIVERY CHALLAN') }}
                            </span>
                            <div class="small"><strong>Challan No:</strong> <span class="font-monospace text-primary fw-bold">{{ $printAssignment->challan_no ?? 'CH-' . str_pad($printAssignment->id, 5, '0', STR_PAD_LEFT) }}</span></div>
                            <div class="small"><strong>Date:</strong> {{ $printAssignment->withdraw_date ? $printAssignment->withdraw_date->format('d M, Y') : date('d M, Y') }}</div>
                        </div>
                    </div>

                    {{-- Customer / Recipient & Technician Info --}}
                    <div class="row g-3 mb-4 p-3 bg-light rounded-3" style="border: 1px dashed #cbd5e1;">
                        <div class="col-6 border-end">
                            <div class="text-xs text-uppercase fw-bold text-muted mb-1">{{ __('Recipient / Customer Details') }}</div>
                            @if($printAssignment->customer)
                                <div class="fw-bold text-dark fs-6">{{ $printAssignment->customer->customer_name }}</div>
                                <div class="small text-muted"><strong>ID:</strong> {{ $printAssignment->customer->customer_unique_id }}</div>
                                @if($printAssignment->customer->mobile)
                                    <div class="small text-muted"><strong>Mobile:</strong> {{ $printAssignment->customer->mobile }}</div>
                                @endif
                                <div class="small text-muted"><strong>Address:</strong> {{ $printAssignment->customer->address ?: $printAssignment->destination_title }}</div>
                                @if($printAssignment->customer->pppUser)
                                    <div class="small text-muted"><strong>User ID:</strong> {{ $printAssignment->customer->pppUser->username }}</div>
                                @endif
                            @else
                                <div class="fw-bold text-dark fs-6">{{ $printAssignment->destination_title }}</div>
                                <div class="small text-muted">{{ __('Direct Deployment / Infrastructure') }}</div>
                            @endif
                        </div>
                        <div class="col-6 ps-3">
                            <div class="text-xs text-uppercase fw-bold text-muted mb-1">{{ __('Issued & Handed Over To') }}</div>
                            <div class="small"><strong>Technician / Field Staff:</strong> <span class="fw-bold text-dark">{{ $printAssignment->technician?->name ?? 'Direct Handover' }}</span></div>
                            @if($printAssignment->technician?->mobile)
                                <div class="small"><strong>Tech Mobile:</strong> {{ $printAssignment->technician->mobile }}</div>
                            @endif
                            <div class="small"><strong>Authorized By:</strong> {{ $printAssignment->actionUser?->name ?? auth()->user()->name }}</div>
                            <div class="small"><strong>Purpose:</strong> {{ $printAssignment->purpose ?: 'New Connection Setup' }}</div>
                            @if($printAssignment->replacementFor)
                                <div class="small text-danger fw-semibold mt-1">
                                    <i class="bi bi-arrow-repeat"></i> Replacement for damaged device: {{ $printAssignment->replacementFor->title }} (SN: {{ $printAssignment->replacementFor->serial_number }})
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Equipment Table --}}
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle mb-0 text-sm">
                            <thead class="table-light">
                                <tr class="text-uppercase text-xs fw-bold">
                                    <th class="text-center" style="width: 40px;">#</th>
                                    <th>{{ __('Item Description / Model') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Serial Number (S/N)') }}</th>
                                    <th>{{ __('MAC Address') }}</th>
                                    <th class="text-center" style="width: 80px;">{{ __('Qty') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center fw-bold">1</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $printAssignment->item?->title }}</div>
                                        @if($printAssignment->item?->brand || $printAssignment->item?->model_no)
                                            <div class="text-muted text-xs">{{ $printAssignment->item->brand }} {{ $printAssignment->item->model_no }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $printAssignment->item?->category?->name ?? 'Equipment' }}</td>
                                    <td>
                                        @if($printAssignment->item?->serial_number)
                                            <span class="font-monospace fw-bold">{{ $printAssignment->item->serial_number }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($printAssignment->item?->mac_address)
                                            <span class="font-monospace">{{ $printAssignment->item->mac_address }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold fs-6">
                                        {{ number_format($printAssignment->quantity, $printAssignment->item?->tracking_type === 'bulk' ? 2 : 0) }}
                                        <small class="text-muted fw-normal">{{ $printAssignment->item?->category?->unit ?? 'pcs' }}</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Terms & Conditions --}}
                    <div class="p-2 mb-4 bg-light rounded text-xs text-muted" style="border-left: 3px solid #64748b;">
                        <strong>{{ __('Notice:') }}</strong> {{ __('The above mentioned equipment has been tested and handed over in full working condition. The receiver/customer is responsible for safe custody. In case of disconnection, equipment must be returned in good condition.') }}
                    </div>

                    {{-- Signatures --}}
                    <div class="row pt-4 mt-4 text-center text-xs">
                        <div class="col-4">
                            <div class="border-top pt-2 mx-3 fw-bold text-dark">
                                {{ __('Store In-Charge') }}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-top pt-2 mx-3 fw-bold text-dark">
                                {{ __('Technician / Delivery') }}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-top pt-2 mx-3 fw-bold text-dark">
                                {{ __('Customer Signature') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer border-0 px-4 py-3" style="background:#f8f9fc;">
                <button type="button" wire:click="closePrintChallan" class="btn btn-light rounded-3 px-4">{{ __('Close') }}</button>
                <button type="button" onclick="window.doPrintChallan()" class="btn btn-success rounded-3 px-5 fw-semibold shadow-sm">
                    <i class="bi bi-printer me-2"></i> {{ __('Print Gate Pass') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    window.doPrintChallan = function() {
        const printElement = document.getElementById('challan-print-area');
        if (!printElement) {
            console.error('Print area #challan-print-area not found');
            return;
        }

        // 1. Primary: Use print-js library if available (as imported in app.js as window.print)
        try {
            if (typeof window.print === 'function') {
                window.print({
                    printable: 'challan-print-area',
                    type: 'html',
                    scanStyles: true,
                    targetStyles: ['*']
                });
                return;
            }
        } catch (e) {
            console.warn('printJS failed, falling back to iframe print:', e);
        }

        // 2. Fallback: Hidden iframe printing (escaped closing tags prevent PHP-Flasher injection)
        try {
            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0px';
            iframe.style.height = '0px';
            iframe.style.border = 'none';
            iframe.style.visibility = 'hidden';
            document.body.appendChild(iframe);

            let cssLinks = Array.from(document.querySelectorAll('link[rel=stylesheet]'))
                .map(link => '<link rel="stylesheet" href="' + link.href + '">')
                .join('');

            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write('<!DOCTYPE html><html><' + 'head><title>Challan Gate Pass<' + '/title>');
            doc.write(cssLinks);
            doc.write('<style>body { background-color: #fff !important; margin: 20px; font-family: sans-serif; } * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } @media print { @page { size: auto; margin: 10mm; } }<' + '/style>');
            doc.write('<' + '/head><' + 'body>');
            doc.write(printElement.outerHTML);
            doc.write('<' + '/body><' + '/html>');
            doc.close();

            setTimeout(() => {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (err) {
                    console.error('Iframe print failed:', err);
                    if (typeof Window.prototype.print === 'function') {
                        Window.prototype.print.call(window);
                    }
                }
                setTimeout(() => {
                    if (iframe.parentNode) {
                        document.body.removeChild(iframe);
                    }
                }, 2000);
            }, 500);
        } catch (err2) {
            console.error('All challan print methods failed:', err2);
        }
    };
</script>

