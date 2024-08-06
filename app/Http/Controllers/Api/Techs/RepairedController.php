<?php

namespace App\Http\Controllers\Api\Techs;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Services\ServiceCollection;
use App\Models\Invoice;
use App\Models\Service;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepairedController extends Controller
{
    public function index(PaginateRequest $request)
    {
        $services = Service::where(function ($q) use ($request) {
            if ($request->search) {
                $q->where('id', 'like', $request->search . '%');
                $q->orWhereHas('customer', function ($q2) use ($request) {
                    $q2->where('name', 'like', $request->search . '%');
                    $q2->orWhere('ci', 'like', $request->search . '%');
                });
            }
            if (Auth::user()->role_id == 3)
                $q->where('tech_id', Auth::id());
        })->whereNotNull('date_repair')->where('delivery', 0)->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')->with(['customer', 'invoice','tech'])->paginate($request->rowsPerPage);
        return new ServiceCollection($services);
    }

    public function delivery(Request $request)
    {
        try {
            DB::beginTransaction();
            $service                = Service::find($request->id);
            $service->date_delivery = Carbon::now();
            $service->delivery      = 1;
            $service->save();

            $invoice               = Invoice::firstOrNew([
                'service_id' => $service->id,
                'tech_id'    => $service->tech_id,
            ]);
            $invoice->payment_mode = $request->payment_mode;
            $invoice->bs_bcv       = number_format((float)$request->dolar,2);
            // $invoice->total = $service->reports()->sum('price') + $service->pieces()->sum('price');
            $invoice->save();
            DB::commit();
            return new ServiceCollection($service->toArray());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'failed report invoice', 'status' => 400, 'error' => $e->getMessage(), 'data' =>$service->reports()->sum('price') + $service->pieces()->sum('price')], 400);
        }

    }

    public function reject(Request $request)
    {
        $service              = Service::where('id', $request->service_id)->first();
        $service->date_repair = null;
        $service->save();
        return new ServiceCollection($service->toArray());
    }
}
