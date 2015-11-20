<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model;

use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;

class Mysql
{
    /**
     * construct
     */
    public function __construct()
    {
        $this->adapter = GlobalAdapterFeature::getStaticAdapter();
    }

    /**
     * get table list
     * @return array
     */
    public function getTableList()
    {
        $sql = 'SHOW TABLES FROM ' . DB_NAME;
        $res = $this->adapter->query($sql, 'execute')->toArray();

        $i = 0;
        $ret = array();
        while ($i < count($res)) {
            list($ret[$i]) = array_values(gv($i, $res));
            ++ $i;
        }
        return $ret;
    }

    /**
     * get target table
     * @param string $_table
     * @return array
     */
    public function getTableInfo($_table)
    {
        $sql = 'SHOW TABLE STATUS'
                . ' FROM ' . DB_NAME
                . ' LIKE ?';
        return $this->adapter->query($sql, array($_table))->toArray();
    }

    /**
     * get target column
     * @param string $_table
     * @return array
     */
    public function getTableColumns($_table)
    {
        $sql = 'SHOW FULL COLUMNS'
                . ' FROM ' . $this->delQuote($_table);

        try {
            $res = $this->adapter->query($sql, 'execute')->toArray();
        } catch (\Exception $e) {
            return IS_TEST ? $e->getMessage() : null;
        }
        return $res;
    }

    /**
     * check target table, column
     * @param string $_table
     * @param string $_column
     * @return boolean
     */
    public function checkTableColumn($_table, $_column)
    {
        $sql = "SELECT COLUMN_NAME AS col"
                . " FROM"
                . " information_schema.COLUMNS"
                . " WHERE"
                . " TABLE_SCHEMA = ?"
                . " AND TABLE_NAME = ?"
                . " AND COLUMN_NAME = ?"
        ;
        $where = array(
            DB_NAME,
            $_table,
            $_column,
        );
        $ret = $this->adapter->query($sql, $where)->current();
        if ($ret) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * get index infomation
     * @param string $_table
     * @return array
     */
    public function getTableIndex($_table)
    {
        $sql = 'SHOW INDEX '
                . ' FROM ' . $this->delQuote($_table);

        try {
            $res = $this->adapter->query($sql, 'execute')->toArray();
        } catch (\Exception $e) {
            return IS_TEST ? $e->getMessage() : null;
        }
        return $res;
    }

    /**
     * get primary information
     * @param string $_table
     * @return array
     */
    public function getTablePrimary($_table)
    {
        $sql = 'SHOW INDEX '
                . ' FROM ' . $this->delQuote($_table)
                . " WHERE Key_name = 'PRIMARY'";
        try {
            $res = $this->adapter->query($sql, 'execute')->toArray();
        } catch (\Exception $e) {
            return IS_TEST ? $e->getMessage() : null;
        }
        return gv(0, $res);
    }

    /**
     * get foreign key information
     * @param string $_table
     * @return array
     */
    public function getTableFK($_table)
    {
        $sql = 'SELECT a.CONSTRAINT_NAME AS key_name,'
                . ' b.COLUMN_NAME AS column_name,'
                . ' b.REFERENCED_TABLE_SCHEMA AS ref_schema,'
                . ' b.REFERENCED_TABLE_NAME AS ref_table,'
                . ' b.REFERENCED_COLUMN_NAME AS ref_column,'
                . ' a.DELETE_RULE AS delete_rule,'
                . ' a.UPDATE_RULE AS update_rule'
                . ' FROM information_schema.REFERENTIAL_CONSTRAINTS AS a'
                . ' INNER JOIN information_schema.KEY_COLUMN_USAGE AS b'
                . ' ON a.CONSTRAINT_NAME = b.CONSTRAINT_NAME'
                . ' WHERE'
                . ' b.TABLE_SCHEMA = ?'
                . ' AND a.TABLE_NAME = ?';
        return $this->adapter->query($sql, array(DB_NAME, $_table))->toArray();
    }

    /**
     * get string except quotes
     * @param string $_str
     * @return string
     */
    public function delQuote($_str)
    {
        return str_replace(array("'", '"'), "", $_str);
    }
}
