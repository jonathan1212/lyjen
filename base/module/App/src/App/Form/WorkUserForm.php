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
use App\Model\Table\SectionTable;
use App\Model\Table\PositionTable;
use App\Model\Table\LanguageTable;

class WorkUserForm extends Form
{
    /**
     * for start processing
     */
    public function startForm()
    {
        parent::__construct('work-start');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

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
                'name' => 'upload_file',
                'type' => 'file',
                'attributes' => array(
                    'id' => 'upload_file',
                ),
                'options' => array(
                    'label' => 'File',
//                    'label' => 'ファイル',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'charset',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'charset',
                ),
                'options' => array(
                    'label' => 'Character Setting',
//                    'label' => 'ファイルの文字セット',
                    'value_options' => $this->getCharset(),
                ),
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'upload',
                    'id' => 'submit',
                    'class' => 'btn',
                ),
            )
        );
    }

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
                    'label' => 'User',
//                    'label' => 'ユーザ名',
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
                    'value_options' => $this->getSectionList($_branch_no),
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
                    'type' => 'button',
                    'value' => 'CLR',
                    'id' => 'search-clear',
                    'class' => 'btn',
                ),
            )
        );
    }

    /**
     * for edit
     * @param int $_branch_no
     */
    public function setEditForm($_branch_no = null)
    {
        parent::__construct('user_edit');
        $this->setAttribute('method', 'post');

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
                'name' => 'tmp_user_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'tmp_user_no',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'login_id',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'login_id',
                ),
                'options' => array(
                    'label' => 'Login ID',
//                    'label' => 'ログインID',
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
                    'label' => 'User',
//                    'label' => 'ユーザ名',
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

        $this->add(array(
                'name' => 'before_login_id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_login_id',
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
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Update',
//                    'value' => '更新',
                    'id' => 'submit',
                    'class' => 'btn',
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
                    'class' => 'btn',
                ),
            )
        );
    }


    /**
     * for edit section and position (bundle)
     * @param int $_branch_no
     */
    public function setBatchEditForm($_branch_no = null)
    {
        parent::__construct('user_edit');
        $this->setAttribute('method', 'post');

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
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Set',
//                    'value' => '設定',
                    'id' => 'submit',
                    'class' => 'btn',
                ),
            )
        );
    }

    public function setFinishForm($_work_no)
    {
        parent::__construct('dl_form');
        $this->setAttribute('method', 'post');

        $this->add(
            array(
                'name' => 'work_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'work_no',
                    'value' => (int) $_work_no,
                ),
            )
        );
        $this->add(
            array(
                'name' => 'charset',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'charset',
                ),
                'options' => array(
                    'label' => 'Character Setting',
//                    'label' => 'ファイルの文字セット',
                    'value_options' => $this->getCharset(),
                ),
            )
        );
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

    /**
     * get language select form
     * @return array
     */
    public function getLanguageList()
    {
        $db = new LanguageTable();
        return $db->getPairs();
    }

    /**
     * get char code form
     * @return array
     */
    public function getCharset()
    {
        $rows = include_once APP_DIR . '/config/inc/charset.php';
        return array_combine($rows, $rows);
    }
}
