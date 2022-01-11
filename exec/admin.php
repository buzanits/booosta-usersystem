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

  protected function init()
  {
    parent::init();
    if($index = $this->config('index_admin')) $this->redirect("$this->base_dir$index");
  }
}

$app = new App();
$app->auth_user();
$app();
