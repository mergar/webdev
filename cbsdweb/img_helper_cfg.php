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

if (!isset($_GET['jname'])) {
	echo "Empty jname";
	exit(0);
}

if (!isset($_GET['helper'])) {
	echo "Empty helper";
	exit(0);
}

$jname=$_GET['jname'];
$helper=$_GET['helper'];

if (isset($_GET['mode'])) {
	$mode=$_GET['mode'];
} else {
	$mode="";
}

require_once('imghelper_menu.php');
jail_menu();

$rp=realpath('');
include_once($rp.'/webdev/db.php');

function forms( $dbfilepath ) 
{
	$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
	
	$query="SELECT idx,group_id,order_id,param,desc,def,cur,new,mandatory,attr,xattr,type FROM forms ORDER BY group_id ASC, order_id ASC";

	$fields = $db->query($query);

	echo '<form name="">';

	while ($row = $fields->fetchArray()) {

		list( $idx , $group_id, $order_id , $param , $desc , $def , $cur , $new , $mandatory , $attr , $xattr , $type ) = $row;

		$tpl=getElement($type, $desc);

		$params=array('param','desc','attr','cur');

		if(isset($cur) && !empty($cur)) $def=$cur;
			$tpl=str_replace('${def}',$def,$tpl);
			
		$required=($mandatory==1)?' required':'';
		$tpl=str_replace('${required}',$required,$tpl);
		echo $tpl;
	}

	echo '</form>';
}
	
function getElement($el, $desc)
{
	$tpl='';

	switch($el)
	{
		case 'inputbox':
			$tpl .= $desc . ":" . '<input type="text" name="${param}" value="${def}" ${attr}${required} /><br>';
			break;
		case 'delimer':
			$tpl .= "<h1>${desc}</h1>";
			break;
	}
	return $tpl;
}

	
function setButtons($arr=array())
{
	echo '<div class="buttons"><input type="button" value="Apply" /> <input type="button" value="Cancel" /></div>';
}


if ($mode=="install") {
	echo "INSTALL";
	$res=cbsd_cmd("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new env NOCOLOR=1 /usr/local/bin/cbsd imghelper module=$helper jname=$jname inter=0");

	exit(0);
}


$jail_form=$workdir."/jails-system/".$jname."/helpers/".$helper.".sqlite";

if (file_exists($jail_form)) {
//	$form=new Forms($helper);
//	$form->generate();
	//$form->setButtons(array('apply','cancel'));
	forms( $jail_form );
} else {
	echo "Module not installed for $jname. Please <a href='/img_helper_cfg.php?jname=$jname&mode=install&helper=$helper'>install module</a>";
}
