<?php
namespace Application\Factory\Storage;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Storage\AuthStorage;

class AuthStorageFactory implements FactoryInterface
{

    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $storage = new AuthStorage('gdi_aruze');
        $storage->setServiceLocator($serviceLocator);
        $storage->setDbHandler();

        return $storage;
    }
}
