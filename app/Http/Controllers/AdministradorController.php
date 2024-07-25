<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdministradorRequest;
use App\Models\Administrador;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdministradorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $administradores = Administrador::with("usuario")->get();
        return response()->json($administradores);
    }

    public function store(AdministradorRequest $administradorRequest)
    {
        // Inicio de la transacción
        DB::beginTransaction();

        try {
            // Validar solicitudes
            $usuarioData = $administradorRequest->validated();

            // Creación del usuario
            $usuario = Usuario::create([
                "correo_electronico" => $usuarioData["correo_electronico"],
                "celular" => $usuarioData["celular"],
            ]);

            // Verifica si el usuario fue creado exitosamente
            if (!$usuario) {
                throw new \Exception("Error al crear el usuario");
            }

            // Creación del administrador
            $administrador = Administrador::create([
                "id_usuario" => $usuario->id_usuario,
                "nombres" => $usuarioData["nombres"],
                "apellido_p" => $usuarioData["apellido_p"],
                "apellido_m" => $usuarioData["apellido_m"],
                "clave" => bcrypt($usuarioData["clave"]),
            ]);

            // Verifica si el administrador fue creado exitosamente
            if (!$administrador) {
                throw new \Exception("Error al crear el administrador");
            }

            // Commit de la transacción
            DB::commit();
            return response()->json(
                [
                    "id_administrador" => $administrador->id_administrador,
                    "id_usuario" => $administrador->id_usuario,
                    "nombres" => $administrador->nombres,
                    "apellido_p" => $administrador->apellido_p,
                    "apellido_m" => $administrador->apellido_m,
                    "usuario" => $usuario,
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "error" => "Error al crear el administrador",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $idAdministrador)
    {
        $administrador = Administrador::with("usuario")->find($idAdministrador);
        if (!$administrador) {
            return response()->json(
                ["error" => "Administrador no encontrado"],
                404,
            );
        }
        return response()->json($administrador);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Administrador $administrador)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Administrador $administrador)
    {
        //
    }
}