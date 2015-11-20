<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model\Entity;

use App\Model\Table\RestrictLoginTable;

class RestrictLoginEntity
{
    protected $db;

    /**
     * construct
     */
    public function __construct() {
        $this->db = new RestrictLoginTable();
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
     * check session existence
     * @param int $_user_no
     * @return boolean
     */
    public function sessionCheck($_user_no)
    {
        $row = $this->db()->getFetchOne($_user_no);
        if (!$row || $row->session_id != session_id()) {
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * for avoid duplication login
     * @param int $_user_no
     * @return string|boolean
     */
    public function restrictCheck($_user_no)
    {
        $row = $this->db->getFetchOne($_user_no);
        if (!$row) {
            return $this->db->insertRecord($_user_no);
        }
        else if ($row->session_id == session_id()) {
            return true;
        }
        else if ($row->user_no == $_user_no) {
            return 'error';
        }
        else {
            return false;
        }
    }
}
