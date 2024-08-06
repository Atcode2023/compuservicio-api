<?php

namespace App\Http\Controllers\Api\Techs;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Services\ServiceCollection;
use App\Models\Invoice;
use App\Models\PieceService;
use App\Models\ReportService;
use App\Models\Service;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Response;

class RepairingController extends Controller
{
    public function index(PaginateRequest $request)
    {
        $services = Service::when(auth()->user()->role_id == 3, function ($q) {
            $q->where('tech_id', auth()->id())->with('tech');
        }, function ($q) {
            $q->whereNotIn('tech_id', [0]);
        })->whereNull('date_repair')->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')->with(['customer', 'tech'])->paginate($request->rowsPerPage);
        return new ServiceCollection($services);
    }

    public function asign(Request $request)
    {
        $service          = Service::find($request->service_id);
        $service->tech_id = auth()->id();
        $service->save();

        return response()->json(['message' => 'success', 'status' => 200], 200);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $service = Service::find($request->id);

            $ReportsIds = [];
            foreach ($request->reports as $report) {
                $reportid = $service->reports()->updateOrCreate(
                    ['id' => $report['id'] ?? null],
                    [
                        'report' => $report['report'],
                        'price'  => $report['price'],
                    ]
                );
                $ReportsIds[] = $reportid->id;
            }
            $service->reports()->whereNotIn('id', $ReportsIds)->delete();

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

            $service->date_repair = Carbon::now();
            $service->save();

            $service->invoice()->updateOrCreate(
                ['tech_id' => $service->tech_id],
                ['total' => $request->total]
            );

            DB::commit();
            return response()->json(['message' => 'success invoice', 'status' => 200], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'failed report invoice', 'status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function reportsAutocomplete(Request $request)
    {
        $reports = ReportService::where('report', 'like', $request->search . '%')
            ->whereDate('created_at', '>=', '2023-04-28 00:00:00')
            ->distinct()->take(15)->pluck('report');
        return new ServiceCollection($reports);
    }

    public function piecesAutocomplete(Request $request)
    {
        $pieces = PieceService::where('piece', 'like', $request->search . '%')
            ->whereDate('created_at', '>=', '2023-04-28 00:00:00')
            ->distinct()->take(15)->pluck('piece');
        return new ServiceCollection($pieces);
    }

    public function count()
    {
        $services = Service::when(auth()->user()->role_id == 3, function ($q) {
            $q->where('tech_id', auth()->id());
        }, function ($q) {
            $q->whereNotIn('tech_id', [0]);
        })->whereNull('date_repair')->count();
        return Response::json(['data' => $services, 'status' => 200, 'message' => 'success'], 200);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $service = Service::find($request->id);

            $ReportsIds = [];
            foreach ($request->reports as $report) {
                $reportid = $service->reports()->updateOrCreate(
                    ['id' => $report['id'] ?? null],
                    [
                        'report' => $report['report'],
                        'price'  => $report['price'],
                    ]
                );
                $ReportsIds[] = $reportid->id;
            }
            $service->reports()->whereNotIn('id', $ReportsIds)->delete();

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

            // $service->date_repair = Carbon::now();
            $service->save();

            $service->invoice()->update(
                ['tech_id' => $service->tech_id],
                ['total' => $request->total]
            );

            DB::commit();
            return response()->json(['message' => 'success invoice', 'status' => 200], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'failed report invoice', 'status' => 400, 'error' => $e->getMessage()], 400);
        }
    }

    public function reject(Request $request)
    {
        $service          = Service::where('id', $request->service_id)->first();
        $service->tech_id = null;
        $service->save();
        return new ServiceCollection($service->toArray());
    }
}
