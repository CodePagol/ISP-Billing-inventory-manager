@if($printAssignment)
<div class="modal fade show d-block" style="background: rgba(0,0,0,0.6);" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-3 d-flex justify-content-between align-items-center">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 text-xs">
                    <i class="bi bi-printer me-1"></i> {{ __('Delivery Challan & Gate Pass Preview') }}
                </span>
                <button type="button" wire:click="closePrintChallan" class="btn-close"></button>
            </div>

            <div class="modal-body px-4 py-4" style="background: #f1f5f9;">
                {{-- Printable Paper Container --}}
                <div id="challan-print-area" style="background: #ffffff; color: #0f172a; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Arial, sans-serif; font-size: 13px; line-height: 1.6; padding: 36px 40px; border: 1px solid #e2e8f0; border-radius: 8px; max-width: 860px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">

                    {{-- 1. HEADER --}}
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px; border-bottom: 2px solid #0f172a; padding-bottom: 18px;">
                        <tr>
                            <td style="width: 58%; vertical-align: top; padding-right: 20px;">
                                <h2 style="margin: 0 0 6px 0; font-size: 24px; font-weight: 800; text-transform: uppercase; color: #0f172a; letter-spacing: 0.8px;">
                                    {{ siteUrlSettings('site_name') ?? config('app.name', 'ISP Billing') }}
                                </h2>
                                <div style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                    {{ siteUrlSettings('site_title') ?? 'Internet Service Provider' }}
                                </div>
                                @if(siteUrlSettings('contact_address'))
                                    <div style="font-size: 12px; color: #64748b; margin-bottom: 3px;">
                                        {{ siteUrlSettings('contact_address') }}
                                    </div>
                                @endif
                                <div style="font-size: 12px; color: #64748b;">
                                    @if(siteUrlSettings('contact_phone'))
                                        <span><strong>Phone:</strong> {{ siteUrlSettings('contact_phone') }}</span>
                                    @endif
                                    @if(siteUrlSettings('contact_email'))
                                        <span style="margin-left: 12px;"><strong>Email:</strong> {{ siteUrlSettings('contact_email') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td style="width: 42%; vertical-align: top; text-align: right;">
                                <div style="display: inline-block; background: #0f172a; color: #ffffff; padding: 7px 18px; font-size: 13px; font-weight: 800; letter-spacing: 1.2px; border-radius: 4px; text-transform: uppercase; margin-bottom: 10px;">
                                    GATE PASS / DELIVERY CHALLAN
                                </div>
                                <table style="width: auto; float: right; border-collapse: collapse; font-size: 13px; margin-top: 6px;">
                                    <tr>
                                        <td style="text-align: right; padding: 4px 8px; font-weight: 600; color: #475569;">Challan No:</td>
                                        <td style="text-align: left; padding: 4px 8px; font-family: monospace; font-weight: 800; color: #2563eb; font-size: 15px;">{{ $printAssignment->challan_no ?? 'CH-' . str_pad($printAssignment->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding: 4px 8px; font-weight: 600; color: #475569;">Issue Date:</td>
                                        <td style="text-align: left; padding: 4px 8px; font-weight: 700; color: #0f172a;">{{ $printAssignment->withdraw_date ? $printAssignment->withdraw_date->format('d F, Y') : date('d F, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; padding: 4px 8px; font-weight: 600; color: #475569;">Status:</td>
                                        <td style="text-align: left; padding: 4px 8px;">
                                            <span style="display: inline-block; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius: 12px; padding: 2px 10px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                {{ ucfirst($printAssignment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    {{-- 2. PARTIES DETAILS (RECIPIENT & TECHNICIAN) --}}
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding: 18px 20px; border-right: 1px dashed #cbd5e1;">
                                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.8px; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                                    RECIPIENT / CUSTOMER DETAILS
                                </div>
                                @if($printAssignment->customer)
                                    <div style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                                        {{ $printAssignment->customer->customer_name }}
                                    </div>
                                    <div style="font-size: 13px; color: #334155; margin-bottom: 5px;">
                                        <strong>Customer ID:</strong> 
                                        <span style="font-family: monospace; font-weight: 800; color: #2563eb; background: #eff6ff; padding: 2px 6px; border-radius: 4px; border: 1px solid #dbeafe;">
                                            {{ $printAssignment->customer->customer_unique_id }}
                                        </span>
                                        @if($printAssignment->customer->pppUser)
                                            <span style="color: #64748b; margin-left: 8px;">(User: <strong>{{ $printAssignment->customer->pppUser->username }}</strong>)</span>
                                        @endif
                                    </div>
                                    @if($printAssignment->customer->mobile)
                                        <div style="font-size: 13px; color: #334155; margin-bottom: 5px;">
                                            <strong>Contact Mobile:</strong> {{ $printAssignment->customer->mobile }}
                                        </div>
                                    @endif
                                    <div style="font-size: 13px; color: #334155;">
                                        <strong>Installation Address:</strong> {{ $printAssignment->customer->address ?: $printAssignment->destination_title }}
                                    </div>
                                @else
                                    <div style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                                        {{ $printAssignment->destination_title }}
                                    </div>
                                    <div style="font-size: 13px; color: #64748b;">Internal ISP Infrastructure / Direct POP Handover</div>
                                @endif
                            </td>
                            <td style="width: 50%; vertical-align: top; padding: 18px 20px;">
                                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.8px; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                                    DELIVERY & HANDOVER DETAILS
                                </div>
                                <div style="font-size: 13px; color: #334155; margin-bottom: 6px;">
                                    <strong>Technician / Staff:</strong> 
                                    <span style="font-size: 15px; font-weight: 800; color: #0f172a; margin-left: 4px;">{{ $printAssignment->technician?->name ?? 'Direct Handover' }}</span>
                                </div>
                                @if($printAssignment->technician?->mobile)
                                    <div style="font-size: 13px; color: #334155; margin-bottom: 6px;">
                                        <strong>Staff Phone:</strong> {{ $printAssignment->technician->mobile }}
                                    </div>
                                @endif
                                <div style="font-size: 13px; color: #334155; margin-bottom: 6px;">
                                    <strong>Issued By:</strong> {{ $printAssignment->actionUser?->name ?? (auth()->check() ? auth()->user()->name : 'Store Admin') }}
                                </div>
                                <div style="font-size: 13px; color: #334155; margin-bottom: 6px;">
                                    <strong>Deployment Purpose:</strong> {{ $printAssignment->purpose ?: 'New Connection Setup' }}
                                </div>
                                @if($printAssignment->replacementFor)
                                    <div style="font-size: 12px; color: #b91c1c; font-weight: 700; margin-top: 8px; background: #fee2e2; padding: 5px 8px; border-radius: 4px; border: 1px solid #fecaca;">
                                        Replacement for Damaged: {{ $printAssignment->replacementFor->title }} (SN: {{ $printAssignment->replacementFor->serial_number ?: 'N/A' }})
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    {{-- 3. EQUIPMENT TABLE --}}
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px; border: 1px solid #0f172a; font-size: 13px;">
                        <thead>
                            <tr style="background-color: #f1f5f9; color: #0f172a; border-bottom: 2px solid #0f172a;">
                                <th style="border: 1px solid #cbd5e1; padding: 12px 10px; text-align: center; width: 40px; font-weight: 800; font-size: 12px;">#</th>
                                <th style="border: 1px solid #cbd5e1; padding: 12px 14px; text-align: left; font-weight: 800; font-size: 12px;">ITEM DESCRIPTION & MODEL</th>
                                <th style="border: 1px solid #cbd5e1; padding: 12px 12px; text-align: left; font-weight: 800; font-size: 12px; width: 120px;">CATEGORY</th>
                                <th style="border: 1px solid #cbd5e1; padding: 12px 12px; text-align: left; font-weight: 800; font-size: 12px; width: 160px;">SERIAL NO (S/N)</th>
                                <th style="border: 1px solid #cbd5e1; padding: 12px 12px; text-align: left; font-weight: 800; font-size: 12px; width: 140px;">MAC ADDRESS</th>
                                <th style="border: 1px solid #cbd5e1; padding: 12px 10px; text-align: center; font-weight: 800; font-size: 12px; width: 80px;">QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: 16px 10px; text-align: center; font-weight: 800; font-size: 14px;">1</td>
                                <td style="border: 1px solid #cbd5e1; padding: 16px 14px;">
                                    <div style="font-weight: 800; color: #0f172a; font-size: 15px; margin-bottom: 3px;">
                                        {{ $printAssignment->item?->title }}
                                    </div>
                                    @if($printAssignment->item?->brand || $printAssignment->item?->model_no)
                                        <div style="color: #64748b; font-size: 12px;">
                                            <span><strong>Brand:</strong> {{ $printAssignment->item->brand ?: 'N/A' }}</span>
                                            @if($printAssignment->item?->model_no)
                                                <span style="margin-left: 10px;"><strong>Model:</strong> {{ $printAssignment->item->model_no }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td style="border: 1px solid #cbd5e1; padding: 16px 12px; color: #334155; font-size: 13px; font-weight: 600;">
                                    {{ $printAssignment->item?->category?->name ?? 'Equipment' }}
                                </td>
                                <td style="border: 1px solid #cbd5e1; padding: 16px 12px; font-family: monospace; font-weight: 800; color: #0f172a; font-size: 14px;">
                                    @if($printAssignment->item?->serial_number)
                                        <span style="background: #f8fafc; padding: 3px 6px; border: 1px solid #e2e8f0; border-radius: 4px;">{{ $printAssignment->item->serial_number }}</span>
                                    @else
                                        <span style="color: #94a3b8;">—</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #cbd5e1; padding: 16px 12px; font-family: monospace; color: #475569; font-size: 13px;">
                                    @if($printAssignment->item?->mac_address)
                                        <span style="background: #f8fafc; padding: 3px 6px; border: 1px solid #e2e8f0; border-radius: 4px;">{{ $printAssignment->item->mac_address }}</span>
                                    @else
                                        <span style="color: #94a3b8;">—</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #cbd5e1; padding: 16px 10px; text-align: center; font-weight: 800; font-size: 16px; color: #0f172a;">
                                    {{ number_format($printAssignment->quantity, $printAssignment->item?->tracking_type === 'bulk' ? 2 : 0) }}
                                    <span style="font-size: 11px; font-weight: normal; color: #64748b; display: block; margin-top: 2px;">{{ $printAssignment->item?->category?->unit ?? 'pcs' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- 4. TERMS & UNDERTAKING --}}
                    <div style="background: #f8fafc; border-left: 4px solid #2563eb; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 18px; margin-bottom: 50px; font-size: 12px; color: #475569; line-height: 1.6;">
                        <strong style="color: #0f172a; font-size: 13px;">Notice & Custody Undertaking:</strong><br>
                        The equipment described above has been thoroughly inspected, tested, and handed over in full operational condition. The client/receiver undertakes complete responsibility for the safe custody and maintenance of the device. In the event of service disconnection or relocation, the equipment must be returned to the provider in good working order.
                    </div>

                    {{-- 5. SIGNATURES (Spacious with 65px room for pen signatures and stamps) --}}
                    <table style="width: 100%; border-collapse: collapse; margin-top: 60px; margin-bottom: 25px; text-align: center;">
                        <tr>
                            <td style="width: 33.33%; padding: 0 25px; vertical-align: bottom;">
                                <div style="height: 65px;"></div>
                                <div style="border-top: 1.5px solid #0f172a; padding-top: 8px; font-weight: 800; color: #0f172a; font-size: 13px;">
                                    Store In-Charge
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Authorized Signature & Seal</div>
                            </td>
                            <td style="width: 33.33%; padding: 0 25px; vertical-align: bottom;">
                                <div style="height: 65px;"></div>
                                <div style="border-top: 1.5px solid #0f172a; padding-top: 8px; font-weight: 800; color: #0f172a; font-size: 13px;">
                                    Technician / Delivered By
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Delivery Signature & Date</div>
                            </td>
                            <td style="width: 33.33%; padding: 0 25px; vertical-align: bottom;">
                                <div style="height: 65px;"></div>
                                <div style="border-top: 1.5px solid #0f172a; padding-top: 8px; font-weight: 800; color: #0f172a; font-size: 13px;">
                                    Customer / Receiver
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Received in Good Condition</div>
                            </td>
                        </tr>
                    </table>

                    {{-- 6. SYSTEM FOOTER --}}
                    <table style="width: 100%; border-collapse: collapse; border-top: 1px dashed #cbd5e1; padding-top: 8px; margin-top: 20px; font-size: 11px; color: #94a3b8;">
                        <tr>
                            <td style="text-align: left;">
                                Printed on: <strong>{{ date('d-M-Y h:i A') }}</strong> &bull; Issued by: {{ auth()->check() ? auth()->user()->name : 'Admin' }}
                            </td>
                            <td style="text-align: right;">
                                System Generated Document &bull; {{ config('app.name', 'ISP Billing') }}
                            </td>
                        </tr>
                    </table>

                </div>
            </div>

            <div class="modal-footer border-0 px-4 py-3" style="background:#ffffff;">
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

        // Use a permanent hidden iframe so browser extensions (like Grammarly) don't crash
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
        doc.write('@page { size: A4 portrait; margin: 15mm 18mm 15mm 18mm; }');
        doc.write('html, body { margin: 0; padding: 0; background: #ffffff !important; color: #0f172a; font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; line-height: 1.6; }');
        doc.write('#challan-print-area { border: none !important; box-shadow: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }');
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
                console.error('Iframe print error, attempting popup fallback:', err);
                const win = window.open('', '_blank', 'width=900,height=950');
                if (win) {
                    win.document.open();
                    win.document.write('<!DOCTYPE html><html><' + 'head><title>Delivery Challan<' + '/title><style>@page{size:A4 portrait;margin:15mm 18mm;}body{margin:0;padding:0;font-family:"Segoe UI",Arial,sans-serif;}#challan-print-area{border:none!important;box-shadow:none!important;padding:0!important;}table{border-collapse:collapse;width:100%;}*{box-sizing:border-box;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}<' + '/style><' + '/head><' + 'body>' + printElement.outerHTML + '<' + '/body><' + '/html>');
                    win.document.close();
                    win.focus();
                    win.print();
                }
            }
        }, 400);
    };
</script>

