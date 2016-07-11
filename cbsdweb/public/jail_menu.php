<?php
require_once("auth.php");
?>


<?php
function jail_menu()
{
	global $jname;

	$str = <<<EOF
	<a href="jlist.php">[ << Jails ]</a> |
	<a href="javascript:location.reload(true)">[ Refresh Page ]</a> | <a href="jconfig.php?jname=$jname">[ Config ]</a> |
	<a href="jclone.php?jname=$jname">[ Clone ]</a> | <a href="jrename.php?jname=$jname">[ Rename ]</a> | <a href="jexport.php?jname=$jname">[ Export ]</a> |
	<a href="jdescr.php?jname=$jname">[ Descr ]</a> |
	<a href="imghelper.php?jname=$jname">[ Helpers ]</a> |

EOF;
	echo $str;
	echo "<hr>";
}
?>


