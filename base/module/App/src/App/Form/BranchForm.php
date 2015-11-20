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

class BranchForm extends Form
{
    /**
     * list - for search
     */
    public function setListForm()
    {
        parent::__construct('branch-search');
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
                'name' => 'search-branch_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-branch_name',
                    'placeholder' => 'Branch Name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => '支社名',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-abbr_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-abbr_name',
					'placeholder' => 'Abbr',
                ),
                'options' => array(
                    'label' => 'Abbr',
//                    'label' => '略称',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-phone',
                'attributes' => array(
                    'type' => 'tel',
                    'id' => 'search-phone',
					'placeholder' => 'Phone',
                ),
                'options' => array(
                    'label' => 'Tel',
//                    'label' => '代表番号',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-address',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-address',
					'placeholder' => 'Address',
                ),
                'options' => array(
                    'label' => 'Address',
//                    'label' => '所在地',
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
                ),
            )
        );
    }

    /**
     * for edit
     */
    public function setEditForm()
    {
        parent::__construct('branch_edit');
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
                'name' => 'branch_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'branch_no',
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
                'name' => 'branch_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'branch_name',
                ),
                'options' => array(
                    'label' => 'Branch',
//                    'label' => '支社名',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'abbr_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'abbr_name',
                ),
                'options' => array(
                    'label' => 'Abbr',
//                    'label' => '略称',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'timezone',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'timezone',
                ),
                'options' => array(
                    'label' => 'Timezone',
                    'value_options' => make_time_list('zone'),
                    'empty_option' => 'Select timezone',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'phone',
                'attributes' => array(
                    'type' => 'tel',
                    'id' => 'phone',
                ),
                'options' => array(
                    'label' => 'Tel',
//                    'label' => '代表番号',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'address',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'address',
                ),
                'options' => array(
                    'label' => 'Address',
//                    'label' => '所在地',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'before_branch_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_branch_name',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_abbr_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_abbr_name',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_timezone',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_timezone',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_phone',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_phone',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_address',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_address',
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