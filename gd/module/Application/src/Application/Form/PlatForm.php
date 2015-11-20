<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;
use Zend\Form\Form;

class PlatForm extends Form
{

    /**
     * for edit
     */
    public function setEditForm()
    {
        parent::__construct('platform_edit');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal col-md-6 management-input');

        $this->add(
            array(
                'name' => 'platform_id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'platform_id',
                ),
            )
        );
        
        $this->add(
            array(
                'name' => 'platform_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'platform_name',
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
                'name' => 'create_user',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'create_user',
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
