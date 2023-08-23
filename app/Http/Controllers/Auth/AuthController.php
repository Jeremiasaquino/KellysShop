<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class AuthController extends Controller
{
    /**
     * Iniciar sesión y generar token de acceso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validar los datos de inicio de sesión
            $request->validate([
                'email' => 'required|email', // El email es obligatorio y debe tener un formato válido
                'password' => 'required',    // La contraseña es obligatoria
                'device_name' => 'required', // El nombre del dispositivo es obligatorio
            ], [
                // Mensajes personalizados para las validaciones
                'email.required' => 'El campo email es obligatorio.',
                'email.email' => 'Por favor, ingresa un email válido.',
                'password.required' => 'El campo contraseña es obligatorio.',
                'device_name.required' => 'El nombre del dispositivo es obligatorio.',
            ]);

            // Verificar si el correo está registrado
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['El correo proporcionado no está registrado.'],
                ]);
            }

            // Verificar las credenciales
            if (!Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }

            /// Verificar si el usuario está inactivo o eliminado
            if ($user->estado === 'inactivo' || $user->trashed()) {
                $userName = $user->name; // Obtener el nombre del usuario
                $errorMessage = $user->estado === 'inactivo' ? "$userName, estás inactivo. Ponte en contacto con el administrador." : "$userName, no puedes iniciar sesión porque has sido eliminado.";
                throw ValidationException::withMessages([
                    'email' => [$errorMessage],
                ]);
            }


            // Generar y retornar el token de acceso
            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'token' => $user->createToken($request->device_name)->plainTextToken,
                'usuario' => $user,
            ], 200);
        } catch (ThrottleRequestsException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Demasiados intentos de inicio de sesión. Por favor, intenta nuevamente en :seconds segundos.',
            ], 429); // Código de estado "Too Many Requests"
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cerrar sesión y revocar tokens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Obtener al usuario autenticado
            $user = Auth::user();

            // Revocar todos los tokens del usuario
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            // Respuesta de éxito
            return response()->json([
                'success' => true,
                'message' => 'Cierre de sesión exitoso',
            ], 200);
        } catch (\Exception $e) {
            // Respuesta de error en caso de excepción
            return response()->json([
                'error' => true,
                'message' => 'Ha ocurrido un error en el servidor',
            ], 500);
        }
    }
}
