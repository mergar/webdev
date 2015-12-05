<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Remove user</title>

	<style type="text/css">
		label {float:left; padding-right:10px;}
		.field {clear:both; text-align:right; line-height:25px;}
		.main,.checkbox-list {float:left;}
		.s_checkbox {width:10px; height:10px}
		body {
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

//	$login = $_POST['login'];

	//echo $login;
	
	foreach ($_POST as $key => $value) {
		if (!strcmp($key,"remove")) continue;
			$cmd="cp $currentuserdir/$key $savedir/$key.del";
			echo "queued: $cmd";
			system($cmd);
			echo "<br>";
	}
	?>
	<button onclick="goBack()">Go Back</button>
</body>
</html>
