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

class ApprovalFilter extends AbstractFilter
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
            ),
        ));
		$this->createInput(array(
            'name' => 'create_user',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
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
            ),
        ));
		$this->createInput(array(
            'name' => 'tpl_title',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
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
            ),
        ));

        $this->inputFilter = $this->getFilter();
    }
	public function setStoreInputFilter()
    {
		//$size = new Size(['max' => FileHandler::FILESIZE]);
        $mime = new MimeType(['mimeType' => '']);
		$extension = new Extension(array('extension'=>array('jpg','png','pdf','xlsx')));
		 $size = new Size(array('min'=>2000000)); 

        $this->createInput(array(
            'name' => 'upload_file',
            'required' => true,
			'validators' => array(
                array(
                'name' => 'File\UploadFile',
                    'options' => array(),
                ),
                array(
                    'name' => 'File\Extension',
                    'options' => array(
                        'extension' => array('jpg','jpeg','png','pdf','xlsx'),
                    ),
                ),
                array(
                    'name' => 'File\Size',
                    'options' => array(
						'min' => 8,
						'max' => 1524288,
                    ),
                ),
				array(
					'name' => 'File\MimeType',
					'options' => array(
						'mimeType' => array('image/jpg,image/jpeg,image/png,application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
						'enableHeaderCheck' => true
					)
				)
            )
        ));

        $this->createInput(array(
            'name' => 'file_name',
            'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name'    => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min'      => 1,
							'max'      => 100,
						),
					),
				),
          ));
	
        $this->inputFilter = $this->getFilter();
    }
    
    public function setDetailsFilter(){
     
        
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
                ),
        ));
        
        //disregard buttons fields
        

        $this->createInput(array(
                'name' => 'approve',
                'required' => false,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
        $this->createInput(array(
                'name' => 'reject',
                'required' => false,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
        
        $this->createInput(array(
                'name' => 'recall',
                'required' => false,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
        
        
        $this->inputFilter = $this->getFilter();
    }
}