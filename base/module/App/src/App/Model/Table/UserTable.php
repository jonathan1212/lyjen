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

class UserTable extends TableModel
{
    protected $tableName = 'm_user';
    protected $primary = 'user_no';
    protected $priName = 'user_name';

    protected $user_no;         /* int(11) */
    protected $branch_no;       /* mediumint(9) */
    protected $section_no;      /* mediumint(9) */
    protected $position_no;     /* mediumint(9) */
    protected $immediate_superior_no; /* int(11) */
    protected $user_name;       /* varchar(100) */
    protected $email;           /* varchar(100) */
    protected $phone;           /* varchar(30) */
    protected $timezone;        /* varchar(50) */
    protected $lang_no;         /* smallint(6) */
    protected $admin;           /* tinyint(1) */
    protected $valid;           /* tinyint(1) */
    protected $update_time;     /* timestamp */

    // r_user_login_id, r_user_login_pw
    protected $login_id;       /* varbinary(200) */
    protected $login_pw;       /* varbinary(200) */

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
        $this->user_no = "";
        $this->branch_no = "";
        $this->section_no = "";
        $this->position_no = "";
        $this->user_name = "";
        $this->email = "";
        $this->phone = "";
        $this->timezone = "";
        $this->lang_no = "";
        $this->admin = "";
        $this->valid = "";
        $this->update_time = "";
        $this->immediate_superior_no = "";
        $this->login_id = "";
        $this->login_pw = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->user_no = (int) gv('user_no', $_data);
        $this->branch_no = (int) gv('branch_no', $_data);
        $this->section_no = (int) gv('section_no', $_data);
        $this->position_no = (int) gv('position_no', $_data);
        $this->immediate_superior_no = (int) gv('immediate_superior_no', $_data);
        $this->user_name = (string) gv('user_name', $_data);
        $this->email = (string) gv('email', $_data);
        $this->phone = (string) gv('phone', $_data, null);
        $this->timezone = (string) gv('timezone', $_data, null);
        $this->lang_no = (int) gv('lang_no', $_data, null);
        $this->admin = (int) gv('admin', $_data);
        $this->valid = (int) gv('valid', $_data);
        $this->update_time = (string) gv('update_time', $_data);

