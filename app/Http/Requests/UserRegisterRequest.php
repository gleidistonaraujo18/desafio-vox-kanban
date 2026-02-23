<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => "O campo nome é obrigatório",
            'name.max' => "O campo nome deve ter no máximo 255 caracteres.",
            'email.required' => "O campo e-mail é obrigatório",
            'email.email' => "O campo e-mail deve ser um endereço válido"

        ];
    }
}
