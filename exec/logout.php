<?php
namespace booosta\usersystem;

include '../../chroot.php';
require_once __DIR__ . '/vendor/autoload.php';

use booosta\Framework as b;
b::load();

class App extends Webappuser
{
  public $base_dir = '../../../';
  public $tpldir = 'vendor/booosta/usersystem/';
  public $translator_dir = 'vendor/booosta/usersystem';
  public $translator_merge = true;

  protected function action_default()
  {
    #\booosta\debug($this->user);
    switch($this->user->get_user_type()):
      case 'adminuser':
        $this->redirect('/vendor/booosta/usersystem/logout_adminuser.php');
      break;
      case 'user':
        $this->redirect('/vendor/booosta/usersystem/logout_user.php');
      break;
      case 'publicuser':
        $this->redirect('/vendor/booosta/usersystem/logout_publicuser.php');
      break;
    endswitch;
  }
}

$app = new App();
$app();
