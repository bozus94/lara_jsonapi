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
            'content' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Optio, quia! Eum fuga repudiandae veniam consequatur minima quibusdam cumque delectus quo sed tenetur, ullam officia assumenda magnam quod itaque vero iste dicta? Magni labore cumque delectus aspernatur tempora adipisci, at, corporis aliquid voluptatem optio amet exercitationem itaque temporibus, magnam veritatis enim!'
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
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatem, sapiente quasi. Porro nostrum recusandae doloremque labore harum culpa magni eos nobis similique consequuntur animi ea impedit quo inventore, fugit minima adipisci ratione sed beatae a officiis! Obcaecati dicta recusandae perferendis maiores ex, quisquam saepe ea quos quas temporibus atque. Eaque!'
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
            'content' => "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Recusandae deserunt minus suscipit doloribus itaque consequatur error esse nostrum nisi. Sed mollitia veritatis porro, ut harum illo facilis maxime pariatur recusandae. Nisi sunt voluptatem, vitae quas soluta error, harum natus excepturi esse unde corrupti praesentium provident repudiandae quisquam amet possimus quaerat?"
        ]);

        $response->assertOk();
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
