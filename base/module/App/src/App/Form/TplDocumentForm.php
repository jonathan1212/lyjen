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
use App\Model\Table\BranchTable;

class TplDocumentForm extends Form
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
                'name' => 'search-branch_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'search-branch_no',
                ),
                'options' => array(
                    'label' => 'Branch',
//                    'label' => '支社',
                    'value_options' => $this->getBranchList(),
                    'empty_option' => 'Select branch',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-tpl_title',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-tpl_title',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'テンプレート名',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-tpl_body',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-tpl_body',
                ),
                'options' => array(
                    'label' => 'Text',
//                    'label' => 'テンプレート文書',
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
        parent::__construct('tpl-document_edit');
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
                'name' => 'tpl_doc_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'tpl_doc_no',
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
                'name' => 'branch_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'branch_no',
                ),
                'options' => array(
                    'label' => 'Branch',
//                    'label' => '支社',
                    'value_options' => $this->getBranchList(),
                    'empty_option' => 'Select branch',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'tpl_title',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'tpl_title',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'テンプレート名',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'tpl_body',
                'type' => 'textarea',
                'attributes' => array(
                    'id' => 'tpl_body',
                ),
                'options' => array(
                    'label' => 'Text',
//                    'label' => 'テンプレート文書',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'before_branch_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_branch_no',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_tpl_title',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_tpl_title',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_tpl_body',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_tpl_body',
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

    /**
     * get branch select form
     * @return array
     */
    public function getBranchList()
    {
        $where = array(
            'branch_no <> 0',
        );
        $db = new BranchTable();
        return $db->getPairs(null, null, 0, $where);
    }
}
