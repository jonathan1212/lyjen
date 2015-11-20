<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'App\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'app' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/[:controller[/:action[/:id]]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'App\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
            'Session\Storage\DbSessionStorage' => 'Session\Factory\DbStorageFactory',
        ),
    ),
    'translator' => array(
        'locale' => LANG_ID,
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
        'translation_files' => array(
            array(
                'type' => 'phpArray',
                'filename' => ZEND2_RESOURCE . '/languages/' . RESOURCE_DIR . '/Zend_Validate.php',
                'locale' => LANG_ID,
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
        ),
    ),
    'view_manager' => array(
        'base_path' => BASE_URL,
        'display_not_found_reason' => IS_TEST,
        'display_exceptions'       => IS_TEST,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/index'         => __DIR__ . '/../view/' . VIEW_DIR . '/layout/index.phtml',
            'layout/index'         => __DIR__ . '/../view/' . VIEW_DIR . '/layout/login.phtml',
         //   'layout/layout'        => __DIR__ . '/../view/' . VIEW_DIR . '/layout/default.phtml',
            'layout/layout'        => __DIR__ . '/../view/' . VIEW_DIR . '/layout/custom.phtml',
            'error/layout'         => __DIR__ . '/../view/' . VIEW_DIR . '/layout/error.phtml',
            'error/404'            => __DIR__ . '/../view/error/404.phtml',
            'error/index'          => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
    		 'ViewJsonStrategy',
    	),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
