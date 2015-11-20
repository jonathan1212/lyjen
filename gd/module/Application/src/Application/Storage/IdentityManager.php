<?php
namespace Application\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;

class IdentityManager implements IdentityManagerInterface
{
    protected $authService;
    protected $dbAdapter;

    public function __construct(AuthenticationService $authService,  AdapterInterface $dbAdapter)
    {
        $this->authService = $authService;
        $this->dbAdapter = $dbAdapter; 
    }

    public function getAuthService()
    {
        return $this->authService;
    }

    /*
     * $identity : username
     * $credential: password
     */
    public function login($identity, $credential) {
        $this->getAuthService()->getAdapter()
             ->setIdentity($identity)
             ->setCredential($credential);

        //TODO: use Bcrypt 
        $result = $this->getAuthService()->authenticate();
        
        return $result;
    }

    public function logout(){
        $this->getAuthService()->getStorage()->clear();
    }

    /*
     * 
     */
    public function hasIdentity(){
        $sessionId = $this->getAuthService()->getStorage()->getSessionId();

        return $this->getAuthService()->getStorage()
                    ->getSessionManager()
                    ->getSaveHandler()
                    ->read($sessionId);
    }
    
    /*
     *
     */
    public function getIdentity(){
    	$sessionId = $this->getAuthService()->getStorage()->getSessionId();
    
    	return json_decode(
    			$this->getAuthService()->getStorage()
    	->getSessionManager()
    	->getSaveHandler()
    	->read($sessionId));
    }
    
    

    public function storeIdentity(array $identity)
    {
        $this->getAuthService()->getStorage()
             ->write($identity);
    }
    
    public function incrementLoginAttempt($username){
    	$result = $this->dbAdapter->query("select * from m_user where m_user.email_address = '{$username}'")->execute();
    	$data  = $result->current();
    	$userDetails = $this->getUserDetails($data['user_id']);
    	//increment
    	$count = (int)$userDetails['pw_error_count'];
    	$count ++;
    	
    	$update = "UPDATE m_user set m_user.pw_error_count = ".$count." ".
    	          "where m_user.user_id = ".$userDetails['user_id'];
    	
    	$query = $this->dbAdapter->query($update)->execute();
    	$query->execute();
    }
    
    public function getUserDetails($id){
    	$result = $this->dbAdapter->query("select * from m_user where m_user.user_id = {$id}")->execute();
    	$data  = $result->current();
    	return $data;
    }
    
    function getCurrentIp()
    {
    	$client  = @$_SERVER['HTTP_CLIENT_IP'];
    	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    	$remote  = $_SERVER['REMOTE_ADDR'];
    
    	if(filter_var($client, FILTER_VALIDATE_IP))
    	{
    		$ip = $client;
    	}
    	elseif(filter_var($forward, FILTER_VALIDATE_IP))
    	{
    		$ip = $forward;
    	}
    	else
    	{
    		$ip = $remote;
    	}
    
    	return $ip;
    }
    
}
