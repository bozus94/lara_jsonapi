<?php

namespace Tests\Feature\Article;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_Update_articles()
    {
        $this->withoutExceptionHandling();

        $article = Article::factory()->create();

        $dataToUpdate = [
            'title' => 'articulo editado',
            'slug' => 'articulo editado',
            'content' => 'contenido del articulo editado'
        ];

        $response = $this->patchJson(route('api.v1.articles.update', $article), $dataToUpdate)->assertOk();

        // la especificación json-api exige que se devuelva el header location.
        $response->assertHeader('Location', route('api.v1.articles.show', $article->getRouteKey()));

        $response->assertExactJsonApiResponse($article, $dataToUpdate, 'articles');
    }


    /** @test */
    public function title_is_required()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'slug',
            'content' => 'contenido del nuevo articulo'
        ]);

        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_8_characters()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'asd',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del nuevo articulo'
        ]);

        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'nuevo articulo',
            'content' => 'contenido del nuevo articulo'
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'nuevo articulo',
            'slug' => 'nuevo-articulo',
        ]);
        $response->assertJsonApiValidationErrors('content');
    }
}
