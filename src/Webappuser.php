<?php
namespace booosta\usersystem;

class Webappuser extends \booosta\webapp\Webapp
{
  #protected $index = 'user';
  protected $index = 'vendor/booosta/usersystem/exec/user.php';
  protected $user_class;
  protected $user_id;

  public function __construct($name = null)
  {
    #$base_dir = $this->real_basedir();
    #$this->extra_templates = ['LEFT' => '/' . $this->usersystem_dir . 'tpl/user_actions.tpl',
    $this->extra_templates = ['BOOSTAMENU' => '/tpl/user_boostamenu.tpl'];

    $this->user_class = 'user';
    parent::__construct($name);

    if($this->script_extension == '') $this->index = 'user';
    #$ext = $this->config('urlhandlermode') ? '' : '.php';
    $this->loginscript = "/login_user$this->script_extension";
    #\booosta\Framework::debug($this->loginscript);
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

