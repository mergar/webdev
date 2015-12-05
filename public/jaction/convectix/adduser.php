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
	<?php
	require('path.inc.php');
	require('get_next_clientid.php');
	require('pwgen.php');
	$cl_id=find_next_client_id();
	$next_client=$client_prefix.$cl_id;
	$client_db=$next_client."_db";
	$master_passwd=pwgen(15);
	$mysql_passwd=pwgen(15);
	?>

	<h2>Add hosting user</h2>
	<form action="adduser-fromargs.php" method="post" onsubmit="return checkForm(this);">
	<div class="main">
    
		<div class="field">
	    		<label for="firstname">First name:</label>
	    		<input type="text" name="firstname" value="" size="15"/>
		</div>
    
		<div class="field">
	    		<label for="lastname">Last name:</label>
	    		<input type="text" name="lastname" value="" size="15"/>
		</div>

		<div class="field">
	    		<label for="mail">Mail:</label>
	    		<input type="text" name="mail" value="" size="25"/>
		</div>
    
		<div class="field">
	    		<label for="shell">Shell:</label>
	    		<input type="text" name="shell" value="/bin/csh" size="15"/>
		</div>

		<div class="field">
			<label for="login">sftp/ssh login:</label>
	    		<input type="text" name="login" value="<?php echo "$next_client" ?>" size="15"/>
		</div>

		<div class="field">
			<label for="masterpass">sftp/ssh password:</label>
			<input type="text" name="masterpass" value="<?php echo "$master_passwd" ?>" size="15"/>
		</div>

		<div class="field">
			<label for="mysqldb">primary mysql db:</label>
			<input type="text" name="mysqldb" value="<?php echo "$client_db" ?>" size="15"/>
		</div>

		<div class="field">
			<label for="mysqlpw">primary mysql pw:</label>
			<input type="text" name="mysqlpw" value="<?php echo "$mysql_passwd" ?>" size="15"/>
		</div>
		
		<div class="field">
			<label for="ip4_addr">jail IP4,IP6 addr:</label>
			<input type="text" name="ip4_addr" value="DHCP" size="15"/>
		</div>
		
		<div class="field">
			<label for="jimport">jail name for import from:</label>
			<input type="text" name="jimport" value="" size="15"/>
		</div>
		
		<div class="field">
			<label for="host_hostname">FQDN for hostname:</label>
			<input type="text" name="host_hostname" value="wordpress.my.domain" size="25"/>
		</div>

		<input type="submit" name="create" value="Create User" >
<!-- 		<button onclick="goBack()">Go Back</button> -->
		<a href="/index.html">Main</a>
	</div>
	</form>
	<div class="main">
	</div>
</body>
</html>
