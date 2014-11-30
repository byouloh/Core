<?php

namespace OpenTribes\Core\Silex;


use Igorw\Silex\ConfigServiceProvider;
use Mustache\Silex\Provider\MustacheServiceProvider;
use OpenTribes\Core\Silex\Provider\ControllerServiceProvider;
use OpenTribes\Core\Silex\Provider\RouteProvider;
use OpenTribes\Core\Silex\Provider\RouteServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\ServiceProviderInterface;
use RecursiveDirectoryIterator;
class Module implements ServiceProviderInterface{
    private $env;

    public function __construct($env)
    {
        $this->env = $env;
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }


    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $this->loadConfigurations($app);
        $this->registerServices($app);
    }


    private function registerServices(Application $app)
    {
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new DoctrineServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $app->register(new MustacheServiceProvider());
        $app->register(new ControllerServiceProvider());
        $app->mount('/',new RouteServiceProvider());

    }

    private function loadConfigurations(Application $app)
    {


        $configDir = realpath(__DIR__.'/../config/'.$this->env);
        $iterator = new RecursiveDirectoryIterator($configDir, RecursiveDirectoryIterator::SKIP_DOTS);
        /**
         * @var \SplFileInfo $object
         */
        foreach ($iterator as $path => $object) {
            if ($object->isFile()) {
                $app->register(new ConfigServiceProvider($path));
            }
        }
    }

} 