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
//use App\Model\Table\WorkUserTable;
use App\Form\WorkUserForm;
use App\Filter\WorkUserFilter;
use App\Model\Entity\WorkUserEntity;

class WorkUserController extends AbstractController
{
    public function __construct()
    {
    }

    public function init()
    {
        $this->setCtrl('user');
        parent::init();

        /**
         * this tool require write permissions
         */
        if (2 > $this->ctrlLv) {
            $this->flashMessenger()->addMessage("Not allowed");
//            $this->flashMessenger()->addMessage("許可されていません。");
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
    }

    /**
     * check own work
     * @return type
     */
    public function selfEditCheck()
    {
        $db = new WorkUserEntity();
        $row = $db->db()->getWork();

        if (!$row || !$row->create_user
                || $this->auth()->get('user_no') != $row->create_user) {

            $this->flashMessenger()->addMessage("Not allowed");
//            $this->flashMessenger()->addMessage("許可されていません。");
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }

        if ($row->work_no) {
            $this->container()->set('work_no', $row->work_no);
        }
    }


    /**
     * download csv
     * @return ViewModel
     */
    public function getTplAction()
    {
        $this->init();

        $view = new ViewModel(array(
            'filename' => 'user_' . date('Ymd') . '.csv',
            'data' => file_get_contents(APP_DIR . '/tpl/user.csv')
        ));
        $view->setTemplate('/common/dl.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * bundle processing 1
     */
    public function startAction()
    {
        $this->init();

        $db = new WorkUserEntity();
        $row = $db->db()->getWork();

        $continue = '';     // url for continu
        $close = '';        // url for kill work
        $working = false;   // check existance of work
        $other = false;     // check own work
        $endFlag = false;   // can kill or not

        if ($row && $row->create_user) {
            $working = true;

            // check own work
            if ($this->auth()->get('user_no') == $row->create_user) {
                $endFlag = true;
                $continue = BASE_URL . '/work-user/list';
            }
            else {
                $other = true;
                $endFlag = (86400 < time() - $row->create_time) ? true : false;
            }

            if ($endFlag) {
                $close = BASE_URL . '/work-user/close';
            }
        }

        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());
        if (!$message && $working) {
            $message = ($other ? 'other user' : '') . 'exist other record';
//            $message = ($other ? '他者が' : '') . '作業中のレコードがあります。';
        }

        $form = null;
        if (!$working) {
            $form = new WorkUserForm();
            $form->startForm();
            $token_id = make_token_id();
            $this->container()->set('token_id', $token_id);
            $form->get('token_id')->setAttribute('value', $token_id);
            $form->get('charset')->setAttribute('value', 'utf-8');
            $form->setAttribute('action', BASE_URL . '/work-user/store');
        }

        $values = array(
            'working' => $working,
            'continue' => $continue,
            'close' => $close,
            'other' => $other,
            'message' => $message,
            'form' => $form,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/work-user/start.phtml');
        return $view;
    }

    /**
     * bundle processing 2
     */
    public function storeAction()
    {
        $this->init();

        $success = true;
        if ($success) {
            $db = new WorkUserEntity();
            $row = $db->db()->getWork();
            if ($row && $row->create_user) {
                $success = false;
            }
        }

        $request = $this->getRequest();
        if (!$request->isPost()) {
            $success = false;
        }

        if (!$success) {
            $this->flashMessenger()->addMessage("No allowed");
//            $this->flashMessenger()->addMessage("許可されていません。");
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }

        $form = new WorkUserForm();
        $form->startForm();
        $filter = new WorkUserFilter();
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
            $msg = "";
            $tmpMsg = "";
            $messages = $form->getMessages();
            foreach ($messages as $key => $val) {
                $msg .= $msg ? "\n" : "";
                switch ($key) {
                    case 'upload_file' :
                        $msg .= $this->translator()->translate("File Error") . "\n";
//                        $msg .= $this->translator()->translate("【ファイル エラー】") . "\n";
                        break;
                    case 'token_id' :
                        $msg .= $this->translator()->translate("Operation Error") . "\n";
//                        $msg .= $this->translator()->translate("【操作 エラー】") . "\n";
                        break;
                    case 'charset' :
                        $msg .= $this->translator()->translate("Character Setting Error") . "\n";
//                        $msg .= $this->translator()->translate("【文字セット 選択エラー】") . "\n";
                        break;
                    default :
                        $msg .= $this->translator()->translate("Error") . "\n";
//                        $msg .= $this->translator()->translate("【エラー】") . "\n";
                        break;
                }
                $tmp = current($val);
                do {
                    if ($tmpMsg == $tmp) {
                        continue;
                    }
                    $msg .= $tmp . "\n";
                    next($val);
                    $tmpMsg = $tmp;
                } while ($tmp = current($val));
            }

            $this->flashMessenger()->addMessage($msg);
            return $this->redirect()->toRoute('app', array(
                'controller' => 'work-user', 'action' => 'start'
            ));
        }

        $i = 0;
        $tmp_name = gv('tmp_name', $file->upload_file);
        $fp = fopen($tmp_name, 'r');
        $line = array();
        while ($res = fgetcsv($fp, 1024)) {
            if (!$success) {
                break;
            }
            // check template
            if (0 == $i) {
                ++ $i;
                $success = ('login_id *' == gv('0', $res)
                        && 'user_name *' == gv('1', $res)
                        && 'email *' == gv('2', $res)
                        && 'phone' == gv('3', $res));
                continue;
            }

            if ('utf-8' != $post->charset) {
                $user_name = mb_convert_encoding(gv('1', $res), 'utf-8', $post->charset);
            }
            else {
                $user_name = gv('1', $res);
            }

            $line[] = array(
                'login_id' => gv('0', $res),
                'user_name' => $user_name,
                'email' => gv('2', $res),
                'phone' => gv('3', $res),
            );
        }
        fclose($fp);
        unlink($tmp_name);

        if ($success && $line) {
            $success = $db->insertWorkRecord(
                    $this->auth()->getUserInfo(), $line);
        }
        else {
            $this->flashMessenger()->addMessage("Data Error");
//            $this->flashMessenger()->addMessage("登録データエラー");
            return $this->redirect()->toRoute('app', array(
                'controller' => 'work-user', 'action' => 'start'
            ));
        }

        // redirect to list page
        if ($success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'work-user', 'action' => 'list'
            ));
        }
    }

