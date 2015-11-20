<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

class ContainerPlugin extends AbstractPlugin
{
    protected $container;

    /**
     * construct
     */

    /**
     * set new Container
     * @param string $_name : container name (if null use controller name)
     */
    public function setContainer($_name = null)
    {
        if (!$_name && !$this->container) {
            $e = $this->getController()->getEvent();
            $name = $e->getRouteMatch()->getParam('__CONTROLLER__', 'index');
            $name = strtolower($name);
            if (preg_match('/(-)/', $name)) {
                $name = str_replace('-', '_', $name);
            }
            $this->container = new Container($name);
        }
        else if ($_name) {
            $this->container = new Container(strtolower($_name));
        }
    }

    /**
     * set key and value to container
     * @param string $_key : key
     * @param string $_value: value
     * @return
     */
    public function set($_key, $_value)
    {
        if (!$_key) {
            return false;
        }
        if (!$this->container) {
            $this->setContainer();
        }
        $this->container->$_key = $_value;
    }

    /**
     * get key of container
     * @param string $_key : key
     * @return string
     */
    public function get($_key)
    {
        if (!$_key) {
            return false;
        }
        if (!$this->container) {
            $this->setContainer();
        }
        return $this->container->$_key;
    }

    /**
     * set null to key
     * @param string $_key : key
     * @return
     */
    public function clear($_key)
    {
        if (!$_key) {
            return false;
        }
        if (!$this->container) {
            $this->setContainer();
        }
        $this->container->$_key = null;
    }

    /**
     * delete container
     * @param string $_name : target container name
     */
    public function clean($_name)
    {
        if (!$this->container) {
            $this->setContainer($_name ? $_name : '');
        }
        $this->container->getManager()->getStorage()->clear($_name);
    }
}
