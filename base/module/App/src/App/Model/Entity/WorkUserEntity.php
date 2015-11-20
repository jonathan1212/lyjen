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
use App\Model\Table\WorkUserTable;

class WorkUserEntity
{
    protected $db;

    /**
     * construct
     */
    public function __construct() {
        $this->db = new WorkUserTable();
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
     * get data for bundle
     * @param array $_tmp_user_no
     * @param int $_user_no
     * @return array
     */
    public function getSearch($_tmp_user_no, $_user_no)
    {
        if (!$_tmp_user_no || !$_user_no) {
            return false;
        }

        $where = array(
            'create_user' => (int) $_user_no,
            'finish_time IS NULL',
            'tmp_user_no IN(' . implode(',', $_tmp_user_no) . ')',
        );
        $order = array(
            'user_name' => 'ASC'
        );

        return $this->db->search($where, $order, null, null);
    }

    /**
     * create
     * @param array $_user_info
     * @param array $_data
     * @return boolean
     */
    public function insertWorkRecord($_user_info, $_data)
    {
        if (!$_user_info || !$_data) {
            return false;
        }

        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();
        $connection->beginTransaction();

        $work_no = $this->db->getMaxId('work_no') + 1;

        $row = false;
        do {
            $data = array(
                'branch_no' => gv('branch_no', $_user_info),
                'login_pw' => make_rand_str(8, 3),
                'join_key' => sha1(time() . microtime() . rand(0, 1000)),
                'work_no' => $work_no,
            );

            try {
                $this->db->exchanegArray(array_merge(current($_data), $data));
                $row = $this->db->insertRecord(gv('user_no', $_user_info));
            }
            catch (\Exception $e) {
                if (IS_TEST) {
                    $connection->rollback();
                    echo $e->getMessage();
                }
                break;
            }
        } while (next($_data));

        if ($row) {
            $this->db->checkLoginId($work_no);
            $connection->commit();
        }

        return $row;
    }

    /**
     * update
     * @param int $_user_no
     * @param array $_data
     * @param int $_work_no
     * @param boolean $_status
     * @return boolean
     */
    public function updateRecord($_user_no, $_data, $_work_no, $_status = false)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();
        $connection->beginTransaction();
        $this->db->exchanegArray($_data);

        try {
            $row = $this->db->updateRecord($_user_no, $_work_no, $_status);

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
     * update section and position (bundle)
     * @param int $_user_no
     * @param array $_data
     * @param int $_work_no
     * @return boolean
     */
    public function updateBatch($_user_no, $_data, $_work_no)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();
        $connection->beginTransaction();

        $tmp_user_no = gv('tmp_user_no', $_data);
        $this->db->exchanegArray(array(
            'section_no' => gv('section_no', $_data),
            'position_no' => gv('position_no', $_data),
        ));

        try {
            $row = $this->db->updateBatch($_user_no, $tmp_user_no, $_work_no);

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
     * copy work data to production table
     * @param int $_user_no
     * @param int $_work_no
     * @return boolean
     */
    public function copyToFormal($_user_no, $_work_no)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $connection = $adapter->getDriver()->getConnection();
        $connection->beginTransaction();
        $w = false;
        $m = false;
        $r1 = false;
        $r2 = false;

        try {
            // status change to 2 for duplicate login
            $this->db->checkLoginId($_work_no);

            // copy to master
            $m = $this->db->copyToUserMaster($_user_no, $_work_no);
            if ($m) {
                // copy login ID
                $r1 = $this->db->copyToLoginId($_work_no);
            }
            if ($r1) {
                // copy login password
                $r2 = $this->db->copyToLoginPw($_work_no);
            }
            if ($r2) {
                // insert into work table
                $w = $this->db->updateStatus($_work_no);
            }

            if ($m && $r1 && $r2 && $w) {
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
