<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model\Table;

class UserRoleTable extends TableModel
{
    protected $tableName = 'r_user_role';
    protected $primary = '';
    protected $priName = '';

    public function __construct($adapter = null)
    {
        parent::__construct($adapter);
    }

    /**
     * insert into r_user_role
     * @param int $_role_no
     * @param int|array $_users
     * @return int|boolean
     */
    public function insertRecord($_role_no, $_users)
    {
        if (!$_users || !$_role_no) {
            return false;
        }

        $ret = false;
        if (is_array($_users)) {
            $user_no = current($_users);
            do {
                next($_users);
                $ret = $this->insert(array(
                    'user_no' => (int) $user_no,
                    'role_no' => (int) $_role_no,
                ));
                if (!$ret) {
                    break;
                }
            } while($user_no = current($_users));
        }
        else {
            $ret = $this->insert(array(
                'user_no' => (int) $_users,
                'role_no' => (int) $_role_no,
            ));
        }

        return $ret;
    }

    /**
     * physical delete r_user_role by role_no
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

    /**
     * physical delete r_user_role by user_no and role_no
     * @param int $_user_no
     * @param int $_role_no
     * @return int|boolean
     */
    public function deleteUserRole($_user_no, $_role_no)
    {
        if (!$_user_no || !$_role_no) {
            return false;
        }
        $where = array(
            'user_no' => (int) $_user_no,
            'role_no' => (int) $_role_no,
        );
        return $this->delete($where);
    }
}