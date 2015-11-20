<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Http\PhpEnvironment\Request as HttpRequest;

class PageCtrl extends AbstractHelper
{
    const PAGE_KEY = 'p';
    const OREDER_IMG = '/js/jq/ui/images/ui-icons_ffffff_256x240.png';

    protected $path;
    protected $request;
    protected $query;
    protected $pageKey = self::PAGE_KEY;
    protected $displayRowItems = array(25, 50, 100, 250, 500);
    protected $orderImage = self::OREDER_IMG;

    /**
     * set key for page
     * @param type $_key
     */
    public function setCurrentPageKey($_key = null)
    {
        if ($_key) {
            $this->pageKey = $_key;
        }
    }

    /**
     * set display number
     * @param array $_data
     */
    public function setDisplayRowItems($_data = array())
    {
        if ($_data) {
            $this->displayRowItems = $_data;
        }
    }

    /**
     * set image file for sorting
     * @param string $_img
     */
    public function setOrderImage($_img)
    {
        if ($_img) {
            $this->orderImage = $_img;
        }
    }

    /**
     * Get request object
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new HttpRequest();
        }
        return $this->request;
    }

    /**
     * Get request > Get query object
     * @return query
     */
    public function getQuery($_name = null)
    {
        if (!$this->query) {
            $this->query = $this->getRequest()->getQuery()->toArray();
        }

        if ($_name) {
            return gv($_name, $this->query);
        }
        else {
            return $this->query;
        }
    }

    /**
     * get accessing URI
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            $uri = $this->getRequest()->getUriString();
            $path = gv('path', parse_url($uri));
            $this->path = BASE_URL . $path;
        }
        return $this->path;
    }

    /**
     * get string for query
     * @param array $_data
     * @param boolean $_p
     * @return string
     */
    public function getQueryStr($_data = array(), $_p = 0)
    {
        $q = $this->getQuery();
        if ($_data) {
            $q = array_merge($q, $_data);
        }

        // if page key exist delete
        if ($_p && isset($q[$this->pageKey])) {
            unset($q[$this->pageKey]);
        }

        return http_build_query($q, '', '&amp;');
    }

    /**
     * get tag for sorting
     * @param string $_name
     * @return string|boolean
     */
    public function getOrderLink($_name)
    {
        if (!$_name) {
            return false;
        }

        $val = 'asc';
        $ord = $this->getQuery('ord');
        if ($ord) {
            $ord = urldecode($ord);
            list($key, $val) = explode('=', $ord);
            if ($key == $_name) {
                $val = ($val == 'asc' ? 'desc' : 'asc');
            }
            else {
                $val = 'asc';
            }
        }

        $data = array('ord' => urlencode($_name . '=' . $val));
        $param = $this->getQueryStr($data);
        $url = $this->getPath() . '?' . $param;

        return '<span class="' . $val . '">'
                . '<img src="' . BASE_URL . $this->orderImage . '" alt='
                . '"' . ($val == 'asc' ? '↓' : '↑') . '"'
                . ' onclick="jump(\''. $url . '\');" ></span>';
    }

    /**
     * page navi : get javascript for controll
     * @return string
     */
    public function getNaviJs()
    {
        $q = $this->getQueryStr(array(), 1);

        $js = '<script type="text/javascript">' . "\n"
                . "<!-- //\n"
                . "function chg_max(obj) {\n"
                . "  var val = obj.value;\n"
                . "  $.cookie('max_row', val, { expire: 30 });\n"
                . "  var url = '" . BASE_URL . "' + location.pathname;\n"
                . "  jump(url);\n"
                . "}\n\n"
                . ""
                . "function chg_page(obj) {\n"
                . "  var url = '" . $this->getPath() . '?'
                . $q . ($q ? "&" : '') . $this->pageKey . "=';\n"
                . "  jump(url + $(obj).val());\n"
                . "}\n\n"
                . ""
                . "$(document).ready(function() {\n"
                . "  $('#search-clear').click(function() {\n"
                . "    $(this.form).find('textarea, :text, [type=number], [type=tel], select').val('').end().find(':checked').prop('checked', false);\n"
                . "  });\n"
                . "});\n\n"
                . ""
                . "// -->\n"
                . "</script>\n"
        ;
        return $js;
    }

