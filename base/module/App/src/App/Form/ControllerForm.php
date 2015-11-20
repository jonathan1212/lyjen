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

class ControllerForm extends Form
{

    /**
     * list - for search
     */
    public function setListForm()
    {
        parent::__construct('search-form');
        $this->setAttribute('method', 'get');
        $this->add(
            array(
                'name' => 'ord',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'ord',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-controller_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-controller_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'コントローラ名',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Search',
//                    'value' => '検索',
                    'id' => 'search-submit',
                    'class' => 'btn',
                    'onclick' => 'search();',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'clear',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'CLR',
                    'id' => 'search-clear',
                    'class' => 'btn',
                ),
            )
        );
    }

    /**
     * for edit
     */
    public function setEditForm()
    {
        parent::__construct('controller_edit');
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
                'name' => 'controller_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'controller_no',
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
                'name' => 'controller_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'controller_name',
                ),
                'options' => array(
                    'label' => 'Controller',
//                    'label' => 'コントローラ名',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'ctrl_description',
                'attributes' => array(
                    'type' => 'textarea',
                    'id' => 'ctrl_description',
                ),
                'options' => array(
                    'label' => 'Description',
//                    'label' => '説明',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'before_controller_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_controller_name',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_ctrl_description',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_ctrl_description',
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
                ),
            )
        );
    }
}
