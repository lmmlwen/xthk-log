<?php

namespace Lmmlwen\Xthklog;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Lmmlwen\Xthklog\Middleware\LogMiddleware;
use Lmmlwen\Xthklog\Logging\LineFormatter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class XthkLogLaravelPackageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // 注册路由
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // 注册中间件
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(LogMiddleware::class);
        } else if ($this->app instanceof LumenApplication) {
            $this->app->middleware([
                LogMiddleware::class
            ]);
        }

        $source = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'merge_logging.php';

        $target = config_path('logging.php');

        if (!file_exists($target)) {
            $this->writeFileContent($target);
        }

        // 合并配置文件
        $this->mergeConfigFrom($source, 'logging.channels');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

    private function writeFileContent($file)
    {
        $text  = "<?php\n";
        $text .= "\n";
        $text .= "use Monolog\Handler\StreamHandler;\n";
        $text .= "use Monolog\Handler\SyslogUdpHandler;\n";
        $text .= "\n";
        $text .= "return [\n";
        $text .= "    'default' => env('LOG_CHANNEL', 'xthklog'),\n";
        $text .= "    'channels' => [\n";
        $text .= "    \n";
        $text .= "    ],\n";
        $text .= "];\n";

        $fp = fopen($file, 'w');
        fwrite($fp, $text);
        fclose($fp);
        chmod($file, 0777);
        return true;
    }
}
