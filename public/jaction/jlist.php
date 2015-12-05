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
<a href="javascript:location.reload(true)">[ Refresh Page ]</a> | <a href="jcreate.php">[ New Jail ]</a>


<?php
function show_jails($nodelist="local")
{
	global $workdir;
	
	$pieces = explode(" ", $nodelist);

	?>
	<table class="images">
		<thead>
		<tr>
			<th>node</th>
			<th>jname</th>
			<th>ip4_addr</th>
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
		if (!$db) return;
		$sql = "SELECT jname,ip4_addr,status FROM jails WHERE emulator != \"bhyve\";";
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
			$ip4_addr = $res['ip4_addr'];
			$status = $res['status'];
			$i++;

			if ( $idle != 0 ) {
				switch ($status) {
				case 0:
					//off
					$statuscolor="#EDECEA";
					$action="<form action=\"jstart.php\" method=\"post\"><input type=\"hidden\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"start\" value=\"Start\"></form>";
					break;
				case 1:
					//running
					$statuscolor="#51FF5F";
					$action="<form action=\"jstop.php\" method=\"post\"><input type=\"hidden\" name=\"jname\" value=\"$jname\"/> <input type=\"submit\" name=\"stop\" value=\"Stop\"></form>";
					break;
				default:
					$statuscolor="#D6D2D0";
					$action="maintenance";
					break;
				}
			} else {
				$statuscolor="#D6D2D0";
				$action="offline";
			}

			
			if ( $idle != 0 ) {
				$status_td="<td><a href=\"jstatus.php?jname=$jname\">$jname</a></td>";
				$remove_td="<td><a href=\"jremove.php?jname=$jname\">Remove</a></td>";
			} else {
				$status_td="<td>$jname</td>";
				$remove_td="<td>Remove</td>";
			}
			
			$str = <<<EOF
				${hdr}
				<td><strong>$nodename</strong></td>
				${status_td}
				<td>$ip4_addr</td>
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

show_jails($nodelist);