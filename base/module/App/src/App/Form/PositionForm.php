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
use App\Model\Table\PositionTable;

class PositionForm extends Form
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
                'name' => 'search-position_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-position_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => '役職名',
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
        parent::__construct('position_edit');
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
                'name' => 'position_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'position_no',
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
                'name' => 'position_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'position_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => '役職名',
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
                'name' => 'approval',
                'type' => 'checkbox',
                'attributes' => array(
                    'id' => 'approval',
                ),
                'options' => array(
                    'label' => 'Approver',
//                    'label' => '稟議承認',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0,
                ),
            )
        );

        $this->add(
            array(
                'name' => 'priority',
                'attributes' => array(
                    'type' => 'number',
                    'id' => 'priority',
                ),
                'options' => array(
                    'label' => 'Priority',
//                    'label' => '優先度',
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
                'name' => 'before_position_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_position_name',
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
                'name' => 'before_approval',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_approval',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_priority',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_priority',
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
     * set position select form (target branch)
     * @param int $_branch_no
     * @param string $_type
     */
    public function setSelectPosition($_branch_no, $_type = null)
    {
        if ($_branch_no) {
            $db = new PositionTable();
            $where = array(
                'position_no <> 0',
                'branch_no' => (int) $_branch_no
            );
            $row = $db->getPairs(null, null, 0, $where);
        }
        else {
            $row = array();
        }

        $this->add(
            array(
                'name' => ($_type == 'search' ? 'search-' : '') . 'position_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => ($_type == 'search' ? 'search-' : '') . 'position_no',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => '役職',
                    'value_options' => $row,
                    'empty_option' => 'Select position',
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