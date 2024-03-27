<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_articles()
    {
        $this->withoutExceptionHandling();

        $dataToCreate = [
            'title' => 'nuevo articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del nuevo articulo'
        ];

        $response = $this->postJson(route('api.v1.articles.create'), $dataToCreate);

        $article = Article::first();

        // la especificación json-api exige que se devuelva el header location.
        $response->assertHeader('Location', route('api.v1.articles.show', $article->getRouteKey()))
            ->assertCreated()
            ->assertExactJsonApiResponse($article, $dataToCreate, 'articles');
    }

    /** @test */
    public function title_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'slug' => 'slug',
            'content' => 'contenido del nuevo articulo'
        ]);

        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_8_characters()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'title' => 'asd',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del nuevo articulo'
        ]);

        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'title' => 'nuevo articulo',
            'content' => 'contenido del nuevo articulo'
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }
    /** @test */
    public function slug_is_unique()
    {
        $article = Article::factory()->create();

        $response = $this->postJson(route('api.v1.articles.create'), [
            'title' => 'nuevo articulo',
            'slug' => $article->slug,
            'content' => 'contenido del nuevo articulo'
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'title' => 'nuevo articulo',
            'slug' => 'nuevo-articulo',
        ]);
        $response->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function content_must_be_at_least_50_characters()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'title' => 'nuevo articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido nuevo'
        ]);

        $response->assertJsonApiValidationErrors('content');
    }
}
