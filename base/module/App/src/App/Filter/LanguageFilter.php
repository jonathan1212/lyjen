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

class LanguageFilter extends AbstractFilter
{
    /**
     * set InputFilter
     */
    public function setInputFilter()
    {

        $this->createInput(array(
            'name' => 'lang_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $this->createInput(array(
            'name' => 'lang_name',
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
            'name' => 'language',
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
            'name' => 'lang_id',
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
                        'min' => 5,
                        'max' => 5,
                    ),
                ),
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-z]{2}+(_){1}+[A-Z]{2}$/',
                    ),
                ),
            ),
        ));

        $this->createInput(array(
            'name' => 'resource_id',
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
                        'min' => 2,
                        'max' => 5,
                    ),
                ),
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-z]{2}+(_)?+[A-Z]{0,2}?$/',
                    ),
                ),
            ),
        ));

        $this->inputFilter = $this->getFilter();
    }

    /**
     * set InputFilter for upload translate file
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
                        'extension' => 'po',
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
            'name' => 'file_name',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 8,
                        'max' => 8,
                    ),
                ),
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-z]{2}+(_){1}+[A-Z]{2}+(\.po)$/',
                    ),
                ),
            ),
        ));

        $this->inputFilter = $this->getFilter();
    }
}
