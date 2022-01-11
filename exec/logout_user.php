<?php
namespace booosta\usersystem;

require_once __DIR__ . '/../../vendor/autoload.php';

use booosta\Framework as b;
b::croot();
b::load();


class App extends Webappuser
{
  #protected $usersystem_dir = '';
  public $base_dir = '/';
  public $tpldir = 'vendor/booosta/usersystem/';
  public $translator_dir = 'vendor/booosta/usersystem';
  protected $translator_merge = true;

  protected function action_default()
  {
    if(method_exists($this, 'before_logout')) $this->before_logout();
    if(method_exists($this, 'before_logout_user')) $this->before_logout_user();

    if($this->user && is_callable([$this->user, 'after_logout'])) $this->user->after_logout();
    $this->user = null;
    session_destroy();
    if($this->index) $this->backpage = $this->index; else $this->backpage = 'user.php';
    #if($this->index) $this->backpage = $this->base_dir . $this->index; else $this->backpage = 'user.php';
    $this->maintpl = \booosta\webapp\FEEDBACK;

    if(method_exists($this, 'after_logout')) $this->after_logout();
    if(method_exists($this, 'after_logout_user')) $this->after_logout_user();
  }
}

$app = new App();
$app();
