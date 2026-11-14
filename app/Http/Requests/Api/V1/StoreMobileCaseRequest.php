<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreMobileCaseRequest extends FormRequest
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
            'nadu_ankaya' => ['required', 'string', 'max:255'],
            'dun_dinaya' => ['nullable', 'date_format:Y-m-d'],
            'kalaya' => ['nullable', 'string', 'max:255'],
            ...$this->personRules('nayakaru1_'),
            ...$this->personRules('nayakaru2_'),
            ...$this->personRules('aepakaru1_'),
            ...$this->personRules('aepakaru2_'),
            'dun_naya_mudala' => ['nullable', 'numeric', 'decimal:0,2', 'between:-9999999999999.99,9999999999999.99'],
            'arawul_mudala' => ['nullable', 'numeric', 'decimal:0,2', 'between:-9999999999999.99,9999999999999.99'],
            'poli_prathishathaya' => ['nullable', 'numeric', 'decimal:0,2', 'between:-999.99,999.99'],
            'awasan_mudal_bendima' => ['nullable', 'date_format:Y-m-d'],
            'dina_ganuna' => ['nullable', 'integer', 'between:-2147483648,2147483647'],
            'apal_pa' => ['nullable', 'numeric', 'decimal:0,2', 'between:-9999999999999.99,9999999999999.99'],
            'apal_vi' => ['nullable', 'numeric', 'decimal:0,2', 'between:-9999999999999.99,9999999999999.99'],
            'mul_mudala' => ['nullable', 'numeric', 'decimal:0,2', 'between:-9999999999999.99,9999999999999.99'],
            'poliya' => ['nullable', 'numeric', 'decimal:0,2', 'between:-9999999999999.99,9999999999999.99'],
            'nadu_gasthu' => ['nullable', 'numeric', 'decimal:0,2', 'between:-9999999999999.99,9999999999999.99'],
            'total' => ['nullable', 'numeric', 'decimal:0,2', 'between:-9999999999999.99,9999999999999.99'],
        ];
    }

    /** @return array<string, list<string>> */
    private function personRules(string $prefix): array
    {
        return [
            $prefix.'nama' => ['nullable', 'string', 'max:255'],
            $prefix.'samajika_ankaya' => ['nullable', 'string', 'max:255'],
            $prefix.'lipinaya1' => ['nullable', 'string', 'max:255'],
            $prefix.'lipinaya2' => ['nullable', 'string', 'max:255'],
            $prefix.'lipinaya3' => ['nullable', 'string', 'max:255'],
        ];
    }
}
