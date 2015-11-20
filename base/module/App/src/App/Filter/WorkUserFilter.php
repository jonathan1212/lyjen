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

class WorkUserFilter extends AbstractFilter
{

    /**
     * set InputFilter for upload file (bundle)
     */
    public function setStoreInputFilter()
    {
        $this->createInput(array(
            'name' => 'upload_file',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'File\UploadFile',
                    'options' => array(
                    ),
                ),
                array(
                    'name' => 'File\Extension',
                    'options' => array(
                        'extension' => 'csv',
                    ),
                ),
                array(
                    'name' => 'File\Size',
                    'options' => array(
                        'min' => 8,
                        'max' => 524288,
                    ),
                ),
            ),
        ));

        $this->createInput(array(
            'name' => 'charset',
            'required' => true,
        ));

        $this->inputFilter = $this->getFilter();
    }

    /**
     * set InputFilter
     */
    public function setInputFilter()
    {
        $this->createInput(array(
            'name' => 'login_id',
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
                        'min' => 6,
                        'max' => 32,
                    ),
                ),
            ),
        ));

        $this->createInput(array(
            'name' => 'tmp_user_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $this->createInput(array(
            'name' => 'section_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $this->createInput(array(
            'name' => 'position_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $this->createInput(array(
            'name' => 'user_name',
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
                        'max' => 100,
                    ),
                ),
            ),
        ));
        $this->createInput(array(
            'name' => 'email',
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
                        'max' => 100,
                    ),
                ),
                array(
                    'name' => 'EmailAddress',
                ),
            ),
        ));
        $this->createInput(array(
            'name' => 'phone',
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
                        'max' => 30,
                    ),
                ),
            ),
        ));
        $this->createInput(array(
            'name' => 'timezone',
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
                        'max' => 50,
                    ),
                ),
            ),
        ));
        $this->createInput(array(
            'name' => 'lang_no',
            'required' => false,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $this->inputFilter = $this->getFilter();
    }

    /**
     * set InputFilter for batch
     */
    public function  setBatchInputFilter()
    {
        $this->createInput(array(
            'name' => 'section_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $this->createInput(array(
            'name' => 'position_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $this->inputFilter = $this->getFilter();
    }
}
