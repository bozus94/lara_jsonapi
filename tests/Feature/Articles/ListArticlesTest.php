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
}
