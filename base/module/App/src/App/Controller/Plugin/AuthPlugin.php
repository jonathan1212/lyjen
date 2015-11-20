<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use App\Controller\Plugin\ContainerPlugin;
use App\Model\Entity\ControllerEntity;

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
            $this->container = new ContainerPlugin();
            $this->container->setContainer('user_auth');
            $this->setUserInfo();
        }
    }

    protected function setUserInfo()
    {
        $this->user_no = (int) $this->container->get('user_no');
        $this->user_name = (string) $this->container->get('user_name');
        $this->branch_no = (int) $this->container->get('branch_no');
        $this->branch_name = (string) $this->container->get('branch_name');
        $this->timezone = (string) $this->container->get('timezone');
        $this->lang_id = (string) $this->container->get('lang_id');
        $this->resource_id = (string) $this->container->get('resource_id');
        $this->approver = (int) $this->container->get('approver');
        $this->admin = (int) $this->container->get('admin');
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

    /**
     * get each property by array
     * @return array
     */
    public function getUserInfo()
    {
        return array(
            'user_no' => $this->user_no,
            'user_name' => $this->user_name,
            'branch_no' => $this->branch_no,
            'branch_name' => $this->branch_name,
            'timezone' => $this->timezone,
            'lang_id' => $this->lang_id,
            'resource_id' => $this->resource_id,
            'approver' => $this->approver,
            'admin' => $this->admin,
        );
    }

    /**
     * is log in
     * @return boolean
     */
    public function isLogin()
    {
        return $this->user_no ? true : false;
    }

    /**
     * get auth level
     * @param string $_ctrl_name : controller name
     * @return int|boolean
     */
    public function getRoleLevel($_ctrl_name)
    {
        if (!$this->user_no) {
            return false;
        }
        // if user have admin, allow all
        else if ($this->admin) {
            return 4;
        }

        $ctrl = new ControllerEntity();
        return $ctrl->getRoleLevel($this->user_no, $_ctrl_name);
    }
}
