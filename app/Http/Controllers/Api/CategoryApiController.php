<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreUpdateArticleRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;


use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */

    protected $categoryRepository;
    protected $articleRepository;
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
        $categories = $this->categoryRepository->paginate();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $category = $this->categoryRepository->create($data);
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       $category = $this->categoryRepository->find($id, ['articles']);
       return new CategoryResource($category);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function update(UpdateCategoryRequest $request, $id)
    {
        $updatedCategory = $this->categoryRepository->update($id, $request->validated());

        return new CategoryResource($updatedCategory);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function destroy($id)
    {
        $articleCount = $this->categoryRepository->countArticles($id);
        if ($articleCount > 0) {
            return response()->json(['message' => 'Cannot delete category with associated articles.'], 400);
        }

        $destroyCategory = $this->categoryRepository->delete($id);
        return new CategoryResource($destroyCategory);
    }

}
