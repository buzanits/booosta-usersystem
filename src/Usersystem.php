<?php
namespace booosta\usersystem;

class Webappadmin extends \booosta\webapp\Webapp
{
  protected $index = 'admin';
  #protected $index = 'vendor/booosta/usersystem/admin.php';
  protected $user_class;

  public function __construct($name = null)
  {
    #$base_dir = $this->real_basedir();
    #$this->extra_templates = ['LEFT' => '/' . $this->usersystem_dir . 'tpl/adminuser_actions.tpl',
    $this->extra_templates = ['BOOSTAMENU' => '/tpl/adminuser_boostamenu.tpl'];

    $this->user_class = 'adminuser';
    parent::__construct($name);
    #$this->loginscript = $this->base_dir . $this->usersystem_dir . 'login_adminuser.php';
    $this->loginscript = 'login_adminuser';
    if($home_link = $this->config('home_link_admin')) $this->home_link = $home_link;

    if(is_object($this->user) && $this->user->is_valid()) $this->TPL['user_logged_in'] = true;
  }

  protected function get_toptpl()
  {
    $template_module = $this->config('template_module');

    if(is_readable($this->config('toptpl_admin'))) return $this->base_dir . $this->config('toptpl_admin');
    if(is_readable('tpl/dashboard_admin.html')) return 'tpl/dashboard_admin.html';
    if(is_readable($this->base_dir."vendor/booosta/$template_module/dashboard_admin.html")) return $this->base_dir."vendor/booosta/$template_module/dashboard_admin.html";
    if(is_readable($this->base_dir.'vendor/booosta/bootstrap/dashboard_admin.html')) return $this->base_dir.'vendor/booosta/bootstrap/dashboard_admin.html';
    if(is_readable('vendor/booosta/bootstrap/dashboard_admin.html')) return $this->base_dir.'vendor/booosta/bootstrap/dashboard_admin.html';  // only works with base_dir

    return parent::get_toptpl();
  }
}


class Webappuser extends \booosta\webapp\Webapp
{
  protected $index = 'user';
  #protected $index = 'vendor/booosta/usersystem/user.php';
  protected $user_class;
  protected $user_id;

  public function __construct($name = null)
  {
    #$base_dir = $this->real_basedir();
    #$this->extra_templates = ['LEFT' => '/' . $this->usersystem_dir . 'tpl/user_actions.tpl',
    $this->extra_templates = ['BOOSTAMENU' => '/tpl/user_boostamenu.tpl'];

    $this->user_class = 'user';
    parent::__construct($name);
    $this->loginscript = 'login_user';
    #$this->loginscript = $this->base_dir . $this->usersystem_dir . 'login_user.php';
    if(is_object($this->user)) $this->user_id = $this->user->get_id();
    if($home_link = $this->config('home_link_user')) $this->home_link = $home_link;
  }

  protected function init()
  {
    ##parent::init();

    if(is_object($this->user) && $this->user->is_valid()):
      $this->user_id = $this->user->get_id();
      $this->TPL['user_logged_in'] = true;
    endif;
    
    parent::init();
  }

  protected function get_toptpl()
  {
    $template_module = $this->config('template_module');

    if(is_readable($this->config('toptpl_user'))) return $this->base_dir . $this->config('toptpl_user');
    if(is_readable('tpl/dashboard_user.html')) return $this->base_dir.'tpl/dashboard_user.html';
    if(is_readable("vendor/booosta/$template_module/dashboard_user.html")) return $this->base_dir."vendor/booosta/$template_module/dashboard_user.html";
    if(is_readable('vendor/booosta/bootstrap/dashboard_user.html')) return $this->base_dir.'vendor/booosta/bootstrap/dashboard_user.html';
    if(is_readable('vendor/booosta/bootstrap/dashboard_user.html')) return $this->base_dir.'vendor/booosta/bootstrap/dashboard_user.html';  // only works with base_dir

    return parent::get_toptpl();
  }
}


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


class Admin_Authenticator extends \booosta\db_authenticator\DB_Authenticator
{
  protected $user_table = 'adminuser';
  protected $password_encrypted = 'true';
}


class Admin_Privileges extends \booosta\db_privileges\DB_Privileges
{
  protected $user_privilege_table = 'adminuser_privilege';
  protected $user_role_table = 'adminuser_role';
}


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

class User_Authenticator extends \booosta\db_authenticator\DB_Authenticator
{
  protected $user_table = 'user';
  protected $password_encrypted = 'true';
}

class User_Privileges extends \booosta\db_privileges\DB_Privileges
{
  protected $user_privilege_table = 'user_privilege';
  protected $user_role_table = 'user_role';
}
