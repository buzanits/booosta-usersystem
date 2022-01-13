<?php
namespace booosta\usersystem;

class Admin_Authenticator extends \booosta\db_authenticator\DB_Authenticator
{
  protected $user_table = 'adminuser';
  protected $password_encrypted = 'true';
}
