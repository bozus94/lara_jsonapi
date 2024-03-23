<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Middleware\ValidateJsonApiHeader;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateJsonApiHeaderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Route::any(
            '/test',
            fn () => 'ok'
        )->middleware(ValidateJsonApiHeader::class);
    }

    /** @test */
    public function accept_header_must_be_present_in_all_requests()
    {
        $this->get('/test')->assertStatus(406);
        $this->get('/test', [
            'accept' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_present_in_post_requests()
    {
        $this->post('/test', [], [
            'accept' => 'application/vnd.api+json'
        ])->assertStatus(415);

        $this->post('/test', [], [
            'accept' => 'application/vnd.api+json',
            'content_type' => 'application/vnd.api+json'
        ])->assertSuccessful(415);
    }

    /** @test */
    public function content_type_header_must_be_present_in_patch_requests()
    {
        $this->patch('/test', [], [
            'accept' => 'application/vnd.api+json'
        ])->assertStatus(415);

        $this->patch('/test', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertSuccessful(415);
    }

    /** @test */
    public function content_type_header_must_be_present_in_response()
    {
        $this->get('/test', [
            'accept' => 'application/vnd.api+json'
        ])->assertHeader('content-type', 'application/vnd.api+json');

        $this->post('/test', [], [
            'accept' => 'application/vnd.api+json', 'content-type' => 'application/vnd.api+json'
        ])->assertHeader('content-type', 'application/vnd.api+json');

        $this->patch('/test', [], [
            'accept' => 'application/vnd.api+json', 'content-type' => 'application/vnd.api+json'
        ])->assertHeader('content-type', 'application/vnd.api+json');
    }

    /** @test */
    public function content_type_header_not_must_be_present_in_empty_response()
    {
        Route::any(
            'empty_response',
            fn () => response()->noContent()
        )->middleware(ValidateJsonApiHeader::class);

        $this->get('empty_response', ['accept' => 'application/vnd.api+json'])->assertHeaderMissing('content_type');

        $this->post('empty_response', [], ['accept' => 'application/vnd.api+json', 'content-type' => 'application/vnd.api+json'])->assertHeaderMissing('content_type');
        $this->patch('empty_response', [], ['accept' => 'application/vnd.api+json', 'content-type' => 'application/vnd.api+json'])->assertHeaderMissing('content_type');
        $this->delete('empty_response', [], ['accept' => 'application/vnd.api+json'])->assertHeaderMissing('content_type');
    }
}
