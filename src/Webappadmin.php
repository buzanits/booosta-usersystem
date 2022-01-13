<?php
namespace booosta\usersystem;

class Webappadmin extends \booosta\webapp\Webapp
{
  #protected $index = 'admin';
  protected $index = 'vendor/booosta/usersystem/exec/admin.php';
  protected $user_class;
  protected $user_id;

  public function __construct($name = null)
  {
    #$base_dir = $this->real_basedir();
    #$this->extra_templates = ['LEFT' => '/' . $this->usersystem_dir . 'tpl/adminuser_actions.tpl',
    $this->extra_templates = ['BOOSTAMENU' => '/tpl/adminuser_boostamenu.tpl'];

    $this->user_class = 'adminuser';
    parent::__construct($name);

    $this->loginscript = "login_adminuser$this->script_extension";
    #\booosta\Framework::debug($this->loginscript);
    if(is_object($this->user)) $this->user_id = $this->user->get_id();
    if($home_link = $this->config('home_link_adminuser')) $this->home_link = $home_link;
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

    if(is_readable($this->config('toptpl_adminuser'))) return $this->base_dir . $this->config('toptpl_adminuser');
    if(is_readable('tpl/dashboard_adminuser.html')) return $this->base_dir.'tpl/dashboard_adminuser.html';
    if(is_readable("vendor/booosta/$template_module/dashboard_adminuser.html")) return $this->base_dir."vendor/booosta/$template_module/dashboard_adminuser.html";
    if(is_readable('vendor/booosta/bootstrap/dashboard_adminuser.html')) return $this->base_dir.'vendor/booosta/bootstrap/dashboard_adminuser.html';
    if(is_readable('vendor/booosta/bootstrap/dashboard_adminuser.html')) return $this->base_dir.'vendor/booosta/bootstrap/dashboard_adminuser.html';  // only works with base_dir


    return parent::get_toptpl();
  }
}
