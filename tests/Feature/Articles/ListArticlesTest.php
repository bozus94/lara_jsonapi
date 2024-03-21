<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_single_article()
    {
        $this->withoutExceptionHandling();
        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.show', $article->getRouteKey()));

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(), //json-api: el id tiene que ser un string
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article->getRouteKey())
                ]
            ]
        ]);
    }

    /** @test */
    public function can_fetch_all_articles()
    {
        $this->withoutExceptionHandling();
        $articles = Article::factory(3)->create();

        $articlesJson = [];
        foreach ($articles as $article) {
            $articlesJson[] = [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(), //json-api: el id tiene que ser un string
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article->getRouteKey())
                ]
            ];
        }

        $response = $this->getJson(route('api.v1.articles.index'));
        $response->assertExactJson([
            'data' => $articlesJson,
            'links' => [
                'self' => route('api.v1.articles.index')
            ]
        ]);
    }

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
}
