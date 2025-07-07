<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateArticleRequest;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Tag;




class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['category', 'tags'])->latest()->paginate(9);

        return view('articles/index', [
            'articles' => $articles
        ]);
    }

    public function detail($id)
    {
        $data = Article::find($id);
        return view('articles.detail', [
            'article' => $data
        ]);
    }

    public function add()
    {
        $data = [
            ["id" => 1, "name" => "Science"],
            ["id" => 2, "name" => "Technology"],
        ];
        
        return view("articles.add", [
            'categories' => $data
        ]);
    }


    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('articles.add', compact('categories', 'tags'));
    }


    public function delete($id)
    {
        $article = Article::find($id);
        $article->delete();

        return redirect('/articles')->with('info', 'Your article is deleted');
    }




public function store(StoreUpdateArticleRequest $request)
{
    $imageName = $this->handleImageUpload($request);

    if ($request->filled('new_category')) {
        $category = Category::create([
            'name' => Str::title($request->new_category),
        ]);
        $categoryId = $category->id;
    } else {
        $categoryId = $request->category_id;
    }

    $article = Article::create([
        'title' => Str::title($request->title),
        'body' => $request->body,
        'category_id' => $categoryId,
        'image' => $imageName,
    ]);

    // Attach tags if selected
    if ($request->filled('tags')) {
        $article->tags()->attach($request->tags);
    }

    return redirect()->route('articles.index')->with('success', 'Article created successfully.');
}



public function edit(Article $article)
{
    $categories = Category::all();
    $tags = Tag::all();
    
    return view('articles.edit', compact('article', 'categories', 'tags'));
}

public function update(StoreUpdateArticleRequest $request, Article $article)
{
    $imageName = $this->handleImageUpload($request, $article->image);

    $article->update([
        'title' => $request->title,
        'body' => $request->body,
        'category_id' => $request->category_id,
        'image' => $imageName,
    ]);

    $article->tags()->sync($request->input('tags', []));

    return redirect()->route('articles.index')->with('success', 'Article updated successfully.');
}



protected function handleImageUpload($request, $currentImage = 'default.jpg')
{
    if ($request->hasFile('image')) {
        // Delete old image if it's not the default one
        if ($currentImage !== 'default.jpg') {
            $oldImagePath = public_path('images/' . $currentImage);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Store new image
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        return $imageName;
    }

    return $currentImage;
}



public function destroy(Article $article)
{
    // Detach all tags first
    $article->tags()->detach();

    // First we remove all tag relationships using detach()
    
    // Delete the article image if it's not the default
    if ($article->image != 'default.jpg') {
        $imagePath = public_path('images/' . $article->image);
        if (file_exists($imagePath)) {
            unlink($imagePath); // delete
        }
    }
    
    // Delete the article
    $article->delete();
    
    return redirect()->route('articles.index')->with('success', 'Article deleted successfully.');
}

}
