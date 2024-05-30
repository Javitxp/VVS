<?php

namespace App\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:100',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
