<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\{
    CategoryRepositoryInterface,
    ArticleRepositoryInterface,
    TagRepositoryInterface
};
use App\Repositories\{
    EloquentCategoryRepository,
    EloquentArticleRepository,
    EloquentTagRepository
};

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            CategoryRepositoryInterface::class,
            EloquentCategoryRepository::class
        );

        $this->app->bind(
            ArticleRepositoryInterface::class,
            EloquentArticleRepository::class
        );

        $this->app->bind(
            TagRepositoryInterface::class,
            EloquentTagRepository::class
        );
    }

    public function boot()
    {
        //
    }
}