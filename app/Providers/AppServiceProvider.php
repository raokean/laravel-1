<?php

namespace App\Providers;

use App\Comment;
use Illuminate\Support\ServiceProvider;
use App\Post;
use App\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *



     * @return void
     */
    public function boot()
    {
        // передача переменных на вид _sideber контента
        view()->composer('page._sidebar', function($view){
            $view->with('categories', Post::getCategoryPosts());
            $view->with('resentPosts', Post::getLatestPosts());
            $view->with('featuredPosts', Post::getFeaturesPosts());
            $view->with('popularPosts', Post::getPopularPosts());
        });

        // передача переменных _sidebar админки
        view()->composer('admin._sidebar', function($view){
            $view->with('commentary_count', Comment::where('status', 1)->count());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
