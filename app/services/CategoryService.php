<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Create a new category.
     */
    public function create(StoreCategoryRequest $request)
    {
        // You can use the validated data directly from the request here
        $validatedData = $request->validated();

        return $this->categoryRepository->create($validatedData);
    }

    /**
     * Update an existing category.
     */
    public function update(UpdateCategoryRequest $request, int $id)
    {
        $validatedData = $request->validated();

        try {
            return $this->categoryRepository->update($id, $validatedData);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Category not found");
        }
    }

    /**
     * Delete a category.
     */
    public function delete(int $id)
    {
        // Ensure that category can be deleted (check for associated articles)
        if ($this->categoryRepository->countArticles($id) > 0) {
            return false; // Can't delete category with articles
        }

        return $this->categoryRepository->delete($id);
    }
}
