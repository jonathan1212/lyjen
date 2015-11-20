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
use App\Model\Table\PageCategoryTable;
use App\Model\Table\ControllerTable;

class PageForm extends Form
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
                'name' => 'search-category_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'search-category_no',
                ),
                'options' => array(
                    'label' => 'Category',
//                    'label' => 'カテゴリ',
                    'value_options' => $this->getPageCategoryList(),
                    'empty_option' => 'Select section',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'search-page_title',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-page_title',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'ページ名',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-page_uri',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-page_uri',
                ),
                'options' => array(
                    'label' => 'Page URI',
//                    'label' => 'ページURI',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'search-page_description',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'search-page_description',
                ),
                'options' => array(
                    'label' => 'Description',
//                    'label' => '説明',
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
        parent::__construct('page_edit');
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
                'name' => 'page_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'page_no',
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
                'name' => 'category_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'category_no',
                ),
                'options' => array(
                    'label' => 'Category',
//                    'label' => 'カテゴリ',
                    'value_options' => $this->getPageCategoryList(),
                    'empty_option' => 'Select category',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'controller_no',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'controller_no',
                ),
                'options' => array(
                    'label' => 'Controller',
//                    'label' => 'コントローラ',
                    'value_options' => $this->getControllerList(),
                    'empty_option' => 'Select controller',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'page_title',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'page_title',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => 'ページ名',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'page_uri',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'page_uri',
                ),
                'options' => array(
                    'label' => 'Page URI',
//                    'label' => 'ページURI',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'page_description',
                'type' => 'textarea',
                'attributes' => array(
                    'id' => 'page_description',
                ),
                'options' => array(
                    'label' => 'Description',
//                    'label' => '説明',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'icon',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'icon',
                ),
                'options' => array(
                    'label' => 'Icon',
//                    'label' => 'アイコン',
                    'value_options' => $this->getIconList(),
                    'empty_option' => 'Select icon',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'use_mobile',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Mobile',
//                    'label' => 'モバイル使用',
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
                'name' => 'before_category_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_category_no',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_controller_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_controller_no',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_page_title',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_page_title',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_page_uri',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_page_uri',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_page_description',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_page_description',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_icon',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_icon',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_use_mobile',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_use_mobile',
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
     * get page and category select form
     * @return array
     */
    public function getPageCategoryList()
    {
        $db = new PageCategoryTable();
        $where = array(
            'category_no <> 0',
        );
        return $db->getPairs(null, null, 0, $where);
    }

    /**
     * get controller select form
     * @return array
     */
    public function getControllerList()
    {
        $db = new ControllerTable();
        $where = array(
            'controller_no <> 0',
        );
        return $db->getPairs(null, null, 0, $where);
		
    }

    /**
     * get icon image file select form
     * @return array
     */
    public function getIconList()
    {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $img = "\public\img\icon";
        } else {
            $img = "/img/icon";
        }
        
        
        $path = DOCUMENT_ROOT . $img;
    //    $path = DOCUMENT_ROOT . '/img/icon';
        $dir = dir($path);
        $ret = array();
        while(($file = $dir->read()) !== false) {
            $info = pathinfo($file);
            if (gv('extension', $info)) {
                $ret[$file] = $file;
            }
        }

        return $ret;
    }
}
