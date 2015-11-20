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

class StatusFilter extends AbstractFilter
{
    /**
     * set InputFilter
     */
    public function setInputFilter()
    {

        $this->createInput(array(
            'name' => 'status_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $this->createInput(array(
            'name' => 'status_name',
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
                        'max' => 20,
                    ),
                ),
            ),
        ));
        $this->createInput(array(
            'name' => 'stat_description',
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
                        'max' => 100,
                    ),
                ),
            ),
        ));

        $this->createInput(array(
            'name' => 'locked',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $this->inputFilter = $this->getFilter();
    }
}
