<?php

namespace SuperView\Providers;

use Illuminate\Support\ServiceProvider;
use SuperView\Utils\Config;

class SuperViewModelProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Get config, then bind automaticly
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'superview');
        $models = Config::get('models');
        foreach ($models as $alias => $model) {
            $this->app->singleton(Config::get('model_prefix') . $alias, function($app) use ($model) {
                return new $model;
            });
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'superview');
        return array_map(function($value) {
            return Config::get('model_prefix') . $value;
        }, array_keys(Config::get('models')));
    }
}