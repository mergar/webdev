<?php
require('cbsd.php');

$jname = $_POST['jname'];
$host_hostname = $_POST['host_hostname'];
$ip4_addr = $_POST['ip4_addr'];
$mount_devfs = $_POST['mount_devfs'];
$arch = $_POST['arch'];
$mkhostfile = $_POST['mkhostfile'];
$devfs_ruleset = $_POST['devfs_ruleset'];
$interface = $_POST['interface'];
$ver = $_POST['ver'];

if (!isset($_POST['basename'])) {
	$basename="";
} else {
	$basename = $_POST['basename'];
}

if (!isset($_POST['sysrc_enable'])) {
	$sysrc_enable="";
} else {
	$sysrc_enable = $_POST['sysrc_enable'];
}


if (!isset($_POST['user_pw_root'])) {
	$user_pw_root="";
} else {
	$user_pw_root = $_POST['user_pw_root'];
}


if (!isset($_POST['user_add'])) {
	$user_add="";
} else {
	$user_add = $_POST['user_add'];
}

if (isset($user_add)) {
	if (!isset($_POST['user_add_gecos'])) {
		$user_add_gecos = "$user_add";
	} else {
		$user_add_gecos = $_POST['user_add_gecos'];
	}

	if (!isset($_POST['user_add_password'])) {
		//no password, reset userlist
		$user_add="";
	} else {
		$user_add_password = $_POST['user_add_password'];
	}
}

if (!isset($_POST['slavenode'])) {
	$slavenode="";
} else {
	$slavenode = $_POST['slavenode'];
}

// checkbox area
if (!isset($_POST['astart'])) {
	$astart="0";
} else {
	$astart = $_POST['astart'];
}

if (!isset($_POST['vnet'])) {
	$vnet="0";
} else {
	$vnet = $_POST['vnet'];
}

if (!isset($_POST['applytpl'])) {
	$applytpl="1";
} else {
	$applytpl = $_POST['applytpl'];
}

if (!isset($_POST['floatresolv'])) {
	$floatresolv="1";
} else {
	$floatresolv = $_POST['floatresolv'];
}

if (!isset($_POST['pkg_bootstrap'])) {
	$pkg_bootstrap="1";
} else {
	$pkg_bootstrap = $_POST['pkg_bootstrap'];
}

if (!isset($_POST['baserw'])) {
	$baserw="0";
} else {
	$baserw = $_POST['baserw'];
}

if (!isset($_POST['mount_src'])) {
	$mount_src="0";
} else {
	$mount_src = $_POST['mount_src'];
}

if (!isset($_POST['mount_obj'])) {
	$mount_obj="0";
} else {
	$mount_obj = $_POST['mount_obj'];
}

if (!isset($_POST['mount_kernel'])) {
	$mount_kernel="0";
} else {
	$mount_kernel = $_POST['mount_kernel'];
}

if (!isset($_POST['mount_ports'])) {
	$mount_ports="0";
} else {
	$mount_ports = $_POST['mount_ports'];
}
///

if (!isset($_POST['mdsize'])) {
	$mdsize="0";
} else {
	$mdsize = $_POST['mdsize'];
}


$exec_start = $_POST['exec_start'];
$execstop = $_POST['exec_stop'];

if (!isset($_POST['pkglist'])) {
	$plglist="";
} else {
	$pkglist = $_POST['pkglist'];
}

if (!isset($_POST['allow_mount'])) {
	$allow_mount="1";
} else {
	$allow_mount = $_POST['allow_mount'];
}

if (!isset($_POST['allow_devfs'])) {
	$allow_devfs="1";
} else {
	$allow_devfs = $_POST['allow_devfs'];
}

if (!isset($_POST['allow_nullfs'])) {
	$allow_nullfs="1";
} else {
	$allow_nullfs = $_POST['allow_nullfs'];
}

$pkglist = $_POST['pkglist'];

//$user_add_gecos
//$user_add_password
//$user_add

$userappend="";

if (isset($user_add)) {
 $userappend = <<<EOF

user_add='$user_add'
user_pw_${user_add}='$user_add_password'
user_gecos_${user_add}='$user_add_gecos'
user_home_${user_add}='/home/$user_add'
user_shell_${user_add}='/bin/csh'
user_member_groups_${user_add}='wheel'

EOF;
}

//if (strcmp($pkglist,"NO")) {
//	$pkgfile = fopen("/tmp/$jname-pkg.list", "w+");
//	fputs($pkgfile,$pkglist);
//	fclose($pkgfile);
//	$pkglist="/tmp/$jname-pkg.list";
//}

$tpl=getJailTemplate();
$file_name='/tmp/'.$jname.'.jconf';
file_put_contents($file_name,$tpl.$userappend);

//$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb autoflush=2 mode=new env NOCOLOR=1 /usr/local/bin/cbsd jcreate inter=0 jconf=/tmp/$jname.jconf", 'r');
$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd jcreate inter=0 jconf=/tmp/$jname.jconf", 'r');
$read = fgets($handle, 4096);
echo "Job Queued: $read";
pclose($handle);
header( 'Location: jlist.php' ) ;
?>
