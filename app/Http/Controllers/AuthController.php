<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validar el nombre siempre
        $request->validate([
            'name' => 'required|string'
        ]);

        // Si hay email, validar email
        if ($request->filled('email')) {
            $request->validate([
                'email' => 'email|unique:users',
                'password' => 'required|string|confirmed'
            ]);
        }
        // Si hay phone, validar phone
        else if ($request->filled('phone')) {
            $request->validate([
                'phone' => 'string|unique:users',
                'password' => 'required|string|confirmed'
            ]);
        }
        // Si no hay ninguno, error
        else {
            return response()->json([
                'message' => 'Se requiere email o teléfono',
                'errors' => ['auth' => ['Debes proporcionar un email o teléfono']]
            ], 422);
        }

        $userData = array_filter([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer'
        ]);

        $user = User::create($userData);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'Usuario creado correctamente',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email|string',
            'password' => 'required|string',
        ]);

        // Determinar si el usuario está intentando entrar con email o teléfono
        $credentials = [];
        if ($request->has('email')) {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
        } else {
            $credentials = [
                'phone' => $request->phone,
                'password' => $request->password
            ];
        }

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales inválidas',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
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
