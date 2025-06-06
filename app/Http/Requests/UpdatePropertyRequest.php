<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Properties can be updated by all authenticated users
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
            'title' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|string|max:500',
            'price' => 'sometimes|required|numeric|min:0|max:999999999',
            'transaction_type' => 'sometimes|required|string|in:sale,rent',
            'property_type' => 'sometimes|required|string|in:apartment,house,villa,studio,land,parking,other',
            'city_id' => 'sometimes|required|integer|exists:cities,id',
            'surface' => 'sometimes|nullable|numeric|min:0|max:999999',
            'rooms' => 'sometimes|nullable|integer|min:1|max:20',
            'source' => 'sometimes|nullable|string|max:100',
            'source_id' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:2000',
            'images' => 'sometimes|nullable|array|max:10',
            'images.*' => 'url|max:500',
            'scraped_at' => 'sometimes|nullable|date',
            'is_active' => 'sometimes|nullable|boolean'
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
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'url.url' => 'L\'URL doit être valide.',
            'price.required' => 'Le prix est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix ne peut pas être négatif.',
            'transaction_type.required' => 'Le type de transaction est obligatoire.',
            'transaction_type.in' => 'Le type de transaction doit être "vente" ou "location".',
            'property_type.required' => 'Le type de bien est obligatoire.',
            'property_type.in' => 'Le type de bien sélectionné n\'est pas valide.',
            'city_id.required' => 'La ville est obligatoire.',
            'city_id.exists' => 'La ville sélectionnée n\'existe pas.',
            'surface.numeric' => 'La surface doit être un nombre.',
            'surface.min' => 'La surface ne peut pas être négative.',
            'rooms.integer' => 'Le nombre de pièces doit être un entier.',
            'rooms.min' => 'Le nombre de pièces doit être au moins 1.',
            'rooms.max' => 'Le nombre de pièces ne peut pas dépasser 20.',
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
            'images.array' => 'Les images doivent être un tableau.',
            'images.max' => 'Vous ne pouvez pas ajouter plus de 10 images.',
            'images.*.url' => 'Chaque image doit être une URL valide.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default URL if empty during update
        if ($this->has('url') && empty($this->input('url'))) {
            $this->merge(['url' => 'annonce inconnue']);
        }
    }
}