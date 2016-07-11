<?php
require_once("auth.php");
?>


<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CBSD Project</title>
	<link type="text/css" href="./css/all.css" rel="stylesheet" />
	<style>
		body {font-size:80%;font-family:Tahoma,'Sans-Serif',Arial;}
		thead {color:green;}
		tbody {color:blue;}
	</style>
</head>
<body>
<a href="javascript:location.reload(true)">[ Refresh Page ]</a>
<script>
</script>

<?php
$rp=realpath('');
include_once($rp.'/cbsd_cmd.php');
require('cbsd.php');
?>

<table class="images">
 <thead>
  <tr>
   <th></th>
   <th>instance</th>
   <th>description</th>
  </tr>
 </thead>
<tbody align="center">

<?php

$res=cbsd_cmd('env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd imghelper header=0');

if ($res['retval'] != 0 ) {
	if (!empty($res['error_message']))
		echo $res['error_message'];
		exit(1);
}

$lst=explode("\n",$res['message']);
$n=0;

$str="";

if(!empty($lst)) foreach($lst as $item) {

	$dbfilepath=$workdir."/formfile/".$item.".sqlite";

	$longdesc="unable to fetch desc";

	if (file_exists($dbfilepath)) {

		$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
	        $helpers = $db->query('SELECT longdesc FROM system');

	        if (!($helpers instanceof Sqlite3Result)) {
	                echo "Error: $dbfilepath";
	        } else {
	                while ($row = $helpers->fetchArray()) {
	                        list( $longdesc ) = $row;
	    		}
			$db->close();
	    	}
	}

	if (file_exists("img/logo/$item.svg")) {
		$imgsrc="img/logo/$item.svg";
	} else {
		$imgsrc="img/logo/empty.svg";
	}


	$str .= <<<EOF
 <tr>
 <td>
	<a href="/helper.php?helper=$item"><img src="$imgsrc" width="200" height="100" alt="$item.svg"></a>
 </td>
 <td>
	<a href="/helper.php?helper=$item">$item</a>
 </td>
 <td>
	$longdesc
 </td>
 </tr>
EOF;
}

$str .= <<<EOF
 </tbody>
</table>
EOF;

echo $str;
echo "\n";
