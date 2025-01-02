<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        
        $response = $next($request);
        
        // Si la respuesta ya es JSON, la devolvemos tal cual
        if ($response instanceof JsonResponse) {
            return $response;
        }
        
        // Si es una respuesta normal, la convertimos a JSON
        if ($response instanceof Response) {
            $content = $response->getContent();
            
            // Intentamos decodificar por si ya es JSON
            $json = json_decode($content);
            if (json_last_error() === JSON_ERROR_NONE) {
                return new JsonResponse($json, $response->getStatusCode());
            }
            
            // Si no es JSON, lo convertimos
            return new JsonResponse(
                ['message' => $content],
                $response->getStatusCode(),
                $response->headers->all()
            );
        }
        
        return $response;
    }
}
