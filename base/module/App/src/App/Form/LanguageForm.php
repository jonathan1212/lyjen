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

class LanguageForm extends Form
{

    /**
     * for edit
     */
    public function setEditForm()
    {
        parent::__construct('language_edit');
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
                'name' => 'lang_no',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'lang_no',
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
                'name' => 'lang_name',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'lang_name',
                ),
                'options' => array(
                    'label' => 'Name',
//                    'label' => '言語名',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'language',
                'attributes' => array(
                    'type' => 'text',
                    'id' => 'language',
                ),
                'options' => array(
                    'label' => 'Language (Native)',
//                    'label' => '言語表記',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'lang_id',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'lang_id',
                ),
                'options' => array(
                    'label' => 'Code1',
//                    'label' => '翻訳ID',
                    'value_options' => $this->getLanguage(),
                    'empty_option' => 'Select langage',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'resource_id',
                'type' => 'select',
                'attributes' => array(
                    'id' => 'resource_id',
                ),
                'options' => array(
                    'label' => 'Code2',
//                    'label' => '翻訳(有効性チェック)',
                    'value_options' => $this->getResource(),
                    'empty_option' => 'Select resource',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'before_lang_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_lang_name',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_language',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_language',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_lang_id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_lang_id',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'before_resource_id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'before_resource_id',
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

    public function setFileUpForm()
    {
        parent::__construct('upload_form');
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
                'name' => 'file_name',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'file_name',
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
                    'label' => 'Translate File',
//                    'label' => '翻訳ファイル',
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
     * get translate file name
     * @return array
     */
    public function getLanguage()
    {
        $path = APP_DIR . '/module/App/language/';
        $dir = dir($path);
        $row = array();
        while(($file = $dir->read()) !== false) {
            if (preg_match('/(\.po)$/', $file)) {
                $name = basename($file, '.po');
                $row[$name] = $name;
            }
        }
        ksort($row);
        return $row;
    }

    /**
     * get directory in {ZEND2_RESOURCE}/languages (for validator)
     * @return array
     */
    public function getResource()
    {
        $resource = ZEND2_RESOURCE . '/languages';
        $dir = dir($resource);
        $row = array();
        while(($file = $dir->read()) !== false) {
            if (preg_match('/(\.)/', $file) || !is_dir($resource . '/' . $file)) {
                continue;
            }
            $row[$file] = $file;
        }
        ksort($row);
        return $row;
    }
}
