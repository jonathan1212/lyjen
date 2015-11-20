<?php 

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class MasterScheduleController extends AbstractActionController{

	public function indexAction()
	{
		$user_info =  $this->authPlugin()->getUserInfo();
		return new ViewModel(array('userInfo'=>$user_info));
	}
}