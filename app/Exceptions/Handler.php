<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        /* foreach version
             $errors = $errors
            foreach ($exception->errors() as $field => $message) {
                $pointer = '/' . Str::replace('.', '/', $field);
                $errors[] =  [
                    'title' =>  __('The given data was invalid'),
                    'detail' => $message[0],
                    'source' => [
                        'pointer' => $pointer
                    ]
                ];
            };
         */

        return response()->json([
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
        ], 422, [
            'content-type' => 'application/vnd.api+json'
        ]);
    }
}
