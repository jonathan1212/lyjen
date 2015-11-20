<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\Form\Form;

class GameGroupForm extends Form{
    
    public function setEditForm()
    {
        parent::__construct('game-group_edit');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal col-md-6 management-input');
        
        $this->add(
            array(
                'name' => 'game_group_id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'game_group_id',
                ),
            )
        );
        
        $this->add(
            array(
                'name' => 'game_group_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'game_group_name',
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