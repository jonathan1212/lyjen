<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Helper;

use Zend\Form\View\Helper\FormElementErrors as ZendFormElementErrors;

class FormElementErrors extends ZendFormElementErrors
{
    protected $messageCloseString     = '</li></ul>';
    protected $messageOpenFormat      = '<ul><li class="text-red">';
    protected $messageSeparatorString = '</li><li>';
}
