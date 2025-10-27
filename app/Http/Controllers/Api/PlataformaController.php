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
 * @OA\Get(
 *     path="/api/v1/plataformas",
 *     tags={"Plataformas"},
 *     summary="Listar todas las plataformas",
 *     description="Obtiene la lista completa de plataformas con el conteo de videojuegos",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de plataformas obtenida exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="nombre", type="string", example="PlayStation 5"),
 *                     @OA\Property(property="fabricante", type="string", example="Sony"),
 *                     @OA\Property(property="videojuegos_count", type="integer", example=5)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado"
 *     )
 * )
 */
    public function index(): JsonResponse
    {
        $plataformas = Plataforma::withCount('videojuegos')->get();

        return response()->json([
            'data' => $plataformas,
        ], 200);
    }

/**
 * @OA\Get(
 *     path="/api/v1/plataformas/{id}",
 *     tags={"Plataformas"},
 *     summary="Ver plataforma por ID",
 *     description="Obtiene una plataforma específica con sus videojuegos",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la plataforma",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Plataforma encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="nombre", type="string", example="PlayStation 5"),
 *                 @OA\Property(property="fabricante", type="string", example="Sony"),
 *                 @OA\Property(
 *                     property="videojuegos",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer"),
 *                         @OA\Property(property="titulo", type="string"),
 *                         @OA\Property(property="anio_lanzamiento", type="string", format="date"),
 *                         @OA\Property(property="genero", type="string")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Plataforma no encontrada"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado"
 *     )
 * )
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
 * @OA\Get(
 *     path="/api/v1/plataformas/mas-popular",
 *     tags={"Plataformas"},
 *     summary="Obtener plataforma más popular",
 *     description="Obtiene la plataforma con mayor cantidad de videojuegos",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Plataforma más popular obtenida exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="nombre", type="string", example="PC"),
 *                 @OA\Property(property="fabricante", type="string", example="Varios"),
 *                 @OA\Property(property="videojuegos_count", type="integer", example=10),
 *                 @OA\Property(
 *                     property="videojuegos",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer"),
 *                         @OA\Property(property="titulo", type="string")
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="message", type="string", example="La plataforma más popular es PC con 10 videojuegos")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No hay plataformas disponibles"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado"
 *     )
 * )
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
 * @OA\Post(
 *     path="/api/v1/plataformas",
 *     tags={"Plataformas"},
 *     summary="Crear plataforma (Solo Admin)",
 *     description="Crea una nueva plataforma. Requiere permisos de administrador",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nombre"},
 *             @OA\Property(property="nombre", type="string", example="Steam Deck"),
 *             @OA\Property(property="fabricante", type="string", example="Valve")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Plataforma creada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Plataforma creada exitosamente"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=7),
 *                 @OA\Property(property="nombre", type="string", example="Steam Deck"),
 *                 @OA\Property(property="fabricante", type="string", example="Valve")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No tienes permisos (solo admin)"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado"
 *     )
 * )
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
 * @OA\Put(
 *     path="/api/v1/plataformas/{id}",
 *     tags={"Plataformas"},
 *     summary="Actualizar plataforma (Solo Admin)",
 *     description="Actualiza una plataforma existente. Requiere permisos de administrador",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la plataforma",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="nombre", type="string", example="Steam Deck OLED"),
 *             @OA\Property(property="fabricante", type="string", example="Valve Corporation")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Plataforma actualizada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Plataforma actualizada exitosamente"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="nombre", type="string"),
 *                 @OA\Property(property="fabricante", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No tienes permisos"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Plataforma no encontrada"
 *     )
 * )
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
 * @OA\Delete(
 *     path="/api/v1/plataformas/{id}",
 *     tags={"Plataformas"},
 *     summary="Eliminar plataforma (Solo Admin)",
 *     description="Elimina una plataforma. Requiere permisos de administrador",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la plataforma",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Plataforma eliminada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Plataforma eliminada exitosamente")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No tienes permisos"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Plataforma no encontrada"
 *     )
 * )
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