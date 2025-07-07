<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentTagRepository implements TagRepositoryInterface
{
    protected $model;

    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 10): Paginator
    {
        return $this->model->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $tag = $this->find($id);
        $tag->update($data);
        return $tag;
    }

    public function delete(int $id)
    {
        $tag = $this->find($id);
        return $tag->delete();
    }

    public function getArticles(int $tagId, int $perPage = 10)
    {
        return $this->find($tagId)->articles()->paginate($perPage);
    }
}