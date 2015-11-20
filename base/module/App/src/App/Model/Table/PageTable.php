<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model\Table;
use Zend\Db\Sql\Predicate\Expression;

class PageTable extends TableModel
{
    protected $tableName = 'm_page';
    protected $primary = 'page_no';
    protected $priName = 'page_title';

    protected $page_no;             /* int(11) */
    protected $category_no;         /* int(11) */
    protected $controller_no;       /* int(11) */
    protected $page_title;          /* varchar(100) */
    protected $page_uri;            /* varchar(100) */
    protected $page_description;    /* varchar(100) */
    protected $icon;                /* varchar(50) */
    protected $order_no;            /* int(11) */
    protected $use_mobile;          /* tinyint(1) */
    protected $update_time;         /* timestamp */

    /**
     * construct
     * @param object $adapter
     */
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
        $this->page_no = "";
        $this->category_no = "";
        $this->controller_no = "";
        $this->page_title = "";
        $this->page_uri = "";
        $this->page_description = "";
        $this->icon = "";
        $this->order_no = "";
        $this->use_mobile = "";
        $this->update_time = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->page_no = (int) gv('page_no', $_data);
        $this->category_no = (int) gv('category_no', $_data);
        $this->controller_no = (int) gv('controller_no', $_data);
        $this->page_title = (string) gv('page_title', $_data);
        $this->page_uri = (string) gv('page_uri', $_data);
        $this->page_description = (string) gv('page_description', $_data);
        $this->icon = (string) gv('icon', $_data);
        $this->order_no = (int) gv('order_no', $_data);
        $this->use_mobile = (int) gv('use_mobile', $_data);
        $this->update_time = (string) gv('update_time', $_data);
    }

    public function getMenuList($_user_no, $_mobile = 0)
    {
        if (!$_user_no) {
            return false;
        }

        $sql = "SELECT DISTINCT"
                . " m1.page_no, m1.page_title, m1.page_uri, m1.page_description,"
                . " m1.icon, m2.category_no, m2.category_name, m2.cate_description"
                . " FROM"
                . " m_page AS m1"
                . " INNER JOIN m_page_category AS m2 ON m1.category_no = m2.category_no"
                . " INNER JOIN m_controller AS m3 ON m1.controller_no = m3.controller_no"
                . " LEFT JOIN r_controller_role AS r1 ON m3.controller_no = r1.controller_no"
                . " LEFT JOIN r_user_role AS r2 ON r1.role_no = r2.role_no"
                . " INNER JOIN m_role AS m4 ON m4.role_no = r2.role_no"
                . " INNER JOIN m_user AS m5 ON m5.user_no = r2.user_no"
                . " WHERE"
                . " m1.deleted = 0"
                . " AND m2.deleted = 0"
                . " AND m3.deleted = 0"
                . " AND m4.deleted = 0"
                . " AND m5.deleted = 0"
                . " AND m5.valid = 1"
                . " AND r1.level > 0"
                . ($_mobile ? " AND m1.use_mobile = 1" : "")
                . " AND m5.user_no = ?"
                . " ORDER BY"
                . " m2.order_no ASC, m1.order_no ASC";
        return $this->adapter->query($sql, array($_user_no))->toArray();
    }

    public function getAllMenu($_mobile = 0)
    {
        $sql = "SELECT DISTINCT"
                . " m1.page_no, m1.page_title, m1.page_uri, m1.page_description,"
                . " m1.icon, m2.category_no, m2.category_name, m2.cate_description"
                . " FROM"
                . " m_page AS m1"
                . " INNER JOIN m_page_category AS m2 ON m1.category_no = m2.category_no"
                . " WHERE"
                . " m1.deleted = 0"
                . " AND m2.deleted = 0"
                . ($_mobile ? " AND m1.use_mobile = 1" : "")
                . " ORDER BY"
                . " m2.order_no ASC, m1.order_no ASC";
        return $this->adapter->query($sql, 'execute')->toArray();
    }

    /**
     * show detail
     * @param int $_page_no
     * @return object|boolean
     */
    public function getDetail($_page_no)
    {
        if (!$_page_no) {
            return false;
        }

        $sql = "SELECT"
                . " m1.page_no, m1.category_no, m1.controller_no, m1.page_title,"
                . " m1.page_uri, m1.page_description, m1.icon, m1.deleted,"
                . " CASE WHEN m1.use_mobile = 1 THEN 'Yes'"
                . " ELSE 'No' END AS use_mobile,"
                . " m2.category_name, m3.controller_name"
                . " FROM " . $this->tableName . " AS m1"
                . " INNER JOIN m_page_category AS m2 ON m1.category_no = m2.category_no"
                . " INNER JOIN m_controller AS m3 ON m1.controller_no = m3.controller_no"
                . " WHERE"
                . " m1.page_no = ?"
            ;
        return $this->adapter->query($sql, array($_page_no))->current();
    }

    /**
     * for show list
     * @param array $_where
     * @param array $_order
     * @param int $_page
     * @param int $_num
     * @return \Zend\Paginator\Paginator
     */
    public function getPageList($_where = array(), $_order = array(), $_page = 1, $_num = 25)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
            'page_no',
            'category_no',
            'controller_no',
            'page_title',
            'page_uri',
            'page_description',
            'icon',
            'order_no',
            'use_mobile',
            'deleted',
            'create_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.create_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
            'update_time' => new Expression(
                    'DATE_ADD(' . $this->tableName . '.update_time, INTERVAL ' . TIME_DIFF . ' SECOND)'),
        ));

        $select->join(array(
            'm1a' => 'm_user'),
            'm1a.user_no = ' . $this->tableName . '.create_user',
            array(
                'create_user' => 'user_name',
            ),
            'left'
        );
        $select->join(array(
            'm1b' => 'm_user'),
            'm1b.user_no = ' . $this->tableName . '.update_user',
            array(
                'update_user' => 'user_name',
            ),
            'left'
        );

        $select->join(array(
            'm2' => 'm_page_category'),
            'm2.category_no = ' . $this->tableName . '.category_no',
            array(
                'category_name',
            ),
            'left'
        );
        $select->join(array(
            'm3' => 'm_controller'),
            'm3.controller_no = ' . $this->tableName . '.controller_no',
            array(
                'controller_name',
            ),
            'left'
        );

        // where
        $select->where($this->tableName . "." . $this->primary . " <> 0");
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                    case 'category_no':
                    case 'deleted':
                        $select->where(array($this->tableName . ".{$key}" => $val));
                        break;
                    case 'page_title':
                    case 'page_uri':
                    case 'page_description':
                        $select->where($this->tableName . ".{$key}"
                                . " like " . $this->adapter->platform->quoteValue( '%' . $val . '%'));
                        break;
                    default:
                        break;
                }
            }
        }

        // order
        if ($_order) {
            list($key, $val) = $_order;
            if (($val == 'asc' || $val == 'desc') && (
                $key == 'page_no'|| $key == 'category_no'
                    || $key == 'controller_no'|| $key == 'page_title'
                    || $key == 'page_uri'|| $key == 'page_description'
                    || $key == 'icon'|| $key == 'order_no'
                    || $key == 'use_mobile'|| $key == 'create_user'
                    || $key == 'create_time'|| $key == 'update_user'
                    || $key == 'update_time'
            )) {
                $select->order(array(
                    $this->tableName . ".{$key}" => $val
                ));
            }
        }

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        $paginator->setCurrentPageNumber((int) $_page);
        $paginator->setItemCountPerPage((int) $_num);

        return $paginator;
    }

    /**
     * insert into m_page
     * @param int $_user_no
     * @return int|boolean
     */
    public function insertRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        // get max value and add 1
        $maxId = $this->getMaxId() + 1;

        $values = array(
            'page_no' => $maxId,
            'category_no' => $this->category_no,
            'controller_no' => $this->controller_no,
            'page_title' => $this->page_title,
            'page_uri' => $this->page_uri,
            'page_description' => $this->page_description,
            'icon' => $this->icon,
            'order_no' => $maxId,
            'use_mobile' => $this->use_mobile,
            'create_user' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        return $this->insert($values);
    }

    /**
     * update m_page
     * @param int $_user_no
     * @return int|boolean
     */
    public function updateRecord($_user_no)
    {
        if (!$_user_no) {
            return false;
        }

        $primary = $this->primary;
        $primaryNo =  $this->$primary;

        $values = array(
            'page_no' => $this->page_no,
            'category_no' => $this->category_no,
            'controller_no' => $this->controller_no,
            'page_title' => $this->page_title,
            'page_uri' => $this->page_uri,
            'page_description' => $this->page_description,
            'icon' => $this->icon,
            'order_no' => $this->order_no,
            'use_mobile' => $this->use_mobile,
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        $where = array(
            $this->primary => $primaryNo,
            'update_time' => $this->update_time,
        );

        return $this->update($values, $where);
    }
}