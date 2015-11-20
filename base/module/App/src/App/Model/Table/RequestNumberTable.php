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

class RequestNumberTable extends TableModel
{
    protected $tableName = 't_request_number';
    protected $primary = 'sequence_no';
    protected $priName = 'sequence_no';
    protected $sequence_no; 
    protected $branch_no;
    protected $status;     
    protected $created;
    

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
		$this->sequence_no = ""; 
		$this->status = "";   
		$this->created = "";
		$this->branch_no = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->sequence_no = (int) gv('sequence_no', $_data);
        $this->status = (int) gv('status', $_data);
        $this->branch_no = (int) gv('branch_no', $_data);
        $this->created = (int) gv('created', $_data);
    }


    /**
     * insert into m_branch
     * @param int $_user_no
     * @return int|boolean
     */
    public function insertRecord()
    {
        $values = array(
                'status' => $this->status,
                'branch_no' => $this->branch_no,
                'created' => new Expression('UTC_TIMESTAMP')
        );

        return $this->insert($values);
    }
    
    public function generateNumber($branch_no){
        $this->status = 1;
        $this->branch_no = $branch_no;
        
        $number = $this->insertRecord();
        return $this->getLastInsertValue();
    }
    
    public function getRecord($id){
        $handle = $this->select(array('sequence_no'=>$id));
        return $handle->current();
    }


  
    
}
