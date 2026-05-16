<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonaJuridicaRequest;
use App\Services\PersonaJuridicaService;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de personas jurídicas.
 * Delega toda la lógica de negocio al PersonaJuridicaService.
 */
class PersonaJuridicaController extends Controller
{
    public function __construct(
        private PersonaJuridicaService $personaJuridicaService,
    ) {}

    /**
     * Obtiene el listado de todas las personas jurídicas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $personas = $this->personaJuridicaService->obtenerTodas();

        return response()->json([
            "status" => "success",
            "data" => $personas,
        ]);
    }

    /**
     * Crea una nueva persona jurídica.
     *
     * @param PersonaJuridicaRequest $request Datos validados.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PersonaJuridicaRequest $request)
    {
        try {
            $persona = $this->personaJuridicaService->crear($request->validated());

            return response()->json([
                "status" => "success",
                "message" => "Persona jurídica creada exitosamente",
                "data" => $persona,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error al crear persona jurídica: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al crear la persona jurídica",
            ], 500);
        }
    }

    /**
     * Muestra el detalle de una persona jurídica (incluye clave de acceso).
     *
     * @param int $idPersonaJuridica ID de la persona jurídica.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $idPersonaJuridica)
    {
        $persona = $this->personaJuridicaService->obtenerPorId($idPersonaJuridica);

        return response()->json([
            "status" => "success",
            "data" => $persona,
        ]);
    }

    /**
     * Actualiza una persona jurídica existente.
     *
     * @param PersonaJuridicaRequest $request Datos validados.
     * @param int $idPersonaJuridica ID de la persona jurídica.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PersonaJuridicaRequest $request, int $idPersonaJuridica)
    {
        try {
            $persona = $this->personaJuridicaService->actualizar(
                $idPersonaJuridica,
                $request->validated(),
            );

            return response()->json([
                "status" => "success",
                "message" => "Persona jurídica actualizada exitosamente",
                "data" => $persona,
            ]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar persona jurídica: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al actualizar la persona jurídica",
            ], 500);
        }
    }

    /**
     * Elimina una persona jurídica.
     *
     * @param int $idPersonaJuridica ID de la persona jurídica.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $idPersonaJuridica)
    {
        try {
            $this->personaJuridicaService->eliminar($idPersonaJuridica);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error("Error al eliminar persona jurídica: " . $e->getMessage());
            return response()->json([
                "status" => "error",
                "message" => "Error al eliminar la persona jurídica",
            ], 500);
        }
    }
}