<?php
if(isset($_POST['file_name'])) {
	$file=$_POST['file_name'];
	$field = $_POST['field'];
	$nodeName = $_POST['node_name'];
	$result = file_put_contents($file, $_POST['data']);
	// if ( $result ) echo 'Commited';
	// Use taskd instead of sending directly

	$descriptorspec = array(
		0 => array("pipe", "r"),
		1 => array("pipe", "w"),
		2 => array("pipe", "r")
	);

	$myCMD = "env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd nodescp norsync=1 $file $nodeName:node.$field";
	$process = proc_open($myCMD, $descriptorspec, $pipes, null, null);
                    
	if (is_resource($process)) {
		while (($buffer = fgets($pipes[1], 4096)) !== false) {
			//nop
		}
                    
		fclose($pipes[0]);
		fclose($pipes[1]);
		fclose($pipes[2]);
                        
		$return_value = proc_close($process);

		if ($return_value!=0) {
			echo "cbsd nodescp error";
			exit($return_value);
		}
		//        echo "exit code: $return_value\n";
	}

	$myCMD = "env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd retrinv node=$nodeName";
	$process = proc_open($myCMD, $descriptorspec, $pipes, null, null);
                    
	if (is_resource($process)) {
		while (($buffer = fgets($pipes[1], 4096)) !== false) {
			//nop
		}
                    
		fclose($pipes[0]);
		fclose($pipes[1]);
		fclose($pipes[2]);
                        
		$return_value = proc_close($process);

		        if ($return_value!=0) {
		            echo "cbsd retrinv error";
		            exit($return_value);
		        }
			//location.reload();
		        echo "exit code: $return_value\n";
			die;
	}
}
?>
