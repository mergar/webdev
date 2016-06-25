<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CBSD Project</title>
	<link type="text/css" href="./css/all.css" rel="stylesheet" />
	<style>
		body {
			font-size:14px;
		}
	</style>
</head>
<body>
<a href="javascript:location.reload(true)">[ Refresh Page ]</a> | <a href="bcreate.php">[ New VM ]</a>
<script>
</script>


<?php
function show_bhyvevm($nodelist="local")
{
	global $workdir;
	
	$pieces = explode(" ", $nodelist);

	?>

	<table class="images">
		<thead>
			<tr>
				<th>node</th>
				<th>vm</th>
				<th>vm_ram</th>
				<th>vm_cpus</th>
				<th>vm_os_type</th>
				<th>status</th>
				<th>action</th>
				<th>remove</th>
			</tr>
		</thead>
		<tbody>
	<?php
	
	foreach ($pieces as $nodename) {
		if (!$nodename) {
			$nodename=$nodelist;
		}
		$db = new SQLite3("$workdir/var/db/$nodename.sqlite"); $db->busyTimeout(5000);
		$sql = "SELECT jname,vm_ram,vm_cpus,vm_os_type FROM bhyve;";
		$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
		$row = array();
		$i = 0;

		if ( $nodename != "local" ) {
			$nodeip=get_node_info($nodename,"ip");
			$idle=check_locktime($nodeip);
		} else {
			$idle=1;
		}
		
		if ($idle == 0 ) {
			$hdr = '<tr style="background-color:#D6D2D0">';
		} else {
			$hdr = '<tr>';
		}

		while($res = $result->fetchArray(SQLITE3_ASSOC)){
			if(!isset($res['jname'])) continue;
			$jname = $res['jname'];
			$vm_ram = $res['vm_ram'] / 1024 / 1024 ;
			$vm_cpus = $res['vm_cpus'];
			$vm_os_type = $res['vm_os_type'];
			$status=check_vmonline($jname);
			$i++;

			if ( $idle != 0 ) {
				switch ($status) {
				case 0:
					//off
					$statuscolor="#EDECEA";
					$action="<form action=\"bstart.php\" method=\"post\"><input type=\"hidden\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"start\" value=\"Start\"></form>";
					break;
				case 1:
					//running
					$statuscolor="#51FF5F";
					$action="<form action=\"bstop.php\" method=\"post\"><input type=\"hidden\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"stop\" value=\"Stop\"></form>";
					break;
				default:
					$action="maintenance";
					break;
				}
			} else {
				$statuscolor="#D6D2D0";
				$action="offline";
			}
	
			if ( $idle != 0 ) {
				$status_td="<td><a href=\"bstatus.php?jname=$jname\">$jname</a></td>";
				$remove_td="<td><a href=\"bremove.php?jname=$jname\">Remove</a></td>";
			} else {
				$status_td="<td>$jname</td>";
				$remove_td="<td>Remove</td>";
			}

			$str = <<<EOF
			${hdr}
			<td><strong>$nodename</strong></td>
			${status_td}
			<td>$vm_ram</td><td>$vm_cpus</td>
			<td>$vm_os_type</td>
			<td style="background-color:$statuscolor">$status</td>
			<td>$action</td>
			${remove_td}
			</tr>
EOF;
	echo $str;
		}
	}
	echo "</tbody></table>";
}

// MAIN
require('cbsd.php');
require('nodes.inc.php');

$db = new SQLite3("$workdir/var/db/nodes.sqlite"); $db->busyTimeout(5000);
$sql = "SELECT nodename FROM nodelist";
$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
$row = array();
$i = 0;

$nodelist="local";

while($res = $result->fetchArray(SQLITE3_ASSOC)){
	if(!isset($res['nodename'])) continue;
	$nodelist=$nodelist." ".$res['nodename'];
}

show_bhyvevm($nodelist);
