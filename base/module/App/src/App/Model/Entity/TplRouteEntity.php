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
use App\Model\Table\TplRouteTable;
use App\Model\Table\TplRouteUserTable;

class TplRouteEntity
{
    protected $db;

    /**
     * construct
     */
    public function __construct() {
        $this->db = new TplRouteTable();
    }

    /**
     * table object を返却
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

        $row = false;
        try {
            $this->db->exchanegArray($_data);
            $tpl_route_no = $this->db->insertRecord($_user_no);

            if ($tpl_route_no) {
                $r = new TplRouteUserTable($adapter);
                $row = $r->insertRecord($tpl_route_no, gv('user_no', $_data));
            }

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
     * @param boolean $_chg_user
     * @return boolean
     */
    public function updateRecord($_user_no, $_data, $_chg_user)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();
        $connection->beginTransaction();
        $this->db->exchanegArray($_data);

        try {
            $row = $this->db->updateRecord($_user_no);

            if ($row && $_chg_user) {
                $tpl_route_no = gv('tpl_route_no', $_data);

                $r = new TplRouteUserTable($adapter);
                $r->deleteRouteUser($tpl_route_no);
                $row = $r->insertRecord($tpl_route_no, gv('user_no', $_data));
            }

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
