<?php
namespace App\Exceptions;

use Throwable;
use App\Http\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LogLevel;

class ApiExceptionHandler
{
    use ApiResponse;

    /**
     * Map of exception classes to their handler methods
     */
    public static array $handlers = [
        ValidationException::class => 'handleValidationException',
        ModelNotFoundException::class => 'handleNotFoundException',
        NotFoundHttpException::class => 'handleNotFoundException',
        MethodNotAllowedHttpException::class => 'handleMethodNotAllowedException',
        HttpException::class => 'handleHttpException'
    ];


    /**
     * Handle validation exceptions
     */
    public function handleValidationException(
        ValidationException $e,
        Request $request
    ): JsonResponse {
        $errors = [];
        foreach ($e->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message,
                ];
            }
        }

        $this->logException($e, 'Validation failed', ['errors' => $errors], LogLevel::INFO);

        return $this->validationErrorResponse(
            $errors,
            $e->getMessage()
        );
    }

    /**
     * Handle not found exceptions (Model or general route)
     */
    public function handleNotFoundException(
        ModelNotFoundException|NotFoundHttpException $e,
        Request $request
    ): JsonResponse {
        $this->logException($e, 'Resource not found', [], LogLevel::NOTICE);
        $message = $e instanceof ModelNotFoundException
            ? 'The requested resource was not found.'
            : "The requested endpoint '{$request->getRequestUri()}' was not found.";

        return $this->notFoundResponse(
            $message
        );
    }

    /**
     * Handle method not allowed exceptions
     */
    public function handleMethodNotAllowedException(
        MethodNotAllowedHttpException $e,
        Request $request
    ): JsonResponse {
        $this->logException($e, 'Method not allowed', [], LogLevel::WARNING);

        $message = "The {$request->method()} method is not allowed for this endpoint.";

        if (!empty($e->getHeaders()['Allow'])) {
            $message .= ' Allowed methods: ' . $e->getHeaders()['Allow'] . '.';
        }

        return $this->errorResponse(
            $message,
            405
        );
    }

    /**
     * Handle general HTTP exceptions
     */
    public function handleHttpException(HttpException $e, Request $request): JsonResponse
    {
        $this->logException($e, 'HTTP exception occurred', [], LogLevel::WARNING);

        return $this->errorResponse(
            $e->getMessage() ?: 'An HTTP error occurred.',
            $e->getStatusCode()
        );
    }

    /**
     * Log exception with context and specified level
     * This helper remains here as it's not part of the ApiResponse trait's public interface.
     */
    private function logException(Throwable $e, string $message, array $context = [], string $level = LogLevel::ERROR): void
    {
        $logContext = array_merge([
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
        ], $context);

        Log::log($level, $message, $logContext);
    }
}
