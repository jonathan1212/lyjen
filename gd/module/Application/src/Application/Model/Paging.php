<?php
namespace Application\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Console\Prompt\Select;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet;
use Zend\Config\Processor\ProcessorInterface;



class Paging extends \Zend\Paginator\Paginator{
	
	protected $paginatorAdapter;
	protected $select;
	protected $adapter;
	protected $result;
	protected $current_page;
	
	// Zend Sql()  has an issues with paginator  https://github.com/zendframework/zf2/issues/3939
	// So lets use DbSelect when selecting selecting data from the database table
	function __construct ($select, $adapter, $result,$current_page){
		
		$this->select = $select;
		$this->adapter = $adapter;
		$this->result  = new \Zend\Db\ResultSet\ResultSet();
		$this->current_page = $current_page;
		
		$this->paginatorAdapter = new DbSelect(
				// our configured select object
				$this->select,
				// the adapter to run it against
				$this->adapter,
				// the result set to hydrate
				$this->result);
		
		parent::__construct($this->paginatorAdapter);
		$this->initPaging();
	}
	
	public function initPaging(){
		$this->setItemCountPerPage(2); // TODO: 2 must get this from config file
		$this->setCurrentPageNumber($this->current_page);
	}
	
}
