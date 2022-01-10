<?php
namespace booosta\usersystem;

include '../../chroot.php';
require_once __DIR__ . '/vendor/autoload.php';

use booosta\Framework as b;
b::load();

class App extends Webappadmin
{
  protected $name = 'adminuser';
  #protected $usersystem_dir = '';
  public $base_dir = '../../../';
  public $tpldir = 'vendor/booosta/usersystem/';
  public $translator_dir = 'vendor/booosta/usersystem';
  protected $translator_merge = true;
  protected $blank_fields = 'password';

  #protected $header = 'Username,Comment,Edit,Delete';
  protected $use_datatable = true;
  protected $use_subtablelink = false;

  protected function init()
  {
    parent::init();
    
    $this->show_fields('username,comment,edit,delete');
    $this->set_header(array('username' => $this->t('username'), 'comment' => $this->t('comment')));
    #$this->auth_actions = true;
  }

  protected function after_action_new()
  {
    $check = $this->makeInstance('Formchecker');
    $check->add_equal_field('password', 'password1');
    $check->add_required_field('username');
    $check->add_required_field('password');
    $this->add_javascript($check);
  }

  protected function before_add_($data, $obj)
  {
    if($data['username'] == '' || $data['password'] == '') $this->raise_error('Username or password missing');
    $password = $data['password'];

    $obj->set('password', $this->encrypt($password));

    $obj->set('active', 1);
    
    if($this->config('ask_login_token') === true || $this->config('ask_login_token_admin') === true):
      $obj->set('token', $this->generate_user_token());
    endif;
  }

  protected function after_add_($data, $newid)
  {
    $privileges = $this->makeInstance("\\booosta\\usersystem\\Admin_Privileges");
    $privileges->add_user_privilege($newid, 'edit self');
  }

  protected function before_action_edit()
  {
    #$check = $this->makeInstance('Formchecker');
    #$check->add_equal_field('password', 'password1');
    #$this->add_javascript($check);

    $this->pass_vars_to_template('object_id');

    $privileges = $this->makeInstance("\\booosta\\usersystem\\Admin_Privileges");
    $act_privs = array_keys($privileges->get_user_privileges($this->id));
    $sel = $this->makeInstance('ui_select', 'privileges', $this->get_opts_from_table('privilege'), $act_privs);
    $sel->set_type('tags');
    $this->TPL['sel_privileges'] = $sel->get_html();

    $act_roles = array_keys($privileges->get_user_roles($this->id));
    $sel = $this->makeInstance('ui_select', 'roles', $this->get_opts_from_table('role'), $act_roles);
    $sel->set_type('tags');
    $this->TPL['sel_roles'] = $sel->get_html();

    $this->show_tokenlinks($this->id);
    $_SESSION['login_token_backpage'] = "{$this->base_dir}vendor/booosta/usersystem/admin_adminuser.php?action=edit&object_id=$this->id";
  }

  protected function before_action_editdo()
  {
    if($this->VAR['password'] == '') unset($this->VAR['password']);
  }

  protected function before_edit_($id, $data, $obj)
  {
    if($password = $data['password']):
      $obj->set('password', $this->encrypt($password));
    endif;

    $obj->set('active', $data['active'] ? 1 : 0);
  }

  protected function after_edit_($id, $data)
  {
    $privileges = $this->makeInstance("\\booosta\\usersystem\\Admin_Privileges");

    $privileges->set_user_privileges($this->id, $this->VAR['privileges']);
    $privileges->set_user_roles($this->id, $this->VAR['roles']);
  }
}

$app = new App();
$app->auth_user();
$app();
