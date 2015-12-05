<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CBSD Project</title>
	<link type="text/css" href="./css/all.css" rel="stylesheet" />
	<style>
		body {
			font-size:14px;
		}
	</style>
</head>
<body>

<?php
require('cbsd.php');

if (!isset($_GET['log'])) {
	echo "Empty log filename";
	exit(0);
} else {
	$logfile=$_GET['log'];
}

$fp = fopen($logfile, 'r');
if ($fp) {
	echo "<pre>";
	while (($buffer = fgets($fp, 4096)) !== false) {
		echo $buffer;
	}
	echo "</pre>";
} else {
	echo "Cant't open: $logfile";
}