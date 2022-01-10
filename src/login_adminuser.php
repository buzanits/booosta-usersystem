<?php
namespace booosta\usersystem;

include '../../chroot.php';
require_once __DIR__ . '/vendor/autoload.php';

use booosta\Framework as b;
b::load();

class App extends Webappadmin
{
  public $base_dir = '../../../';
  public $usersystem_dir = '';
  public $tpldir = 'vendor/booosta/usersystem/';
  public $translator_dir = 'vendor/booosta/usersystem';
  public $translator_merge = true;

  protected function init()
  {
    parent::init();

    // first try cookie login
    $user = $this->makeInstance("\\booosta\\usersystem\\adminuser");

    if(is_object($user) && $user->is_valid()):
      if($this->VAR['backpage']) $this->backpage = $this->base_dir . $this->VAR['backpage'];
      elseif($this->index) $this->backpage = $this->index;
      else $this->backpage = 'vendor/booosta/usersystem/adminuser.php';

      if($this->index) $this->backpage = $this->index; else $this->backpage = 'vendor/booosta/usersystem/admin.php';
      $this->TPL['AUTH_USER'] = $user->get_username();
      $this->maintpl = 'systpl/feedback.tpl';

      if(method_exists($this, 'after_login')) $this->after_login();
      if(method_exists($this, 'after_login_admin')) $this->after_login_admin();;
    else:   // cookie login failed - show username + password form
      $template_module = $this->config('template_module');
      $cfg_toptpl = $this->config('login_toptpl');

      if($template_module && $cfg_toptpl) $this->toptpl = "$this->base_dir/vendor/booosta/$template_module/$cfg_toptpl";
      elseif($template_module) $this->toptpl = "{$this->base_dir}vendor/booosta/$template_module/login.html";
      elseif($template_module) $this->toptpl = "$this->base_dir/vendor/booosta/$template_module/blank.html";
      elseif($cfg_toptpl) $this->toptpl = $this->base_dir . $cfg_toptpl;
      else $this->toptpl = $this->base_dir . 'vendor/booosta/bootstrap/blank.html';
      #\booosta\debug("toptpl: $this->toptpl");

      $this->extra_templates = ['MAIN' => $this->usersystem_dir . "tpl/adminuser_login.tpl"];

      if($_SESSION['login_failure']) $this->TPL['login_message'] = $this->t('Wrong username or password');
      else $this->TPL['login_message'] = $this->t('You are not logged in or your session has expired');

      unset($_SESSION['login_failure']);
    
      if($this->config('ask_login_token') === true || $this->config('ask_login_token_admin') === true) $this->TPL['login_token'] = true;
      elseif($this->config('ask_login_token') == 'optional' || $this->config('ask_login_token_admin') == 'optional') $this->TPL['login_token_optional'] = true;;
    endif;
  }
}

$app = new App();
$app();
