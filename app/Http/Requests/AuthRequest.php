<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AuthRequest extends FormRequest
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
            "correo_electronico" => "required|email",
            "clave" => "required",
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "correo_electronico.required" =>
                "El correo electrónico es requerido",
            "correo_electronico.email" => "El correo electrónico no es válido",
            "clave.required" => "La contraseña es obligatoria.",
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            "correo_electronico" => strtolower(
                trim($this->input("correo_electronico")),
            ),
        ]);
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException(
            $validator,
            response()->json(
                [
                    "message" => "Datos no válidos",
                    "errors" => $validator->errors(),
                ],
                400,
            ),
        );
    }
}