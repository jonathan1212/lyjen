<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model\Table;

class TplRouteUserTable extends TableModel
{
    protected $tableName = 'r_tpl_route_user';
    protected $primary = '';
    protected $priName = '';

    public function __construct($adapter = null)
    {
        parent::__construct($adapter);
    }

    /**
     * insert into r_tpl_route_user
     * @param int $_tpl_route_no
     * @param int|array $_users
     * @return int|boolean
     */
    public function insertRecord($_tpl_route_no, $_users)
    {
        if (!$_users || !$_tpl_route_no) {
            return false;
        }

        $ret = false;
        if (is_array($_users)) {
            $user_no = current($_users);
            do {
                next($_users);
                $ret = $this->insert(array(
                    'tpl_route_no' => (int) $_tpl_route_no,
                    'user_no' => (int) $user_no,
                ));
                if (!$ret) {
                    break;
                }
            } while($user_no = current($_users));
        }
        else {
            $ret = $this->insert(array(
                'tpl_route_no' => (int) $_tpl_route_no,
                'user_no' => (int) $_users,
            ));
        }
        return $ret;
    }

    /**
     * physical delete r_tpl_route_user by role_no
     * @param int $_tpl_route_no
     * @return int|boolean
     */
    public function deleteRouteUser($_tpl_route_no)
    {
        if (!$_tpl_route_no) {
            return false;
        }
        $where = array(
            'tpl_route_no' => (int) $_tpl_route_no,
        );
        return $this->delete($where);
    }
}
