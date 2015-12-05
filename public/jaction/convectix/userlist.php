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

<table class="users">
	<form action="removeuser.php" method="post">
	<thead>
	<tr>
		<th>login</th>
		<th>name</th>
		<th>email</th>
		<th>remove</th>
	</tr>
	</thead><tbody>
	<?php
	require('path.inc.php');
	if ($handle = opendir($currentuserdir)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				echo "<tr><td>$entry</td><td>Name</td><td>Email</td><td><input type=\"checkbox\" class=\"s_checkbox\" name=\"$entry\" value=\"$entry\"></td></tr>";
			}
		}
		closedir($handle);
	}
	?>
	</tbody></table>
	<input type="submit" name="remove" value="Remove" >
	</form>
</body>
</html>
