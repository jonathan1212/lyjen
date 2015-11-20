<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model\Table;

class RestrictLoginTable extends TableModel
{
    protected $tableName = 't_restrict_login';
    protected $primary = 'user_no';

    protected $user_no;    /* int(11) */
    protected $session_id; /* varchar(32) */

    /**
     * construct
     * @param object $adapter
     */
    public function __construct($adapter = null)
    {
        parent::__construct($adapter);
    }

    /**
     * insert
     * @param int $_user_no
     */
    public function insertRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }
        $this->physicalDelete($_user_no);

        $sql = "INSERT INTO " . $this->tableName . "("
                . " user_no, session_id)"
                . " VALUES ("
                . (int) $_user_no . ","
                . $this->adapter->platform->quoteValue(session_id())
                . " )";
        $res = $this->adapter->query($sql, 'execute');
        return $res->getAffectedRows();
    }

    public function clean()
    {
        $sql = "DELETE FROM " . $this->tableName
                . " WHERE NOT EXISTS ("
                . " SELECT * FROM t_session AS t"
                . " WHERE t.session_id = " . $this->tableName . ".session_id"
                . ")";
        $res = $this->adapter->query($sql, 'execute');
        return $res->getAffectedRows();
    }
}
