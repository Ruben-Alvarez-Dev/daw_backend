<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'customer') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Customer access required.'
            ], 403);
        }

        // Restricciones para customers
        if ($request->is('api/v1/users/*')) {
            // Solo puede editar su propio perfil
            $userId = $request->route('user')->id;
            if ($userId != auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You can only edit your own profile.'
                ], 403);
            }

            // No puede cambiar el role
            if ($request->has('role')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You cannot change user roles.'
                ], 403);
            }
        }

        // Restricciones para reservas
        if ($request->is('api/v1/reservations')) {
            if ($request->isMethod('post')) {
                // Solo permitir datetime y guests en nuevas reservas
                $allowedFields = ['datetime', 'guests'];
                $inputFields = array_keys($request->all());
                $invalidFields = array_diff($inputFields, $allowedFields);

                if (!empty($invalidFields)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Only datetime and guests fields are allowed for customers.',
                        'invalid_fields' => $invalidFields
                    ], 403);
                }
            } elseif ($request->isMethod('patch') || $request->isMethod('put')) {
                // No permitir modificar reservas existentes
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customers cannot modify existing reservations.'
                ], 403);
            }
        }

        // No permitir acceso a la gestión de mesas
        if ($request->is('api/v1/tables*')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tables management is restricted to administrators.'
            ], 403);
        }

        return $next($request);
    }
}
