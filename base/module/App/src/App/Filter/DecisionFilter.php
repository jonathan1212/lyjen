<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Filter;
use App\Filter\AbstractFilter;
use Zend\InputFilter;
use Zend\Filter\File\RenameUpload;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;

class DecisionFilter extends AbstractFilter
{
    /**
     * set InputFilter
     */
    public function setInputFilter()
    {

        $this->createInput(array(
            'name' => 'decision_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        
         $this->createInput(array(
          'name' => 'root_authority_list',
          'required' => false,
          
        ));
         
        $this->createInput(array(
                'name' => 'tpl_title',
                'required' => false,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
        
        
        $this->createInput(array(
                'name' => 'selected_approvers',
                'required' => false,
                'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 1000, //0000,0000,0000,0000
                                ),
                        ),
                ),
        ));
   
        
        $this->createInput(array(
                'name' => 'files',
                'required' => false,
                'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 1000, //0000,0000,0000,0000
                                ),
                        ),
                ),
        ));
        
        
        $this->createInput(array(
                'name' => 'file_attachments',
                'required' => false,
                'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 1000, //0000,0000,0000,0000
                                ),
                        ),
                ),
        ));
        
        
        
          $this->createInput(array(
            'name' => 'approvers_csv',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'max' => 20, //0000,0000,0000,0000
                    ),
                ),
                    array(
                            'name' =>'NotEmpty',
                            'options' => array(
                                    'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Approvers are  required...'
                                    ),
                            ),
                    ),
            ),
        ));
        
        
         $this->createInput(array(
            'name' => 'ref_no',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'max' => 13,
                    ),
                ),
            ),
        ));
         
		$this->createInput(array(
            'name' => 'branch_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
		
		
		$this->createInput(array(
		        'name' => 'preferred_date',
		        'required' => true,
		        'validators' => array(
		                array(
		                        'name' => 'StringLength',
		                        'options' => array(
		                                'encoding' => 'UTF-8',
		                                'max' => 100,
		                        ),
		                ),
		                array(
		                        'name' =>'NotEmpty',
		                        'options' => array(
		                                'messages' => array(
		                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter request preferred date'
		                                ),
		                        ),
		                ),
		        ),
		));
		
		
        $this->createInput(array(
            'name' => 'decision_title',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'max' => 50,
                    ),
                ),
                 array(
                            'name' =>'NotEmpty',
                            'options' => array(
                                    'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter request title'
                                    ),
                            ),
                    ),
            ),
        ));
        
        $this->createInput(array(
                'name' => 'document',
                'required' => true,
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 2000,
                                ),
                        ),
                        array(
                                'name' =>'NotEmpty',
                                'options' => array(
                                        'messages' => array(
                                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter request body'
                                        ),
                                ),
                        ),
                ),
        ));

        $this->createInput(array(
            'name' => 'remarks',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'max' => 2000,
                    ),
                ),
                    array(
                            'name' =>'NotEmpty',
                            'options' => array(
                                    'messages' => array(
                                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter remarks'
                                    ),
                            ),
                    ),
            ),
        ));

        $this->inputFilter = $this->getFilter();
    }
    
    public function setDraftFilter()
    {

        $this->createInput(array(
            'name' => 'decision_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        
         $this->createInput(array(
          'name' => 'root_authority_list',
          'required' => false,
          
        ));
         
        $this->createInput(array(
                'name' => 'tpl_title',
                'required' => false,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
        
        
        $this->createInput(array(
                'name' => 'selected_approvers',
                'required' => false,
                'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 1000, //0000,0000,0000,0000
                                ),
                        ),
                ),
        ));
   
        
        $this->createInput(array(
                'name' => 'files',
                'required' => false,
                'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 1000, //0000,0000,0000,0000
                                ),
                        ),
                ),
        ));
        
        
        $this->createInput(array(
                'name' => 'file_attachments',
                'required' => false,
                'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 1000, //0000,0000,0000,0000
                                ),
                        ),
                ),
        ));
        
        
        
          $this->createInput(array(
            'name' => 'approvers_csv',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'max' => 20, //0000,0000,0000,0000
                    ),
                ),                  
            ),
        ));
        
        
         $this->createInput(array(
            'name' => 'ref_no',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'max' => 13,
                    ),
                ),
            ),
        ));
         
		$this->createInput(array(
            'name' => 'branch_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
		
		
		$this->createInput(array(
		        'name' => 'preferred_date',
		        'required' => true,
		));
		
		
        $this->createInput(array(
            'name' => 'decision_title',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
    
        ));
        
        $this->createInput(array(
                'name' => 'document',
                'required' => false,
                
        ));

        $this->createInput(array(
            'name' => 'remarks',
            'required' => false,           
        ));

        $this->inputFilter = $this->getFilter();
    }
}