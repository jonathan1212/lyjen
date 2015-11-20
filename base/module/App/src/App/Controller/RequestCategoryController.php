<?php 
namespace App\Controller;

use Zend\View\Model\ViewModel;
use App\Controller\AbstractController;
use App\Model\Entity\RequestCategoryEntity;
use App\Form\RequestCategoryForm;
use App\Model\Table\RequestCategoryTable;
use Zend\Form\Fieldset;
use App\Filter\RequestCategoryFilter;

class RequestCategoryController extends AbstractController{
    
    public function __construct(){
    
    }
    
    public function listAction(){
        $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        
        $id = (int)$this->params()->fromRoute('id');
        $db = new RequestCategoryEntity();
        $form = new RequestCategoryForm();
        $filter = new RequestCategoryFilter();
        
        if(!$id){
            $form->setRootListForm();
            $form->get('submit')->setAttribute('value', 'Add Parent Node');
            $filter->setRootListFilter();
        }else{
            $form->setChildListForm();
            $form->get('submit')->setAttribute('value', 'Add Child Node');
            $filter->setChildListFilter();
        }
        // add new menu node
        $request = $this->getRequest();
        if($request->isPost()){
        $form->setInputFilter($filter->getInputFilter());
        $form->setData($request->getPost());
        
        if($form->isValid()){
            $db->db()->addParentMenu($form->getData());
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'request-category', 'action' => 'list',
                    'id'=>$id
            ));
        }
        
        }
       
        try{
            if($this->auth()->get('admin')){
            	$menus = $db->db()->getChildMenu($id,0);
            	
            }else{
            	
            	$db->db()->checkBranchAccess($this->auth()->get('branch_no'),$id);
            	$menus = $db->db()->getChildMenu($id,$this->auth()->get('branch_no'));
            }
            
            $parent_menu = $db->db()->getPreviousRoute($id);
            $list  = $this->getMenuRoute($parent_menu);
            
            if($id){
                $parent = $db->db()->getParent($id);
                $form->get('branch_no')->setValue($parent['branch_no']);
                $form->get('parent_id')->setValue($parent['id']);
            }
          
        }catch (\Exception $e){
            die($e->getMessage());
        }

        $values = array(
                'ctrlLv' => $this->ctrlLv,
                'menu_route'=>$list,
                'menus' => $menus,
                'form'=>$form,
                'admin' => $this->auth()->get('admin'),
                'id'=>$id
        );
        
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/request-category/list.phtml');
        return $view;
    }
    
    public function editAction(){
        $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
   
        $id = (int)$this->params()->fromRoute('id');
        if(!$id){
            return $this->redirect()->toRoute('app', 
                    array(
                            'controller' => 'failed',
                            'action' => 'forbidden'
                    ));
        }
        
        $db = new RequestCategoryEntity();
        
        try {
        	
        	if(!$this->auth()->get('admin')){
        		$db->db()->checkBranchAccess($this->auth()->get('branch_no'), $id);
        	}
        	
            $requestCategory = $db->db()->getMenuItemById($id);
            $parent_menu = $db->db()->getPreviousRoute($id);
            $list  = $this->getMenuRoute($parent_menu);
        }catch (\Exception $e){
            die($e->getMessage());
        }
        
        $form = new RequestCategoryForm();
        $form->setEditForm(($this->auth()->get('admin')) ? 0 : $this->auth()->get('branch_no'));
        $form->bind($requestCategory);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = new RequestCategoryFilter();
            $form->setData($request->getPost());
            $form->setInputFilter($filter->getInputFilter());
            if ($form->isValid()) {
              $db->db()->updateMenuItem($requestCategory);
              
              return $this->redirect()->toRoute('app', array(
                      'controller' => 'request-category', 'action' => 'list','id'=>$requestCategory->parent_id,
              ));
            }
         
        }
        
        $values = array(
                'ctrlLv' => $this->ctrlLv,
                'menu_route'=>($list),
                'form'=>$form,
                'admin' => $this->auth()->get('admin'),
                'id'=>$id
        );
        
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/request-category/edit.phtml');
        return $view;
    }
    
    
    public function deleteAction(){
        $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $view = new ViewModel();
        $view->setTemplate('/' . VIEW_DIR . '/request-category/edit.phtml');
        return $view;
    }
    
    
    public function restoreAction(){
        $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $view = new ViewModel();
        $view->setTemplate('/' . VIEW_DIR . '/request-category/edit.phtml');
        return $view;
    }
    
    
    
    public function addAction(){
        $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $view = new ViewModel();
        $view->setTemplate('/' . VIEW_DIR . '/request-category/edit.phtml');
        return $view;
    }
    
    private function getMenuRoute($parent_menu){
    	
        $db = new RequestCategoryEntity();
        $list = array();
        $list[] = $parent_menu;
        if($parent_menu['parent_id']!=NULL){
            $key = true;
             
            while($key){
                $parent_menu = $db->db()->getPreviousRoute($parent_menu['parent_id']);
                $list[] = $parent_menu;
                if($parent_menu['parent_id']==NULL)$key = false;
            }
        
        }
        return array_reverse($list);
    }
}
?>