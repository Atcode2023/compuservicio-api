<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\Users\Customer\storeRequest;
use App\Http\Requests\Users\Customer\updateRequest;
use App\Http\Resources\Users\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(PaginateRequest $request) : CustomerResource
    {
        return new CustomerResource(Customer::where(function ($q) use ($request) {
            $q->where('name', 'like', "{$request->search}%")->orWhere('ci', 'like', "{$request->search}%");
        })->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')->paginate($request->rowsPerPage));
    }

    public function store(storeRequest $request) : CustomerResource
    {
        $customer = Customer::create($request->validated());

        return new CustomerResource($customer->toArray());
    }

    public function show(Customer $customer) : CustomerResource
    {
        return new CustomerResource($customer->toArray());
    }

    public function update(updateRequest $request, Customer $customer) : CustomerResource
    {
        $customer->update($request->validated());

        return new CustomerResource($customer->toArray());
    }

    public function destroy(Customer $customer) : CustomerResource
    {
        $customer->delete();

        return new CustomerResource([]);
    }

    public function check(Request $request) : CustomerResource
    {
        $request->validate([
            'cedula' => 'required|min:6',
        ]);
        $customer = Customer::where('ci', $request->cedula)->first();

        return new CustomerResource($customer ? $customer->toArray() : []);
    }

    public function autocomplete(Request $request)
    {
        $customer = Customer::where('name', 'like', '%' . $request->search . '%')->take(10)->get();

        return new CustomerResource($customer ? $customer->toArray() : []);
    }
}