    /**
     * kill bundle processing
     */
    public function closeAction()
    {
        $this->init();

        $db = new WorkUserEntity();
        $row = $db->db()->getWork();

        $working = false;   // is exist work record
        $endFlag = false;   // can kill or not
        if ($row && $row->create_user) {
            $working = true;

            // is own work
            if ($this->auth()->get('user_no') == $row->create_user) {
                $endFlag = true;
            }
            else {
                $endFlag = (86400 < time() - $row->create_time) ? true : false;
            }
        }

        if (!$endFlag) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }
        $success = $db->db()->closeRecord($this->auth()->get('user_no'));

        $this->flashMessenger()
                ->addMessage($success ? 'Finished' : 'Failed');
//                ->addMessage($success ? '作業を終了しました。' : '処理失敗');

        return $this->redirect()->toRoute('app', array(
            'controller' => 'work-user', 'action' => 'start'
        ));
    }

    /**
     * show list
     * @return ViewModel
     */
    public function listAction()
    {
        $this->init();
        $this->selfEditCheck();

        $this->container()->clear('batch');

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

        $form = new WorkUserForm();
        $form->setListForm($this->auth()->get('branch_no'));
        if ($param) {
            $form->bind(get_array_object($param));
        }
        $form->get('submit')->setAttribute('value', 'Search');
//        $form->get('submit')->setAttribute('value', '検索');

        $where = $this->search()->getSearchParamConv($param);

        $order = $this->search()->getOrder();
        $current = $this->search()->getPage();
        $max = $this->search()->getDisplayNum();

        $db = new WorkUserEntity();
        $page = $db->db()->getPageList($where, $order, $current, $max);
        $work_no = $this->container()->get('work_no');
        $copy = $db->db()->checkImportData($work_no);

        $values = array(
            'rows' => $page->getCurrentItems()->toArray(),
            'page' => $page->getPages(),
            'form' => $form,
            'copy' => $copy,
            'message' => $message,
        );
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/work-user/list.phtml');
        return $view;
    }

    /**
     * show information
     * @return ViewModel
     */
    public function detailAction()
    {
        $this->init();
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toUrl(BASE_URL);
        }
        $this->selfEditCheck();
        $work_no = $this->container()->get('work_no');

        $db = new WorkUserEntity();
        $row = $db->db()->getDetail($id, $this->auth()->get('user_no'), $work_no);
        if (!$row) {
            $this->flashMessenger()->addMessage('Target does not exist');
//            $this->flashMessenger()->addMessage('指定レコードは存在しません。');
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'index'
            ));
        }

        $fm = $this->flashMessenger();
        $err_msg = gv('0', $fm->getMessages());

        $values = array(
            'row' => $row,
            'err_msg' => $err_msg,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/work-user/detail.phtml');
        $view->setTerminal(true);
        return $view;
    }

    public function batchCheckAction()
    {
        $this->init();
        $this->selfEditCheck();
        $request = $this->getRequest();

        // to list page
        $postRequest = $this->container()->get('postRequest');
        if ($postRequest) {
            $this->container()->clear('postRequest');
            $p = json_decode($postRequest, true);
            $tmp_user = gv('tmp_user_no', $p);
        }
        else if ($request->isPost() && $request->getPost('tmp_user_no')) {
            $tmp_user = $request->getPost('tmp_user_no');
        }
        else {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'work-user', 'action' => 'list'
            ));
        }

        $this->container()->set('batch', json_encode($tmp_user));
        return $this->redirect()->toRoute('app', array(
            'controller' => 'work-user', 'action' => 'batch-edit'
        ));
    }

    public function batchEditAction()
    {
        $this->init();
        $this->selfEditCheck();

        $tmp_user_no = $this->container()->get('batch');
        if (!$tmp_user_no) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'work-user', 'action' => 'list'
            ));
        }
        $tmp_user_no = json_decode($tmp_user_no, true);

        $db = new WorkUserEntity();
        $row = $db->getSearch($tmp_user_no, $this->auth()->get('user_no'));
        if (!$row) {
            $this->flashMessenger()->addMessage('Target does not exist');
//            $this->flashMessenger()->addMessage('指定レコードは存在しません。');
            return $this->redirect()->toRoute('app', array(
                'controller' => 'work-user', 'action' => 'list'
            ));
        }

        $form = new WorkUserForm();
        $form->setBatchEditForm($this->auth()->get('branch_no'));
        $filter = new WorkUserFilter();

        $request = $this->getRequest();
        // update
        if ($request->isPost()) {

            $token_id = $this->container()->get('token_id');
            $this->container()->clear('token_id');
            $filter->setCreateToken($token_id);
            $filter->setBatchInputFilter();
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($request->getPost());

            $success = $form->isValid();

            if ($success) {
                $work_no = $this->container()->get('work_no');
                // update
                $success = $db->updateBatch(
                        $this->auth()->get('user_no'),
                        array_merge($form->getData(), array('tmp_user_no' => $tmp_user_no)),
                        $work_no
                );
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
                    'controller' => 'work-user',
                    'action' => 'list',
                ));
            }
        }

        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);
        $form->get('token_id')->setAttribute('value', $token_id);
        $form->get('submit')->setAttribute('value', 'Set');
