<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model\Entity;

use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use App\Model\Table\BranchTable;

class BranchEntity
{
    protected $db;

    /**
     * construct
     */
    public function  __construct() {
        $this->db = new BranchTable();
    }

    /**
     * get table object
     * @return object
     */
    public function db()
    {
        return $this->db;
    }

    /**
     * create
     * @param int $_user_no
     * @param array $_data
     * @return boolean
     */
    public function insertRecord($_user_no, $_data)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();
        $connection->beginTransaction();

        try {
            $this->db->exchanegArray($_data);
            $row = $this->db->insertRecord($_user_no);

            if ($row) {
                $connection->commit();
                return true;
            }
        }
        catch (\Exception $e) {
            $connection->rollback();
            if (IS_TEST) {
                echo $e->getMessage();
            }
            return false;
        }
    }

    /**
     * update
     * @param int $_user_no
     * @param array $_data
     * @return boolean
     */
    public function updateRecord($_user_no, $_data)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();
        $connection->beginTransaction();
        $this->db->exchanegArray($_data);

        try {
            $row = $this->db->updateRecord($_user_no);

            if ($row) {
                $connection->commit();
                return true;
            }
        }
        catch (\Exception $e) {
            $connection->rollback();
            if (IS_TEST) {
                echo $e->getMessage();
            }
            return false;
        }
    }
}
