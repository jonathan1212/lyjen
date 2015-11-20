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
use App\Model\Entity\TplRouteEntity;
use App\Form\TplRouteForm;
use App\Filter\TplRouteFilter;

class TplRouteController extends AbstractController
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

        // get search word
        $param = $this->search()->getSearchParam();
        if ($param) {
            $this->container()->set('search', json_encode($param));
        }
        else {
            $param = $this->container()->get('search');
            $param = json_decode($param, true);
        }

        // set belonging branch
        if (!$this->auth()->get('admin')) {
            $param['search-branch_no'] = $this->auth()->get('branch_no');
        }

        $form = new TplRouteForm();
        $form->setListForm();
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

        $db = new TplRouteEntity();
        $page = $db->db()->getPageList($where, $order, $current, $max);

        $values = array(
            'ctrlLv' => $this->ctrlLv,
            'rows' => $page->getCurrentItems()->toArray(),
            'page' => $page->getPages(),
            'form' => $form,
            'admin' => $this->auth()->get('admin'),
            'message' => $message,
        );
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/tpl-route/list.phtml');
        return $view;
    }

    /**
     * show information
     * @return ViewModel
     */
    public function detailAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toUrl(BASE_URL);
        }

        $this->init();
        $success = (0 < $this->ctrlLv) ? true : false;

        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $db = new TplRouteEntity();
        $row = $db->db()->getFetchOne($id);
        $success = false;
        if (!$row) {
            $this->flashMessenger()->addMessage('Target does not exist');
//            $this->flashMessenger()->addMessage('指定レコードは存在しません。');
        }

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

        $userList = $db->db()->getRouteSectionUser($id)->toArray();
        $values = array(
            'ctrlLv' => $this->ctrlLv,
            'row' => $row,
            'userList' => $userList,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/tpl-route/detail.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * create page + insert processing
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

        $request = $this->getRequest();

        $postRequest = null;
        if (!$request->isPost()) {
            $postRequest = $this->container()->get('postRequest');
            if ($postRequest) {
                $this->container()->clear('postRequest');
                $row = get_array_object(json_decode($postRequest, true));
            }
        }

        $branch_no = $this->auth()->get('branch_no');
        if ($this->auth()->get('admin')) {
            $branch_no = !$request->isPost() ? null : $request->getPost('branch_no');
        }
        $form_opt = array(
            'name' => (!$request->isPost() && !$postRequest ? 'add' : 'faild'),
            'user_no' => (isset($row->user_no) && $row->user_no) ? $row->user_no : $this->params()->fromPost('user_no'),
            'branch_no' => $branch_no,
        );

        $form = new TplRouteForm();
        $form->setEditForm($form_opt);
        $db = new TplRouteEntity();
        $filter = new TplRouteFilter();

        // update
        if ($request->isPost()) {
            // check belonging branch
            if ($this->auth()->get('branch_no') != $this->params()->fromPost('branch_no')
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
            $filter->setInputFilter();
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            $success = false;
            if ($form->isValid()) {
                // insert
                $success = $db->insertRecord($this->auth()->get('user_no'), $form->getData());
            }
            else {
                // set result to form
                $data = $form->getInputFilter()->getValues();
                $form->bind(get_array_object($data));
            }
            if ($success) {
                $this->flashMessenger()->addMessage("Copleted");
//                $this->flashMessenger()->addMessage("処理完了");
                return $this->redirect()->toRoute('app', array(
                    'controller' => 'tpl-route',
                    'action' => 'list',
                ));
            }
        }

        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);
        $form->get('token_id')->setAttribute('value', $token_id);
        $form->get('section_no')->setAttribute('value', '');
        $form->get('submit')->setAttribute('value', 'Save');
//        $form->get('submit')->setAttribute('value', '登録');
        $form->get('reset')->setAttribute('value', 'Reset');
//        $form->get('reset')->setAttribute('value', 'リセット');

        if (!$this->auth()->get('admin')) {
            $form->setData(array('branch_no' => $this->auth()->get('branch_no')));
        }

        $values = array(
            'id' => '',
            'action' => 'add',
            'form' => $form,
            'admin' => $this->auth()->get('admin'),
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/tpl-route/edit.phtml');
        return $view;
    }

    /**
     * get update page + update
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

        $db = new TplRouteEntity();
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

        $branch_no = $this->auth()->get('branch_no');
        if ($this->auth()->get('admin')) {
            $branch_no = !$request->isPost() ? $row->branch_no : $request->getPost('branch_no');
        }
        $form_opt = array(
            'name' => (!$request->isPost() && !$postRequest ? 'edit' : 'faild'),
            'user_no' => (isset($row->user_no) && $row->user_no) ? $row->user_no : $this->params()->fromPost('user_no'),
            'branch_no' => $branch_no,
            'tpl_route_no' => (int) $id,
        );

        $form = new TplRouteForm();
        $form->setEditForm($form_opt);
        $filter = new TplRouteFilter();

        // get update page
        if (!$request->isPost()) {
            $form->bind($row);

            $beforeData = make_before_data($row, 'tpl_route_no');
            if ($beforeData) {
               $form->bind($beforeData);
            }

            $users = array();
            $rows = $db->db()->getRouteUserPairs($id);
            $users = array_keys($rows);

            $beforeData = array(
                'before_user_no' => $users,
            );
        }
        // update
        else {

            // check belonging branch
            if ($this->auth()->get('branch_no') != $this->params()->fromPost('branch_no')
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
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            $success = $form->isValid();

            if ($success) {
                $success = check_change_data($request->getPost());
                $err_msg = $success ? '' : 'Not changed';
//                $err_msg = $success ? '' : '変更を確認できません。';
            }

            if ($success) {
                // update
                $chg_user = ($request->getPost('user_no') != $request->getPost('before_user_no'));
                $success = $db->updateRecord($this->auth()->get('user_no'), $form->getData(), $chg_user);
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
                    'controller' => 'tpl-route',
                    'action' => 'list',
                ));
            }

            $beforeData = array(
                'before_user_no' => $request->getPost('before_user_no'),
            );
        }

        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);
        $form->get('token_id')->setAttribute('value', $token_id);
        $form->get('section_no')->setAttribute('value', '');
        $form->get('submit')->setAttribute('value', 'Update');
//        $form->get('submit')->setAttribute('value', '更新');
        $form->get('reset')->setAttribute('value', 'Reset');
//        $form->get('reset')->setAttribute('value', 'リセット');

        if (!$this->auth()->get('admin')) {
            $form->setData(array('branch_no' => $this->auth()->get('branch_no')));
        }

        $values = array(
            'id' => $id,
            'action' => 'edit',
            'form' => $form,
            'admin' => $this->auth()->get('admin'),
            'err_msg' => $err_msg,
            'beforeData' => $beforeData,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/tpl-route/edit.phtml');
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
        $db = new TplRouteEntity;


        // check belonging branch
        if ($success && !$this->auth()->get('admin')) {
            $row = $db->db()->getFetchOne($id);
            $success = ($this->auth()->get('branch_no') == $row->branch_no);
        }

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
        $db = new TplRouteEntity;


        // check belonging branch
        if ($success && !$this->auth()->get('admin')) {
            $row = $db->db()->getFetchOne($id);
            $success = ($this->auth()->get('branch_no') == $row->branch_no);
        }

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
}
