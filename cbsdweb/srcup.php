<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CBSD Project</title>
	<link type="text/css" href="./css/all.css" rel="stylesheet" />
	<style>
		body {
			font-size:14px;
		}
		h1 {color:white;background:silver;margin:0;padding:10px;}
		.small {font-size:x-small;}
		.form-field {padding:4px 10px 0 10px;margin:0 4px; background:#fafafa;}
		.form-field span {margin-left:10px;}
		.form-field input {width:300px;}
		form {border:1px solid gray;padding:0;margin-bottom:10px;width:500px;border-radius:8px;overflow:hidden;box-shadow:4px 4px 6px rgba(0,0,0,0.2);}
		.buttons {padding:20px 10px;text-align:center;}
	</style>
</head>
<body>

<?php
$rp=realpath('');
include_once($rp.'/cbsd_cmd.php');
require('cbsd.php');

if (!isset($_GET['mode'])) {
	echo "Empty mode";
	exit(0);
}

if (!isset($_GET['ver'])) {
	echo "Empty ver";
	exit(0);
}

$ver=$_GET['ver'];
$mode=$_GET['mode'];

$rp=realpath('');
include_once($rp.'/webdev/db.php');

if ($ver=="default") {
	$myver="";
} else {
	$myver="ver=$ver";
}

if ($mode=="remove") {
	echo "Remove..";
	$res=cbsd_cmd("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new env NOCOLOR=1 /usr/local/bin/cbsd removesrc $myver");
	exit(0);
}

if ($mode=="update") {
	echo "Update...";
	$res=cbsd_cmd("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new env NOCOLOR=1 /usr/local/bin/cbsd srcup $myver");
	exit(0);
}
