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
use Application\Controller\Plugin\ContainerPlugin;


class AuthPlugin extends AbstractPlugin
{
    protected $container;
    protected $user_no;
    protected $user_name;
    protected $branch_no;
    protected $branch_name;
    protected $timezone;
    protected $lang_id;
    protected $resource_id;
    protected $approver;
    protected $admin;

    /**
     * construct
     */
    public function __construct()
    {
    
        if (!$this->container) {
            $this->container = new containerPlugin();
            $this->container->setContainer('user_auth');
            
        }
    }

    /**
     * get property
     * @param string $_name : property name
     * @return string|boolean
     */
    public function get($_name)
    {
        if (isset($this->$_name)) {
            return $this->$_name;
        }
        return false;
    }

    public function setUserInfo($_info){
    	
    	$this->container->set("userInfo", $_info);
    	
    }
    
    /**
     * get each property by array
     * @return array
     */
    public function getUserInfo()
    {
    	return $this->container->get("userInfo");
    }

    public function destroyContainer(){
    	return $this->container->clean('user_auth');
    }
}
