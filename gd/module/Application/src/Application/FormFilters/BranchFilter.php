<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\FormFilters;
use Application\FormFilters\AbstractFilter;

class BranchFilter extends AbstractFilter
{
    /**
     * set InputFilter
     */
    public function setInputFilter()
    {

        $this->createInput(array(
            'name' => 'branch_id',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $this->createInput(array(
            'name' => 'branch_name',
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
                        'max' => 40,
                    ),
                ),
            ),
        ));

        $this->createInput(array(
            'name' => 'branch_abbr',
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
                        'max' => 4,
                    ),
                ),
            ),
        ));

        $this->inputFilter = $this->getFilter();
    }
}