//        $form->get('submit')->setAttribute('value', '設定');

        $values = array(
            'row' => $row,
            'form' => $form,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/work-user/batch.phtml');
        return $view;
    }

    /**
     * get update page + update
     * @return ViewModel
     */
    public function editAction()
    {
        $err_msg = "";
        $this->init();
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toUrl(BASE_URL);
        }
        $this->selfEditCheck();

        $db = new WorkUserEntity();
        // get update information from DB
        $postRequest = $this->container()->get('postRequest');
        if ($postRequest) {
            $this->container()->clear('postRequest');
            $row = get_array_object(json_decode($postRequest, true));
        }
        else {
            $work_no = $this->container()->get('work_no');
            $row = $db->db()->getFetchRow($id, $this->auth()->get('user_no'), $work_no);
        }

        $form = new WorkUserForm();
        $form->setEditForm($this->auth()->get('branch_no'));
        $filter = new WorkUserFilter();

        $success = false;
        // if not exist target record
        if (!$row || $row->deleted) {
            $this->flashMessenger()->addMessage('Target does not exist');
//            $this->flashMessenger()->addMessage('指定レコードは存在しません。');
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'index'
            ));
        }

        $request = $this->getRequest();

        // get edit page
        if (!$request->isPost()) {
            $form->bind($row);

            $beforeData = make_before_data($row, 'tmp_user_no');
            if ($beforeData) {
               $form->bind($beforeData);
            }
        }
        // get edit processing
        else {

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
                $work_no = $this->container()->get('work_no');
                // update
                $stat = ($request->getPost('login_id') != $request->getPost('before_login_id'))
                        ? true : false;
                $success = $db->updateRecord(
                        $this->auth()->get('user_no'), $form->getData(), $work_no, $stat);
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
                    'controller' => 'work-user',
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
            'form' => $form,
            'err_msg' => $err_msg,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/work-user/edit.phtml');
        return $view;
    }

    /**
     * to production
     * @return type
     */
    public function copyAction()
    {
        $this->init();
        $this->selfEditCheck();
        $work_no = $this->container()->get('work_no');
        $db = new WorkUserEntity();
        $row = $db->copyToFormal($this->auth()->get('user_no'), $work_no);

        if (!$row) {
            $this->flashMessenger()->addMessage("Failed");
//            $this->flashMessenger()->addMessage("処理失敗");
            return $this->redirect()->toRoute('app', array(
                'controller' => 'work-user',
                'action' => 'list',
            ));
        }

        // exit
        $db->db()->finishRecord($this->auth()->get('user_no'), $work_no);
        $this->flashMessenger()->addMessage("Finished");
//        $this->flashMessenger()->addMessage("作業終了");
        return $this->redirect()->toRoute('app', array(
            'controller' => 'work-user',
            'action' => 'finish',
        ));
    }

    public function finishAction()
    {
        $this->init();
        $work_no = $this->container()->get('work_no');
        if (!$work_no) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'work-user',
                'action' => 'start',
            ));
        }
        $this->container()->clean('work_user');

        $form = new WorkUserForm();
        $form->setFinishForm($work_no);

        $db = new WorkUserEntity();
        $row = $db->db()->getFinishedRecord($this->auth()->get('user_no'), $work_no);

        $fm = $this->flashMessenger();
        $message = gv('0', $fm->getMessages());

        $values = array(
            'form' => $form,
            'rows' => $row,
            'message' => $message,
        );
        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/work-user/finish.phtml');
        return $view;
    }

    public function dataDlAction()
    {
        $this->init();
        $request = $this->getRequest();

        // get edit page
        if (!$request->isPost()) {
            exit();
        }
        $work_no = $request->getPost('work_no');
        $charset = $request->getPost('charset');

        $db = new WorkUserEntity();
        $rows = $db->db()->getFinishedRecord($this->auth()->get('user_no'), $work_no, 1);
        if (!$rows) {
            $this->flashMessenger()->addMessage("Failed");
//            $this->flashMessenger()->addMessage("処理失敗");
            return $this->redirect()->toRoute('app', array(
                'controller' => 'work-user',
                'action' => 'start',
            ));
        }

        $data = '"' . $this->translator()->translate("Section") . '",'
//        $data = '"' . $this->translator()->translate("部署") . '",'
                . '"' . $this->translator()->translate("User") . '",'
//                . '"' . $this->translator()->translate("ユーザ名") . '",'
                . '"Login ID","Login PW"'
                . "\r\n";
        foreach ($rows as $row) {
            $data .=
                    '"' . gv('section_name', $row) . '",'
                    . '"' . gv('user_name', $row) . '",'
                    . '"' . gv('login_id', $row) . '",'
                    . '"' . gv('login_pw', $row) . '"' . "\r\n"
                ;
        }

        if ($charset && 'utf-8' != $charset) {
            $data = mb_convert_encoding($data, $charset, 'utf-8');
        }

        $values = array(
            'filename' => 'user_list_' . date('Ymd') . '.csv',
            'data' => $data,
        );
        $view = new ViewModel($values);
        $view->setTemplate('/common/dl.phtml');
        $view->setTerminal(true);
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
            return $this->redirect()->toUrl(BASE_URL);
        }
        $this->selfEditCheck();

        $db = new WorkUserEntity();
        $success = $db->db()->logicalDelete($id, $this->auth()->get('user_no'));

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
            return $this->redirect()->toUrl(BASE_URL);
        }
        $this->selfEditCheck();

        $db = new WorkUserEntity;
        $success = $db->db()->restoreRecord($id, $this->auth()->get('user_no'));

        $this->flashMessenger()
                ->addMessage($success ? 'Restore' : 'Failed');
//                ->addMessage($success ? '復帰しました。' : '処理失敗');

        $view = new ViewModel(array(
            'message' => ($success ? 'success' : 'failed')));
        $view->setTemplate('/common/message.phtml');
        $view->setTerminal(true);
        return $view;
    }

}
