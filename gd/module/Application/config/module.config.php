<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array (
		'router' => array (
				'routes' => array (
						'home' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/',
										'defaults' => array (
												'controller' => 'Application\Controller\Index',
												'action' => 'index' 
										) 
								) 
						),
						// The following is a route to simplify getting started creating
						// new controllers and actions without needing to create a new
						// module. Simply drop new controllers in, and you can access them
						// using the path /application/:controller/:action
						'application' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/application',
										'defaults' => array (
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Index',
												'action' => 'index' 
										) 
								),
								'may_terminate' => true,
								'child_routes' => array (
										'default' => array (
												'type' => 'Segment',
												'options' => array (
														'route' => '/[:controller[/:action[/:id]]]',
														'constraints' => array (
																'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
																'action' => '[a-zA-Z][a-zA-Z0-9_-]*' 
														),
														'defaults' => array () 
												) 
										) 
								) 
						),
						'auth' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/auth[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Auth',
												'action' => 'index' 
										) 
								) 
						),
						'product-information' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/product-information[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ProductInformation',
												'action' => 'index' 
										) 
								) 
						),
						'my-page' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/my-page[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\MyPage',
												'action' => 'index' 
										) 
								) 
						),
						'approval-status-summary' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/approval-status-summary[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ApprovalStatusSummary',
												'action' => 'index'
										)
								)
						),
						'marketing-roadmap' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/marketing-roadmap[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\MarketingRoadmap',
												'action' => 'index'
										)
								)
						),
						'master-schedule' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/master-schedule[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\MasterSchedule',
												'action' => 'index'
										)
								)
						),
						'input-product-information' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/input-product-information[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\InputProductInformation',
												'action' => 'index'
										)
								)
						),
						'approved-publication' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/approved-publication[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ApprovedPublication',
												'action' => 'index'
										)
								)
						),
						'management-menu' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/management-menu[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ManagementMenu',
												'action' => 'index'
										)
								)
						),
                                                'branch' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/branch[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Branch',
												'action' => 'index'
										)
								)
						),  
                                                'platform' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/platform[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Platform',
												'action' => 'index'
										)
								)
						), 
                                                'jurisdiction' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/jurisdiction[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Jurisdiction',
												'action' => 'index'
										)
								)
						), 
                                                'game-category' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/game-category[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\GameCategory',
												'action' => 'index'
										)
								)
						),  
                                                'game-group' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/game-group[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\GameGroup',
												'action' => 'index'
										)
								)
						),
                                                'market' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/market[/:action][/:id]',
										'constraints' => array (
												'action' => '[a-z][a-z0-9_-]*',
												'id' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Market',
												'action' => 'index'
										)
								)
						),  
				) 
		),
		'service_manager' => array (
				'abstract_factories' => array (
						'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
						'Zend\Log\LoggerAbstractServiceFactory' 
				),
				'factories' => array (
						'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
						'AuthStorage' => 'Application\Factory\Storage\AuthStorageFactory',
						'AuthService' => 'Application\Factory\Storage\AuthenticationServiceFactory',
						'IdentityManager' => 'Application\Factory\Storage\IdentityManagerFactory',
                                                'Session\Storage\DbSessionStorage' => 'Session\Factory\DbStorageFactory',
				)
				 
		),
    
		'translator' => array (
				'locale' => 'ja_JP',
				'translation_file_patterns' => array (
						array (
								'type' => 'gettext',
								'base_dir' => __DIR__ . '/../language',
								'pattern' => '%s.mo' 
						) 
				) 
		),
		'controllers' => array (
				'invokables' => array (
						'Application\Controller\Index' => 'Application\Controller\IndexController',
						'Application\Controller\ProductInformation' => 'Application\Controller\ProductInformationController',
						'Application\Controller\MyPage' => 'Application\Controller\MyPageController',
						'Application\Controller\ApprovalStatusSummary' => 'Application\Controller\ApprovalStatusSummaryController',
						'Application\Controller\MarketingRoadmap' => 'Application\Controller\MarketingRoadmapController',
						'Application\Controller\MasterSchedule' => 'Application\Controller\MasterScheduleController',
						'Application\Controller\InputProductInformation' => 'Application\Controller\InputProductInformationController',
						'Application\Controller\ApprovedPublication' => 'Application\Controller\ApprovedPublicationController',
						'Application\Controller\ManagementMenu' => 'Application\Controller\ManagementMenuController',
                                                'Application\Controller\Branch' => 'Application\Controller\BranchController',
                                                'Application\Controller\Platform' => 'Application\Controller\PlatformController',
                                                'Application\Controller\Jurisdiction' => 'Application\Controller\JurisdictionController',
                                                'Application\Controller\GameCategory' => 'Application\Controller\GameCategoryController',
                                                'Application\Controller\GameGroup' => 'Application\Controller\GameGroupController',
                                                'Application\Controller\Market' => 'Application\Controller\MarketController'
						
						
				) 
				,
				'factories' => array (
						'Application\Controller\Auth' => 'Application\Factory\Controller\AuthControllerServiceFactory' 
				) 
		),
		'controller_plugins' => array (
				'invokables' => array (
						'AuthPlugin' => 'Application\Controller\Plugin\AuthPlugin',
                                                'search' => 'Application\Controller\Plugin\SearchPlugin'
				),
				'factories' => array ()

				 
		),
		'view_manager' => array (
				'display_not_found_reason' => true,
				'display_exceptions' => true,
				'doctype' => 'HTML5',
				'not_found_template' => 'error/404',
				'exception_template' => 'error/index',
				'template_map' => array (
						'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
						'layout/login' => __DIR__ . '/../view/layout/loginLayout.phtml',
						'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
						'flashmessenger/layout' => __DIR__ . '/../view/flashmessenger/layout.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml',
						'application/paginator' => __DIR__ . '/../view/partial/paginator.phtml',
						'application/headermenuLayout' => __DIR__ . '/../view/partial/headerMenuLayout.phtml' 
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		),
		// Placeholder for console routes
		'console' => array (
				'router' => array (
						'routes' => array () 
				) 
		) 
);
