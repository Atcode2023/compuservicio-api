<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\Users\Tech\StoreTechRequest;
use App\Http\Requests\Users\Tech\UpdateTechRequest;
use App\Http\Resources\Users\TechCollection;
use App\Models\User;
use Illuminate\Http\Request;

class TechController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\Users\TechCollection
     */
    public function index(PaginateRequest $request) : TechCollection
    {
        return new TechCollection(User::where(function ($q) use ($request) {
            $q->where('name', 'like', "{$request->search}%")->orWhere('ci', 'like', "{$request->search}%");
        })->where('role_id', 3)->orderBy($request->sortBy ?? 'id', $request->sortBy && $request->descending == 'false' ? 'asc' : 'desc')->paginate($request->rowsPerPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Users\Tech\StoreTechRequest  $request
     * @return \App\Http\Resources\Users\TechCollection
     */
    public function store(StoreTechRequest $request) : TechCollection
    {
        $tech = new User;
        $tech->fill($request->validated());
        $tech->password = bcrypt($request->password);
        $tech->role_id  = 3;
        $tech->save();
        return new TechCollection($tech->toArray());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \App\Http\Resources\Users\TechCollection
     */
    public function show(User $user) : TechCollection
    {
        return new TechCollection($user->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Users\Tech\UpdateTechRequest  $request
     * @param  \App\Models\User  $user
     * @return \App\Http\Resources\Users\TechCollection
     */
    public function update(UpdateTechRequest $request, User $user) : TechCollection
    {
        $user->update($request->validated());
        if ($request->password)
            $user->update(['password' => bcrypt($request->password)]);
        return new TechCollection($user->toArray());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \App\Http\Resources\Users\TechCollection
     */
    public function destroy(User $user) : TechCollection
    {
        $user->delete();
        return new TechCollection([]);
    }

    /**
     * Update status of tech user
     *
     * @param \App\Models\User $user
     * @return \App\Http\Resources\Users\TechCollection
     */
    public function status(User $user, Request $request) : TechCollection
    {
        $user->status = $request->status;
        $user->save();
        return new TechCollection($user->toArray());
    }
}