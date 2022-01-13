<?php
namespace booosta\usersystem;

require_once __DIR__ . '/../../vendor/autoload.php';

use booosta\Framework as b;
b::croot();
b::load();

class App extends Webappuser
{
  protected $name = 'user';
  public $base_dir = '/';
  public $subtpldir = 'vendor/booosta/usersystem/exec/';
  public $translator_dir = 'vendor/booosta/usersystem';
  protected $translator_merge = true;

  protected function init()
  {
    parent::init();

    $this->privs['edit'] = 'edit self';
    $this->real_base_dir = $this->real_basedir();
    $this->leftmenu = $this->init_menu('local/leftmenudefinitionfile_user.php', 'tpl/leftmenu_user.tpl.php', 'left');
  }

  protected function action_default()
  {
    $this->id = $this->user->get_id();
    $this->action_edit();
    unset($_SESSION['login_token_backpage']);
  }

  protected function before_edit_($id, $data, $obj)
  {
    if($password = $data['password']):
      $cur_pw = $this->encrypt($data['curpassword']);
      $act_pw = $this->DB->query_value("select password from user where id=?", $id);
      #b::debug("cur_pw: $cur_pw, act_pw: $act_pw");
      if($act_pw && $cur_pw != $act_pw) $this->raise_error($this->t('Current password not correct'));

      $obj->set('password', $this->encrypt($password));
    endif;
  }

  protected function before_action_edit()
  {
    $this->maintpl = 'tpl/user_self.tpl';

    $check = $this->makeInstance('Formchecker');
    $check->add_equal_field('password', 'password1');

    $check->set_errormessage('equal_field', $this->t('The passwords must match'));
    $this->add_javascript($check);

    #$this->TPL['oldpassword'] = $this->decrypt($this->get_data('password'));
    #$this->add_jquery_ready('add_toggle_checkbox("pwd");');

    if(method_exists($this, 'show_tokenlinks_user')) $this->show_tokenlinks_user();
 
    // forced password change, if "changepassword" is set in usersettings
    $userobj = $this->getDataobject('user', $this->user_id);
    if($userobj->get('usersettings', 'changepassword')) $this->TPL['must_change_password'] = true;
  }

  protected function before_action_editdo()
  {
    if($this->VAR['password'] == ''):
      unset($this->VAR['password']);
    else:
      $userobj = $this->getDataobject('user', $this->user_id);
      if($userobj->get('usersettings', 'changepassword')):
        $userobj->set('usersettings', 'changepassword', null);
        $userobj->update();
      endif;
    endif;
  }

  protected function after_action_editdo()
  {
    $this->TPL['output'] = $this->t('Account edited successfully');
    $this->goback = false;
  }
}

$app = new App();
$app->auth_user();
$app();
