<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Videogames API",
 *     description="API REST para gestión de videojuegos y plataformas con autenticación OAuth 2.0",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Servidor Local de Desarrollo"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Ingresa tu token Bearer obtenido del endpoint de login"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints de autenticación y gestión de sesión"
 * )
 * 
 * @OA\Tag(
 *     name="Videojuegos",
 *     description="Endpoints para gestión de videojuegos (CRUD completo)"
 * )
 * 
 * @OA\Tag(
 *     name="Plataformas",
 *     description="Endpoints para gestión de plataformas de videojuegos"
 * )
 */
abstract class Controller
{
    //
}