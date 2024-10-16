<?php
namespace booosta\usersystem;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use booosta\Framework as b;
b::croot();
b::load();


class App extends Webappuser
{
  protected $name = 'user';
  #protected $usersystem_dir = '';
  public $base_dir = '/';
  #public $tpldir = 'vendor/booosta/usersystem/';
  public $subtpldir = 'vendor/booosta/usersystem/exec/';
  public $translator_dir = 'vendor/booosta/usersystem/exec';
  protected $translator_merge = true;
  protected $cfg_toptpl = 'blank.html';

  protected $urlhandler_action_paramlist = ['confirm' => 'action/username/token', 'resetpasswordconfirm' => 'action/username/token'];


  protected function action_default()
  {
    #b::debug($_SERVER);
    $this->maintpl = 'tpl/register.tpl';
    $this->extra_templates['LEFT'] = 'systpl/empty.tpl';
    if(!$this->config('allow_registration')) $this->raise_error($this->t('Registration not allowed'), 'user.php');

    $check = $this->makeInstance('Formchecker');
    $check->add_equal_field('password', 'password1');
    $check->add_regexp_field('username', '/^([A-Za-z0-9.@_-])+$/');
    $check->add_email_field('email');

    $check->set_errormessage('regexp_field', $this->t('username may only contain A-Z a-z 0-9 . @ _ -'));
    $check->set_errormessage('equal_field', $this->t('The passwords must match'));
    $check->set_errormessage('email_field', $this->t('The email address is incorrect'));

    $this->add_javascript($check);

    $ajax = $this->makeInstance('Ajax', 'username');
    $this->add_javascript($ajax, true);
    $prot = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
    $this->TPL['ajaxurl'] = "$prot://{$_SERVER['SERVER_NAME']}/user_register/checkusername";

    $this->TPL['username_taken'] = $this->t('username already taken');
    $this->generate_form_token();
  }

  protected function action_checkusername()
  {
    $found = $this->DB->query_value("select count(*) from user where username='{$this->VAR['username']}'");
    #$found = $this->authenicator->username_exists($this->VAR['username'], 'user');
    if($found) $result = 'duplicate'; else $result = 'ok';

    \booosta\ajax\Ajax::print_response('result', $result);
    $this->no_output = true;
  }

  protected function action_registerdo()
  {
    $this->extra_templates['LEFT'] = 'systpl/empty.tpl';
    if(!$this->config('allow_registration')) $this->raise_error($this->t('Registration not allowed'));

    $this->action_newdo();

    $this->goback = false;
    if($this->index) $this->backpage = $this->base_dir . $this->index; else $this->backpage = '/user';

    $this->TPL['output'] = $this->t('Register success');
  }

  protected function before_add_($data, $obj)
  {
    $exists = $this->DB->query_value('select count(*) from user where username=?', $data['username']);
    if($exists) $this->raise_error($this->t('This username already exists'));

    $password = $data['password'];

    $obj->set('password', $this->encrypt($password));
    $obj->set('active', 1);
    $obj->set('usersettings', ['email' => $data['email']]);

    if($this->config('ask_login_token') === true || $this->config('ask_login_token_user') === true):
      $obj->set('token', $this->generate_user_token());
    endif;

    if($this->config('confirm_registration')):
      $obj->set('active', 0);
      $token = md5(uniqid());
      $obj->set('usersettings', ['email' => $data['email'], 'token' => $token]);

      $prot = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
      $text = $this->t('You have been registered as') . " {$data['username']}.<br>\n<br>\n" . 
$this->t('Click on the following link to complete your registration') . ":<br>\n
<a href='$prot://{$_SERVER['SERVER_NAME']}/user_register/confirm/{$data['username']}/$token'>
$prot://{$_SERVER['SERVER_NAME']}/user_register/confirm/{$data['username']}/$token</a><br>\n<br>\n";
#$this->t('Click on the following link to complete your registration') . ":<br>\n
#<a href='http://{$_SERVER['SERVER_NAME']}$this->phpself?action=confirm&username={$data['username']}&token=$token'>
#http://{$_SERVER['SERVER_NAME']}$this->phpself?action=confirm&username={$data['username']}&token=$token</a><br>\n<br>\n";

      $subject = $this->t('Your registration with') . ' ' . $this->config('site_name_text') ?: $this->config('site_name');
      $mailer = $this->makeInstance('Email', $this->config('mail_sender'), $data['email'], $subject, $text);
      if($this->config('mail_backend') == 'smtp'):
        $mailer->set_smtp_params($this->config('mail_smtp_params'));
        $mailer->set_backend('smtp');
      endif;

      $mailer->send();
    endif;
  }

