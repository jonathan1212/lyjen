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
use Zend\View\Model\ViewModel;

class FailedController extends AbstractActionController
{
    /**
     * for common error
     */
    public function indexAction()
    {
        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());
        if ($message) {
            $values = array(
                'message' => $message,
            );
        }
        else {
            $values = array();
        }
        $this->layout('error/layout');
        $view = new ViewModel($values);
        $view->setTemplate('/error/index.phtml');
        return $view;
    }

    /**
     * for 404 error
     */
    public function notFoundAction()
    {
        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());
        if ($message) {
            $values = array(
                'message' => $message
            );
        }
        else {
            $values = array();
        }
        $this->layout('error/layout');
        $view = new ViewModel($values);
        $view->setTemplate('/error/404.phtml');
        return $view;
    }

    /**
     * for 403 error
     */
    public function forbiddenAction()
    {
        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());
        if ($message) {
            $values = array(
                'message' => gv('0', $fm->getMessages())
            );
        }
        else {
            $values = array();
        }
        $this->layout('error/layout');
        $view = new ViewModel($values);
        $view->setTemplate('/error/403.phtml');
        return $view;
    }
}