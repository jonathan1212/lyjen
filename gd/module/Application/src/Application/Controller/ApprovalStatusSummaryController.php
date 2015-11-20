<?php 

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class ApprovalStatusSummaryController extends AbstractActionController{

	public function indexAction()
	{
		$this->flashMessenger()->addInfoMessage("Approval Summary Status");
		$user_info =  $this->authPlugin()->getUserInfo();
		return new ViewModel(array('userInfo'=>$user_info));
	}
}