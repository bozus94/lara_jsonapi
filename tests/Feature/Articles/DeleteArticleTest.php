<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_delete_article()
    {
        $this->expectsDatabaseQueryCount(7);

        $article = Article::factory()->count(2)->create();

        $this->getJson(route('api.v1.articles.destroy',  $article[0]))
            ->assertNoContent();

        $this->assertModelMissing($article[0]);
        $this->assertModelExists($article[1]);
        $this->assertDatabaseCount('articles', 1);
    }
}
