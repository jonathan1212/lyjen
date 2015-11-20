<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\Form\Form;

class JurisdictionForm extends Form{
    
    public function setEditForm()
    {
        parent::__construct('jurisdiction_edit');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal col-md-6 management-input');
        
        $this->add(
            array(
                'name' => 'token_id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'token_id',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'jurisdiction_id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'jurisdiction_id',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'update_time',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'update_time',
                ),
            )
        );


        $this->add(
            array(
                'name' => 'jurisdiction_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'jurisdiction_name',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Name',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label',
                    )
                ),
            )
        );

        $this->add(
            array(
                'name' => 'jurisdiction_abbr',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'jurisdiction_abbr',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Abbreviation',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label',
                    )
                ),
            )
        );
        $this->add(
            array(
                'name' => 'is_regulator',
                'type' => 'checkbox',
                'attributes' => array(
                    'value' => 'unchecked_value'
                ),
                'options' => array(
                    'label' => 'Regulator Required',
                    'use_hidden_element' => true,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label',
                    )
                ),
            )
        );
        $this->add(
            array(
                'name' => 'create_user',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'create_user',
                ),
            )
        );
    
        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Save',
                    'id' => 'submit',
                    'class' => 'btn btn-primary'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'reset',
                'attributes' => array(
                    'type' => 'reset',
                    'value' => 'Reset',
                    'id' => 'reset',
                    'class' => 'btn btn-primary'
                ),
            )
        );
    }
}