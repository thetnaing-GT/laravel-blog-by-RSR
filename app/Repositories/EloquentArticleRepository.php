<?php
namespace App\Repositories;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EloquentArticleRepository implements ArticleRepositoryInterface
{
    protected $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function allWithRelations(array $relations = []): Collection
    {
        return $this->model->with($relations)->get();
    }


     public function paginateWithRelations(array $relations = [], int $perPage = 9): Paginator
    {
        return $this->model->with($relations)
                           ->withCapitalizedTitle() // Apply the capitalized title scope here
                           ->latest()
                           ->paginate($perPage);
    }


    public function findWithRelations(int $id, array $relations = [])
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    public function create(array $data)
    {
        $data['title'] = Str::title($data['title']);
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $article = $this->findWithRelations($id);
        $article->update($data);
        return $article;
    }

    public function delete(int $id)
    {
        $article = $this->findWithRelations($id);
        
        // Detach all tags first
        $article->tags()->detach();

        // Delete the article image if it's not the default
        if ($article->image != 'default.jpg') {
            $imagePath = public_path('images/' . $article->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
        
        return $article->delete();
    }

    public function syncTags(int $articleId, array $tagIds = [])
    {
        $article = $this->findWithRelations($articleId);
        return $article->tags()->sync($tagIds);
    }

    public function findOrCreateCategory(string $categoryName)
    {
        $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
            
        if (!$category) {
            $category = Category::create(['name' => $categoryName]);
        }
        
        return $category->id;
    }

    public function handleImageUpload($request, string $currentImage = 'default.jpg'): string
    {
        if ($request->hasFile('image')) {
            // Delete old image if it's not the default one
            if ($currentImage !== 'default.jpg') {
                $oldImagePath = public_path('images/' . $currentImage);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
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