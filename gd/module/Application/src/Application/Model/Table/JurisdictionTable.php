<?php

namespace Application\Model\Table;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class JurisdictionTable extends TableModel
{
    protected $tableName = 'm_jurisdiction';
    protected $primary = 'jurisdiction_id';
    protected $priName = 'jurisdiction_name';

    protected $jurisdiction_id;
    protected $jurisdiction_name;
    protected $jurisdiction_abbr;
    protected $is_regulator;
    protected $update_time;

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
        $this->jurisdiction_id = "";
        $this->jurisdiction_name = "";
        $this->jurisdiction_abbr = "";
        $this->is_regulator = 0;
        $this->update_time = "";
    }

    public function exchangeArray($_data)
    {
        $this->jurisdiction_id     = (isset($_data['jurisdiction_id']))     ? $_data['jurisdiction_id']     : null;
        $this->jurisdiction_name   = (isset($_data['jurisdiction_name']))   ? $_data['jurisdiction_name']   : null;
        $this->jurisdiction_abbr   = (isset($_data['jurisdiction_abbr']))   ? $_data['jurisdiction_abbr']   : null;
        $this->is_regulator   = (isset($_data['is_regulator']))   ? $_data['is_regulator']   : null;
        $this->update_time     = (isset($_data['update_time']))     ? $_data['update_time']     : null;
    }

    public function insertRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $values = array(
            'jurisdiction_id' => $this->jurisdiction_id,
            'jurisdiction_name' => $this->jurisdiction_name,
            'jurisdiction_abbr' => $this->jurisdiction_abbr,
            'is_regulator' =>  $this->is_regulator,
            'create_user_id' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user_id' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        return $this->insert($values);
    }
    public function getPageList($_where = array(), $_order = array(), $_page = 1, $_num = 25)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
        'jurisdiction_id',
        'jurisdiction_name',
        'jurisdiction_abbr',
        'is_regulator',
        'is_deleted',
        'update_time',   
        ));
        $select->join('m_user', 'm_jurisdiction.update_user_id = m_user.user_id', array('first_name','last_name'));
        $select->order('update_time DESC');
        
        $adapter = new DbSelect($select, $sql);
        $paginator = new Paginator($adapter);

        return $paginator;
    }
    public function getRecord($jurisdiction_id){
        $value = $this->select(array('jurisdiction_id'=>$jurisdiction_id, 'jurisdiction_abbr'))->current();
        return $value['jurisdiction_abbr'];
    }
    
     public function updateRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $primary = $this->primary;
        $primaryNo =  $this->$primary;

        $values = array(
            'jurisdiction_name' => $this->jurisdiction_name,
            'jurisdiction_abbr' => $this->jurisdiction_abbr,
            'is_regulator' =>  $this->is_regulator,
            'update_user_id' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        $where = array(
            $this->primary => $primaryNo,
            'update_time' => $this->update_time,
        );

        return $this->update($values, $where);
    }
    
    public function getJuris(){
         $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
        'jurisdiction_id',
        'jurisdiction_name',
        'jurisdiction_abbr'))->current();
    }
}
