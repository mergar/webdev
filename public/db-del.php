<?php

$rp=realpath('');
include_once($rp.'/webdev/db.php');
echo '<pre>';

/*
const CBSD_CMD='env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd ';
	
function cbsd_cmd($cmd)
{
	$descriptorspec = array(
		0 => array('pipe','r'),
		1 => array('pipe','w'),
		2 => array('pipe','r')
	);

	$process = proc_open(CBSD_CMD.$cmd,$descriptorspec,$pipes,null,null);

	$error=false;
	$error_message='';
	$message='';
	if (is_resource($process))
	{
		$buf=stream_get_contents($pipes[1]);
		$buf0=stream_get_contents($pipes[0]);
		$buf1=stream_get_contents($pipes[2]);
		fclose($pipes[0]);
		fclose($pipes[1]);
		fclose($pipes[2]);

		$task_id=-1;
		$return_value = proc_close($process);
		if($return_value!=0)
		{
			$error=true;
			$error_message=$buf;
		}else{
			$message=trim($buf);
		}
		
		return array('cmd'=>$cmd,'retval'=>$return_value, 'message'=>$message, 'error'=>$error,'error_message'=>$error_message);
	}
}

$workdir=getenv('WORKDIR');
$path=$workdir.DIRECTORY_SEPARATOR."export".DIRECTORY_SEPARATOR;
$files=array_diff(scandir($path), array('..', '.'));
//print_r($files);
if(!empty($files)) foreach($files as $key=>$file)
{
	$file_name=$path.$file;
	$res=cbsd_cmd('imgpart jname='.$file_name.' part=descr mode=extract');
	print_r($res);
	echo $key,' — ',$file,': ',filesize($path.$file),PHP_EOL;
}
exit;
*/

/*
$file_name='/usr/jails/jails-system/jail24/descr';
echo filemtime($file_name),PHP_EOL;
$file=file_get_contents($file_name);
var_dump($file);
exit;
*/


/*
$db=new Db('sqlite_cbsd','jails');
$res=$db->select("select * from jails limit 10");
print_r($res);
*/

//*
$db=new Db('sqlite_cbsd','tasks');
//$db=new Db('sqlite_webdev');
$query="select status,client_id from taskd where client_id in (27) and status<2 limit 10";
$query="select * from taskd";	// where status<2 // and client_id in (22,23,24,25,27,28,33,34,35)
//$query="select * from modules_groups";
//$query="insert into modules_groups (id,name,comment) values (0,'Unsorted','Default group of modules')";
$res=$db->select($query);
print_r($res);
//*/
/*
$db=new Db('sqlite_webdev');
$res=$db->select("select * from jails limit 100");
print_r($res);
*/
/*
	Наполняем все описания в джейлах
*/ /*
$db=new Db('sqlite_webdev');
$query="select id,description from jails";
$res=$db->select($query);
if(!empty($res))
{
	foreach($res as $r)
	{
		if(!empty($r['description']))
		{
			$workdir=$db->getWorkdir();
			$path=$workdir.'/jails-system/jail'.$r['id'].'/';
			$file_name=$path.'descr';
			$time=time();
			if(file_exists($path))
			{
				$uquery="update jails set descr_mtime={$time} where id={$r['id']}";
				file_put_contents($file_name,$r['description']);	//b"\xEF\xBB\xBF".
				$db->update($uquery);
				echo $file_name,PHP_EOL,'<hr>',PHP_EOL;
			}
		}
	}
}
*/
