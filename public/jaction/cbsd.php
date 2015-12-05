<?php
$workdir=getenv('WORKDIR');

if (!isset($workdir)) {
	echo "No such workdir. Please set in nginx: fastcgi_param WORKDIR /path/to/cbsdworkdir;";
	exit(0);
}

function getJailTemplate()
{
	global $jname, $host_hostname, $astart, $ip4_addr, $workdir;
	global $mount_devfs, $allow_mount, $allow_devfs, $allow_nullfs;
	global $mkhostsfile, $devfs_ruleset, $ver, $baserw, $mount_src, $mount_obj, $mount_kernel;
	global $mount_ports, $astart, $vnet, $applytpl, $mdsize, $floatresolv;
	global $pkg_bootstrap, $user_pw_root, $interface, $sysrc_enable;

	$file=file_get_contents('/root/dummyhosting/public_html/jaction/jailtpl.jconf');

	if(!empty($file))
	{
		$file=str_replace('#jname#',$jname,$file);
		$file=str_replace('#host_hostname#',$host_hostname,$file);
		$file=str_replace('#astart#',$astart,$file);
		$file=str_replace('#ip4_addr#',$ip4_addr,$file);
		$file=str_replace('#workdir#',$workdir,$file);

		$file=str_replace('#mount_devfs#',$mount_devfs,$file);
		$file=str_replace('#allow_mount#',$allow_mount,$file);
		$file=str_replace('#allow_devfs#',$allow_devfs,$file);
		$file=str_replace('#allow_nullfs#',$allow_nullfs,$file);

		$file=str_replace('#mkhostsfile#',$mkhostsfile,$file);
		$file=str_replace('#devfs_ruleset#',$devfs_ruleset,$file);
		$file=str_replace('#ver#',$ver,$file);
		$file=str_replace('#baserw#',$baserw,$file);
		$file=str_replace('#mount_obj#',$mount_obj,$file);
		$file=str_replace('#mount_kernel#',$mount_kernel,$file);

		$file=str_replace('#mount_ports#',$mount_ports,$file);
		$file=str_replace('#mount_src#',$mount_src,$file);
		$file=str_replace('#astart#',$astart,$file);
		$file=str_replace('#vnet#',$vnet,$file);
		$file=str_replace('#applytpl#',$applytpl,$file);
		$file=str_replace('#mdsize#',$mdsize,$file);
		$file=str_replace('#floatresolv#',$floatresolv,$file);

		$file=str_replace('#pkg_bootstrap#',$pkg_bootstrap,$file);
		$file=str_replace('#user_pw_root#',$user_pw_root,$file);
		$file=str_replace('#interface#',$interface,$file);
		$file=str_replace('#sysrc_enable#',$sysrc_enable,$file);
	}
	return $file;
}


function human_filesize($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

// $status=check_vmonline("f10a");
// if status=1 - vm online
function check_vmonline($vm) {
	$exist=0;

	if (!file_exists("/dev/vmm")) return 0;

	if ($handle = opendir("/dev/vmm")) {
		while (false !== ($entry = readdir($handle))) {
			if ( "${vm}" == "${entry}" ) return 1;
		}
		closedir($handle);
	}

	return 0;
}
?>
