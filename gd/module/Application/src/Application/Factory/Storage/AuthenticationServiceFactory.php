<?php
namespace Application\Factory\Storage;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class AuthenticationServiceFactory implements FactoryInterface
{

    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'm_user', 
                'email_address', 'password', 'MD5(?)');

        $authService = new AuthenticationService($serviceLocator->get('AuthStorage'), $dbTableAuthAdapter);

        return $authService;
    }
}
