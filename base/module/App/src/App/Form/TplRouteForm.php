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
use App\Model\Table\UserTable;
use App\Model\Table\TplRouteTable;
use App\Model\Table\SectionTable;

class TplRouteForm extends Form
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
    public function setEditForm($_data)
    {
        parent::__construct('tpl-route_edit');
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
                'name' => 'tpl_route_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'tpl_route_no',
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
                'name' => 'section_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'section_no',
                ),
                'options' => array(
                    'label' => 'Section',
//                    'label' => '部署',
                    'value_options' => $this->getSectionList($_data),
                    'empty_option' => 'Select section',
                ),
            )
        );
        $name = gv('name', $_data);
        switch ($name) {
            case 'faild':
                $user_data = $this->getUserList($_data);
                break;
            case 'edit':
                $user_data = $this->getUserRouteList($_data);
                break;
            default :
                $user_data = array();
                break;
        }
        $this->add(
            array(
                'name' => 'user_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'user_no',
                    'multiple' => 'multiple',
                ),
                'options' => array(
                    'label' => 'User',
//                    'label' => 'ユーザ',
                    'value_options' => $user_data,
                ),
            )
        );
        $this->add(
            array(
                'name' => 'user_selector',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'user_selector',
                    'multiple' => 'multiple',
                ),
                'options' => array(
                    'label' => 'User',
//                    'label' => 'ユーザ',
                    'value_options' => array(),
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
     * get user select form (for error)
     * @param array $_data
     * @return array
     */
    public function getUserList($_data)
    {
        $ret = array();
        $db = new UserTable();
        $user_no = gv('user_no', $_data);
        if ($user_no) {
            $where = array(
                'm_user.user_no IN (' . implode(', ', $user_no) . ")",
            );
            $ret = $db->getTiltleUserPairs($where);
        }
        return $ret;
    }

    /**
     * get user (target approval route template)
     * @param array $_data
     * @return array
     */
    public function getUserRouteList($_data)
    {
        $db = new TplRouteTable();
        return $db->getRouteUserPairs(gv('tpl_route_no', $_data));
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

    /**
     * get section select form
     * @param array $_data
     * @return array
     */
    public function getSectionList($_data)
    {
        $branch_no = gv('branch_no', $_data);
        $where = array(
            'branch_no' => $branch_no,
        );
        $db = new SectionTable();
        return $db->getPairs(null, null, 0, $where);
    }
}
