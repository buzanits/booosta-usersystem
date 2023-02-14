<?php
namespace booosta\usersystem;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use booosta\Framework as b;
b::croot();
b::load();

class App extends Webappuser
{
  public $base_dir = '/';
  public $tpldir = 'vendor/booosta/usersystem/';
  public $translator_dir = 'vendor/booosta/usersystem';
  public $translator_merge = true;

  protected function action_default()
  {
    #\booosta\debug($this->user);
    if(!is_object($this->user)):
      $this->redirect('/');
    else:
      switch($this->user->get_user_type()):
        case 'adminuser':
          $dest = $this->script_extension == '.php' ? '/vendor/booosta/usersystem/exec/logout_adminuser.php' : '/logout_adminuser';
          $this->redirect($dest);
        break;
        case 'user':
          $dest = $this->script_extension == '.php' ? '/vendor/booosta/usersystem/exec/logout_user.php' : '/logout_user';
          $this->redirect($dest);
        break;
      endswitch;
    endif;
  }
}

$app = new App();
$app();
