<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Route::any(
            '/test',
            fn () => 'ok'
        )->middleware(ValidateJsonApiDocument::class);
    }

    /** @test */
    public function only_accept_valid_json_document()
    {
        $this->postJson('test', [
            'data' => [
                'type' => 'string',
                'attributes' => ['string'],
            ]
        ])->assertSuccessful();

        $this->patchJson('test', [
            'data' => [
                'id' => '1',
                'type' => 'string',
                'attributes' => ['string'],
            ]
        ])->assertSuccessful();
    }

    /** @test */
    public function data_is_required()
    {
        $this->postJson('test', [])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('test', [])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_must_be_an_array()
    {
        $this->postJson('test', [
            'data' => 'array'
        ])->assertJsonApiValidationErrors('data');

        $this->patchJson('test', [
            'data' => 'array'
        ])->assertJsonApiValidationErrors('data');
    }


    /** @test */
    public function attributtes_is_required()
    {
        $this->postJson('test', [
            'data' => [
                'type' => 'string',
            ]
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test', [
            'data' => [
                'type' => 'string',
                'id' => '1',
            ]
        ])->assertJsonApiValidationErrors('data.attributes');
    }


    /** @test */
    public function attributtes_must_be_an_array()
    {
        $this->postJson('test', [
            'data' => [
                'attributes' => 'string',
                'type' => 'string'
            ]
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test', [
            'data' => [
                'id' => '1',
                'attributes' => 'string',
                'type' => 'string'
            ]
        ])->assertJsonApiValidationErrors('data.attributes');
    }


    /** @test */
    public function type_is_required()
    {
        $this->postJson('test', [
            'data' => [
                'attributes' => ['string'],
            ]
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test', [
            'data' => [
                'id' => '1',
                'attributes' => ['string'],
            ]
        ])->assertJsonApiValidationErrors('data.type');
    }
    /** @test */
    public function type_must_be_a_string()
    {
        $this->postJson('test', [
            'data' => [
                'type' => 1,
                'attributes' => ['string'],
            ]
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test', [
            'data' => [
                'type' => 1,
                'id' => '1',
                'attributes' => ['string'],
            ]
        ])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function id_is_required_in_patch_request()
    {
        $this->patchJson('test', [
            'data' => [
                'type' => 'string',
                'attributes' => ['string'],
            ]
        ])->assertJsonApiValidationErrors('data.id');
    }
    /** @test */
    public function id_must_be_a_string_in_patch_request()
    {
        $this->patchJson('test', [
            'data' => [
                'type' => 'string',
                'id' => 1,
                'attributes' => ['string'],
            ]
        ])->assertJsonApiValidationErrors('data.id');
    }
}
