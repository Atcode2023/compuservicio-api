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
            clear: none
        }


        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 10px;
            margin: 16pt 16px;
            text-transform: uppercase
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

        .table-custom,
        .table-custom td,
        .table-custom th {
            border: 1px solid;
        }


        .border-bottom-import {
            border-bottom: 1px solid !important;
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

        .text-capitalize {
            text-transform: capitalize !important;
        }

        .text-bold {
            font-weight: bold
        }

        .text-justify {
            text-align: justify
        }

        .font-6 {
            font-size: 6px;
        }

        .font-8 {
            font-size: 8px;
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
                    <p class="mb-0 text-uppercase font-16 text-center text-bold mt-1">Contrato de servicio</p>
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

    <table class="table mb-0 uppercase">
        <tbody>
            <tr class="no-padding no-margin">
                <td colspan="2" class="border-0 pl-0 no-padding no-margin" width="100%">
                    <p class="text-uppercase text-center no-padding no-margin font-12 text-bold">
                        datos del cliente
                    </p>
                </td>
            </tr>
            <tr>
                <td width="50%" class="no-padding no-margin">
                    <p class="no-margin">
                        <span class="text-bold">Cliente:</span>
                        {{ $invoice->seller->name }}
                    </p>
                    <p class="no-margin">
                        <span class="text-bold">Teléfono:</span>
                        {{ $invoice->seller->phone }}
                    </p>
                    <p class="no-margin">
                        <span class="text-bold">Dirección:</span>
                        {{ $invoice->seller->address }}
                    </p>
                    <p class="no-margin">
                        <span class="text-bold">Rif / Cédula: </span>
                        {{ $invoice->seller->ci }}
                    </p>
                </td>
                <td width="50%" class="no-padding no-margin">
                    <p class="no-margin">
                        <span class="text-bold">Fecha de recepción:</span>
                        {{ $invoice->seller->created_at->format('d/m/Y') }}
                    </p>
                    <p class="no-margin">
                        <span class="text-bold">Hora de recepción:</span>
                        {{ $invoice->seller->created_at->format('h:s') }}
                    </p>
                    <p class="no-margin">
                        <span class="text-bold">Fecha de entrega:</span>
                        @if ($invoice->seller->date_delivery)
                            {{ \Carbon\Carbon::parse($invoice->seller->date_delivery)->format('d/m/Y') }}
                        @endif
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table mb-0">
        <tbody>
            <tr class="no-padding no-margin">
                <td colspan="2" class="border-0 pl-0 no-padding no-margin" width="100%">
                    <p class="text-uppercase text-center no-padding no-margin font-12 text-bold">
                        datos del equipo
                    </p>
                </td>
            </tr>
            <tr>
                <td width="50%" class="no-padding no-margin">
                    <p class="no-margin">
                        <span class="text-bold">Equipo:</span>
                        {{ $invoice->seller->equipo }}
                    </p>
                    <p class="no-margin">
                        <span class="text-bold">Accesorios:</span>
                        {{ $invoice->seller->accesorio }}
                    </p>
                    <p class="no-margin">
                        <span class="text-bold">Falla Aparente:</span>
                        {{ $invoice->seller->falla }}
                    </p>

                </td>
                <td width="50%" class="no-padding no-margin">
                    <p class="no-margin">
                        <span class="text-bold">Marca:</span>
                        {{ $invoice->seller->marca }}
                    </p>
                    <p class="no-margin">
                        <span class="text-bold">Nota: </span>
                        {{ $invoice->seller->notas }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <div>
        <p class="text-uppercase text-center no-padding no-margin font-12 text-bold">
            Reporte técnico
        </p>
    </div>
    <table class="table-custom mb-5 uppercase" width="100%">
        <thead>
            <tr class="bg-dark">
                <th class="no-margin text-center">
                    <span class="">Servicio</span>
                </th>
                <th class="no-margin no-padding text-center" style="width: 120px">
                    Cantidad</th>
                <th class="no-margin no-padding text-center" style="width: 120px">Monto
                    <strong>({{ $invoice->buyer->payment_mode ? 'bs' : '$' }})</strong>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr class="no-padding no-margin"">
                    <td class="no-margin no-padding border-bottom-import">
                        <span class="px-1 uppercase">{{ $item->title }}</span>
                    </td>
                    <td class="no-margin no-padding text-center border-bottom-import"">
                        {{ $item->quantity }}

                    </td>
                    <td class="no-margin no-padding text-center border-bottom-import">
                        {{ $item->sub_total_price }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- Table --}}
    {{-- <table class="table table-items">
    <thead>
      <tr>
        <th scope="col" class="border-0 pl-0">{{ __('invoices::invoice.description') }}</th>
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
        <th scope="col" class="text-right border-0 pr-0">{{ __('invoices::invoice.sub_total') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($invoice->items as $item)
        <tr>
          <td class="pl-0">
            {{ $item->title }}

            @if ($item->description)
              <p class="cool-gray">{{ $item->description }}</p>
            @endif
          </td>
          @if ($invoice->hasItemUnits)
            <td class="text-center">{{ $item->units }}</td>
          @endif
          <td class="text-center">{{ $item->quantity }}</td>
          <td class="text-right">
            {{ $invoice->formatCurrency($item->price_per_unit) }}
          </td>
          @if ($invoice->hasItemDiscount)
            <td class="text-right">
              {{ $invoice->formatCurrency($item->discount) }}
            </td>
          @endif
          @if ($invoice->hasItemTax)
            <td class="text-right">
              {{ $invoice->formatCurrency($item->tax) }}
            </td>
          @endif

          <td class="text-right pr-0">
            {{ $invoice->formatCurrency($item->sub_total_price) }}
          </td>
        </tr>
      @endforeach
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
        <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
        <td class="text-right pl-0">{{ __('invoices::invoice.total_amount') }}</td>
        <td class="text-right pr-0 total-amount">
          {{ $invoice->formatCurrency($invoice->total_amount) }}
        </td>
      </tr>
    </tbody>
  </table> --}}

    <table class="table mb-0 full-width mt-2">
        <tbody>
            <tr>
                <td width="50%" class="no-padding no-margin">
                    <p class="no-margin  font-12 text-bold">
                        Recibo conforme _________________
                    </p>
                </td>
                <td width="50%" class="no-padding no-margin text-right">
                    <p class="no-margin">
                        <span class="text-uppercase text-bold"> subtotal:
                            {{ $invoice->formatCurrency($invoice->taxable_amount) }}
                        </span>
                    </p>
                    <p class="no-margin">
                        <span class="text-uppercase text-bold"> abono:
                            {{ $invoice->seller->abonos }} $
                        </span>
                    </p>
                    <p class="no-margin">
                        <span class="text-uppercase text-bold"> total:
                            {{ $invoice->formatCurrency($invoice->taxable_amount - $invoice->seller->abonos) }}
                        </span>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="full-wdith no-margin no-padding">
                    <p class="no-margin font-6 text-justify">
                        CONDICIONES DEL CONTRATO:(1).- El signatario cuyo nombre, y apellido y demás particularidades
                        consignatarias
                        del equipo presente. Se presume su propietario. (2).- COMPUSERVICIOS .C.A., No se hace
                        responsable por la
                        legalidad y procedencia del software que vienen instalados en los equipos. El signatario se hace
                        responsable
                        por las confiscaciones y multas a las que hubiera lugar, en caso de ocurrir una fiscalización
                        por parte de
                        las autoridades competentes. (3).- Solo podra ser retirada(s) la(s) pieza(s) y/o parte(s)
                        consignado(s) al
                        signatario y portador del presente documento el cual asume toda la responsabilidad por el
                        mismo.(4).- Si
                        transcurrido los cuarenta y cinco días(45) contínuos, no ha sido retirada(s) la(s) o parte(s)
                        consignada(s)
                        a COMPUSERVICIOS C.A., En el servicio ténico, queda entendido que el(los) equipo(s) antes
                        descrito(s), se
                        entenderá que el signatario, presunto propietario ha desistido en su rescate, consecuencia de
                        tal actitud,
                        la empresa asume y subroga en la procesión y propiedad del equipo consignado, quedando
                        autorizado
                        COMPUSERVICIOS C.A. Proceder a disponer del uso del equipo o pieza en consignación, para cubrir
                        los gastos
                        ocasionados, sea cuales fueran, y cual fuera su magnitud, tales como: servicio, tiempo de
                        custodia, cuido,
                        intereses de mora, locro cesante y daño emergente si lo hubiere. El signatario antes indicado
                        declaro que:
                        apto en todas y cada una de tus partes del presente contrato. Rechacen dos ejemplares al mismo
                        efecto y se
                        elige como domicilio especial, la ciudad de Guanare para todos los efectos del presente
                        contrato.
                    </p>

                </td>
            </tr>
        </tbody>
    </table>

    @if ($invoice->notes)
        <p>
            {{ trans('invoices::invoice.notes') }}: {!! $invoice->notes !!}
        </p>
    @endif

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
