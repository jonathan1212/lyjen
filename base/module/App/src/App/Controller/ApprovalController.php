<?php
/**
 *
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Controller;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Hydrator\ObjectProperty;
use App\Controller\AbstractController;
use App\Model\Entity\ApprovalEntity;
use App\Filter\ApprovalFilter;

use Zend\Http\Headers;
use Zend\Http\Response\Stream;
use App\Model\RingiDocument;
use App\Model\Table\ApprovalTable;
use App\Model\Table\UserTable;
use App\Model\Table\RequestNumberTable;
use App\Model\Table\BranchTable;
use App\Model\Table\TplDocumentTable;
use App\Model\Table\DecisionTable;
use Zend\Form\Fieldset;

use App\Form\DecisionForm;
use App\Form\ApprovalForm;
use App\Filter\DecisionFilter;
use Zend\Db\ResultSet\ResultSet;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;
/**ZendPDF stuffs : end**/

class ApprovalController extends AbstractController
{
    
 protected  $db;
 
    public function __construct(){
           
    }
    
   
    public function listAction(){
        
       
        if(!$this->auth()->get('approver')){
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'approval', 'action' => 'progressing'
            ));
            
        }
    	$this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        
        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());
        $user_no =  (int)$this->auth()->get('user_no');

        $param = $this->search()->getSearchParam();
        if ($param) {
            $this->container()->set('search', json_encode($param));
        }
        else {
            $param = $this->container()->get('search');
            $param = json_decode($param, true);
        }
        

        $form = new ApprovalForm();
        
       //$branch_no = gv('search-branch_no', $param);
        $form->setListForm();
       if ($param) {
            $form->bind(get_array_object($param));
        } 

        $form->get('submit')->setAttribute('value', 'Search');

        $where = $this->search()->getSearchParamConv($param);
        if (4 > $this->ctrlLv) {
            $where = array_merge($where, array('deleted' => 0));
        }

        $order = $this->search()->getOrder();
        $page = $this->search()->getPage();
        $max = $this->search()->getDisplayNum();
                
        $db = new ApprovalEntity();
        $page = $db->db()->getPageList($user_no,$where, $order, $page, $max);
        
        $values = array(
            'ctrlLv' => $this->ctrlLv,
            'admin' => $this->auth()->get('admin'),
            'approver'=>$this->auth()->get('approver'),
            'rows' => $page->getCurrentItems()->toArray(),
            'page' => $page->getPages(),
            'form' => $form,
            'branch_no' => $this->auth()->get('branch_no'),
            'message' => $message,
   
        );
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/approval/receiving-tab.phtml');
        return $view;
    }
    
   
    public function detailsAction(){
        
        $decision_no = (int) $this->params()->fromRoute('id', 0);
        if (!$decision_no) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        
        $this->init();
        $fm = $this->flashMessenger();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $fm = $this->flashMessenger();
        $decisionTable = new DecisionTable();
        $loggedin_user_no = $this->auth()->get('user_no');
        try{
             $decisionDetails = $decisionTable->getApprovalDetails($decision_no,$loggedin_user_no);
             $approversList  = $decisionTable->getApproverList($decisionDetails['decision_no'],$this->auth()->get('user_no'));
             $approvalAttachments = $decisionTable->getApprovalAttachments($decision_no);
           
             
        }catch (\Exception $e){
            $this->flashMessenger()->addMessage("Fatal Error! ".$e->getMessage());
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
  
        
        $continue = false;
        
       
        
        foreach ($approversList as $approver){
           if($approver["user_no"] == $loggedin_user_no || 
                   $loggedin_user_no == $decisionDetails['create_user']){
                $continue = true;
                break;
           }
            
        }
        
       if(!$continue){
            $this->flashMessenger()->addMessage(
                    "You must be 1 of its approver in order to view this resource..");
            return $this->redirect()->toRoute('app', 
                    array(
                            'controller' => 'failed',
                            'action' => 'forbidden'
                    ));
        }
        
        $form = new ApprovalForm();
        $form->setApprovalDetailsForm();
        $success = false;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            $filter = new ApprovalFilter();
            
            $filter->setDetailsFilter();
            $form->setInputFilter($filter->getInputFilter());
            
            if ($form->isValid()) {
                $parram = $this->params();
                $mail = new Message();
                $mail->setEncoding("UTF-8");
                
                if ($parram->fromPost('approve') != null) {
                    $success = $decisionTable->approveRequest($decision_no, 
                    $this->auth()->get('user_no'), $this->params()->fromPost('remarks'));

                    $approver = $decisionTable->getCurrentApprover($decision_no);
                    $progress = $decisionTable->isCompleted($decision_no);

                    if($progress){       
                        $mail->addFrom(AUTO_MAIL_FROM)
                             ->addTo($decisionDetails['email'])
                             ->setSubject("Request Completed - " .$decisionDetails['decision_title']);
                        $body_tpl = "Your Request has been approved.";
                        $body_tpl .= "\nFor more details, just click the link below";
                        $body_tpl .= "\n\n\n Reference no.: ".$decisionDetails['ref_no'];
                        $body_tpl .= "\n Request: " .$decisionDetails['decision_title'];
                        $body_tpl .= "\n" . "URL:" . BASE_URL . "/approval/details/" . $decision_no;
                        $body_tpl .= "\n\n *** This is an automatically generated email, please do not reply ***";
                        $mail->setBody($body_tpl);
                    }
                    else{
                        $mail->addFrom(AUTO_MAIL_FROM)
                             ->addTo($approver['email'])
                             ->setSubject("Approval Request - " .$decisionDetails['decision_title']);

                        $body_tpl = file_get_contents(APP_DIR . '/tpl/remind-request.txt');
                        $body_tpl = trim(str_replace("\r\n", "\n", $body_tpl));
                        $body_tpl = $this->translator()->translate($body_tpl);
                        $body_tpl .= "\n\n\n Reference no.: ".$decisionDetails['ref_no'];
                        $body_tpl .= "\n Request: " .$decisionDetails['decision_title'];
                        $body_tpl .= "\n" . "URL:" . BASE_URL . "/approval/details/" . $decision_no;
                        $body_tpl .= "\n\n *** This is an automatically generated email, please do not reply ***";
                        $mail->setBody($body_tpl);
                        }                       
                } else 
                    if ($parram->fromPost('reject') != null) {
                        $success = $decisionTable->rejectRequest($decision_no, 
                        $this->auth()->get('user_no'),$this->params()->fromPost('remarks'));
                        
                        $mail->addFrom(AUTO_MAIL_FROM)
                        ->addTo($decisionDetails['email'])
                        ->setSubject("Request Rejected - " .$decisionDetails['decision_title']);

                        $cc_approver = $decisionTable->getApprovers($decision_no);
                        foreach ($cc_approver as $each_recipient){
                            $mail->addCc($each_recipient['email']);
                        }
                        $body_tpl = "Request has been rejected by " .$this->auth()->get('user_name');
                        $body_tpl .= "\n\n\n Reference no.: " .$decisionDetails['ref_no'];
                        $body_tpl .= "\n Request: " .$decisionDetails['decision_title'];
                        $body_tpl .= "\n\n " .$this->auth()->get('user_name').":".$this->params()->fromPost('remarks')."\n\n";
                        $body_tpl .= "\n\n *** This is an automatically generated email, please do not reply ***";
                        $mail->setBody($body_tpl);    
                        
                    } else 
                        if ($parram->fromPost('recall') != null) {
                            $success = $decisionTable->recallRequest($decision_no, 
                                $this->auth()
                                    ->get('user_no'), 
                                $this->params()
                                    ->fromPost('remarks'));
                            $nextApprover = $decisionTable->nextApprover($decision_no);
                            $mail->addFrom(AUTO_MAIL_FROM)
                                 ->addCc($nextApprover['email'])
                                 ->addBcc($decisionDetails['email']) //will remove this after verifying the function req
                                 ->setSubject("Approval Recall - ".$decisionDetails['decision_title']);
                            
                            $recipients = $decisionTable->getApprovers($decision_no);
                            foreach ($recipients as $each_recipient){
                                $mail->addTo($each_recipient['email']);
                            }
                            $body_tpl = "Request has been recalled by " .$this->auth()->get('user_name') ." san";
                            $body_tpl .= "\n\n\n Reference no.: " .$decisionDetails['ref_no'];
                            $body_tpl .= "\n Subject: " .$decisionDetails['decision_title'];
                            $body_tpl .= "\n\n ".$this->auth()->get('user_name').":".$this->params()->fromPost('remarks')."\n\n";
                            $body_tpl .= "\n\n *** This is an automatically generated email, please do not reply ***";
                            $mail->setBody($body_tpl);
                        }
                    else 
                        if ($parram->fromPost('recall-by-owner') != null) {
                            $success = $decisionTable->recallByOwner($decision_no, 
                            $this->auth()
                                ->get('user_no'), 
                            $this->params()
                                ->fromPost('remarks'));

                            $mail->addFrom(AUTO_MAIL_FROM)
                                 ->setSubject("Approval Recall by Requestor - " .$decisionDetails['decision_title']);

                            $recipients = $decisionTable->getApprovers($decision_no);

                            foreach ($recipients as $each_recipient){
                                $mail->addTo($each_recipient['email']);
                            }
                            $body_tpl = "Request has been recalled by applicant - " .$decisionDetails['user_name'];
                            $body_tpl .= "\n\n\n Reference no.: " .$decisionDetails['ref_no'];
                            $body_tpl .= "\n Subject: " .$decisionDetails['decision_title'];
                            $body_tpl .= "\n\n " .$decisionDetails['user_name'].":".$this->params()->fromPost('remarks')."\n\n";
                            $body_tpl .= "\n\n *** This is an automatically generated email, please do not reply ***";
                            $mail->setBody($body_tpl);
                        }

            if ($success) {
                $transport = new SmtpTransport();
                $options   = new SmtpOptions(array(
                    'host'              => 'smtp.gmail.com',
                    'connection_class'  => 'plain',
                    'connection_config' => array(
                        'ssl'       => 'tls',
                        'username' => AUTO_MAIL_FROM,
                        'password' => AUTO_MAIL_FROM_PASSWORD,
                    ),
                    'port' => 587,
                ));

                $transport->setOptions($options);
               $success = $transport->send($mail);
           }
           else if ($success && IS_TEST) {
               $logger = new \Zend\Log\Logger();
               $writer = new \Zend\Log\Writer\Stream(APP_DIR . '/log/debug.txt');
               $logger->addWriter($writer);
               $logger->log(\Zend\Log\Logger::DEBUG, print_r($mail, 1));
           }
                return $this->redirect()->toRoute('app', 
                        array(
                                'controller' => 'approval',
                                'action' => 'details',
                                'id' => $decision_no
                        ));
            }
     
       }
       
       $values = array(
                'ctrlLv' => $this->ctrlLv,
                'admin' => $this->auth()->get('admin'),
                'user_log' =>$loggedin_user_no,
                'approver'=>$this->auth()->get('approver'),
                'decisionDetails'=>$decisionDetails,
                'approversList'=>$approversList,
                'approvalAttachments'=>$approvalAttachments,
                'form'=>$form,
                'id'=>$decision_no
        );
       
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/approval/details.phtml');
        return $view;
    }
    
    
    public function progressingAction(){
        $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
       
        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());
        $user_no =  (int)$this->auth()->get('user_no');

        $param = $this->search()->getSearchParam();
        if ($param) {
            $this->container()->set('search', json_encode($param));
        }
        else {
            $param = $this->container()->get('search');
            $param = json_decode($param, true);
        }
        

        $form = new ApprovalForm();
        $form->setListForm();
    /**    if ($param) {
            $form->bind(get_array_object($param));
        }**/
        $form->get('submit')->setAttribute('value', 'Search');

        $where = $this->search()->getSearchParamConv($param);
        if (4 > $this->ctrlLv) {
            $where = array_merge($where, array('deleted' => 0));
        }

        $order = $this->search()->getOrder();
        $page = $this->search()->getPage();
        $max = 5;
      
                
        $db = new ApprovalEntity();
        $page = $db->db()->getProgressList($user_no,$where, $order, $page, $max);
        
        $values = array(
            'ctrlLv' => $this->ctrlLv,
            'admin' => $this->auth()->get('admin'),
            'approver'=>$this->auth()->get('approver'),
            'rows' => $page->getCurrentItems()->toArray(),
            'page' => $page->getPages(),
            'form' => $form,
            'branch_no' => $this->auth()->get('branch_no'),
            'message' => $message,
   
        );
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/approval/progressing-tab.phtml');
        return $view;
        
    }
    
    
    
    public function completeAction(){
    $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
       
        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());
        $user_no =  (int)$this->auth()->get('user_no');

        $param = $this->search()->getSearchParam();
        if ($param) {
            $this->container()->set('search', json_encode($param));
        }
        else {
            $param = $this->container()->get('search');
            $param = json_decode($param, true);
        }
        

        $form = new ApprovalForm();
        $form->setListForm();
        /**if ($param) {
            $form->bind(get_array_object($param));
        }**/
        $form->get('submit')->setAttribute('value', 'Search');

        $where = $this->search()->getSearchParamConv($param);
        if (4 > $this->ctrlLv) {
            $where = array_merge($where, array('deleted' => 0));
        }

        $order = $this->search()->getOrder();
        $page = $this->search()->getPage();
        $max = $this->search()->getDisplayNum();
      
                
        $db = new ApprovalEntity();
        $page = $db->db()->getCompletedList($user_no,$where, $order, $page, $max);
        
        $values = array(
            'ctrlLv' => $this->ctrlLv,
            'admin' => $this->auth()->get('admin'),
            'approver'=>$this->auth()->get('approver'),
            'rows' => $page->getCurrentItems()->toArray(),
            'page' => $page->getPages(),
            'form' => $form,
            'branch_no' => $this->auth()->get('branch_no'),
            'message' => $message,
   
        );
       
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/approval/complete-tab.phtml');
        return $view;
    }
    
    
    
    public function draftAction(){
        $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
       
        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());
        $user_no =  (int)$this->auth()->get('user_no');

        $param = $this->search()->getSearchParam();
        if ($param) {
            $this->container()->set('search', json_encode($param));
        }
        else {
            $param = $this->container()->get('search');
            $param = json_decode($param, true);
        }
        

        $form = new ApprovalForm();
        
        $form->setListForm();
        /**if ($param) {
            $form->bind(get_array_object($param));
        }**/
        $form->get('submit')->setAttribute('value', 'Search');

        $where = $this->search()->getSearchParamConv($param);
        if (4 > $this->ctrlLv) {
            $where = array_merge($where, array('deleted' => 0));
        }

        $order = $this->search()->getOrder();
        $page = $this->search()->getPage();
        $max = $this->search()->getDisplayNum();

                
        $db = new ApprovalEntity();
        $page = $db->db()->getDraftList($user_no,$where, $order, $page, $max);
        
        $values = array(
            'ctrlLv' => $this->ctrlLv,
            'admin' => $this->auth()->get('admin'),
            'approver'=>$this->auth()->get('approver'),
            'rows' => $page->getCurrentItems()->toArray(),
            'page' => $page->getPages(),
            'form' => $form,
            'branch_no' => $this->auth()->get('branch_no'),
            'message' => $message,
   
        );
    
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/approval/draft-tab.phtml');
        return $view;
    }
    
    
    
    /**
     * TODO
     */
    public function checkBeforeSwapAction(){
        $this->init();
        
        // check auth
        $success = (1 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }

        $viewJson = new JsonModel();
        $viewJson->setVariables(array('message'=>'success'));
        return $viewJson;
    }
    
    
    public function getApproverProxiesAction(){
        $this->init();
        
        // check auth
        $success = (1 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        
        $viewJson = new JsonModel();
        $user_no = (int)$this->params()->fromRoute('id');
       // $logged_in_user = (int)$this->auth()->get('user_no'); //avoid picking his own rec
        
      //  $user_no = (int)$this->params()->fromPost('user_no');
        if (!$user_no) {
            $viewJson->setVariables(array('message'=>'failed'));
            return $viewJson;
        }
        
        $userTable = new UserTable();
        $proxies = $userTable->getApproverProxies($user_no);
        if(!$proxies){
            $proxies = array('message'=>'failed');
        }
        $filtered_proxies = array();
        
        foreach ($proxies as $k=>$v){
         if($k!= $this->auth()->get('user_no'))
          $filtered_proxies[$k] = $v;
        }
         
        $viewJson->setVariables($filtered_proxies);
        return $viewJson;
    }
    
    
    public function viewTemplateDocAction(){
        $id = (int)$this->params()->fromRoute('id');
        
        if(!$id){
           return;
        }
        
        $tmplDocObj  = new TplDocumentTable();
        $data = $tmplDocObj->select(array('tpl_doc_no'=>$id));
        $template = $data->current();
        $view = new ViewModel(array('template'=>$template));
        $view->setTemplate('/common/view-template-doc.phtml');
        $view->setTerminal(true);
        return $view;
    }
    
    public function CreateAction(){
        
        $this->init();
        
        // check auth
        $success = (1 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        
        $decision_no = (int)$this->params()->fromRoute('id');
        $fm = $this->flashMessenger();
        
        
        if(!$decision_no){
            die("id is required");
        }
        
        $decisionTable = new DecisionTable();
        $form = new DecisionForm();
        $data = array();
        $data['branch_no'] = $this->auth()->get('branch_no');
        $form->setEditForm($data);
        //upload attachment form 
        $formUpload = new ApprovalForm();
        $formUpload->setAttachForm();
       
        //initialize containers
        $approver_csv = null;
        
        try{
            
            $decision = $decisionTable->getUserDecision($decision_no,$this->auth()->get('user_no'));
            $approver_csv = $decisionTable->getApproverList($decision_no, $this->auth()->get('user_no'));
            
          
        }catch (\Exception $e){
            
            $this->flashMessenger()->addMessage("Fatal Error! ".$e->getMessage());
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'index'
            ));
            
        }
        
        $decision_no = (int)$decision['decision_no'];
        
        // redirect to view details if the status of request is already recieving 
        if($decision["status"] == "receiving"){
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'approval', 'action' => 'approval-details',
                    'id'=>$decision_no
            ));
        }
        
        $form->bind($decision);
        // make the submit button change value from submit to apply
        $form->get('submit')->setAttribute('value', 'Apply');
        
        if(!empty($approver_csv)){
            $selectData = array();
            $array_acsv_hidden = array();
         
          foreach ($approver_csv as $res) {
    
             $selectData[$res['user_no']] = $res['user_name']." ( ".$res['position_name']." )";
             $array_acsv_hidden[] =  $res['user_no'];
           }
         
         $form->get('selected_approvers')->setAttribute('options', $selectData);
         $form->get('approvers_csv')->setAttribute('value', implode(",", $array_acsv_hidden));
         
        }   
        $request = $this->getRequest();
        if ($request->isPost()) {

        	$parram = $this->params();
            $approver_csv = $this->params()->fromPost('approvers_csv');
            $attachment_csv = $this->params()->fromPost('file_attachments');
            
            $filter = new DecisionFilter();
            if($parram->fromPost('draft') != null){
            	$filter->setDraftFilter();
            	$form->setInputFilter($filter->getInputFilter());
            }else{
            	$form->setInputFilter($filter->getInputFilter());
            }
            
            
            $form->setData($request->getPost());

            if(!empty($approver_csv))
                $form->updateSelectedApprovers($approver_csv);
            if(!empty($attachment_csv))
                $form->updateAttachments($attachment_csv);
          
          
                if ($form->isValid()) {

                    $decisionTable->exchanegArray($request->getPost());
                    $decision_no = (int)$decisionTable->addDecision($this->auth()
                        ->get('user_no'));

                     if($approver_csv !=""){
                     	if($decisionTable->setUpApprovers($decision_no,$approver_csv)){
                     	
                     		if(!empty($attachment_csv))
                     			$attache_file_handle = $decisionTable->setUpAttachements($decision_no,$attachment_csv);
                     	}
                     }
               
                     //check if draf button is clicked instead of apply button
                     // if draf button edit the decition and set the status to draft
                     
                     if($parram->fromPost('draft') != null){
                     	 
                     	 
                     	$save = (int)$decisionTable->saveDraft($decision_no,$this->auth()
                     			->get('user_no'));
                     	 
                     	 
                     	return $this->redirect()->toRoute('app',
                     			array(
                     					'controller' => 'approval',
                     					'action' => 'draft'
                     			));
                     }
                     
                    $approver = $decisionTable->getCurrentApprover($decision_no);

                    $mail = new Message();
                    $mail->setEncoding("UTF-8");


                    $mail->addFrom(AUTO_MAIL_FROM)
                            ->addTo($approver['email'])
                            ->setSubject($this->translator()->translate("New Approval request"));

                    $body_tpl = file_get_contents(APP_DIR . '/tpl/remind-request.txt');
                    $body_tpl = trim(str_replace("\r\n", "\n", $body_tpl));
                    $body_tpl = $this->translator()->translate($body_tpl);
                    $body_tpl .= "\n\n\n" . "URL:" . BASE_URL . "/approval/details/" . $decision_no;
                    $body_tpl .= "\n\n *** This is an automatically generated email, please do not reply ***";

                    $mail->setBody($body_tpl);

                    $transport = new SmtpTransport();
                    $options   = new SmtpOptions(array(
                        'host'              => 'smtp.gmail.com',
                        'connection_class'  => 'plain',
                        'connection_config' => array(
                            'ssl'       => 'tls',
                            'username' => AUTO_MAIL_FROM,
                            'password' => AUTO_MAIL_FROM_PASSWORD,
                        ),
                        'port' => 587,
                    ));

                    $transport->setOptions($options);
                    $success = $transport->send($mail);
                    
                

                    return $this->redirect()->toRoute('app', 
                            array(
                                    'controller' => 'approval',
                                    'action' => 'progressing'
                            ));
                }
            
        }
        
        
        $value = array('form'=>$form,
                       'upload'=>$formUpload,
                       'id'=>$decision_no,        
        );
        
        
        $view = new ViewModel($value);
        $view->setTemplate('/' . VIEW_DIR . '/approval/edit-draft.phtml');
    
        return $view;
    }
    
	public function addAction(){	    
        $this->init();

        // check auth
        $success = (1 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        
        
        //get user loggedin user branch
        $branchTable = new BranchTable();
        $branch_no = (int)$this->auth()->get('branch_no');
        
        // admin is not allowed to create approval and also with user's with no brach no
        if(!$branch_no){
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        
        $userBranch = $branchTable->getRecord($this->auth()->get('branch_no'));
        
        //generate id
        $rNumber = new RequestNumberTable();
        $id = $rNumber->generateNumber($this->auth()->get('branch_no'));
        $sequence_no = $rNumber->getRecord($id);
        $approval_id = \strtoupper ($userBranch['abbr_name'])."-".date("md")."-".$sequence_no['sequence_no'];

        $form = new ApprovalForm();
        $data['branch_no'] = $this->auth()->get('branch_no');
        $form->setAddForm($data);
        $form->get('ref_no')->setValue($approval_id);
	
		$formUpload = new ApprovalForm();
        $formUpload->setAttachForm();
		
        $db = new ApprovalEntity();
        
        $filter = new ApprovalFilter();
        $request = $this->getRequest();
        // get update page
        if (!$request->isPost()) {
            $postRequest = $this->container()->get('postRequest');
            if ($postRequest) {
                $this->container()->clear('postRequest');
                $row = get_array_object(json_decode($postRequest, true));
                $form->bind($row);
            }
        }
        // get update processing
        // request is post
        else {
            $token_id = $this->container()->get('token_id');
            $this->container()->clear('token_id');
            $filter->setCreateToken($token_id);
            $filter->setInputFilter('add');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            $success = false;
            if ($form->isValid()) {
                // insert prosessing
               echo "FORM IS VALID!";
               exit;
            }
            else {
                // set result to form
                $data = $form->getInputFilter()->getValues();
                $form->bind(get_array_object($data));
            }
            if ($success) {
                $this->flashMessenger()->addMessage("Completed");
//                $this->flashMessenger()->addMessage("処理完了");
                return $this->redirect()->toRoute('app', array(
					'ctrlLv' => $this->ctrlLv,
                    'controller' => 'approval',
                    'action' => 'list',
                ));
            }
        }

        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);
        $form->get('token_id')->setAttribute('value', $token_id);
        $form->get('submit')->setAttribute('value', 'Apply');
//        $form->get('submit')->setAttribute('value', '登録');
        $form->get('draft')->setAttribute('value', 'Draft');
//        $form->get('reset')->setAttribute('value', 'リセット');

        $values = array(
            'id' => $id,
            'approval_id'=>$approval_id,
            'action' => 'add',
            'form' => $form,
            'formUpload' => $formUpload,
            'admin' => $this->auth()->get('admin'),
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/approval/edit.phtml');
        return $view;
    }
    
    
    
    public function createDraftAction(){
        $this->init();
        
        // check auth
        $success = (1 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
       
        $user_no =  (int)$this->auth()->get('user_no');
        $branch_no = (int)$this->auth()->get('branch_no');
        $approval = new ApprovalTable();
        $ref_no  = trim($this->createRefNumber());
        
        try{
            
            $id = (int)$approval->insertDraft($user_no, $ref_no);
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'approval', 'action' => 'create','id'=>$id
            ));
            
        }catch (\Exception $e){
          die($e->getMessage());
        }
        
        
        $response = array('message'=>$id);
        $viewJson = new JsonModel();
        $viewJson->setVariables($response);
        return $viewJson;
    }
    
    
    
    /**
     *  separated to avoid complexity, (Spaghetti Code !!! )
     * @return string
     */
    private function createRefNumber(){
        $branchTable = new BranchTable();
        $userBranch = $branchTable->getRecord($this->auth()->get('branch_no'));
        //generate id
        $rNumber = new RequestNumberTable();
        $id = $rNumber->generateNumber($this->auth()->get('branch_no'));
        $sequence_no = $rNumber->getRecord($id);
        $approval_id = \strtoupper ($userBranch['abbr_name'])."-".date("md")."-".$sequence_no['sequence_no'];
        return $approval_id;
    }
    
    /**
     * 
     * @return Json : {"Option 1": "value1",
        			  "Option 2": "value2",
        			  "Option 3": "value3"
        			  };
     */
        public function createSelectBoxAction(){
        
        $selectData = array('0'=>'select');
        $request = $this->getRequest();
        $user_no = $this->auth()->get('user_no');
        $viewJson = new JsonModel();
        
       if($request->isPost()){
           
            $id = (int)$this->params()->fromPost('id');
            if (! $id) {
                $viewJson->setVariables($selectData);
               return $viewJson();
            }
            
            $dbAdapter = $this->getDb();
            
            $handle = $dbAdapter->query("SELECT
                    t_request_category.id,
                    t_request_category.menu_item_name,
                    t_request_category.parent_id,
                    t_request_category.position_no,
                    m_position.position_name
                    FROM t_request_category 
                    LEFT join m_position
                    ON t_request_category.position_no = m_position.position_no
                    WHERE t_request_category.id = {$id} AND 
                    m_position.approval = 1");
            
            $result = $handle->execute();
            $rows = $result->current();
            $position_no = (int)$rows['position_no'];
            $position_name = $rows['position_name'];
            $counter = 0;
            
            if((int)$position_no > 0){
                $sql = "SELECT m_user.user_no, m_user.user_name 
                                             FROM m_user 
                                             LEFT join m_position
                                             ON m_user.position_no  = m_position.position_no
                                             WHERE m_user.position_no = {$position_no}
                                             AND m_user.user_no  != {$user_no} 
                                             AND  m_position.approval = 1
                                             ORDER BY RAND()"; // avoid picking his own
                
                $handle = $this->getDb()->query($sql);
                $result2 = $handle->execute();
                $rows2 = $result2->current();
           
               
                if(!$rows2) {
                    $selectData = array(
                            'status' => 'failed',
                            'message' => "Currently there's no user with that postion in the system, please assign atleast 1 {$position_name} and then retry "
                    );
                }else{
                    
                    $selectData = array(
                            'status' => 'success',
                            'data' => array('final_approver'=>array(
                                    'position_no' => $position_no,
                                    'position_name'=>$position_name,
                                    'approver_no'=>$rows2['user_no'],
                                    'approver_name'=>$rows2['user_name'],
                                    'final_approver'=>true,
                                    'counter'=>$counter++
                            ))
                    );
                    
                    
                     // get 2nd approver
                    if((int)$rows2['user_no'] &&
                            $rows2['user_no']!=null){
                       
                        $handle = $this->getDb()->query("SELECT m_user.user_no,
                                m_user.user_name,
                                m_user.position_no,
                                m_position.position_name
                                FROM m_user
                                LEFT JOIN m_position
                                ON m_user.position_no = m_position.position_no
                                WHERE m_user.immediate_superior_no = {$rows2['user_no']} 
                                AND m_position.approval = 1
                                AND m_user.user_no != {$user_no}
                                ORDER BY RAND()")->execute();
                        $row1stApprover = $handle->current();
                        
                        
                        if((int)$row1stApprover['user_no']){
                            $selectData['data']['approvers'][] = array(
                                    'position_no' => $row1stApprover['position_no'],
                                    'position_name'=>$row1stApprover['position_name'],
                                    'approver_no'=>$row1stApprover['user_no'],
                                    'approver_name'=>$row1stApprover['user_name'],
                                    'final_approver'=>false,
                                    'counter'=>$counter++
                            );
                        }
                      

                        // get 3rd approver
                        if((int)$row1stApprover['user_no']){
                             
                            $handle = $this->getDb()->query("SELECT m_user.user_no,
                                    m_user.user_name,
                                    m_user.position_no,
                                    m_position.position_name
                                    FROM m_user
                                    LEFT JOIN m_position
                                    ON m_user.position_no = m_position.position_no
                                    WHERE m_user.immediate_superior_no = {$row1stApprover['user_no']}
                                    AND m_user.user_no != {$user_no}
                            ORDER BY RAND()")->execute();
                            $row2ndApprover = $handle->current();
                        
                            if((int)$row2ndApprover['user_no']){
                                 $selectData['data']['approvers'][]  = array(
                                        'position_no' => $row2ndApprover['position_no'],
                                        'position_name'=>$row2ndApprover['position_name'],
                                        'approver_no'=>$row2ndApprover['user_no'],
                                        'approver_name'=>$row2ndApprover['user_name'],
                                         'final_approver'=>false,
                                         'counter'=>$counter++
                                );
                            }
                            

                            // get 4th approver
                            if((int)$row2ndApprover['user_no']){
                                 
                                $handle = $this->getDb()->query("SELECT m_user.user_no,
                                        m_user.user_name,
                                        m_user.position_no,
                                        m_position.position_name
                                        FROM m_user
                                        LEFT JOIN m_position
                                        ON m_user.position_no = m_position.position_no
                                        WHERE m_user.immediate_superior_no = {$row2ndApprover['user_no']}
                                        AND m_user.user_no != {$user_no} 
                                        AND m_position.approval = 1
                                        ORDER BY RAND()")->execute();
                                $row3rdApprover = $handle->current();
                            
                                if((int)$row3rdApprover['user_no']){
                                     $selectData['data']['approvers'][] = array(
                                            'position_no' => $row3rdApprover['position_no'],
                                            'position_name'=>$row3rdApprover['position_name'],
                                            'approver_no'=>$row3rdApprover['user_no'],
                                            'approver_name'=>$row3rdApprover['user_name'],
                                             'final_approver'=>false,
                                             'counter'=>$counter++
                                    );
                                }
                                
                                
                            }
                            
                        
                        }  
                       
                    }


        
                }
                
                $reverse =  \rsort($selectData['data']['approvers']); 
                $viewJson->setVariables($selectData);
                return $viewJson;
            }
            
            $sql = "SELECT
                    t_request_category.id,
                    t_request_category.menu_item_name,
                    t_request_category.parent_id,
                    t_request_category.position_no,
                    m_position.position_name
                    FROM t_request_category 
                    LEFT join m_position
                    ON t_request_category.position_no = m_position.position_no
                    WHERE t_request_category.parent_id = {$id}";
            
            $handle = $dbAdapter->query($sql);
            $result = $handle->execute();
             
            $selectData = array();
            
            if(count($result)>0){
                $selectData['0'] = '-select-';
                foreach ($result as $res) {
                
                    $selectData[$res['id']] = $res['menu_item_name'];
                }
            }
            
            
        }
     
        
        $viewJson->setVariables($selectData);
        return $viewJson; 
    }
    
    
    
	 public function createRingiTemplateAction(){
        $this->init();
        $view = new ViewModel(array('name'=>'pogi'));
        $view->setTemplate('/' . VIEW_DIR . '/approval/create-ringi-template.phtml');
        return $view;
    }

    
    
      public function exportAction(){
     $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
                    ));
        }
        // database connection test
        $dbAdapter = $this->getDb();
        $decisionTable  = new DecisionTable();
        $sql = "SELECT 
                     t_decision.decision_no,
                     t_decision.branch_no,
                     t_decision.ref_no,
                     t_decision.decision_title,
                     t_decision.document,
                     t_decision.remarks as suplimentary_advice,
                     m_section.abbr_name as user_section,
                     date_format(t_decision.preferred_date, '%Y-%m-%d') as preferred_date,
                     LOWER(t_decision.status) as status,
                     t_decision.deleted,
                     t_decision.create_user,
                     date_format(t_decision.create_time, '%Y-%m-%d') as create_time,
                     date_format(t_decision.update_time, '%Y-%m-%d %h:%i %p') as update_time,
                     t_decision.update_user,
                     m_branch.branch_name,
                     m_branch.abbr_name,
                     m_user.user_name
                FROM t_decision 
           LEFT JOIN m_branch
                  ON m_branch.branch_no = t_decision.branch_no
           LEFT JOIN m_user
                  ON m_user.user_no = t_decision.create_user
           LEFT JOIN m_section 
                  ON m_user.section_no = m_section.section_no
               WHERE decision_no = {$id}";
        
       $result  = $dbAdapter->query($sql)->execute();
       $data = $result->current();
       
       if($data['status']!='complete'){
           die("Status not yet complete");
       }
       if(empty($data["decision_no"])){
           
          $this->flashMessenger()->addMessage('Approval document not found');
         // $this->flashMessenger()->addMessage('??????????????');
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'index'
            ));
       }
        try{
            $alist = $decisionTable->getApproverList($id, $this->auth()->get('user_no'));
            $attatchmentList = $decisionTable->getApprovalAttachments($id);
        
        }catch (\Exception $e){
             die($e->getMessage());
        }
            $content = preg_replace("/&#?[a-z0-9]{2,8};/i","",$data["document"]);
    	    $this->init();
    	    $tempTarget = implode(SEPARATOR, array('data','pdf')).SEPARATOR;
    	    $target = DOCUMENT_ROOT.$tempTarget.'approval-'.$id.'.pdf';
    	    
    		$rdoc= new RingiDocument();
    		$template = ($data['branch_no']==3) ? "jp":"en";
    		//$template = "jp";
    		$rdoc->setLanguage($template); // basically only 2 template are supported here the japan and the en 
    		$total_approver = count($alist);
  
    		
    		if(count($alist)>=2){
    		    $rdoc->setApproverCount($total_approver);
    		    $rdoc->setApproverList($alist);
    		    $rdoc->setFinalApproverComment($alist[$total_approver -1]["remarks"]);
    		    
    		}else{
    		    die("Fatal Error! Approvers count error ");
    		}
    		
    		if("approved" == \strtolower($alist[$total_approver -1]["status_name"])){
    		    $rdoc->setStatus("approved");
    		    
    		}
    		
    		$rdoc->setDateIssued($data["create_time"]); // from db %Y-%m-%d
    		$rdoc->setApplicantName($data["user_name"]); // from db
    		$rdoc->setBranchName($data["branch_name"]); 
    		$rdoc->setSubject($data["decision_title"]);
    		$rdoc->setStatus($data["status"]); // rejected/complete
    		$rdoc->setSerialNumber($data['ref_no']);
    		$rdoc->setSuplimentaryAdvice($data['suplimentary_advice']);
    		$rdoc->setApproveLocationText("Shoud be approved from ".$data['branch_name']);
    		$rdoc->setApplicantSection($data['user_section']);
    		
    		//set attachement 
    		if(count($attatchmentList)>=1){
    		    $rdoc->setAttachment($attatchmentList);
    		    $rdoc->setAttachmentCount(count($attatchmentList));
    		}
    		
    		$rdoc->setContent($content);
    		$rdoc->setRequestedDeadlineDate($data["preferred_date"]);
    		$rdoc->setApprovedDate(date("Y-m-d")); // temporarily use current date with format date("Y-m-d");
    		$file = $rdoc->exportToPDF($target);
    		
    	$response = new Stream();
    	$response->setStream(fopen($file, 'r'));
    	$response->setStatusCode(200);
    	$response->setStreamName(basename($file));
    	 
    	$headers = new Headers();
    	$headers->addHeaders(array(
    			'Content-Disposition' => 'inline; filename="' . basename($file) .'"',
    			'Content-Type' => 'application/pdf;charset=UTF-8',
    			'Content-Length' => filesize($file)
    	));
    	 
    	$response->setHeaders($headers);
    	return $response;
    }
    
    
    public function viewFileAttachmentAction(){
        $file_no = (int) $this->params()->fromRoute('id', 0);
        if (!$file_no) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $currentUser = $this->auth()->get('user_no');
        
        $dbAdapter = $this->getDb();
        $sql = "SELECT 
                      t_decision_file.file_no, 
                      t_decision_file.decision_no, 
                      t_decision_file.file_name, 
                      t_decision_file.content_type, 
                      OCTET_LENGTH( t_decision_file.binary_data) as file_size, 
                      t_decision_file.binary_data,
                      t_decision.create_user
                 FROM t_decision_file 
            LEFT JOIN t_decision 
                   ON t_decision_file.decision_no = t_decision.decision_no
                WHERE t_decision_file.file_no = {$file_no}";
  
        $result  = $dbAdapter->query($sql)->execute();
        $file = $result->current();
     
        if(!$file){
             return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'not-found'
            ));
        }
        
        $continue = true;
        
        // you must be the create user of the request 
        if($file['create_user'] != $currentUser){
            $continue = false;
        }
    
        // if you are not the create_user of the request you must be 1 of approver     
        if(!$continue){
            // check file restriction
            $result2 = $dbAdapter->query("SELECT
                    t_decision_approval.user_no
                    FROM t_decision_approval
                    WHERE t_decision_approval.decision_no = {$file['decision_no']}")->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($result2);
            $resultAr = $resultSet->toArray();
             
            if(is_array($resultAr)){
                foreach ($resultAr as $rec){
                    if((int)$rec['user_no'] === (int)$currentUser){
                        $continue = true;
                        break;
                    }
                } 
            }

            
        }

        // Otherwise you will not be able to access this attachment 
       if(!$continue){
           $this->flashMessenger()->addMessage("You don't have enough access level to view this document");
         return $this->redirect()->toRoute('app', array(
                       'controller' => 'failed', 'action' => 'forbidden'
               ));
         
       }
        
        
        $file_name = $file["file_name"];
        $file_size = $file["file_size"];
        $file_mime_type = $file["content_type"]; 
        $file_content = $file["binary_data"];
        $new_filename = "temp_" . $file_name;
        $filePath = APP_UPLOAD_DIR.$new_filename; 
        \file_put_contents($filePath, $file_content);
       	$response = new Stream();
    	$response->setStream(fopen($filePath, 'r'));
    	$response->setStatusCode(200);
    	$response->setStreamName(basename($filePath));
        $headers = new Headers();
        $headers->addHeaders(array(
                'Content-Disposition' => 'inline; filename="' . $new_filename .'"',
                'Content-Type' => $file_mime_type.';charset=UTF-8', 
                'Content-Length' => $file_size
        ));
        $response->setHeaders($headers);
        /**$view = new ViewModel(array(
            'response' => $response,
            'headers' => $headers
        ));
        $view->setTemplate('/common/view-attachment.phtml');
        $view->setTerminal(true);**/
      //  return $view;
        return $response;
    }
    
    
    public function getAllAuthorityListJsonAction(){
        
        $dbAdapter = $this->getDb();
        $handle = $dbAdapter->query("SELECT
                id,
                menu_item_name,
                parent_id
                FROM t_request_category ");
        
        $result = $handle->execute();
        
        
        $viewJson = new JsonModel();
        $viewJson->setVariables($result);
        return $viewJson;
        
    }
       public function getDb(){
           if(!null==$this->db){
               return $this->db;
           }
           $this->db  = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
           
           return $this->db;
       }
	   
	
public function uploadAction(){
	$this->init();
        if (2 > $this->ctrlLv) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $form = new ApprovalForm();
        $form->setAttachForm();

		
        $view = new ViewModel(array(
            'controller' => 'approval',
            'action' => 'fileStore',
            'form' => $form,
        ));
		$view->setTemplate('/common/attachment.phtml');
        $view->setTerminal(true);
        return $view;
	}
	
public function fileStoreAction(){
        $this->init();
		
        $request = $this->getRequest();
	

        if (!$request->isPost() ) {
            exit();
        }
		
        $form = new ApprovalForm();
        $form->setAttachForm();
		$filter = new ApprovalFilter();
        $filter->setStoreInputFilter();

        $hydrator = new ObjectProperty();
        $post = $request->getPost();
        $file = $request->getFiles();
        $hydrator->hydrate($file->toArray(), $post);
		
		$form->setInputFilter($filter->getInputFilter());
        $form->setData($post);

        $success = $form->isValid();
        
		if (!$success) {
            $token_id = make_token_id();
            $this->container()->set('token_id', $token_id);
            $form->get('token_id')->setAttribute('value', $token_id);

            $values = array(
                'controller' => 'approval',
                'action' => 'fileStore',
                'form' => $form,
            );
            $view = new ViewModel($values);
            $view->setTemplate('/common/attachment.phtml');
            $view->setTerminal(true);
            return $view;
        }
       
        $dir_name =  APP_UPLOAD_DIR;
        if(!file_exists($dir_name)){
                 mkdir($dir_name, 0777, true);
        }
        $file_name = gv('name', $file->upload_file);
        $tmp_name = gv('tmp_name', $file->upload_file);
        if (file_exists($dir_name . $file_name)) {
                unlink($dir_name . $file_name);
        }
        else{
                $path = tempnam(sys_get_temp_dir(), $file_name);
                $temp = explode(".", $file_name);
                $base_name = basename($file_name);
                $no_extension = preg_replace("/\.[^.]+$/", "", $base_name);
                $newfilename = $no_extension . '_' .round(microtime(true)) . '.' . end($temp);

                $success = move_uploaded_file($tmp_name, $dir_name . $newfilename);

                if ($success) {
                        $message = 'Successfully uploaded';
                }
                else {
                        $message = 'Failed to Upload File';
                }
        }
        $view = new ViewModel(array('message' => $message,
									'filename' => $newfilename,
									'dir' => $path,
		));
        $view->setTemplate('/common/attach.phtml');
        $view->setTerminal(true);
        return $view;
    }
    
    public function deleteUploadAction(){
        $this->init();
    
        $request = $this->getRequest();
        $file_name = trim($request->getPost('button'));
    
        $dir_name =  APP_UPLOAD_DIR;
    
        if (file_exists($dir_name . $file_name)) {
            unlink($dir_name . $file_name);
    
        } else{
            echo "The file name does not exist";
        }
        $view = new ViewModel(array('result' => $file_name));
        $view->setTerminal(true);
        return $view;
    }
    
    public function viewMessageAction()
    {
        $success = true;
        $id = (int) $this->params()->fromQuery('id', 0);
        $user = (int) $this->params()->fromQuery('user',0);
        if (!$id) {
            return $this->redirect()->toUrl(BASE_URL);
        }

        $this->init();
        $self = true;
        if ($id != $this->auth()->get('user_no')) {
            $self = false;
            $success = (0 < $this->ctrlLv) ? true : false;
        }

        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $db = new ApprovalTable();
        $message = $db->getMessage($id,$user);
    

        $values = array(
            'self' => $self,
            'ctrlLv' => $this->ctrlLv,
            'message' => $message,

        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/approval/comment.phtml');
        return $view;
    }

    public function readAction()
    {
        $view = self::viewMessageAction();
        $view->setTerminal(true);
        return $view;
    }
   
    public function viewRemarksAction()
    {
        $success = true;

        $id = (int) $this->params()->fromQuery('id', 0);
        $user = (int) $this->params()->fromQuery('user',0);
 
        $this->init();
        $self = true;
        if ($id != $this->auth()->get('user_no')) {
            $self = false;
            $success = (0 < $this->ctrlLv) ? true : false;
        }

        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $db = new ApprovalTable();
        $message = $db->getRemarks($id,$user);
    
        $values = array(
            'self' => $self,
            'ctrlLv' => $this->ctrlLv,
            'message' => $message,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/approval/comment.phtml');
        return $view;
    }
    public function readRemarksAction()
    {
        $view = self::viewRemarksAction();
        $view->setTerminal(true);
        return $view;
    }
}
