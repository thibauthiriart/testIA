<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => [
                'sometimes',
                'required',
                'string',
                'regex:/^[A-Za-zÀ-ÿ\s\-\']+$/',
                'min:2',
                'max:100'
            ],
            'code_postal' => [
                'sometimes',
                'required',
                'string',
                'regex:/^[0-9]{5}$/'
            ],
            'population' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                'max:50000000'
            ],
            'department_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:departments,id'
            ]
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.regex' => 'Le nom de la ville ne doit contenir que des lettres, espaces, tirets et apostrophes',
            'nom.min' => 'Le nom de la ville doit contenir au moins 2 caractères',
            'nom.max' => 'Le nom de la ville ne peut pas dépasser 100 caractères',
            'population.min' => 'La population ne peut pas être négative',
            'population.max' => 'La population ne peut pas dépasser 50 millions',
            'department_id.exists' => 'Le département sélectionné n\'existe pas'
        ];
    }
}