<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \Validator::extend('iunique', function ($attribute, $value, $parameters, $validator) {
            $query = \DB::table($parameters[0]);
            $column = $query->getGrammar()->wrap($parameters[1]);
            return ! $query->whereRaw("lower({$column}) = lower(?)", [$value])->count();
        });
    }
}
