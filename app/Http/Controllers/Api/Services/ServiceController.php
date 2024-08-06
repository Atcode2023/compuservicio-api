<?php

namespace App\Http\Controllers\Api\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\Services\ServiceCollection;
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

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\Services\ServiceCollection
     */
    public function index(PaginateRequest $request)
    {
        $services = Service::where(function ($q) use ($request) {
            $q->where('id', 'like', $request->search . '%')->orWhereHas('customer', function ($q2) use ($request) {
                $q2->where('name', 'like', $request->search . '%');
                $q2->orWhere('ci', 'like', $request->search . '%');
            });
        })->where('tech_id', null)->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')->with(['customer', 'pieces', 'reports'])->paginate($request->rowsPerPage);
        return new ServiceCollection($services);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \App\Http\Resources\Services\ServiceCollection
     */
    public function store(StoreServiceRequest $request)
    {
        $service = new Service;
        $service->fill($request->validated());
        $service->admin_id = Auth::id();
        $service->save();

        $PiecesIds = [];
        foreach ($request->pieces as $piece) {
            $pieceid = $service->pieces()->updateOrCreate(
                ['id' => $piece['id'] ?? null],
                [
                    'piece'    => $piece['piece'],
                    'quantity' => $piece['quantity'],
                    'serial'   => $piece['serial'],
                    'price'    => $piece['price'],
                ]
            );
            $PiecesIds[] = $pieceid->id;
        }
        $service->pieces()->whereNotIn('id', $PiecesIds)->delete();

        $data = Service::find($service->id);

        return new ServiceCollection($data->toArray());
    }

    /**
     * Display the specified resource.
     *
     * @return \App\Http\Resources\Services\ServiceCollection
     */
    public function show(Service $service)
    {
        return new ServiceCollection($service->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \App\Http\Resources\Services\ServiceCollection
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());
        $PiecesIds = [];
        foreach ($request->pieces as $piece) {
            $pieceid = $service->pieces()->updateOrCreate(
                ['id' => $piece['id'] ?? null],
                [
                    'piece'    => $piece['piece'],
                    'quantity' => $piece['quantity'],
                    'serial'   => $piece['serial'],
                    'price'    => $piece['price'],
                ]
            );
            $PiecesIds[] = $pieceid->id;
        }
        $service->pieces()->whereNotIn('id', $PiecesIds)->delete();

        return new ServiceCollection($service->toArray());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \App\Http\Resources\Services\ServiceCollection
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return new ServiceCollection([]);
    }

    public function pdf($id)
    {

        try {
            $service = Service::find($id);

            $buyer = new Buyer([
                'name'  => $service->tech?->name ?? null,
                'phone' => $service->tech?->phone ?? null,
                'id'    => $service->id,
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
            ]);

            $items[] = (new InvoiceItem())->title('Precio Estimado')->pricePerUnit((float) $service->monto_estimado);

            foreach ($service->pieces as $item) {
                $price   = $item->price;
                $items[] = (new InvoiceItem())->title($item->piece)->pricePerUnit((float) $price)->quantity($item->quantity);
            }

            $invoice = Invoice::make('Factura')
                ->buyer($buyer)
                ->seller($seller)
                ->status($service->delivery ? 'pagado' : 'pendiente')
                ->taxRate(0)
                ->sequence($service->id)
                ->series('')
                ->delimiter('')
                ->addItems($items)
            // ->logo($logo)
            // ->save('invoices');
            ;

            $invoice->save('invoices');
            $link = $invoice->url();

            return Response::json(['link' => $link, 'message' => 'success'], 200);

        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage(), 'message' => 'failed'], 400);
        }
    }

    public function count()
    {
        $services = Service::where(function ($q) {
            $q->where('tech_id', null);
        })->count();
        return Response::json(['data' => $services, 'status' => 200, 'message' => 'success'], 200);
    }

    public function autocomplete(Request $request)
    {
        $services = Service::where(function ($q) use ($request) {
            $q->where($request->type, 'like', "{$request->equipo}%");
            $q->whereDate('created_at', '>=', '2023-04-28 00:00:00');
        })->distinct($request->type)->pluck($request->type);

        return new ServiceCollection($services);
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
            $printer->setJustification(Printer::JUSTIFY_CENTER);
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
            // $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(false);
            $printer->text("===========================================\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("CANT:          DESCRIPCION           SUB TOTAL \n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("===========================================\n");

            $printer->setEmphasis(false);
            $subtotal = 0;
            $printer->text(new item(
                $service->monto_estimado,
                strtoupper('monto estimado'),
                '$' . number_format($service->monto_estimado, 2)
            ));

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
            // $total = $service->price;
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
            // $printer->text("Compuservicio");
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
