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
use App\Model\Entity\PositionEntity;
use App\Form\PositionForm;
use App\Filter\PositionFilter;

class PositionController extends AbstractController
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

        // set branch
        if (!$this->auth()->get('admin')) {
            $param['search-branch_no'] = $this->auth()->get('branch_no');
        }

        $form = new PositionForm();
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

        $db = new PositionEntity();
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
        $view->setTemplate('/' . VIEW_DIR . '/position/list.phtml');
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

        $form = new PositionForm();
        $form->setEditForm();
        $db = new PositionEntity();
        $filter = new PositionFilter();

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
        // get edit processing
        else {

            // check belonging to branch
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
                $this->flashMessenger()->addMessage("Completed");
//                $this->flashMessenger()->addMessage("処理完了");
                return $this->redirect()->toRoute('app', array(
                    'controller' => 'position',
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
        $view->setTemplate('/' . VIEW_DIR . '/position/edit.phtml');
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

        $form = new PositionForm();
        $form->setEditForm();
        $db = new PositionEntity();
        $filter = new PositionFilter();

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

        // user cannot edit record except his branch
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

            $beforeData = make_before_data($row, 'position_no');
            if ($beforeData) {
               $form->bind($beforeData);
            }
        }
        // get update processing
        else {

            // check belonging to branch
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
                    'controller' => 'position',
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

        if (!$this->auth()->get('admin')) {
            $form->setData(array('branch_no' => $this->auth()->get('branch_no')));
        }

        $values = array(
            'id' => $id,
            'action' => 'edit',
            'form' => $form,
            'admin' => $this->auth()->get('admin'),
            'err_msg' => $err_msg,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/position/edit.phtml');
        return $view;
    }

    /**
     * 
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
        $db = new PositionEntity;

        // check belonging to branch
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
        $db = new PositionEntity;

        // check belonging to branch
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

        $form = new PositionForm();
        $form->setSelectPosition($branch_no, $type);

        $view = new ViewModel(array(
            'form_id' => ($type == 'search' ? 'search-' : '') . 'position_no',
            'form' => $form
        ));
        $view->setTemplate('/common/select-form.phtml');
        $view->setTerminal(true);
        return $view;
    }
}
