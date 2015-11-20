<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Form;
use Zend\Form\Form;

class StatusForm extends Form
{
    /**
     * list - for search
     */
    public function setEditForm()
    {
        parent::__construct('status_edit');
        $this->setAttribute('method', 'post');
		$this->setAttribute('class', 'content_form');
		
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
                'name' => 'status_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'status_no',
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
                'name' => 'status_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'status_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'ステータス名',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'stat_description',
                'type' => 'textarea',
                'attributes' => array(
                    'id' => 'stat_description',
                ),
                'options' => array(
                    'label' => 'Description',
//                    'label' => '説明',
                ),
            )
        );

        $this->add(
           array(
                'name' => 'locked',
                'type' => 'checkbox',
                'attributes' => array(
                    'id' => 'locked',
                ),
                'options' => array(
                    'label' => 'Lock',
//                    'label' => 'ロック',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0,
                ),
            )
        );

        $this->add(
            array(
                'name' => 'before_status_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_status_name',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_stat_description',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_stat_description',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_locked',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_locked',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Save',
//                    'value' => '登録',
                    'id' => 'submit',
//                    'class' => 'btn',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'reset',
                'attributes' => array(
                    'type' => 'reset',
                    'value' => 'Reset',
//                    'value' => 'リセット',
                    'id' => 'reset',
//                    'class' => 'btn',
                ),
            )
        );
    }
}