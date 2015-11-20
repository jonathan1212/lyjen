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

class LanguageTable extends TableModel
{
    protected $tableName = 'm_language';
    protected $primary = 'lang_no';
    protected $priName = 'lang_name';

    protected $lang_no;        /* smallint(6) */
    protected $lang_name;      /* varchar(50) */
    protected $language;       /* varchar(50) */
    protected $lang_id;        /* varchar(5) */
    protected $resource_id;    /* varchar(5) */
    protected $update_time;    /* timestamp */

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
        $this->lang_no = "";
        $this->lang_name = "";
        $this->language = "";
        $this->lang_id = "";
        $this->resource_id = "";
        $this->update_time = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->lang_no = (int) gv('lang_no', $_data);
        $this->lang_name = (string) gv('lang_name', $_data);
        $this->language = (string) gv('language', $_data);
        $this->lang_id = (string) gv('lang_id', $_data);
        $this->resource_id = (string) gv('resource_id', $_data);
        $this->update_time = (string) gv('update_time', $_data);
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
            'lang_no',
            'lang_name',
            'language',
            'lang_id',
            'resource_id',
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

        // where
        $select->where($this->tableName . "." . $this->primary . " <> 0");
        if ($_where) {
            foreach ($_where as $key => $val) {
                switch ($key) {
                    case 'deleted':
                        $select->where(array($this->tableName . ".{$key}" => $val));
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
                $key == 'lang_no'|| $key == 'lang_name'
                    || $key == 'language'|| $key == 'lang_id'
                    || $key == 'resource_id'|| $key == 'create_user'
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
     * insert into m_language
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
            'lang_no' => $maxId,
            'lang_name' => $this->lang_name,
            'language' => $this->language,
            'lang_id' => $this->lang_id,
            'resource_id' => $this->resource_id,
            'create_user' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'update_user' => (int) $_user_no,
            'update_time' => new Expression('UTC_TIMESTAMP'),
        );

        return $this->insert($values);
    }

    /**
     * update m_language
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
            'lang_no' => $this->lang_no,
            'lang_name' => $this->lang_name,
            'language' => $this->language,
            'lang_id' => $this->lang_id,
            'resource_id' => $this->resource_id,
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
