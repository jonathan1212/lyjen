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
use App\Model\Table\UserTable;

class UserEntity
{
    protected $db;

    /**
     * construct
     */
    public function __construct() {
        $this->db = new UserTable();
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
    public function insertUser($_user_no, $_data)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();

        $connection->beginTransaction();
        try {
            $this->db->exchanegArray($_data);
            $row = $this->db->insertRecord($_user_no);

            if ($row) {
                $row = $this->db->insertLoginId($row);
            }
            if ($row) {
                $new_pw = make_rand_str(8, 3);
                $row = $this->db->insertLoginPw($row, $new_pw, 1, 0);
            }

            if ($row) {
                $connection->commit();
                return $new_pw;
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
    public function updateUser($_user_no, $_data)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();

        $connection->beginTransaction();
        try {
            $this->db->exchanegArray($_data);
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

    /**
     * update password
     * @param int $_user_no
     * @param string $_new_pw
     * @param boolean $_initial
     * @return boolean
     */
    public function changePw($_user_no, $_new_pw, $_initial)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();

        $connection->beginTransaction();
        try {
            $row = $this->db->insertLoginPw($_user_no, $_new_pw, $_initial, 1);
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