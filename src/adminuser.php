<?php
namespace booosta\usersystem;

class adminuser extends \booosta\genericuser\Genericuser
{
  protected $user_type = 'adminuser';

  public function make_authenticator() 
  { 
    $class = $this->config('admin_authenticator') ?? "\\booosta\\usersystem\\Admin_Authenticator";
    return $this->makeInstance($class);
  }
  
  protected function make_privileges() { return $this->makeInstance("\\booosta\\usersystem\\Admin_Privileges"); }
}
