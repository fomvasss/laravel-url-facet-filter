<?php

namespace Fomvasss\UrlFacetFilter;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-url-facet-filter.php' => config_path('laravel-url-facet-filter.php')
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-url-facet-filter.php', 'laravel-url-facet-filter');

        $this->app->singleton(FacetFilterBuilder::class, function () {
            return new FacetFilterBuilder($this->app);
        });

        $this->app->alias(FacetFilterBuilder::class, 'url-facet-filter');
    }
}
