<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateArticleRequest;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Interfaces\{
    ArticleRepositoryInterface,
    CategoryRepositoryInterface,
    TagRepositoryInterface
};

class ArticleController extends Controller
{
    protected $articleRepository;
    protected $categoryRepository;
    protected $tagRepository;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        CategoryRepositoryInterface $categoryRepository,
        TagRepositoryInterface $tagRepository
    ) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->middleware('auth')->except(['index', 'detail']);
    }

    public function index()
    {
        $articles = $this->articleRepository->paginateWithRelations(['category', 'tags', 'user']);
        return view('articles.index', ['articles' => $articles]);
    }


    public function detail($id)
    {
        $article = $this->articleRepository->findWithRelations($id, ['category', 'tags']);
        return view('articles.detail', ['article' => $article]);
    }

    public function create()
    {
        $categories = $this->categoryRepository->all();
        $tags = $this->tagRepository->all();
        return view('articles.add', compact('categories', 'tags'));
    }

    // In ArticleController
    private function resolveCategoryId($request)
    {
        return $request->filled('new_category') && !$request->filled('category_id')
            ? $this->articleRepository->findOrCreateCategory($request->new_category)
            : $request->category_id;
    }

    public function store(StoreUpdateArticleRequest $request)
    {
        // Handle category
        $categoryId = $this->resolveCategoryId($request);
        // Handle image upload
        $imageName = $this->articleRepository->handleImageUpload($request);

        // Create article
        $article = $this->articleRepository->create([
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $categoryId,
            'image' => $imageName,
        ]);

        // Attach tags if provided
        if ($request->filled('tags')) {
            $this->articleRepository->syncTags($article->id, $request->tags);
        }

        return redirect()->route('articles.index')
                        ->with('success', 'Article created successfully.');
    }

    public function edit($id)
    {
        $article = $this->articleRepository->findWithRelations($id);
        $categories = $this->categoryRepository->all();
        $tags = $this->tagRepository->all();
        return view('articles.edit', compact('article', 'categories', 'tags'));
    }

    public function update(StoreUpdateArticleRequest $request, $id)
    {
        // Handle category
        $categoryId = $this->resolveCategoryId($request);
        
        // Handle image upload
        $currentImage = $this->articleRepository->findWithRelations($id)->image;
        $imageName = $this->articleRepository->handleImageUpload($request, $currentImage);

        // Update article
        $this->articleRepository->update($id, [
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $categoryId,
            'image' => $imageName,
            // Security: You don’t trust user_id coming from the request (the user might try to submit someone else’s ID).
        

        ]);

        // Sync tags
        $this->articleRepository->syncTags($id, $request->input('tags', []));

        return redirect()->route('articles.index')
                        ->with('success', 'Article updated successfully.');
    }

    public function destroy($id)
    {
        $this->articleRepository->delete($id);
        return redirect()->route('articles.index')
                        ->with('success', 'Article deleted successfully.');
    }

    public function myArticles()
{
    $articles = $this->articleRepository
                    ->getByUserIdWithRelations(Auth::id(), ['category', 'tags']);
                    
    return view('articles.my_articles', ['articles' => $articles]);
}
}