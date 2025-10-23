<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plataforma;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PlataformaController extends Controller
{
    /**
     * Listar todas las plataformas con conteo de videojuegos
     */
    public function index(): JsonResponse
    {
        $plataformas = Plataforma::withCount('videojuegos')->get();

        return response()->json([
            'data' => $plataformas,
        ], 200);
    }

    /**
     * Ver una plataforma específica con sus videojuegos
     */
    public function show(string $id): JsonResponse
    {
        $plataforma = Plataforma::with('videojuegos')->find($id);

        if (!$plataforma) {
            return response()->json([
                'message' => 'Plataforma no encontrada',
            ], 404);
        }

        return response()->json([
            'data' => $plataforma,
        ], 200);
    }

    /**
     * Obtener la plataforma más popular (con más videojuegos)
     */
    public function masPopular(): JsonResponse
    {
        $plataforma = Plataforma::withCount('videojuegos')
            ->orderBy('videojuegos_count', 'desc')
            ->first();

        if (!$plataforma) {
            return response()->json([
                'message' => 'No hay plataformas disponibles',
            ], 404);
        }

        $plataforma->load('videojuegos');

        return response()->json([
            'data' => $plataforma,
            'message' => "La plataforma más popular es {$plataforma->nombre} con {$plataforma->videojuegos_count} videojuegos",
        ], 200);
    }

    /**
     * Crear una nueva plataforma (Solo Admin)
     */
    public function store(Request $request): JsonResponse
    {
        if (Gate::denies('admin-only')) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción',
            ], 403);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:plataformas,nombre',
            'fabricante' => 'nullable|string|max:150',
        ]);

        $plataforma = Plataforma::create($validated);

        return response()->json([
            'message' => 'Plataforma creada exitosamente',
            'data' => $plataforma,
        ], 201);
    }

    /**
     * Actualizar una plataforma existente (Solo Admin)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        if (Gate::denies('admin-only')) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción',
            ], 403);
        }

        $plataforma = Plataforma::find($id);

        if (!$plataforma) {
            return response()->json([
                'message' => 'Plataforma no encontrada',
            ], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100|unique:plataformas,nombre,' . $id,
            'fabricante' => 'sometimes|nullable|string|max:150',
        ]);

        $plataforma->update($validated);

        return response()->json([
            'message' => 'Plataforma actualizada exitosamente',
            'data' => $plataforma,
        ], 200);
    }

    /**
     * Eliminar una plataforma (Solo Admin)
     */
    public function destroy(string $id): JsonResponse
    {
        if (Gate::denies('admin-only')) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción',
            ], 403);
        }

        $plataforma = Plataforma::find($id);

        if (!$plataforma) {
            return response()->json([
                'message' => 'Plataforma no encontrada',
            ], 404);
        }

        // Desvincular todos los videojuegos antes de eliminar
        $plataforma->videojuegos()->detach();
        $plataforma->delete();

        return response()->json([
            'message' => 'Plataforma eliminada exitosamente',
        ], 200);
    }
}