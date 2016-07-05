<?php
require('cbsd.php');

if (isset($_POST['vm_os_type'])) {
	$vm_os_type = $_POST['vm_os_type'];
}

if (isset($_POST['jname'])) {
	$jname = $_POST['jname'];
}

if (isset($_POST['imgsize'])) {
	$imgsize = $_POST['imgsize'];
}

if (isset($_POST['vm_cpus'])) {
	$vm_cpus = $_POST['vm_cpus'];
}

if (isset($_POST['vm_ram'])) {
	$vm_ram = $_POST['vm_ram'];
}

if (isset($_POST['vm_authkey'])) {
	$vm_authkey = $_POST['vm_authkey'];
} else {
	$vm_authkey = "0";
}

if ((strlen($vm_os_type)<2)) {
	echo "No vm_os_type";
	die;
}

if ((strlen($jname)<2)) {
	echo "No jname";
	die;
}

if ((strlen($imgsize)<2)) {
	echo "No imgsize";
	die;
}

if ((strlen($vm_cpus)<1)) {
	echo "No vm_cpus";
	die;
}

if ((strlen($vm_ram)<1)) {
	echo "No vm_ram";
	die;
}

if ((strlen($vm_authkey)<2)) {
	$vm_authkey = "0";
}


echo "READY";
die;
$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd node inter=0 mode=add node=$address pw=$jname port=$vm_authkey", "r");
$read = fgets($handle, 4096);
echo "Job Queued: $read";
pclose($handle);
header( 'Location: nodes.php' ) ;
?>
