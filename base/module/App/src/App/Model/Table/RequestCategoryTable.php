<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Model\Table;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Filter\Null;

class RequestCategoryTable extends TableModel 
{
    protected $tableName = 't_request_category';
    protected $primary = 'id';
    protected $id;     
    protected $menu_item_name;     
    protected $parent_id;       
    protected $menu_item_code;        /* varchar(50) */
    protected $created;           /* timestamp */
    protected $modified;         /* timestamp */
    protected $created_by;     /* int user id  */
    protected $approver_no;
  
    

    public function __construct($adapter = null)
    {
        $this->reset();
        parent::__construct($adapter);
    }


    public function reset()
    {
        $this->id = "";
        $this->menu_item_name = "";
        $this->parent_id = "";
        $this->menu_item_code = "";
        $this->created = "";
        $this->modified = "";
        $this->created_by = "";
        $this->approver_no = "";
    }


    public function exchanegArray($_data){
        $this->id = (int) gv('id', $_data);
        $this->menu_item_name = (string) gv('menu_item_name', $_data);
        $this->branch_no = (string) gv('branch_no', $_data);
        $this->parent_id = (string) gv('parent_id', $_data);
        $this->menu_item_code = (string) gv('menu_item_code', $_data);
        $this->created = (string) gv('created', $_data);
        $this->modified = (string) gv('modified', $_data);
        $this->created_by = (string) gv('created_by', $_data);
        $this->approver_no = (int) gv('approver_no', $_data);
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
   
    
    
    public function getPreviousRoute($id){
    
        
        $result = $this->adapter->query("SELECT id, menu_item_name, parent_id from t_request_category WHERE id = {$id}")->execute();
        $row = $result->current();
        return $row; 
      
    }
    

    public function addParentMenu($data){
        
       $insertData = array('id'=>null,
                           'menu_item_name'=>\strtoupper($data["menu_item_name"]),
                           'branch_no'=>$data["branch_no"],
                           'parent_id'=>$data["parent_id"],
       );
       
       $this->insert($insertData);
        
    }
    
    public function addChildMenu($data){
        
        if(!(int)$data["parent_id"]){
            throw new \Exception("Parent id is required");
        }
    
        $insertData = array('id'=>null,
                'menu_item_name'=>\strtoupper($data["menu_item_name"]),
                'branch_no'=>$data["branch_no"],
                'parent_id'=>$data["parent_id"],
        );
         
        $this->insert($insertData);
    
    }
    
    
    /**
     * check if menu can be access by branch id
     * @param unknown $branch_no
     * @param unknown $menu_id
     */
    public function checkBranchAccess($branch_no,$menu_id){
    	
    	$menu_id = (int)$menu_id;
    
    	$selectResult = $this->select(array('id'=>$menu_id));
    	$menu =  	$selectResult->current(); 
    	 
    	
    	if($menu['branch_no'] != $branch_no &&
    			$menu_id > 1){
    		throw new \Exception(" Access to menu not allowed");
    	}
    	
    	return true;
    }
    
    /**
     * all all parent menus
     */
    public function getChildMenu($id,$brach_no = 0){
    	
    	$brach_no = (int) $brach_no;
        
        if(!$id){
            $sql = "SELECT t_request_category.id, 
                           t_request_category.menu_item_name, 
                           t_request_category.created,
                           m_position.position_name,
                           m_branch.branch_name
                    FROM   t_request_category
                    LEFT JOIN m_position 
                    ON  t_request_category.position_no = m_position.position_no 
                    LEFT JOIN m_branch
                    ON t_request_category.branch_no = m_branch.branch_no
                  WHERE (parent_id IS NULL OR parent_id = 0 ) ";
            
             if($brach_no > 0){
             	$sql .= " AND m_branch.branch_no = {$brach_no} ";
             }
             
        }else{
            $sql = "SELECT t_request_category.id, 
                           t_request_category.menu_item_name, 
                           t_request_category.created,
                           m_position.position_name,
                           m_branch.branch_name
                    FROM   t_request_category 
                    LEFT JOIN m_position 
                    ON  t_request_category.position_no = m_position.position_no 
                    LEFT JOIN m_branch
                    ON t_request_category.branch_no = m_branch.branch_no
                  WHERE (parent_id = {$id}) ";
        }
        
      

       $sql .=    "ORDER BY t_request_category.id";
        
       $adapter =  $this->adapter;
       $handle = $adapter->query($sql);
       $rows = $handle->execute();
       return $rows;
    }
    
    
    public function getParent($_id){
        $id = (int)$_id;
        if(!$id){
            throw new \Exception("Can't find record with that id");
        }
        
        $rows = $this->select(array(id=>$id));
        $data =  $rows->current();
       return $data;
    }



    public function getMenuItemById($_id){
        $id = (int) $_id;
        $rowset = $this->select(array('id'=>$id));
        $row = $rowset->current();
        
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }
    
    
    public function updateMenuItem($data){
         $this->update($data,array('id' => $data->id));
    }
    
    public function insertRecord($data){

        var_dump($data);
        exit;

//         if (!$data) {
//             return false;
//         }


//         $values = array(
//             'branch_no' => $maxId,
//             'branch_name' => $this->branch_name,
//             'abbr_name' => $this->abbr_name,
//             'timezone' => $this->timezone,
//             'phone' => $this->phone,
//             'address' => $this->address,
//             'create_user' => (int) $_user_no,
//             'create_time' => new Expression('UTC_TIMESTAMP'),
//             'update_user' => (int) $_user_no,
//             'update_time' => new Expression('UTC_TIMESTAMP'),
//         );

//         return $this->insert($values);
     }
}
