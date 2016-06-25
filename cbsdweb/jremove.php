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

if (!isset($_GET['jname'])) {
	echo "Empty jname";
	exit(0);
}

if (isset($_GET['sure'])) {
	$sure=1;
} else {
	$sure=0;
}

$jname=$_GET['jname'];

if ($sure==0) {
	$str = <<<EOF
<script type="text/javascript">
<!--

var answer = confirm("Really remove $jname jail?")
if (!answer)
window.location="jlist.php"
else
window.location="jremove.php?jname=$jname&sure=1"
// -->
</script>
EOF;
	echo $str;
	exit(0);
}

//$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb autoflush=2 mode=new env NOCOLOR=1 /usr/local/bin/cbsd jremove inter=0 jname=$jname", 'r');
$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd jremove inter=0 jname=$jname", 'r');
$read = fgets($handle, 4096);
echo "Job Queued: $read";
pclose($handle);
header( 'Location: jlist.php' ) ;
?>
