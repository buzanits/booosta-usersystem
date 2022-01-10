<?php
namespace booosta\usersystem;

\booosta\Framework::add_module_trait('webapp', 'usersystem\webapp');

trait Webapp
{
  protected $user_class, $user;
  protected $loginscript;
  protected $usersystem_dir = 'vendor/booosta/usersystem/';

  protected function autorun_usersystem()
  {
    if($this->TPL === null) $this->TPL = [];
    $this->TPL['usersystem_dir'] = $this->usersystem_dir;
    if(isset($_SESSION['AUTH_USER'])) $this->user = unserialize($_SESSION['AUTH_USER']);
    if(is_object($this->user)):
      $this->user->parentobj = $this;
      $this->user->topobj = $this->topobj;
      if(is_object($this->user->authenticator)):
        $this->user->authenticator->parentobj = $this->user;
        $this->user->authenticator->topobj = $this->topobj;
      endif;

      $this->TPL['AUTH_USER'] = $this->user->get_username();
      $this->user_id = $this->user->get_id();
    endif;
  }

  public function auth_user($user_class = null)
  {
    if($user_class === null) $user_class = $this->user_class;
    $success = (is_object($this->user) && get_class($this->user) == "booosta\\usersystem\\$user_class" && $this->user->is_valid());

    #\booosta\debug($this->user);
    #\booosta\debug(get_class($this->user));
    #\booosta\debug($user_class);
    #\booosta\debug("success: $success");
    #\booosta\debug("loginscript in trait: $this->loginscript");

    if($success) return true;

    if($this->config('DEBUG_MODE') && $this->runs_on_cli) return true;

    $loginscript = $this->loginscript ? $this->loginscript : 'index.php';
    header("Location: $loginscript");
    exit;
  }

  public function get_user() { return $this->user; }
}
