<?php
require_once __DIR__ . '/vendor/autoload.php';

use booosta\Framework as b;
b::load();

if(php_sapi_name() != 'cli') exit(1);

if($argv[1] == '') $argv[1] = 'admin';
if($argv[2] == '') {$argv[2] = $argv[1]; $argv[1] = 'admin'; }

$crypter = new \booosta\aes256\AES256();
$pwd = $crypter->encrypt($argv[2]);

$db = new booosta\database\DB();
$db->query("update adminuser set password='$pwd' where username='$argv[1]'");

print "Password updated.\n";
