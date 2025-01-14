<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'customer'
        ]);
        
        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return $user;
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }

    public function createSimpleUser(Request $request)
    {
        // Verificar que el usuario es admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users'
        ]);

        // Usar el teléfono como contraseña inicial, pero hasheada
        $password = Hash::make($request->phone);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => $password,
            'role' => 'customer'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario creado correctamente',
            'user' => $user
        ]);
    }

    public function search(Request $request)
    {
        $term = $request->query('term');
        
        return User::where('name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->get();
    }
}
