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

<?php
require('cbsd.php');

if (!isset($_GET['jname'])) {
	"Empty jname";
	exit(0);
}

$jname=$_GET['jname'];
$db = new SQLite3("$workdir/var/db/inv.home.my.domain.sqlite"); $db->busyTimeout(5000);

$sql = "SELECT 
host_hostname,
ip4_addr,
mount_devfs,
allow_mount,
allow_devfs,
allow_nullfs,
mkhostsfile, 
devfs_ruleset,
interface, 
baserw, 
mount_src, 
mount_kernel, 
mount_ports, 
astart, 
vnet, 
applytpl,
floatresolv,
exec_start, 
exec_stop, 
exec_poststart,
exec_poststop, 
exec_prestart, 
exec_prestop, 
exec_master_poststart,
exec_master_poststop, 
exec_master_prestart, 
exec_master_prestop, 
exec_timeout, 
exec_fib,
stop_timeout,
mount_fdescfs,
allow_procfs,
allow_tmpfs,
allow_zfs,
cpuset, 
exec_consolelog,
status
FROM jails WHERE jname=\"$jname\"";

$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
$row = array();
$i = 0;

while($res = $result->fetchArray(SQLITE3_ASSOC)){
	$host_hostname = $res['host_hostname'];
	$ip4_addr = $res['ip4_addr'];
	$mount_devfs = $res['mount_devfs'];
	$allow_mount = $res['allow_mount'];
	$allow_devfs = $res['allow_devfs'];
	$allow_nullfs = $res['allow_nullfs'];
	$mkhostsfile = $res['mkhostsfile'];
	$devfs_ruleset = $res['devfs_ruleset'];
	$interface = $res['interface'];
	$baserw = $res['baserw'];
	$mount_src = $res['mount_src'];
	$mount_kernel = $res['mount_kernel'];
	$mount_ports = $res['mount_ports'];
	$astart = $res['astart'];
	$vnet = $res['vnet'];
	$applytpl = $res['applytpl'];
	$floatresolv = $res['floatresolv'];
	$exec_start = $res['exec_start'];
	$exec_stop = $res['exec_stop'];
	$exec_poststart = $res['exec_poststart'];
	$exec_poststop = $res['exec_poststop'];
	$exec_prestart = $res['exec_prestart'];
	$exec_prestop = $res['exec_prestop'];
	$exec_master_poststart = $res['exec_master_poststart'];
	$exec_master_poststop = $res['exec_master_poststop'];
	$exec_master_prestart = $res['exec_master_prestart'];
	$exec_master_prestop = $res['exec_master_prestop'];
	$exec_timeout = $res['exec_timeout'];
	$exec_fib = $res['exec_fib'];
	$stop_timeout = $res['stop_timeout'];
	$mount_fdescfs = $res['mount_fdescfs'];
	$allow_procfs = $res['allow_procfs'];
	$allow_tmpfs = $res['allow_tmpfs'];
	$allow_zfs = $res['allow_zfs'];
	$cpuset = $res['cpuset'];
	$exec_consolelog = $res['exec_consolelog'];
	$status = $res['status'];
?>

<div class="main">
<form action="jset.php" method="post">
<?php

if ($status==0) {
$str = <<<EOF
<div class="field">
<input type="hidden" name="jname" value="$jname"/>
</div>

<div class="field">
<label for="host_hostname">Hostname (FQDN):</label>
<input type="text" name="host_hostname" value="$host_hostname"/>
</div>

<div class="field">
<label for="ip4_addr">IP address:</label>
<input type="text" name="ip4_addr" value="$ip4_addr"/>
</div>

<div class="field">
<label for="devfs_ruleset">devfs_ruleset:</label>
<input type="text" name="devfs_ruleset" value="$devfs_ruleset"/>
</div>

<div class="field">
<label for="interface">interface:</label>
<input type="text" name="interface" value="$interface"/>
</div>

<!--

<div class="field">
<label for="exec_start">exec_start:</label>
<input type="text" name="exec_start" value="$exec_start"/>
</div>

<div class="field">
<label for="exec_stop">exec_stop:</label>
<input type="text" name="exec_stop" value="$exec_stop"/>
</div>

<div class="field">
<label for="exec_poststart">exec_poststart:</label>
<input type="text" name="exec_poststart" value="$exec_poststart"/>
</div>

<div class="field">
<label for="exec_poststop">exec_poststop:</label>
<input type="text" name="exec_poststop" value="$exec_poststop"/>
</div>

<div class="field">
<label for="exec_prestart">exec_prestart:</label>
<input type="text" name="exec_prestart" value="$exec_prestart"/>
</div>

<div class="field">
<label for="exec_prestop">exec_prestop:</label>
<input type="text" name="exec_prestop" value="$exec_prestop"/>
</div>

<div class="field">
<label for="exec_master_poststart">exec_master_poststart:</label>
<input type="text" name="exec_master_poststart" value="$exec_master_poststart"/>
</div>

<div class="field">
<label for="exec_master_poststop">exec_master_poststop:</label>
<input type="text" name="exec_master_poststop" value="$exec_master_poststop"/>
</div>

<div class="field">
<label for="exec_master_prestart">exec_master_prestart:</label>
<input type="text" name="exec_master_prestart" value="$exec_master_prestart"/>
</div>

<div class="field">
<label for="exec_master_prestop">exec_master_prestop:</label>
<input type="text" name="exec_master_prestop" value="$exec_master_prestop"/>
</div>

--!>

<div class="field">
<label for="exec_timeout">exec_timeout:</label>
<input type="text" name="exec_timeout" value="$exec_timeout"/>
</div>

<div class="field">
<label for="exec_fib">exec_fib:</label>
<input type="text" name="exec_fib" value="$exec_fib"/>
</div>

<div class="field">
<label for="stop_timeout">stop_timeout:</label>
<input type="text" name="stop_timeout" value="$stop_timeout"/>
</div>

<div class="field">
<label for="cpuset">cpuset:</label>
<input type="text" name="cpuset" value="$cpuset"/>
</div>

<div class="field">
<label for="exec_consolelog">exec_consolelog:</label>
<input type="text" name="exec_consolelog" value="$exec_consolelog"/>
</div>

<div class="field">
<label for="mount_src">mount_src:</label>
<input type="checkbox" name="mount_src" value="mount_src"/>
</div>

<div class="field">
<label for="mount_kernel">mount_kernel:</label>
<input type="checkbox" name="mount_kernel" value="mount_kernel"/>
</div>

<div class="field">
<label for="mount_ports">mount_ports:</label>
<input type="checkbox" name="mount_ports" value="mount_ports"/>
</div>

<div class="field">
<label for="astart">astart:</label>
<input type="checkbox" name="astart" value="astart"/>
</div>

<div class="field">
<label for="baserw">baserw:</label>
<input type="checkbox" name="baserw" value="baserw"/>
</div>

<div class="field">
<label for="mount_devfs">mount_devfs:</label>
<input type="checkbox" name="mount_devfs" value="mount_devfs"/>
</div>

<div class="field">
<label for="allow_mount">allow_mount:</label>
<input type="checkbox" name="allow_mount" value="allow_mount"/>
</div>

<div class="field">
<label for="allow_devfs">allow_devfs:</label>
<input type="checkbox" name="allow_devfs" value="allow_devfs"/>
</div>

<div class="field">
<label for="allow_nullfs">allow_nullfs:</label>
<input type="checkbox" name="allow_nullfs" value="allow_nullfs"/>
</div>

<div class="field">
<label for="mkhostsfile">mkhostsfile:</label>
<input type="checkbox" name="mkhostsfile" value="mkhostsfile"/>
</div>

<div class="field">
<label for="vnet">vnet:</label>
<input type="checkbox" name="vnet" value="$vnet"/>
</div>

<div class="field">
<label for="applytpl">applytpl:</label>
<input type="checkbox" name="applytpl" value="$applytpl"/>
</div>

<div class="field">
<label for="floatresolv">floatresolv:</label>
<input type="checkbox" name="floatresolv" value="floatresolv"/>
</div>

<div class="field">
<label for="mount_fdescfs">mount_fdescfs:</label>
<input type="checkbox" name="mount_fdescfs" value="mount_fdescfs"/>
</div>

<div class="field">
<label for="allow_procfs">allow_procfs:</label>
<input type="checkbox" name="allow_procfs" value="allow_procfs"/>
</div>

<div class="field">
<label for="allow_tmpfs">allow_tmpfs:</label>
<input type="checkbox" name="allow_tmpfs" value="$allow_tmpfs"/>
</div>

<div class="field">
<label for="allow_zfs">allow_zfs:</label>
<input type="checkbox" name="allow_zfs" value="$allow_zfs"/>
</div>
EOF;
} else if ($status==1) {
$str = <<<EOF
<strong>Jail in ONLINE. Only restricted number of parameters can be set.</strong>

<div class="field">
<input type="hidden" name="jname" value="$jname"/>
</div>

<div class="field">
<label for="host_hostname">Hostname (FQDN):</label>
<input type="text" readonly="readonly" name="nop" value="$host_hostname"/>
</div>

<div class="field">
<label for="ip4_addr">IP address:</label>
<input type="text" name="ip4_addr" value="$ip4_addr"/>
</div>

<div class="field">
<label for="devfs_ruleset">devfs_ruleset:</label>
<input type="text" name="nop" readonly="readonly" value="$devfs_ruleset"/>
</div>

<div class="field">
<label for="interface">interface:</label>
<input type="text" name="nop" readonly="readonly" value="$interface"/>
</div>

<div class="field">
<label for="exec_start">exec_start:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_start"/>
</div>

<div class="field">
<label for="exec_stop">exec_stop:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_stop"/>
</div>

<div class="field">
<label for="exec_poststart">exec_poststart:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_poststart"/>
</div>

<div class="field">
<label for="exec_poststop">exec_poststop:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_poststop"/>
</div>

<div class="field">
<label for="exec_prestart">exec_prestart:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_prestart"/>
</div>

<div class="field">
<label for="exec_prestop">exec_prestop:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_prestop"/>
</div>

<div class="field">
<label for="exec_master_poststart">exec_master_poststart:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_master_poststart"/>
</div>

<div class="field">
<label for="exec_master_poststop">exec_master_poststop:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_master_poststop"/>
</div>

<div class="field">
<label for="exec_master_prestart">exec_master_prestart:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_master_prestart"/>
</div>

<div class="field">
<label for="exec_master_prestop">exec_master_prestop:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_master_prestop"/>
</div>

<div class="field">
<label for="exec_timeout">exec_timeout:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_timeout"/>
</div>

<div class="field">
<label for="exec_fib">exec_fib:</label>
<input type="text" name="nop" readonly="readonly" value="$exec_fib"/>
</div>

<div class="field">
<label for="stop_timeout">stop_timeout:</label>
<input type="text" name="nop" readonly="readonly" value="$stop_timeout"/>
</div>

<div class="field">
<label for="cpuset">cpuset:</label>
<input type="text" name="cpuset" value="$cpuset"/>
</div>

<div class="field">
<label for="exec_consolelog">exec_consolelog:</label>
<input type="text" name="exec_consolelog" value="$exec_consolelog"/>
</div>

<div class="field">
<label for="mount_src">mount_src:</label>
<input type="checkbox" name="nop" readonly="readonly" value="mount_src"/>
</div>

<div class="field">
<label for="mount_kernel">mount_kernel:</label>
<input type="checkbox" name="nop" readonly="readonly" value="mount_kernel"/>
</div>

<div class="field">
<label for="mount_ports">mount_ports:</label>
<input type="checkbox" name="nop" readonly="readonly" value="mount_ports"/>
</div>

<div class="field">
<label for="astart">astart:</label>
<input type="checkbox" name="astart" value="astart"/>
</div>

<div class="field">
<label for="baserw">baserw:</label>
<input type="checkbox" name="nop" readonly="readonly" value="baserw"/>
</div>

<div class="field">
<label for="mount_devfs">mount_devfs:</label>
<input type="checkbox" name="nop" readonly="readonly" value="mount_devfs"/>
</div>

<div class="field">
<label for="allow_mount">allow_mount:</label>
<input type="checkbox" name="nop" readonly="readonly" value="allow_mount"/>
</div>

<div class="field">
<label for="allow_devfs">allow_devfs:</label>
<input type="checkbox" name="nop" readonly="readonly" value="allow_devfs"/>
</div>

<div class="field">
<label for="allow_nullfs">allow_nullfs:</label>
<input type="checkbox" name="nop" readonly="readonly" value="allow_nullfs"/>
</div>

<div class="field">
<label for="mkhostsfile">mkhostsfile:</label>
<input type="checkbox" name="nop" readonly="readonly" value="mkhostsfile"/>
</div>

<div class="field">
<label for="vnet">vnet:</label>
<input type="checkbox" name="nop" readonly="readonly" value="$vnet"/>
</div>

<div class="field">
<label for="applytpl">applytpl:</label>
<input type="checkbox" name="nop" readonly="readonly" value="$applytpl"/>
</div>

<div class="field">
<label for="floatresolv">floatresolv:</label>
<input type="checkbox" name="nop" readonly="readonly" value="floatresolv"/>
</div>

<div class="field">
<label for="mount_fdescfs">mount_fdescfs:</label>
<input type="checkbox" name="nop" readonly="readonly" value="mount_fdescfs"/>
</div>

<div class="field">
<label for="allow_procfs">allow_procfs:</label>
<input type="checkbox" name="nop" readonly="readonly" value="allow_procfs"/>
</div>

<div class="field">
<label for="allow_tmpfs">allow_tmpfs:</label>
<input type="checkbox" name="nop" readonly="readonly" value="$allow_tmpfs"/>
</div>

<div class="field">
<label for="allow_zfs">allow_zfs:</label>
<input type="checkbox" name="nop" readonly="readonly" value="$allow_zfs"/>
</div>
EOF;
}
	echo $str;
	$i++;
	}
?>
<input type="submit" name="create" value="Apply" >
</form>
<p><a href="jlist.php">Jail list</a></p>
</div>
