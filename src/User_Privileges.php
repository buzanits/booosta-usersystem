<?php
namespace booosta\usersystem;

class User_Privileges extends \booosta\db_privileges\DB_Privileges
{
  protected $user_privilege_table = 'user_privilege';
  protected $user_role_table = 'user_role';
}
