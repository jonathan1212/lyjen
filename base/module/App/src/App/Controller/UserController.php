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
use App\Controller\AbstractController;
use App\Model\Entity\UserEntity;
use App\Form\UserForm;
use App\Filter\UserFilter;

class UserController extends AbstractController
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

        // get search word from get parameter
        $param = $this->search()->getSearchParam();
        if ($param) {
            $this->container()->set('search', json_encode($param));
        }
        else {
            $param = $this->container()->get('search');
            $param = json_decode($param, true);
        }

        $form = new UserForm();
        $branch_no = gv('search-branch_no', $param);
        $form->setListForm($branch_no);
        if ($param) {
            $form->bind(get_array_object($param));
        }
        $form->get('submit')->setAttribute('value', 'Search');
//        $form->get('submit')->setAttribute('value', '検索');

        $where = $this->search()->getSearchParamConv($param);
        if (4 > $this->ctrlLv) {
            $where = array_merge($where, array('deleted' => 0));
        }

        $order = $this->search()->getOrder();
        $current = $this->search()->getPage();
        $max = $this->search()->getDisplayNum();

        $db = new UserEntity();
        $page = $db->db()->getPageList($where, $order, $current, $max);

        $values = array(
            'ctrlLv' => $this->ctrlLv,
            'user_no' => $this->auth()->get('user_no'),
            'rows' => $page->getCurrentItems()->toArray(),
            'page' => $page->getPages(),
            'form' => $form,
            'admin' => $this->auth()->get('admin'),
            'branch_no' => $this->auth()->get('branch_no'),
            'message' => $message,
        );
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/user/list.phtml');
        return $view;
    }

    /**
     * show information
     * @return ViewModel
     */
    public function detailAction()
    {
        $success = true;
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toUrl(BASE_URL);
        }

        $this->init();
        $self = true;
        if ($id != $this->auth()->get('user_no')) {
            $self = false;
            $success = (0 < $this->ctrlLv) ? true : false;
        }

        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $db = new UserEntity();
        $row = $db->db()->getDetail($id);
        if (!$row) {
            $this->flashMessenger()->addMessage('Target does not exist');
//            $this->flashMessenger()->addMessage('指定レコードは存在しません。');
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'index'
            ));
        }

        $fm = $this->flashMessenger();
        $err_msg = gv('0', $fm->getMessages());

        $edit_btn = false;
        if ($self || $this->auth()->get('admin')
                || (2 < $this->ctrlLv && $this->auth()->get('branch_no') == $row->branch_no)) {
            $edit_btn = true;
        }

        $action = $this->params()->fromRoute('action', 0);
        $values = array(
            'action' => $action,
            'self' => $self,
            'ctrlLv' => $this->ctrlLv,
            'row' => $row,
            'edit_btn' => $edit_btn,
            'err_msg' => $err_msg,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/user/detail.phtml');
        return $view;
    }

    /**
     * show information (for inline)
     * @return ViewModel
     */
    public function viewAction()
    {
        $view = self::detailAction();
        $view->setTerminal(true);
        return $view;
    }

    /**
     * get page of create + insert processing
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

        $form = new UserForm();
        if ($this->auth()->get('admin')) {
            $branch_no = $this->params()->fromPost('branch_no');
        }
        else {
            $branch_no = $this->auth()->get('branch_no');
        }
        $form->setEditForm('add', $branch_no);
        $db = new UserEntity();
        $filter = new UserFilter();

        $request = $this->getRequest();
        // get update page
        if (!$request->isPost()) {
            $postRequest = $this->container()->get('postRequest');
            if ($postRequest) {
                $this->container()->clear('postRequest');
                $row = get_array_object(json_decode($postRequest, true));
                $form->bind($row);
            }
            else {
                // default of valid false
                $form->get('valid')->setAttribute('value', '0');
            }

            if (!$this->auth()->get('admin')) {
                $form->setData(array('branch_no' => $this->auth()->get('branch_no')));
            }
        }
        // update
        else {
            if (($this->auth()->get('branch_no') != $this->params()->fromPost('branch_no')
                    || $this->params()->fromPost('admin'))
                    && !$this->auth()->get('admin')) {
                $this->flashMessenger()->addMessage('Not allowed');
//                $this->flashMessenger()->addMessage('許可されていません。');
                return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'index'
                ));
            }

            $token_id = $this->container()->get('token_id');
            $this->container()->clear('token_id');
            $filter->setCreateToken($token_id);
            $filter->setInputFilter('add');
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            $success = false;
            if ($form->isValid()) {
                // insert
                $success = $db->insertUser($this->auth()->get('user_no'), $form->getData());
            }
            else {
                // set result to form
                $data = $form->getInputFilter()->getValues();
                $form->bind(get_array_object($data));
            }
            if (false !== $success) {
                $this->flashMessenger()
                        ->addMessage("Login PW : [ " . $success . " ]\n\n\n");

                $id = $db->db()->getMaxId();
                return $this->redirect()->toRoute('app', array(
                    'controller' => 'user',
                    'action' => 'detail',
                    'id' => $id
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

        if (!$this->auth()->get('admin')) {
            $form->setData(array('branch_no' => $this->auth()->get('branch_no')));
        }

        $values = array(
            'action' => 'add',
            'form' => $form,
            'admin' => $this->auth()->get('admin'),
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/user/edit.phtml');
        return $view;
    }

    /**
     * get update page + update processing
     * @return ViewModel
     */
    public function editAction()
    {
        $this->init();
        $err_msg = "";

        // check auth by id
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $success = false;
        }
        else if ($id == $this->auth()->get('user_no')) {
            $self = true;
            $success = true;
        }
        else {
            $self = false;
            $success = (2 < $this->ctrlLv) ? true : false;
        }

        // redirect to error page
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }

        $db = new UserEntity();
        // get update information from DB
        $postRequest = $this->container()->get('postRequest');
        if ($postRequest) {
            $this->container()->clear('postRequest');
            $row = get_array_object(json_decode($postRequest, true));
        }
        else {
            $row = $db->db()->getFetchOne($id);
        }
       

        $form = new UserForm();
        if ($this->auth()->get('admin')) {
            $branch_no = $row->branch_no;
        }
        else {
            $branch_no = $this->auth()->get('branch_no');
        }
        $form->setEditForm(null, $branch_no);
        $filter = new UserFilter();

        $success = false;
        if (!$row || $row->deleted) {
            $this->flashMessenger()->addMessage('Target does not exist');
//            $this->flashMessenger()->addMessage('指定レコードは存在しません。');
        }
        // update only belonging branch
        else if (!$this->auth()->get('admin')
                && $this->auth()->get('branch_no') != $row->branch_no) {
            $this->flashMessenger()->addMessage('Not allowed');
//            $this->flashMessenger()->addMessage('許可されていません。');
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

            $beforeData = make_before_data($row, 'user_no'); 
            if ($beforeData) {
               $form->bind($beforeData);
            }
        }
        // update
        else {
            // update only belonging branch
            // user can't update position and section
            if (($self && (
                    $row->section_no != $this->params()->fromPost('section_no')
                    || $row->position_no != $this->params()->fromPost('position_no')))
                || (!$this->auth()->get('admin') &&
                        $this->auth()->get('branch_no') != $this->params()->fromPost('branch_no')
            )) {
                $this->flashMessenger()->addMessage('Not allowed');
//                $this->flashMessenger()->addMessage('許可されていません。');
                return $this->redirect()->toRoute('app', array(
                    'controller' => 'failed', 'action' => 'index'
                ));
            }

            $token_id = $this->container()->get('token_id');
            $this->container()->clear('token_id');
            $filter->setCreateToken($token_id);
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());
       

            $success = $form->isValid();
            

            if ($success) {
                $success = check_change_data($request->getPost());
                $success = true; // temporary hack
                $err_msg = $success ? '' : 'Not changed';
//                $err_msg = $success ? '' : '変更を確認できません。';
            }

            if ($success) {
                // update
                $success = $db->updateUser($this->auth()->get('user_no'), $form->getData());
            }
            else {
                // set result to form
                $data = $form->getInputFilter()->getValues();
                $form->bind(get_array_object($data));
            }

            if (false !== $success && $self) {
                $this->container()->setContainer('user_auth');
                $this->container()->set('user_name', $request->getPost('user_name'));
                $this->container()->set('timezone', $request->getPost('timezone'));
                setcookie('timezone', $request->getPost('timezone'), time() + (60 * 60 * 24 * 30), '/', BASE_DOMAIN);

                $lang = new \App\Model\Table\LanguageTable();
                $row = $lang->getFetchOne($request->getPost('lang_no'));
                $this->container()->set('lang_id', $row->lang_id);
                $this->container()->set('resource_id', $row->resource_id);

                setcookie('lang_id', $row->lang_id, time() + (60 * 60 * 24 * 30), '/', BASE_DOMAIN);
                setcookie('resource_id', $row->resource_id, time() + (60 * 60 * 24 * 30), '/', BASE_DOMAIN);
            }

            if (false !== $success) {
                $this->flashMessenger()->addMessage("Success");
//                $this->flashMessenger()->addMessage("成功しました。");
                return $this->redirect()->toRoute('app', array(
                    'controller' => 'user',
                    'action' => 'detail',
                    'id' => $id
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

        if (!$this->auth()->get('admin')) {
            $form->setData(array('branch_no' => $this->auth()->get('branch_no')));
        }

        $values = array(
            'id' => $id,
            'self' => $self,
            'action' => 'edit',
            'form' => $form,
            'admin' => $this->auth()->get('admin'),
            'err_msg' => $err_msg,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/user/edit.phtml');
        return $view;
    }

    /**
     * logical delete
     * @return ViewModel
     */
    public function deleteAction()
    {
        $this->init();
        $success = false;
        $user_no = $this->auth()->get('user_no');

        // check auth by id
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id || $id == $user_no) {
            $success = false;
        }
        else {
            $success = (4 == $this->ctrlLv) ? true : false;
        }
        $db = new UserEntity();

        // check belonging branch
        if ($success && !$this->auth()->get('admin')) {
            $row = $db->db()->getFetchOne($id);
            $success = ($this->auth()->get('branch_no') == $row->branch_no);
        }

        if ($success) {
            $success = $db->db()->logicalDelete($id, $user_no);
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
        $success = false;
        $user_no = $this->auth()->get('user_no');

        // check auth by id
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id || $id == $user_no) {
            $success = false;
        }
        else {
            $success = (4 == $this->ctrlLv) ? true : false;
        }

        $db = new UserEntity;
        // check belonging branch
        if ($success && !$this->auth()->get('admin')) {
            $row = $db->db()->getFetchOne($id);
            $success = ($this->auth()->get('branch_no') == $row->branch_no);
        }

        if ($success) {
            $success = $db->db()->restoreRecord($id, $user_no);
        }

        $this->flashMessenger()
                ->addMessage($success ? 'Restore' : 'Failed');
//                ->addMessage($success ? '復帰しました。' : '処理失敗');

        $view = new ViewModel(array(
            'message' => ($success ? 'success' : 'failed')));
        $view->setTemplate('/common/message.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * get select form
     * @return ViewModel
     */
    public function getSelectAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            exit();
        }
        $branch_no = $this->params()->fromPost('branch_no');
        $type = $this->params()->fromPost('type');

        $form = new UserForm();
        $form->setSelectUser($branch_no, $type);

        $view = new ViewModel(array(
            'form_id' => ($type == 'search' ? 'search-' : '') . 'user_no',
            'form' => $form
        ));
        $view->setTemplate('/common/select-form.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * get multiple select form
     * @return ViewModel
     */
    public function getUserSectionAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            exit();
        }

        $where = array(
            'branch_no' => (int) $request->getPost('branch_no'),
            'section_no' => (int) $request->getPost('section_no'),
            'not_user' => $request->getPost('not_user'),
            'approval' => (int) $request->getPost('approval'),
        );

        $form = new UserForm();
        $form->setSelectUserSectionM($where);

        $view = new ViewModel(array(
            'form_id' => 'user_selector',
            'form' => $form
        ));
        $view->setTemplate('/common/select-form.phtml');
        $view->setTerminal(true);
        return $view;
    }
}
