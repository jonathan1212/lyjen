<?php
namespace Application\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Storage\IdentityManagerInterface;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController{
	protected $identityManager;
	public function __construct (IdentityManagerInterface $identityManager)
	{
		$this->identityManager = $identityManager;
	}
	
    public function indexAction(){
    	if($this->identityManager->hasIdentity()){
    		return $this->redirect()->toRoute('home');
    	}
    	
    	$this->layout('layout/login');
    	//get the form 
    	$form = $this->getServiceLocator()
    	->get('FormElementManager')
    	->get('Application\Form\Auth\AuthForm');
    
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setData($request->getPost());
    		
    		if ($form->isValid()) {
    			$dataform = $form->getData();
    			$result = $this->identityManager->login($dataform['username'],$dataform['password']);
    			if ($result->isValid()) {
    				// TODO:reset account login attemp
    				$identityRow = $this->identityManager->getAuthService()
    				->getAdapter()
    				->getResultRowObject();
    				
    				$userDetails = $this->identityManager->getUserDetails($identityRow->user_id);
    				$this->authPlugin()->setUserInfo($userDetails);
    				$this->identityManager->storeIdentity(
    						array(
    								'id' => $userDetails['user_id'],
    								'ip_address' => $this->identityManager->getCurrentIp(),
    								'user_agent' => $request->getServer(
    										'HTTP_USER_AGENT')
    						));
    				
    				return $this->redirect()->toRoute('home',
    						array(
    								'action' => 'index'
    						));
    				
    			}else{
    				// code: -3 means correct username but wrong password
    				// http://framework.zend.com/apidoc/2.4/classes/Zend.Authentication.Result.html#constant_FAILURE_CREDENTIAL_INVALID
    				$messages = "";
    				//harvest the messages 
    				foreach ($result->getMessages() as $message) {
    					$this->flashMessenger()->addErrorMessage($message);
    				}
    				
    				 //set to flashmessenger 
    				// display proper warning
    				if($result->getCode() == "-3"){
    					//TODO: -3 mean correct username but wrong password
    					// in this case increment the login attempt 
    					//$this->identityManager->incrementLoginAttempt($dataform['username']);
    				}
    			
    				return $this->redirect()->toRoute('auth',array('action'=>'index'));
    			}
    			
    		}
    	}
    
       return array('form'=>$form);
      
    }
    
    public function logoutAction (){
     $this->layout('layout/login');
     
     $form = $this->getServiceLocator()
     ->get('FormElementManager')
     ->get('Application\Form\Auth\LogoutForm');
     
     $request = $this->getRequest();
     if($request->isPost()){
      $yes = $request->getPost('LogoutYes', 'No');
      
      if($yes == 'Yes'){
       $this->identityManager->logout();
       $this->authPlugin()->destroyContainer();
       $this->flashMessenger()->addInfoMessage("You have been logged out from the system");
       return $this->redirect()->toRoute('auth');
       
      }else{
       return $this->redirect()->toRoute('home');
      }
     
     }
     
     return array('form'=>$form);
    
    }
    
    public function forgotPasswordAction(){
    	$this->layout('layout/login');
    	return array('form'=>'Forgot password ?');
    }
    
    
}
