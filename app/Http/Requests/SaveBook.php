<?php

namespace App\Http\Requests;

use App\Rules\Base64Image;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class SaveBook extends FormRequest
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
        $currentYear = Carbon::now()->format('Y');
        return [
            'image' => ['nullable', 'present', new Base64Image()],
            'title' => 'required|string|max:150',
            'author' => 'required|string|max:100',
            'year' => "nullable|present|integer|between:0,$currentYear",
            'description' => 'required|string|max:2000',
        ];
    }
}
