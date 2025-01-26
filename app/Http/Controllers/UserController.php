<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if (!$request->show_inactive) {
            $query->active();
        }
        
        $users = $query->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                function ($attribute, $value, $fail) {
                    if ($value && User::uniqueActive('email', $value)->exists()) {
                        $fail('El email ya está en uso por un usuario activo.');
                    }
                }
            ],
            'phone' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value && User::uniqueActive('phone', $value)->exists()) {
                        $fail('El teléfono ya está en uso por un usuario activo.');
                    }
                }
            ],
            'password' => 'nullable|string',
            'role' => 'required|in:admin,customer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);
        
        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return $user;
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'nullable',
                'email',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && User::uniqueActive('email', $value, $user->id)->exists()) {
                        $fail('El email ya está en uso por un usuario activo.');
                    }
                }
            ],
            'phone' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && User::uniqueActive('phone', $value, $user->id)->exists()) {
                        $fail('El teléfono ya está en uso por un usuario activo.');
                    }
                }
            ],
            'password' => 'nullable|string',
            'role' => 'sometimes|required|in:admin,customer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('password')) {
            $request->merge([
                'password' => Hash::make($request->password)
            ]);
        }

        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        // Verificar si el usuario tiene reservas futuras
        $hasFutureReservations = $user->reservations()
            ->where('datetime', '>', now())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();

        if ($hasFutureReservations) {
            return response()->json([
                'message' => 'No se puede desactivar un usuario con reservas futuras'
            ], 422);
        }

        $user->update(['active_until' => now()]);
        return response()->json([
            'message' => 'Usuario desactivado correctamente',
            'user' => $user
        ]);
    }

    public function createSimpleUser(Request $request)
    {
        // Verificar que el usuario es admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (User::uniqueActive('phone', $value)->exists()) {
                        $fail('El teléfono ya está en uso por un usuario activo.');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => 'customer'
        ]);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user' => $user
        ], 201);
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
