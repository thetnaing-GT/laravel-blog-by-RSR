<?php 
// app/Repositories/EloquentCategoryRepository.php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 10): Paginator
    {
        return $this->model->latest()->paginate($perPage);
    }

    public function find(int $id, array $relations = [])
    {
        // return $this->model->findOrFail($id);
        return $this->model->with($relations)->findOrFail($id);

    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id)
    {
        $category = $this->find($id);
        return $category->delete();
    }

    public function countArticles(int $categoryId): int
    {
        return $this->find($categoryId)->articles()->count();
    }
}