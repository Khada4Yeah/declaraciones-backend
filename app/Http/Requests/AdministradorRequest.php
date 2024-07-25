<?php

namespace App\Http\Requests;

use App\Models\Administrador;

class AdministradorRequest extends UsuarioRequest
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
        if ($this->route("administradore")) {
            $administrador = Administrador::findOrFail(
                $this->route("administradore"),
            );
            return $administrador->id_usuario;
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
        return array_merge(parent::rules(), [
            "nombres" => "required|string",
            "apellido_p" => "string",
            "apellido_m" => "string",
            "clave" => "required",
        ]);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            "nombres.required" => "El nombre es requerido",
            "nombres.string" => "El nombre debe ser una cadena de texto",
            "apellido_p.string" =>
                "El apellido paterno debe ser una cadena de texto",
            "apellido_m.string" =>
                "El apellido materno debe ser una cadena de texto",
            "clave.required" => "La clave es requerida",
        ]);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        parent::prepareForValidation(); // Llama al método base

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