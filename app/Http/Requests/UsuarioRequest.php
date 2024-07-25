<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the id of the user making the request.
     *
     * @return int|null
     */
    protected function idUsuario(): int|null
    {
        return null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        switch ($this->method()) {
            case "POST":
                return [
                    "correo_electronico" => "required|email|unique:usuarios",
                    "celular" => "required|numeric|digits:10",
                ];
                break;
            case "PATCH":
                return [
                    "correo_electronico" => [
                        "required",
                        "email",
                        Rule::unique("usuarios", "correo_electronico")->ignore(
                            $this->idUsuario(),
                            "id_usuario",
                        ),
                    ],
                    "celular" => "required|numeric|digits:10",
                ];
                break;

            default:
                break;
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "correo_electronico.required" =>
                "El correo electrónico es requerido",
            "correo_electronico.email" => "El correo electrónico no es válido",
            "correo_electronico.unique" =>
                "El correo electrónico ya está en uso",
            "celular.required" => "El celular es requerido",
            "celular.numeric" => "El celular debe ser numérico",
            "celular.digits" => "El celular debe tener 10 dígitos",
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