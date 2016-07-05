<?php
require('cbsd.php');
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CBSD Project</title>
	
	<style>
		body {font-size:80%;font-family:Tahoma,'Sans-Serif',Arial;}
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
	<form action="bcreate-fromargs.php" method="post">
	<div class="main">
		<div class="field">
			<label for="vm_os_type">VM OS Type</label>
			<select>
				<option>UbuntuBSD</option>
				<option>FreeBSD</option>
			</select>
			<input type="text" name="vm_os_type" value="freebsd" readonly/>
		</div>

		<div class="field">
			<label for="jname">VM name</label>
			<input type="text" name="jname" />
		</div>
		<div class="field">
			<label for="imgsize">Image size</label>
			<input type="text" name="imgsize" value="10g"/>
		</div>
		<div class="field">
			<label for="vm_cpus">VM CPUs</label>
			<input type="range" name="vm_cpus" min="0" max="16">
			<input type="submit">
		</div>

		<div class="field">
			<label for="vm_ram">VM RAM</label>
			<input type="text" name="vm_ram" value="1g"/>
		</div>

		<input type="submit" name="create" value="Create VM" >
	</div>
</form>
</body>
</html>
