<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model\Table;

class ControllerRoleTable extends TableModel
{
    protected $tableName = 'r_controller_role';
    protected $primary = '';
    protected $priName = '';

    public function __construct($adapter = null)
    {
        parent::__construct($adapter);
    }

    /**
     * insert into r_controller_role
     * @param int $_role_no
     * @param array $_ctrl
     * @return int|boolean
     */
    public function insertRecord($_role_no, $_ctrl)
    {
        if (!$_role_no || !$_ctrl || !is_array($_ctrl)) {
            return false;
        }

        $ret = false;
        list($ctrl, $level) = each($_ctrl);
        do {
            $ret = $this->insert(array(
                'controller_no' => (int) $ctrl,
                'role_no' => (int) $_role_no,
                'level' => (int) $level,
            ));
            if (!$ret) {
                break;
            }
        } while(list($ctrl, $level) = each($_ctrl));

        return $ret;
    }

    /**
     * delete r_controller_role
     * @param int $_role_no
     * @return int|boolean
     */
    public function deleteRole($_role_no)
    {
        if (!$_role_no) {
            return false;
        }
        $where = array(
            'role_no' => (int) $_role_no,
        );
        return $this->delete($where);
    }
}