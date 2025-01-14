<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:3|confirmed',
        ]);

        // Validar que al menos se proporcione email o teléfono
        if (!$request->has('email') && !$request->has('phone')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debe proporcionar un email o un teléfono',
            ], 400);
        }

        // Validar email o teléfono si se proporcionan
        if ($request->has('email')) {
            $request->validate(['email' => 'required|string|email|max:255|unique:users']);
        }
        if ($request->has('phone')) {
            $request->validate(['phone' => 'required|string|unique:users']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email ?? null,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role' => 'customer'
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'Usuario creado exitosamente',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function loginWithEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Buscar el usuario primero para debug
        $user = User::where('email', $request->email)->first();
        \Log::info('Login attempt:', [
            'email' => $request->email,
            'user_found' => $user ? true : false,
            'user_id' => $user ? $user->id : null
        ]);

        if (!$token = auth()->attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales inválidas',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => auth()->user(),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function loginWithPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['phone', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales inválidas',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => auth()->user(),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function completeRegistration(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user_id,
            'password' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        // Verificar que el usuario no esté ya registrado
        if ($user->is_registered) {
            return response()->json([
                'status' => 'error',
                'message' => 'Este usuario ya está registrado completamente'
            ], 400);
        }

        // Completar el registro
        $user->update([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_registered' => true,
            'registration_completed_at' => Carbon::now()
        ]);

        $token = Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Registro completado con éxito',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
