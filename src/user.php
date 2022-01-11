<?php
namespace booosta\usersystem;

class user extends \booosta\genericuser\Genericuser
{
  protected $user_type = 'user';

  public function make_authenticator() 
  { 
    $class = $this->config('user_authenticator') ?? "\\booosta\\usersystem\\User_Authenticator";
    return $this->makeInstance($class);
  }
  
  protected function make_privileges() { return $this->makeInstance("\\booosta\\usersystem\\User_Privileges"); }
}
