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
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'nuevo articulo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'contenido del nuevo articulo'
                ]
            ]
        ]);

        $article = Article::first();
        // la especificacion json-api exige que se devuelva el header location.
        $response->assertHeader('Location', route('api.v1.articles.show', $article->getRouteKey()));

        $response->assertCreated();
        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(), //json-api: el id tiene que ser un string
                'attributes' => [
                    'title' => 'nuevo articulo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'contenido del nuevo articulo'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article->getRouteKey())
                ]
            ]
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'slug' => 'slug',
                    'content' => 'contenido del nuevo articulo'
                ]
            ]
        ]);


        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_8_characters()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'asd',
                    'slug' => 'nuevo-articulo',
                    'content' => 'contenido del nuevo articulo'
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'nuevo articulo',
                    'content' => 'contenido del nuevo articulo'
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'nuevo articulo',
                    'slug' => 'nuevo-articulo',
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('content');
    }
}