    /**
     * page navi : get tag for controle
     * @param pagenator obeject $_data
     * @return string
     */
    public function getNavi($_data = null)
    {
        $html = "<div class=\"page_navi\">\n";
        if (2 < $_data->current) {
            $q = $this->getQueryStr(array($this->pageKey => 1));
            $url = $this->getPath() . '?' . $q;
            $html .= '<span class="tooltip" title="'
                    . $this->getView()->translate('First') . '" onclick="jump(\''
                    . $url . '\');">&lt;&lt;</span>&nbsp;&nbsp;' . "\n";
        }

        if ($_data->current != $_data->first) {
            $q = $this->getQueryStr(array($this->pageKey => $_data->current - 1));
            $url = $this->getPath() . '?' . $q;
            $html .= '<span class="tooltip" title="'
                    . $this->getView()->translate('Previous') . '" onclick="jump(\''
//                    . $this->getView()->translate('前頁') . '" onclick="jump(\''
                    . $url . '\');">&lt;</span>&nbsp;&nbsp;' . "\n";
        }

        $html .= '<select class="tooltip" name="jump_page" onchange="chg_page(this);" title="'
                . $this->getView()->translate('Select Page') . '">' . "\n";
//                . $this->getView()->translate('頁を指定') . '">' . "\n";
        foreach ($_data->pagesInRange as $val) {
            $select = ($val == $_data->current ? ' selected' : '');
            $html .= "<option value=\"{$val}\"{$select}>{$val}</option>\n";
        }
        $html .= "</select>&nbsp;page&nbsp;&nbsp;\n";

        if (isset($_data->next) && $_data->current != $_data->last) {
            $q = $this->getQueryStr(array($this->pageKey => $_data->next));
            $url = $this->getPath() . '?' . $q;
            $html .= '<span class="tooltip" title="'
                    . $this->getView()->translate('Next') . '" onclick="jump(\''
//                    . $this->getView()->translate('次頁') . '" onclick="jump(\''
                    . $url . '\');">&gt;</span>&nbsp;&nbsp;' . "\n";
        }

        if ($_data->current < $_data->last - 1) {
            $q = $this->getQueryStr(array($this->pageKey => $_data->last));
            $url = $this->getPath() . '?' . $q;
            $html .= '<span class="tooltip" title="'
                    . $this->getView()->translate('End') . '" onclick="jump(\''
//                    . $this->getView()->translate('最後') . '" onclick="jump(\''
                    . $url . '\');">&gt;&gt;</span>&nbsp;&nbsp;' . "\n";
        }
        $html .= "<span class=\"navi_separator\">|</span>&nbsp;&nbsp;\n";

        $html .= '<select class="tooltip" name="max_rows" onchange="chg_max(this);" title="'
                    . $this->getView()->translate('Change Display Number') . '">' . "\n";
//                    . $this->getView()->translate('表示件数を変更') . '">' . "\n";
        foreach ($this->displayRowItems as $val) {
            $select = ($val == $_data->itemCountPerPage ? ' selected' : '');
            $html .= "<option value=\"{$val}\"{$select}>{$val}</option>\n";
        }
        $html .= "</select>&nbsp;max&nbsp;&nbsp;\n"
                . "(total:{$_data->totalItemCount}, {$_data->current} / {$_data->pageCount} page)\n"
                . "</div>\n";

        return $html;
    }
    
    public function getMaxView($_data = null)
    {
        $html = "<div class=\"page_navi\"> Show \n";
 
        $html .= '<select class="tooltip" name="max_rows" onchange="chg_max(this);" title="'
                    . $this->getView()->translate('Change Display Number') . '">' . "\n";
//                    . $this->getView()->translate('表示件数を変更') . '">' . "\n";
        foreach ($this->displayRowItems as $val) {
            $select = ($val == $_data->itemCountPerPage ? ' selected' : '');
            $html .= "<option value=\"{$val}\"{$select}>{$val}</option>\n";
        }
        $html .= "</select>&nbsp;Records&nbsp;&nbsp;\n"
                . "(total:{$_data->totalItemCount}, {$_data->current} / {$_data->pageCount} page)\n"
                . "</div>\n";

        return $html;
    }
    
    public function getPageJump($_data = null)
    {
        $html = "<div class=\"jump_nav\">\n";
        if($_data->pageCount != 0){
        $html .= '<p>Jump to ';         
        $html .= '<select class="tooltip" name="jump_page" onchange="chg_page(this);" title="'
                . $this->getView()->translate('Select Page') . '">' . "\n";
//                . $this->getView()->translate('頁を指定') . '">' . "\n";
        foreach ($_data->pagesInRange as $val) {
            $select = ($val == $_data->current ? ' selected' : '');
            $html .= "<option value=\"{$val}\"{$select}>{$val}</option>\n";
        }
        $html .= "</select>&nbsp;page&nbsp;&nbsp;\n </p>";
        }
        $html .= '</div>';
        return $html;
    }
    
    public function getPagination($_data)
    {
        $html = "<ul class=\"pagination\">\n";
       
            if (2 < $_data->current) {
                $q = $this->getQueryStr(array($this->pageKey => 1));
                $url = $this->getPath() . '?' . $q;
                $html .= "<li><a href=\"{$url}\" title='".$this->getView()->translate('First')."'>&lt;&lt;</a></li>&nbsp;&nbsp;" . "\n";
            }

            if ($_data->current != $_data->first) {
                $q = $this->getQueryStr(array($this->pageKey => $_data->current - 1));
                $url = $this->getPath() . '?' . $q;
                $html .= "<li><a href=\"{$url}\" title='".$this->getView()->translate('Previous')."'>&lt;</a></li>&nbsp;&nbsp;" . "\n";
            }

            foreach ($_data->pagesInRange as $val) {
                $list = ($val == $_data->current ? ' active' : '');
                $q = $this->getQueryStr(array($this->pageKey => $val));
                $url = $this->getPath() . '?' . $q;
                
                if($_data->pageCount != 0){
                    $html .= "<li class=\"{$list}\"><a href=\"{$url}\">{$val}</a></li>\n";
                }

            }

           if (isset($_data->next) && $_data->current != $_data->last) {
                $q = $this->getQueryStr(array($this->pageKey => $_data->next));
                $url = $this->getPath() . '?' . $q;
                $html .= "<li><a href=\"{$url}\" title='".$this->getView()->translate('Next')."'>&gt;</a></li>&nbsp;&nbsp;" . "\n";
            }

            if ($_data->current < $_data->last - 1) {
                $q = $this->getQueryStr(array($this->pageKey => $_data->last));
                $url = $this->getPath() . '?' . $q;
                $html .= "<li><a  href=\"{$url}\" title='".$this->getView()->translate('Last')."'>&gt;&gt;</a></li>&nbsp;&nbsp;" . "\n";
            }
   
         

        $html .= "</ul>";
        return $html;
    }
}
