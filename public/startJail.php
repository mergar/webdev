<?php

define('CBSD_CMD','env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd ');
echo '<pre>';print_r( cbsd_cmd('jstart inter=0 jname=jail17') );

	function cbsd_cmd($cmd)
	{
		$descriptorspec = array(
			0 => array('pipe','r'),
			1 => array('pipe','w'),
			2 => array('pipe','r')
		);
//echo self::CBSD_CMD.' '.$cmd;exit;
		$process = proc_open(CBSD_CMD.$cmd,$descriptorspec,$pipes,null,null);

		$error=false;
		$error_message='';
		if (is_resource($process))
		{
			$buf=stream_get_contents($pipes[1]);
			fclose($pipes[0]);
			fclose($pipes[1]);
			fclose($pipes[2]);

			$return_value = proc_close($process);
			if($return_value!=0)
			{
				$error=true;
				$error_message=$buf;
			}
			return array('cmd'=>$cmd,'retval'=>$return_value,'error'=>$error,'error_message'=>$error_message);
		}
	}
	
	
	