<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\FormFilters;

use Application\FormFilters\AbstractFilter;

class JurisdictionFilter extends AbstractFilter{
    
    public function setInputFilter()
    {

        $this->createInput(array(
            'name' => 'jurisdiction_id',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $this->createInput(array(
            'name' => 'jurisdiction_name',
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
            'name' => 'jurisdiction_abbr',
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