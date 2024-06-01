<?php

namespace App\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FollowStreamerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'userId' => 'required|string|max:100',
            'streamerId' => 'required|string|max:100',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
