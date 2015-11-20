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

class WorkUserTable extends TableModel
{
    protected $tableName = 'w_user';
    protected $primary = 'tmp_user_no';
    protected $priName = '';

    protected $tmp_user_no;     /* int(11) */
    protected $branch_no;       /* mediumint(9) */
    protected $section_no;      /* mediumint(9) */
    protected $position_no;     /* mediumint(9) */
    protected $login_id;        /* varchar(64) */
    protected $login_pw;        /* varchar(64) */
    protected $user_name;       /* varchar(100) */
    protected $email;           /* varchar(100) */
    protected $phone;           /* varchar(30) */
    protected $timezone;        /* varchar(50) */
    protected $lang_no;         /* smallint(6) */
    protected $join_key;        /* char(40) */
    protected $status;          /* tinyint(1) */
    protected $update_time;     /* timestamp */
    protected $finish_time;     /* datetime */
    protected $work_no;         /* int(11) */

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
        $this->tmp_user_no = "";
        $this->branch_no = "";
        $this->section_no = "";
        $this->position_no = "";
        $this->login_id = "";
        $this->login_pw = "";
        $this->user_name = "";
        $this->email = "";
        $this->phone = "";
        $this->timezone = "";
        $this->lang_no = "";
        $this->join_key = "";
        $this->status = "";
        $this->update_time = "";
        $this->finish_time = "";
        $this->work_no = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->tmp_user_no = (int) gv('tmp_user_no', $_data);
        $this->branch_no = (int) gv('branch_no', $_data);
        $this->section_no = (int) gv('section_no', $_data);
        $this->position_no = (int) gv('position_no', $_data);
        $this->login_id = (string) gv('login_id', $_data);
        $this->login_pw = (string) gv('login_pw', $_data);
        $this->user_name = (string) gv('user_name', $_data);
        $this->email = (string) gv('email', $_data);
        $this->phone = (string) gv('phone', $_data);
        $this->timezone = (string) gv('timezone', $_data);
        $this->lang_no = (int) gv('lang_no', $_data);
        $this->join_key = (string) gv('join_key', $_data);
        $this->status = (int) gv('status', $_data);
        $this->update_time = (string) gv('update_time', $_data);
        $this->finish_time = (string) gv('finish_time', $_data);
        $this->work_no = (int) gv('work_no', $_data);
    }

    /**
     * query for detail
     * @param int $_tmp_user_no
     * @param int $_user_no
     * @param int $_work_no
     * @return object|boolean
     */
    public function getDetail($_tmp_user_no, $_user_no, $_work_no)
    {
        if (!$_tmp_user_no || !$_user_no || !$_work_no) {
            return false;
        }

        $sql = "SELECT"
                . " w.tmp_user_no, w.branch_no, w.section_no, w.position_no,"
                . " w.login_id, w.user_name, w.email, w.phone, w.timezone,"
                . " w.status, w.deleted,"
                . " m1.branch_name, m2.section_name, m3.position_name, m4.language"
                . " FROM " . $this->tableName . " AS w"
                . " LEFT JOIN m_branch AS m1 ON w.branch_no = m1.branch_no"
                . " LEFT JOIN m_section AS m2 ON w.section_no = m2.section_no"
                . " LEFT JOIN m_position AS m3 ON w.position_no = m3.position_no"
                . " LEFT JOIN m_language AS m4 ON w.lang_no = m4.lang_no"
                . " WHERE"
                . " w.tmp_user_no = ?"
                . " AND w.create_user = ?"
                . " AND w.work_no = ?"
            ;
        return $this->adapter->query($sql, array(
            $_tmp_user_no, $_user_no, $_work_no))->current();
    }

    /**
     * select by primary key
     * @param int $_tmp_user_no
     * @param int $_user_no
     * @param int $_work_no
     * @return boolean|array
     */
    public function getFetchRow($_tmp_user_no, $_user_no, $_work_no)
    {
        if (!$_tmp_user_no || !$_user_no || !$_work_no) {
            return false;
        }
        $where = array(
            $this->primary => $_tmp_user_no,
            'create_user' => (int) $_user_no,
            'work_no' => (int) $_work_no,
        );

        return $this->select($where)->current();
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
            'tmp_user_no',
            'branch_no',
            'section_no',
            'position_no',
            'login_id',
            'user_name',
            'email',
            'phone',
            'timezone',
            'lang_no',
            'deleted',
            'status',
        ));
        $select->join(array(
            'm2' => 'm_branch'),
            'm2.branch_no = ' . $this->tableName . '.branch_no',
            array(
                'branch_name'
            ),
            'left'
        );
        $select->join(array(
            'm3' => 'm_section'),
            'm3.section_no = ' . $this->tableName . '.section_no',
            array(
                'section_name'
            ),
            'left'
        );
        $select->join(array(
            'm4' => 'm_position'),
            'm4.position_no = ' . $this->tableName . '.position_no',
            array(
                'position_name'
            ),
            'left'
        );
        $select->join(array(
            'm5' => 'm_language'),
            'm5.lang_no = ' . $this->tableName . '.lang_no',
            array(
                'language'
            ),
            'left'
        );

        // where
        $select->where(array(
            $this->tableName . ".finish_time IS NULL",
            $this->tableName . ".deleted <> 2",
        ));
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                    case 'section_no':
                    case 'position_no':
                        $select->where(array($this->tableName . ".{$key}" => (int) $val));
                        break;
                    case 'user_name':
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
                $key == 'tmp_user_no' || $key == 'section_no'
                    || $key == 'position_no' || $key == 'login_id'
                    || $key == 'user_name' || $key == 'email'
                    || $key == 'phone'
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
     * get working data
     * @param int $_user_no
     * @param int $_work_no
     * @param int $_status
     * @return boolean
     */
    public function getFinishedRecord($_user_no, $_work_no, $_status = null)
    {
        if (!$_user_no || !$_work_no) {
            return false;
        }

        $sql = "SELECT"
                . " w.tmp_user_no, w.branch_no, w.section_no, w.position_no,"
                . " w.login_id, w.login_pw, w.user_name, w.email, w.phone,"
                . " w.timezone, w.status, w.deleted,"
                . " m1.section_name, m2.position_name"
                . " FROM " . $this->tableName . " AS w"
                . " INNER JOIN m_section AS m1 ON w.section_no = m1.section_no"
                . " INNER JOIN m_position AS m2 ON w.position_no = m2.position_no"
                . " WHERE"
                . " w.create_user = " . (int) $_user_no
                . " AND w.work_no = " . (int) $_work_no
        ;
        if ($_status) {
            $sql .= " AND w.status = " . (int) $_status;
        }
        return $this->adapter->query($sql, 'execute')->toArray();
    }

    /**
     * insert into w_user
     * @param int $_user_no
     * @return int|boolean
     */
    public function insertRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $values = array(
            'branch_no' => $this->branch_no,
            'section_no' => $this->section_no,
            'position_no' => $this->position_no,
            'login_id' => $this->login_id,
            'login_pw' => $this->login_pw,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'timezone' => $this->timezone,
            'lang_no' => $this->lang_no,
            'join_key' => $this->join_key,
            'create_user' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user' => (int) $_user_no,
            'work_no' => $this->work_no,
        );

        return $this->insert($values);
    }

    /**
     * update w_user
     * @param int $_user_no
     * @param int $_work_no
     * @param boolean $_status
     * @return int|boolean
     */
    public function updateRecord($_user_no, $_work_no, $_status = false)
    {
        if (!$_user_no) {
            return false;
        }

        $primary = $this->primary;
        $primaryNo =  $this->$primary;

        $values = array(
            'section_no' => $this->section_no,
            'position_no' => $this->position_no,
            'login_id' => $this->login_id,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'timezone' => $this->timezone,
            'lang_no' => $this->lang_no,
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );
        if ($_status) {
            $values['status'] = 0;
        }

        $where = array(
            $this->primary => $primaryNo,
            'work_no' => $_work_no,
        );

        return $this->update($values, $where);
    }

    /**
     * update position and section by id (bundle)
     * @param int $_user_no
     * @param array $_tmp_user_no
     * @param int $_work_no
     * @return int|boolean
     */
    public function updateBatch($_user_no, $_tmp_user_no, $_work_no)
    {
        $values = array(
            'section_no' => $this->section_no,
            'position_no' => $this->position_no,
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        $where = array(
            'work_no' => $_work_no,
            'tmp_user_no IN(' . implode(',', $_tmp_user_no) . ')',
        );

        return $this->update($values, $where);
    }

    /**
     * copy data of work table to production table
     * @param int $_user_no
     * @param int $_work_no
     * @return type
     */
    public function copyToUserMaster($_user_no, $_work_no)
    {
        $sql = "INSERT INTO m_user ("
                . " user_no, branch_no, section_no, position_no,"
                . " user_name, email, phone, timezone, lang_no,"
                . " join_key, create_user, create_time, update_user)"
                . " SELECT"
                . " (@i := @i + 1) + @x, w.branch_no, w.section_no, w.position_no,"
                . " w.user_name, w.email, w.phone, w.timezone, w.lang_no,"
                . " w.join_key, w.create_user, UTC_TIMESTAMP, w.update_user"
                . " FROM"
                . " (SELECT @x := (SELECT COALESCE(MAX(user_no), 0) FROM m_user)) AS x1,"
                . " (SELECT @i := 0) AS x2,"
                . " " . $this->tableName . " AS w"
                . " LEFT JOIN ("
                . " SELECT"
                . " CAST(DES_DECRYPT(r.login_id, MD5(m.user_no)) AS BINARY) AS login_id"
                . " FROM m_user AS m"
                . " INNER JOIN r_user_login_id AS r ON m.join_key = r.join_key"
                . " ) AS b ON w.login_id = b.login_id"
                . " WHERE"
                . " b.login_id IS NULL AND w.deleted = 0"
                . " AND w.work_no = " . (int) $_work_no
                . " AND w.create_user = " . (int) $_user_no
        ;
        return $this->adapter->query($sql, 'execute');
    }

    /**
     * copy data of work table to r_user_login_id table
     * @param int $_work_no
     * @return type
     */
    public function copyToLoginId($_work_no)
    {
        $sql = "INSERT INTO r_user_login_id("
                . " login_id, join_key)"
                . " SELECT DES_ENCRYPT(w.login_id, MD5(m.user_no)), m.join_key"
                . " FROM m_user AS m"
                . " INNER JOIN " . $this->tableName . " AS w"
                . " ON m.join_key = w.join_key AND w.work_no = " . (int) $_work_no
        ;
        return $this->adapter->query($sql, 'execute');
    }

    /**
     * copy data of work table to r_user_login_pw
     * @param int $_work_no
     * @return type
     */
    public function copyToLoginPw($_work_no)
    {
        $sql = "INSERT INTO r_user_login_pw("
                . " login_pw, join_key, pw_no, initial_flag)"
                . " SELECT DES_ENCRYPT(w.login_pw, MD5(w.login_pw)),"
                . " m.join_key, 1, 1"
                . " FROM m_user AS m"
                . " INNER JOIN " . $this->tableName . " AS w"
                . " ON m.join_key = w.join_key AND w.work_no = " . (int) $_work_no
        ;
        return $this->adapter->query($sql, 'execute');
    }

    /**
     * change status for today's work data
     * @param int $_work_no
     * @return
     */
    public function updateStatus($_work_no)
    {
        $sql = "UPDATE " . $this->tableName . " AS w"
                . " INNER JOIN m_user AS m ON w.join_key = m.join_key"
                . " SET w.status = 1"
                . " WHERE"
                . " w.work_no = " . (int) $_work_no
                . " AND DATE(m.create_time) = CURRENT_DATE"
        ;
        return $this->adapter->query($sql, 'execute');
    }


    /**
     * if duplicate id, set status 2
     * @return type
     */
    public function checkLoginId($_work_no)
    {
        if (!$_work_no) {
            return false;
        }

        $values = array(
            'status' => 2,
        );
        $where = array(
            'work_no' => $_work_no,
            'login_id IN (SELECT CAST(DES_DECRYPT(r.login_id, MD5(m.user_no)) AS BINARY)'
                . ' FROM m_user AS m'
                . ' INNER JOIN r_user_login_id AS r ON m.join_key = r.join_key)'
        );

        return $this->update($values, $where);
    }

    /**
     * check required data
     * @param int $_work_no
     * @return boolean
     */
    public function checkImportData($_work_no)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
            'cnt' => new Expression('COUNT(*)'),
        ));
        $select->where(array(
            'work_no' => $_work_no,
            'deleted' => '0',
            '(section_no = 0 OR position_no = 0)',
        ));

        $row = $this->selectWith($select)->current();
        if (0 < $row->cnt) {
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * get work record
     * @return array|boolean
     */
    public function getWork()
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
            'create_user', 'deleted', 'finish_time', 'work_no',
            'create_time' => new Expression('MAX(UNIX_TIMESTAMP(create_time))'),
        ));

        $select->where(array(
            'deleted <> 2',
            'finish_time IS NULL',
        ));

        return $this->selectWith($select)->current();
    }

    /**
     * exit work record
     * @param int $_user_no
     * @return int|boolean
     */
    public function finishRecord($_user_no, $_work_no)
    {
        if (!$_user_no) {
            return false;
        }

        $values = array(
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
            'finish_time' => new Expression('UTC_TIMESTAMP'),
        );

        $where = array(
            'create_user' => (int) $_user_no,
            'work_no' =>(int) $_work_no,
        );

        return $this->update($values, $where);
    }

    /**
     * kill work record
     * @param int $_user_no
     * @return int|boolean
     */
    public function closeRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $values = array(
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
            'finish_time' => new Expression('UTC_TIMESTAMP'),
            'deleted' => 2
        );

        $where = array(
            'finish_time IS NULL',
        );

        return $this->update($values, $where);
    }
}
