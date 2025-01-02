<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                // Suppress deprecation warnings in API responses
                if ($e instanceof \ErrorException && str_contains($e->getMessage(), 'Deprecated')) {
                    return;
                }

                $status = $e instanceof HttpException ? $e->getStatusCode() : 500;
                
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], $status);
            }
        });
    }
}
