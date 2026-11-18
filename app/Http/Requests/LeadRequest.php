<?php

namespace App\Http\Requests;

use App\Enums\LeadType;
use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_column(LeadType::cases(), 'value'))],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:190'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'locality' => ['nullable', 'string', 'max:120'],
            'street' => ['nullable', 'string', 'max:190'],
            'house' => ['nullable', 'string', 'max:32'],
            'object_type' => ['nullable', 'string', 'max:64'],
            'tariff_id' => ['nullable', 'integer', 'exists:tariffs,id'],
            'company_name' => ['nullable', 'string', 'max:190'],
            'message' => ['nullable', 'string', 'max:5000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'size:0'],
            'source_page' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Укажите тип обращения.',
            'type.in' => 'Неизвестный тип обращения.',
            'name.required' => 'Укажите имя.',
            'name.max' => 'Имя слишком длинное.',
            'phone.max' => 'Телефон слишком длинный.',
            'email.email' => 'Укажите корректный e-mail.',
            'area_id.exists' => 'Выбранный населённый пункт не найден.',
            'tariff_id.exists' => 'Выбранный тариф не найден.',
            'message.max' => 'Сообщение слишком длинное.',
            'consent.accepted' => 'Необходимо согласие на обработку персональных данных.',
            'website.size' => 'Проверка не пройдена.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('phone') && ! $this->filled('email')) {
                $validator->errors()->add('phone', 'Укажите телефон или e-mail для связи.');
            }

            if ($this->filled('tariff_id')) {
                $tariff = Tariff::query()->published()->find($this->integer('tariff_id'));

                if (! $tariff) {
                    $validator->errors()->add('tariff_id', 'Выбранный тариф недоступен.');
                }
            }

            if ($this->input('type') === LeadType::Business->value && ! $this->filled('company_name')) {
                $validator->errors()->add('company_name', 'Укажите название организации.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->filled('phone') ? preg_replace('/[^0-9+()\-\s]/', '', (string) $this->input('phone')) : null,
            'name' => trim((string) $this->input('name')),
        ]);
    }
}
