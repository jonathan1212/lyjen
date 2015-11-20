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
use App\Model\Table\UserTable;
use App\Model\Table\BranchTable;
use App\Model\Table\SectionTable;
use App\Model\Table\PositionTable;
use App\Model\Table\LanguageTable;

class UserForm extends Form
{

    /**
     * list - for search
     */
    public function setListForm($_branch_no = null)
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
                'name' => 'search-user_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-user_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'ユーザ名',
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
                'name' => 'search-section_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'search-section_no',
                ),
                'options' => array(
                    'label' => 'Section',
//                    'label' => '部署',
                    'value_options' => $this->setSelectUser($_branch_no),
                    'empty_option' => 'Select section',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-position_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'search-position_no',
                ),
                'options' => array(
                    'label' => 'Position',
//                    'label' => '役職',
                    'value_options' => $this->getPositionList($_branch_no),
                    'empty_option' => 'Select position',
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
     * @param string $_name
     * @param int $_branch_no
     */
    public function setEditForm($_name = null, $_branch_no = null)
    {
        parent::__construct('user_edit');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'content_form');
		
        if ('add' == $_name) {
            $this->add(
                array(
                    'name' => 'login_id',
                    'attributes' => array(
                        'type' => 'text',
                        'id' => 'login_id',
                    ),
                    'options' => array(
                        'label' => 'Login ID',
//                        'label' => 'ログインID',
                    ),
                )
            );
        }

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
                'name' => 'user_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'user_no',
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
                'name' => 'user_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'user_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'ユーザ名',
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
                'name' => 'section_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'section_no',
                ),
                'options' => array(
                    'label' => 'Section',
//                    'label' => '部署',
                    'value_options' => $this->getSectionList($_branch_no),
                    'empty_option' => 'Select section',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'position_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'position_no',
                ),
                'options' => array(
                    'label' => 'Position',
//                    'label' => '役職',
                    'value_options' => $this->getPositionList($_branch_no),
                    'empty_option' => 'Select position',
                ),
            )
        );
        
        $this->add(
                array(
                        'name' => 'immediate_superior_no',
                        'type' => 'select',
                        'attributes' => array(
                                'id' => 'immediate-superior-no',
                        ),
                        'options' => array(
                                'label' => 'Immediate Superior',
                                //                    'label' => '役職',
                                'value_options' => $this->getUserList(),
                                'empty_option' => 'select immediate superior',
                        ),
                )
        );

        $this->add(
            array(
                'name' => 'email',
                'attributes' => array(
                    'type' => 'email',
                    'id' => 'email',
                ),
                'options' => array(
                    'label' => 'E-mail',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'phone',
                'attributes' => array(
                    'type' => 'phone',
                    'id' => 'phone',
                ),
                'options' => array(
                    'label' => 'Phone',
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
                'name' => 'lang_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'lang_no',
                ),
                'options' => array(
                    'label' => 'Language',
//                    'label' => '言語',
                    'value_options' => $this->getLanguageList(),
                    'empty_option' => 'Select language',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'valid',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Active',
//                    'label' => '使用可否',
                    'value_options' => array(
                        'Valid' => array(
                            'value' => 1,
                            'label' => 'Yes',
                            'attributes' => array(
                                'id' => 'valid',
                            ),
                        ),
                        'Invalid' => array(
                            'value' => 0,
                            'label' => 'No',
                            'attributes' => array(
                                'id' => 'invalid',
                            ),
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'name' => 'admin',
                'type' => 'checkbox',
                'options' => array(
                    'label' => 'Administrator',
//                    'label' => '管理者権限',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0,
                ),
            )
        );

        $this->add(array(
                'name' => 'before_user_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_user_name',
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
                'name' => 'before_section_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_section_no',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_position_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_position_no',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_email',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_email',
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
                'name' => 'before_timezone',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_timezone',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_lang_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_lang_no',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_valid',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_valid',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_admin',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_admin',
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
     * get user belonging to branch select form
     * @param int $_branch_no
     * @param string $_type 'search' = 検索 form 用 （name, id に 'search-' を付与）
     */
    public function   setSelectUser($_branch_no, $_type = null)
    {
        if ($_branch_no) {
            $db = new UserTable();
            $where = array(
                'user_no <> 0',
                'valid' => 1,
                'branch_no' => (int) $_branch_no,
            );
            $row = $db->getPairs(null, null, 0, $where);
        }
        else {
            $row = array();
        }

        $this->add(
            array(
                'name' => ($_type == 'search' ? 'search-' : '') . 'user_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => ($_type == 'search' ? 'search-' : '') . 'user_no',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'ユーザ',
                    'value_options' => $row,
                    'empty_option' => 'Select ',
                ),
            )
        );
    }

    /**
     * get user (target section) select form [multiple]
     * @param array $_data
     */
    public function setSelectUserSectionM($_data)
    {
        $where = array();
        if (gv('branch_no', $_data)) {
            $where['m_user.branch_no'] = (int) gv('branch_no', $_data);
        }
        if (gv('section_no', $_data)) {
            $where['m_user.section_no'] = (int) gv('section_no', $_data);
        }
        if (gv('not_user', $_data)) {
            $where[] = 'm_user.user_no NOT IN(' . implode(', ', gv('not_user', $_data)) . ')';
        }
        if (gv('approval', $_data)) {
            $where['m_position.approval'] = 1;
        }

        $db = new UserTable();
        $row = $db->getTiltleUserPairs($where);

        $this->add(
            array(
                'name' => 'user_selector',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'user_selector',
                    'multiple' => 'multiple',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'ユーザ',
                    'value_options' => $row,
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
     * get section select form
     * @param int $_branch_no
     * @return array
     */
    public function getSectionList($_branch_no)
    {
        if (!$_branch_no) {
            return array();
        }
        $where = array(
            'branch_no' => (int) $_branch_no,
        );
        $db = new SectionTable();
        return $db->getPairs(null, null, 0, $where);
    }

    /**
     * get position select form
     * @param int $_branch_no
     * @return array
     */
    public function getPositionList($_branch_no)
    {
        if (!$_branch_no) {
            return array();
        }
        $where = array(
            'branch_no' => (int) $_branch_no,
        );
        $db = new PositionTable();
        return $db->getPairs(null, null, 0, $where);
    }
    
    public function getUserList()
    {
        $where = array(
                'user_no > 1', // 1 is administrator
        );
        $db = new UserTable();
        return $db->getTiltleUserPairs(null, null, 0, $where);
    }

    /**
     * get language select form
     * @return array
     */
    public function getLanguageList()
    {
        $db = new LanguageTable();
        return $db->getPairs();
    }
}
