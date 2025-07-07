<?php
namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

interface CategoryRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10): Paginator;
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function countArticles(int $categoryId): int;
}