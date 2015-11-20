<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Storage\IdentityManagerInterface;

class InputProductInformationController extends AbstractActionController{
	
	
    public function indexAction()
    {
    	
       $user_info =  $this->authPlugin()->getUserInfo();
		return new ViewModel(array('userInfo'=>$user_info));
    }
}