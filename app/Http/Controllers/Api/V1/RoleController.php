<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $roles = Role::all();

            return response()->json([
                'success' => true,
                'roles' => $roles,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos del rol
            $request->validate([
                'name' => 'required|unique:roles,name',
            ]);

            // Crear el rol
            $role = Role::create(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Rol creado exitosamente.',
                'role' => $role,
            ], 201);
        } catch (ValidationException $e) {
            // Manejar errores de validación
            return response()->json([
                'error' => true,
                'message' => 'Error en las validaciones.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejar otros errores
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Role = Role::findOrFail($id);

            return response()->json([
                'success' => true,
                'Rol' => $Role,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Rol no encontrado.',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Encontrar el rol a actualizar
            $role = Role::findOrFail($id);

            // Validar el nuevo nombre del rol
            $request->validate([
                'name' => 'required|unique:roles,name,' . $role->id,
            ]);

            // Actualizar el nombre del rol
            $role->update(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado exitosamente.',
                'role' => $role,
            ]);
        } catch (ValidationException $e) {
            // Manejar errores de validación
            return response()->json([
                'error' => true,
                'message' => 'Error en las validaciones.',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => 'El rol no fue encontrado.',
            ], 404);
        } catch (\Exception $e) {
            // Manejar otros errores
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Obtener el rol que se va a eliminar
            $role = Role::findOrFail($id);

            // Verificar si el rol tiene permisos asignados
            if (!$role->permissions->isEmpty()) {
                return response()->json([
                    'error' => true,
                    'message' => 'No se puede eliminar el rol porque tiene permisos asignados.',
                ], 403); // Código de estado "Forbidden"
            }

            // Realizar cualquier otra lógica de eliminación aquí
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado exitosamente.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => 'El rol no fue encontrado.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Asignar un rol a un usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRoleToUser(Request $request, $userId)
    {
        try {
            // Encontrar el usuario al que se asignará el rol
            $user = User::findOrFail($userId);

            // Validar el nombre del rol
            $request->validate([
                'role_name' => 'required|exists:roles,name',
            ]);

            // Encontrar el rol
            $role = Role::where('name', $request->role_name)->firstOrFail();

            // Verificar si el rol tiene permisos asociados
            if ($role->permissions->isEmpty()) {
                return response()->json([
                    'error' => true,
                    'message' => 'El rol no tiene permisos asociados y no puede ser asignado al usuario.',
                ], 422);
            }

            // Asignar el rol al usuario
            $user->assignRole($role);

            return response()->json([
                'success' => true,
                'message' => 'Rol asignado al usuario exitosamente.',
                'user' => $user,
            ]);
        } catch (ValidationException $e) {
            // Manejar errores de validación
            return response()->json([
                'error' => true,
                'message' => 'Error en las validaciones.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejar otros errores
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.' . $e->getMessage(),
            ], 500);
        }
    }
}
