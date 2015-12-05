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
	
	<style type="text/css">
		body {font-size:14px;}
		label {float:left; padding-right:10px;}
		.field {clear:both; text-align:right; line-height:25px;}
		.main,.checkbox-list {float:left;}
		.s_checkbox {width:10px; height:10px}
	</style>
	
	<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>

	<script type="text/javascript">
	$(function(){
		$('input[type="checkbox"]').click(function(){
			if ( $( this ).hasClass( "s_checkbox" ) ) {
			    if($(this).is(':checked')){
				    $('input[name="sysrc_enable"]').val($('input[name="sysrc_enable"]').val()+$(this).attr('name')+' ');
			    }else{
				    $('input[name="sysrc_enable"]').val($('input[name="sysrc_enable"]').val().replace($(this).attr('name')+' ',''));
			    }
			}
		});
	});
	</script>
	
	
</head>
<body>
	<form action="jcreate-fromargs.php" method="post">
	<div class="main">
		<div class="field">
			<label for="jname">Jname</label>
			<input type="text" name="jname" />
		</div>
		<div class="field">
			<label for="fqdn">Fqdn</label>
			<input type="text" name="host_hostname" />
		</div>
		<div class="field">
			<label for="ip4_addr">Ipv4_addr</label>
			<input type="text" name="ip4_addr" value="DHCP"/>
		</div>
		<div class="field">
			<label for="interface">Interface</label>
			<input type="text" name="interface" value="auto" />
		</div>
		<div class="field">
			<!-- <label for="mount_devfs">mount_devfs</label> -->
			<input type="hidden" name="mount_devfs" value="1"/>
		</div>
		<div class="field">
			<!-- <label for="arch">arch</label> -->
			<input type="hidden" name="arch" value="native"/>
		</div>
		<div class="field">
			<!-- <label for="mkhostfile">mkhostfile</label> -->
			<input type="hidden" name="mkhostfile" value="1"/>
		</div>
		<div class="field">
			<!-- <label for="arch">devfs_ruleset</label> -->
			<input type="hidden" name="devfs_ruleset" value="4"/>
		</div>
		<div class="field">
			<!-- <label for="ver">ver</label> -->
			<input type="hidden" name="ver" value="native"/>
		<div class="field">
			<label for="baserw">Base writable?</label>
			<input type="checkbox" name="baserw" value="baserw"/>
		</div>
		<div class="field">
			<!-- <label for="mount_src">mount_src</label> -->
			<input type="hidden" name="mount_src" value="0"/>
		</div>
		<div class="field">
			<!-- <label for="mount_obj">mount_obj</label> -->
			<input type="hidden" name="mount_obj" value="0"/>
		</div>
		<div class="field">
			<!-- <label for="mount_kernel">mount_kernel</label> -->
			<input type="hidden" name="mount_kernel" value="0"/>
		</div>
		<div class="field">
			<label for="mount_ports">Mount /usr/ports?</label>
			<input type="checkbox" name="mount_ports" value="mount_ports"/>
		</div>
		<div class="field">
			<label for="astart">Autostart:</label>
			<input type="checkbox" name="astart" value="astart"/>
		</div>

		<div class="field">
			<!-- <label for="vnet">vnet</label> -->
			<input type="hidden" name="vnet" value="0"/>
		</div>
		<div class="field">
			<!-- <label for="applytpl">applytpl</label> -->
			<input type="hidden" name="applytpl" value="1"/>
		</div>
		<div class="field">
			<!-- <label for="floatresolv">floatresolv</label> -->
			<input type="hidden" name="floatresolv" value="1"/>
		</div>
		<div class="field">
			<label for="user_pw_root">Root password</label>
			<input type="text" name="user_pw_root" />
		</div>
		<br>
		<div class="field">
			<label for="user_add">Unprivileged User</label>
			<input type="text" name="user_add" />
		</div>
		<div class="field">
			<label for="user_add_gecos">Gecos (Full Name)</label>
			<input type="text" name="user_add_gecos" />
		</div>
		<div class="field">
			<label for="user_add_password">User Password</label>
			<input type="text" name="user_add_password" />
		</div>
		<hr>
		<div class="field">
			<label for="sysrc_enable">Enabled services:</label>
 			<input type="text" readonly="readonly" name="sysrc_enable" value=""/>
			<?php include 'srvlist.shtml' ?>
		</div>
		<input type="submit" name="create" value="Create Jail" >
	</div>
</form>
</body>
</html>
