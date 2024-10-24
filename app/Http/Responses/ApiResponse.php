<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class ApiResponse implements Responsable
{
    protected int $httpCode;

    protected array $data;

    protected string $message;

    protected bool $success;

    protected array $invalidFields;

    public function __construct(int $httpCode, array $data = [], string $message = '', bool $success = false, array $invalidFields = [])
    {
        $this->httpCode = $httpCode;
        $this->data = $data;
        $this->message = $message;
        $this->success = $success;
        $this->invalidFields = $invalidFields;
    }

    public function toResponse($request): JsonResponse
    {
        $payload = match (true) {
            $this->httpCode >= 500 => ['message' => 'internalServerError', 'success' => false],
            $this->httpCode >= 400 => ['data' => $this->data, 'message' => $this->message, 'success' => false, 'invalidFields' => $this->invalidFields],
            $this->httpCode >= 200 => ['data' => $this->data, 'success' => true],
        };

        return response()->json($payload, $this->httpCode, [], JSON_UNESCAPED_UNICODE);
    }

    public static function ok(array $data = [])
    {
        return new static(200, $data);
    }

    public static function created(array $data)
    {
        return new static(201, $data);
    }

    public static function notFound(string $message = 'entityNotFound')
    {
        return new static(404, message: $message);
    }

    public static function badRequest(string $message = 'validationException', array $invalidFields = [])
    {
        return new static(400, message: $message, invalidFields: $invalidFields);
    }

    public static function conflict(string $message = 'resourceAlreadyExists')
    {
        return new static(409, message: $message);
    }

    public static function unauthenticated(string $message = 'unauthenticated')
    {
        return new static(401, message: $message);
    }

    public static function forbidden(string $message = 'accessDenied')
    {
        return new static(403, message: $message);
    }
}
