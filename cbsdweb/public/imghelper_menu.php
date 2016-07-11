<?php
require_once("auth.php");
?>


<?php
$rp=realpath('');
include_once($rp.'/cbsd_cmd.php');

function jail_menu()
{
	global $jname;


#	$str = <<<EOF
#	<a href="javascript:location.reload(true)">[ Refresh Page ]</a> | <a href="jconfig.php?jname=$jname">[ Config ]</a> | 
#	<a href="jclone.php?jname=$jname">[ Clone ]</a> | <a href="jrename.php?jname=$jname">[ Rename ]</a> | <a href="jexport.php?jname=$jname">[ Export ]</a> |
#	<a href="jdescr.php?jname=$jname">[ Descr ]</a> |
#	<a href="imghelper.php?jname=$jname">[ Helpers ]</a> |
#
#EOF;
#	echo $str;
#	echo "<hr>";

$str = <<<EOF
        <a href="javascript:location.reload(true)">[ Refresh Page ]</a> | <a href="jconfig.php?jname=$jname">[ Config ]</a> |
EOF;


//$fp=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd imghelper header=0", "r");
$res=cbsd_cmd('env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd imghelper header=0');

if ($res['retval'] != 0 ) {
	if (!empty($res['error_message']))
		echo $res['error_message'];
	exit(1);
}


$lst=explode("\n",$res['message']);
$n=0;
if(!empty($lst)) foreach($lst as $item)
{
	$str .= <<<EOF
        <a href="img_helper_cfg.php?jname=$jname&helper=$item">[ $item ]</a> |
EOF;
}

echo $str;
echo "<hr>";

}
?>


