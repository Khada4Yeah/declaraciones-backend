<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la subida de archivos.
 * Valida que se envíe al menos un archivo, que el año sea válido
 * y que cada archivo cumpla con los tipos y tamaño permitidos.
 */
class ArchivoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta petición.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para la subida de archivos.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "id_usuario" => "required|integer|exists:usuarios,id_usuario",
            "year" => "required|integer|min:2020|max:" . (date("Y") + 1),
            "files" => "required|array|min:1",
            "files.*" => [
                "required",
                "file",
                "mimes:pdf,xml,jpg,jpeg,png,xlsx,xls,docx,doc,csv,txt,zip",
                "max:10240",
            ],
        ];
    }

    /**
     * Mensajes de validación personalizados en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "id_usuario.required" => "El usuario es obligatorio",
            "id_usuario.integer" => "El ID de usuario debe ser un número entero",
            "id_usuario.exists" => "El usuario especificado no existe",
            "year.required" => "El año es obligatorio",
            "year.integer" => "El año debe ser un número entero",
            "year.min" => "El año mínimo permitido es 2020",
            "year.max" => "El año máximo permitido es " . (date("Y") + 1),
            "files.required" => "Debe seleccionar al menos un archivo",
            "files.array" => "Los archivos deben enviarse como un arreglo",
            "files.min" => "Debe seleccionar al menos un archivo",
            "files.*.required" => "Cada archivo es obligatorio",
            "files.*.file" => "Cada elemento debe ser un archivo válido",
            "files.*.mimes" =>
                "Solo se permiten archivos de tipo: pdf, xml, jpg, jpeg, png, xlsx, xls, docx, doc, csv, txt, zip",
            "files.*.max" =>
                "Cada archivo no debe superar los 10MB de tamaño",
        ];
    }
}
