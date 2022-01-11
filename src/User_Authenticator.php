<?php
namespace booosta\usersystem;

class User_Authenticator extends \booosta\db_authenticator\DB_Authenticator
{
  protected $user_table = 'user';
  protected $password_encrypted = 'true';
}
