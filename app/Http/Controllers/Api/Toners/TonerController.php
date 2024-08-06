<?php

namespace App\Http\Controllers\Api\Toners;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\StoreTonerRequest;
use App\Http\Requests\UpdateTonerRequest;
use App\Http\Resources\Toner\TonerCollection;
use App\Models\PieceToner;
use App\Models\ServiceToner;
use App\Models\Toner;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;
use Mike42\Escpos\ImagickEscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Response;

class TonerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\Toner\TonerCollection
     */
    public function index(PaginateRequest $request)
    {
        $toners = Toner::where(function ($q) use ($request) {
            $q->where('id', 'like', $request->search . '%')->orWhereHas('customer', function ($q2) use ($request) {
                $q2->where('name', 'like', $request->search . '%');
                $q2->orWhere('ci', 'like', $request->search . '%');
            });
        })->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')->with(['customer', 'pieces'])->paginate($request->rowsPerPage);
        return new TonerCollection($toners);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \App\Http\Resources\Toner\TonerCollection
     */
    public function store(StoreTonerRequest $request)
    {
        $toner = new Toner;
        $toner->fill($request->validated());
        $toner->admin_id = Auth::id();
        $toner->save();

        $toner->toners()->createMany($request->toners);
        $toner->pieces()->createMany($request->pieces);

        $data = Toner::find($toner->id);

        return new TonerCollection($data->toArray());
    }

    /**
     * Display the specified resource.
     *
     * @return \App\Http\Resources\Toner\TonerCollection
     */
    public function show(Toner $toner)
    {
        return new TonerCollection($toner->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \App\Http\Resources\Toner\TonerCollection
     */
    public function update(UpdateTonerRequest $request, Toner $toner)
    {
        $toner->update($request->validated());
        $PiecesIds = [];
        foreach ($request->pieces as $piece) {
            $pieceid = $toner->pieces()->updateOrCreate(
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
        $toner->pieces()->whereNotIn('id', $PiecesIds)->delete();
        $TonersIds = [];
        foreach ($request->toners as $piece) {
            $pieceid = $toner->toners()->updateOrCreate(
                ['id' => $piece['id'] ?? null],
                [
                    'piece'    => $piece['piece'],
                    'quantity' => $piece['quantity'],
                    // 'serial'   => $piece['serial'],
                    'price'    => $piece['price'],
                ]
            );
            $TonersIds[] = $pieceid->id;
        }
        $toner->toners()->whereNotIn('id', $TonersIds)->delete();

        return new TonerCollection($toner->toArray());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \App\Http\Resources\Toner\TonerCollection
     */
    public function destroy(Toner $toner)
    {
        $toner->delete();

        return new TonerCollection([]);
    }

    public function piecesAutocomplete(Request $request)
    {
        $pieces = PieceToner::where('piece', 'like', $request->search . '%')
            ->distinct()->take(15)->pluck('piece');
        return new TonerCollection($pieces);
    }

    public function servicesAutocomplete(Request $request)
    {
        $pieces = ServiceToner::where('piece', 'like', $request->search . '%')
            ->distinct()->take(15)->pluck('piece');
        return new TonerCollection($pieces);
    }

    public function pdf($id)
    {

        try {
            $service = Toner::find($id);

            $buyer = new Buyer([
                'name'  => $service->admin?->name ?? null,
                'phone' => $service->admin?->phone ?? null,
                'id'    => $service->id,
            ]);

            $seller = new Party([
                'name'          => $service->customer->name,
                'phone'         => $service->customer->phone,
                'ci'            => $service->customer->ci,
                'address'       => $service->customer->address,
                'model'         => $service->model,
                'date_delivery' => $service->date_delivery,
                'created_at'    => $service->created_at,
                'abonos'        => $service->abonos,
            ]);

            $items = [];
            // = (new InvoiceItem())->title('Precio Estimado')->pricePerUnit((float) $service->price);

            foreach ($service->toners as $item) {
                $price   = $item->price;
                $items[] = (new InvoiceItem())->title($item->piece)->pricePerUnit((float) $price)->quantity($item->quantity);
            }

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

    public function delivery(Toner $toner, Request $request)
    {
        $toner->date_delivery = Carbon::now();
        $toner->delivery      = !$request->delivery;
        $toner->save();

        return new TonerCollection($toner->toArray());
    }

    public function printReceipt()
    {
        $pdf       = 'storage/invoices/factura_AA_11145.pdf';
        $connector = new FilePrintConnector("php://stdout");
        $printer   = new Printer($connector);
        try {
            $pages = ImagickEscposImage::loadPdf($pdf);
            foreach ($pages as $page) {
                $printer->graphics($page);
            }
            $printer->cut();
        } catch (Exception $e) {
            /*
             * loadPdf() throws exceptions if files or not found, or you don't have the
             * imagick extension to read PDF's
             */
            echo $e->getMessage() . "\n";
        } finally {
            $printer->close();
        }
    }
}
