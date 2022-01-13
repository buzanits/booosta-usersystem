<?php
namespace booosta\usersystem;

class Admin_Privileges extends \booosta\db_privileges\DB_Privileges
{
  protected $user_privilege_table = 'adminuser_privilege';
  protected $user_role_table = 'adminuser_role';
}
