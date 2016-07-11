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
   <th>ver</th>
   <th>update</th>
   <th>remove</th>
  </tr>
 </thead>
<tbody align="center">

<?php

$res=cbsd_cmd('env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd srcls header=0');

if ($res['retval'] != 0 ) {
	if (!empty($res['error_message']))
		echo $res['error_message'];
		exit(1);
}

$lst=explode("\n",$res['message']);
$n=0;

$str="";

if(!empty($lst)) foreach($lst as $item) {

	$msg = preg_replace('/\s\s+/', ' ',$item);

	list($src, $ver ) = explode(' ', $msg );

	if (!isset($ver)) $ver="default";
	$str .= <<<EOF
 <tr>
  <td>$ver</td>
  <td><a href="srcup.php?mode=update&ver=$ver">>>srcup</a></td>
  <td><a href="srcup.php?mode=remove&ver=$ver">remove</a></td>
 </tr>
EOF;
}

$str .= <<<EOF
 </tbody>
</table>
EOF;

echo $str;
echo "\n";
