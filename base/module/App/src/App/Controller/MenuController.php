<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Controller;
use Zend\View\Model\ViewModel;
use App\Controller\AbstractController;
use App\Model\Entity\PageEntity;

class MenuController extends AbstractController
{
    public function __construct()
    {
    }

    public function indexAction()
    {
        return $this->topAction();
    }

    /**
     * menu
     * @return ViewModel
     */
    public function topAction()
    {
        $user_info = $this->auth()->getUserInfo();
        $this->layout()->setVariables($user_info);

        $page = new PageEntity();
        if (!$this->auth()->get('admin')) {
            $rows = $page->db()->getMenuList($this->auth()->get('user_no'), IS_SP);
        }
        else {
            $rows = $page->db()->getAllMenu(IS_SP);
        }

        $values = array(
            'rows' => $rows,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/menu/top.phtml');
        return $view;
    }

    
}
