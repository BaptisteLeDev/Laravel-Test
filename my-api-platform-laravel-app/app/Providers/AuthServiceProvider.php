<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Formation;
use App\Policies\FormationPolicy;
use App\Models\Classe;
use App\Policies\ClassePolicy;
use App\Models\Chapitre;
use App\Policies\ChapitrePolicy;
use App\Models\Article;
use App\Policies\ArticlePolicy;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Formation::class, FormationPolicy::class);
        Gate::policy(Classe::class, ClassePolicy::class);
        Gate::policy(Chapitre::class, ChapitrePolicy::class);
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
