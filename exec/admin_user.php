<?php
namespace booosta\usersystem;

require_once __DIR__ . '/../../vendor/autoload.php';

use booosta\Framework as b;
b::croot();
b::load();

class App extends Webappadmin
{
  protected $name = 'user';
  public $base_dir = '/';
  public $subtpldir = 'vendor/booosta/usersystem/exec/';
  public $translator_dir = 'vendor/booosta/usersystem';
  protected $translator_merge = true;

  protected $fields = 'username,edit,delete';
  protected $header = 'Username';
  protected $blank_fields = 'password';
  protected $checkbox_fields = 'active';
  protected $use_datatable = true;
  #protected $ui_modal_cancelpage = 'admin_user.php';


  protected function before_add_($data, $obj)
  {
    if($data['username'] == '' || $data['password'] == '') $this->raise_error('Username or password missing');
    $password = $data['password'];

    $obj->set('password', $this->encrypt($password));

    $obj->set('usersettings', 'email', $data['email']);
    $obj->set('active', 1);
    
    if($this->config('ask_login_token') === true || $this->config('ask_login_token_user') === true):
      $obj->set('token', $this->generate_user_token());
    endif;
  }

  protected function after_add_($data, $newid)
  {
    $privileges = $this->makeInstance("\\booosta\\usersystem\\User_Privileges");
    $privileges->add_user_privilege($newid, 'edit self');
    if($this->edit_params) $this->backpage = "admin_user$this->edit_params$newid";
    else $this->backpage = "vendor/booosta/usersystem/exec/admin_user.php?action=edit&object_id=$newid";
  }

  protected function before_edit_($id, $data, $obj)
  {
    if($password = $data['password']):
      $obj->set('password', $this->encrypt($password));
    endif;

    $obj->set('usersettings', $data['data']);
    $obj->set('active', $data['active'] ? 1 : 0);
    #\booosta\debug($obj->get_data());
  }

  protected function before_action_edit()
  {
    $check = $this->makeInstance('Formchecker');
    $check->add_equal_field('password', 'password1');
    $this->add_javascript($check);

    $this->pass_vars_to_template('object_id');

    $privileges = $this->makeInstance("\\booosta\\usersystem\\User_Privileges");
    $act_privs = array_keys($privileges->get_user_privileges($this->id));
    $sel = $this->makeInstance('ui_select', 'privileges', $this->get_opts_from_table('privilege'), $act_privs);
    $sel->set_type('tags');
    $this->TPL['sel_privileges'] = $sel->get_html();

    $act_roles = array_keys($privileges->get_user_roles($this->id));
    $sel = $this->makeInstance('ui_select', 'roles', $this->get_opts_from_table('role'), $act_roles);
    $sel->set_type('tags');
    $this->TPL['sel_roles'] = $sel->get_html();

    $this->TPL = array_merge($this->TPL, $this->get_data('usersettings'));
    
    if(method_exists($this, 'show_tokenlinks')) $this->show_tokenlinks($this->id, 'user');
    $_SESSION['login_token_backpage'] = "{$this->base_dir}vendor/booosta/usersystem/exec/admin_user.php?action=edit&object_id=$this->id";

    $this->TPL['forcepw_link'] = $this->config('urlhandlermode') ? "admin_user/forcepwchange/$this->id" : "admin_user.php?action=forcepwchange&user_id=$this->id";
  }

  protected function before_action_editdo()
  {
    if($this->VAR['password'] == '') unset($this->VAR['password']);
  }

  protected function after_edit_($id, $data)
  {
    $privileges = $this->makeInstance("\\booosta\\usersystem\\User_Privileges");

    #\booosta\debug($this->VAR);
    $privileges->set_user_privileges($this->id, $this->VAR['privileges']);
    $privileges->set_user_roles($this->id, $this->VAR['roles']);
  }
  
  protected function action_forcepwchange()
  {
    $userobj = $this->getDataobject('user', $this->VAR['user_id']);
    if($userobj->set('usersettings', 'changepassword', true));
    $userobj->update();

    $this->maintpl = \booosta\webapp\FEEDBACK;
    $this->TPL['output'] = "User will be forced to change password after next login";
    $this->backpage = "admin_user.php?action=edit&object_id={$this->VAR['user_id']}";
    $this->goback = false;
  }
}

if(is_readable('local/classes_user.php')) include_once 'local/classes_user.php';
if(class_exists("\\booosta\\usersystem\\App1")) $app = new App1(); else $app = new App();
$app->auth_user();
$app();
