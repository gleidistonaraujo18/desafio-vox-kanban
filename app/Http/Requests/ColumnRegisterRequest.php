<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ColumnRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "O campo nome é obrigatório",
            'name.max' => "O campo nome deve ter no máximo 255 caracteres."
        ];
    }
}
