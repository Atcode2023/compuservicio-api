<?php

namespace App\Http\Controllers\Api\Toners;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Reports\ReportCollection;
use App\Models\Toner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Invoice;
use Response;

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\GdEscposImage;

class ReportTonerController extends Controller
{

    public function index(PaginateRequest $request)
    {
        try {
            //code...
            // return response()->json([Carbon::parse($request->date)->format('Y-m-d')], 400);

            $reports = Toner::where(function ($q) use ($request) {
                if ($request->date && isset($request->date['from'])) {
                    $q->whereBetween('date_delivery', [Carbon::parse($request->date['from']), Carbon::parse($request->date['to'])]);
                } else {
                    $q->whereDay('date_delivery', Carbon::parse($request->date)->format('d'))
                        ->whereMonth('date_delivery', Carbon::parse($request->date)->format('m'))
                        ->whereYear('date_delivery', Carbon::parse($request->date)->format('Y'));
                }
            })
                ->whereNotNull('date_delivery')
                ->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')
                ->with(['customer'])
                ->paginate(1000);
            return new ReportCollection($reports);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }

    public function pdf(Request $request)
    {
        try {
            $date    = json_decode($request->date);
            $reports = Toner::where(function ($q) use ($request, $date) {
                if ($date && isset($date->from)) {
                    $q->whereBetween('date_delivery', [Carbon::parse($date->from), Carbon::parse($date->to)]);
                } else {
                    $q->whereDay('date_delivery', Carbon::parse($request->date)->format('d'))
                        ->whereMonth('date_delivery', Carbon::parse($request->date)->format('m'))
                        ->whereYear('date_delivery', Carbon::parse($request->date)->format('Y'));
                }
            })
                ->whereNotNull('date_delivery')
                ->orderBy('id', 'asc')
                ->with(['customer'])
                ->get();

            // $total_bs  = 0;
            $total_usd = 0;
            foreach ($reports as $item) {
                $items[]   = (new InvoiceItem())->title($item->customer->name)->pricePerUnit((float) $item->price);
                $total_usd += $item->price;
            }

            $buyer = new Buyer([
                'reports'   => $reports,
                'date'      => $date ?? $request->date,
                // 'total_bs'  => $total_bs,
                'total_usd' => $total_usd,
            ]);

            $invoice = Invoice::make('Reportes')
                ->buyer($buyer)
                ->addItems($items ?? [(new InvoiceItem())->title('-----')->pricePerUnit((float) 0)->quantity(0)])
                ->series('')
                ->delimiter('')
                ->template('toners')
                ->save('invoices')
            ;

            $link = $invoice->url();

            return Response::json(['link' => $link, 'message' => 'success', 'data' => $request->date], 200);
        } catch (\Exception $e) {
            return response()->json([$request->date, $e->getMessage()], 400);
        }
    }


    public function factura($id)
    {
        try {
            //code...
            $sale         = Toner::find($id);
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
            $printer->text("Servicio Nro:  " . $sale->id . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Fecha:  " . Carbon::parse($sale->created_at)->format('d/m/Y') . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Hora:  " . Carbon::parse($sale->created_at)->format('h:i:s') . "\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(false);
            $printer->text("===========================================\n");
            //clientes

            ///$sale->user->name
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Nombre:  " . strtoupper($sale->customer?->name) . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("CI/RIF:  " . $sale->customer->ci . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            // $printer->text("Direccion:  " . strtoupper($sale->customer?->address) . "\n");
            // $printer->text("VENDEDOR:  " . $sale->admin->name . "\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(false);
            $printer->text("===========================================\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("CANT:          DESCRIPCION           SUB TOTAL \n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("===========================================\n");

            $printer->setEmphasis(false);
            foreach ($sale->pieces as $order) {

                $printer->text(new item(
                    $order->quantity . "x" . $order->price,
                    strtoupper($order->piece),
                    '$' . number_format(($order->quantity * $order->price) * 1, 2)
                ));
                // $subtotal += $order->quantity * $order->price - $order->quantity * $order->price * $order->discount / 100;
                // $printer->setJustification(Printer::JUSTIFY_LEFT);
                // $printer->text($order->quantity . "x" . $order->price);
                // $printer->setEmphasis(false);
                // $printer->setJustification(Printer::JUSTIFY_CENTER);
                // $printer->text(" " . $order->piece);
                // $printer->setEmphasis(false);
                // $printer->setJustification(Printer::JUSTIFY_RIGHT);
                // $printer->text(' B.s' . number_format(($order->quantity * $order->price) * 1, 2) . "\n");

            }
            $total = $sale->price;
            // * $sale->bcv;
            $subtotal = $sale->price;
            // * $sale->bcv;
            $printer->setEmphasis(true);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("\n===========================================\n");
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->setEmphasis(true);
            // $printer->text("Sub Total: $" . number_format($subtotal, 2) . "\n");
            // $printer->setJustification(Printer::JUSTIFY_RIGHT);
            // $printer->text("IGTF: 00,00 \n");
            // $printer->text(($sale->tax == 0 ? "Exento" : "Impuesto") . ": $" . number_format($subtotal * $sale->tax / 100, 2) . "\n");
            $printer->text("Total a Pagar: $" . number_format($total, 2) . "\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(1, 1);
            $printer->text(strtoupper("Gracias por preferirnos!") . "\n");
            $printer->text(strtoupper("Vuelvan Pronto!") . "\n");
            // $printer->text("Compuservicio");
            $printer->feed(2);
            $printer->cut();
            $printer->close();
            return Response::json(['success' => true, 'message' => 'factura imprimidos', 'printer' => $printer], 200);
        } catch (\Exception $e) {
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
