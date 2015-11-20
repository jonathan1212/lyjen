<?php

namespace Application\Model\Table;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Adapter\Driver\DriverInterface;
/**
 * MBranchModel
 *
 * Model methods below.
 */

class MarketTable extends TableModel
{
    protected $tableName = 'm_market';
    protected $primary = 'market_id';
    protected $priName = 'market_name';

    protected $market_id; 
    protected $market_name;
    protected $market_abbr;
    protected $jurisdiction;
    protected $criterion_id;
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
        $this->market_id = "";
        $this->market_name = "";
        $this->market_abbr = "";
        $this->jurisdiction = "";
        $this->criterion_id = "";
        $this->update_time = "";
    }

    public function exchangeArray($_data)
    {
        $this->market_id     = (isset($_data['market_id']))     ? $_data['market_id']     : null;
        $this->market_name   = (isset($_data['market_name']))   ? $_data['market_name']   : null;
        $this->market_abbr   = (isset($_data['market_abbr']))   ? $_data['market_abbr']   : null;
        $this->jurisdiction   = (isset($_data['jurisdiction']))   ? $_data['jurisdiction']   : null;
        $this->criterion_id   = (isset($_data['criterion_id']))   ? $_data['criterion_id']   : null;
        $this->update_time     = (isset($_data['update_time']))     ? $_data['update_time']     : null;
    }

    public function insertRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $values = array(
            'market_id' => $this->market_id,
            'market_name' => $this->market_name,
            'market_abbr' => $this->market_abbr,
            'criterion_jurisdiction_id' =>  $this->criterion_id,
            'create_user_id' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user_id' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );
       
        $market =  $this->insert($values);
        $_market_id = $this->adapter->getDriver()->getLastGeneratedValue();
        var_dump($_market_id);
        var_dump($this->jurisdiction);

       foreach($this->jurisdiction as $row=>$jurisdiction)
        {
        $values  = implode(',',$jurisdiction);
        var_dump($values);
        $statement = $this->adapter->query("INSERT INTO r_market_jurisdiction VALUES ({$_market_id},{$values}, '')")->execute();
        }
        return $market;
        
        }
    public function getPageList($_where = array(), $_order = array(), $_page = 1, $_num = 25)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
        'market_id',
        'market_name',
        'market_abbr',
       // 'criterion_jurisdiction_id',
        'is_deleted',
        'update_time',   
        ));
        $select->join('m_user', 'm_market.update_user_id = m_user.user_id', array('first_name','last_name'));

        $select->join( array('j1' => 'm_jurisdiction'),
                        'm_market.criterion_jurisdiction_id = j1.jurisdiction_id',
                        array('criterion_jurisdiction_id'=>'jurisdiction_id',
                               'jurisdiction_name','jurisdiction_abbr'
                            ),
                        'left'
                        );
        
        $select->join( 'r_market_jurisdiction',
                       'm_market.market_id = r_market_jurisdiction.market_id',
                       array('jurisdiction_id'=>'jurisdiction_id',
                           ),
                       'left'
                       );
       // $select->join( array('j2' => 'm_jurisdiction'),
       //                 'r_market_jurisdiction.jurisdiction_id = j2.jurisdiction_id ',
       //                 array('id' => new \Zend\Db\Sql\Expression('GROUP_CONCAT( m2.jurisdiction_abbr SEPARATOR '/') AS jurisdiction_abbr')),
       //                 'left'
       //                 );
    /**   SELECT m_market.market_id, m_market.market_name, m_market.market_abbr, 
     * m_market.criterion_jurisdiction_id AS criterion, 
     * m1.jurisdiction_abbr AS criterion_abbr, 
     * r_market_jurisdiction.jurisdiction_id AS jurisdiction, 
     * GROUP_CONCAT( m2.jurisdiction_abbr SEPARATOR '/') AS jurisdiction_abbr, 
     * CONCAT( m_user.first_name, " ", m_user.last_name ) AS username FROM m_market 
     * 
     * xLEFT JOIN m_jurisdiction as m1 ON m_market.criterion_jurisdiction_id = m1.jurisdiction_id 
     * xLEFT JOIN r_market_jurisdiction ON m_market.market_id = r_market_jurisdiction.market_id 
     * xLEFT JOIN m_jurisdiction as m2 ON r_market_jurisdiction.jurisdiction_id = m2.jurisdiction_id 
     * xLEFT JOIN m_user ON m_market.update_user_id = m_user.user_id 
     * GROUP BY m_market.market_id
    **/
// order
       $select->group('market_id');
        $adapter = new DbSelect($select, $sql);
        $paginator = new Paginator($adapter);


        return $paginator;
    }
    public function getRecord($jurisdiction_id){
        return $this->select(array('market_id'=>$market_id))->current();
    }
    
     public function updateRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $primary = $this->primary;
        $primaryNo =  $this->$primary;

        $values = array(
            'market_name' => $this->jurisdiction_name,
            'market_abbr' => $this->jurisdiction_abbr,
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
}
