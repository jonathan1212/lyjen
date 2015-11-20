<?php 

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\BranchForm;
use Application\FormFilters\BranchFilter;
use Application\Model\Table\BranchTable;
use Application\Model\Entity\BranchEntity;

class BranchController extends AbstractActionController{

    public function indexAction()
    {

        $user_info =  $this->authPlugin()->getUserInfo();
        $db = new BranchEntity();
        
        
        $page = $db->db()->getPageList();
       
        $page->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
	$page->setItemCountPerPage((int) $this->params()->fromQuery('max_rows', 10));
        
        return new ViewModel(array(
            'userInfo'=>$user_info,
            'rows' => $page,
            ));
        
    }

    public function addAction(){
        $user_info =  $this->authPlugin()->getUserInfo();
        if(!$user_info)
        {
             return $this->redirect()->toRoute('application', array(
                    'controller' => 'auth', 'action' => 'index'
                ));
        }
        $form = new BranchForm();
        $form->setEditForm('add');
        $db = new BranchEntity();
        $filter = new BranchFilter();

        $request = $this->getRequest();
        if($request->isPost()){
         
            $filter->setInputFilter('add');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            
            $success = false;
            if ($form->isValid()) {
                
                
                $success = $db->insertRecord($user_info['user_id'], $form->getData());
            }
            else {
                $data = $form->getInputFilter()->getValues();

            }
            if ($success) {

                return $this->redirect()->toRoute('branch', array(
                    'action' => 'index',
                ));
            }
        }
        $form->get('submit')->setAttribute('value', 'Save');
        $form->get('reset')->setAttribute('value', 'Reset');

        $values = array(
            'form' => $form,
            'userInfo'=>$user_info
        );

        return new ViewModel($values);
    }

    public function editAction(){

        $user_info =  $this->authPlugin()->getUserInfo();

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $success = false;
        }

        $db = new BranchEntity();

        $filter = new BranchFilter();

        try{
            $row = $db->db()->getFetchOne($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('branch', array(
                         'action' => 'index'
            ));
        }

        $form = new BranchForm();
        $form->setEditForm();
        $form->bind($row); 

        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                
                $form->setInputFilter($filter->getInputFilter());
                $form->setData($request->getPost());

                $success = $form->isValid();

                if ($success) {
                    $success = $db->updateRecord($user_info['user_id'], $form->getData());
                }
                else {
                    $data = $form->getInputFilter()->getValues();
                }

                if (false !== $success) {
                    $this->flashMessenger()->addSuccessMessage("Success");
                    return $this->redirect()->toRoute('branch', array(
                        'action' => 'index',
                    ));
                }
            }
        }

        $form->get('submit')->setAttribute('value', 'Update');
        $form->get('reset')->setAttribute('value', 'Reset');

        $values = array(
         'id' => $id,
         'action' => 'edit',
         'form' => $form,
         'auth'=>$user_info,
        );

        return new ViewModel($values);
    }
    public function deleteAction(){
        
    }
}