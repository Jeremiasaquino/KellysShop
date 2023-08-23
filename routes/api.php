<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PermisoController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RolePermissionController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

// Ruta para iniciar sesión y obtener un token
Route::post('login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('throttle:6,1'); // Límite de 6 intentos por minuto


// Ruta para cerrar sesión y revocar tokens
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Otras Rutas de la API
|--------------------------------------------------------------------------
*/

/// Rutas de usuarios (UserController)
Route::middleware('auth:sanctum')->prefix('usuarios')->group(function () {
    // Ruta para listar usuarios
    Route::get('/', [UserController::class, 'index'])->middleware('can:viewAny,App\Models\User');
    // Ruta para crear un nuevo usuario
    Route::post('/', [UserController::class, 'store'])->middleware('can:create,App\Models\User');
    // Ruta para ver la información de un usuario específico
    Route::get('/{user}', [UserController::class, 'show'])->middleware('can:view,user');
    // Ruta para actualizar los datos de un usuario específico
    Route::put('/{user}', [UserController::class, 'update'])->middleware('can:update,user');
    // Ruta para eliminar un usuario específico
    Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('can:delete,user');
});

/// Rutas de perfil (ProfileController)
Route::middleware('auth:sanctum')->prefix('perfil')->group(function () {
    // Ruta para obtener información del usuario autenticado
    Route::get('/me', [ProfileController::class, 'show']);
    // Ruta para actualizar el perfil del usuario
    Route::patch('/actualizar', [ProfileController::class, 'update']);
    // Ruta para cambiar la contraseña del usuario
    Route::patch('/actualizar-contrasena', [ProfileController::class, 'updatePassword']);
});

// Rutas de roles  (RoleController)
Route::middleware('auth:sanctum', 'role:Administrador')->prefix('roles')->group(function () {
    // Ruta para obtener información de los roles
    Route::get('/', [RoleController::class, 'index']);
    // Ruta para crear un nuevo rol
    Route::post('/', [RoleController::class, 'store']);
    // Ruta para actualizar un rol existente
    Route::put('/{id}', [RoleController::class, 'update']);
    // Ruta para obtener información del rol existente
    Route::get('/{id}', [RoleController::class, 'show']);
    // Ruta para eliminar al rol
    Route::delete('/{id}', [RoleController::class, 'destroy']);
    // Ruta para asignar un rol a un usuario
    Route::post('/{userId}', [RoleController::class, 'assignRoleToUser']);
});

// Rutas de permisos (PermisoController)
Route::middleware('auth:sanctum')->prefix('permisos')->group(function () {
    // Ruta para obtener información de los permisos
    Route::get('/', [PermisoController::class, 'index']);
    // Ruta para crear un nuevo permiso
    Route::post('/', [PermisoController::class, 'store']);
    // Ruta para actualizar un permiso existente
    Route::put('/{id}', [PermisoController::class, 'update']);
    // Ruta para obtener información del permiso existente
    Route::get('/{id}', [PermisoController::class, 'show']);
    // Ruta para eliminar al permiso
    Route::delete('/{id}', [PermisoController::class, 'destroy']);
    // Ruta para asignar un rol a un permiso
    Route::post('/{roleId}', [PermisoController::class, 'assignPermissionToRole']);
});



/*// Ejemplo de ruta protegida
Route::middleware('auth.user')->get('perfil', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Acceso al perfil del usuario',
        'user' => $request->user(),
    ], 200);
})->name('api.perfil');*/
