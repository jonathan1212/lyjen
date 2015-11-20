<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model\Table;
use Zend\Db\Sql\Predicate\Expression;

class TplDocumentTable extends TableModel
{
    protected $tableName = 'm_tpl_document';
    protected $primary = 'tpl_doc_no';
    protected $priName = 'tpl_title';

    protected $tpl_doc_no;      /* int(11) */
    protected $branch_no;       /* mediumint(9) */
    protected $tpl_title;       /* varchar(100) */
    protected $tpl_body;        /* text */
    protected $update_time;     /* timestamp */

    public function __construct($adapter = null)
    {
        $this->reset();
        parent::__construct($adapter);
    }

   /**
     * reset
     */
    public function reset()
    {
        $this->tpl_doc_no = "";
        $this->branch_no = "";
        $this->tpl_title = "";
        $this->tpl_body = "";
        $this->update_time = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->tpl_doc_no = (int) gv('tpl_doc_no', $_data);
        $this->branch_no = (int) gv('branch_no', $_data);
        $this->tpl_title = (string) gv('tpl_title', $_data);
        $this->tpl_body = \stripslashes(\str_replace("&nbsp;", '', (string) gv('tpl_body', $_data)));
        $this->update_time = (string) gv('update_time', $_data);
    }

    /**
     * for show list
     * @param array $_where
     * @param array $_order
     * @param int $_page
     * @param int $_num
     * @return \Zend\Paginator\Paginator
     */
    public function getPageList($_where = array(), $_order = array(), $_page = 1, $_num = 25)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
            'tpl_doc_no',
            'branch_no',
            'tpl_title',
            'tpl_body',
            'deleted',
            'create_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.create_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
            'update_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.update_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
        ));
        $select->join(array(
            'm1a' => 'm_user'),
            'm1a.user_no = ' . $this->tableName . '.create_user',
            array(
                'create_user' => 'user_name',
            ),
            'left'
        );
        $select->join(array(
            'm1b' => 'm_user'),
            'm1b.user_no = ' . $this->tableName . '.update_user',
            array(
                'update_user' => 'user_name',
            ),
            'left'
        );
        $select->join(array(
            'm2' => 'm_branch'),
            'm2.branch_no = ' . $this->tableName . '.branch_no',
            array(
                'branch_name'
            ),
            'left'
        );

        // where
        $select->where($this->tableName . "." . $this->primary . " <> 0");
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                    case 'branch_no':
                    case 'deleted':
                        $select->where(array($this->tableName . ".{$key}" => $val));
                        break;
                    case 'tpl_body':
                    case 'tpl_title':
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    default:
                        break;
                }
            }
        }

        // order
        if ($_order) {
            list($key, $val) = $_order;
            if (($val == 'asc' || $val == 'desc') && (
                $key == 'tpl_doc_no'|| $key == 'branch_no'
                    || $key == 'tpl_title'|| $key == 'tpl_body'
                    || $key == 'create_user'|| $key == 'create_time'
                    || $key == 'update_user'|| $key == 'update_time'

            )) {
                $select->order(array(
                    $this->tableName . ".{$key}" => $val
                ));
            }
        }

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        $paginator->setCurrentPageNumber((int) $_page);
        $paginator->setItemCountPerPage((int) $_num);

        return $paginator;
    }

    /**
     * insert into m_tpl_document
     * @param int $_user_no
     * @return int|boolean
     */
    public function insertRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        // get max value and add 1
        $maxId = $this->getMaxId() + 1;

        $values = array(
            'tpl_doc_no' => $maxId,
            'branch_no' => $this->branch_no,
            'tpl_title' => $this->tpl_title,
            'tpl_body' => $this->tpl_body,
            'create_user' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        return $this->insert($values);
    }

    /**
     * update m_tpl_document
     * @param int $_user_no
     * @return int|boolean
     */
    public function updateRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $primary = $this->primary;
        $primaryNo =  $this->$primary;

        $values = array(
            'tpl_doc_no' => $this->tpl_doc_no,
            'branch_no' => $this->branch_no,
            'tpl_title' => $this->tpl_title,
            'tpl_body' => $this->tpl_body,
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        $where = array(
            $this->primary => $primaryNo,
            'update_time' => $this->update_time,
        );

        return $this->update($values, $where);
    }
}
