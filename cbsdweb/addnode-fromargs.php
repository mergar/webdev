<?php
require('cbsd.php');

if (isset($_POST['address'])) {
	$address = $_POST['address'];
}

if (isset($_POST['password'])) {
	$password = $_POST['password'];
}

if (isset($_POST['sshport'])) {
	$sshport = $_POST['sshport'];
} else {
	$sshport = 22222;
}

if ((strlen($address)<2)) {
	echo "No address";
	die;
}

if ((strlen($password)<2)) {
	echo "No password";
	die;
}

if ((strlen($sshport)<2)) {
	echo "No sshport";
	die;
}

$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd node inter=0 mode=add node=$address pw=$password port=$sshport", "r");
$read = fgets($handle, 4096);
echo "Job Queued: $read";
pclose($handle);
header( 'Location: nodes.php' ) ;
?>
