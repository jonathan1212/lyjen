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

class PageCategoryForm extends Form
{
    /**
     * for edit
     */
    public function setEditForm()
    {
        parent::__construct('page-category_edit');
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
                'name' => 'category_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'category_no',
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
                'name' => 'category_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'category_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'カテゴリ名',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'cate_description',
                'type' => 'textarea',
                'attributes' => array(
                    'id' => 'cate_description',
                ),
                'options' => array(
                    'label' => 'Description',
//                    'label' => '説明',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'before_category_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_category_name',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_cate_description',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_cate_description',
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