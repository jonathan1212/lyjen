<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Helper;

use Zend\Form\View\Helper\FormElementErrors as ZendFormElementErrors;

class FormElementErrors extends ZendFormElementErrors
{
    protected $messageCloseString     = '</li></ul>';
    protected $messageOpenFormat      = '<ul class="err_msg"><li>';
    protected $messageSeparatorString = '</li><li>';
}
