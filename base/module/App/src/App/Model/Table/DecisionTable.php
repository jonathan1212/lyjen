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
use Zend\Validator\Explode;
use Zend\Db\ResultSet\ResultSet;

class DecisionTable extends TableModel
{
    protected $tableName = 't_decision';
    protected $primary = 'decision_no';
    protected $priName = 'decision_title';

    public  $decision_no;
    public  $ref_no;
    public  $branch_no;     
    public  $decision_title;   
    public  $document;       
    public  $remarks;       
    public  $preferred_date; 
    public  $create_user;
    public  $update_time;
    public  $status;
    

    public function __construct($adapter = null)
    {
        $this->reset();
        parent::__construct($adapter);
    }

   /**
     * reset
     */
    public function reset()
    {
		$this->decision_no = "";
		$this->ref_no = "";
		$this->branch_no = "";   
		$this->decision_title = "";  
		$this->document = "";    
		$this->remarks = "";    
		$this->preferred_date = "";
		$this->create_user = "";
        $this->update_time = "";
        $this->status = "";
    }

    /**
     * store array for each property
     * @param array $_data
     */
    public function exchanegArray($_data)
    {
        $this->decision_no = (int)$_data['decision_no'];
        $this->ref_no = (string) $_data['ref_no'];
        $this->branch_no = (int)$_data['branch_no'];
        $this->create_user = (int)$_data['create_user'];
        $this->decision_title = (string) $_data['decision_title'];
        $this->document = (string) $_data['document'];
        $this->remarks = (string) $_data['remarks'];
        $this->preferred_date = (string) $_data['preferred_date'];
        $this->update_time = (string) $_data['update_time'];
        $this->status = (string) $_data['status'];
    }
    
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    

    
    
     public function getUserDecision($decision_no,$owner_no){
         
         $decision_no = (int)$decision_no;
         $owner_no    = (int)$owner_no;
         
         if(!$decision_no && !$owner_no){
             throw new \Exception("Invalid Argument");
         }
         
         $rowset = $this->select(array('create_user' => $owner_no,'decision_no'=>$decision_no));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find decision data, are you tryin to access someone else record?");
         }
         
