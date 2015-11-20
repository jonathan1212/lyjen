<?php

namespace Application\Model\Table;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class BranchTable extends TableModel
{
    protected $tableName = 'm_branch';
    protected $primary = 'branch_id';
    protected $priName = 'branch_name';

    protected $branch_id;
    protected $branch_name;
    protected $branch_abbr;
    protected $update_time;

    public function __construct($adapter = null)
    {
        $this->reset();
        parent::__construct($adapter);
    }

    public function reset()
    {
        $this->branch_id = "";
        $this->branch_name = "";
        $this->branch_abbr = "";
        $this->update_time = "";
    }

    public function exchangeArray($_data)
    {
        $this->branch_id     = (isset($_data['branch_id']))     ? $_data['branch_id']     : null;
        $this->branch_name  = (isset($_data['branch_name']))     ? $_data['branch_name']     : null;
        $this->branch_abbr  = (isset($_data['branch_abbr']))     ? $_data['branch_abbr']     : null;
        $this->update_time  = (isset($_data['update_time']))     ? $_data['update_time']     : null;
    }
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    public function insertRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $values = array(
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch_name,
            'branch_abbr' => $this->branch_abbr,
            'create_user_id' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user_id' => (int) $_user_no,
        );

        return $this->insert($values);
    }
    public function getPageList($_where = array(), $_order = array(), $_page = 1, $_num = 10)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
        'branch_id',
        'branch_name',
        'branch_abbr',
        'is_deleted',
        'update_time',
        ));

        $select->join('m_user', 'm_branch.update_user_id = m_user.user_id', array('first_name','last_name'));

        $select->order('update_time DESC');
        $adapter = new DbSelect($select, $sql);
        $paginator = new Paginator($adapter);

        return $paginator;
    }
    
    public function getRecord($branch_id){
        return $this->select(array('branch_id'=>$branch_id))->current();
    }
    
     public function updateRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $primary = $this->primary;
        $primaryNo =  $this->$primary;

        $values = array(
           'branch_id' => $this->branch_id,
            'branch_name' => $this->branch_name,
            'branch_abbr' => $this->branch_abbr,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user_id' => (int) $_user_no,
        );

        $where = array(
            $this->primary => $primaryNo
        );

        return $this->update($values, $where);
    }
}
