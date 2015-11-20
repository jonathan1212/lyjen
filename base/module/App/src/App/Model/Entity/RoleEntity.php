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
use App\Model\Table\RoleTable;
use App\Model\Table\UserRoleTable;
use App\Model\Table\ControllerRoleTable;

class RoleEntity
{
    protected $db;

    /**
     * construct
     */
    public function __construct() {
        $this->db = new RoleTable();
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

        $r1 = new UserRoleTable($adapter);
        $r2 = new ControllerRoleTable($adapter);

        $row = false;
        try {
            $this->db->exchanegArray($_data);

            $role_no = $this->db->insertRecord($_user_no);

            if ($role_no) {
                $row = $r1->insertRecord($role_no, gv('user_no', $_data));
            }

            if ($row) {
                $row = $r2->insertRecord($role_no, gv('level', $_data));
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
     * @param array $_chg
     * @return boolean
     */
    public function updateRecord($_user_no, $_data, $_chg)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();
        $connection->beginTransaction();
        $this->db->exchanegArray($_data);

        $role_no = gv('role_no', $_data);
        $chg_user = gv('chg_user', $_chg);
        $chg_lv = gv('chg_lv', $_chg);

        try {
            $row = $this->db->updateRecord($_user_no);

            if ($row && $chg_user) {
                $r1 = new UserRoleTable($adapter);
                $r1->deleteRole($role_no);
                $row = $r1->insertRecord($role_no, gv('user_no', $_data));
            }

            if ($row && $chg_lv) {
                $r2 = new ControllerRoleTable($adapter);
                $r2->deleteRole($role_no);
                $row = $r2->insertRecord($role_no, gv('level', $_data));
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
