<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

use App\Exceptions\ApiExceptionHandler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            // Get the exception class name
            $className = get_class($e);

            // Get our custom handlers
            $handlers = ApiExceptionHandler::$handlers;

            $apiHandler = new ApiExceptionHandler();

            // Check if we have a specific handler for this exception
            if (array_key_exists($className, $handlers)) {
                $method = $handlers[$className];
                return $apiHandler->$method($e, $request);
            }

            // Fallback to default error response
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            $message = $e->getMessage() ?: 'An unexpected API error occurred.';

            $response = [
                'success' => false,
                'message' => $message,
            ];
            return response()->json($response, $statusCode);
        });
    })
    ->create();
