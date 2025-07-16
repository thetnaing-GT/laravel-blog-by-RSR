<?php
namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

interface ArticleRepositoryInterface
{
    public function allWithRelations(array $relations = []): Collection;
    public function paginateWithRelations(array $relations = [], int $perPage = 9): Paginator;
    public function findWithRelations(int $id, array $relations = []);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function syncTags(int $articleId, array $tagIds = []);
    public function findOrCreateCategory(string $categoryName);
    public function handleImageUpload($request, string $currentImage = 'default.jpg'): string;
    public function getByUserIdWithRelations($userId, array $relations = []);

}