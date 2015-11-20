<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Controller;
use Zend\View\Model\ViewModel;
use App\Controller\AbstractController;

class ImageController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * select image (location:img/icon)
     * @return ViewModel
     */
    public function iconListAction()
    {
		$public = '/img/icon';

        $path = DOCUMENT_ROOT . '/public' . $public;
        $dir = dir($path);
        $row = array();
        while(($file = $dir->read()) !== false) {
            $info = pathinfo($file);
            if (gv('extension', $info)) {
                $row[$file] = $public . '/' . $file;
            }
        }
		
        $values = array(
            'col_num' => 10,
            'row' => $row,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/image/icon.phtml');
        $view->setTerminal(true);
        return $view;
    }
}
