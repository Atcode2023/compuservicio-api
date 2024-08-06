<?php

namespace App\Http\Resources\Toner;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TonerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data'    => $this->collection,
            'message' => 'Operation Success Toners',
            'success' => true,
        ];
    }
}