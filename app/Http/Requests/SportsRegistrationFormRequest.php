<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SportsRegistrationFormRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $formRoute = 'forms.sports.create';
        $initiative = \App\Models\Initiative::where('form_route', $formRoute)->first();

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:10', 'max:30'],
            'gender' => ['required', 'in:Male,Female,Prefer not to say'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
            'sport' => ['required', 'string'],
            'team_name' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'remarks' => ['nullable', 'string'],
        ];

        if ($initiative && is_array($initiative->custom_fields)) {
            $rules['custom_fields'] = ['nullable', 'array'];
            foreach ($initiative->custom_fields as $field) {
                $fieldName = $field['name'] ?? null;
                if (!$fieldName) continue;

                $fieldRules = [];
                if ($field['required'] ?? false) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                $fieldRules[] = match ($field['type'] ?? 'text') {
                    'number' => 'numeric',
                    'date' => 'date',
                    default => 'string',
                };

                $rules["custom_fields.{$fieldName}"] = $fieldRules;
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];
        $formRoute = 'forms.sports.create';
        $initiative = \App\Models\Initiative::where('form_route', $formRoute)->first();
        if ($initiative && is_array($initiative->custom_fields)) {
            foreach ($initiative->custom_fields as $field) {
                $fieldName = $field['name'] ?? null;
                if ($fieldName) {
                    $attributes["custom_fields.{$fieldName}"] = $field['label'] ?? $fieldName;
                }
            }
        }
        return $attributes;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        session()->flash('failed_form', 'sports');
        parent::failedValidation($validator);
    }
}
