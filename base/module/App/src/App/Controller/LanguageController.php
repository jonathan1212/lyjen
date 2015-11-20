<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Controller;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\Hydrator\ObjectProperty;
use App\Controller\AbstractController;
use App\Model\Entity\LanguageEntity;
use App\Form\LanguageForm;
use App\Filter\LanguageFilter;

class LanguageController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * show list
     * @return ViewModel
     */
    public function listAction()
    {
        $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());

        $where = array();
        if (4 > $this->ctrlLv) {
            $where = array_merge($where, array('deleted' => '0'));
        }

        $order = $this->search()->getOrder();
        $current = $this->search()->getPage();
        $max = $this->search()->getDisplayNum();

        $db = new LanguageEntity();
        $page = $db->db()->getPageList($where, $order, $current, $max);

        $values = array(
            'ctrlLv' => $this->ctrlLv,
            'rows' => $page->getCurrentItems()->toArray(),
            'page' => $page->getPages(),
            'admin' => $this->auth()->get('admin'),
            'message' => $message,
        );
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/language/list.phtml');
        return $view;
    }

    /**
     * get create page + insert processing
     * @return ViewModel
     */
    public function addAction()
    {
        $this->init();

        // check auth
        $success = (1 < $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }

        $form = new LanguageForm();
        $form->setEditForm();
        $db = new LanguageEntity();
        $filter = new LanguageFilter();

        $request = $this->getRequest();
        // get edit page
        if (!$request->isPost()) {
            $postRequest = $this->container()->get('postRequest');
            if ($postRequest) {
                $this->container()->clear('postRequest');
                $row = get_array_object(json_decode($postRequest, true));
                $form->bind($row);
            }
        }
        // get update processing
        else {
            $token_id = $this->container()->get('token_id');
            $this->container()->clear('token_id');
            $filter->setCreateToken($token_id);
            $filter->setInputFilter();
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            $success = false;
            if ($form->isValid()) {
                // insert processing
                $success = $db->insertRecord($this->auth()->get('user_no'), $form->getData());
            }
            else {
                // set result to form
                $data = $form->getInputFilter()->getValues();
                $form->bind(get_array_object($data));
            }
            if ($success) {
                $this->flashMessenger()->addMessage("Completed");
//                $this->flashMessenger()->addMessage("処理完了");
                return $this->redirect()->toRoute('app', array(
                    'controller' => 'language',
                    'action' => 'list',
                ));
            }
        }

        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);
        $form->get('token_id')->setAttribute('value', $token_id);
        $form->get('submit')->setAttribute('value', 'Save');
//        $form->get('submit')->setAttribute('value', '登録');
        $form->get('reset')->setAttribute('value', 'Reset');
//        $form->get('reset')->setAttribute('value', 'リセット');

        $values = array(
            'id' => '',
            'action' => 'add',
            'form' => $form,
            'admin' => $this->auth()->get('admin'),
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/language/edit.phtml');
        return $view;
    }

    /**
     * get update page + update processing
     * @return ViewModel
     */
    public function editAction()
    {
        $this->init();
        $err_msg = '';

        // check target and auth by id
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $success = false;
        }
        else {
            $success = (2 < $this->ctrlLv) ? true : false;
        }

        // redirect to error page
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }

        $form = new LanguageForm();
        $form->setEditForm();
        $db = new LanguageEntity();
        $filter = new LanguageFilter();

        // get update information from DB
        $postRequest = $this->container()->get('postRequest');
        if ($postRequest) {
            $this->container()->clear('postRequest');
            $row = get_array_object(json_decode($postRequest, true));
        }
        else {
            $row = $db->db()->getFetchOne($id);
        }

        // if not exist target record
        $success = false;

        if (!$row || $row->deleted) {
            $this->flashMessenger()->addMessage('Target does not exist');
//            $this->flashMessenger()->addMessage('指定レコードは存在しません。');
        }

        else {
            $success = true;
        }

        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'index'
            ));
        }

        $request = $this->getRequest();

        // get update page
        if (!$request->isPost()) {
            $form->bind($row);

            $beforeData = make_before_data($row, 'lang_no');
            if ($beforeData) {
               $form->bind($beforeData);
            }
        }
        // get update processing
        else {
            $token_id = $this->container()->get('token_id');
            $this->container()->clear('token_id');
            $filter->setCreateToken($token_id);
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            $success = $form->isValid();

            if ($success) {
                $success = check_change_data($request->getPost());
                $err_msg = $success ? '' : 'Not Changed';
//                $err_msg = $success ? '' : '変更を確認できません。';
            }

            if ($success) {
                // update
                $success = $db->updateRecord($this->auth()->get('user_no'), $form->getData());
            }
            else {
                // set result to form
                $data = $form->getInputFilter()->getValues();
                $form->bind(get_array_object($data));
            }

            if (false !== $success) {
                $this->flashMessenger()->addMessage("Success");
//                $this->flashMessenger()->addMessage("成功しました。");
                return $this->redirect()->toRoute('app', array(
                    'controller' => 'language',
                    'action' => 'list',
                ));
            }
        }

        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);

        $form->get('token_id')->setAttribute('value', $token_id);
        $form->get('submit')->setAttribute('value', 'Update');
