<?php
namespace Application\Form\Auth;

use Zend\Form\Form;
use Zend\InputFilter;
class forgotPasswordForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
        		'attributes' => array(
        				'id'=>'inputEmail3',
        				'class' => 'form-control',
        				'placeholder' => 'email address',
        		),
        ));

         $this->add(array(
            'name' => 'Loginsubmit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Send',
                'id' => 'Loginsubmit',
            	'class'=>'btn btn-info pull-right',
            ),
        ));

        $this->setInputFilter($this->createInputFilter());
    }

    public function createInputFilter(){
        $inputFilter = new InputFilter\InputFilter();
        //username
        $username = new InputFilter\Input('username');
        $username->setRequired(true);
        $inputFilter->add($username);

        return $inputFilter;
    }
}
