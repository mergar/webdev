<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>CBSD Project</title>
        <link type="text/css" href="./css/all.css" rel="stylesheet" />
	<style>
		body {
			background-image: url("/img/adm_bg.jpg");
			background-color: #84CF99;
			font-size:14px;
		}
	</style>
</head>
<body>

<?php
require('cbsd.php');

if (!isset($_POST['jname'])) {
	echo "Empty jname";
	exit(0);
}

$jname=$_POST['jname'];

$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd jstop inter=0 jname=$jname", 'r');
echo "'$handle'; " . gettype($handle) . "\n";
$read = fread($handle, 2096);
echo $read;
pclose($handle);

header( 'Location: jlist.php' ) ;
?>
