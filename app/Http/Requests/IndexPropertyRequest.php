<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexPropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Properties are accessible to all authenticated users
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
            'search' => 'nullable|string|max:255',
            'city_id' => 'nullable|integer|exists:cities,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'min_surface' => 'nullable|numeric|min:0',
            'max_surface' => 'nullable|numeric|min:0|gte:min_surface',
            'rooms' => 'nullable|integer|min:1|max:20',
            'property_type' => 'nullable|string|in:house,apartment,land,parking,other',
            'transaction_type' => 'nullable|string|in:sale,rent',
            'sort' => 'nullable|string|in:scraped_at,price,surface,rooms,created_at',
            'direction' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
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
            'city_id.exists' => 'La ville sélectionnée n\'existe pas.',
            'max_price.gte' => 'Le prix maximum doit être supérieur ou égal au prix minimum.',
            'max_surface.gte' => 'La surface maximum doit être supérieure ou égale à la surface minimum.',
            'property_type.in' => 'Le type de bien sélectionné n\'est pas valide.',
            'transaction_type.in' => 'Le type de transaction sélectionné n\'est pas valide.',
        ];
    }
}