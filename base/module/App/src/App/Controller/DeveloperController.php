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
use App\Model\Mysql;

class DeveloperController extends AbstractController
{
    public function __construct()
    {
        if (!IS_TEST) {
            exit();
        }
    }

    /**
     * table list
     * @return ViewModel
     */
    public function tableListAction()
    {
        $this->adminCheck();
        $this->init();
        $success = (4 == $this->ctrlLv) ? true : false;
        if (!$success) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'failed', 'action' => 'forbidden'
            ));
        }

        $db = new Mysql();
        $rows =  $db->getTableList();

        $view = new ViewModel(array(
            'rows' => $rows
        ));
        $view->setTemplate('/developer/table-list.phtml');
        return $view;
    }

    /**
     * detail infomation about target table
     * @return ViewModel
     */
    public function tableDetailAction()
    {
        $this->adminCheck();

        $request = $this->getRequest();
        if (!$request->isPost()) {
            exit();
        }

        $ctrl = (string) $this->params()->fromRoute('__CONTROLLER__');
        $ctrlLv = $this->auth()->getRoleLevel($ctrl);
        if (4 != $ctrlLv) {
            exit();
        }

        $table_id = $this->params()->fromPost('table_id');
        $db = new Mysql();

        $col = $ix = $fk = array();
        $table = $db->getTableInfo($table_id);
        if ($table) {
            $col = $db->getTableColumns($table_id);
            $ix = $db->getTableIndex($table_id);
            $fk = $db->getTableFK($table_id);
        }
        else {
            exit();
        }

        $values = array(
            'table_id' => $table_id,
            'tbl' => gv(0, $table),
            'col' => $col,
            'ix' => $ix,
            'fk' => $fk,
        );
        $view = new ViewModel($values);
        $view->setTemplate('/developer/table-detail.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * skelton file download
     * @return ViewModel
     */
    public function dlSkeltonAction()
    {
        $this->adminCheck();

        $request = $this->getRequest();
        if (!$request->isPost()) {
            exit();
        }

        $table_id = $this->params()->fromPost('table_id');
        $db = new Mysql();

        $baseName = substr($table_id, 2);
        $nameId = conv_nameId($baseName);
        $baseFolder = conv_folderId($baseName);
        $dl_type = $this->params()->fromPost('dl_type');
        $use_detail = $this->params()->fromPost('use_detail');
        $pk = gv('Column_name', $db->getTablePrimary($table_id));
        $branch = $db->checkTableColumn($table_id, 'branch_no');

        switch ($dl_type) {
            case 'ctrl':
                $table = array();
                $col = array();
                break;
            default :
                $table = $db->getTableInfo($table_id);
                $col = $db->getTableColumns($table_id);
                break;
        }

        $values = array(
            'branch' => $branch,
            'use_detail' => $use_detail,
            'nameId' => $nameId,
            'baseFolder' => $baseFolder,
            'tbl' => gv(0, $table),
            'col' => $col,
            'pk' => $pk,
        );
        $view = new ViewModel($values);
        $view->setTemplate('/developer/tpl_' . $dl_type . '.phtml');
        $view->setTerminal(true);
        return $view;
    }

    /**
     * check is administrator
     * @return boolean
     */
    public function adminCheck()
    {
        if ($this->auth()->get('admin')) {
            return true;
        }
        else {
            exit();
        }
    }
}
