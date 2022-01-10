<?php
namespace booosta\usersystem;

include '../../chroot.php';
require_once __DIR__ . '/vendor/autoload.php';

use booosta\Framework as b;
b::load();

class App extends Webappadmin
{
  #protected $usersystem_dir = '';
  public $base_dir = '../../../';
  public $tpldir = 'vendor/booosta/usersystem/';
  public $translator_dir = 'vendor/booosta/usersystem';
  public $translator_merge = true;

  protected function action_default()
  {
    if(method_exists($this, 'before_login')) $this->before_login();
    if(method_exists($this, 'before_login_admin')) $this->before_login_admin();

    $param = [];
    $ask_login_token = $this->config('ask_login_token') ?? $this->config('ask_login_token_admin');
    if($ask_login_token) $param['login_param_1'] = $this->VAR['token'];

    if($this->VAR['rememberme']) $param['store_logincookie'] = true;

    $user = $this->makeInstance("\\booosta\\usersystem\\adminuser", $this->VAR['username'], $this->VAR['password'], $param);

    if($this->VAR['backpage']) $this->backpage = $this->base_dir . $this->VAR['backpage'];
    elseif($this->index) $this->backpage = $this->index;
    #elseif($this->index) $this->backpage = $this->base_dir . $this->index;
    else $this->backpage = 'vendor/booosta/usersystem/adminuser.php';

    if($this->index) $this->backpage = $this->index; else $this->backpage = 'vendor/booosta/usersystem/admin.php';
    #if($this->index) $this->backpage = $this->base_dir . $this->index; else $this->backpage = 'vendor/booosta/usersystem/admin.php';
    $this->TPL['AUTH_USER'] = $user->get_username();
    $this->maintpl = 'systpl/feedback.tpl';

    if(!is_object($user) || !$user->is_valid()) $this->auth_user();

    if(method_exists($this, 'after_login')) $this->after_login();
    if(method_exists($this, 'after_login_admin')) $this->after_login_admin();
  }
}

$app = new App();
$app();
