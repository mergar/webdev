<?php
require('cbsd.php');
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CBSD Project</title>
	
	<style>
		body {
			font-size:14px;
		}
	</style>
	
</head>
<body>
	<form action="addnode-fromargs.php" method="post">
	<div class="main">
		<div class="field">
			<label for="address">Node IP or Hostname:</label>
			<input type="text" name="address" />
		</div>
		<div class="field">
			<label for="password">CBSD User Password:</label>
			<input type="text" name="password" />
		</div>
		<div class="field">
			<label for="sshport">SSH Port:</label>
			<input type="text" name="sshport" value="22222" maxlength="5" size="5" />
		</div>
		<p><input type="submit" name="create" value="Connect" ></p>
	</div>
</form>
</body>
</html>
