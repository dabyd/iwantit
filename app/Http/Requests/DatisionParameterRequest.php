<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatisionParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ajusta con políticas si hace falta
    }

    public function rules(): array
    {
        return [
            'machine_url'   => ['required', 'string', 'url', 'max:2048'],
            'threshold_sec' => ['required', 'integer', 'min:0', 'max:86400'],
        ];
    }

    public function attributes(): array
    {
        return [
            'machine_url'   => __('datision.fields.machine_url'),
            'threshold_sec' => __('datision.fields.threshold_sec'),
        ];
    }

    public function messages(): array
    {
        return [
            'machine_url.required' => __('datision.validation.machine_url_required'),
            'machine_url.url'      => __('datision.validation.machine_url_url'),
            'threshold_sec.required'=> __('datision.validation.threshold_required'),
            'threshold_sec.integer' => __('datision.validation.threshold_integer'),
            'threshold_sec.min'     => __('datision.validation.threshold_min'),
            'threshold_sec.max'     => __('datision.validation.threshold_max'),
        ];
    }
}