<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */

    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
     public function index()
    {
        $categories = $this->categoryRepository->paginate();
        return view('categories.index', compact('categories'));
    }


    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category.
     */
      public function store(StoreCategoryRequest $request)
    {
        $this->categoryRepository->create($request->validated());
        return redirect()->route('categories.index')
                         ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */

      public function show($id)
    {
        $category = $this->categoryRepository->find($id);
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($id)
    {
        $category = $this->categoryRepository->find($id);
        return view('categories.edit', compact('category'));
    }


    /**
     * Update the specified category.
     */
     public function update(UpdateCategoryRequest $request, $id)
    {
        $this->categoryRepository->update($id, $request->validated());
        return redirect()->route('categories.index')
                         ->with('success', 'Category updated successfully.');
    }


    /**
     * Remove the specified category.
     */
     public function destroy($id)
    {
        if ($this->categoryRepository->countArticles($id) > 0) {
            return redirect()->route('categories.index')
                             ->with('error', 'Cannot delete category with associated articles.');
        }

        $this->categoryRepository->delete($id);
        return redirect()->route('categories.index')
                         ->with('success', 'Category deleted successfully.');
    }


    /**
     * API endpoint for fetching categories (for select dropdowns)
     */
     public function apiIndex()
    {
        return response()->json($this->categoryRepository->all());
    }
}