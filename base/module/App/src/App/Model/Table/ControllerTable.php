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

class ControllerTable extends TableModel
{
    protected $tableName = 'm_controller';
    protected $primary = 'controller_no';
    protected $priName = 'controller_name';

    protected $controller_no;       /* int(11) */
    protected $controller_name;     /* varchar(32) */
    protected $ctrl_description;    /* varchar(200) */
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
        $this->controller_no = "";
        $this->controller_name = "";
        $this->ctrl_description = "";
        $this->update_time = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->controller_no = (int) gv('controller_no', $_data);
        $this->controller_name = (string) gv('controller_name', $_data);
        $this->ctrl_description = (string) gv('ctrl_description', $_data);
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
            'controller_no', 'controller_name', 'ctrl_description', 'deleted',
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
                    case 'controller_name':
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
            if (($val == 'asc' || $val == 'desc') &&
                    ($key == 'controller_no' || $key == 'controller_name'
                        || $key == 'create_user' || $key == 'create_time'
                        || $key == 'update_user' || $key == 'update_time')
                    ) {
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
     * insert into m_controller
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
            'controller_no' => $maxId,
            'controller_name' => $this->controller_name,
            'ctrl_description' => $this->ctrl_description,
            'create_user' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user' => (int) $_user_no,
        );

        return $this->insert($values);
    }

    /**
     * update m_controller
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
            'controller_name' => $this->controller_name,
            'ctrl_description' => $this->ctrl_description,
            'update_user' => (int) $_user_no,
        );

        $where = array(
            $this->primary => $primaryNo,
            'update_time' => $this->update_time,
        );

        return $this->update($values, $where);
    }

    /**
     * get auth level of target controller
     * @param int $_user_no
     * @param string $_ctrl_name
     * @return int
     */
    public function getRoleLevel($_user_no, $_ctrl_name)
    {
        if (!$_user_no || !$_ctrl_name) {
            return false;
        }
        $sql = "SELECT COALESCE(MAX(r1.level), 0) AS level"
                . " FROM " . $this->tableName . " AS m1"
                . " INNER JOIN r_controller_role AS r1"
                . " ON m1.controller_no = r1.controller_no"
                . " INNER JOIN m_role AS m2 ON r1.role_no = m2.role_no"
                . " INNER JOIN r_user_role AS r2 ON m2.role_no = r2.role_no"
                . " WHERE"
                . " m1.deleted = 0"
                . " AND m2.deleted = 0"
                . " AND r2.user_no = ?"
                . " AND m1.controller_name = ?"
        ;

        $res = $this->adapter->query($sql, array(
            $_user_no, $_ctrl_name))->current();
        return $res->level;
    }
}
