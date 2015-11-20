<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Session\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Session\Storage\DbSessionStorage;

class DbStorageFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $ServiceLocator)
    {
        $config = $ServiceLocator->get('Config');
        $request = $ServiceLocator->get('Request');
        return new DbSessionStorage(gv('session_config', $config), $request);
    }
}