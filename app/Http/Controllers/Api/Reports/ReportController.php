<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Reports\ReportCollection;
use App\Models\Service;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;

class ReportController extends Controller
{
    // protected $BASE_URL = "http://localhost:9000/";
    public function index(PaginateRequest $request)
    {

        $date    = json_decode($request->date);
        $reports = Service::where(function ($q) use ($request, $date) {
            if ($date && isset($date->from)) {
                $q->whereBetween('created_at', [Carbon::parse($date->from), Carbon::parse($date->to)]);
            }
            if ($request->search) {
                $q->where('id', 'like', $request->search . '%');
                $q->orWhereHas('customer', function ($q2) use ($request) {
                    $q2->where('name', 'like', $request->search . '%');
                    $q2->orWhere('ci', 'like', $request->search . '%');
                });
            }
            if (Auth::user()->role_id == 3) {
                $q->where('tech_id', Auth::id());
            }

        })
        // ->whereNotNull('date_delivery')
        // ->whereHas('invoice')
            ->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')
            ->with(['customer', 'invoice'])
            ->paginate($request->rowsPerPage);
        return new ReportCollection($reports);
    }

    public function show($id, Request $request)
    {

        try {
            $service      = Service::find($id);
            $payment_mode = $service->invoice->payment_mode ?? null;
            $dolar        = $service->invoice->bs_bcv ?? 1;

            $buyer = new Buyer([
                'name'         => $service->admin->name ?? null,
                'phone'        => $service->admin->phone ?? null,
                'id'           => $service->id,
                'payment_mode' => $payment_mode,
                // 'custom_fields' => [
                // ],
            ]);

            $seller = new Party([
                'name'          => $service->customer->name,
                'phone'         => $service->customer->phone,
                'ci'            => $service->customer->ci,
                'address'       => $service->customer->address,
                'equipo'        => $service->equipo,
                'marca'         => $service->marca,
                'accesorio'     => $service->accesorio,
                'falla'         => $service->falla,
                'notas'         => $service->notas,
                'date_delivery' => $service->date_delivery,
                'created_at'    => $service->created_at,
                'abonos'        => $service->abonos,
                // 'custom_fields' => [
                // 'note'        => 'IDDQD',
                // 'business id' => '365#GG',
                // ],
            ]);
            $items = [];
            foreach ($service->pieces as $item) {
                $price   = $item->price * ($payment_mode ? $dolar : 1);
                $items[] = (new InvoiceItem())->title($item->piece)->pricePerUnit((float) $price)->quantity($item->quantity);
            }
            foreach ($service->reports as $item) {
                $price   = $item->price * ($payment_mode ? $dolar : 1);
                $items[] = (new InvoiceItem())->title($item->report)->pricePerUnit((float) $price);
            }

            $invoice = Invoice::make('Factura')
                ->buyer($buyer)
                ->seller($seller)
                ->status($service->delivery ? 'pagado' : 'pendiente')
                ->taxRate(0)
                ->sequence($service->id)
                ->series('')
                ->delimiter('')
                ->addItems(count($items) > 0 ? $items : [(new InvoiceItem())->title('precios estimado')->pricePerUnit((float) $service->monto_estimado)])
                ->currencySymbol($payment_mode ? 'bs' : '$')
            // ->logo($logo)
                ->save('invoices')
            ;

            $link = $invoice->url();

            if ($request->type == 'html5') {
                return Response::json(['link' => $link, 'message' => 'success', 'service' => $service], 200);
            } else {
                return $invoice->stream();
            }

        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage(), 'message' => 'failed'], 400);
        }
    }

    public function factura($id)
    {
        try {
            //code...
            $service = Service::find($id);
            // $printer_name = ("xprinter");
            $printer_name = ("smb://Guests@alfonso/xprinter");
            $connector    = new WindowsPrintConnector($printer_name);
            $printer      = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            // $printer->text("SENIAT\n");
            // $img = new GdEscposImage(public_path("img/logo.png"), false);
            // $printer->bitImage($img);
            // dd($img, $printer);

            $printer->text("COMPUSERVICIOS C.A.\n");
            // $printer->text("RIF: J-5622625626\n");
            $printer->text("Avenida Unda entre calle 5ta y 6ta\n");
            $printer->text("whatsapp: 0412-5558030\n");
            $printer->text("@compuserviciosguanare\n");
            $printer->text("Guanare-Portuguesa\n");
            $printer->text("--------------------------------------------\n");
            $printer->setEmphasis(false);
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Servicio Nro:  " . $service->id . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Fecha:  " . Carbon::parse($service->created_at)->format('d/m/Y') . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Hora:  " . Carbon::parse($service->created_at)->format('h:i:s') . "\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(false);
            $printer->text("===========================================\n");
            //clientes

            ///$service->user->name
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("NOMBRE:  " . strtoupper($service->customer?->name) . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("CI/RIF:  " . $service->customer->ci . "\n");
            // descripcion del equipo
            $printer->text("===========================================\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("EQUIPO:  " . $service->equipo . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("MARCA:  " . $service->marca . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("ACCESORIO:  " . $service->accesorio . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("FALLA:  " . $service->falla . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("NOTAS:  " . $service->notas . "\n");
            // $printer->setJustification(Printer::JUSTIFY_LEFT);
            // $printer->text("Direccion:  " . strtoupper($service->customer?->address) . "\n");
            // $printer->text("VENDEDOR:  " . $service->admin->name . "\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(false);
            $printer->text("===========================================\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("CANT:          DESCRIPCION           SUB TOTAL \n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("===========================================\n");

            $printer->setEmphasis(false);

            $subtotal = 0;

            foreach ($service->reports as $order) {

                $printer->text(new item(
                    $order->price,
                    strtoupper($order->report),
                    '$' . number_format($order->price, 2)
                ));
                $subtotal += (float) $order->price;

            }
            foreach ($service->pieces as $order) {

                $printer->text(new item(
                    $order->quantity . "x" . $order->price,
                    strtoupper($order->piece),
                    '$' . number_format(($order->quantity * $order->price) * 1, 2)
                ));
                $subtotal += (float) ($order->quantity * $order->price);

            }
            // * $service->bcv;
            $printer->setEmphasis(true);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("\n===========================================\n");
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->setEmphasis(true);
            $printer->text("Total a Pagar: $" . number_format($subtotal, 2) . "\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(1, 1);
            $printer->text(strtoupper("Gracias por preferirnos!") . "\n");
            $printer->text(strtoupper("Vuelvan Pronto!") . "\n");
            $printer->feed(2);
            $printer->cut();
            $printer->close();
            return Response::json(['success' => true, 'message' => 'factura imprimidos', 'printer' => $printer], 200);
        } catch (Exception $e) {
            return Response::json(['success' => false, 'errors' => $e->getMessage()], 400);
        }

    }
}

/* A wrapper to do organise item names & prices into columns */
class item
{
    private $quantity;
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($quantity = '', $name = '', $price = '', $dollarSign = false)
    {
        $this->quantity   = $quantity;
        $this->name       = $name;
        $this->price      = $price;
        $this->dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 10;
        $leftCols  = 18;
        if ($this->dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left   = str_pad($this->quantity, $leftCols);
        $center = str_pad($this->name, $leftCols);

        $sign  = ($this->dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$center$right\n";
    }
}
