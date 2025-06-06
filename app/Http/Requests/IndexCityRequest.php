<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admins can access cities management
        return auth()->check() && auth()->user()->hasRole('admin');
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
            'department_search' => 'nullable|string|max:255',
            'department_id' => 'nullable|integer|exists:departments,id',
            'sort' => 'nullable|string|in:name,population,department',
            'direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|in:5,10,25,50,100',
            'page' => 'nullable|integer|min:1',
            'population_operator' => 'nullable|string|in:gt,lt,eq|required_with:population_value',
            'population_value' => 'nullable|integer|min:0|max:10000000|required_with:population_operator'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'department_id.exists' => 'Le département sélectionné n\'existe pas.',
            'sort.in' => 'Le champ de tri doit être name, population ou department.',
            'direction.in' => 'La direction de tri doit être asc ou desc.',
            'per_page.in' => 'Le nombre d\'éléments par page doit être 5, 10, 25, 50 ou 100.',
            'page.min' => 'Le numéro de page doit être supérieur à 0.',
            'population_operator.in' => 'L\'opérateur de population doit être gt (plus grand), lt (plus petit) ou eq (égal).',
            'population_operator.required_with' => 'L\'opérateur est requis si une valeur de population est fournie.',
            'population_value.required_with' => 'La valeur de population est requise si un opérateur est fourni.',
            'population_value.min' => 'La population doit être supérieure ou égale à 0.',
            'population_value.max' => 'La population doit être inférieure à 10 millions.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer les valeurs vides
        $data = [];
        
        foreach ($this->all() as $key => $value) {
            if ($value !== '' && $value !== null) {
                $data[$key] = $value;
            }
        }
        
        $this->replace($data);
    }
}
