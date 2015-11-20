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

class UserFilter extends AbstractFilter
{

    /**
     * set InputFilter
     * @param string $_name
     */
    public function setInputFilter($_name = null)
    {
        if ('add' == $_name) {
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
        }

        $this->createInput(array(
            'name' => 'user_no',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
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
                'name' => 'immediate_superior_no',
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
        $this->createInput(array(
            'name' => 'admin',
            'required' => false,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $this->createInput(array(
            'name' => 'valid',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $this->inputFilter = $this->getFilter();
    }
}
