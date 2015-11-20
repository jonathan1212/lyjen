<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Filter;
use App\Filter\AbstractFilter;



class RequestCategoryFilter extends AbstractFilter
{
    
    public function setInputFilter(){
    
        $this->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
        $this->createInput(array(
                'name' => 'parent_id',
                'required' => false,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
    
        $this->createInput(array(
                'name' => 'approver_no',
                'required' => false,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
 
    
        $this->createInput(array(
                'name' => 'menu_item_name',
                'required' => true,
                'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 75,
                                ),
                        ),
                ),
        ));
        
        
        $this->createInput(array(
                'name' => 'menu_item_code',
                'required' => false,
                'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 10,
                                ),
                        ),
                ),
        ));

    
        $this->inputFilter = $this->getFilter();
    }
    
    
    
    public function setChildListFilter(){
        
        $this->createInput(array(
                'name' => 'branch_no',
                'required' => true,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
        
        $this->createInput(array(
                'name' => 'parent_id',
                'required' => true,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
        
        $this->createInput(array(
                'name' => 'menu_item_name',
                'required' => true,
                'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 75,
                                ),
                        ),
                ),
        ));
        

        
        $this->inputFilter = $this->getFilter();
    }
    
    
    public function setRootListFilter(){
    
    
        $this->createInput(array(
                'name' => 'menu_item_name',
                'required' => true,
                'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                ),
                'validators' => array(
                        array(
                                'name' => 'StringLength',
                                'options' => array(
                                        'encoding' => 'UTF-8',
                                        'max' => 75,
                                ),
                        ),
                ),
        ));
    
    
        $this->createInput(array(
                'name' => 'branch_no',
                'required' => true,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
        
        $this->createInput(array(
                'name' => 'parent_id',
                'required' => true,
                'filters' => array(
                        array('name' => 'Int'),
                ),
        ));
    
    
        $this->inputFilter = $this->getFilter();
    }

}