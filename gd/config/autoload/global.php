<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    // ...
		'db' => array(
				'driver'         => 'Pdo',
				'dsn'            => 'mysql:dbname=gdi;host=localhost',
				'driver_options' => array(
						PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
				),
		),
		'navigation' => array(
				'default' => array(
						array(
								'label' => 'Home',
								'route' => 'home',
								'class' => 'fa-home',
						),
						array(
								'label' => 'Product Information List',
								'route' => 'product-information',
								'class' => ' fa-sitemap',
						),
						array(
								'label' => 'Approval Status Summary',
								'route' => 'approval-status-summary',
								'class' => 'fa-thumbs-up',
						),
						array(
								'label' => 'Marketing Roadmap',
								'route' => 'marketing-roadmap',
								'class' => 'fa-map-signs',
						),
						array(
								'label' => 'Master Schedule',
								'route' => 'master-schedule',
								'class' => 'fa-calendar-check-o',
						),
						array(
								'label' => 'Input Product Informtion',
								'route' => 'input-product-information',
								'class' => 'fa-keyboard-o',
						),
						array(
								'label' => 'Approved Publication',
								'route' => 'approved-publication',
								'class' => 'fa-flag-checkered ',
						),
						array(
								'label' => 'Management Menu',
								'route' => 'management-menu',
								'class' => 'fa-users',
						),
						array(
								'label' => 'My Page',
								'route' => 'my-page',
								'class' => 'fa-user',
						),
                                                 array(
								'label' => 'Branch',
								'route' => 'branch',
								'class' => 'fa-user',
						),
                                                array(
								'label' => 'Platform',
								'route' => 'platform',
								'class' => 'fa-user',
						),
                                                array(
								'label' => 'Jurisdiction',
								'route' => 'jurisdiction',
								'class' => 'fa-user',
						),
                                                array(
								'label' => 'Game Category',
								'route' => 'game-category',
								'class' => 'fa-user',
						),
                                                array(
								'label' => 'Game Group',
								'route' => 'game-group',
								'class' => 'fa-user',
						),
                                                array(
								'label' => 'Market',
								'route' => 'market',
								'class' => 'fa-user',
						),
						array(
								'label' => 'Logout',
								'route' => 'auth',
								'action'=> 'logout',
								'class' => 'fa-power-off',
								'id'=>'logout-btn'
						),
				),
		),
		'service_manager' => array(
				'factories' => array(
						'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
						'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
				),
		),
		
);