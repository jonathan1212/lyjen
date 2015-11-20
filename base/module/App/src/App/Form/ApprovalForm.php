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
use App\Model\Table\TplDocumentTable;
use App\Model\Table\BranchTable;
use App\Model\Table\ApprovalTable;
use App\Model\Table\TplRouteTable;
use App\Model\Table\SectionTable;
use Zend\InputFilter;

class ApprovalForm extends Form
{
    /**
     * list - for search
     */

	
    public function setListForm()
    {
        parent::__construct('application-search');
        $this->setAttribute('method', 'get');
        $this->add(
            array(
                'name' => 'ord',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'ord',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'search-branch_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'search-branch_no',
                ),
                'options' => array(
                    'label' => 'No.',
                    'value_options' => $this->getBranchList(),
                    'empty_option' => 'Select Branch'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-ref_no',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-ref_no',
                    'placeholder' => 'Reference Number',
                ),
                'options' => array(
                    'label' => 'Reference No.',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-decision_title',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-key',
                    'placeholder' => 'Keyword',
                ),
                'options' => array(
                    'label' => 'Keyword',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-choices',
                'type' => 'radio',
                'attributes' => array(
                    'value' => 'decision_title'
                ),
                'options' => array(
                    'label' => 'key',
                     'value_options' => array(
                        'decision_title' => 'Subject',
                        'document' => 'Text',
                    ),
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-create_user',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'search-applicant',
                ),
                'options' => array(
                    'label' => 'Applicant',
                    'value_options' => $this->getSectionList($_data),
                    'empty_option' => 'Select Applicant'
                ),
            )
        );
        $this->add(
            array(
                    'name' => 'search-create_time',
                    'attributes' => array(
                            'type' => 'text',
                            'id' => 'apply_date',
                    ),
                    'options' => array(
                            'label' => 'Apply Date',
                    ),
            )
        );
        $this->add(
            array(
                    'name' => 'search-create_time2',
                    'attributes' => array(
                            'type' => 'text',
                            'id' => 'apply_date2',
                    ),
                    'options' => array(
                            'label' => 'Apply Date',
                    ),
            )
        );
        $this->add(
            array(
                    'name' => 'search-update_time',
                    'attributes' => array(
                            'type' => 'text',
                            'id' => 'update_time',
                    ),
                    'options' => array(
                            'label' => 'Last Update',
                    ),
            )
        );
         $this->add(
            array(
                    'name' => 'search-update_time2',
                    'attributes' => array(
                            'type' => 'text',
                            'id' => 'update_time2',
                    ),
                    'options' => array(
                            'label' => 'Last Update',
                    ),
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Search',
                    'id' => 'search',
                    'onclick' => 'search();',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'clear',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'CLR',
                    'id' => 'search-clear',
                ),
            )
        );
    }
    
    
    /**
     * for add
     */
    public function setAddForm($data){
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'application_form');
        
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
                                'id' => 'token_id',
                        ),
                )
        );
    
   
        
        $this->add(
                array(
                        'name' => 'branch_no',
                        'attributes' => array(
                                'type' => 'text',
                                'id' => 'branch_no',
                        ),
                        'options' => array(
                                'label' => 'Number',
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
                                'label' => 'Requested Deadline',
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
        
        $this->add(
                array(
                        'name' => 'document',
                        'attributes' => array(
                                'type' => 'textarea',
                                'id' => 'application_doc',
                        ),
                        'options' => array(
                                'label' => 'Content',
                        ),
                )
        );
    
        $this->add(
                array(
                        'name' => 'cat1',
                        'type' => 'select',
                        'attributes' => array(
                                'id' => 'category1',
                                'class'=>'authority_option'
                        ),
                        'options' => array(
                                'label' => 'Category 1',
                                'value_options' => $this->getCategory1($data),
                                'empty_option' => 'Select',
                        ),
                )
        );
    
    
        $this->add(
                array(
                        'name' => 'file_name',
                        'type' => 'select',
                        'attributes' => array(
                                'id' => 'for_attachment',
                                'multiple' => 'multiple',
                        ),
                        'options' => array(
                                'label' => 'Attachments',
                        ),
                )
        );
        
    
        
  
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
        
        $this->add(
                array(
                        'name' => 'submit',
                        'attributes' => array(
                                'type' => 'submit',
                                'value' => 'Apply',
                                'id' => 'submit',
                        ),
                )
        );
        
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
     * for edit
     */
    public function setEditForm($_data){
        parent::__construct();
        $this->setAttribute('method', 'post');
	$this->setAttribute('class', 'application_form');
		

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
                                'id' => 'token_id',
                        ),
                )
        );
        

        $this->add(
                array(
                        'name' => 'authority_sequence_no',
                        'attributes' => array(
                                'type' => 'hidden',
                                'id' => 'authority-sequence_no',
                        ),
                )
        );
        
        $this->add(
            array(
                'name' => 'decision_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'decision_no',
                ),
            )
        );
         $this->add(
            array(
                'name' => 'branch_no',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'branch_no',
                ),
                'options' => array(
                    'label' => 'Number',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'update_time',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'update_time',
                ),
            )
        );


        $this->add(
            array(
                'name' => 'decision_title',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'decision_title',
                ),
                'options' => array(
                    'label' => 'Subject',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'create_user',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'create_user',
                ),
                'options' => array(
                    'label' => 'Applicant',
                ),
            )
        );

		$this->add(array(
            'type' => 'Date',
            'name' => 'preferred_date',
            'options' => array(
                'label' => 'Requested Deadline'
            )
        ));
		
		$this->add(
			array(
				'name' => 'tpl_title',
				'type' => 'select',
				'attributes' => array(
					'id' => 'textEditor',
					'class' => 'content_template',
				),
				'options' => array(
					'label' => 'Load Template',
					
					'empty_option' => 'Select Template',
				)
			)
		);
        $this->add(
            array(
                'name' => 'document',
                'attributes' => array(
                    'type' => 'textarea',
					'id' => 'application_doc',
                ),
                'options' => array(
                    'label' => 'Content',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'cat1',
				'type' => 'select',
                'attributes' => array(
                    'id' => 'category1',
                     'class'=>'authority_option'
                ),
                'options' => array(
                    'label' => 'Category 1',
					'value_options' => $this->getCategory1($_data),
                    'empty_option' => 'Select',
                ),
            )
        );
        
	
		$this->add(
            array(
                'name' => 'file_name',
				'type' => 'select',
                'attributes' => array(
                    'id' => 'for_attachment',
					'multiple' => 'multiple',
                ),
                'options' => array(
                    'label' => 'Attachments',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'route_template',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'route_template',
                ),
                'options' => array(
                    'label' => 'Template',
					'value_options' => $this->getUserRouteList($_data),
                    'empty_option' => 'Template',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'branch_name',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'application_branch',
                ),
                'options' => array(
                    'label' => 'Branch',
					'value_options' => $this->getBranchList(),
                    'empty_option' => 'Select Branch',
                ),
            )
        );
		
		 $this->add(
            array(
                'name' => 'section_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'section_no',
                ),
                'options' => array(
                    'label' => 'Section',
                    'value_options' => $this->getSectionList($_data),
                    'empty_option' => 'Select section',
                ),
            )
        );
        $name = gv('name', $_data);
        switch ($name) {
            case 'faild':
                $user_data = $this->getUserList($_data);
                break;
            case 'edit':
                $user_data = $this->getUserRouteList($_data);
                break;
            default :
                $user_data = array();
                break;
        }
        $this->add(
            array(
                'name' => 'user_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'form-item-from',
                    'size'=>5
                ),
                'options' => array(
                    'label' => 'User',
                    'value_options' => $user_data,
                ),
            )
        );
        $this->add(
            array(
                'name' => 'user_selector',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'form-item-to',
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
        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Apply',
                    'id' => 'submit',
                ),
            )
        );
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
	
	public function getDocTemplateList($_data){
	    $branch_no = (int)$_data['branch_no'];
		$where = array('tpl_doc_no <> 0',"branch_no ={$branch_no}");
		
		$db = new TplDocumentTable();
		return $db->getPairs(null, null, 0, $where);
	}
	
	public function getBranchList(){
		$where = array('branch_no <> 0');
		
		$db = new BranchTable();
		return $db->getPairs(null, null, 0, $where);
	}
	
	
	public function getCategory1($_data){
	
	    $aTable  = new ApprovalTable();
	    
	    return $aTable->getCategory1(gv('branch_no', $_data));

	    
	}
	 public function getSectionList($_data)
    {
        $branch_no = gv('branch_no', $_data);
        $where = array(
            'branch_no' => $branch_no,
        );
        $db = new SectionTable();
        return $db->getPairs(null, null, 0, $where);
    } 
	 
	 public function getUserRouteList($_data)
	{
		$db = new TplRouteTable();
		return $db->getRouteUserPairs(gv('tpl_route_no', $_data));
	}
	
	public function setAttachForm(){
		 parent::__construct('upload_form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

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
                'name' => 'file_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'file_name',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'upload_file',
                'type' => 'Zend\Form\Element\File',
                'attributes' => array(
                    'id' => 'upload_file',
                ),
                'options' => array(
                    'label' => 'Attach File',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Add',
                    'id' => 'submit',
                    'class' => 'btn'
                ),
            )
        );
    }
    
    /**
     * this form is for approval details page
     */
   public function setApprovalDetailsForm(){
       parent::__construct('approval-details');
       $this->setAttribute('method', 'post');
       $this->setAttribute('class', 'application_form');
       
     
       
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
       
       
       $this->add(
               array(
                       'name' => 'approve',
                       'attributes' => array(
                               'type' => 'submit',
                               'value' => 'Approved',
                               'id' => 'btn-approved',
                               'onclick' => 'showLoading();'
                       ),
               )
       );
       
       $this->add(
               array(
                       'name' => 'reject',
                       'attributes' => array(
                               'type' => 'submit',
                               'value' => 'Reject',
                               'id' => 'btn-reject',
                                'onclick' => 'showLoading();'
                       ),
               )
       );
       
       $this->add(
               array(
                       'name' => 'recall',
                       'attributes' => array(
                               'type' => 'submit',
                               'value' => 'Re-call',
                               'id' => 'btn-recall',
                               'onclick' => 'showLoading();'
                       ),
               )
       );

       $this->add(
               array(
                       'name' => 'recall-by-owner',
                       'attributes' => array(
                               'type' => 'submit',
                               'value' => 'Re-call',
                               'id' => 'btn-recall-by-owner',
                               'onclick' => 'showLoading();'
                       ),
               )
       );
   
   }
   
   
	public function attachSelectionForm(){
		parent::__construct('upload-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

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
                'name' => 'file_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'file_name',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'upload_file',
                'type' => 'file',
                'attributes' => array(
                    'id' => 'upload_file',
                ),
                'options' => array(
                    'label' => 'Attach File',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Add',
                    'id' => 'submit',
                    'class' => 'btn',
                ),
            )
        );
    }
	
    public function getApplicant(){
    $where = array('branch_no <> 0');

    $db = new BranchTable();
    return $db->getPairs(null, null, 0, $where);
    }
}