<?php

namespace App\Http\Requests;

use App\Models\PersonaNatural;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonaNaturalRequest extends UsuarioRequest
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
        if ($this->route("personas_naturale")) {
            $persona_natural = PersonaNatural::findOrFail(
                $this->route("personas_naturale"),
            );
            return $persona_natural->id_usuario;
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
            "identificacion" => [
                "required",
                "numeric",
                'regex:/^\d{10}$|^\d{13}$/',
                Rule::unique("personas_naturales", "identificacion"),
            ],
            "nombres" => "required|string",
            "apellido_p" => "nullable|string",
            "apellido_m" => "nullable|string",
            "clave_acceso" => "required|string",
            "informacion_adicional" => "string",
        ];

        if ($this->isMethod("PUT")) {
            $customRules["identificacion"] = [
                "required",
                "numeric",
                'regex:/^\d{10}$|^\d{13}$/',
                Rule::unique("personas_naturales", "identificacion")->ignore(
                    $this->idUsuario(),
                    "id_usuario",
                ),
            ];
        }

        return array_merge($baseRules, $customRules);
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            "identificacion.required" => "La cédula o el RUC es requerido",
            "identificacion.unique" =>
                "La cédula o el RUC ya está registrado en el sistema",
            "identificacion.numeric" => "La cédula o el RUC debe ser numérica",
            "identificacion.digits_between" =>
                "La cédula o el RUC debe tener 10 o 13 dígitos",
            "nombres.required" => "El nombre es obligatorio",
            "nombres.string" => "El nombre debe ser una cadena de texto",
            "apellido_p.string" =>
                "El apellido paterno debe ser una cadena de texto",
            "apellido_m.string" =>
                "El apellido materno debe ser una cadena de texto",
            "clave_acceso.required" => "La contraseña es obligatoria",
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
            "nombres" => ucwords(strtolower(trim($this->input("nombres")))),
            "apellido_p" => ucwords(
                strtolower(trim($this->input("apellido_p"))),
            ),
            "apellido_m" => ucwords(
                strtolower(trim($this->input("apellido_m"))),
            ),
        ]);
    }
}