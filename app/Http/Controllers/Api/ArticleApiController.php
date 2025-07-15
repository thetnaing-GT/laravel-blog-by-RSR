<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;

use App\Http\Resources\ArticleResource;



class ArticleApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
    }

    public function index()
    {

        $articles = $this->articleRepository->paginateWithRelations(['category', 'tags']);
        return ArticleResource::collection($articles);

        
    }

 

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateArticleRequest $request)
    {
        // Handle category
        $categoryId = $request->filled('new_category') && !$request->filled('category_id')
            ? $this->articleRepository->findOrCreateCategory($request->new_category)
            : $request->category_id;

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

        return new ArticleResource($article);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = $this->articleRepository->findWithRelations($id, ['category', 'tags']);
        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateArticleRequest $request, $id)
    {
        $article = $this->articleRepository->findWithRelations($id);
        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        // Handle category
        $categoryId = $request->filled('new_category') && !$request->filled('category_id')
            ? $this->articleRepository->findOrCreateCategory($request->new_category)
            : $request->category_id;

        // Handle image upload
        $currentImage = $article->image;
        $imageName = $this->articleRepository->handleImageUpload($request, $currentImage);

        // Update article
        $this->articleRepository->update($id, [
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $categoryId,
            'image' => $imageName,
        ]);

        // Sync tags
        $this->articleRepository->syncTags($id, $request->input('tags', []));

        return new ArticleResource($this->articleRepository->findWithRelations($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = $this->articleRepository->findWithRelations($id);
        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        $this->articleRepository->delete($id);
        return response()->json(null, 204);
    }
}
