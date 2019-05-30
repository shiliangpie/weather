<?php
/**
 * Created by PhpStorm.
 * User: tanbin
 * Date: 2019/5/30
 * Time: 11:12
 */

namespace Shiliangpie\Weather;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Weather::class, function(){
            return new Weather(config('services.weather.key'));
        });

        $this->app->alias(Weather::class, 'weather');
    }
    
    public function provides()
    {
        return [Weather::class, 'weather'];
    }
}