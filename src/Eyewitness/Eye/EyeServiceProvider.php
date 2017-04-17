<?php

namespace Eyewitness\Eye;

use Eyewitness\Eye\Http\Middleware\CaptureRequest;
use Symfony\Component\Console\Input\ArgvInput;
use Eyewitness\Eye\Commands\InstallCommand;
use Eyewitness\Eye\Commands\DownCommand;
use Eyewitness\Eye\Commands\WorkCommand;
use Illuminate\Support\ServiceProvider;
use Eyewitness\Eye\Commands\UpCommand;
use Eyewitness\Eye\Queue\Worker;
use Config;
use Event;
use Queue;
use Route;
use Log;

class EyeServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('eyewitness/eye4', 'eye');

        $this->loadRoutes();
        $this->loadLogTracking();
        $this->loadMiddleware();

        if ($this->app->runningInConsole()) {
            $this->loadConsole();
        } else {
            $this->loadRequestTracking();
        }
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->singleton('Eyewitness\Eye\Eye');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

    /**
     * Load the package routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        include __DIR__.'/../../routes/api.php';
    }

    /**
     * Load the log tracking.
     *
     * @return void
     */
    protected function loadLogTracking()
    {
        if (Config::get('eye::monitor_log')) {
            Log::listen(function($level, $message, $context) {
                app(Eye::class)->log()->logError($level);
            });
        }
    }

    /**
     * Load the middleware.
     *
     * @return void
     */
    protected function loadMiddleware()
    {
        Route::filter('eyewitness_auth', 'Eyewitness\Eye\Http\Middleware\AuthRoute');
        Route::filter('eyewitness_log_route', 'Eyewitness\Eye\Http\Middleware\EnabledLogRoute');
        Route::filter('eyewitness_queue_route', 'Eyewitness\Eye\Http\Middleware\EnabledQueueRoute');
        Route::filter('eyewitness_composer_route', 'Eyewitness\Eye\Http\Middleware\EnabledComposerRoute');
    }

    /**
     * Load the console.
     *
     * @return void
     */
    protected function loadConsole()
    {
        $this->loadSchedulerMonitor();
        $this->loadInstallCommand();
        $this->loadMaintenanceMonitor();
        $this->loadQueueMonitor();
    }

    /**
     * Load the scheduler monitor.
     *
     * @return void
     */
    protected function loadSchedulerMonitor()
    {
        if (Config::get('eye::monitor_scheduler')) {
            Event::listen('artisan.start', function($app) {
                app(Eye::class)->scheduler()->inspectCommand($_SERVER['argv']);
            });

            $this->app->shutdown(function() {
                app(Eye::class)->scheduler()->trackCommand();
            });
        }
    }

    /**
     * LAdd the Eyewitness install command.
     *
     * @return void
     */
    protected function loadInstallCommand()
    {
        $this->app->bind('eye::eyewitness:install', function($app) {
            return new InstallCommand();
        });

        $this->commands([
            'eye::eyewitness:install'
        ]);
    }

    /**
     * Load the maintenance monitor. This allows us to ping when
     * the application goes up or down.
     *
     * @return void
     */
    protected function loadMaintenanceMonitor()
    {
        if (Config::get('eye::monitor_maintenance_mode')) {
            $this->app->extend('command.down', function () {
                return new DownCommand();
            });

            $this->app->extend('command.up', function() {
                return new UpCommand();
            });
        }
    }

    /**
     * Load the queue monitor.
     *
     * @return void
     */
    protected function loadQueueMonitor()
    {
        if (Config::get('eye::monitor_queue')) {
            $this->registerFailingQueueHandler();
            $this->registerQueueWorker();
            $this->registerWorkCommand();
        }
    }

    /**
     * Register a failing queue hander.
     *
     * @return void
     */
    protected function registerFailingQueueHandler()
    {
        Queue::failing(function($connection, $job, $data) {
            Log::info('logged failing');
            app(Eye::class)->api()->sendQueueFailingPing($connection, $data['job'], $job->getQueue());
        });
    }

    /**
     * Register a new queue worker. This allows us to capture heartbeats of
     * the queue actually working.
     *
     * @return void
     */
    protected function registerQueueWorker()
    {
        $this->app->singleton('queue.worker', function () {
            return new Worker($this->app['queue'], $this->app['queue.failer'], $this->app['events']);
        });
    }

    /**
     * Register a new queue:work command.
     *
     * @return void
     */
    protected function registerWorkCommand()
    {
        $this->app->extend('command.queue.work', function () {
            return new WorkCommand($this->app['queue.worker']);
        });
    }

    /**
     * Load the request tracking.
     *
     * @return void
     */
    protected function loadRequestTracking()
    {
        if (Config::get('eye::monitor_request')) {
            $this->app->finish(function($request, $response) {
                $cr = new CaptureRequest;
                $cr->terminate($request, $response);
            });
        }
    }
}
