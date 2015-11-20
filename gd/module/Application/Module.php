<?php
/**
 * Author: Marvin Manguiat 
 * Date: 10/14/2015
 * Company: Aruze Gaming Philippines 
 * Dept:  PHP IT Team
 * 	 
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\ModuleManager\ModuleManager;
use Zend\Db\Adapter\Profiler\Profiler;
use Zend\Db\Adapter\StatementContainer;

class Module {
    /**
     * bootstrap 
     * @param MvcEvent $e
     */

    public function init(ModuleManager $mm)
    {
        $this->loggingError();
    }
    
    public function onBootstrap(MvcEvent $e) {
         
    $eventManager = $e->getApplication ()->getEventManager();
    $moduleRouteListener = new ModuleRouteListener ();
    $moduleRouteListener->attach($eventManager );
    
    $eventManager->attach ( 'route', array (
                    $this,
                    'checkIfLoggedin' 
    ) );
    $this->createDbAdapter($e);
    }
    /**
     * check if user already logged in,
     * if not redirect to AuthController::index
     * @param MvcEvent $e
     */
    public function checkIfLoggedin(MvcEvent $e) {
            $this->loggingError();
            $serviceManager = $e->getApplication ()->getServiceManager ();
            $customStorage = $serviceManager->get ( 'AuthService' )->getStorage ();
            $identitymanager = $serviceManager->get ( 'IdentityManager' );
            $sessionManager = $customStorage->getSessionManager ();
            $sessionId = $customStorage->getSessionId ();
            $isLoggedin = $sessionManager->getSaveHandler ()->read ( $sessionId );

            // something useful in the future developments
            $routeParams = $e->getRouteMatch ()->getParams ();
            $controller = $routeParams ['controller'];
            $action = strtolower ( $routeParams ['action'] );

            if (! $isLoggedin) {
                    $e->getRouteMatch ()->setParam ( 'controller', 'Application\Controller\Auth' );
            }
    }
     protected function createDbAdapter(MvcEvent $e)
    {
    $config = $e->getApplication()->getConfig();

    $adapter = new Adapter($config['db']);
    GlobalAdapterFeature::setStaticAdapter($adapter);

 }
   
    public function loggingError()
    {
        $logger = new Logger();
        $logDir = dirname(dirname(__DIR__)). '/log/error/'. date('Y-m-d');
        $writer = new Stream($logDir);
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
        /**
         * get configuration 
         */
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
                'formElementErrors' => 'Application\Helper\FormElementErrors',

            ),
        );
    }
    
  
}
