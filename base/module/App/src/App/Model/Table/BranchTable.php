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

class BranchTable extends TableModel
{
    protected $tableName = 'm_branch';
    protected $primary = 'branch_no';
    protected $priName = 'branch_name';

    protected $branch_no;       /* mediumint(9) */
    protected $branch_name;     /* varchar(40) */
    protected $abbr_name;       /* varchar(4) */
    protected $timezone;        /* varchar(50) */
    protected $phone;           /* varchar(30) */
    protected $address;         /* varchar(100) */
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
        $this->branch_no = "";
        $this->branch_name = "";
        $this->abbr_name = "";
        $this->timezone = "";
        $this->phone = "";
        $this->address = "";
        $this->update_time = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->branch_no = (int) gv('branch_no', $_data);
        $this->branch_name = (string) gv('branch_name', $_data);
        $this->abbr_name = (string) gv('abbr_name', $_data);
        $this->timezone = (string) gv('timezone', $_data);
        $this->phone = (string) gv('phone', $_data);
        $this->address = (string) gv('address', $_data);
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
        'branch_no',
        'branch_name',
        'abbr_name',
        'timezone',
        'phone',
        'address',
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
                    case 'branch_name':
                    case 'abbr_name':
                    case 'phone':
                    case 'address':
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
     * insert into m_branch
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
            'branch_no' => $maxId,
            'branch_name' => $this->branch_name,
            'abbr_name' => $this->abbr_name,
            'timezone' => $this->timezone,
            'phone' => $this->phone,
            'address' => $this->address,
            'create_user' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        return $this->insert($values);
    }
    
    public function getRecord($branch_no){
        return $this->select(array('branch_no'=>$branch_no))->current();
    }

    /**
     * update m_branch
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
            'branch_no' => $this->branch_no,
            'branch_name' => $this->branch_name,
            'abbr_name' => $this->abbr_name,
            'timezone' => $this->timezone,
            'phone' => $this->phone,
            'address' => $this->address,
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
