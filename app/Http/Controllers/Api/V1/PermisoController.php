<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $Permission = Permission::all();

            return response()->json([
                'success' => true,
                'Permisos' => $Permission,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }

    /**
     *  Crear un nuevo permiso.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos del permiso
            $request->validate([
                'name' => 'required|unique:permissions,name',
            ]);

            // Crear el permiso
            $permission = Permission::create(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso creado exitosamente.',
                'permission' => $permission,
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
            $Permission = Permission::findOrFail($id);

            return response()->json([
                'success' => true,
                'permiso' => $Permission,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Permiso no encontrado.',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Encontrar el Permission a actualizar
            $Permission = Permission::findOrFail($id);

            // Validar el nuevo nombre del Permission
            $request->validate([
                'name' => 'required|unique:permissions,name,' . $Permission->id,
            ]);

            // Actualizar el nombre del rol
            $Permission->update(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso actualizado exitosamente.',
                'Permiso' => $Permission,
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
                'message' => 'El Permiso no fue encontrado.',
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
        //
    }

    /**
     * Asignar un permiso a un rol.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPermissionToRole(Request $request, $roleId)
    {
        try {
            // Encontrar el rol al que se asignará el permiso
            $role = Role::findOrFail($roleId);

            // Validar el nombre del permiso
            $request->validate([
                'permission_name' => 'required|exists:permissions,name',
            ]);

            // Encontrar el permiso
            $permission = Permission::where('name', $request->permission_name)->firstOrFail();

            // Asignar el permiso al rol
            $role->givePermissionTo($permission);

            return response()->json([
                'success' => true,
                'message' => 'Permiso asignado al rol exitosamente.',
                'role' => $role,
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
                'message' => 'Ha ocurrido un error en el servidor.',
            ], 500);
        }
    }
}
