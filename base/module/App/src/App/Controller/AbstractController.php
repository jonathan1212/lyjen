<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Controller;
use Zend\Mvc\Controller\AbstractActionController;

class AbstractController extends AbstractActionController
{
    protected $translator;
    protected $ctrl;

    /**
     * common
     */
    public function init()
    {
        // setting controller
        if (!$this->getCtrl()) {
            $this->setCtrl($this->params()->fromRoute('__CONTROLLER__'));
        }

        $user_info = $this->auth()->getUserInfo();
        $this->layout()->setVariables($user_info);
        $this->ctrlLv = $this->auth()->getRoleLevel($this->getCtrl());
    }

    /**
     * setting controller name
     * @param string $_ctrl
     * @return boolean
     */
    public function setCtrl($_ctrl)
    {
        if (!$_ctrl) {
            return false;
        }
        $this->ctrl = (string) $_ctrl;
    }

    /**
     * get controller name
     * @return $this->ctrl
     */
    public function getCtrl()
    {
        return $this->ctrl;
    }

    /**
     * when specifying action unknown
     * @return ViewModel
     */
    public function notFoundAction()
    {
        return $this->redirect()->toRoute('app', array(
            'controller' => 'failed', 'action' => 'not-found'
        ));
    }

    /**
     * when action omitted
     * @return lsitAction();
     */
    public function indexAction()
    {
        return $this->listAction();
    }
}
