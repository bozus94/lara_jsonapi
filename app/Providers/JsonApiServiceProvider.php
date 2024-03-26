<?php

namespace App\Providers;

use Closure;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Illuminate\Database\Eloquent\Model;
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

        TestResponse::macro(
            'assertExactJsonApiResponse',
            $this->assertExactJsonApiResponse()
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
                PHPUnit::fail("Failed to find a JSON:API validation error for key:'{$attribute}'"
                    . PHP_EOL . PHP_EOL .
                    $e->getMessage());
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

    public function assertExactJsonApiResponse(): Closure
    {
        return function (Model $model, array $data, string $type) {
            /** @var TestResponse $this */

            try {
                $this->assertExactJson([
                    'data' => [
                        'type' => $type,
                        'id' => (string) $model->getRouteKey(), //json-api: el id tiene que ser un string
                        'attributes' => $data,
                        'links' => [
                            'self' => route("api.v1.{$type}.show", $model->getRouteKey())
                        ]
                    ]
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail(
                    'failed to check that the current json is the same as expected.' .
                        PHP_EOL .
                        "Expected => \t {$e->getComparisonFailure()->getExpected()}" .
                        PHP_EOL .
                        "Actual => \t {$e->getComparisonFailure()->getActual()}"
                );
            }
        };
    }
}
