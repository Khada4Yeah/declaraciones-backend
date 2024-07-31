<?php

namespace App\Http\Requests;

use App\Models\PersonaJuridica;
use Illuminate\Validation\Rule;

class PersonaJuridicaRequest extends UsuarioRequest
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
        if ($this->route("personas_juridica")) {
            $persona_juridica = PersonaJuridica::findOrFail(
                $this->route("personas_juridica"),
            );
            return $persona_juridica->id_usuario;
        }
        return null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $baseRules = parent::rules();

        $customRules = [
            "ruc" => [
                "required",
                "numeric",
                "digits:13",
                Rule::unique("personas_juridicas", "ruc"),
            ],
            "razon_social" => "required|string",
            "clave_acceso" => "required|string",
            "informacion_adicional" => "string",
        ];
        if ($this->isMethod("PUT")) {
            $customRules["ruc"] = [
                "required",
                "numeric",
                "digits:13",
                Rule::unique("personas_juridicas", "ruc")->ignore(
                    $this->idUsuario(),
                    "id_usuario",
                ),
            ];
        }

        return array_merge($baseRules, $customRules);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            "ruc.required" => "El RUC es requerido",
            "ruc.numeric" => "El RUC debe ser numérico",
            "ruc.digits" => "El RUC debe tener 13 dígitos",
            "ruc.unique" => "El RUC ya está en uso",
            "razon_social.required" => "La razón social es requerida",
            "razon_social.string" =>
                "La razón social debe ser una cadena de texto",
            "clave_acceso.required" => "La clave de acceso es requerida",
        ]);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        parent::prepareForValidation();

        $this->merge([
            "ruc" => trim($this->input("ruc")),
            "razon_social" => trim($this->input("razon_social")),
        ]);
    }
}