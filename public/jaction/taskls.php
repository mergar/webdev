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
<a href="javascript:location.reload(true)">[ Refresh Page ]</a> | <a href="taskls.php?flushlog=true">[ Flush Log ]</a>

<?php
// clean all log records in taskdb
function flush_log()
{
	$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task mode=flushall", "r");
	$read = fgets($handle, 4096);
	pclose($handle);
	header( 'Location: taskls.php' ) ;
}

// just show all log where owner is cbsdweb
function show_logs()
{
	global $workdir;
	
	$db = new SQLite3("$workdir/var/db/cbsdtaskd.sqlite"); $db->busyTimeout(5000);
	$sql = "SELECT id,st_time,end_time,cmd,status,errcode,logfile FROM taskd WHERE owner='cbsdweb' ORDER BY id DESC;";
	$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
	$row = array();
	$i = 0;
	?>
	<table class="images">
	<thead>
	<tr>
		<th>id</th>
		<th>cmd</th>
		<th>start time</th>
		<th>end time</th>
		<th>status</th>
		<th>errcode</th>
		<th>logfile</th>
		<th>logsize</th>
	</tr>
	</thead><tbody>
	<?php
	while($res = $result->fetchArray(SQLITE3_ASSOC)){
		if(!isset($res['id'])) continue;
		$id = $res['id'];
		$cmd = $res['cmd'];
		$start_time = $res['st_time'];
		$end_time = $res['end_time'];
		$status = $res['status'];
		$errcode = $res['errcode'];
		$logfile = $res['logfile'];

		if (file_exists($logfile)) {
			$tmplogsize=filesize($logfile);
			$logsize=human_filesize($tmplogsize,0);
		} else {
			$logsize=0;
		}
		$i++;

		switch ($status) {
		case 0:
			//pending
			$hdr = '<tr style="background-color:#51FF5F">';
			break;
		case 1:
			//in progress
			$hdr = '<tr style="background-color:#F3FF05">';
			break;
		case 2:
			//complete
			switch ($errcode) {
			case 0:
				$hdr = '<tr style="background-color:#EDECEA">';
				break;
			default:
				//errcode not 0
				$hdr = '<tr style="background-color:#FFA7A1">';
				break;
			}
			break;
		}

		$s_time=date("Y-M-d H:i", strtotime($start_time));
		$e_time=date("Y-M-d H:i", strtotime($end_time));

		if ( $logsize!= 0 ) {
			$logfiletd="<td><a href=\"showtasklog.php?log=$logfile\">$logfile</a></td>";
		} else {
			$logfiletd="<td>$logfile</td>";
		}

		$str = <<<EOF
			<td>$id</td>
			<td>$cmd</td>
			<td>$s_time</td>
			<td>$e_time</td>
			<td>$status</td>
			<td>$errcode</td>
			$logfiletd
			<td>$logsize</td>
			</tr>
EOF;
		echo $hdr.$str;
	}
	echo "</tbody></table>";
}

// MAIN

require('cbsd.php');
if (isset($_GET['flushlog'])) {
	flush_log();
}

show_logs();

