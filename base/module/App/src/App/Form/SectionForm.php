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
use App\Model\Table\SectionTable;

class SectionForm extends Form
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
                'name' => 'search-section_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-section_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => '部署名',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-abbr_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-abbr_name',
                ),
                'options' => array(
                    'label' => 'Abbr',
//                    'label' => '略称',
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
//                    'class' => 'btn',
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
//                    'class' => 'btn',
                ),
            )
        );
    }

    /**
     * for edit
     */
    public function setEditForm()
    {
        parent::__construct('section_edit');
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
                'name' => 'section_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'section_no',
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
                'name' => 'section_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'section_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => '部署名',
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
                'name' => 'before_branch_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_branch_no',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_section_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_section_name',
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
     * get section select form (target branch)
     * @param int $_branch_no
     * @param string $_type
     */
    public function setSelectSection($_branch_no, $_type = null)
    {
        if ($_branch_no) {
            $db = new SectionTable();
            $where = array(
                'section_no <> 0',
                'branch_no' => (int) $_branch_no,
            );
            $row = $db->getPairs(null, null, 0, $where);
        }
        else {
            $row = array();
        }

        $this->add(
            array(
                'name' => ($_type == 'search' ? 'search-' : '') . 'section_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => ($_type == 'search' ? 'search-' : '') . 'section_no',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => '部署',
                    'value_options' => $row,
                    'empty_option' => 'Select section',
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
        $db = new BranchTable();
        $where = array(
            'branch_no <> 0',
        );
        return $db->getPairs(null, null, 0, $where);
    }
}
