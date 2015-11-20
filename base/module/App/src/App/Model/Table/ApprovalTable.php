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

class ApprovalTable extends TableModel
{
    protected $tableName = 't_decision';
    protected $primary = 'decision_no';
    protected $priName = 'decision_title';
    protected $status = 'status';
    
    protected $decision_no;
    protected $ref_no;
    protected $branch_no;     
    protected $decision_title;   
    protected $document;       
    protected $remarks;       
    protected $preferred_date; 
    protected $create_user;
    protected $update_time;
    
    protected $status_no;
    

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
		$this->decision_no = "";
		$this->ref_no = "";
		$this->branch_no = "";   
		$this->decision_title = "";  
		$this->document = "";    
		$this->remarks = "";    
		$this->preferred_date = "";
                $this->status = "";
		$this->create_user = "";
                $this->update_time = "";
                $this->status_no = 0;
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->decision_no = (int) gv('decision_no', $_data);
        $this->ref_no = (int) gv('ref_no', $_data);
        $this->branch_no = (int) gv('branch_no', $_data);
        $this->create_user = (int) gv('create_user', $_data);
        $this->decision_title = (string) gv('decision_title', $_data);
        $this->document = (string) gv('document', $_data);
        $this->remarks = (string) gv('remarks', $_data);
        $this->preferred_date = (string) gv('preferred_date', $_data);
        $this->status = (string) gv('status', $_data);
        $this->update_time = (string) gv('update_time', $_data);
        $this->status_no = (int) gv('status_no', $_data);
    }
    
    
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * for show list
     * @param array $_where
     * @param array $_order
     * @param int $_page
     * @param int $_num
     * @return \Zend\Paginator\Paginator
     */
    
    
    public function getPageList($_user_no,$_where = array(), $_order = array(), $_page = 1, $_num = 25)
    {
        
     
        $_user_no = (int)$_user_no;
        if (!$_user_no) {
            return false;
        }
        
        $sql = $this->getSql();
        $select = $sql->select();
        
        $select->columns(array(
            'ref_no',
            'branch_no',
            'decision_title',
            'preferred_date',
            'status',
            'deleted',
            'create_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.create_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
            'update_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.update_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
        ));
        $select->join(array(
            'm1a' => 't_decision_approval'),
            'm1a.decision_no = ' . $this->tableName . '.decision_no',
            array(
                'decision_no' => 'decision_no',
            ),
            'left'
        );
        
        $select->join(array(
            'm1b' => 'm_user'),
            'm1b.user_no = ' . $this->tableName . '.create_user',
            array(
                'create_user' => 'user_name',
            ),
            'left'
        );

        // where 
        $select->where("m1a.user_no = " . (int)$_user_no . " AND (m1a.turn = 1 OR m1a.status_no = 6 )AND (t_decision.status = 'progressing' OR t_decision.status = 'receiving' OR t_decision.status = 'recalled')");
         
        
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                    case 'branch_no':
                    case 'ref_no':
                    case 'decision_title': 
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    case 'document':
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    case 'create_user':
                        $select->where($this->tableName . ".{$key}"
                                . " = " . $this->adapter->platform->quoteValue( $val));
                        break;
                    case 'create_time': 
                        $select->where($this->tableName . ".{$key}"         
                      . " BETWEEN " . $this->adapter->platform->quoteValue($_where['create_time']) . "AND" .$this->adapter->platform->quoteValue($_where['create_time2']));
                        break;
                    
                    case 'update_time': 
                        $select->where($this->tableName . ".{$key}"         
                      . " BETWEEN " . $this->adapter->platform->quoteValue($_where['update_time']) . "AND" .$this->adapter->platform->quoteValue($_where['update_time2']));
                        break;
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
                $key == 'ref_no'|| $key == 'decision_title'
                    || $key == 'create_user' || $key == 'create_time'
                    || $key == 'update_user' || $key == 'update_time'
            )) {
                $select->order(array(
                    $this->tableName . ".{$key}" => $val
                ));
            }
        }
   
        $select->order('update_time DESC');
        $select->group('ref_no');


        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        $paginator->setCurrentPageNumber((int) $_page);
        $paginator->setItemCountPerPage((int) $_num);

        return $paginator;
                  
    }
     
    public function getProgressList($_user_no, $_where = array(), $_order = array(), $_page = 1, $_num = 25)
    {
        $_user_no = (int)$_user_no;
        if (!$_user_no) {
            return false;
        }
        
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
            'decision_no',
            'ref_no',
            'branch_no',
            'decision_title',
            'preferred_date',
            'status',
            'deleted',
            'create_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.create_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
            'update_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.update_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
        ));

        
        $select->join(array(
            'm1b' => 'm_user'),
            'm1b.user_no = ' . $this->tableName . '.create_user',
            array(
                'create_user' => 'user_name',
            ),
            'left'
        );
        
        $select->where($this->tableName . "." . $this->primary . " <> 0 AND m1b.user_no = ". (int)$_user_no . " AND (t_decision.status = 'progressing' OR t_decision.status = 'receiving')");
       
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                   case 'decision_title': 
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    case 'document':
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    case 'create_user':
                        $select->where($this->tableName . ".{$key}"
                                . " = " . $this->adapter->platform->quoteValue( $val));
                        break;
                    case 'create_time': 
                        $select->where($this->tableName . ".{$key}"         
                      . " BETWEEN " . $this->adapter->platform->quoteValue($_where['create_time']) . "AND" .$this->adapter->platform->quoteValue($_where['create_time2']));
                        break;
                    
                    case 'update_time': 
                        $select->where($this->tableName . ".{$key}"         
                      . " BETWEEN " . $this->adapter->platform->quoteValue($_where['update_time']) . "AND" .$this->adapter->platform->quoteValue($_where['update_time2']));
                        break;
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
                $key == 'ref_no'|| $key == 'decision_title'
                    || $key == 'create_time' || $key = 'preferred_date' || $key == 'update_time' || $key == 'status'
            )) {
                $select->order(array(
                    $this->tableName . ".{$key}" => $val
                ));
            }
        }
        
        $select->order('update_time DESC');
        
        $select->group('ref_no');

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        $paginator->setCurrentPageNumber((int) $_page);
        $paginator->setItemCountPerPage((int) $_num);

        return $paginator;
    }
    public function getDraftList($_user_no, $_where = array(), $_order = array(), $_page = 1, $_num = 25)
    {
        $_user_no = (int)$_user_no;
        if (!$_user_no) {
            return false;
        }
        
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
            'decision_no',
            'ref_no',
            'branch_no',
            'decision_title',
            'preferred_date',
            'status',
            'deleted',
            'create_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.create_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
            'update_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.update_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
        ));
       
        $select->join(array(
            'm1b' => 'm_user'),
            'm1b.user_no = ' . $this->tableName . '.create_user',
            array(
                'create_user' => 'user_name',
            ),
            'left'
        );
        
        $select->where($this->tableName . "." . $this->primary . " <> 0 AND m1b.user_no = ". (int)$_user_no . " AND (t_decision.status = 'draft' OR t_decision.status = 'recalled_by_owner' OR t_decision.status = 'recalled')" );
        
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                    case 'decision_title': 
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    case 'document':
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    case 'create_user':
                        $select->where($this->tableName . ".{$key}"
                                . " = " . $this->adapter->platform->quoteValue( $val));
                        break;
                    case 'create_time': 
                        $select->where($this->tableName . ".{$key}"         
                      . " BETWEEN " . $this->adapter->platform->quoteValue($_where['create_time']) . "AND" .$this->adapter->platform->quoteValue($_where['create_time2']));
                        break;
                    
                    case 'update_time': 
                        $select->where($this->tableName . ".{$key}"         
                      . " BETWEEN " . $this->adapter->platform->quoteValue($_where['update_time']) . "AND" .$this->adapter->platform->quoteValue($_where['update_time2']));
                        break;
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
                $key == 'ref_no'|| $key == 'decision_title'
                    || $key == 'create_time' || $key == 'update_time' || $key == 'status'
            )) {
                $select->order(array(
                    $this->tableName . ".{$key}" => $val
                ));
            }
        }
        
        $select->order('update_time DESC');
        
        $select->group('ref_no');

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        $paginator->setCurrentPageNumber((int) $_page);
        $paginator->setItemCountPerPage((int) $_num);

        return $paginator;
    }
    
    public function getCompletedList($_user_no, $_where = array(), $_order = array(), $_page = 1, $_num = 25)
    {
        $_user_no = (int)$_user_no;
        if (!$_user_no) {
            return false;
        }
        
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
            'decision_no',
            'ref_no',
            'branch_no',
            'decision_title',
            'preferred_date',
            'status',
            'deleted',
            'create_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.create_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
            'update_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.update_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
        ));
       
        $select->join(array(
            'm1b' => 'm_user'),
            'm1b.user_no = ' . $this->tableName . '.create_user',
            array(
                'create_user' => 'user_name',
            ),
            'left'
        );
        
        $select->where($this->tableName . "." . $this->primary . " <> 0 AND m1b.user_no = ". (int)$_user_no . " AND (t_decision.status = 'complete' OR t_decision.status = 'rejected')" );
        
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                    case 'ref_no':
                    case 'decision_title': 
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    case 'document':
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    case 'create_user':
                        $select->where($this->tableName . ".{$key}"
                                . " = " . $this->adapter->platform->quoteValue( $val));
                        break;
                    case 'create_time': 
                        $select->where($this->tableName . ".{$key}"         
                      . " BETWEEN " . $this->adapter->platform->quoteValue($_where['create_time']) . "AND" .$this->adapter->platform->quoteValue($_where['create_time2']));
                        break;
                    
                    case 'update_time': 
                        $select->where($this->tableName . ".{$key}"         
                      . " BETWEEN " . $this->adapter->platform->quoteValue($_where['update_time']) . "AND" .$this->adapter->platform->quoteValue($_where['update_time2']));
                        break;
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
                $key == 'ref_no'|| $key == 'decision_title'
                    || $key == 'create_time' || $key == 'preferred_date' || $key == 'update_time' || $key == 'status'
            )) {
                $select->order(array(
                    $this->tableName . ".{$key}" => $val
                ));
            }
        }
        
        $select->order('update_time DESC');
        
        $select->group('ref_no');

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
        
        $_user_no = (int)$_user_no;
        
        if (!$_user_no) {
            return false;
        }

        // get max value and add 1
        $maxId = (int)$this->getMaxId() + 1;
        
        if(!$maxId){
            
            throw new \Exception("Unable to get max id");
        }

        $values = array(
            'branch_no' => $maxId,
            'ref_no' => $this->ref_no,
            'branch_no' => $this->branch_no,
            'status_no' => $this->status_no,
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
    
    
    /**
     * 
     * @param unknown $_user_no
     * @param unknown $ref_no
     */
    public function insertDraft($_user_no,$_ref_no){
        $_user_no = (int)$_user_no;
        
        if (!$_user_no) {
            return false;
        }
        
        //get user branch no
        $userObj = new UserTable();
        $branchData = $userObj->select(array('user_no'=>$_user_no))->current();
        $branch_no = (int)$branchData['branch_no'];
        
        if(!$branch_no){
         throw new \Exception("Invalid Brach no");    
        }
        
        $data = array(
                'decision_title'=>'draf',
                'status' => 'draft',
                'branch_no'=>(int)$branchData['branch_no'],
                'ref_no' => $_ref_no,
                'update_user' => (int) $_user_no,
                'create_user' => (int) $_user_no,
                'create_time' => new Expression('UTC_TIMESTAMP'),
                'update_time' => new Expression('UTC_TIMESTAMP'),
        );
        
       $this->insert($data);
       return  $this->getLastInsertValue();
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
    
    

    
    public function getCategory1($branch_no){
        $branch_no = (int)$branch_no;
        if(!$branch_no){
            throw new \Exception("Unable to find branch with that id");
        }

        
        $sql = "SELECT t.id,t.branch_no, t.menu_item_name 
                   FROM (
                          SELECT t_request_category.id, 
                                 t_request_category.menu_item_name,
                                 t_request_category.branch_no 
                            FROM t_request_category  
                           WHERE t_request_category.parent_id IS NULL 
                              OR parent_id = 0) AS t
                  WHERE t.branch_no ={$branch_no}";
        
        
      

        
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
         
        $selectData = array();
         
        foreach ($result as $res) {
    
            $selectData[$res['id']] = $res['menu_item_name'];
        }
         
 
         
        return $selectData;
    }
    
     public function getMessage($_decision_no,$_user_no){
        $statement = $this->adapter->query("SELECT t_decision.decision_no,t_decision.remarks,t_decision.update_time as update_time, m_user.user_name FROM t_decision LEFT JOIN m_user ON t_decision.create_user = m_user.user_no WHERE t_decision.decision_no = {$_decision_no} AND t_decision.create_user = {$_user_no} "); 
        $result = $statement->execute();
        $remarks = $result->current();
        return $remarks;
     }
     
     public function getRemarks($_decision_no,$_user_no){
        $statement = $this->adapter->query("SELECT t_decision_approval.decision_no,t_decision_approval.remarks,t_decision_approval.last_update as update_time, m_user.user_name FROM t_decision_approval LEFT JOIN m_user ON t_decision_approval.user_no = m_user.user_no WHERE t_decision_approval.decision_no = {$_decision_no} AND t_decision_approval.user_no = {$_user_no}"); 
        $result = $statement->execute();
        $remarks = $result->current();
        return $remarks;
     }
 
}
