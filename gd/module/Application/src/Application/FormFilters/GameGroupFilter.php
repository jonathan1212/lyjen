<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\FormFilters;

use Application\FormFilters\AbstractFilter;

class GameGroupFilter extends AbstractFilter{
    
     public function setInputFilter()
    {

        $this->createInput(array(
            'name' => 'game_group_id',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $this->createInput(array(
            'name' => 'game_group_name',
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

        $this->inputFilter = $this->getFilter();
    }
}