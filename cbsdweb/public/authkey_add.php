<?php
require_once("auth.php");
?>


<?php
require('cbsd.php');

$authkey = $_POST['authkey'];
$authkey_name = $_POST['authkey_name'];

if (empty($authkey)) {
	echo "No such authkey";
	exit;
}

if (empty($authkey_name)) {
	echo "No such authkey_name";
	exit;
}

//echo "OK: $authkey and $authkey_name";

$dbfilepath="/var/db/webdev/authkey.sqlite";

$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
//$db->exec('PRAGMA journal_mode = wal;');

//$query="insert into authkey (name,authkey) ('{$authkey_name}','{$authkey}');";

//echo "$query";

$db->exec("INSERT INTO authkey (name, authkey) VALUES ('{$authkey_name}','{$authkey}')");

//$db->exec($query);
$db->close();


echo "OK: $authkey and $authkey_name added";

header( 'Location: authkey.php' ) ;
?>
