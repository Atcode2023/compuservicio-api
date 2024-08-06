<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;

class ApiExceptionHandler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // Verificar si la excepción es una instancia de una excepción de validación
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
                'errors'  => $exception->errors()
            ], 422);
        }

        // Verificar si la excepción es una instancia de una excepción de modelo no encontrado
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return new JsonResponse([
                'message' => 'El recurso solicitado no se pudo encontrar.'
            ], 404);
        }

        // Devolver una respuesta JSON genérica para otras excepciones
        return new JsonResponse([
            'message' => 'Ocurrió un error interno en el servidor.'
        ], 500);
    }
}
