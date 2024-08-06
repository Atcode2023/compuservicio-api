<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'customer_id'    => 'required',
            'admin_id'       => 'nullable',
            'tech_id'        => 'nullable',
            'equipo'         => 'required',
            'marca'          => 'nullable',
            'accesorios'     => 'nullable',
            'falla'          => 'required',
            'notas'          => 'nullable',
            'monto_estimado' => 'nullable',
            'date_repair'    => 'nullable',
            'date_delivery'  => 'nullable',
            'delivery'       => 'nullable',
            'pieces'         => 'nullable|array',
            'abonos'         => 'nullable',
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'monto_estimado' => request()->input('monto_estimado') ?? '0',
            'abonos'         => request()->input('abonos') ?? '0',
        ]);

    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data'    => $validator->errors(),
        ], 400));
    }
}
