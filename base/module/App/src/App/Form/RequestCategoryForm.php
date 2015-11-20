<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Form;
use Zend\Form\Form;
use App\Model\Table\PositionTable;
use App\Model\Table\BranchTable;

class RequestCategoryForm extends Form
{
    /**
     * list - for search
     */
    
    
    
    public function setRootListForm(){
    
        parent::__construct('request-category-list');
        $this->setAttribute('method', 'post');

        $this->add(
                array(
                        'name' => 'menu_item_name',
                        'attributes' => array(
                                'type' => 'text',
                                'id' => 'menu-item-name',
                                'placeholder' => 'new menu',
                        ),
                )
        );
    
        $this->add(
                array(
                        'name' => 'branch_no',
                        'type' => 'select',
                        'attributes' => array(
                                'id' => 'branch-no',
                        ),
                        'options' => array(
                                'label' => 'Set Branch: ',
                                'value_options' => $this->getBranchSelection(),
                                'empty_option' => 'select branch',
                        ),
                )
        );
        
          
        
        $this->add(
                array(
                        'name' => 'parent_id',
                        'attributes' => array(
                                'type' => 'hidden',
                                'id' => 'parent-id',
                        ),
                )
        );
    
    
        $this->add(
                array(
                        'name' => 'submit',
                        'attributes' => array(
                                'type' => 'submit',
                                'value' => 'Add',
                                'id' => 'btn-submit',
                                'onclick' => '',
                        ),
                )
        );
    }
    
    
    public function setChildListForm(){
        
        parent::__construct('request-category-list');
        $this->setAttribute('method', 'post');
   
        
        $this->add(
                array(
                        'name' => 'branch_no',
                        'attributes' => array(
                                'type' => 'hidden',
                                'id' => 'branch-no',
                        ),
                )
        );
        
        $this->add(
                array(
                        'name' => 'parent_id',
                        'attributes' => array(
                                'type' => 'hidden',
                                'id' => 'parent-id',
                        ),
                )
        );
        
        $this->add(
            array(
                'name' => 'menu_item_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'menu-item-name',
                    'placeholder' => 'new menu',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Add',
                    'id' => 'btn-submit',
                    'onclick' => '',
                ),
            )
        );
    }

    
    /**
     * for edit
     */
    public function setEditForm(){
        parent::__construct('request-category-edit');
        $this->setAttribute('method', 'post');
		$this->setAttribute('class', 'content_form');

        $this->add(
            array(
                'name' => 'id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'menu-item-id',
                ),
            )
        );
        
        
        $this->add(
                array(
                        'name' => 'parent_id',
                        'attributes' => array(
                                'type' => 'hidden',
                                'id' => 'parent-id',
                        ),
                )
        );
        
        
        $this->add(
                array(
                        'name' => 'menu_item_name',
                        'attributes' => array(
                                'type' => 'text',
                                'id' => 'menu-item-name',
                                'placeholder' => 'new menu'
                        ),
                        'options' => array(
                                'label' => 'Menu Item Name: '
                        )
                        
                ));
        
        $this->add(
                array(
                        'name' => 'menu_item_code',
                        'attributes' => array(
                                'type' => 'text',
                                'id' => 'menu-item-code',
                                'placeholder' => 'Enter Item Code'
                        ),
                        'options' => array(
                                'label' => 'Menu Item Code: '
                        )
        
                ));
        
        
        $this->add(
                array(
                        'name' => 'position_no',
                        'type' => 'select',
                        'attributes' => array(
                                'id' => 'position_no',
                                'class'=>'authority_option'
                        ),
                        'options' => array(
                                'label' => 'Set Approver: ',
                                'value_options' => $this->getPostionSelection(),
                                'empty_option' => 'not allocated',
                        ),
                )
        );
        
        
        $this->add(
                  array(
                          'name' => 'submit',
                          'attributes' => array(
                                  'type' => 'submit',
                                  'value' => 'Save Changes',
                                  'id' => 'btn-submit',
                                  'onclick' => '',
                          ),
                  )
          );
    }
    
    
 
    
    
    public function getPostionSelection(){
        $where = array('position_no <> 0');
    
        $db = new PositionTable();
        return $db->getPairs(null, null, 0, $where);
    }
    
    public function getBranchSelection(){
        $where = array('branch_no <> 0');
    
        $db = new BranchTable();
        return $db->getPairs(null, null, 0, $where);
    }
}