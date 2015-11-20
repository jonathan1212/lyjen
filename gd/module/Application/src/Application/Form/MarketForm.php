<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

use Zend\Form\Form;
use Application\Model\Table\JurisdictionTable;

class MarketForm extends Form{
    
     public function setEditForm()
    {
        parent::__construct('market_edit');
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
                'name' => 'market_id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'market_id',
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
                'name' => 'market_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'market_name',
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
                'name' => 'market_abbr',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'market_abbr',
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
                'name' => 'create_user',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'create_user',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'jurisdiction',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'jurisdiction-id',
                    'class' => 'form-control',
                     'multiple' => 'multiple',
                ),
                'options' => array(
                    'label' => 'Jurisdiction',
                    'label_attributes' => array(
                        'class' => 'col-sm-2 control-label',
                    ),
                    'value_options' => $this->getJurisdictionList('jurisdiction_abbr'),
                ),
            )
        );
        $this->add(
            array(
                'name' => 'criterion_id',
                'type' => 'select',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'label' => 'Criterion',
                    'empty_option' => '1',
                     'label_attributes' => array(
                        'class' => 'col-sm-2 control-label',
                    ),
                   'value_options' => $this->getJurisdictionList(),
                   
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
    public function getJurisdictionList(){
        $where = array('jurisdiction_id <> 0');

        $db = new JurisdictionTable();
        var_dump($db->getJuris());
        //$db->getPairs();
        
    }
    
    public function getCriterionList($_data)
    {
        $jurisdiction_id = $_data['jurisdiction_id'];
        $where = array(
            'jurisdiction_id' => $jurisdiction_id,
        );
        $db = new JurisdictionTable();
        return $db->getPairs(null, null, 0, $where);
    }
}