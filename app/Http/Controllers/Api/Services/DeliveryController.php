<?php

namespace App\Http\Controllers\Api\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Services\ServiceCollection;
use App\Models\Service;
use Auth;
use Illuminate\Http\Request;

class DeliveryController extends Controller
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
                $q->where('tech_id', Auth::id())->with('tech');
        })->whereNotNull('date_delivery')->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')->with(['customer', 'invoice','tech'])->paginate($request->rowsPerPage);
        return new ServiceCollection($services);
    }

    public function reject(Request $request)
    {
        $service                = Service::where('id', $request->service_id)->first();
        $service->date_delivery = null;
        $service->delivery      = 0;
        $service->save();
        return new ServiceCollection($service->toArray());
    }
}
