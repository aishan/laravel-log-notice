<?php
namespace Aishan\LaravelLogNotice;

use Illuminate\Support\ServiceProvider;

/**
 * Created by PhpStorm.
 * User: aishan
 * Date: 17-6-29
 * Time: 上午9:33
 */
class LogNoticeServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->publishes([
            realpath(__DIR__ . '/config/log-notice.php') => $this->getConfigPath(),
        ],'config');
    }

    public function register()
    {
        $configPath = realpath(__DIR__ . '/config/log-notice.php');
        $this->mergeConfigFrom($configPath, 'log-notice' );
    }

    private function getConfigPath(){
        return config_path('log-notice.php');
    }
}