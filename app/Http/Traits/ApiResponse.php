<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use App\Exceptions\ApiExceptionHandler;

trait ApiResponse
{

  /**
   * Return a success JSON response.
   *
   * @param mixed $data The data to be returned.
   * @param string $message A descriptive message for the response.
   * @param int $code The HTTP status code.
   * @return JsonResponse
   */
  protected function successResponse(mixed $data = [], string $message = 'Operation successful', int $code = 200): JsonResponse
  {
      return response()->json([
          'success' => true,
          'message' => $message,
          'data' => $data,
      ], $code);
  }

  /**
   * Return a success JSON response with Pagination.
   *
   * @param mixed $data The data to be returned.
   * @param string $message A descriptive message for the response.
   * @param int $code The HTTP status code.
   * @return JsonResponse
   */
  protected function successPaginatedResponse(mixed $data = [], string $message = 'Operation successful', int $code = 200): JsonResponse
  {
      return response()->json([
         'success' => true,
         'message' => $message,
         'data' => $data->items(),
         'meta' => [
             'current_page' => $data->currentPage(),
             'last_page' => $data->lastPage(),
             'per_page' => $data->perPage(),
             'total' => $data->total(),
         ],
         'links' => [
             'first' => $data->url(1),
             'last' => $data->url($data->lastPage()),
             'prev' => $data->previousPageUrl(),
             'next' => $data->nextPageUrl(),
         ]
     ], $code);
  }

  /**
   * Return an error JSON response.
   *
   * @param string $message A descriptive message for the error.
   * @param int $code The HTTP status code.
   * @param mixed $errorDetails Optional detailed error information (e.g., validation errors).
   * @return JsonResponse
   */
  protected function errorResponse(string $message = 'An error occurred', int $code = 500, mixed $errorDetails = null): JsonResponse
  {
      $response = [
          'success' => false,
          'message' => $message,
      ];

      if ($errorDetails) {
          $response['errors'] = $errorDetails;
      }

      return response()->json($response, $code);
  }

  /**
   * Return a "Not Found" error response (404).
   *
   * @param string $message A descriptive message for the not found error.
   * @return JsonResponse
   */
  protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
  {
      return $this->errorResponse($message, 404);
  }

  /**
   * Return a "Validation Error" response (422).
   *
   * @param mixed $errors The validation errors.
   * @param string $message A descriptive message for the validation error.
   * @return JsonResponse
   */
  protected function validationErrorResponse(mixed $errors, string $message = 'Validation failed'): JsonResponse
  {
      return $this->errorResponse($message, 422, $errors);
  }


}
