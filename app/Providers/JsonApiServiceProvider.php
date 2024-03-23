<?php

namespace App\Providers;

use Closure;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\Assert;
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
            try {
                $this->assertJsonFragment([
                    'source' =>  ['pointer' => "/data/attributes/'{$attribute}'"]
                ]);
            } catch (ExpectationFailedException $th) {
                Assert::fail('adsad') .
                    \PHP_EOL . \PHP_EOL .
                    $th->getMessage();
            }

            $this->assertJsonStructure([
                'errors' => [
                    ['title', 'detail', 'source']
                ]
            ]);

            $this->assertStatus(422)
                ->assertHeader('content-type', 'application/vnd.api+json');
        };
    }
}