  protected function after_add($data, $newid)
  {
    $ur = $this->makeDataobject('user_role');
    $ur->set('user', $newid);
    $ur->set('role', $this->DB->query_value("select id from role where name='Customer'"));
    $ur->insert();
  }

  protected function action_confirm()
  {
    $obj = $this->getDataobject('user', "username='{$this->VAR['username']}'");
    #print 'obj: '; print_r($obj);
    if(is_object($obj) && $obj->get('id')):
      $token = $obj->get('usersettings', 'token');

      if($token == $this->VAR['token']):
        $obj->set('active', 1);
        $obj->del('usersettings', 'token');
        $obj->update();

        $message = $this->t('Your account has been successully confirmed. You can now login with your username and password.');
      else:
        $message = $this->t('Wrong token or username.');
      endif;
    else:
      $message = "username {$this->VAR['username']} not valid.";
    endif;

    $this->maintpl = \booosta\webapp\FEEDBACK;
    $this->goback = false;
    if($this->index) $this->backpage = $this->base_dir . $this->index; else $this->backpage = '/user';
    $this->TPL['output'] = $message;
    $this->extra_templates['LEFT'] = \booosta\webapp\FEEDBACK;
  }
      
  protected function action_resetpassword()
  {
    $this->extra_templates['LEFT'] = 'systpl/empty.tpl';
    $this->maintpl = 'tpl/resetpassword.tpl';
  }

  protected function action_resetpassworddo()
  {
    $this->extra_templates['LEFT'] = 'systpl/empty.tpl';

    $obj = $this->getDataobject('user', "username='{$this->VAR['username']}'");
    if(is_object($obj) && $obj->get('id')):
      $email = $obj->get('usersettings', 'email');
    else:
      $error = true;
    endif;

    if($error || filter_var($email, FILTER_VALIDATE_EMAIL) === false)
      $this->raise_error($this->t('username or Email not found'), '/user_register/resetpassword');

    $token = md5(uniqid());
    $obj->set('usersettings', 'pwdtoken', $token);
    $obj->update();

    $prot = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
    $text = $this->t('Your password can be reset using this link') . ":<br>\n
<a href='$prot://{$_SERVER['SERVER_NAME']}/user_register/resetpasswordconfirm/{$this->VAR['username']}/$token'>
$prot://{$_SERVER['SERVER_NAME']}/user_register/resetpasswordconfirm/{$this->VAR['username']}/$token</a><br>\n<br>\n";

    $subject = $this->config('site_name') . ' ' . $this->t('password reset');
    $mailer = $this->makeInstance('Email', $this->config('mail_sender'), $email, $subject, $text);
    if($this->config('mail_backend') == 'smtp'):
      $mailer->set_smtp_params($this->config('mail_smtp_params'));
      $mailer->set_backend('smtp');
    endif;

    $mailer->send();

    $this->maintpl = \booosta\webapp\FEEDBACK;
    $this->TPL['output'] = $this->t('You will receive an email with a link to the password reset page');
    $this->goback = false;
    $this->backpage = '/user';
  }

  protected function action_resetpasswordconfirm()
  {
    $this->extra_templates['LEFT'] = 'systpl/empty.tpl';
    $this->pass_vars_to_template('username,token');

    $obj = $this->getDataobject('user', "username='{$this->VAR['username']}'");
    if(is_object($obj) && $obj->get('id')):
      $token = $obj->get('usersettings', 'pwdtoken');

      if($token == $this->VAR['token']):
        $this->maintpl = 'tpl/setpassword.tpl';

        $check = $this->makeInstance('Formchecker');
        $check->add_equal_field('password', 'password1');
        $check->set_errormessage('equal_field', $this->t('The passwords must match'));
        $this->add_javascript($check, true);  // last param: add <script> tags

        return;
      endif;
    endif;

    $this->raise_error($this->t('Wrong token or username.'), '/user');
  }

  protected function action_setpassword()
  {
    $this->extra_templates['LEFT'] = 'systpl/empty.tpl';
    $this->maintpl = \booosta\webapp\FEEDBACK;

    $obj = $this->getDataobject('user', "username='{$this->VAR['username']}'");
    if(is_object($obj) && $obj->get('id')):
      $token = $obj->get('usersettings', 'pwdtoken');

      if($token == $this->VAR['token']):
        $password = $this->VAR['password'];
        $obj->set('password', $this->encrypt($password));
        $obj->del('usersettings', 'pwdtoken');

        $obj->update();

        $this->TPL['output'] = $this->t('Your password has been reset. You can now login in with the new password');
        $this->goback = false;
        $this->backpage = '/user';
        return;
      endif;
    endif;

    $this->raise_error($this->t('Wrong token or username.'), '/user');
  }
}

$app = new App();
$app();
