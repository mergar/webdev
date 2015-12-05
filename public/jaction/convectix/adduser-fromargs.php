<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Add user</title>

	<style type="text/css">
		label {float:left; padding-right:10px;}
		.field {clear:both; text-align:right; line-height:25px;}
		.main,.checkbox-list {float:left;}
		.s_checkbox {width:10px; height:10px}
		body {
			background-image: url("/img/adm_bg.jpg");
			background-color: #84CF99;
			font-size:14px;
	}
	</style>

	<script>
		function goBack(){
			window.history.back()
		}
	</script>

	<script type="text/javascript">
		function checkForm(form)
		{
			if(!form) return false;
			var inps=form.getElementsByTagName('input');
			for(n=0,nl=inps.length;n<nl;n++)
			{
				var inp=inps[n];
				if(inp.value=='')
				{
					alert('You must fill all fields!');
					inp.focus();
					return false;
				}
			}
			return true;
		}
	</script>
</head>
<body>
	<?php
	require('path.inc.php');

	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$mail = $_POST['mail'];
	$shell = $_POST['shell'];
	$login = $_POST['login'];
	$masterpass = $_POST['masterpass'];
	$mysqldb = $_POST['mysqldb'];
	$mysqlpw = $_POST['mysqlpw'];
	$ip4_addr = $_POST['ip4_addr'];
	$jimport = $_POST['jimport'];
	$host_hostname = $_POST['host_hostname'];

$str = <<<EOF
firstname="$firstname"
lastname="$lastname"
mail="$mail"
shell="$shell"
login="$login"
masterpass="$masterpass"
mysqldb="$mysqldb"
mysqlpw="$mysqlpw"
ip4_addr="$ip4_addr"
jimport="$jimport"
host_hostname="$host_hostname"
EOF;

	$fp = fopen("$savedir/$login.add", "w+");
	if (!$fp) {
		echo "Error fopen $savedir/$login.add for write";
		exit(0);
	}
	if (!fwrite($fp,$str)) echo "Error write to $savedir/$login.add";
	fclose($fp);
	echo "Stored in $savedir/$login.add";
	?>
	<button onclick="goBack()">Go Back</button>
</body>
</html>
