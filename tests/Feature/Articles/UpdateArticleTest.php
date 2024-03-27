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
    public function can_update_article_when_it_is_the_same_slug()
    {
        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'nuevo articulo',
            'slug' => $article->slug,
            'content' => "Lorem ipsum dolor sit, amet consectetur adipisicing elit. Nam, voluptates! Nulla, et. Recusandae ratione eligendi autem! Ipsam expedita quas labore molestiae mollitia debitis, animi soluta reprehenderit quam repellendus ab, voluptates quibusdam accusantium explicabo ullam adipisci natus! Atque perspiciatis aliquam dolor, natus perferendis quisquam quas dolore nisi, nesciunt sed nihil sunt consequuntur."
        ]);

        $response->assertOk('slug');
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
