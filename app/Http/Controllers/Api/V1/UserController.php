<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Obtener la lista de usuarios.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $users = User::all();

            return response()->json([
                'success' => true,
                'users' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }

    /**
     * Crear un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos del usuario
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
            ]);

            // Crear el nuevo usuario
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->estado = 'activo'; // Establecer el estado por defecto
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente.',
                'user' => $user,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error en las validaciones.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }

    /**
     * Mostrar los detalles de un usuario específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Usuario no encontrado.',
            ], 404);
        }
    }

    /**
     * Actualizar los datos de un usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Validar los datos de actualización
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            // Actualizar los datos
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Datos de usuario actualizados exitosamente.',
                'user' => $user,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => 'El Usuario no fue encontrado.',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error en las validaciones.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }

    /**
     * Cambiar la contraseña de un usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Validar los datos de cambio de contraseña
            $request->validate([
                'password' => 'required|min:6|confirmed', // Agregar la validación de confirmación
            ]);

            // Cambiar la contraseña
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Contraseña de usuario cambiada exitosamente.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error en las validaciones.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }

    /**
     * Cambiar el estado de un usuario (activo/inactivo).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Validar el estado
            $request->validate([
                'estado' => 'required|in:activo,inactivo',
            ]);

            // Cambiar el estado
            $user->estado = $request->estado;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado de usuario cambiado exitosamente.',
                'user' => $user,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error en las validaciones.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }

    /**
     * Eliminar un usuario.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Verificar si el usuario tiene permiso para ser eliminado
            if (!$user->canBeDeleted()) {
                return response()->json([
                    'error' => true,
                    'message' => 'El usuario no puede ser eliminado debido a sus permisos o roles.',
                ], 422);
            }

            // Eliminar el usuario
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => 'El Usuario no fue encontrado.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }
}
