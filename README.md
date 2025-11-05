# 🎮 Videogames API

API REST completa para gestión de videojuegos y plataformas desarrollada con **Laravel 12** y **Laravel Passport** para autenticación OAuth 2.0.

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Tecnologías](#-tecnologías)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Estructura de la Base de Datos](#-estructura-de-la-base-de-datos)
- [Endpoints de la API](#-endpoints-de-la-api)
- [Autenticación](#-autenticación)
- [Roles y Permisos](#-roles-y-permisos)
- [Testing](#-testing)
- [Documentación con Swagger](#-documentación-con-swagger)
- [Colección de Postman](#-colección-de-postman)

---

## ✨ Características

- ✅ **Autenticación OAuth 2.0** con Laravel Passport
- ✅ **CRUD completo** de videojuegos y plataformas
- ✅ **Sistema de roles** (Admin/User)
- ✅ **Relaciones Many-to-Many** entre videojuegos y plataformas
- ✅ **Validaciones robustas** de datos
- ✅ **31 tests automatizados** (100% cobertura)
- ✅ **Documentación interactiva** con Swagger/OpenAPI
- ✅ **Colección de Postman** lista para usar
- ✅ **Seeders** para datos de prueba

---

## 🛠 Tecnologías

- **Framework:** Laravel 12.x
- **PHP:** 8.2+
- **Base de Datos:** MySQL 8.0 / MariaDB
- **Autenticación:** Laravel Passport 12.x
- **Testing:** PHPUnit
- **Documentación:** L5-Swagger (OpenAPI 3.0)
- **Validación:** Form Requests de Laravel

---

## 📦 Requisitos

- PHP >= 8.2
- Composer
- MySQL >= 8.0 o MariaDB >= 10.4
- Extensiones PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, JSON

---

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/videogames-api.git
cd videogames-api
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=videogames_api
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4. Crear la base de datos

```sql
CREATE DATABASE videogames_api;
```

### 5. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

### 6. Instalar Passport

```bash
php artisan passport:install
php artisan passport:client --personal
```

### 7. Generar documentación Swagger

```bash
php artisan l5-swagger:generate
```

### 8. Iniciar el servidor

```bash
php artisan serve
```

La API estará disponible en: **http://127.0.0.1:8000**

---

## ⚙️ Configuración

### Passport (OAuth 2.0)

El proyecto usa Laravel Passport para autenticación con tokens Bearer.

**Configuración en `config/auth.php`:**

```php
'guards' => [
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

### CORS

Configurado para permitir peticiones desde el frontend.

**`config/cors.php`:**

```php
'allowed_origins' => ['http://localhost:5173'],
```

---

## 🗄️ Estructura de la Base de Datos

### Tabla: `users`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| name | varchar(255) | Nombre del usuario |
| email | varchar(255) | Email (único) |
| password | varchar(255) | Contraseña (hasheada) |
| role | enum('admin','user') | Rol del usuario |
| email_verified_at | timestamp | Verificación email |
| created_at | timestamp | Fecha creación |
| updated_at | timestamp | Fecha actualización |

### Tabla: `videojuegos`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| titulo | varchar(255) | Título del videojuego |
| anio_lanzamiento | date | Fecha de lanzamiento |
| genero | varchar(100) | Género |
| created_at | timestamp | Fecha creación |
| updated_at | timestamp | Fecha actualización |

### Tabla: `plataformas`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| nombre | varchar(100) | Nombre de la plataforma |
| fabricante | varchar(100) | Fabricante |
| created_at | timestamp | Fecha creación |
| updated_at | timestamp | Fecha actualización |

### Tabla: `plataforma_videojuego` (Pivot)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| videojuego_id | bigint | FK a videojuegos |
| plataforma_id | bigint | FK a plataformas |

---

## 🔌 Endpoints de la API

### Base URL

```
http://127.0.0.1:8000/api/v1
```

### 🔐 Autenticación

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/login` | Iniciar sesión | No |
| POST | `/logout` | Cerrar sesión | Sí |
| GET | `/profile` | Ver perfil | Sí |

### 🎮 Videojuegos

| Método | Endpoint | Descripción | Auth | Admin |
|--------|----------|-------------|------|-------|
| GET | `/videojuegos` | Listar todos | Sí | No |
| GET | `/videojuegos/{id}` | Ver uno | Sí | No |
| GET | `/videojuegos/recientes` | Últimos 3 | Sí | No |
| POST | `/videojuegos` | Crear | Sí | **Sí** |
| PUT | `/videojuegos/{id}` | Actualizar | Sí | **Sí** |
| DELETE | `/videojuegos/{id}` | Eliminar | Sí | **Sí** |

### 🕹️ Plataformas

| Método | Endpoint | Descripción | Auth | Admin |
|--------|----------|-------------|------|-------|
| GET | `/plataformas` | Listar todas | Sí | No |
| GET | `/plataformas/{id}` | Ver una | Sí | No |
| GET | `/plataformas/mas-popular` | Más popular | Sí | No |
| POST | `/plataformas` | Crear | Sí | **Sí** |
| PUT | `/plataformas/{id}` | Actualizar | Sí | **Sí** |
| DELETE | `/plataformas/{id}` | Eliminar | Sí | **Sí** |

---

## 🔑 Autenticación

### Login

**Request:**

```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response:**

```json
{
    "message": "Login exitoso",
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "role": "admin"
    }
}
```

### Usar el Token

Incluye el token en el header de todas las peticiones autenticadas:

```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

---

## 👥 Roles y Permisos

### Usuarios Creados por Seeders

| Email | Password | Role |
|-------|----------|------|
| admin@example.com | password | admin |
| user@example.com | password | user |

### Permisos por Rol

**Admin:**
- ✅ Todos los permisos
- ✅ Crear, editar y eliminar videojuegos
- ✅ Crear, editar y eliminar plataformas

**User:**
- ✅ Ver videojuegos
- ✅ Ver plataformas
- ❌ No puede crear/editar/eliminar

### Gates Implementados

**`app/Providers/AppServiceProvider.php`:**

```php
Gate::define('admin-only', function (User $user) {
    return $user->role === 'admin';
});
```

---

## 🧪 Testing

El proyecto incluye **31 tests** que cubren todos los endpoints y casos de uso.

### Ejecutar todos los tests

```bash
php artisan test
```

### Ejecutar tests con cobertura

```bash
php artisan test --coverage
```

### Tests Incluidos

#### AuthTest (8 tests)
- ✅ Login exitoso
- ✅ Login con credenciales incorrectas
- ✅ Login con campos faltantes
- ✅ Logout exitoso
- ✅ Logout sin autenticación
- ✅ Ver perfil
- ✅ Ver perfil sin autenticación
- ✅ Token inválido

#### VideojuegoTest (12 tests)
- ✅ Listar videojuegos
- ✅ Ver videojuego por ID
- ✅ Ver videojuego inexistente
- ✅ Ver videojuegos recientes
- ✅ Crear videojuego (admin)
- ✅ Crear videojuego sin permisos
- ✅ Actualizar videojuego (admin)
- ✅ Actualizar videojuego sin permisos
- ✅ Eliminar videojuego (admin)
- ✅ Eliminar videojuego sin permisos
- ✅ Validaciones de campos
- ✅ Relaciones con plataformas

#### PlataformaTest (11 tests)
- ✅ Listar plataformas
- ✅ Ver plataforma por ID
- ✅ Ver plataforma inexistente
- ✅ Ver plataforma más popular
- ✅ Crear plataforma (admin)
- ✅ Crear plataforma sin permisos
- ✅ Actualizar plataforma (admin)
- ✅ Actualizar plataforma sin permisos
- ✅ Eliminar plataforma (admin)
- ✅ Eliminar plataforma sin permisos
- ✅ Validaciones de campos

### Resultado Esperado

```
PASS  Tests\Feature\AuthTest
✓ login exitoso                              0.50s
✓ login con credenciales incorrectas         0.05s
...

Tests:    31 passed (156 assertions)
Duration: 2.35s
```

---

## 📚 Documentación con Swagger

La API cuenta con documentación interactiva generada con **L5-Swagger**.

### Acceder a la Documentación

```
http://127.0.0.1:8000/api/documentation
```

### Características de Swagger

- ✅ Interfaz interactiva
- ✅ Prueba endpoints desde el navegador
- ✅ Autenticación Bearer integrada
- ✅ Ejemplos de request/response
- ✅ Esquemas de datos
- ✅ Validaciones documentadas

### Regenerar Documentación

```bash
php artisan l5-swagger:generate
```

### Usar Swagger

1. Abre `http://127.0.0.1:8000/api/documentation`
2. Haz login en tu API para obtener un token
3. Click en **"Authorize"** (botón con candado)
4. Ingresa: `Bearer TU_TOKEN_AQUI`
5. Click en **"Authorize"**
6. Prueba cualquier endpoint

---

## 📮 Colección de Postman

El proyecto incluye una colección completa de Postman con todos los endpoints.

### Importar en Postman

1. Abre Postman
2. Click en **"Import"**
3. Selecciona `Videogames_API.postman_collection.json`
4. La colección se importará automáticamente

### Características

- ✅ 15 requests pre-configurados
- ✅ Variables de entorno (base_url, access_token)
- ✅ Auto-guardado de token al hacer login
- ✅ Tests automáticos en cada request
- ✅ Ejemplos de request/response

### Variables de Entorno

| Variable | Valor | Descripción |
|----------|-------|-------------|
| `base_url` | `http://127.0.0.1:8000` | URL base de la API |
| `access_token` | (auto) | Token Bearer |

### Uso Rápido

1. Ejecuta el request **"Login"** en la carpeta "Authentication"
2. El token se guarda automáticamente
3. Todos los demás requests usan el token guardado
4. ¡Listo para probar!

---

## 📁 Estructura del Proyecto

```
videogames-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── VideojuegoController.php
│   │   │       └── PlataformaController.php
│   │   └── Requests/
│   │       ├── StoreVideojuegoRequest.php
│   │       ├── UpdateVideojuegoRequest.php
│   │       ├── StorePlataformaRequest.php
│   │       └── UpdatePlataformaRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Videojuego.php
│   │   └── Plataforma.php
│   └── Providers/
│       └── AppServiceProvider.php (Gates)
├── database/
│   ├── migrations/
│   │   ├── 2024_10_26_000001_create_videojuegos_table.php
│   │   ├── 2024_10_26_000002_create_plataformas_table.php
│   │   └── 2024_10_26_000003_create_plataforma_videojuego_table.php
│   └── seeders/
│       ├── UserSeeder.php
│       ├── PlataformaSeeder.php
│       └── VideojuegoSeeder.php
├── routes/
│   └── api.php
├── tests/
│   └── Feature/
│       ├── AuthTest.php
│       ├── VideojuegoTest.php
│       └── PlataformaTest.php
├── storage/
│   └── api-docs/
│       └── api-docs.json (Swagger)
└── Videogames_API.postman_collection.json
```

---

## 🔄 Comandos Útiles

### Limpiar y Resetear

```bash
# Resetear base de datos con datos frescos
php artisan migrate:fresh --seed

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Reinstalar Passport
php artisan passport:install
php artisan passport:client --personal
```

### Testing

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar un test específico
php artisan test --filter=AuthTest

# Ver cobertura
php artisan test --coverage
```

### Documentación

```bash
# Regenerar Swagger
php artisan l5-swagger:generate

# Ver rutas de la API
php artisan route:list --path=api
```

---

## 📊 Datos de Prueba (Seeders)

### Usuarios

- **Admin:** admin@example.com / password
- **User:** user@example.com / password

### Plataformas (6)

1. PlayStation 5 (Sony)
2. Xbox Series X (Microsoft)
3. Nintendo Switch (Nintendo)
4. PC (Varios)
5. PlayStation 4 (Sony)
6. Xbox One (Microsoft)

### Videojuegos (5)

1. The Legend of Zelda: Breath of the Wild (2017) - Aventura
2. God of War (2018) - Acción
3. Elden Ring (2022) - RPG
4. Cyberpunk 2077 (2020) - RPG
5. The Last of Us Part II (2020) - Aventura

---

## 🐛 Solución de Problemas

### Error: "Personal access client not found"

```bash
php artisan passport:client --personal
```

### Error: "SQLSTATE[HY000] [1045] Access denied"

Verifica tus credenciales en `.env`:

```env
DB_USERNAME=root
DB_PASSWORD=tu_password_correcta
```

### Error: "Class 'Laravel\Passport\PassportServiceProvider' not found"

```bash
composer require laravel/passport
```

### Tests fallan

```bash
# Resetear base de datos de testing
php artisan migrate:fresh --seed --env=testing
```

---

## 📝 Notas Técnicas

### Validaciones

Todas las validaciones se manejan con **Form Requests** personalizados:

- `StoreVideojuegoRequest`: Validación para crear videojuegos
- `UpdateVideojuegoRequest`: Validación para actualizar videojuegos
- `StorePlataformaRequest`: Validación para crear plataformas
- `UpdatePlataformaRequest`: Validación para actualizar plataformas

### Relaciones Eloquent

**Videojuego → Plataformas (Many to Many)**

```php
public function plataformas()
{
    return $this->belongsToMany(Plataforma::class, 'plataforma_videojuego');
}
```

**Plataforma → Videojuegos (Many to Many)**

```php
public function videojuegos()
{
    return $this->belongsToMany(Videojuego::class, 'plataforma_videojuego');
}
```

### Autenticación en Controladores

```php
// Verificar permisos de admin
if (Gate::denies('admin-only')) {
    return response()->json([
        'message' => 'No tienes permisos para realizar esta acción'
    ], 403);
}
```

---


## 📄 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

---

## 👨‍💻 Autor

Francesc Humbert

---
