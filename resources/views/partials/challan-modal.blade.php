@if($printAssignment)
<div class="modal fade show d-block" style="background: rgba(0,0,0,0.65);" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 text-xs fw-semibold">
                        <i class="bi bi-printer me-1"></i> {{ __('Dual Copy Gate Pass: Office & Customer Copy (Exactly 1 Page)') }}
                    </span>
                    <small class="text-muted d-block mt-1" style="font-size: 11px;">
                        {{ __('Fits both Office Copy and Customer Copy neatly on a single A4 page.') }}
                    </small>
                </div>
                <button type="button" wire:click="closePrintChallan" class="btn-close"></button>
            </div>

            <div class="modal-body px-3 py-3" style="background: #f1f5f9;">
                @php
                    $copies = [
                        [
                            'type'        => 'office',
                            'badge_title' => __('OFFICE COPY'),
                            'badge_bg'    => '#0f172a',
                            'badge_color' => '#ffffff',
                            'accent'      => '#0f172a',
                        ],
                        [
                            'type'        => 'customer',
                            'badge_title' => __('CUSTOMER COPY'),
                            'badge_bg'    => '#1e40af',
                            'badge_color' => '#ffffff',
                            'accent'      => '#1e40af',
                        ],
                    ];
                @endphp

                {{-- Printable Paper Container --}}
                <div id="challan-print-area" style="background: #ffffff; color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans Bengali', 'Hind Siliguri', 'Kalpurush', 'SolaimanLipi', sans-serif; font-size: 11px; line-height: 1.35; padding: 18px 24px; border: 1px solid #cbd5e1; border-radius: 8px; max-width: 820px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">

                    @foreach($copies as $index => $copy)
                        <div class="challan-copy" style="page-break-inside: avoid;">

                            {{-- 1. HEADER --}}
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px; border-bottom: 1.5px solid {{ $copy['accent'] }}; padding-bottom: 4px;">
                                <tr>
                                    <td style="width: 60%; vertical-align: top; padding-right: 10px;">
                                        <div style="font-size: 16px; font-weight: 800; text-transform: uppercase; color: #0f172a; letter-spacing: 0.5px; line-height: 1.2;">
                                            {{ siteUrlSettings('site_name') ?? config('app.name', 'ISP Billing') }}
                                        </div>
                                        <div style="font-size: 11px; font-weight: 600; color: #475569; margin-top: 1px;">
                                            {{ siteUrlSettings('site_title') ?? 'Internet Service Provider' }}
                                        </div>
                                        <div style="font-size: 10px; color: #64748b; margin-top: 2px; line-height: 1.3;">
                                            @if(siteUrlSettings('contact_address'))
                                                <span>{{ siteUrlSettings('contact_address') }}</span>
                                            @endif
                                            @if(siteUrlSettings('contact_phone'))
                                                <span style="margin-left: 6px;">&bull; Tel: {{ siteUrlSettings('contact_phone') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="width: 40%; vertical-align: top; text-align: right;">
                                        <div style="display: inline-block; background: {{ $copy['badge_bg'] }}; color: {{ $copy['badge_color'] }}; padding: 2.5px 10px; font-size: 10px; font-weight: 800; letter-spacing: 0.8px; border-radius: 3px; text-transform: uppercase; margin-bottom: 3px;">
                                            {{ $copy['badge_title'] }}
                                        </div>
                                        <table style="width: auto; float: right; border-collapse: collapse; font-size: 11px; margin-top: 1px;">
                                            <tr>
                                                <td style="text-align: right; padding: 1px 4px; font-weight: 600; color: #475569;">{{ __('Challan No:') }}</td>
                                                <td style="text-align: left; padding: 1px 4px; font-family: monospace; font-weight: 800; color: #2563eb; font-size: 12.5px;">{{ $printAssignment->challan_no ?? 'CH-' . str_pad($printAssignment->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right; padding: 1px 4px; font-weight: 600; color: #475569;">{{ __('Date:') }}</td>
                                                <td style="text-align: left; padding: 1px 4px; font-weight: 700; color: #0f172a;">{{ $printAssignment->withdraw_date ? $printAssignment->withdraw_date->format('d M, Y') : date('d M, Y') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- 2. PARTIES DETAILS --}}
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 10.5px;">
                                <tr>
                                    <td style="width: 50%; vertical-align: top; padding: 6px 10px; border-right: 1px dashed #cbd5e1;">
                                        <div style="font-size: 9.5px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 3px; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">
                                            {{ __('RECIPIENT / CUSTOMER') }}
                                        </div>
                                        @if($printAssignment->customer)
                                            <div style="font-size: 13px; font-weight: 800; color: #0f172a;">
                                                {{ $printAssignment->customer->customer_name }}
                                            </div>
                                            <div style="color: #334155; margin-top: 1px;">
                                                <strong>{{ __('ID') }}:</strong> <span style="font-family: monospace; font-weight: 800; color: #2563eb;">{{ $printAssignment->customer->customer_unique_id }}</span>
                                                @if($printAssignment->customer->pppUser)
                                                    <span style="color: #64748b; margin-left: 4px;">({{ $printAssignment->customer->pppUser->username }})</span>
                                                @endif
                                                @if($printAssignment->customer->mobile)
                                                    <span style="margin-left: 6px;">&bull; {{ __('Mob') }}: {{ $printAssignment->customer->mobile }}</span>
                                                @endif
                                            </div>
                                            <div style="color: #334155; margin-top: 1px;">
                                                <strong>{{ __('Address') }}:</strong> {{ Str::limit($printAssignment->customer->address ?: $printAssignment->destination_title, 45) }}
                                            </div>
                                        @else
                                            <div style="font-size: 13px; font-weight: 700; color: #0f172a;">
                                                {{ $printAssignment->destination_title }}
                                            </div>
                                            <div style="color: #64748b;">{{ __('Internal ISP Infrastructure / Direct Handover') }}</div>
                                        @endif
                                    </td>
                                    <td style="width: 50%; vertical-align: top; padding: 6px 10px;">
                                        <div style="font-size: 9.5px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 3px; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">
                                            {{ __('DELIVERY & TECHNICIAN') }}
                                        </div>
                                        <div style="color: #334155;">
                                            <strong>{{ __('Technician') }}:</strong> <span style="font-weight: 800; color: #0f172a;">{{ $printAssignment->technician?->name ?? __('Direct Handover') }}</span>
                                            @if($printAssignment->technician?->mobile)
                                                <span style="color: #64748b; margin-left: 4px;">({{ $printAssignment->technician->mobile }})</span>
                                            @endif
                                        </div>
                                        <div style="color: #334155; margin-top: 1px;">
                                            <strong>{{ __('Issued By') }}:</strong> {{ $printAssignment->actionUser?->name ?? (auth()->check() ? auth()->user()->name : __('Store Admin')) }}
                                            <span style="margin-left: 6px;">&bull; <strong>{{ __('Purpose') }}:</strong> {{ Str::limit($printAssignment->purpose ?: __('New Setup'), 25) }}</span>
                                        </div>
                                        @if($printAssignment->replacementFor)
                                            <div style="font-size: 9.5px; color: #b91c1c; font-weight: 700; margin-top: 2px;">
                                                {{ __('Replacement for') }}: {{ $printAssignment->replacementFor->title }} (SN: {{ $printAssignment->replacementFor->serial_number ?: 'N/A' }})
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            {{-- 3. EQUIPMENT TABLE --}}
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px; border: 1.5px solid #0f172a; font-size: 10.5px;">
                                <thead>
                                    <tr style="background-color: #f1f5f9; color: #0f172a; border-bottom: 1.5px solid #0f172a;">
                                        <th style="border: 1px solid #cbd5e1; padding: 4px 6px; text-align: center; width: 30px; font-weight: 800; font-size: 10px;">#</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 4px 8px; text-align: left; font-weight: 800; font-size: 10px;">{{ __('ITEM DESCRIPTION & MODEL') }}</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 4px 8px; text-align: left; font-weight: 800; font-size: 10px; width: 95px;">{{ __('CATEGORY') }}</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 4px 8px; text-align: left; font-weight: 800; font-size: 10px; width: 140px;">{{ __('SERIAL NO (S/N)') }}</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 4px 8px; text-align: left; font-weight: 800; font-size: 10px; width: 120px;">{{ __('MAC ADDRESS') }}</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 4px 6px; text-align: center; font-weight: 800; font-size: 10px; width: 55px;">{{ __('QTY') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: 800;">1</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px 8px;">
                                            <div style="font-weight: 800; color: #0f172a; font-size: 11.5px;">
                                                {{ $printAssignment->item?->title }}
                                            </div>
                                            @if($printAssignment->item?->brand || $printAssignment->item?->model_no)
                                                <div style="color: #64748b; font-size: 9.5px;">
                                                    {{ $printAssignment->item->brand }} {{ $printAssignment->item->model_no }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px 8px; color: #334155; font-size: 10.5px; font-weight: 600;">
                                            {{ $printAssignment->item?->category?->name ?? 'Equipment' }}
                                        </td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px 8px; font-family: monospace; font-weight: 800; color: #0f172a; font-size: 11px;">
                                            {{ $printAssignment->item?->serial_number ?: '—' }}
                                        </td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px 8px; font-family: monospace; color: #475569; font-size: 10.5px;">
                                            {{ $printAssignment->item?->mac_address ?: '—' }}
                                        </td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: 800; font-size: 12px; color: #0f172a;">
                                            {{ number_format($printAssignment->quantity, $printAssignment->item?->tracking_type === 'bulk' ? 2 : 0) }}
                                            <span style="font-size: 9px; font-weight: normal; color: #64748b; display: block;">{{ __($printAssignment->item?->category?->unit ?? 'pcs') }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- 4. NOTICE & TERMS --}}
                            <div style="background: #f8fafc; border-left: 3px solid {{ $copy['accent'] }}; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; border-radius: 3px; padding: 4px 8px; margin-bottom: 8px; font-size: 9.5px; color: #475569; line-height: 1.35;">
                                <strong>{{ __('Declaration:') }}</strong> {{ __('The above equipment is delivered in good working condition. The receiver undertakes safe custody. In case of service disconnection, all equipment must be returned to provider.') }}
                            </div>

                            {{-- 5. SIGNATURES (Spacious 35px height for signature & seal) --}}
                            <table style="width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 4px; text-align: center;">
                                <tr>
                                    <td style="width: 33.33%; padding: 0 15px; vertical-align: bottom;">
                                        <div style="height: 35px;"></div>
                                        <div style="border-top: 1.5px solid #0f172a; padding-top: 3px; font-weight: 800; color: #0f172a; font-size: 10.5px;">
                                            {{ __('Store In-Charge') }}
                                        </div>
                                        <div style="font-size: 8.5px; color: #64748b;">{{ __('Seal & Signature') }}</div>
                                    </td>
                                    <td style="width: 33.33%; padding: 0 15px; vertical-align: bottom;">
                                        <div style="height: 35px;"></div>
                                        <div style="border-top: 1.5px solid #0f172a; padding-top: 3px; font-weight: 800; color: #0f172a; font-size: 10.5px;">
                                            {{ __('Technician / Staff') }}
                                        </div>
                                        <div style="font-size: 8.5px; color: #64748b;">{{ __('Delivered By & Date') }}</div>
                                    </td>
                                    <td style="width: 33.33%; padding: 0 15px; vertical-align: bottom;">
                                        <div style="height: 35px;"></div>
                                        <div style="border-top: 1.5px solid #0f172a; padding-top: 3px; font-weight: 800; color: #0f172a; font-size: 10.5px;">
                                            {{ __('Customer / Receiver') }}
                                        </div>
                                        <div style="font-size: 8.5px; color: #64748b;">{{ __('Received in Good Condition') }}</div>
                                    </td>
                                </tr>
                            </table>

                        </div>

                        {{-- Scissor Cutting Divider between Office Copy & Customer Copy --}}
                        @if($index === 0)
                            <table class="challan-divider" style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                                <tr>
                                    <td style="border-bottom: 1.5px dashed #64748b; width: 33%;"></td>
                                    <td style="text-align: center; white-space: nowrap; padding: 0 10px; font-size: 9.5px; font-weight: 700; color: #475569; letter-spacing: 0.5px;">
                                        ✂ &nbsp; {{ __('CUT HERE TO SEPARATE') }} &nbsp; ✂
                                    </td>
                                    <td style="border-bottom: 1.5px dashed #64748b; width: 33%;"></td>
                                </tr>
                            </table>
                        @endif
                    @endforeach

                    {{-- SYSTEM FOOTER --}}
                    <div style="border-top: 1px dashed #cbd5e1; padding-top: 4px; margin-top: 6px; font-size: 8.5px; color: #94a3b8; display: flex; justify-content: space-between;">
                        <span>{{ __('Printed on:') }} <strong>{{ date('d-M-Y h:i A') }}</strong> &bull; {{ __('Operator:') }} {{ auth()->check() ? auth()->user()->name : __('Admin') }}</span>
                        <span>{{ config('app.name', 'ISP Billing') }} &bull; {{ __('Dual Gate Pass') }}</span>
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
        doc.write('<title>' + ('{{ __('Delivery Challan') }} - ' + ('{{ $printAssignment?->challan_no }}' || '{{ __('Gate Pass') }}')) + '<' + '/title>');
        doc.write('<style>');
        doc.write('@page { size: A4 portrait; margin: 6mm 10mm 6mm 10mm; }');
        doc.write('html, body { margin: 0; padding: 0; background: #ffffff !important; color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans Bengali", "Hind Siliguri", "Kalpurush", "SolaimanLipi", sans-serif; font-size: 11px; line-height: 1.35; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }');
        doc.write('#challan-print-area { border: none !important; box-shadow: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; margin: 0 !important; }');
        doc.write('.challan-copy { page-break-inside: avoid !important; }');
        doc.write('table { border-collapse: collapse; width: 100%; }');
        doc.write('* { box-sizing: border-box; }');
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
                const win = window.open('', '_blank', 'width=850,height=900');
                if (win) {
                    win.document.open();
                    win.document.write('<!DOCTYPE html><html><' + 'head><title>{{ __('Delivery Challan') }}<' + '/title><style>@page{size:A4 portrait;margin:6mm 10mm;}html,body{margin:0;padding:0;font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans Bengali", "Hind Siliguri", "Kalpurush", "SolaimanLipi", sans-serif;font-size:11px;}#challan-print-area{border:none!important;box-shadow:none!important;padding:0!important;}table{border-collapse:collapse;width:100%;}*{box-sizing:border-box;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}<' + '/style><' + '/head><' + 'body>' + printElement.innerHTML + '<' + '/body><' + '/html>');
                    win.document.close();
                    win.focus();
                    win.print();
                }
            }
        }, 400);
    };
</script>
