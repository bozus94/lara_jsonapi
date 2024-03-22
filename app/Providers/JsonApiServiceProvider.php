<?php

namespace App\Providers;

use Illuminate\Testing\TestResponse;
use Illuminate\Support\ServiceProvider;

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
            function (string $attribute) {
                /** @var TestResponse $this   */
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source']
                    ]
                ])->assertJsonFragment([
                    'source' =>  ['pointer' => '/data/attributes/' . $attribute]
                ])->assertStatus(422)
                    ->assertHeader('content-type', 'application/vnd.api+json');
            }
        );
    }
}
