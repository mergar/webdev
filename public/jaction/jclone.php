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
function show_descr()
{



}


function traffic_stats()
{


}



// MAIN
require('cbsd.php');

if (!isset($_GET['jname'])) {
	echo "Empty jname";
	exit(0);
} else {
	$jname=$_GET['jname'];
}

require_once('jail_menu.php');
jail_menu();
show_descr();
traffic_stats();


?>
