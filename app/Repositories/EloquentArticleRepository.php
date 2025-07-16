<?php
namespace App\Repositories;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
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
        $relations = array_merge($relations, ['user']); // Ensure user is always loaded
        return $this->model->with(array_unique($relations))
                        ->withCapitalizedTitle()
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
        $data['user_id'] = Auth::id(); //  user_id to the authenticated user's ID and every new articles have user_id
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        // Added Ownership Checks 
        $article = $this->findWithRelations($id, ['user']);
        if ($article->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $data['title'] = Str::title($data['title']);
        $article->update($data);
        return $article;
    }

    public function delete(int $id)
    {
        $article = $this->findWithRelations($id, ['user']);
        if ($article->user_id !== Auth::id()) { // Ownership Check
            abort(403, 'Unauthorized action.');
        }
        
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
        $categoryName = Str::title($categoryName);
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

    public function getByUserIdWithRelations($userId, array $relations = [])
    {
        return Article::with($relations) // with() method is used for eager loading of related data
                    ->where('user_id', $userId)
                    ->latest()
                    ->paginate();
    }

}