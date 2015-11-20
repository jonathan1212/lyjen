<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class SearchPlugin extends AbstractPlugin
{

    const CURRENT_PAGE_KEY = 'p';
    const DEFAULT_MAX_ROW = 50;

    protected $currentPageKey = self::CURRENT_PAGE_KEY;
    protected $defaultMaxRow = self::DEFAULT_MAX_ROW;
    protected $query;

    /**
     * set key representing key
     * @param type $_key : key
     */
    public function setCurrentPageKey($_key = null)
    {
        if ($_key) {
            $this->currentPageKey = $_key;
        }
    }

    /**
     * setting max display default
     * @param type $_num : max display default
     */
    public function setDefaultMaxRow($_num)
    {
        if ($_num) {
            $this->defaultMaxRow = (int) $_num;
        }
    }

    /**
     * get parameter
     * @param string $_name : get key (if null get all)
     * @return
     */
    public function getQuery($_name = null)
    {
        if (!$this->query) {
            $this->query = $this->getController()->getRequest()->getQuery()->toArray();
        }

        if ($_name) {
            return gv($_name, $this->query);
        }
        else {
            return $this->query;
        }
    }

    /**
     * get beginning from "search-"
     * @return type
     */
    public function getSearchParam()
    {
        $data = $this->getQuery();
        if (!$data || !is_array($data)) {
            return array();
        }

        $ret = array();
        foreach ($data as $key => $val) {
            if (preg_match('/^(search-)/', $key)) {
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    /**
     * change beginning from "search XXX" to "XXX"
     * @param array $_data : array of get (if null get from 'getSearchParam')
     * @return array
     */
    public function getSearchParamConv($_data = array())
    {
        if (!$_data) {
            $_data = $this->getSearchParam();
        }
        if (!$_data || !is_array($_data)) {
            return array();
        }

        $ret = array();
        foreach ($_data as $key => $val) {
            $key = preg_replace('/^(search-)/', '', $key);
            if ($val !== '') {
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    /**
     * get ORDER
     * @return type
     */
    public function getOrder()
    {
        $ret = array();
        $ord = $this->getQuery('ord');
        if ($ord) {
            $ord = urldecode($ord);
            $ret = explode('=', $ord);
        }
        return $ret;
    }

    /**
     * get present page
     * @return int
     */
    public function getPage()
    {
        return gv($this->currentPageKey, $this->getQuery(), 1);
    }

    /**
     * get display number
     * @return int
     */
    public function getDisplayNum()
    {
        $cookie = (array) $this->getController()->getRequest()->getCookie();
        return gv('max_row', $cookie, $this->defaultMaxRow);
    }
}