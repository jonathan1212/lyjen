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

class TemporaryIdTable extends TableModel
{
    protected $tableName = 't_temporary_id';
    protected $primary = 'tmp_no';
    protected $priName = '';

    protected $tmp_no;        /* int(11) */
    protected $tmp_id;        /* varchar(128) */
    protected $life_time;     /* int(11) */

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
        $this->tmp_no = "";
        $this->tmp_id = "";
        $this->life_time = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->tmp_no = (int) gv('tmp_no', $_data);
        $this->tmp_id = (string) gv('tmp_id', $_data);
        $this->life_time = (int) gv('life_time', $_data);
    }

    /**
     * insert into t_temporary_id
     * @param int $_user_no
     * @return int|boolean
     */
    public function insertRecord($_user_no)
    {
        if ($_user_no === null) {
            return false;
        }

        $values = array(
            'tmp_id' => $this->tmp_id,
            'create_user' => (int) $_user_no,
            'create_time' => new Expression('UTC_TIMESTAMP'),
            'life_time' => $this->life_time,
            'finish_time' => null,
        );

        return $this->insert($values);
    }

    /**
     * update t_temporary_id
     * @param string $_tmp_id
     * @return int|boolean
     */
    public function finishRecord($_tmp_id)
    {
        if (!$_tmp_id) {
            return false;
        }

        return $this->update(
                array(
                    'finish_time' => new Expression('UTC_TIMESTAMP'),
                ),
                array(
                    'tmp_id' => $_tmp_id,
                )
            );
    }

    public function getFetchOne($_temp_id)
    {
        if (!$_temp_id) {
            return false;
        }

        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(
            'tmp_no', 'tmp_id', 'create_user',
            'create_time', 'life_time', 'finish_time'
        ));
        $select->join(array(
            'm1' => 'm_user'),
            'm1.user_no = ' . $this->tableName . '.create_user',
            array(
                'email' => 'email',
            ),
            'inner'
        );
        $select->where(array(
            $this->tableName . ".tmp_id" => (string) $_temp_id,
            $this->tableName . ".finish_time IS NULL",
            "UTC_TIMESTAMP < DATE_ADD("
                . $this->tableName . ".create_time, INTERVAL life_time SECOND)"
        ));

        return $this->selectWith($select)->current();
    }
}
