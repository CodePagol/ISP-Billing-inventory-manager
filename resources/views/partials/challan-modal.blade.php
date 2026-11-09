@if($printAssignment)
<div class="modal fade show d-block" style="background: rgba(0,0,0,0.6);" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-3 d-flex justify-content-between align-items-center">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 text-xs">
                    <i class="bi bi-printer me-1"></i> {{ __('Delivery Challan / Gate Pass Preview') }}
                </span>
                <button type="button" wire:click="closePrintChallan" class="btn-close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                {{-- Printable Paper Container with Self-Contained Table Layout --}}
                <div id="challan-print-area" style="background: #ffffff; color: #0f172a; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; line-height: 1.4; padding: 24px; border: 1px solid #cbd5e1; border-radius: 8px;">

                    {{-- 1. HEADER --}}
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px; border-bottom: 2px solid #0f172a; padding-bottom: 14px;">
                        <tr>
                            <td style="width: 58%; vertical-align: top; padding-right: 15px;">
                                <h3 style="margin: 0 0 4px 0; font-size: 20px; font-weight: 800; text-transform: uppercase; color: #0f172a; letter-spacing: 0.5px;">
                                    {{ siteUrlSettings('site_name') ?? config('app.name', 'ISP Billing') }}
                                </h3>
                                <div style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                                    {{ siteUrlSettings('site_title') ?? 'Internet Service Provider' }}
                                </div>
                                @if(siteUrlSettings('contact_address'))
                                    <div style="font-size: 11px; color: #64748b;">{{ siteUrlSettings('contact_address') }}</div>
                                @endif
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    @if(siteUrlSettings('contact_phone'))
                                        <span><strong>Phone:</strong> {{ siteUrlSettings('contact_phone') }}</span>
                                    @endif
                                    @if(siteUrlSettings('contact_email'))
                                        <span style="margin-left: 8px;"><strong>Email:</strong> {{ siteUrlSettings('contact_email') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td style="width: 42%; vertical-align: top; text-align: right;">
                                <div style="display: inline-block; background: #0f172a; color: #ffffff; padding: 5px 14px; font-size: 12px; font-weight: 800; letter-spacing: 1px; border-radius: 4px; text-transform: uppercase; margin-bottom: 8px;">
                                    GATE PASS / DELIVERY CHALLAN
                                </div>
                                <table style="width: auto; float: right; border-collapse: collapse; font-size: 12px; margin-top: 4px;">
                                    <tr>
                                        <td style="text-align: right; padding: 2px 6px; font-weight: 600; color: #475569;">Challan No:</td>
                                        <td style="text-align: left; padding: 2px 6px; font-family: monospace; font-weight: 700; color: #2563eb; font-size: 13px;">{{ $printAssignment->challan_no ?? 'CH-' . str_pad($printAssignment->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding: 2px 6px; font-weight: 600; color: #475569;">Date:</td>
                                        <td style="text-align: left; padding: 2px 6px; font-weight: 600; color: #0f172a;">{{ $printAssignment->withdraw_date ? $printAssignment->withdraw_date->format('d M, Y') : date('d M, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding: 2px 6px; font-weight: 600; color: #475569;">Status:</td>
                                        <td style="text-align: left; padding: 2px 6px;">
                                            <span style="display: inline-block; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                {{ ucfirst($printAssignment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    {{-- 2. PARTIES INFO (RECIPIENT & TECHNICIAN) --}}
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 18px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding: 12px 14px; border-right: 1px dashed #cbd5e1;">
                                <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px;">
                                    RECIPIENT / CUSTOMER DETAILS
                                </div>
                                @if($printAssignment->customer)
                                    <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px;">
                                        {{ $printAssignment->customer->customer_name }}
                                    </div>
                                    <div style="font-size: 11px; color: #334155; margin-bottom: 2px;">
                                        <strong>Customer ID:</strong> <span style="font-family: monospace; font-weight: 700; color: #2563eb;">{{ $printAssignment->customer->customer_unique_id }}</span>
                                        @if($printAssignment->customer->pppUser)
                                            <span style="color: #64748b; margin-left: 6px;">(User: {{ $printAssignment->customer->pppUser->username }})</span>
                                        @endif
                                    </div>
                                    @if($printAssignment->customer->mobile)
                                        <div style="font-size: 11px; color: #334155; margin-bottom: 2px;">
                                            <strong>Mobile:</strong> {{ $printAssignment->customer->mobile }}
                                        </div>
                                    @endif
                                    <div style="font-size: 11px; color: #334155;">
                                        <strong>Address:</strong> {{ $printAssignment->customer->address ?: $printAssignment->destination_title }}
                                    </div>
                                @else
                                    <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px;">
                                        {{ $printAssignment->destination_title }}
                                    </div>
                                    <div style="font-size: 11px; color: #64748b;">Internal Infrastructure / Direct Handover</div>
                                @endif
                            </td>
                            <td style="width: 50%; vertical-align: top; padding: 12px 14px;">
                                <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px;">
                                    DELIVERY & STAFF DETAILS
                                </div>
                                <div style="font-size: 11px; color: #334155; margin-bottom: 3px;">
                                    <strong>Technician / Staff:</strong> 
                                    <span style="font-weight: 700; color: #0f172a;">{{ $printAssignment->technician?->name ?? 'Direct Handover' }}</span>
                                </div>
                                @if($printAssignment->technician?->mobile)
                                    <div style="font-size: 11px; color: #334155; margin-bottom: 3px;">
                                        <strong>Tech Mobile:</strong> {{ $printAssignment->technician->mobile }}
                                    </div>
                                @endif
                                <div style="font-size: 11px; color: #334155; margin-bottom: 3px;">
                                    <strong>Issued By:</strong> {{ $printAssignment->actionUser?->name ?? (auth()->check() ? auth()->user()->name : 'Store Admin') }}
                                </div>
                                <div style="font-size: 11px; color: #334155; margin-bottom: 3px;">
                                    <strong>Purpose:</strong> {{ $printAssignment->purpose ?: 'New Connection Setup' }}
                                </div>
                                @if($printAssignment->replacementFor)
                                    <div style="font-size: 11px; color: #b91c1c; font-weight: 700; margin-top: 4px; background: #fee2e2; padding: 3px 6px; border-radius: 4px; border: 1px solid #fecaca;">
                                        Replacement for: {{ $printAssignment->replacementFor->title }} (SN: {{ $printAssignment->replacementFor->serial_number ?: 'N/A' }})
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    {{-- 3. EQUIPMENT TABLE --}}
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 18px; border: 1px solid #0f172a; font-size: 12px;">
                        <thead>
                            <tr style="background-color: #f1f5f9; color: #0f172a; border-bottom: 2px solid #0f172a;">
                                <th style="border: 1px solid #cbd5e1; padding: 8px 6px; text-align: center; width: 35px; font-weight: 800; font-size: 11px;">#</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-weight: 800; font-size: 11px;">ITEM DESCRIPTION & MODEL</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-weight: 800; font-size: 11px; width: 110px;">CATEGORY</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-weight: 800; font-size: 11px; width: 140px;">SERIAL NO (S/N)</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-weight: 800; font-size: 11px; width: 130px;">MAC ADDRESS</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 8px; text-align: center; font-weight: 800; font-size: 11px; width: 65px;">QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: 10px 6px; text-align: center; font-weight: 700;">1</td>
                                <td style="border: 1px solid #cbd5e1; padding: 10px 10px;">
                                    <div style="font-weight: 700; color: #0f172a; font-size: 13px;">{{ $printAssignment->item?->title }}</div>
                                    @if($printAssignment->item?->brand || $printAssignment->item?->model_no)
                                        <div style="color: #64748b; font-size: 11px; margin-top: 2px;">
                                            {{ $printAssignment->item->brand }} {{ $printAssignment->item->model_no }}
                                        </div>
                                    @endif
                                </td>
                                <td style="border: 1px solid #cbd5e1; padding: 10px 10px; color: #334155; font-size: 11px;">
                                    {{ $printAssignment->item?->category?->name ?? 'Equipment' }}
                                </td>
                                <td style="border: 1px solid #cbd5e1; padding: 10px 10px; font-family: monospace; font-weight: 700; color: #0f172a; font-size: 12px;">
                                    {{ $printAssignment->item?->serial_number ?: '—' }}
                                </td>
                                <td style="border: 1px solid #cbd5e1; padding: 10px 10px; font-family: monospace; color: #475569; font-size: 12px;">
                                    {{ $printAssignment->item?->mac_address ?: '—' }}
                                </td>
                                <td style="border: 1px solid #cbd5e1; padding: 10px 8px; text-align: center; font-weight: 800; font-size: 14px; color: #0f172a;">
                                    {{ number_format($printAssignment->quantity, $printAssignment->item?->tracking_type === 'bulk' ? 2 : 0) }}
                                    <span style="font-size: 10px; font-weight: normal; color: #64748b; display: block;">{{ $printAssignment->item?->category?->unit ?? 'pcs' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- 4. TERMS & NOTICE --}}
                    <div style="background: #f8fafc; border-left: 3px solid #0f172a; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; border-radius: 4px; padding: 9px 12px; margin-bottom: 30px; font-size: 11px; color: #475569; line-height: 1.4;">
                        <strong style="color: #0f172a;">Terms & Undertaking:</strong> 
                        The equipment listed above has been inspected and delivered in full working condition. The receiver/customer is solely responsible for safe custody and protection against physical or electrical damages. In case of service disconnection, the equipment must be returned to the provider in good working order.
                    </div>

                    {{-- 5. SIGNATURES --}}
                    <table style="width: 100%; border-collapse: collapse; margin-top: 40px; margin-bottom: 15px; text-align: center;">
                        <tr>
                            <td style="width: 33.33%; padding: 0 15px; vertical-align: bottom;">
                                <div style="border-top: 1px solid #0f172a; padding-top: 6px; font-weight: 700; color: #0f172a; font-size: 11px;">
                                    Store In-Charge
                                </div>
                                <div style="font-size: 10px; color: #64748b; margin-top: 1px;">Authorized Signature</div>
                            </td>
                            <td style="width: 33.33%; padding: 0 15px; vertical-align: bottom;">
                                <div style="border-top: 1px solid #0f172a; padding-top: 6px; font-weight: 700; color: #0f172a; font-size: 11px;">
                                    Technician / Delivered By
                                </div>
                                <div style="font-size: 10px; color: #64748b; margin-top: 1px;">Staff Signature & Date</div>
                            </td>
                            <td style="width: 33.33%; padding: 0 15px; vertical-align: bottom;">
                                <div style="border-top: 1px solid #0f172a; padding-top: 6px; font-weight: 700; color: #0f172a; font-size: 11px;">
                                    Customer / Receiver
                                </div>
                                <div style="font-size: 10px; color: #64748b; margin-top: 1px;">Received in Good Condition</div>
                            </td>
                        </tr>
                    </table>

                    {{-- 6. SYSTEM FOOTER --}}
                    <table style="width: 100%; border-collapse: collapse; border-top: 1px dashed #cbd5e1; padding-top: 6px; margin-top: 15px; font-size: 9px; color: #94a3b8;">
                        <tr>
                            <td style="text-align: left;">
                                Printed on: {{ date('d-M-Y h:i A') }} | Operator: {{ auth()->check() ? auth()->user()->name : 'Admin' }}
                            </td>
                            <td style="text-align: right;">
                                System Generated Gate Pass &bull; {{ config('app.name', 'ISP Billing') }}
                            </td>
                        </tr>
                    </table>

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

        let iframe = document.getElementById('challan_print_iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'challan_print_iframe';
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0px';
            iframe.style.height = '0px';
            iframe.style.border = 'none';
            iframe.style.visibility = 'hidden';
            document.body.appendChild(iframe);
        }

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write('<!DOCTYPE html><html><' + 'head>');
        doc.write('<title>Delivery Challan<' + '/title>');
        doc.write('<style>');
        doc.write('@page { size: A4 portrait; margin: 12mm 15mm; }');
        doc.write('body { margin: 0; padding: 12px; background: #ffffff !important; color: #0f172a; font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; line-height: 1.4; }');
        doc.write('table { border-collapse: collapse; width: 100%; }');
        doc.write('* { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }');
        doc.write('<' + '/style>');
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
                // Fallback: window.open
                const win = window.open('', '_blank', 'width=850,height=900');
                if (win) {
                    win.document.open();
                    win.document.write('<!DOCTYPE html><html><' + 'head><title>Challan<' + '/title><style>@page{size:A4 portrait;margin:12mm 15mm;}body{margin:0;padding:12px;font-family:Arial,sans-serif;}table{border-collapse:collapse;width:100%;}*{box-sizing:border-box;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}<' + '/style><' + '/head><' + 'body>' + printElement.outerHTML + '<' + '/body><' + '/html>');
                    win.document.close();
                    win.focus();
                    win.print();
                }
            }
        }, 400);
    };
</script>

