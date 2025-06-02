<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'regex:/^([0-9]{2}[A-Z]?|97[1-6]|98[4-8])$/',
                'unique:departments,code',
                'max:3'
            ],
            'name' => [
                'required',
                'string',
                'regex:/^[A-Za-zÀ-ÿ\s\-\']+$/',
                'min:2',
                'max:100'
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
            'code.regex' => 'Le code département doit être composé de 2 chiffres suivis éventuellement d\'une lettre majuscule (ex: 75, 2A, 2B) ou d\'un code DOM-TOM (971-976, 984-988)',
            'code.unique' => 'Ce code département existe déjà',
            'nom.regex' => 'Le nom du département ne doit contenir que des lettres, espaces, tirets et apostrophes',
            'nom.min' => 'Le nom du département doit contenir au moins 2 caractères',
            'nom.max' => 'Le nom du département ne peut pas dépasser 100 caractères'
        ];
    }
}