<?php
require_once("auth.php");
?>


<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CBSD Project</title>
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
	<link type="text/css" href="./css/all.css" rel="stylesheet" />
</head>
<body>

<?php
$rp=realpath('');
include_once($rp.'/cbsd_cmd.php');
require('cbsd.php');

$rp=realpath('');
include_once($rp.'/webdev/db.php');

function forms( $dbfilepath, $helper )
{
	$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);

	$query="SELECT idx,group_id,order_id,param,desc,def,cur,new,mandatory,attr,xattr,type FROM forms ORDER BY group_id ASC, order_id ASC";

	$fields = $db->query($query);

	?>

	<form class="helperbox" name="<?php echo $helper; ?>" id="<?php echo $helper; ?>" action="helper.php?helper=<?php echo $helper; ?>" method="POST">

	<label for="jname">jname</label>
	<input type="text" name="newjname" required /><br>

	<label for="ip4_addr">ip4_addr</label>
	<input type="text" name="ip4_addr" required value="DHCP" /><br>

	<?php

	while ($row = $fields->fetchArray()) {

		list( $idx , $group_id, $order_id , $param , $desc , $def , $cur , $new , $mandatory , $attr , $xattr , $type ) = $row;

		$tpl=getElement($type);

		$params=array('param','desc','attr','cur');

		foreach($params as $param)
		{
			if($param) {
//				$tpl=str_replace('${'.$param.'}',$param,$tpl);
				$tpl=str_replace('${'.$param.'}',$$param,$tpl);
//				$tpl=str_replace('${param}',$$param,$tpl);
			}
		}

//		$tpl=str_replace('${param}',$$param,$tpl);
		$tpl=str_replace('${param}',"HOHO",$tpl);

		$value=$def;
		if(isset($cur) && !empty($cur)) $value=$cur;
		$tpl=str_replace('${def}',$value,$tpl);

		$required=($mandatory==1)?' required':'';
		$tpl=str_replace('${required}',$required,$tpl);
		echo $tpl;
	}

	?>
	<input type="submit" value="Apply"/>
	<br>
	</form>
	<?php
}

function getElement($el)
{
	$tpl='';

	switch($el)
	{
		case 'inputbox':
			$tpl .= '"${desc}" : <input type="text" name="${param}" value="${def}" ${attr}${required} /><br>';
			break;
		case 'delimer':
			$tpl .= '<h1>${desc}</h1>';
			break;
	}
	return $tpl;
}


function setButtons($arr=array())
{
	echo '<div class="buttons"><input type="button" value="Apply" /> <input type="button" value="Cancel" /></div>';
}


if (!isset($_GET['helper'])) {
        echo "Empty helper";
        exit(0);
} else {
        $helper=$_GET['helper'];
}

$jail_form=$workdir."/formfile/".$helper.".sqlite";

if (!file_exists($jail_form)) {
	echo "No such module $helper at $jail_form";
	die();
}

if (isset($_POST['newjname'])) {
	$newjname = $_POST['newjname'];

	$db = new SQLite3($jail_form); $db->busyTimeout(5000);
	$query="SELECT param FROM forms";
	$fields = $db->query($query);

	while ($row = $fields->fetchArray()) {
		list( $param ) = $row;

		echo "$param";
		echo "<br>";

		if (isset($_POST["$param"])) {
			echo $_POST["$param"];
			echo "<br>";
		}
//		echo $param;
	}
	exit(0);
}

$jail_form=$workdir."/formfile/".$helper.".sqlite";
forms( $jail_form, "redis" );
