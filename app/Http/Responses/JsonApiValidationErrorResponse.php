<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class JsonApiValidationErrorResponse extends JsonResponse
{
    public function __construct(ValidationException $exception, $status = 422)
    {
        $data = $this->formatResponse($exception);

        $headers = [
            'content-type' => 'application/vnd.api+json'
        ];

        parent::__construct($data, $status, $headers);
    }

    public function formatResponse(ValidationException $exception): array
    {
        return [
            'errors' => collect($exception->errors())
                ->map(function ($message, $field) {
                    return [
                        'title' => __('The given data was invalid'),
                        'detail' => $message[0],
                        'source' => [
                            'pointer' => '/' . Str::replace('.', '/', $field),
                        ],
                    ];
                })->values()
        ];
    }
}
