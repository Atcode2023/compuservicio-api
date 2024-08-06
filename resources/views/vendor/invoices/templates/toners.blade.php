<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $invoice->name }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style type="text/css" media="screen">
        html {
            font-family: sans-serif;
            line-height: 1.15;
            margin: 0;
        }

        @page {
            size: 21cm 29.7cm portrait;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 10px;
            margin: 16pt 36px 35pt 45px;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        strong {
            font-weight: bolder;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        table {
            border-collapse: collapse;
        }

        .table-custom,
        .table-custom td,
        .table-custom th {
            border: 1px solid;
        }

        .table-custom,
        .table-custom th.no-border,
        .table-custom td.no-border {
            border-left: 1px solid transparent;
            border-bottom: 1px solid transparent;
        }

        .border-bottom-import {
            border-bottom: 1px solid;
        }


        th {
            text-align: inherit;
        }

        h4,
        .h4 {
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        h4,
        .h4 {
            font-size: 1.5rem;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
        }

        .table.table-items td {
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .no-margin {
            margin: 0 !important;
        }

        .no-padding {
            padding: 0 !important;
        }

        .mt-1 {
            margin-top: 0.5rem !important;
        }

        .mt-2 {
            margin-top: 1rem !important;
        }

        .mt-3 {
            margin-top: 1.5rem !important;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }



        .mb-0 {
            margin-bottom: 0;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .px-1 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .text-bold {
            font-weight: bold
        }

        .font-10 {
            font-size: 10px;
        }

        .font-12 {
            font-size: 12px;
        }

        .font-14 {
            font-size: 14px;
        }

        .font-16 {
            font-size: 16px;
        }

        .font-18 {
            font-size: 18px;
        }


        * {
            font-family: "DejaVu Sans";
        }

        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        table,
        th,
        tr,
        td,
        p,
        div {
            line-height: 1.1;
        }

        .party-header {
            font-size: 1.5rem;
            font-weight: 400;
        }

        .total-amount {
            font-size: 12px;
            font-weight: 700;
        }

        .border-0 {
            border: none !important;
        }

        .box-bottom {
            display: flex !important;
            justify-content: space-between;
        }

        .full-width {
            width: 100% !important;
        }

        .cool-gray {
            color: #6B7280;
        }

        .bg-dark {
            background-color: #6B7280;
            color: #fff
        }
    </style>
</head>

<body>
    {{-- Header --}}
    @if ($invoice->logo)
        <img src="{{ $invoice->getLogo() }}" alt="logo" height="100">
    @endif
    <table class="table mb-0">
        <tbody>
            <tr class="no-padding no-margin">
                <td class="border-0 pl-0 no-padding no-margin" width="60%">
                    {{-- <p class="no-margin">{{ $invoice->getDate() }}</p> --}}
                    <h4 class="text-uppercase no-padding no-margin mb-0">
                        <strong>COMPUSERVICIOS c.a.</strong>
                    </h4>
                    <p class="no-padding no-margin font-14">Rif: J-31526760-8</p>

                </td>
                <td class="border-0 pl-0 no-padding no-margin">
                    {{-- <p class="mb-0 text-uppercase font-16 text-center text-bold mt-1">Contrato de servicio</p> --}}
                    @isset($invoice->buyer->date->from)
                        <p class="no-padding no-margin font-12 text-center">
                            <span class="text-bold">Desde: </span> {{ $invoice->buyer->date->from }}
                        </p>
                        <p class="no-padding no-margin font-12 text-center">
                            <span class="text-bold">Hasta:</span> {{ $invoice->buyer->date->to }}
                        </p>
                    @else
                        <p class="no-padding no-margin font-14 text-center">
                            Fecha: {{ $invoice->buyer->date }}
                        </p>
                    @endisset
                    <p class="no-padding no-margin font-14 text-center">Nro. {{ $invoice->getSerialNumber() }}</p>
                </td>
            </tr>
            <tr class="no-padding no-margin">
                <td colspan="2" class="no-padding no-margin">
                    <p class="no-margin font-10 text-center">
                        Av unda E/Carreras 5 y 6 Edif. San Sebastian, Piso P/B, Local 1, Sector Centro. - Tlf:
                        0257-2531551 -
                        Guanare - Estado Portuguesa
                    </p>
                </td>
            </tr>

        </tbody>
    </table>

    {{-- Table --}}
    <table class="table-custom" width="100%">
        <thead>
            <tr class="bg-dark">
                {{-- <th scope="col" class="border-0 pl-0">{{ __('invoices::invoice.description') }}</th>
                    @if ($invoice->hasItemUnits)
                        <th scope="col" class="text-center border-0">{{ __('invoices::invoice.units') }}</th>
                    @endif
                    <th scope="col" class="text-center border-0">{{ __('invoices::invoice.quantity') }}</th>
                    <th scope="col" class="text-right border-0">{{ __('invoices::invoice.price') }}</th>
                    @if ($invoice->hasItemDiscount)
                        <th scope="col" class="text-right border-0">{{ __('invoices::invoice.discount') }}</th>
                    @endif
                    @if ($invoice->hasItemTax)
                        <th scope="col" class="text-right border-0">{{ __('invoices::invoice.tax') }}</th>
                    @endif
                    <th scope="col" class="text-right border-0 pr-0">{{ __('invoices::invoice.sub_total') }}</th> --}}
                <th class="text-center">Contrato</th>
                <th class="text-center">Cédula</th>
                <th class="text-center">Nombre</th>
                <th class="text-center">Fecha Recibido</th>
                <th class="text-center">Fecha Entregado</th>
                <th class="text-center">Total Dolar</th>
                {{-- <th class="text-center">Total BS</th> --}}
            </tr>
        </thead>
        <tbody>
            {{-- Items --}}
            @foreach ($invoice->buyer->reports as $report)
                <tr>
                    <td class="text-center">
                        {{ $report->id }}
                    </td>
                    <td class="text-center">
                        {{ $report->customer?->ci }}
                    </td>
                    <td class="text-center">
                        {{ $report->customer?->name }}
                    </td>
                    <td class="text-center">
                        {{ $report->created_at->format('d/m/Y') }}
                    </td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($report->date_delivery)->format('d/m/Y') }}
                    </td>
                    <td class="text-center">
                        @if ($report->price)
                            {{ $report->price }}
                        @endif
                    </td>
                    {{-- <td class="text-center">
                        @if ($report->invoice?->payment_mode == 0)
                            {{ $report->invoice?->total }}
                        @endif
                    </td> --}}
                    {{-- <td class="text-center">
                        @if ($report->invoice?->payment_mode == 1)
                            {{ $report->invoice?->total * $report->invoice->bs_bcv }}
                        @endif
                    </td> --}}
                </tr>
            @endforeach
            {{-- Summary --}}
            @if ($invoice->hasItemOrInvoiceDiscount())
                <tr>
                    <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                    <td class="text-right pl-0">{{ __('invoices::invoice.total_discount') }}</td>
                    <td class="text-right pr-0">
                        {{ $invoice->formatCurrency($invoice->total_discount) }}
                    </td>
                </tr>
            @endif
            @if ($invoice->taxable_amount)
                <tr>
                    <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                    <td class="text-right pl-0">{{ __('invoices::invoice.taxable_amount') }}</td>
                    <td class="text-right pr-0">
                        {{ $invoice->formatCurrency($invoice->taxable_amount) }}
                    </td>
                </tr>
            @endif
            @if ($invoice->tax_rate)
                <tr>
                    <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                    <td class="text-right pl-0">{{ __('invoices::invoice.tax_rate') }}</td>
                    <td class="text-right pr-0">
                        {{ $invoice->tax_rate }}%
                    </td>
                </tr>
            @endif
            @if ($invoice->hasItemOrInvoiceTax())
                <tr>
                    <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                    <td class="text-right pl-0">{{ __('invoices::invoice.total_taxes') }}</td>
                    <td class="text-right pr-0">
                        {{ $invoice->formatCurrency($invoice->total_taxes) }}
                    </td>
                </tr>
            @endif
            @if ($invoice->shipping_amount)
                <tr>
                    <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                    <td class="text-right pl-0">{{ __('invoices::invoice.shipping') }}</td>
                    <td class="text-right pr-0">
                        {{ $invoice->formatCurrency($invoice->shipping_amount) }}
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="4" class="border-0"></td>
                <td class="text-right pl-0">Total</td>
                <td class="text-right pl-0"> {{ $invoice->buyer->total_usd }}$</td>
                {{-- <td class="text-right pr-0 total-amount">
                    {{ $invoice->buyer->total_bs }}bs
                </td> --}}
            </tr>
        </tbody>
    </table>

    <script type="text/php">
            if (isset($pdf) && $PAGE_COUNT > 1) {
                $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
</body>

</html>
