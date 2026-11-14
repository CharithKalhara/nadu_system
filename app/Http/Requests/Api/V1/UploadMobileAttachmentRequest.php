<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UploadMobileAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'typing';
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'min:1', 'exists:companies,id'],
            'case_id' => ['required', 'integer', 'min:1'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }
}
