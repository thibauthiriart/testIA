<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchPropertyByLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Properties search is accessible to all authenticated users
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            'city' => 'nullable|string|max:255',
            'radius' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'latitude.required_with' => 'La latitude est requise si la longitude est fournie.',
            'longitude.required_with' => 'La longitude est requise si la latitude est fournie.',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',
            'radius.min' => 'Le rayon doit être d\'au moins 1 km.',
            'radius.max' => 'Le rayon ne peut pas dépasser 100 km.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->validated();
            
            // Ensure at least one search parameter is provided
            if (empty($data['latitude']) && empty($data['longitude']) && empty($data['city'])) {
                $validator->errors()->add('search', 'Veuillez fournir soit des coordonnées (latitude/longitude) soit un nom de ville.');
            }
        });
    }
}