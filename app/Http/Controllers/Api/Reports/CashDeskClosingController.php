<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Reports\ReportCollection;
use App\Models\Service;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Invoice;
use Response;

class CashDeskClosingController extends Controller
{
    public function index(PaginateRequest $request)
    {
        try {
            //code...
            // return response()->json([Carbon::parse($request->date)->format('Y-m-d')], 400);

            $reports = Service::where(function ($q) use ($request) {
                if ($request->date && isset($request->date['from'])) {
                    $q->whereBetween('date_delivery', [Carbon::parse($request->date['from']), Carbon::parse($request->date['to'])]);
                } else {
                    $q->whereDay('date_delivery', Carbon::parse($request->date)->format('d'))
                        ->whereMonth('date_delivery', Carbon::parse($request->date)->format('m'))
                        ->whereYear('date_delivery', Carbon::parse($request->date)->format('Y'));
                }
            })
                ->whereNotNull('date_delivery')
                // ->whereHas('invoice', function($q){
                //     $q->select(DB::raw("SUM(total) as invoice_total"));
                // })
                ->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')
                ->with(['customer', 'invoice'])
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
            $reports = Service::where(function ($q) use ($request, $date) {
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
                ->with(['customer', 'invoice'])
                ->get();

            $total_bs  = 0;
            $total_usd = 0;
            foreach ($reports as $item) {
                $items[] = (new InvoiceItem())->title($item->equipo)->pricePerUnit((float) $item->invoice?->total);

                if ($item->invoice?->payment_mode == 0)
                    $total_usd += $item->invoice?->total;
                else
                    $total_bs += $item->invoice?->total * $item->invoice->bs_bcv;
            }

            $buyer = new Buyer([
                'reports'   => $reports,
                'date'      => $date ?? $request->date,
                'total_bs'  => $total_bs,
                'total_usd' => $total_usd,
            ]);

            $invoice = Invoice::make('Reportes')
                ->buyer($buyer)
                ->addItems($items ?? [(new InvoiceItem())->title('-----')->pricePerUnit((float) 0)->quantity(0)])
                // ->seller($seller)
                // ->status($service->status? 'pagado' : 'pendiente')
                // ->taxRate(0)
                // ->sequence($service->id)
                // ->addItems($items)
                // ->logo($logo)
                ->series('')
                ->delimiter('')
                ->template('reports')
                ->save('invoices')
            ;

            $link = $invoice->url();

            return Response::json(['link' => $link, 'message' => 'success', 'data' => $request->date], 200);
        } catch (\Exception $e) {
            return response()->json([$request->date, $e->getMessage()], 400);
        }
    }

}
