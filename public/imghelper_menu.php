<?php

function jail_menu()
{
	global $jname;

$str = <<<EOF
        <a href="javascript:location.reload(true)">[ Refresh Page ]</a> | <a href="jconfig.php?jname=$jname">[ Config ]</a> |
EOF;


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

