<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Form;
use Zend\Form\Element;
use Zend\Form\Form;
use App\Model\Table\ApprovalTable;
use App\Model\Table\TplDocumentTable;
use App\Model\Table\UserTable;
use Zend\Validator\Explode;


class DecisionForm extends Form
{
    /**
     * for edit
     */
    public function setEditForm($data){
        
        parent::__construct();
        $this->setAttribute('method', 'post');
		$this->setAttribute('class', 'application_form');
		
		//Hidden Fields
        $this->add(
            array(
                'name' => 'token_id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'token_id',
                ),
            )
        );
        
        $this->add(
                array(
                        'name' => 'ref_no',
                        'attributes' => array(
                                'type' => 'hidden',
                                'id' => 'ref-no',
                        ),
                )
        );
        
        
        $this->add(
                array(
                        'name' => 'file_attachments',
                        'attributes' => array(
                                'type' => 'hidden',
                                'id' => 'file-attachments',
                        ),
                )
        );
        
        

        // the PK id of decition
        $this->add(
            array(
                'name' => 'decision_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'decision-no',
                ),
            )
        );
        
        
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
                        'name' => 'approvers_csv',
                        'attributes' => array(
                                'type' => 'hidden',
                                'id' => 'approvers-csv',
                        ),
                )
        );
        

        $this->add(
            array(
                'name' => 'decision_title',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'decision-title',
                ),
                'options' => array(
                    'label' => 'Subject',
                ),
            )
        );

        
        $this->add(
                array(
                        'name' => 'preferred_date',
                        'attributes' => array(
                                'type' => 'text',
                                'id' => 'preferred-date',
                        ),
                        'options' => array(
                                'label' => 'Deadline',
                                'title'=>'Preferred Deadline'
                        ),
                )
        );
        
        $this->add(
                array(
                        'name' => 'tpl_title',
                        'type' => 'select',
                        'attributes' => array(
                                'id' => 'load-doc-template-select',
                                'class' => 'content_template',
                        ),
                        'options' => array(
                                'label' => 'Load Template',
                                'empty_option' => 'Select Template',
                                'value_options' => $this->getDocTemplateList($data),
                        )
                )
        );
        
		
		// remarks textarea
		$this->add(
		        array(
		                'name' => 'document',
		                'attributes' => array(
		                        'type' => 'textarea',
		                        'id' => 'document',
		                ),
		                'options' => array(
		                        'label' => 'Content',
		                ),
		        )
		);
		
		$this->add(
		        array(
		                'name' => 'root_authority_list',
		                'type' => 'select',
		                'attributes' => array(
		                        'id' => 'root-authority-list',
		                        'class'=>'authority_option'
		                ),
		                'options' => array(
		                        'value_options' => $this->getCategory1($data),
		                        'empty_option' => 'Select',
		                ),
		        )
		);
		
	
		
		$this->add(
		        array(
		                'name' => 'selected_approvers',
		                'type' => 'select',
		                'attributes' => array(
		                        'id' => 'selected-approvers',
		                        'size'=>5
		                ),
		                'options' => array(
		                        'label' => 'User',
		                        'value_options' => array(),
		                ),
		        )
		);
		
		$this->add(
		        array(
		                'name' => 'files',
		                'type' => 'select',
		                'attributes' => array(
		                        'id' => 'for_attachment',
		                        'size'=>5,
		                        'multiple'=>"multiple",
		                ),
		                'options' => array(
		                        'label' => 'Attachement',
		                        'value_options' => array(),
		                ),
		        )
		);
		
        
        // remarks textarea
		$this->add(
            array(
                'name' => 'remarks',
                'attributes' => array(
                    'type' => 'textarea',
                    'id' => 'remarks',
                ),
				 'options' => array(
                    'label' => 'Remarks',
                ),
            )
        );
		
		
		//draft Apply button
        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Apply',
                    'id' => 'submit',
                    'title'=>'Save ',
                    'onclick' => 'showLoading();'
                ),
            )
        );
        
        //draft button
        $this->add(
            array(
                'name' => 'draft',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Draft',
                    'id' => 'draft',
                ),
            )
        );
    }
    
    
    
    /**
     * 
     * @param string $_approver_csv 
     */
    public function updateSelectedApprovers($_approver_csv){
        
        $this->remove("selected_approvers");
        
        //re fill 
        $this->add(
                array(
                        'name' => 'selected_approvers',
                        'type' => 'select',
                        'attributes' => array(
                                'id' => 'selected-approvers',
                                'size' => 5
                        ),
                        'options' => array(
                                'label' => 'Route',
                                'value_options' => $this->getApproverList($_approver_csv)
                        )
                ));
        
  
    }
    
    
    public function updateAttachments($_attachment_csv){
    
        $this->remove("files");
        
        $attachmentAr  = explode(',', $_attachment_csv);
        $selectOption = array();
        
        foreach ($attachmentAr as $v){
            $selectOption[$v] = $v;
        }
        
    
        //re fill
       	$this->add(
		        array(
		                'name' => 'files',
		                'type' => 'select',
		                'attributes' => array(
		                        'id' => 'for_attachment',
		                        'size'=>5,
		                        'multiple'=>"multiple",
		                ),
		                'options' => array(
		                        'label' => 'Attachement',
		                        'value_options' => $selectOption,
		                ),
		        )
		);
    
    
    }
    
    public function getCategory1($_data){
    
        $aTable  = new ApprovalTable();
    
        return $aTable->getCategory1(gv('branch_no', $_data));
    
         
    }
    
    
    public function getApproverList($ids){ //3,5
        
       $idsAr = explode(',', $ids);
       $userTable = new UserTable();
       
      $rowSet =  $userTable->getTiltleUserPairs(array(),$idsAr);
      //var_dump($rowSet);
      // 2,5,12
      //array(3) { [12]=> string(33) "Marvin Manguiat (Supervisor - PH)" [6]=> string(29) "Yokota Tetsuya (Manager - PH)" [5]=> string(28) "Yoshikawa Ken (Manager - PH)" }
      
      $selectData = array();
     
      if(count($idsAr)>0){
          foreach ($idsAr as $id){
              $selectData[$id] = $rowSet[$id];
          }
      }
     
      
      return $selectData;
      
    }
    
    
    public function getDocTemplateList($_data){
        $branch_no = (int)$_data['branch_no'];
        $where = array('tpl_doc_no <> 0',"branch_no ={$branch_no}");
    
        $db = new TplDocumentTable();
        return $db->getPairs(null, null, 0, $where);
    }


}