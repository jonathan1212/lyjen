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

class StatusTable extends TableModel
{
    protected $tableName = 'm_status';
    protected $primary = 'status_no';
    protected $priName = 'status_name';

    protected $status_no;           /* tinyint(4) */
    protected $status_name;         /* varchar(20) */
    protected $stat_description;    /* varchar(100) */
    protected $locked;              /* tinyint(1) */
    protected $order_no;            /* tinyint(4) */
    protected $update_time;         /* timestamp */

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
        $this->status_no = "";
        $this->status_name = "";
        $this->stat_description = "";
        $this->locked = "";
        $this->order_no = "";
        $this->update_time = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->status_no = (int) gv('status_no', $_data);
        $this->status_name = (string) gv('status_name', $_data);
        $this->stat_description = (string) gv('stat_description', $_data);
        $this->locked = (int) gv('locked', $_data);
        $this->order_no = (int) gv('order_no', $_data);
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
            'status_no',
            'status_name',
            'stat_description',
            'locked' => new Expression("CASE WHEN locked = 1 THEN 'Yes' ELSE 'No' END"),
            'order_no',
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

        // where
        $select->where($this->tableName . "." . $this->primary . " <> 0");
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                    case 'deleted':
                        $select->where(array($this->tableName . ".{$key}" => $val));
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
                $key == 'status_no'|| $key == 'status_name' || $key == 'locked'
                    || $key == 'order_no'|| $key == 'create_user'
                    || $key == 'create_time'|| $key == 'update_user'
                    || $key == 'update_time'
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
     * insert into m_status
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
            'status_no' => $maxId,
            'status_name' => $this->status_name,
            'stat_description' => $this->stat_description,
            'locked' => $this->locked,
            'order_no' => $maxId,
            'create_user' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        return $this->insert($values);
    }

    /**
     * update m_status
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
            'status_no' => $this->status_no,
            'status_name' => $this->status_name,
            'stat_description' => $this->stat_description,
            'locked' => $this->locked,
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
