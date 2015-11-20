<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'Batch\Controller\Log' => 'Batch\Controller\LogController',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'console' => array(
                    'options' => array(
                        'route' => 'log_rotate',
                        'defaults' => array(
                            'controller' => 'Batch\Controller\Log',
                            'action' => 'rotate'
                        ),
                    ),
                ),
            ),
        ),
    ),
);