         return $row;
     }
     
     
     public function getApprovalDetails($decision_no,$loggedin_user_no){
        $decision_no = (int)$decision_no;
        if(!$decision_no ){
            throw new \Exception("Invalid Argument");
        }
        $statement = $this->adapter->query("SELECT t_decision.*,
                                                   m_user.user_name,
                                                   m_position.abbr_name,
                                                   m_user.email
                                           FROM t_decision 
                                          LEFT JOIN m_user 
                                                ON t_decision.create_user = m_user.user_no
                                          LEFT JOIN m_position
                                                ON m_user.position_no = m_position.position_no
                                           WHERE t_decision.decision_no = {$decision_no}"); 
        $result = $statement->execute();
        $record = $result->current();
        
        $statement2 = $this->adapter->query("SELECT * FROM `t_decision_approval` WHERE t_decision_approval.decision_no = {$decision_no} AND t_decision_approval.user_no = {$loggedin_user_no} AND (turn = 1 OR status_no = 6)")->execute();
        $approver = $statement2->current();

        if(!$record){
            throw new \Exception("Unable to get approval details, its either the data doestn't exists or you dont have correct privileges to access it");
        }
        if((!$approver) && ($record['create_user']!= $loggedin_user_no)){
              throw new \Exception("Unable to view. You don't have the privilege to access it");
        }
        
        if(($record['status'] != "recalled_by_owner") || ($record['status'] == "recalled_by_owner" && $record['create_user'] == $loggedin_user_no)){
            return $record;
        
        }else{throw new \Exception("Unable to view approval details, it's either you don't have the privilege to access it or has been recalled by applicant");}
       
     }
     
     public function getApprovalAttachments($decision_no){
         $resultSet = new ResultSet();
         
         $sql = "SELECT  t_decision_file.file_no,
                         t_decision_file.decision_no,
                         t_decision_file.file_name,
                         t_decision_file.content_type,
                         OCTET_LENGTH(t_decision_file.binary_data) as file_size
                 FROM  t_decision_file 
                 WHERE  t_decision_file.decision_no = {$decision_no}";
         
          
         $statement = $this->adapter->query($sql); // i dont know this but lets use 1 for now
         $result = $statement->execute();
         $resultSet->initialize($result);
         return $resultSet->toArray();
     }
     
     
       public function getApproverList($decision_no,$loggedin_user_no){
         
         $loggedin_user_no = (int)$loggedin_user_no;
         
         if(!$loggedin_user_no){
           throw new \Exception("Unable to get approver's list wrong user_no");    
         }
         
         
         $resultSet = new ResultSet();
         
         $sql = "SELECT t_decision_approval.priority,
                        t_decision_approval.status_no,
                        t_decision_approval.remarks,
                        t_decision_approval.last_update, 
                        m_user.user_no,
                        m_user.user_name,
                        m_position.position_name,
                        m_position.abbr_name,
                        m_status.status_name,
                        m_section.abbr_name as section_abbr_name
                   FROM t_decision_approval 
              LEFT JOIN m_user 
                     ON t_decision_approval.user_no = m_user.user_no
              LEFT JOIN m_position
                     ON m_user.position_no = m_position.position_no
              LEFT JOIN m_status 
                     ON t_decision_approval.status_no = m_status.status_no
              LEFT JOIN m_section
                     ON m_user.section_no = m_section.section_no
                  WHERE t_decision_approval.decision_no = {$decision_no}
                   ORDER BY t_decision_approval.priority ASC";
         
         
         $statement = $this->adapter->query($sql); // i dont know this but lets use 1 for now
         $result = $statement->execute();
         $resultSet->initialize($result);
         $list =  $resultSet->toArray();
         $approversListAr =  array();
         $temAr = array();
         if(is_array($list)){
             
             
             foreach ($list as $l){
                
                 if($loggedin_user_no == $l['user_no']){
                      $approversListAr[] =  array_merge($l,array('active'=>true));
                 }else{
                     $approversListAr[] =  array_merge($l,array('active'=>false));
                 }
                  
                
             }
             
             
             return $approversListAr;
         }
         
         
         
       return false;
     }
     
     
     /**
      * 
      * @param int $user_number the id number of the author 
      * @throws \Exception
      */
     public function addDecision($user_number)
     {
         $data = array(
                 'decision_no'=>$this->decision_no,
                 'update_user' => $user_number,
                 'create_user' => $user_number,
                 'ref_no'  => $this->ref_no,
                 'branch_no' => $this->branch_no,
                 'decision_title'=>$this->decision_title,
                 'document' => $this->document,
                 'remarks' => $this->remarks,
                 'preferred_date' => $this->preferred_date,
                 'status' => 'receiving',
                 'create_time'=>new Expression('UTC_TIMESTAMP')
         );
         
       
         $this->update($data,array('decision_no'=>$this->decision_no));
     
         return $this->getMaxId();
     }
     
     
     /**
      * 
      * @param unknown $decision_no
      * @param unknown $approvers_csv
      * @throws Exception
      */
    public function setUpApprovers($decision_no, $approvers_csv){
         $approver_ids = explode(",", $approvers_csv);
         
         if(!is_array($approver_ids) &&  
                 count($approver_ids)<1){
             
             throw new Exception("Invalid Approver's Ids ");
         }
         
         //clear all approvers
          $this->deleteApproversFromRequest($decision_no);
    
         
         $count = 0;
         $turn = 1;
         foreach ($approver_ids as $user_no){
             
            
             $statement = $this->adapter->query("INSERT INTO 
                                                 t_decision_approval
                                                 SET decision_no = {$decision_no},
                                                     user_no = {$user_no},
                                                     priority = {$count}, 
                                                     status_no = 0,
                                                     turn = {$turn}"); // i dont know this but lets use 1 for now
             $statement->execute();
             $count++;
             $turn = 0;
         }
         
        return ($count == count($approver_ids));
 
     }
     
    public function deleteApproversFromRequest($decision_no){
      $statement = $this->adapter->query("DELETE FROM t_decision_approval where t_decision_approval.decision_no  = {$decision_no}"); 
      $statement->execute();
     }
     
     /**
      * 
      * @param unknown $decision_no
      * @param unknown $attachments_csv
      */
     public function setUpAttachements($decision_no, $attachments_csv){
      
         
         $attachments_files = explode(",", $attachments_csv);
         $count = 0;
         
          foreach ($attachments_files as $file){
              //TODO: move the file from temp folder to secret folder 
              $file = trim($file);
              $tmpName = APP_UPLOAD_DIR.trim($file);
              
              if(!file_exists($tmpName)){
                 
                  throw new \Exception("File doest not exist ".$tmpName);
              }
              
              
              $fp = fopen($tmpName, 'r');
              $content = fread($fp, filesize($tmpName));
              //close file reader
              fclose($fp);
              
              $finfo = \finfo_open(FILEINFO_MIME_TYPE);
              $mime = \finfo_file($finfo, $tmpName);
              \finfo_close($finfo);
              
              
              $sql = "INSERT INTO t_decision_file(decision_no,file_name,content_type,binary_data)
                      VALUES({$decision_no},'{$file}','{$mime}','" .\addslashes($content) . "')";
              
              $statement = $this->adapter->query($sql); // i dont know this but lets use 1 for now
              $statement->execute();
              $count++;
              
          }
          //TODO: clean the file from tem folder
          return ($count == count($attachments_files));  
     }
     
     
    
    public function getCategory1($branch_no){
        $branch_no = (int)$branch_no;
        if(!$branch_no){
            throw new \Exception("Unable to find branch with that id");
        }

        
        $sql = "SELECT t.id,t.branch_no, t.menu_item_name 
                   FROM (
                          SELECT t_request_category.id, 
                                 t_request_category.menu_item_name,
                                 t_request_category.branch_no 
                            FROM t_request_category  
                           WHERE t_request_category.parent_id IS NULL 
                              OR parent_id = 0) AS t
                  WHERE t.branch_no ={$branch_no}";
        
        
      

        
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
         
        $selectData = array();
         
        foreach ($result as $res) {
    
            $selectData[$res['id']] = $res['menu_item_name'];
        }
         
 
         
        return $selectData;
    }
     public function approveRequest($decision_no,$logged_user_no,$remarks="approved"){
         
         $decision_no  = (int)$decision_no;
         $logged_user_no = (int)$logged_user_no;
         $sql = "UPDATE t_decision_approval SET 
                  t_decision_approval.remarks = '".\addslashes($remarks)."',
                  t_decision_approval.status_no = 6,
                  t_decision_approval.last_update = NOW()
                 WHERE t_decision_approval.decision_no = {$decision_no} AND 
                       t_decision_approval.user_no = {$logged_user_no}";
        
         $statement = $this->adapter->query($sql);
         $result = $statement->execute();
         $this->updateApproversTurn($decision_no, $logged_user_no);
      
         return $result;
     }
     private function updateApproversTurn($decision_no,$user_no){
         
         //update all turn to zero
         $statement1 = $this->adapter->query("Update t_decision_approval set t_decision_approval.turn = 0 
                                             WHERE t_decision_approval.decision_no = {$decision_no}");
         $statement1->execute();
         
         
         
         $statement2 = $this->adapter->query("SELECT t_decision_approval.priority,
                                                     t_decision_approval.user_no
                                                FROM t_decision_approval
                                               WHERE t_decision_approval.decision_no = {$decision_no}
                                               AND  t_decision_approval.user_no  = {$user_no} ")->execute();
       
         $user = $statement2->current();
         $priority = (int)$user['priority']+1;
         $prev_priority =  (int)$user['priority']-1;
         
         $statement3 = $this->adapter->query("Update t_decision_approval set t_decision_approval.turn = 1 
                                               WHERE t_decision_approval.decision_no = {$decision_no}
                                                AND t_decision_approval.priority = {$priority}")->execute();
                                                
         $statement4 = $this->adapter->query("Update t_decision_approval set t_decision_approval.status_no = 4 
                                               WHERE t_decision_approval.decision_no = {$decision_no}
                                                AND t_decision_approval.priority = {$prev_priority}")->execute();

        if($this->isCompleted($decision_no)){
             $this->changeStatuToComplete($decision_no);
             
         }else{
             $this->changeStatuToProgressing($decision_no);
         }
     }
     public function changeStatuToProgressing($decision_no){
          
     
         $statement2 = $this->adapter->query("Update t_decision set status  = 'progressing'
                 WHERE  t_decision.decision_no = {$decision_no}
         AND (t_decision.status = 'receiving' OR t_decision.status=  'recalled')")->execute();
     }
     public function changeStatuToDraft($decision_no){
          
     
         $statement2 = $this->adapter->query("Update t_decision set status  = 'draft'
                 WHERE  t_decision.decision_no = {$decision_no}
         AND t_decision.status = 'receiving'")->execute();
     }
     
     public function changeStatuToComplete($decision_no){
     
          
         $statement2 = $this->adapter->query("Update t_decision set status  = 'complete'
                 WHERE  t_decision.decision_no = {$decision_no}
         AND t_decision.status = 'progressing'")->execute();
     }
     
     public function changeStatuToRecalled($decision_no){
          
     
         $statement2 = $this->adapter->query("Update t_decision set status  = 'recalled'
                 WHERE  t_decision.decision_no = {$decision_no}
         AND t_decision.status = 'receiving' OR t_decision.status = 'progressing'")->execute();
     }
     public function isCompleted($decision_no){
        
        $sql  = "SELECT count(*) as total_record 
                 FROM t_decision_approval 
                 WHERE t_decision_approval.decision_no = {$decision_no} 
                   AND t_decision_approval.status_no IN(0,1,2,5)";
  
         
         $statement = $this->adapter->query($sql)->execute();
         $result =  $statement->current();
         
      return ($result['total_record'] == 0);
         
     }
     /**
      * Reject Request 
      * @param unknown $decision_no
      * @param unknown $logged_user_no
      * @param string $remarks
      */
     public function rejectRequest($decision_no,$logged_user_no,$remarks="reject"){
          
         $decision_no  = (int)$decision_no;
         $logged_user_no = (int)$logged_user_no;
         $sql = "UPDATE t_decision_approval SET
                  t_decision_approval.remarks = '".\addslashes($remarks)."',
                       t_decision_approval.status_no = 3
                       WHERE t_decision_approval.decision_no = {$decision_no} AND
                       t_decision_approval.user_no = {$logged_user_no}";
     
         $statement = $this->adapter->query($sql);
         $result = $statement->execute();
         $this->adapter->query("UPDATE t_decision 
                                    SET t_decision.status = 'rejected' 
                                    WHERE t_decision.decision_no = {$decision_no}")->execute();
                                    
         return $result;
     }
     
     
     /**
      * Recall Request
      * @param unknown $decision_no
      * @param unknown $logged_user_no
      * @param string $remarks
      */
       public function recallRequest($decision_no,$logged_user_no,$remarks="reject"){
     
         $decision_no  = (int)$decision_no;
         $logged_user_no = (int)$logged_user_no;
         $sql = "UPDATE t_decision_approval SET
                  t_decision_approval.remarks = '".\addslashes($remarks)."',
                       t_decision_approval.status_no = 2,
                       t_decision_approval.turn = 1
                       WHERE t_decision_approval.decision_no = {$decision_no} AND
                       t_decision_approval.user_no = {$logged_user_no}";
          
         $statement = $this->adapter->query($sql);
         $result = $statement->execute();
         
         $this->adapter->query("UPDATE t_decision 
                                    SET t_decision.status = 'recalled' 
                                    WHERE t_decision.decision_no = {$decision_no}")->execute();
        
        $statement1 = $this->adapter->query("SELECT t_decision_approval.decision_no,
                                                     t_decision_approval.priority,
                                                     t_decision_approval.user_no
                                                FROM t_decision_approval
                                               WHERE t_decision_approval.decision_no = {$decision_no}
                                               AND  t_decision_approval.user_no  = {$logged_user_no} ")->execute();
       
         $user = $statement1->current();
         $decision_no = (int)$user['decision_no'];
         $priority = (int)$user['priority']+1;
         
         $statement2 = $this->adapter->query("Update t_decision_approval set t_decision_approval.turn = 0 
                                               WHERE t_decision_approval.decision_no = {$decision_no}
                                                AND t_decision_approval.priority = {$priority}")->execute();
         return $result;
     }
     public function recallByOwner($decision_no,$logged_user_no,$remarks="recall"){
     
         $decision_no  = (int)$decision_no;
         $logged_user_no = (int)$logged_user_no;
         $sql = "UPDATE t_decision SET
                t_decision.status = 'recalled_by_owner',
                t_decision.remarks = '".\addslashes($remarks)."'
                       WHERE t_decision.decision_no = {$decision_no} AND
                       t_decision.create_user = {$logged_user_no}";
          
         $statement = $this->adapter->query($sql);
         $result = $statement->execute();
         
         return $result;
     }
     
     public function getCurrentApprover($_decision_no){
        
        $statement = $this->adapter->query("SELECT t_decision_approval.decision_no,t_decision_approval.user_no,t_decision_approval.turn, m_user.user_name, m_user.email  FROM t_decision_approval
        LEFT JOIN m_user ON t_decision_approval.user_no = m_user.user_no
        WHERE t_decision_approval.decision_no = {$_decision_no} AND t_decision_approval.turn = 1")->execute();
        $result = $statement->current();
        
        return $result;
     }
     
     public function getApprovers($_decision_no){

        $resultSet = new ResultSet();
         
        $sql = "SELECT t_decision_approval.decision_no,t_decision_approval.user_no, m_user.user_name, m_user.email AS email FROM t_decision_approval LEFT JOIN m_user ON t_decision_approval.user_no = m_user.user_no WHERE t_decision_approval.decision_no = {$_decision_no} AND (t_decision_approval.status_no = 4 OR t_decision_approval.status_no = 6 OR t_decision_approval.turn = 1)";
         
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $resultSet->initialize($result);
        $list =  $resultSet->toArray();
        $approvers =  array();
   
        foreach ($list as $l){
           $approvers[] = $l;
        }
             
        return $approvers;
    }
    public function nextApprover($_decision_no){
        $statement = $this->adapter->query("SELECT *  FROM t_decision_approval WHERE t_decision_approval.decision_no = {$_decision_no} AND t_decision_approval.status_no = 2 AND t_decision_approval.turn = 1")->execute();
        $result = $statement->current();
        $priority = (int)$result['priority']+1;
        
        $statement2 = $this->adapter->query("SELECT t_decision_approval.*, m_user.user_name, m_user.email FROM t_decision_approval LEFT JOIN m_user ON t_decision_approval.user_no = m_user.user_no WHERE t_decision_approval.decision_no = {$_decision_no} AND t_decision_approval.priority = {$priority}")->execute();
        $approver = $statement2->current();
        return $approver;
    }
   public function saveDraft($_decision_no,$_user_no)
     {
        if (!$_user_no) {
            return false;
        }
        $data = array(
            'decision_no'=> $_decision_no,
            'update_user' => $_user_no,
            'create_user' => $_user_no,
            'ref_no'  => $this->ref_no,
            'branch_no' => $this->branch_no,
            'decision_title'=>$this->decision_title,
            'document' => $this->document,
            'remarks' => $this->remarks,
            'preferred_date' => $this->preferred_date,
            'status' => 'draft',
            'create_time'=>new Expression('UTC_TIMESTAMP')
         );

        return $this->update($data,array('decision_no'=>$_decision_no,'create_user'=>$_user_no));

     }
}
