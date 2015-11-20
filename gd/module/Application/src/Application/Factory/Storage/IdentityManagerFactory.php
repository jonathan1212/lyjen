<?php
namespace Application\Factory\Storage;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Storage\IdentityManager;

class IdentityManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authService = $serviceLocator->get('AuthService');
        $dbAdapter  = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        
        $identityManager = new IdentityManager($authService,$dbAdapter);

        return $identityManager;
    }
}
