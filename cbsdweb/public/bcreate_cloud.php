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
        </style>
</head>
<body>
<a href="blist.php">[ << Bhyve VMs ]</a>
<hr>

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

<?php
$dbfilepath="/var/db/webdev/authkey.sqlite";

$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
$authkeyres = $db->query('SELECT idx,name,authkey FROM authkey;');

$authkey_list="";

if (!($authkeyres instanceof Sqlite3Result)) {
        echo "Error: $dbfilepath";
        $gwinfo="unable to fetch authkey";
} else {
        while ($row = $authkeyres->fetchArray()) {
                list( $idx , $name, $authkey ) = $row;
$authkey_list .= <<<EOF
<option value="$name">$name</option>
EOF;
	}
}
?>

<body>
	<form action="bcreate_cloud-fromargs.php" method="post">
	<div class="main">
		<div class="field">
			<label for="vm_os_type">VM OS Type</label>
			<select name="vm_os_type">
				<option selected value="linux">Linux Ubuntu 16.04</option>
				<option value="freebsd">FreeBSD 11.0-RELEASE</option>
			</select>
		</div>
		<div class="field">
			<label for="jname">VM name</label>
			<input type="text" name="jname" />
		</div>
		<div class="field">
			<label for="vm_size">Image size, GB:</label>
			<div style="display:table-cell;">
				<input type="text" value="5" style="width:35px;text-align:center;" id="vm_size_view" />
				<input type="range" step="5" value="5" name="vm_size" id="vm_size" min="5" max="40" onmousemove="$('#vm_size_view').val($(this).val());" style="vertical-align:middle;" />
			</div>
		</div>
		<div class="field">
			<label for="vm_ram">VM RAM size, GB:</label>
			<div style="display:table-cell;">
				<input type="text" value="1" style="width:35px;text-align:center;" id="vm_ram_view" />
				<input type="range" name="vm_ram" value="1" id="vm_ram" min="1" max="16" onmousemove="$('#vm_ram_view').val($(this).val());" style="vertical-align:middle;" />
			</div>
		</div>
		<div class="field">
			<label for="vm_cpus">VM CPU, num:</label>
			<input type="number" name="vm_cpus" min="1" max="8" step="1" value="1">
		</div>
		<div class="field">
			<label for="ip4_addr">VM IP4 address</label>
			<input type="text" name="ip4_addr" value="DHCP"/>
		</div>
		<div class="field">
			<label for="vm_authkey">Authkey:</label>
			<select name="vm_authkey">
<?php
echo $authkey_list;
?>
			</select>
		</div>
		<input type="submit" name="create" value="Create VM" >
	</div>
</form>
</body>
</html>
