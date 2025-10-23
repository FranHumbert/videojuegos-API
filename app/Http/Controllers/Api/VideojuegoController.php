<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Videojuego;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VideojuegoController extends Controller
{
    public function index(): JsonResponse
    {
        $videojuegos = Videojuego::with('plataformas')->get();

        return response()->json([
            'data' => $videojuegos,
        ], 200);
    }

    public function show(string $id): JsonResponse
    {
        $videojuego = Videojuego::with('plataformas')->find($id);

        if (!$videojuego) {
            return response()->json([
                'message' => 'Videojuego no encontrado',
            ], 404);
        }

        return response()->json([
            'data' => $videojuego,
        ], 200);
    }

    public function recientes(): JsonResponse
    {
        $videojuegos = Videojuego::with('plataformas')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'data' => $videojuegos,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        if (Gate::denies('admin-only')) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción',
            ], 403);
        }

        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'anio_lanzamiento' => 'required|date',
            'genero' => 'required|string|max:150',
            'plataformas' => 'required|array',
            'plataformas.*' => 'exists:plataformas,id',
        ]);

        $videojuego = Videojuego::create([
            'titulo' => $validated['titulo'],
            'anio_lanzamiento' => $validated['anio_lanzamiento'],
            'genero' => $validated['genero'],
        ]);

        $videojuego->plataformas()->attach($validated['plataformas']);
        $videojuego->load('plataformas');

        return response()->json([
            'message' => 'Videojuego creado exitosamente',
            'data' => $videojuego,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        if (Gate::denies('admin-only')) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción',
            ], 403);
        }

        $videojuego = Videojuego::find($id);

        if (!$videojuego) {
            return response()->json([
                'message' => 'Videojuego no encontrado',
            ], 404);
        }

        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:150',
            'anio_lanzamiento' => 'sometimes|date',
            'genero' => 'sometimes|string|max:150',
            'plataformas' => 'sometimes|array',
            'plataformas.*' => 'exists:plataformas,id',
        ]);

        $videojuego->update($validated);

        if (isset($validated['plataformas'])) {
            $videojuego->plataformas()->sync($validated['plataformas']);
        }

        $videojuego->load('plataformas');

        return response()->json([
            'message' => 'Videojuego actualizado exitosamente',
            'data' => $videojuego,
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        if (Gate::denies('admin-only')) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción',
            ], 403);
        }

        $videojuego = Videojuego::find($id);

        if (!$videojuego) {
            return response()->json([
                'message' => 'Videojuego no encontrado',
            ], 404);
        }

        $videojuego->plataformas()->detach();
        $videojuego->delete();

        return response()->json([
            'message' => 'Videojuego eliminado exitosamente',
        ], 200);
    }
}