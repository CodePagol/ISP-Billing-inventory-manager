@if($printAssignment)
<div class="modal fade show d-block" style="background: rgba(0,0,0,0.65);" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 text-xs fw-semibold">
                        <i class="bi bi-printer me-1"></i> {{ __('Dual Copy Gate Pass: Office & Customer Copy (Full A4 Page)') }}
                    </span>
                    <small class="text-muted d-block mt-1" style="font-size: 11px;">
                        {{ __('সম্পূর্ণ A4 পাতার উচ্চতা জুড়ে ২টি কপি (অফিস কপি ও গ্রাহক কপি) সমানভাবে বিন্যস্ত থাকবে।') }}
                    </small>
                </div>
                <button type="button" wire:click="closePrintChallan" class="btn-close"></button>
            </div>

            <div class="modal-body px-3 py-3" style="background: #f1f5f9;">
                @php
                    $copies = [
                        [
                            'type'        => 'office',
                            'badge_title' => 'OFFICE COPY / অফিস কপি',
                            'badge_bg'    => '#0f172a',
                            'badge_color' => '#ffffff',
                            'accent'      => '#0f172a',
                        ],
                        [
                            'type'        => 'customer',
                            'badge_title' => 'CUSTOMER COPY / গ্রাহক কপি',
                            'badge_bg'    => '#1e40af',
                            'badge_color' => '#ffffff',
                            'accent'      => '#1e40af',
                        ],
                    ];
                @endphp

                {{-- Printable Paper Container --}}
                <div id="challan-print-area" style="background: #ffffff; color: #0f172a; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; line-height: 1.5; padding: 24px 30px; border: 1px solid #cbd5e1; border-radius: 8px; max-width: 860px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">

                    @foreach($copies as $index => $copy)
                        <div class="challan-copy" style="page-break-inside: avoid;">

                            {{-- 1. HEADER --}}
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; border-bottom: 2px solid {{ $copy['accent'] }}; padding-bottom: 8px;">
                                <tr>
                                    <td style="width: 60%; vertical-align: top; padding-right: 15px;">
                                        <div style="font-size: 19px; font-weight: 800; text-transform: uppercase; color: #0f172a; letter-spacing: 0.5px; line-height: 1.2;">
                                            {{ siteUrlSettings('site_name') ?? config('app.name', 'ISP Billing') }}
                                        </div>
                                        <div style="font-size: 12px; font-weight: 600; color: #475569; margin-top: 2px;">
                                            {{ siteUrlSettings('site_title') ?? 'Internet Service Provider' }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b; margin-top: 3px; line-height: 1.4;">
                                            @if(siteUrlSettings('contact_address'))
                                                <span>{{ siteUrlSettings('contact_address') }}</span>
                                            @endif
                                            @if(siteUrlSettings('contact_phone'))
                                                <span style="margin-left: 8px;">&bull; Tel: {{ siteUrlSettings('contact_phone') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="width: 40%; vertical-align: top; text-align: right;">
                                        <div style="display: inline-block; background: {{ $copy['badge_bg'] }}; color: {{ $copy['badge_color'] }}; padding: 4px 12px; font-size: 11px; font-weight: 800; letter-spacing: 0.8px; border-radius: 4px; text-transform: uppercase; margin-bottom: 6px;">
                                            {{ $copy['badge_title'] }}
                                        </div>
                                        <table style="width: auto; float: right; border-collapse: collapse; font-size: 12px; margin-top: 2px;">
                                            <tr>
                                                <td style="text-align: right; padding: 2px 6px; font-weight: 600; color: #475569;">Challan No:</td>
                                                <td style="text-align: left; padding: 2px 6px; font-family: monospace; font-weight: 800; color: #2563eb; font-size: 14px;">{{ $printAssignment->challan_no ?? 'CH-' . str_pad($printAssignment->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right; padding: 2px 6px; font-weight: 600; color: #475569;">Date:</td>
                                                <td style="text-align: left; padding: 2px 6px; font-weight: 700; color: #0f172a;">{{ $printAssignment->withdraw_date ? $printAssignment->withdraw_date->format('d M, Y') : date('d M, Y') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- 2. PARTIES DETAILS --}}
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 11.5px;">
                                <tr>
                                    <td style="width: 50%; vertical-align: top; padding: 10px 14px; border-right: 1px dashed #cbd5e1;">
                                        <div style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.6px; margin-bottom: 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px;">
                                            RECIPIENT / CUSTOMER
                                        </div>
                                        @if($printAssignment->customer)
                                            <div style="font-size: 14.5px; font-weight: 800; color: #0f172a; margin-bottom: 3px;">
                                                {{ $printAssignment->customer->customer_name }}
                                            </div>
                                            <div style="color: #334155; margin-bottom: 2px;">
                                                <strong>Customer ID:</strong> 
                                                <span style="font-family: monospace; font-weight: 800; color: #2563eb; background: #eff6ff; padding: 1px 5px; border-radius: 3px; border: 1px solid #dbeafe;">
                                                    {{ $printAssignment->customer->customer_unique_id }}
                                                </span>
                                                @if($printAssignment->customer->pppUser)
                                                    <span style="color: #64748b; margin-left: 6px;">(User: <strong>{{ $printAssignment->customer->pppUser->username }}</strong>)</span>
                                                @endif
                                            </div>
                                            @if($printAssignment->customer->mobile)
                                                <div style="color: #334155; margin-bottom: 2px;">
                                                    <strong>Mobile:</strong> {{ $printAssignment->customer->mobile }}
                                                </div>
                                            @endif
                                            <div style="color: #334155;">
                                                <strong>Address:</strong> {{ Str::limit($printAssignment->customer->address ?: $printAssignment->destination_title, 55) }}
                                            </div>
                                        @else
                                            <div style="font-size: 14.5px; font-weight: 800; color: #0f172a; margin-bottom: 3px;">
                                                {{ $printAssignment->destination_title }}
                                            </div>
                                            <div style="color: #64748b;">Internal ISP Infrastructure / Direct Distribution</div>
                                        @endif
                                    </td>
                                    <td style="width: 50%; vertical-align: top; padding: 10px 14px;">
                                        <div style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.6px; margin-bottom: 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px;">
                                            DELIVERY & TECHNICIAN
                                        </div>
                                        <div style="color: #334155; margin-bottom: 3px;">
                                            <strong>Technician / Staff:</strong> 
                                            <span style="font-weight: 800; color: #0f172a; font-size: 13.5px; margin-left: 3px;">{{ $printAssignment->technician?->name ?? 'Direct Handover' }}</span>
                                            @if($printAssignment->technician?->mobile)
                                                <span style="color: #64748b; margin-left: 6px;">({{ $printAssignment->technician->mobile }})</span>
                                            @endif
                                        </div>
                                        <div style="color: #334155; margin-bottom: 3px;">
                                            <strong>Issued By:</strong> {{ $printAssignment->actionUser?->name ?? (auth()->check() ? auth()->user()->name : 'Store Admin') }}
                                        </div>
                                        <div style="color: #334155; margin-bottom: 3px;">
                                            <strong>Purpose:</strong> {{ $printAssignment->purpose ?: 'New Connection Setup' }}
                                        </div>
                                        @if($printAssignment->replacementFor)
                                            <div style="font-size: 10.5px; color: #b91c1c; font-weight: 700; margin-top: 4px; background: #fee2e2; padding: 2px 6px; border-radius: 3px; border: 1px solid #fecaca;">
                                                Replacement for: {{ $printAssignment->replacementFor->title }} (SN: {{ $printAssignment->replacementFor->serial_number ?: 'N/A' }})
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            {{-- 3. EQUIPMENT TABLE --}}
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; border: 1.5px solid #0f172a; font-size: 11.5px;">
                                <thead>
                                    <tr style="background-color: #f1f5f9; color: #0f172a; border-bottom: 1.5px solid #0f172a;">
                                        <th style="border: 1px solid #cbd5e1; padding: 7px 8px; text-align: center; width: 35px; font-weight: 800; font-size: 11px;">#</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 7px 10px; text-align: left; font-weight: 800; font-size: 11px;">ITEM DESCRIPTION & MODEL</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 7px 10px; text-align: left; font-weight: 800; font-size: 11px; width: 110px;">CATEGORY</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 7px 10px; text-align: left; font-weight: 800; font-size: 11px; width: 150px;">SERIAL NO (S/N)</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 7px 10px; text-align: left; font-weight: 800; font-size: 11px; width: 135px;">MAC ADDRESS</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 7px 8px; text-align: center; font-weight: 800; font-size: 11px; width: 65px;">QTY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="border: 1px solid #cbd5e1; padding: 10px 8px; text-align: center; font-weight: 800; font-size: 13px;">1</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 10px 10px;">
                                            <div style="font-weight: 800; color: #0f172a; font-size: 13.5px; margin-bottom: 2px;">
                                                {{ $printAssignment->item?->title }}
                                            </div>
                                            @if($printAssignment->item?->brand || $printAssignment->item?->model_no)
                                                <div style="color: #64748b; font-size: 10.5px;">
                                                    <span><strong>Brand:</strong> {{ $printAssignment->item->brand ?: 'N/A' }}</span>
                                                    @if($printAssignment->item?->model_no)
                                                        <span style="margin-left: 8px;"><strong>Model:</strong> {{ $printAssignment->item->model_no }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td style="border: 1px solid #cbd5e1; padding: 10px 10px; color: #334155; font-size: 11.5px; font-weight: 600;">
                                            {{ $printAssignment->item?->category?->name ?? 'Equipment' }}
                                        </td>
                                        <td style="border: 1px solid #cbd5e1; padding: 10px 10px; font-family: monospace; font-weight: 800; color: #0f172a; font-size: 13px;">
                                            @if($printAssignment->item?->serial_number)
                                                <span style="background: #f8fafc; padding: 2px 6px; border: 1px solid #e2e8f0; border-radius: 4px;">{{ $printAssignment->item->serial_number }}</span>
                                            @else
                                                <span style="color: #94a3b8;">—</span>
                                            @endif
                                        </td>
                                        <td style="border: 1px solid #cbd5e1; padding: 10px 10px; font-family: monospace; color: #475569; font-size: 12px;">
                                            @if($printAssignment->item?->mac_address)
                                                <span style="background: #f8fafc; padding: 2px 6px; border: 1px solid #e2e8f0; border-radius: 4px;">{{ $printAssignment->item->mac_address }}</span>
                                            @else
                                                <span style="color: #94a3b8;">—</span>
                                            @endif
                                        </td>
                                        <td style="border: 1px solid #cbd5e1; padding: 10px 8px; text-align: center; font-weight: 800; font-size: 14px; color: #0f172a;">
                                            {{ number_format($printAssignment->quantity, $printAssignment->item?->tracking_type === 'bulk' ? 2 : 0) }}
                                            <span style="font-size: 10px; font-weight: normal; color: #64748b; display: block; margin-top: 1px;">{{ $printAssignment->item?->category?->unit ?? 'pcs' }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- 4. NOTICE & TERMS --}}
                            <div style="background: #f8fafc; border-left: 3.5px solid {{ $copy['accent'] }}; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; border-radius: 4px; padding: 7px 12px; margin-bottom: 18px; font-size: 10.5px; color: #475569; line-height: 1.45;">
                                <strong style="color: #0f172a;">Declaration & Custody Undertaking:</strong> 
                                The equipment mentioned above has been delivered in full working condition. The receiver/customer undertakes safe custody of the equipment. In case of service disconnection, all equipment must be returned to the provider in good condition.
                            </div>

                            {{-- 5. SIGNATURES (Spacious 55px room for signature & seal) --}}
                            <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 8px; text-align: center;">
                                <tr>
                                    <td style="width: 33.33%; padding: 0 20px; vertical-align: bottom;">
                                        <div style="height: 55px;"></div>
                                        <div style="border-top: 1.5px solid #0f172a; padding-top: 5px; font-weight: 800; color: #0f172a; font-size: 11px;">
                                            Store In-Charge
                                        </div>
                                        <div style="font-size: 9.5px; color: #64748b; margin-top: 1px;">Authorized Signature & Seal</div>
                                    </td>
                                    <td style="width: 33.33%; padding: 0 20px; vertical-align: bottom;">
                                        <div style="height: 55px;"></div>
                                        <div style="border-top: 1.5px solid #0f172a; padding-top: 5px; font-weight: 800; color: #0f172a; font-size: 11px;">
                                            Technician / Staff
                                        </div>
                                        <div style="font-size: 9.5px; color: #64748b; margin-top: 1px;">Delivered By & Date</div>
                                    </td>
                                    <td style="width: 33.33%; padding: 0 20px; vertical-align: bottom;">
                                        <div style="height: 55px;"></div>
                                        <div style="border-top: 1.5px solid #0f172a; padding-top: 5px; font-weight: 800; color: #0f172a; font-size: 11px;">
                                            Customer / Receiver
                                        </div>
                                        <div style="font-size: 9.5px; color: #64748b; margin-top: 1px;">Received in Good Condition</div>
                                    </td>
                                </tr>
                            </table>

                        </div>

                        {{-- Scissor Cutting Divider between Office Copy & Customer Copy --}}
                        @if($index === 0)
                            <div class="challan-divider" style="margin: 20px 0 22px 0; border-top: 1.5px dashed #475569; text-align: center; position: relative;">
                                <span style="background: #ffffff; padding: 3px 18px; color: #334155; font-size: 11px; font-weight: 700; position: relative; top: -10px; border: 1px solid #94a3b8; border-radius: 12px; letter-spacing: 0.5px;">
                                    ✂ &nbsp; CUT HERE / এখান থেকে কেটে আলাদা করুন (OFFICE COPY / CUSTOMER COPY) &nbsp; ✂
                                </span>
                            </div>
                        @endif
                    @endforeach

                    {{-- SYSTEM FOOTER --}}
                    <div style="border-top: 1px dashed #cbd5e1; padding-top: 6px; margin-top: 12px; font-size: 9.5px; color: #94a3b8; display: flex; justify-content: space-between;">
                        <span>Printed on: <strong>{{ date('d-M-Y h:i A') }}</strong> &bull; Operator: {{ auth()->check() ? auth()->user()->name : 'Admin' }}</span>
                        <span>{{ config('app.name', 'ISP Billing') }} &bull; Dual Gate Pass (Full A4)</span>
                    </div>

                </div>
            </div>

            <div class="modal-footer border-0 px-4 py-3" style="background:#ffffff;">
                <button type="button" wire:click="closePrintChallan" class="btn btn-light rounded-3 px-4">{{ __('Close') }}</button>
                <button type="button" onclick="window.doPrintChallan()" class="btn btn-success rounded-3 px-5 fw-semibold shadow-sm">
                    <i class="bi bi-printer me-2"></i> {{ __('Print Dual Copy (A4)') }}
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
        doc.write('<title>Delivery Challan - ' + ('{{ $printAssignment?->challan_no }}' || 'Gate Pass') + '<' + '/title>');
        doc.write('<style>');
        doc.write('@page { size: A4 portrait; margin: 8mm 12mm 8mm 12mm; }');
        doc.write('html, body { margin: 0; padding: 0; background: #ffffff !important; color: #0f172a; font-family: "Segoe UI", Arial, sans-serif; font-size: 12px; line-height: 1.5; height: 100%; }');
        doc.write('#challan-print-area { border: none !important; box-shadow: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }');
        doc.write('.challan-copy { flex: 1; display: flex; flex-direction: column; justify-content: space-between; page-break-inside: avoid !important; }');
        doc.write('table { border-collapse: collapse; width: 100%; }');
        doc.write('* { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }');
        doc.write('<' + '/style>');
        doc.write('<' + '/head><' + 'body>');
        doc.write(printElement.innerHTML);
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
                    win.document.write('<!DOCTYPE html><html><' + 'head><title>Delivery Challan<' + '/title><style>@page{size:A4 portrait;margin:8mm 12mm;}body{margin:0;padding:0;font-family:"Segoe UI",Arial,sans-serif;height:100%;}#challan-print-area{border:none!important;box-shadow:none!important;padding:0!important;height:100%;display:flex;flex-direction:column;justify-content:space-between;}table{border-collapse:collapse;width:100%;}*{box-sizing:border-box;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}<' + '/style><' + '/head><' + 'body>' + printElement.innerHTML + '<' + '/body><' + '/html>');
                    win.document.close();
                    win.focus();
                    win.print();
                }
            }
        }, 400);
    };
</script>
