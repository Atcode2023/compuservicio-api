<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTonerRequest extends FormRequest
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
            'customer_id'   => 'required',
            'admin_id'      => 'nullable',
            // 'model'         => 'required',
            'price'         => 'required',
            'date_delivery' => 'nullable',
            'delivery'      => 'nullable',
            'pieces'        => 'nullable|array',
            'abonos'        => 'nullable',
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'abonos' => request()->input('abonos') ?? '0',
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
