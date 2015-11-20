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
use App\Model\Table\RoleTable;
use App\Model\Table\SectionTable;

class RoleForm extends Form
{
    protected $branch_no;

    public function setBranch($_branch_no)
    {
        $this->branch_no = (int) $_branch_no;
    }

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
                'name' => 'search-role_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-role_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'ロール名',
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
        parent::__construct('role_edit');
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
                'name' => 'role_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'role_no',
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
                'name' => 'role_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'role_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'ロール名',
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
                $user_data = $this->getUserRoleList($_data);
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
                'name' => 'level',
                'type' => 'multi-checkbox',
                'options' => array(
                    'label' => 'Authority',
//                    'label' => '権限',
                    'value_options' => array(
                        '1' => 1, '2' => 2, '3' => 3, '4' => 4),
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
                'name' => 'before_role_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_role_name',
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

    /**
     * get user (target role)
     * @param array $_data
     * @return array
     */
    public function getUserRoleList($_data)
    {
        $db = new RoleTable();
        return $db->getRoleUserPairs(gv('role_no', $_data));
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