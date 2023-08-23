<?php

namespace App\Exceptions;

use Throwable;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedException) {
            return response()->json(['message' => 'Página no autorizada. Póngase en contacto con el administrador.'], 403);
        } elseif ($exception instanceof AccessDeniedHttpException) {
            return response()->json(['message' => 'Acceso no autorizado.'], 403);
        }
    
        return parent::render($request, $exception);
    }


}
