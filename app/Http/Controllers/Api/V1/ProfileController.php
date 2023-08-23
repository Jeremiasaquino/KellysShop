<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Acceso al perfil del usuario',
            'user' => $request->user(),
        ], 200);
    }

    /**
     * Actualizar el perfil del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error en las validaciones.',
                'errors' => $e->errors(),
            ], 422); // Código de estado "Unprocessable Entity"
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocurrió un error en el servidor.',
            ], 500); // Código de estado "Internal Server Error"
        }
    }

    /**
     * Actualizar la contraseña del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'error' => true,
                    'message' => 'La contraseña actual no coincide.',
                ], 400); // Código de estado "Bad Request"
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error en las validaciones.',
                'errors' => $e->errors(),
            ], 422); // Código de estado "Unprocessable Entity"
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocurrió un error en el servidor.',
            ], 500); // Código de estado "Internal Server Error"
        }
    }
}
