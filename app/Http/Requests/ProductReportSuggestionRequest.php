<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductReportSuggestionRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
//            'store_name' => 'required|string',
//            'store_email' => 'required|email',
            'description' => 'required|max:3000',
        ];
    }


    public function messages()
    {
        return [
//            'store_name.required' => 'A store name is required.',
        ];
    }

}
