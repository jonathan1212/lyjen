<?php
namespace Application\Factory\Controller;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Controller\AuthController;


class AuthControllerServiceFactory implements FactoryInterface{

    public function createService (ServiceLocatorInterface $serviceLocator){
        $identityManager = $serviceLocator->getServiceLocator()->get('IdentityManager');
        return new AuthController($identityManager);
    }
}
