<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ArticleCollection::make(Article::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'data.attributes.title' => ['required', 'min:8'],
            'data.attributes.slug' => ['required',],
            'data.attributes.content' => ['required'],
        ]);

        $data = $request->input('data.attributes');
        $article = Article::create([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
        ]);

        return ArticleResource::make($article);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return ArticleResource::make($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'data.attributes.title' => ['required', 'min:8'],
            'data.attributes.slug' => ['required',],
            'data.attributes.content' => ['required'],
        ]);

        $data = $request->input('data.attributes');

        $article->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
        ]);

        return ArticleResource::make($article);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        //
    }
}
