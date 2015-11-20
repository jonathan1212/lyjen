<?php
namespace Application\Form\Auth;

use Zend\Form\Form;
use Zend\InputFilter;

class LogoutForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->setAttribute('method', 'post');

        $this->add(array(
          'name' => 'LogoutYes',
          'type' => 'Submit',
          'attributes' => array(
            'value' => 'Yes',
            'id' => '',
            'class'=>'btn btn-info pull-right',
          ),
        ));
       

    }

}