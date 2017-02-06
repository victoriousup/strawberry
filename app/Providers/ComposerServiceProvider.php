<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        view()->composer('layouts.admin.app', 'App\Http\ViewComposers\Admin\Layout\LeftNavViewComposer');
    }


    public function register()
    {

    }
}
