<?php 

namespace Application\Model\Table;

use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class TableModel extends TableGateway
{
    protected $tableName;    /* table name */
    protected $primary;      /* primary key */
    protected $priName;      /* column name */
    
    public function __construct($adapter = null)
    {
        if ($adapter == null) {
            $adapter = GlobalAdapterFeature::getStaticAdapter();
        }
        parent::__construct($this->tableName, $adapter);
    }

    /**
     * select by primary key
     * @return boolean|array
     */
    public function getFetchOne($_primary)
    {
        if (!$_primary) {
            return false;
        }
        $where = array(
            $this->primary => $_primary,
        );

        return $this->select($where)->current();
    }

    /**
     * get all record
     * @return array
     */
    public function getFetchAll()
    {
        return $this->select()->toArray();
    }

    /**
     * get result
     * @param array $_where
     * @param array $_order
     * @param int $_limit
     * @param int $_offset
     * @return array
     */
    public function search($_where, $_order = null, $_limit = 30, $_offset = 0)
    {
        $select = $this->getSql()->select();
//        $select->columns('*');
        $select->where($_where);
        if ($_order) {
            $select->order($_order);
        }
        if ($_limit && is_int($_limit)) {
            $select->limit($_limit);
        }
        if (null !== $_offset && is_int($_offset)) {
            $select->offset($_offset);
        }

        return $this->selectWith($select)->toArray();
    }

    /**
     * get query for select method of tableGateway
     * @param object $_select
     * @param array $_where
     * @param array $_order
     * @param int $_limit
     * @param int $_offset
     * @return object
     */
    public function addObj($_select, $_where = array(), $_order = "", $_limit = 30, $_offset = 0)
    {
        if (!$_select) {
            return false;
        }
        if ($_where) {
            $_select->where($_where);
        }
        if ($_order) {
            $_select->order($_order);
        }
        $_select->limit($_limit);
        $_select->offset($_offset);
        return $_select;
    }

    /**
     * logical delete
     * @param string $_primary
     * @param int $_user_no
     * @return int : update number
     */
    public function logicalDelete($_primary, $_user_no)
    {
        if (!$_primary || !$_user_no) {
            return false;
        }

        $values = array(
            'deleted' => 1,
            'update_user' => (int) $_user_no,
        );

        $where = array(
            $this->primary => $_primary,
        );

        return $this->update($values, $where);
    }

    /**
     * update
     * @param int $_user_no
     * @return int : update number
     */
    public function restoreRecord($_primary, $_user_no)
    {
        if (!$_primary || !$_user_no) {
            return false;
        }

        $values = array(
            'deleted' => 0,
            'update_user' => (int) $_user_no,
        );

        $where = array(
            $this->primary => $_primary,
        );

        return $this->update($values, $where);
    }

    /**
     * physical delete
     * @param int|array|string $_primary
     * @return int : deleted number
     */
    public function physicalDelete($_primary)
    {
        if (!$_primary) {
            return false;
        }
        $where = array(
            $this->primary => $_primary,
        );
        return $this->delete($where);
    }

    /**
     * get max value of table
     * @param string $_column
     * @param boolean $_next
     * @return int|boolean
     */
    public function getMaxId($_column = null, $_next = false)
    {
        $column = $_column ? $_column : $this->primary;
        if (!$column) {
            return false;
        }

        $select = $this->getSql()->select();
        $select->columns(array('col' => new Expression('MAX(' . $column . ')')));
        $row = $this->selectWith($select)->toArray();
        $val = gv('col', gv(0, $row));
        if ($_next && is_numeric($val)) {
            return (int) $val + 1;
        }
        else {
            return (int) $val;
        }
    }

    /**
     * get array (key, value)
     * @param string $_key
     * @param string $_val
     * @param boolean $_deleted
     * @param array $_where
     * @return array
     */
    public function getPairs($_key = null, $_val = null, $_deleted = 0, $_where = array())
    {
        $key = $_key ? $_key : $this->primary;
        $val = $_val ? $_val : $this->priName;

        $select = $this->getSql()->select();
        $select->columns(array($key, $val));

        // except delete
        if (!$_deleted) {
            $select->where(array('is_deleted' => 0));
        }

        if ($_where) {
            $select->where($_where);
        }

        $rows = $this->selectWith($select);
        $res = array();
        foreach ($rows as $row) {
            $res[$row->$key] = $row->$val;
        }
        return $res;
    }

    /**
     * dump query
     * @param object $_select
     */
    public function dumpSqlString($_select)
    {
        if (!$_select || !is_object($_select)) {
            return false;
        }
        var_dump($_select->getSqlString($this->adapter->getPlatform()));
    }
}