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
include_once($rp.'/webdev/db.php');

class Forms
{
	private $name='';
	private $db='';
	
	function __construct($name)
	{
		$this->name=$name;
		$this->db=new Db('helpers',$name);
	}
	
	function generate()
	{
		$query="select * from forms order by group_id asc, order_id asc";
		$fields=$this->db->select($query);
		echo '<form name="">';
		foreach($fields as $key=>$field)
		{
			$tpl=$this->getElement($field['type']);
			$params=array('param','desc','attr','cur');
			foreach($params as $param)
			{
				if(isset($field[$param]))
					$tpl=str_replace('${'.$param.'}',$field[$param],$tpl);
			}
			
			$value=$field['def'];
			if(isset($field['cur']) && !empty($field['cur'])) $value=$field['cur'];
			$tpl=str_replace('${value}',$value,$tpl);
			
			$required=($field['mandatory']==1)?' required':'';
			$tpl=str_replace('${required}',$required,$tpl);
			echo $tpl;
		}
		$this->setButtons();
		echo '</form>';
	}
	
	function getElement($el)
	{
		$tpl='';
		switch($el)
		{
			case 'inputbox':
				$tpl='<div class="form-field"><input type="text" name="${param}" value="${value}" ${attr}${required} /><span class="small">${desc}</span></div>';
				break;
			case 'delimer':
				$tpl='<h1>${desc}</h1>';
				break;
		}
		return $tpl;
	}
	
	function setButtons($arr=array())
	{
		echo '<div class="buttons"><input type="button" value="Apply" /> <input type="button" value="Cancel" /></div>';
	}
}

if ($mode=="install") {
	echo "INSTALL";
#	$res=cbsd_cmd('env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb autoflush=2 mode=new env NOCOLOR=1 /usr/local/bin/cbsd imghelper module=$hlper jname=$jname inter=0');
	$res=cbsd_cmd("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new env NOCOLOR=1 /usr/local/bin/cbsd imghelper module=$helper jname=$jname inter=0");

	exit(0);
}


$jail_form=$workdir."/jails-system/".$jname."/helpers/".$helper.".sqlite";

if (file_exists($jail_form)) {
	$form=new Forms($helper);
	$form->generate();
	//$form->setButtons(array('apply','cancel'));
} else {
	echo "Module not installed for $jname. Please <a href='/img_helper_cfg.php?jname=$jname&mode=install&helper=$helper'>install module</a>";
}