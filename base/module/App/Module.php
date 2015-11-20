<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

use Zend\ModuleManager\ModuleManager;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Db\Adapter\Profiler\Profiler;
use Zend\Db\Adapter\StatementContainer;

use App\Controller\Plugin\AuthPlugin;
use App\Controller\Plugin\ContainerPlugin;

class Module
{
    protected $toRoute;
    protected $auth;

    public function init(ModuleManager $mm)
    {
        $this->loggingError();
    }

    public function onBootstrap(MvcEvent $e)
    {
        $controller_id = (string) '';
        $action_id = (string) '';
        get_action($controller_id, $action_id);

        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 200);

        if (IS_TEST) {
            $this->loggingAccess($e->getRequest());
        }

        $this->createDbAdapter($e);
        $valid = $this->bootstrapSession($e);

        if (!$valid && 'failed' != $controller_id) {
            $this->toRoute = array(
                'controller' => 'failed',
            );
            session_destroy();
        }
        else if ('failed' != $controller_id) {
            $this->auth = new AuthPlugin();
            $login = $this->auth->isLogin();

            if (RESTRICT_LOGIN) {
                $user_no = $this->auth->get('user_no');
                $storage = $e->getApplication()->getServiceManager()->get('Session\Storage\DbSessionStorage');
                $storage->renewRestrictLogin($user_no);
            }

            if ($login && 'index' == $controller_id
                    && ('index' == $action_id || !$action_id) ) {
                $this->toRoute = array(
                    'controller' => 'menu',
                    'action' => 'top',
                );
            }
            else if (!$login && 'index' != $controller_id) {
                $this->toRoute = array(
                    'controller' => 'index', 'action' => 'index',
                );
                $container = new ContainerPlugin();
                $container->setContainer('index');
                $uri = filter_input(INPUT_SERVER, "REQUEST_URI");
                $container->set('uri', $uri);
                $container->set('err_msg', 'セッションが切れました。');

                // save request data
                $p = $e->getRequest()->getPost()->toArray();
                if (!$login && $this->toRoute && $p) {
                    $container->setContainer($controller_id);
                    $container->set('postRequest', json_encode($p));
                }
            }

            $this->bootTranslator($e);
        }
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), 100);

        if ($this->toRoute) {
            $eventManager
                    ->getSharedManager()
                    ->attach('Zend\Mvc\Controller\AbstractActionController'
                        , 'dispatch', function($e) {
                            $controller = $e->getTarget();
                            $controller->plugin('redirect')
                                ->toRoute('app', $this->toRoute);
                    });
            return false;
        }

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function bootstrapSession($e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $storage = $serviceManager->get('Session\Storage\DbSessionStorage');
        $storage->setSessionStorage();
        return $storage->isValid();
    }

    public function bootTranslator($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $translatorConfig = $sm->get('translator');
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translatorConfig);
    }

    protected function createDbAdapter(MvcEvent $e)
    {
        $config = $e->getApplication()->getConfig();
        $adapter = new Adapter($config['db']);
        GlobalAdapterFeature::setStaticAdapter($adapter);

        if (IS_TEST) {
            $this->loggingQuery($adapter);
        }
    }

    public function onDispatch(MvcEvent $e)
    {
        $routeMatch       = $e->getRouteMatch();
        $controllerName   = $routeMatch->getParam('controller', 'not-found');
        $controllerLoader = $e->getApplication()->getServiceManager()->get('controllerLoader');

        /**
         * setting controller from request (processing description omitted at "module.config.php")
         * If "$controllerName" and actual file are diffrent mapping, stop this processing
         * and describe at "module.config.php" or overwrite "setInvokableClass"
         */
        $file = __DIR__ . '/src/' . str_replace('\\', '/', $controllerName) . 'Controller.php';
        if (file_exists($file)) {
            $controllerLoader->setInvokableClass($controllerName, $controllerName . 'Controller');
        }
        else {
            $controllerLoader->setInvokableClass($controllerName, 'App\Controller\FailedController');
        }

        $controller = $controllerLoader->get($controllerName);
        $pluginManager = $controller->getPluginManager();
        $pluginManager->setInvokableClass('auth', 'App\Controller\Plugin\AuthPlugin');
        $pluginManager->setInvokableClass('container', 'App\Controller\Plugin\ContainerPlugin');
        $pluginManager->setInvokableClass('search', 'App\Controller\Plugin\SearchPlugin');
        $pluginManager->setInvokableClass('translator', 'App\Controller\Plugin\TranslatorPlugin');
    }

    public function onDispatchError(MvcEvent $e)
    {
        $vm = $e->getViewModel();
        $vm->setTemplate('error/layout');
    }

    public function getConfig()
    {
        return include_once __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'formElementErrors' => 'App\Helper\FormElementErrors',
                'PageCtrl' => 'App\Helper\PageCtrl',
                'ViewButton' => 'App\Helper\ViewButton',
            ),
        );
    }

    public function loggingAccess($request)
    {
        $logger = new Logger();
        $writer = new Stream(LOG_DIR . "/access/" . date('Y-m-d'));
        $logger->addWriter($writer);
        $logger->info($request);
    }


    public function loggingQuery($adapter)
    {
        $logger = new Logger();
        $writer = new Stream(LOG_DIR . "/query/" . date('Y-m-d'));
        $logger->addWriter($writer);

        $profiler = new Profiler();
        $adapter->setProfiler($profiler);
        $profiler->profilerStart(new StatementContainer);

        register_shutdown_function(function () use ($logger, $profiler)
        {
            $profilers = $profiler->getProfiles();
            if ($profilers) {
                foreach ($profilers as $profile) {
                    $logger->debug($profile['sql']);
                }
            }
        });
    }

    public function loggingError()
    {
        $logger = new Logger();
        $writer = new Stream(LOG_DIR . "/error/" . date('Y-m-d'));
        $logger->addWriter($writer);

        Logger::registerErrorHandler($logger);
        Logger::registerExceptionHandler($logger);

        register_shutdown_function(function () use ($logger)
        {
            $e = error_get_last();
            if ($e) {
                $logger->err(print_r($e, 1));
            }
        });
    }

}
