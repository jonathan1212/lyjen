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

class ViewButton extends AbstractHelper
{

    /**
     * get javascript for confirm detail button
     * @return string
     */
    public function getViewButtonJs()
    {

        $js = '<script type="text/javascript">' . "\n"
                . "<!-- //\n"
                . "function view_button(url, h, w, f) {\n"
                . "  var frame = f ? true : false;\n"
                . "  $.colorbox({\n"
                . "    width: w, height: h, href: url, iframe: frame" . "\n"
                . "  });\n"
                . "}\n\n"
                . "// -->\n"
                . "</script>\n"
        ;
        return $js;
    }

    /**
     * get tag for confirm detail button
     * @param array $_attr
     * @param boolean $_disabled
     * @return string|boolean
     */
    public function printViewButton($_attr = array(), $_disabled = false)
    {
        if (!$_attr) {
            return false;
        }

        $url = gv('url', $_attr);
        $val = gv('value', $_attr);
        $id = gv('id', $_attr);
        $class = gv('class', $_attr);
        $h = (int) gv('height', $_attr, 800);
        $w = (int) gv('width', $_attr, 600);
        $f = (bool) gv('frame', $_attr);
        $t = gv('title', $_attr);

        echo '<button type="button" onclick="view_button('
            . "'" . $url . "', " . $h . ", " . $w . ", "
            . ($f ? 1 : 0) . ');"'
            . ($id ? " id='{$id}'" : '')
            . ($class ? " class='{$class}'" : '')
            . ($t ? " title='{$t}'" : '')
            . ($_disabled ? ' disabled=disabled' : '')
            . ">" . $val . "</button>";
    }
}
