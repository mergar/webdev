<!-- //For server/admins panel with NSS Ldap enabled -->
<?php
//GLOBAL
$client_prefix="cl";
$client_maxid=1024;

// find next $client_prefix$X user
// $client_prefix for example: cl
// example:
//	$cl_id=find_next_client_id();
//	echo $client_prefix.$cl_id.PHP_EOL;
function find_next_client_id()
{
    if (empty($GLOBALS['client_prefix'])) $GLOBALS['client_prefix']="cl";

    for ($cl_id=1;$cl_id<$GLOBALS['client_maxid']+1;$cl_id++) {
	$str="/usr/bin/id ".$GLOBALS['client_prefix'].$cl_id." >/dev/null 2>&1";
	system($str, $retval);
	if ($retval==1) break;
    }

    return ($cl_id == $GLOBALS['client_maxid'] ) ? 0 : $cl_id ;
}
