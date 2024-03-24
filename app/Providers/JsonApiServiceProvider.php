<?php

namespace App\Providers;

use Closure;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        TestResponse::macro(
            'assertJsonApiValidationErrors',
            $this->AssertJsonApiValidationsErrors()
        );
    }

    public function AssertJsonApiValidationsErrors(): Closure
    {
        return function (string $attribute) {

            /** @var TestResponse $this   */

            $pointer = Str::of($attribute)->startsWith('data')
                ? '/' . \str_replace('.', '/', $attribute)
                : "/data/attributes/{$attribute}";

            try {
                $this->assertJsonFragment([
                    'source' =>  [
                        'pointer' => $pointer
                    ]
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail("Failed to find a JSON:API validation error for key:'{$attribute}'" . PHP_EOL . PHP_EOL . $e->getMessage());
            }

            try {
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']]
                    ]
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail("Failed to find a valid JSON:API error response'");
            }

            $this->assertStatus(422)
                ->assertHeader('content-type', 'application/vnd.api+json');
        };
    }
}
