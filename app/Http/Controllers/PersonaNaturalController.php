<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonaNaturalRequest;
use App\Models\PersonaNatural;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PersonaNaturalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personas_naturales = PersonaNatural::with("usuario")->get();
        $personas_naturales = $personas_naturales->map(function ($persona) {
            $persona->clave_acceso = Crypt::decryptString(
                $persona->clave_acceso,
            );
            return $persona;
        });

        return response()->json($personas_naturales);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonaNaturalRequest $personaNaturalRequest)
    {
        // Inicio de la transacción
        DB::beginTransaction();

        try {
            // Obteniendo datos validados
            $usuarioData = $personaNaturalRequest->validated();

            // Creación del usuario
            $usuario = Usuario::create([
                "correo_electronico" => $usuarioData["correo_electronico"],
                "celular" => $usuarioData["celular"],
            ]);

            // Verifica si el usuario fue creado exitosamente
            if (!$usuario) {
                throw new \Exception("Error al crear el usuario");
            }

            // Creación de la persona natural
            $persona_natural = PersonaNatural::create([
                "id_usuario" => $usuario->id_usuario,
                "identificacion" => $usuarioData["identificacion"],
                "nombres" => $usuarioData["nombres"],
                "apellido_p" => $usuarioData["apellido_p"],
                "apellido_m" => $usuarioData["apellido_m"],
                "clave_acceso" => Crypt::encryptString(
                    $usuarioData["clave_acceso"],
                ),
                "informacion_adicional" =>
                    $usuarioData["informacion_adicional"] ?? null,
            ]);

            // Verifica si la persona natural fue creada exitosamente
            if (!$persona_natural) {
                throw new \Exception("Error al crear la persona natural");
            }

            // Commit de la transacción
            DB::commit();

            return response()->json(
                [
                    "id_persona_natural" =>
                        $persona_natural->id_persona_natural,
                    "id_usuario" => $usuario->id_usuario,
                    "identificacion" => $persona_natural->identificacion,
                    "nombres" => $persona_natural->nombres,
                    "apellido_p" => $persona_natural->apellido_p,
                    "apellido_m" => $persona_natural->apellido_m,
                    "informacion_adicional" =>
                        $persona_natural->informacion_adicional,
                    "usuario" => $usuario,
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "error" => "Error al crear la persona natural",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $idPersonaNatural)
    {
        $persona_natural = PersonaNatural::with("usuario")->findOrFail(
            $idPersonaNatural,
        );
        $persona_natural->clave_acceso = Crypt::decryptString(
            $persona_natural->clave_acceso,
        );
        return response()->json($persona_natural);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        PersonaNaturalRequest $personaNaturalRequest,
        int $idPersonaNatural,
    ) {
        // Busca la persona natural
        $persona_natural = PersonaNatural::findOrFail($idPersonaNatural);

        // Inicio de la transacción
        DB::beginTransaction();

        try {
            // Obteniendo datos validados
            $usuarioData = $personaNaturalRequest->validated();

            // Actualización del usuario
            $usuario = Usuario::findOrFail($persona_natural->id_usuario);
            $usuario->update([
                "correo_electronico" => $usuarioData["correo_electronico"],
                "celular" => $usuarioData["celular"],
            ]);

            // Guardar el modelo
            $usuario->save();

            // Actualización de la persona natural
            $persona_natural->update([
                "identificacion" => $usuarioData["identificacion"],
                "nombres" => $usuarioData["nombres"],
                "apellido_p" => $usuarioData["apellido_p"] ?? null,
                "apellido_m" => $usuarioData["apellido_m"] ?? null,
                "clave_acceso" => Crypt::encryptString(
                    $usuarioData["clave_acceso"],
                ),
                "informacion_adicional" =>
                    $usuarioData["informacion_adicional"] ?? null,
            ]);

            // Guardar el modelo
            $persona_natural->save();

            // Commit de la transacción
            DB::commit();

            return response()->json([
                "id_persona_natural" => $persona_natural->id_persona_natural,
                "id_usuario" => $usuario->id_usuario,
                "identificacion" => $persona_natural->identificacion,
                "nombres" => $persona_natural->nombres,
                "apellido_p" => $persona_natural->apellido_p,
                "apellido_m" => $persona_natural->apellido_m,
                "informacion_adicional" =>
                    $persona_natural->informacion_adicional,
                "usuario" => $usuario,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "error" => "Error al actualizar la persona natural",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $idPersonaNatural)
    {
        // Busca la persona natural
        $persona_natural = PersonaNatural::findOrFail($idPersonaNatural);

        // Inicio de la transacción
        DB::beginTransaction();

        try {
            // Eliminación de la persona natural
            $persona_natural->delete();

            // Eliminación del usuario
            $usuario = Usuario::findOrFail($persona_natural->id_usuario);
            $usuario->delete();

            // Commit de la transacción
            DB::commit();

            // Respuesta
            return response()->json(
                [
                    "message" => "Persona natural eliminada exitosamente",
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "error" => "Error al eliminar la persona natural",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
