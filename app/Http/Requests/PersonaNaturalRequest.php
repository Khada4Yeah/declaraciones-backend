<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonaNaturalRequest extends FormRequest
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
        $rules = parent::rules();

        switch ($this->method()) {
            case "POST":
                $rules = array_merge($rules, [
                    "ruc" => "required|numeric|digits:13",
                    "razon_social" => "required|string",
                    "clave_acceso" => "required|string",
                ]);
                break;
            case "PATCH":
                $rules = array_merge($rules, [
                    "ruc" => [
                        "required",
                        "numeric",
                        "digits:13",
                        Rule::unique("personas_juridicas")->ignore(
                            $this->route("personas_juridica"),
                            "id_usuario",
                        ),
                    ],
                    "razon_social" => "required|string",
                    "clave_acceso" => "required|string",
                ]);
                break;
            default:
                break;
        }

        return $rules;
    }
}