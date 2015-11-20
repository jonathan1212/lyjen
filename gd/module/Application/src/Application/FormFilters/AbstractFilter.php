<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\FormFilters;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class AbstractFilter
{
    protected $inputFilter;
    protected $filter;
    protected $factory;

    public function __construct()
    {
    }

    public function getFilter()
    {
        if (!$this->filter) {
            $this->filter = new InputFilter();
        }
        return $this->filter;
    }

    public function getFactory()
    {
        if (!$this->factory) {
            $this->factory = new InputFactory();
        }
        return $this->factory;
    }

    public function createInput($_data)
    {
        $this->getFilter()->add($this->getFactory()->createInput($_data));
    }

    public function setCreateToken($_data)
    {
        $this->createInput(array(
            'name' => 'token_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Hex',
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 32,
                        'max' => 32,
                    ),
                ),
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => $_data,
                        'messages' => array(
                            \Zend\Validator\Identical::NOT_SAME => 'Forbidden operation.',
                        ),
                    ),

                ),
            ),
        ));
    }

    /**
     * get InputFilter
     * @return type
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $this->setInputFilter();
        }
        return $this->inputFilter;
    }
}
