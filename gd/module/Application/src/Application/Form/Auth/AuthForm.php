<?php
namespace Application\Form\Auth;

use Zend\Form\Form;
use Zend\InputFilter;

class AuthForm extends Form
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
            'name' => 'password',
            'type' => 'Password',
        	'attributes' => array(
        	  'id'=>'inputPassword3',
        	  'class' => 'form-control',
        			'placeholder' => 'Password',
        	),
        ));

         $this->add(array(
            'name' => 'Loginsubmit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Login',
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

        //password
        $password = new InputFilter\Input('password');
        $password->setRequired(true);
        $inputFilter->add($password);

        return $inputFilter;
    }
}
