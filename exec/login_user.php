<?php
namespace booosta\usersystem;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use booosta\Framework as b;
b::croot();
b::load();

class App extends Webappuser
{
  public $base_dir = '/';
  #public $base_dir = '../../../../';
  public $usersystem_dir = '/vendor/booosta/usersystem/exec/';
  public $tpldir = '/vendor/booosta/usersystem/exec/tpl/';
  public $translator_dir = '/vendor/booosta/usersystem';
  public $translator_merge = true;

  protected function init()
  {
    parent::init();

    // first try cookie login
    $user = $this->makeInstance("\\booosta\\usersystem\\user");

    if(is_object($user) && $user->is_valid()):
      if($this->VAR['backpage']) $this->backpage = $this->base_dir . $this->VAR['backpage'];
      elseif($this->index) $this->backpage = $this->index;
      else $this->backpage = 'vendor/booosta/usersystem/exec/user.php';

      if($this->index) $this->backpage = $this->index; else $this->backpage = 'vendor/booosta/usersystem/exec/user.php';
      $this->TPL['AUTH_USER'] = $user->get_username();
      $this->maintpl = 'systpl/feedback.tpl';

      if(method_exists($this, 'after_login')) $this->after_login();
      if(method_exists($this, 'after_login_user')) $this->after_login_user();;
    else:   // cookie login failed - show username + password form
      $template_module = $this->config('template_module');
      $cfg_toptpl = $this->config('login_toptpl');

      if($template_module && $cfg_toptpl) $this->toptpl = "$this->base_dir/vendor/booosta/$template_module/$cfg_toptpl";
      elseif($template_module) $this->toptpl = "{$this->base_dir}vendor/booosta/$template_module/src/login.html";
      elseif($template_module) $this->toptpl = "$this->base_dir/vendor/booosta/$template_module/src/blank.html";
      elseif($cfg_toptpl) $this->toptpl = $this->base_dir . $cfg_toptpl;
      else $this->toptpl = $this->base_dir . 'vendor/booosta/bootstrap/src/blank.html';
      #\booosta\debug("toptpl: $this->toptpl");
      #b::debug(getcwd() . " $this->toptpl");

      $this->extra_templates = ['MAIN' => $this->usersystem_dir . "tpl/user_login.tpl"];
      if($this->config('allow_registration')) $this->TPL['registration_allowed'] = true;

      #\booosta\debug($_SESSION);
      if($_SESSION['login_failure']) $this->TPL['login_message'] = $this->t('Wrong username or password');
      else $this->TPL['login_message'] = $this->t('You are not logged in or your session has expired');

      unset($_SESSION['login_failure']);
    
      if($this->config('ask_login_token') === true || $this->config('ask_login_token_user') === true) $this->TPL['login_token'] = true;
      elseif($this->config('ask_login_token') == 'optional' || $this->config('ask_login_token_user') == 'optional') $this->TPL['login_token_optional'] = true;;
    endif;

    if(method_exists($this, 'after_show_loginform')) $this->after_show_loginform();
  }
}

$app = new App();
$app();
