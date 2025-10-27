<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Videojuego;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VideojuegoController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/v1/videojuegos",
 *     tags={"Videojuegos"},
 *     summary="Listar todos los videojuegos",
 *     description="Obtiene la lista completa de videojuegos con sus plataformas",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de videojuegos obtenida exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="titulo", type="string", example="The Legend of Zelda: Breath of the Wild"),
 *                     @OA\Property(property="anio_lanzamiento", type="string", format="date", example="2017-03-03"),
 *                     @OA\Property(property="genero", type="string", example="Aventura"),
 *                     @OA\Property(
 *                         property="plataformas",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="id", type="integer", example=3),
 *                             @OA\Property(property="nombre", type="string", example="Nintendo Switch"),
 *                             @OA\Property(property="fabricante", type="string", example="Nintendo")
 *                         )
 *                     )
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
        $videojuegos = Videojuego::with('plataformas')->get();

        return response()->json([
            'data' => $videojuegos,
        ], 200);
    }

    /**
 * @OA\Get(
 *     path="/api/v1/videojuegos/{id}",
 *     tags={"Videojuegos"},
 *     summary="Ver videojuego por ID",
 *     description="Obtiene un videojuego específico con sus plataformas",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del videojuego",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Videojuego encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="titulo", type="string", example="Elden Ring"),
 *                 @OA\Property(property="anio_lanzamiento", type="string", format="date", example="2022-02-25"),
 *                 @OA\Property(property="genero", type="string", example="RPG"),
 *                 @OA\Property(
 *                     property="plataformas",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer"),
 *                         @OA\Property(property="nombre", type="string"),
 *                         @OA\Property(property="fabricante", type="string")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Videojuego no encontrado"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado"
 *     )
 * )
 */

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

    /**
 * @OA\Get(
 *     path="/api/v1/videojuegos/recientes",
 *     tags={"Videojuegos"},
 *     summary="Obtener videojuegos recientes",
 *     description="Obtiene los 3 videojuegos más recientes agregados al sistema",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Videojuegos recientes obtenidos exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="titulo", type="string"),
 *                     @OA\Property(property="anio_lanzamiento", type="string", format="date"),
 *                     @OA\Property(property="genero", type="string")
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

    /**
 * @OA\Post(
 *     path="/api/v1/videojuegos",
 *     tags={"Videojuegos"},
 *     summary="Crear videojuego (Solo Admin)",
 *     description="Crea un nuevo videojuego. Requiere permisos de administrador",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"titulo","anio_lanzamiento","genero","plataformas"},
 *             @OA\Property(property="titulo", type="string", example="Super Mario Odyssey"),
 *             @OA\Property(property="anio_lanzamiento", type="string", format="date", example="2017-10-27"),
 *             @OA\Property(property="genero", type="string", example="Plataformas"),
 *             @OA\Property(
 *                 property="plataformas",
 *                 type="array",
 *                 @OA\Items(type="integer"),
 *                 example={3}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Videojuego creado exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Videojuego creado exitosamente"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="titulo", type="string"),
 *                 @OA\Property(property="anio_lanzamiento", type="string", format="date"),
 *                 @OA\Property(property="genero", type="string")
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

    /**
 * @OA\Put(
 *     path="/api/v1/videojuegos/{id}",
 *     tags={"Videojuegos"},
 *     summary="Actualizar videojuego (Solo Admin)",
 *     description="Actualiza un videojuego existente. Requiere permisos de administrador",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del videojuego",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="titulo", type="string", example="Super Mario Odyssey - Edición Deluxe"),
 *             @OA\Property(property="anio_lanzamiento", type="string", format="date", example="2017-10-27"),
 *             @OA\Property(property="genero", type="string", example="Aventura/Plataformas"),
 *             @OA\Property(
 *                 property="plataformas",
 *                 type="array",
 *                 @OA\Items(type="integer"),
 *                 example={3}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Videojuego actualizado exitosamente"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No tienes permisos"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Videojuego no encontrado"
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

    /**
 * @OA\Delete(
 *     path="/api/v1/videojuegos/{id}",
 *     tags={"Videojuegos"},
 *     summary="Eliminar videojuego (Solo Admin)",
 *     description="Elimina un videojuego. Requiere permisos de administrador",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del videojuego",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Videojuego eliminado exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Videojuego eliminado exitosamente")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No tienes permisos"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Videojuego no encontrado"
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