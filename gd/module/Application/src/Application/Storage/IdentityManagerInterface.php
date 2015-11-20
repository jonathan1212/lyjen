<?php
namespace Application\Storage;

interface IdentityManagerInterface
{
    public function login($identity, $credential);
    public function logout();
    public function hasIdentity();
    public function getIdentity();
    public function storeIdentity(array $identity);
    public function getAuthService();
    public function getCurrentIp();
    public function incrementLoginAttempt($username);
    public function getUserDetails($id);
    
}
