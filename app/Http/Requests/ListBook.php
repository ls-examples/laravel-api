<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListBook extends FormRequest
{

    public function attributes()
    {
        return [
            'search' => 'строка поиска',
            'order_direction' => 'направаление сортировки',
            'order_by' => 'по какому полю сортировать',
        ];
    }

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
            'search' => 'sometimes|nullable|string',
            'order_direction' => 'required_with:order_by|in:desc,asc',
            'order_by' => 'required_with:order_direction|in:title,author'
        ];
    }
}
