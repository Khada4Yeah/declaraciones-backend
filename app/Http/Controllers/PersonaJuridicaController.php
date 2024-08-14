<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonaJuridicaRequest;
use App\Models\PersonaJuridica;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PersonaJuridicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personas_juridicas = PersonaJuridica::with("usuario")->get();
        $personas_juridicas = $personas_juridicas->map(function ($persona) {
            $persona->clave_acceso = Crypt::decryptString(
                $persona->clave_acceso,
            );
            return $persona;
        });
        return response()->json($personas_juridicas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonaJuridicaRequest $personaJuridicaRequest)
    {
        // Inicio de la transacción
        DB::beginTransaction();

        try {
            // Obteniendo datos validados
            $usuarioData = $personaJuridicaRequest->validated();

            // Creación del usuario
            $usuario = Usuario::create([
                "correo_electronico" => $usuarioData["correo_electronico"],
                "celular" => $usuarioData["celular"],
            ]);

            // Verifica si el usuario fue creado exitosamente
            if (!$usuario) {
                throw new \Exception("Error al crear el usuario");
            }

            // Creación de la persona jurídica
            $persona_juridica = PersonaJuridica::create([
                "id_usuario" => $usuario->id_usuario,
                "ruc" => $usuarioData["ruc"],
                "razon_social" => $usuarioData["razon_social"],
                "clave_acceso" => Crypt::encryptString(
                    $usuarioData["clave_acceso"],
                ),
                "informacion_adicional" =>
                    $usuarioData["informacion_adicional"] ?? null,
            ]);

            // Verifica si la persona jurídica fue creada exitosamente
            if (!$persona_juridica) {
                throw new \Exception("Error al crear la persona jurídica");
            }

            // Commit de la transacción
            DB::commit();

            return response()->json(
                [
                    "id_persona_juridica" =>
                        $persona_juridica->id_persona_juridica,
                    "id_usuario" => $usuario->id_usuario,
                    "ruc" => $persona_juridica->ruc,
                    "razon_social" => $persona_juridica->razon_social,
                    "informacion_adicional" =>
                        $persona_juridica->informacion_adicional,
                    "usuario" => $usuario,
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "error" => "Error al crear la persona jurídica",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $idPersonaJuridica)
    {
        $personaJuridica = PersonaJuridica::with("usuario")->findOrFail(
            $idPersonaJuridica,
        );
        $personaJuridica->clave_acceso = Crypt::decryptString(
            $personaJuridica->clave_acceso,
        );
        return response()->json($personaJuridica);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        PersonaJuridicaRequest $personaJuridicaRequest,
        int $idPersonaJuridica,
    ) {
        // Buscando la persona jurídica
        $personaJuridica = PersonaJuridica::findOrFail($idPersonaJuridica);

        // Inicio de la transacción
        DB::beginTransaction();

        try {
            // Obteniendo datos validados
            $usuarioData = $personaJuridicaRequest->validated();

            // Actualización del usuario
            $usuario = Usuario::findOrFail($personaJuridica->id_usuario);
            $usuario->correo_electronico = $usuarioData["correo_electronico"];
            $usuario->celular = $usuarioData["celular"];

            // Guardar el modelo
            $usuario->save();

            // Actualización de la persona jurídica
            $personaJuridica->ruc = $usuarioData["ruc"];
            $personaJuridica->razon_social = $usuarioData["razon_social"];
            $personaJuridica->clave_acceso = Crypt::encryptString(
                $usuarioData["clave_acceso"],
            );
            $personaJuridica->informacion_adicional =
                $usuarioData["informacion_adicional"] ?? null;

            // Guardar el modelo
            $personaJuridica->save();

            // Commit de la transacción
            DB::commit();

            return response()->json(
                [
                    "id_persona_juridica" =>
                        $personaJuridica->id_persona_juridica,
                    "id_usuario" => $usuario->id_usuario,
                    "ruc" => $personaJuridica->ruc,
                    "razon_social" => $personaJuridica->razon_social,
                    "informacion_adicional" =>
                        $personaJuridica->informacion_adicional,
                    "usuario" => $usuario,
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "error" => "Error al actualizar la persona jurídica",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $idPersonaJuridica)
    {
        // Busca la persona natural
        $persona_juridica = PersonaJuridica::findOrFail($idPersonaJuridica);

        // Inicio de la transacción
        DB::beginTransaction();

        try {
            // Eliminación de la persona natural
            $persona_juridica->delete();

            // Eliminación del usuario
            $usuario = Usuario::findOrFail($persona_juridica->id_usuario);
            $usuario->delete();

            // Commit de la transacción
            DB::commit();

            // Respuesta
            return response()->json(
                [
                    "message" => "Persona juridica eliminada exitosamente",
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "error" => "Error al eliminar la persona juridica",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }
}