//        $form->get('submit')->setAttribute('value', '更新');
        $form->get('reset')->setAttribute('value', 'Reset');
//        $form->get('reset')->setAttribute('value', 'リセット');

        $values = array(
            'id' => $id,
            'action' => 'edit',
            'form' => $form,
            'admin' => $this->auth()->get('admin'),
            'err_msg' => $err_msg,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/language/edit.phtml');
        return $view;
    }

    /**
     * logical delete
     * @return ViewModel
     */
    public function deleteAction()
    {
        $this->init();

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $success = false;
        }
        else {
            $success = (4 == $this->ctrlLv) ? true : false;
        }
        $db = new LanguageEntity;

        if ($success) {
            $success = $db->db()->logicalDelete($id, $this->auth()->get('user_no'));
        }

        $this->flashMessenger()
                ->addMessage($success ? 'Deleted' : 'Failed');
//                ->addMessage($success ? '削除しました。' : '処理失敗');

        $view = new ViewModel(array(
            'message' => ($success ? 'success' : 'failed')));
        $view->setTemplate('/common/message.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * restore
     * @return ViewModel
     */
    public function restoreAction()
    {
        $this->init();

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $success = false;
        }
        else {
            $success = (4 == $this->ctrlLv) ? true : false;
        }
        $db = new LanguageEntity;

        if ($success) {
            $success = $db->db()->restoreRecord($id, $this->auth()->get('user_no'));
        }

        $this->flashMessenger()
                ->addMessage($success ? 'Restored' : 'Failed');
//                ->addMessage($success ? '復帰しました。' : '処理失敗');

        $view = new ViewModel(array(
            'message' => ($success ? 'success' : 'failed')));
        $view->setTemplate('/common/message.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * download translate file
     * @return ViewModel
     */
    public function dlAction()
    {
        $this->init();
        $request = $this->getRequest();

        if (!$request->isPost()
                || !$request->getPost('translate_file') || 2 > $this->ctrlLv) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }

        $name = $request->getPost('translate_file');

        $file = APP_DIR . '/module/App/language/' . $name . '.po';
        if (!file_exists($file)) {
            exit();
        }

        $view = new ViewModel(array(
            'filename' => $name . '.po',
            'data' => file_get_contents($file),
        ));

        $view->setTemplate('/common/dl.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * upload form for translate file
     * @return ViewModel
     */
    public function uploadFileAction()
    {
        $this->init();
        if (2 > $this->ctrlLv) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }

        $form = new LanguageForm();
        $form->setFileUpForm();

        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);
        $form->get('token_id')->setAttribute('value', $token_id);

        $view = new ViewModel(array(
            'controller' => 'language',
            'action' => 'file-store',
            'form' => $form,
            'label' => 'Translate File',
//            'label' => '翻訳ファイル',
            'max_size' => 524288,
        ));

        $view->setTemplate('/common/file-upload.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * set translate file
     * @return ViewModel
     */
    public function fileStoreAction()
    {
        $this->init();
        $request = $this->getRequest();

        if (!$request->isPost() || 2 > $this->ctrlLv) {
            exit();
        }

        $form = new LanguageForm();
        $form->setFileUpForm();
        $filter = new LanguageFilter();

        $token_id = $this->container()->get('token_id');
        $this->container()->clear('token_id');

        $filter->setCreateToken($token_id);
        $filter->setStoreInputFilter();

        $hydrator = new ObjectProperty();
        $post = $request->getPost();
        $file = $request->getFiles();
        $hydrator->hydrate($file->toArray(), $post);

        $form->setInputFilter($filter->getInputFilter());
        $form->setData($post);

        $success = $form->isValid();

        if (!$success) {
            $token_id = make_token_id();
            $this->container()->set('token_id', $token_id);
            $form->get('token_id')->setAttribute('value', $token_id);

            $values = array(
                'controller' => 'language',
                'action' => 'file-store',
                'form' => $form,
                'label' => 'Translate File',
//                'label' => '翻訳ファイル',
                'max_size' => 524288,
            );
            $view = new ViewModel($values);
            $view->setTemplate('/common/file-upload.phtml');
            $view->setTerminal(true);
            return $view;
        }

        // ***** save file
        $dir_name = APP_DIR . '/module/App/language/';
        $file_name = gv('name', $file->upload_file);
        $tmp_name = gv('tmp_name', $file->upload_file);

        // delete file in server
        if (file_exists($dir_name . $file_name)) {
            unlink($dir_name . $file_name);
        }
        // re-set file
        $success = move_uploaded_file($tmp_name, $dir_name . $file_name);

        if ($success) {
            $cmd = "msgfmt -o {$dir_name}" . str_replace('.po', '.mo', $file_name)
                    . " " . $dir_name . $file_name;
            system($cmd, $success);
        }
        if (0 === $success) {
            $message = 'Completed';
//            $message = '処理完了';
        }
        else {
            $message = 'Failed';
//            $message = '処理失敗';
        }

        $view = new ViewModel(array('message' => $message));
        $view->setTemplate('/common/message.phtml');
        $view->setTerminal(true);
        return $view;
    }
}