        $this->login_id = (string) gv('login_id', $_data);
        $this->login_pw = (string) gv('login_pw', $_data);
    }

    /**
     * query for detail
     * @param int $_user_no
     * @return object|boolean
     */
    public function getDetail($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $sql = "SELECT"
                . " m1.user_no, m1.branch_no, m1.section_no, m1.position_no,"
                . " m1.user_name, m1.email, m1.phone, m1.timezone,"
                . " CASE WHEN m1.admin = 1 THEN 'Yes'"
                . " ELSE 'No' END AS admin,"
                . " CASE WHEN m1.valid = 1 THEN 'Yes'"
                . " ELSE 'No' END AS valid, m1.deleted,"
                . " m2.branch_name, m3.section_name, m4.position_name, m5.language"
                . " FROM " . $this->tableName . " AS m1"
                . " LEFT JOIN m_branch AS m2 ON m1.branch_no = m2.branch_no"
                . " LEFT JOIN m_section AS m3 ON m1.section_no = m3.section_no"
                . " LEFT JOIN m_position AS m4 ON m1.position_no = m4.position_no"
                . " LEFT JOIN m_language AS m5 ON m1.lang_no = m5.lang_no"
                . " WHERE"
                . " m1.user_no = ?"
            ;
        return $this->adapter->query($sql, array($_user_no))->current();
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
            'user_no', 'branch_no', 'section_no', 'position_no', 'user_name',
            'email', 'phone', 'timezone', 'lang_no', 'admin', 'valid', 'deleted',
            'create_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.create_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
            'update_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.update_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
        ));
        $select->join(array(
            'm1a' => $this->tableName),
            'm1a.user_no = ' . $this->tableName . '.create_user',
            array(
                'create_user' => 'user_name',
            ),
            'left'
        );
        $select->join(array(
            'm1b' => $this->tableName),
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
        $select->where($this->tableName . "." . $this->primary . " <> 0");
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                    case 'deleted':
                    case 'branch_no':
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
            if (($val == 'asc' || $val == 'desc') &&
                    ($key == 'user_name' || $key == 'section_no' || $key == 'branch_no'
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
     * login information
     * @param string $_login_id
     * @param string $_key_id
     * @return object
     */
   public function getLoginInfo($_login_id, $_key_id)
    {
        if (!$_login_id || !$_key_id) {
            return false;
        }

        $sql = "SELECT"
                . " m1.user_no, m1.branch_no, m1.user_name, m1.admin,"
                . " m2.branch_name, m3.section_name, m4.position_name,"
                . " m4.approval AS approver,"
                . " COALESCE(m1.timezone, m2.timezone, 'UTC') AS timezone,"
                . " COALESCE(m5.lang_id, 'en_US') AS lang_id,"
                . " COALESCE(m5.resource_id, 'en') AS resource_id,"
                . " CAST(DES_DECRYPT(r2.login_pw, " . $this->adapter->platform->quoteValue($_key_id) . ")"
                . " AS CHAR) AS login_pw, r2.initial_flag, t1.ng_count,"
                . " DATEDIFF(UTC_TIMESTAMP, r2.create_time) AS past_day"
                . " FROM " . $this->tableName . " AS m1"
                . " INNER JOIN m_branch AS m2 ON m1.branch_no = m2.branch_no"
                . " INNER JOIN m_section AS m3 ON m1.section_no = m3.section_no"
                . " INNER JOIN m_position AS m4 ON m1.position_no = m4.position_no"
                . " LEFT JOIN m_language AS m5 ON m1.lang_no = m5.lang_no"
                . " LEFT JOIN t_login_failed AS t1 ON m1.user_no = t1.user_no"
                . " AND t1.access_date = CURDATE()"
                . " INNER JOIN r_user_login_id AS r1 ON m1.join_key = r1.join_key"
                . " INNER JOIN r_user_login_pw AS r2 ON m1.join_key = r2.join_key"
                . " WHERE"
                . " m1.valid=1 AND m1.deleted=0 AND r2.pw_no=1"
                . " AND CAST(DES_DECRYPT(r1.login_id, MD5(m1.user_no)) AS BINARY) = ?"
        ;

        return $this->adapter->query($sql, array($_login_id))->current();
    }

    public function getRemindUserChk($_login_id, $_mail)
    {
        if (!$_login_id || !$_mail) {
            return false;
        }

        $sql = "SELECT"
                . " m1.user_no, m1.user_name, m1.email"
                . " FROM " . $this->tableName . " AS m1"
                . " INNER JOIN r_user_login_id AS r1 ON m1.join_key = r1.join_key"
                . " WHERE"
                . " m1.valid=1 AND m1.deleted=0"
                . " AND CAST(DES_DECRYPT(r1.login_id, MD5(m1.user_no)) AS BINARY) = ?"
                . " AND m1.email = ?"
        ;
        $where = array($_login_id, $_mail);
        return $this->adapter->query($sql, $where)->current();
    }

    /**
     * insert into m_user
     * @param int $_user_no
     * @return int|boolean
     */
    public function insertRecord($_user_no)
    {
        // get max value and add 1
        $maxId = $this->getMaxId() + 1;

        $sql = "INSERT INTO " . $this->tableName . " ("
                . " user_no, branch_no, section_no, position_no, immediate_superior_no, user_name,"
                . " email, phone, timezone, lang_no, admin, valid,"
                . " join_key, create_user, create_time, update_user)"
                . " VALUES("
                . $maxId . ", "
                . $this->branch_no . ", " . $this->section_no . ", "
                . $this->position_no . ", "
                . $this->immediate_superior_no . ", "
                . $this->adapter->platform->quoteValue($this->user_name) . ", "
                . $this->adapter->platform->quoteValue($this->email) . ", "
                . ($this->phone
                        ? $this->adapter->platform->quoteValue($this->phone)
                        : 'NULL') . ", "
                . ($this->timezone
                        ? $this->adapter->platform->quoteValue($this->timezone)
                        : 'NULL') . ", "
                . ($this->lang_no ? $this->lang_no : 'NULL') . ", "
                . $this->admin . ", " . $this->valid . ", "
                . " SHA('" . md5(time() . microtime() . rand(0, 1000)) . "'),"
                . (int) $_user_no . ", UTC_TIMESTAMP, " . (int) $_user_no
                . ")"
            ;

        $res = $this->adapter->query($sql, 'execute');
        if ($res->getAffectedRows()) {
            return $maxId;
        }
        else {
            return false;
        }
    }

    /**
     * update
     * @param int $_user_no
     * @param boolean $_self : is own record
     * @return int
     */
    public function updateRecord($_user_no, $_self = false)
    {
        $primary = $this->primary;
        $primaryNo =  $this->$primary;

        $a = array();
        if (!$_self) {
            $a = array(
                'branch_no' => $this->branch_no,
                'section_no' => $this->section_no,
                'position_no' => $this->position_no,
                'immediate_superior_no'=>$this->immediate_superior_no,
                'admin' => $this->admin,
                'valid' => $this->valid,
            );
        }

        $b = array(
            'user_name' => $this->user_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'timezone' => $this->timezone,
            'lang_no' => $this->lang_no,
            'update_user' => (int) $_user_no,
        );
        $values = array_merge($a, $b);

        $where = array(
            $this->primary => $primaryNo,
            'update_time' => $this->update_time,
        );
        return $this->update($values, $where);
    }

    /**
     * insert into r_user_login_id
     * @param int $_user_no
     * @return int|boolean
     */
    public function insertLoginId($_user_no)
    {
        $sql = "INSERT INTO r_user_login_id("
                . " login_id, join_key)"
                . " SELECT "
                . " DES_ENCRYPT("
                . $this->adapter->platform->quoteValue($this->login_id) . ","
                . " MD5(" . (int) $_user_no . ")), join_key"
                . " FROM  " . $this->tableName
                . " WHERE user_no = " . (int) $_user_no
                ;
        $res = $this->adapter->query($sql, 'execute');
        if ($res->getAffectedRows()) {
            return $_user_no;
        }
        else {
            return false;
        }
    }

    /**
     * insert new password
     * @param int $_user_no
     * @param string $_new_pw
     * @param boolean $_initial : is initial password
     * @param boolean $_change : is changed
     * @return int|boolean
     */
    public function insertLoginPw($_user_no, $_new_pw, $_initial = 0, $_change = 0)
    {
        if (!$_user_no || !$_new_pw) {
            return false;
        }

        if ($_change) {
            $row = $this->updateLoginPwNo($_user_no);
            if (!$row) {
                return false;
            }
        }

        $sql = "INSERT INTO r_user_login_pw("
                . " login_pw, join_key, pw_no, initial_flag)"
                . " SELECT "
                . " DES_ENCRYPT("
                . $this->adapter->platform->quoteValue($_new_pw)
                . ", MD5("
                . $this->adapter->platform->quoteValue($_new_pw)
                . ")) AS login_pw, join_key, 1, "
                . ($_initial ? 1 : 0)
                . " FROM  " . $this->tableName
                . " WHERE user_no = " . (int) $_user_no
        ;
        $res = $this->adapter->query($sql, 'execute');
        $row = $res->getAffectedRows();

        if ($_change) {
            $row = $this->deleteLoginPw($_user_no);
        }
        return $row;
    }

    /**
     * save login error number
     * @param int $_user_no
     * @return object
     */
    public function insertLoginFailed($_user_no)
    {
        if (!$_user_no) {
            return false;
        }
        $sql = "INSERT INTO t_login_failed("
                . " user_no, access_date, ng_count)"
                . " VALUES ("
                . (int) $_user_no . ", CURRENT_DATE, 1"
                . ") ON DUPLICATE KEY UPDATE"
                . " ng_count = ng_count + 1";
        return $this->adapter->query($sql, 'execute');
    }

    /**
     * add 1 to pw_no
     * @param int $_user_no
     * @return int|boolean
     */
    protected function updateLoginPwNo($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $sql = "UPDATE r_user_login_pw AS r, "
                . $this->tableName . " AS m"
                . " SET r.pw_no = r.pw_no + 1"
                . " WHERE"
                . " m.join_key = r.join_key"
                . " AND m.user_no = " . (int) $_user_no
        ;
        $res = $this->adapter->query($sql, 'execute');
        return $res->getAffectedRows();
    }

    /**
     * count number of password by user_no
     * @param int $_user_no
     * @return int
     */
    protected function countLoginPw($_user_no)
    {
        $select = $this->getSql()->select();
        $select->columns(array('cnt' => new Expression('COUNT(*)')));
        $select->join(
                array('r' => 'r_user_login_pw'),
                'r.join_key = ' . $this->tableName . '.join_key',
                'pw_no'
        );
        $select->where(array('user_no' => $_user_no));
        $res = $this->selectWith($select)->current();
        return $res->cnt;
    }

    /**
     * check using of changed password
     * @param int $_user_no
     * @param string $_new_pw
     * @return string|boolean
     */
    public function checkLoginPw($_user_no, $_new_pw)
    {
        if (!$_user_no || !$_new_pw) {
            return false;
        }

        $new_pw = $this->adapter->platform->quoteValue($_new_pw);
        $sql = "SELECT m.join_key"
                . " FROM " . $this->tableName . " AS m"
                . " INNER JOIN r_user_login_pw AS r"
                . " ON m.join_key = r.join_key"
                . " WHERE"
                . " m.user_no = ?"
                . " AND r.login_pw = DES_ENCRYPT(" . $new_pw
                . ", MD5(" . $new_pw . "))"
                . " AND r.pw_no < " . (int) FORBID_SAME_PW
        ;

        $res = $this->adapter->query($sql, array($_user_no))->current();
        if ($res) {
            return $res->join_key;
        }
        else {
            return false;
        }
    }

    /**
     * delete previous password by user_no
     * @param int $_user_no
     * @return int|boolean
     */
    public function deleteLoginPw($_user_no)
    {
        if (!$_user_no) {
            return false;
        }
        $cnt = $this->countLoginPw($_user_no);
        if ($cnt <= FORBID_SAME_PW) {
            return true;
        }
        $sql = "DELETE r FROM r_user_login_pw AS r"
                . " INNER JOIN " . $this->tableName . " AS m"
                . " ON m.join_key = r.join_key"
                . " WHERE m.user_no = " . (int) $_user_no
                . " AND pw_no > " . (int) FORBID_SAME_PW;
        $res = $this->adapter->query($sql, 'execute');
        return $res->getAffectedRows();
    }

    /**
     * delete record from t_session in case of forced logout
     * @param int $_user_no
     * @return int|boolean
     */
    public function deleteSession($_user_no)
    {
        if (!$_user_no) {
            return false;
        }
        $sql = "DELETE t1, t2 FROM t_session AS t1"
                . " INNER JOIN t_restrict_login AS t2"
                . " ON t1.session_id = t2.session_id"
                . " WHERE"
                . " t2.user_no = " . (int) $_user_no
        ;
        $res = $this->adapter->query($sql, 'execute');
        return $res->getAffectedRows();
    }
    
    
    public function getApproverProxies($user_no){
        $user_no = (int)$user_no;
        if(!$user_no){
            throw new Exception("Can't find this record with that number");
        }
        
        $select = $this->select(array('user_no'=>$user_no));
        $user = $select->current();
        return  ($user) ? $this->getTiltleUserPairs(array('m_position.position_no'=>$user['position_no'],'m_position.approval'=>1)):null;
    }

    public function getTiltleUserPairs($_where = array(),$ids=array())
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
            'user_no',
            'user_name' => new Expression(
                    " CONCAT(" . $this->tableName . ".user_name,"
                    . " ' (', m_position.position_name, ')')"),
        ));
        $select->join(
            'm_position',
            'm_position.position_no = ' . $this->tableName . '.position_no',
            array(
                'position_name',
            ),
            'inner'
        );

        // where
        $where = array(
            $this->tableName . "." . $this->primary . " <> 0",
            $this->tableName . ".deleted" => 0,
            $this->tableName . ".valid" => 1,
        );
        $select->where($where);
        
        if(count($ids)>0){
           $select->where->in("user_no",$ids);
        }

        if ($_where) {
            $select->where($_where);
        }

        // order
        $select->order(array(
            $this->tableName . ".user_name" => "ASC"
        ));

        $rows = $this->selectWith($select);
        $res = array();
        foreach ($rows as $row) {
            $res[$row->user_no] = $row->user_name;
        }
        return $res;
    }
    
    public function deleteLocked($_user_no){
         if (!$_user_no) {
            return false;
        }
        $sql = "DELETE FROM t_login_failed"
                . " WHERE"
                . " user_no = " . (int) $_user_no
                . " AND access_date = CURRENT_DATE";
        
        $res = $this->adapter->query($sql, 'execute');
        return $res->getAffectedRows();
    }
    
       public function checkUser($_login_id, $_mail)
    {
        if (!$_login_id || !$_mail) {
            return false;
        }

        $sql = "SELECT"
                . " m1.user_no, m1.user_name, m1.email"
                . " FROM " . $this->tableName . " AS m1"
                . " INNER JOIN r_user_login_id AS r1 ON m1.join_key = r1.join_key"
                . " WHERE"
                . " m1.valid=1 AND m1.deleted=0"
                . " AND CAST(DES_DECRYPT(r1.login_id, MD5(m1.user_no)) AS BINARY) = ?"
                . " AND m1.email = ?"
        ;
        $where = array($_login_id, $_mail);
        return $this->adapter->query($sql, $where)->current();
    }
    
}
