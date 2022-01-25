<?php
namespace booosta\usersystem;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use booosta\Framework as b;
b::croot();
b::load();


class App extends Webappuser
{
  #protected $usersystem_dir = '';
  public $base_dir = '/';
  public $tpldir = 'vendor/booosta/usersystem/';
  public $translator_dir = 'vendor/booosta/usersystem';
  public $translator_merge = true;

  protected function action_default()
  {
    if(method_exists($this, 'before_login')) $this->before_login();
    if(method_exists($this, 'before_login_user')) $this->before_login_user();

    $param = [];
    $ask_login_token = $this->config('ask_login_token') ?? $this->config('ask_login_token_user');
    if($ask_login_token) $param['login_param_1'] = $this->VAR['token'];

    if($this->VAR['rememberme']) $param['store_logincookie'] = true;

    $user = $this->makeInstance("\\booosta\\usersystem\\user", $this->VAR['username'], $this->VAR['password'], $param);

    if($this->VAR['backpage']) $this->backpage = $this->base_dir . $this->VAR['backpage'];
    elseif($this->index) $this->backpage = $this->index; 
    #elseif($this->index) $this->backpage = $this->base_dir . $this->index; 
    elseif($this->script_extension == '.php') $this->backpage = 'vendor/booosta/usersystem/user.php';
    else $this->backpage = '/user';
    #b::debug("backpage: $this->backpage");

    $this->TPL['AUTH_USER'] = $user->get_username();
    $this->user_id = $user->get_id();
    $this->maintpl = \booosta\webapp\FEEDBACK;

    if(!is_object($user) || !$user->is_valid()) $this->auth_user();

    if(method_exists($this, 'after_login')) $this->after_login();
    if(method_exists($this, 'after_login_user')) $this->after_login_user();
  }
}

$app = new App();
$app();
