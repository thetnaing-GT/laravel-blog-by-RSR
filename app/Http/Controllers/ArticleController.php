<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['category', 'tags'])->latest()->paginate(9);
        return view('articles.index', ['articles' => $articles]);
    }

    public function detail($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.detail', ['article' => $article]);
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('articles.add', compact('categories', 'tags'));
    }

    public function store(StoreUpdateArticleRequest $request)
    {
        // Handle category
        $categoryId = $this->handleCategory($request);
        
        // Handle image upload
        $imageName = $this->handleImageUpload($request);

        // Create article
        $article = Article::create([
            'title' => Str::title($request->title),
            'body' => $request->body,
            'category_id' => $categoryId,
            'image' => $imageName,
        ]);

        // Attach tags if provided
        if ($request->filled('tags')) {
            $article->tags()->attach($request->tags);
        }

        return redirect()->route('articles.index')
                        ->with('success', 'Article created successfully.');
    }

    public function edit(Article $article)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('articles.edit', compact('article', 'categories', 'tags'));
    }

    public function update(StoreUpdateArticleRequest $request, Article $article)
    {
        // Handle category
        $categoryId = $this->handleCategory($request);
        
        // Handle image upload
        $imageName = $this->handleImageUpload($request, $article->image);

        // Update article
        $article->update([
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $categoryId,
            'image' => $imageName,
        ]);

        // Sync tags
        $article->tags()->sync($request->input('tags', []));

        return redirect()->route('articles.index')
                        ->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article)
    {
        // Detach all tags first
        $article->tags()->detach();

        // Delete the article image if it's not the default
        if ($article->image != 'default.jpg') {
            $imagePath = public_path('images/' . $article->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Delete the article
        $article->delete();
        
        return redirect()->route('articles.index')
                        ->with('success', 'Article deleted successfully.');
    }

    /**
     * Handles category selection/creation
     */
   protected function handleCategory($request)
    {
        // If new category is provided and no existing category is selected
        if ($request->filled('new_category') && !$request->filled('category_id')) {
            // Check if category already exists (case-insensitive)
            $category = Category::whereRaw('LOWER(name) = ?', [strtolower($request->new_category)])->first();
            
            if (!$category) {
                $category = Category::create(['name' => $request->new_category]);
            }
            
            return $category->id;
        }
        
        // Otherwise use the selected category_id
        return $request->category_id;
    }
    /**
     * Handles image upload
     */
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
}