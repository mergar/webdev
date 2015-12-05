<?php

class WebDev
{
	public $projectId=0;
	public $jailId=0;
	public $moduleId=0;
	public $mode='';
	public $form='';
	public $workdir='';
	
	public $realpath='';

	private $_post=false;
	private $_vars=array();
	private $_db=null;
	private $_db_tasks=null;
	private $_db_jails=null;
	
	const CBSD_CMD='env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd ';
	
	function cbsd_cmd($cmd)
	{
		$descriptorspec = array(
			0 => array('pipe','r'),
			1 => array('pipe','w'),
			2 => array('pipe','r')
		);
//echo self::CBSD_CMD.$cmd;exit;
		$process = proc_open(self::CBSD_CMD.$cmd,$descriptorspec,$pipes,null,null);

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
				//$log_file='/tmp';
				//if(file_exists())
			}else{
				$message=trim($buf);
			}
			//echo self::CBSD_CMD.$cmd;
			return array('cmd'=>$cmd,'retval'=>$return_value, 'message'=>$message, 'error'=>$error,'error_message'=>$error_message);
		}
	}
	
	function __construct()
	{
		$this->workdir=getenv('WORKDIR');
		$rp=realpath('');
//echo base64_encode(file_get_contents($rp.'/images/tree-minus.gif'));exit;
		if(substr($rp,-7)=='/webdev')
		{
			$this->realpath=substr($rp,0,-7);
		}else{
			$this->realpath=$rp;
		}
		include_once($this->realpath.'/webdev/db.php');
		
		
		$this->_db=new Db('sqlite_webdev');
		$this->_db_tasks=new Db('sqlite_cbsd','tasks');
		$this->_db_jails=new Db('sqlite_cbsd','jails');
		$this->_post=($_SERVER['REQUEST_METHOD']=='POST');
		
		if(isset($_POST['groupsUpdate'])) return;
		
		if($this->_post)
		{
			$this->_vars=$_POST;
			unset($_POST);
			
			$this->projectId=intval($this->_vars['project']);
			$this->jailId=intval($this->_vars['jail']);
			$this->moduleId=intval($this->_vars['module']);
			$this->mode=$this->_vars['mode'];
			if(isset($this->_vars['form_data'])) $this->form=$this->_vars['form_data'];
			
			switch($this->mode)
			{
				case 'getProjectsList':
					$projects=$this->getProjectsList();
					echo json_encode(array('projects'=>$projects));
					return;break;
				case 'getJailsList':
					$projects=$this->getProjectsList();
					$jails=$this->getJailsList();
					echo json_encode(array('jails'=>$jails,'projects'=>$projects));
					return;break;
				case 'getModulesList':
					$jails=$this->getJailsList();
					$modules=$this->getModulesList();
					echo json_encode(array('jails'=>$jails,'modules'=>$modules));
					return;break;
				case 'getModuleSettings':
					$modules=$this->getModulesList();
					$settings=$this->getModuleSettings();
					echo json_encode(array('modules'=>$modules,'settings'=>$settings));
					return;break;
				case 'getServicesList':
					$jails=$this->getJailsList();
					$services=$this->getServicesList();
					echo json_encode(array('jails'=>$jails,'services'=>$services));
					return;break;
				case 'getUsersList':
					$jails=$this->getJailsList();
					$users=$this->getUsersList();
					echo json_encode(array('jails'=>$jails,'users'=>$users));
					return;break;
				case 'getModulesListForInstall':
//$this->updateCountsModules();
					$modules=$this->getModulesListForInstallHtml();
					echo json_encode(array('html'=>$modules));
					return;break;
/*
				case 'getInstalledModulesList':
					$jails=$this->getJailsList();
					$modules=$this->getInstalledModules();
					echo json_encode(array('jails'=>$jails,'html'=>$modules));
					return;break;
*/
				case 'addProject':
					echo json_encode($this->addProject());
					return;break;
				case 'addJail':
					echo json_encode($this->addJail());
					return;break;
				case 'editJail':
					echo json_encode($this->editJail());
					return;break;
/*
				case 'addModule':
					$this->addModule();
					return;break;
*/
/*
				case 'removeModules':
					$this->removeModules();
					return;break;
*/
				case 'jailStart':
					echo json_encode($this->jailStart($this->form['jail_name']));
					return;break;
				case 'getTasksStatus':
					echo json_encode($this->_getTasksStatus($this->form['jsonObj']));
					return;break;
				case 'getJailSettings':
					echo json_encode($this->getJailSettings($this->form['id']));
					return;break;
				case 'getExportedFiles':
					echo json_encode($this->getExportedFiles());
					return;break;
				case 'getImportedFileInfo':
					echo json_encode($this->getImportedFileInfo($this->form));
					return;break;
				case 'addNewUser':
					$new_user=$this->addNewUser($this->form);
					$jails=$this->getJailsList();
					$users=$this->getUsersList();
					echo json_encode(array('jails'=>$jails,'users'=>$users,'new_user'=>$new_user));
					return;break;
				case 'editUser':
					$edit_user=$this->editUser($this->form);
					$user=array();
					$jails=$this->getJailsList();
					$users=$this->getUsersList();
					echo json_encode(array('jails'=>$jails,'users'=>$users,'new_user'=>$edit_user));
					return;break;
				case 'getTaskLog':
					$jails=$this->getJailsList();
					$log=$this->getTaskLog();
					echo json_encode(array('jails'=>$jails,'tasklog'=>$log));
					return;break;
				case 'getTaskLogItem':
					$jails=$this->getJailsList();
					$item=$this->getTaskLogItem();
					echo json_encode(array('jails'=>$jails,'item'=>$item));
					return;break;
				case 'getForm':
					$res=$this->getForm();
					echo json_encode($res);
					return;break;
			}
		}
	}
/*
	function getProjectsListOnStart()
	{
		$query='select * from projects';
		$res=$this->_db->select($query);
		echo '	var projects=',json_encode($res),PHP_EOL;
	}
*/

/*
	function getTaskStatus($task_id)
	{
		$status=$this->_db_tasks->selectAssoc("select status,logfile,errcode from taskd where id='{$task_id}'");
		if($status['errcode']>0)
		{
			$status['errmsg']=file_get_contents($status['logfile']);
		}
		return $status;
	}
*/
	function _getTasksStatus($jsonObj)
	{
		$tasks=array();
		$obj=json_decode($jsonObj,true);
		
		if(isset($obj['mod_ops'])) return $this->GetModulesTasksStatus($obj);
		
		$ops_array=array('jstart','jstop','jedit','jremove','jexport','jimport','madd','sstart','sstop');	//,'mremove'
		$stat_array=array(
			'jstart'=>array('Starting','Launched'),
			'jstop'=>array('Stopping','Stopped'),
			'jedit'=>array('Saving','Saved'),
			'jremove'=>array('Removing','Removed'),
			'jexport'=>array('Exporting','Exported'),
			'jimport'=>array('Importing','Imported'),
			'madd'=>array('Installing','Installed'),
			//'mremove'=>array('Removing','Removed'),
			'sstart'=>array('Starting','Started'),
			'sstop'=>array('Stopping','Stopped'),
		);
		if(!empty($obj)) foreach($obj as $key=>$task)
		{
			$op=$task['operation'];
			$status=$task['status'];
			if(in_array($op,$ops_array))
			{
				$res=false;
				if($status==-1)
				{
					switch($op)
					{
						case 'jstart':	$res=$this->jailStart('jail'.$key,$key);break;
						case 'jstop':	$res=$this->jailStop('jail'.$key,$key);break;
						case 'jedit':	$res=$this->jailEdit('jail'.$key);break;
						case 'jremove':	$res=$this->jailRemove('jail'.$key,$key);break;
						case 'jexport':	$res=$this->jailExport('jail'.$key,$task['jname'],$key);break;
						case 'jimport':	$res=$this->jailImport('jail'.$key,$task['jname'],$key);break;
						case 'madd':	$res=$this->moduleAdd('jail'.$key,$task['jname'],$key);break;
						//case 'mremove':	$res=$this->moduleRemove('jail'.$key,$task['jname'],$key);break;
						case 'sstart':	$res=$this->serviceStart($task);break;
						case 'sstop':	$res=$this->serviceStop($task);break;
					}
				}
				
				if($res!==false)
				{
					if($res['error'])
						$obj[$key]['retval']=$res['retval'];
					if(!empty($res['error_message']))
						$obj[$key]['error_message']=$res['error_message'];

					if(isset($res['message']))
					{
						$task_id=intval($res['message']);
						if($task_id>0)
						{
							$tasks[]=$task_id;
							$obj[$key]['task_id']=$task_id;
							//$obj[$key]['txt_log']=file_get_contents('/tmp/taskd.'.$task_id.'.log');
						}
					}
				}else{
					$tasks[]=$task['task_id'];
				}
			}
		}
		
		$ids=join(',',$tasks);
		if(!empty($ids))
		{
			$query="select id,status,logfile,errcode from taskd where id in ({$ids})";
			$statuses=$this->_db_tasks->select($query);
			if(!empty($obj)) foreach($obj as $key=>$task)
			{
				if(!empty($statuses)) foreach($statuses as $stat)
				{
					if($task['task_id']==$stat['id'])
					{
						$obj[$key]['status']=$stat['status'];
						$num=($stat['status']<2?0:1);
						$obj[$key]['txt_status']=$stat_array[$obj[$key]['operation']][$num];
						if($stat['errcode']>0)
						{
							$obj[$key]['errmsg']=file_get_contents($stat['logfile']);
							$obj[$key]['txt_status']='Error';
						}
					#	Удаляем джейл
						if($stat['status']==2 && $task['operation']=='jremove')
						{
							$this->jailRemoveFromDb($stat['errcode'],$task);
						}
					#	Удаляем модуль
					/*
						if($stat['status']==2 && $task['operation']=='mremove')
						{
							$this->moduleRemoveFromDb($stat['errcode'],$task);
						}
					*/
					}
				}
			}
		}
		
		return $obj;
	}
	
	function GetModulesTasksStatus($obj)
	{
/*
		[mod_ops] => Array
			(
				[operation] => modremove
				[modules_id] => 22,40
				[status] => -1
			)
*/
		$stat_array=array(
			'modremove'=>array('Removing','Removed'),
			'modinstall'=>array('Installing','Installed'),
		);
		if(isset($obj['mod_ops']))
		{
			$ops=$obj['mod_ops'];
			if(isset($ops['operation']) && isset($stat_array[$ops['operation']]))
			{
				$status=$ops['status'];
				$modules_id=$ops['modules_id'];
				$op=$ops['operation'];
				$res=false;
				
				if($status==-1)
				{
					switch($op)
					{
						case 'modremove':	$res=$this->modulesRemove($modules_id);break;
						case 'modinstall':	$res=$this->modulesInstall($modules_id);break;
					}
				}
				
				if($res!==false)
				{
					if($res['error'])
						$ops['retval']=$res['retval'];
					if(!empty($res['error_message']))
						$ops['error_message']=$res['error_message'];

					if(isset($res['message']))
					{
						$task_id=intval($res['message']);
						if($task_id>0)
						{
							$ops['task_id']=$task_id;
						}
					}
				}
			}
		}
		
		$task_id=$ops['task_id'];
		if(isset($task_id))
		{
			$query="select id,status,logfile,errcode from taskd where id in ({$task_id})";
			$stat=$this->_db_tasks->selectAssoc($query);
			if(!empty($stat))
			{
				$ops['status']=$stat['status'];
				$num=($stat['status']<2?0:1);
				$ops['txt_status']=$stat_array[$ops['operation']][$num];
				if($stat['errcode']>0)
				{
					$ops['errmsg']=file_get_contents($stat['logfile']);
					$ops['txt_status']='Error';
				}
				
				if($ops['status']==2)
				{
					switch($op)
					{
						case 'modremove':
						case 'modinstall':
							$minfo=$this->updateModulesListInJail();
							break;
					}
				}
			}
		}
		
		$ret=array('mod_ops'=>$ops);
		
		if(isset($minfo) && !empty($minfo))
		{
			$minfo['mod_ops']=$ops;
			$ret=$minfo;
			unset($minfo);
		}
		
		return $ret;
	}
	
	function jailRemoveFromDb($errcode,$task)
	{
		$need_delete=false;
		if($errcode==0)
		{
			$need_delete=true;
		}else{
			$query="select count(*) from jails where jname='jail{$task['jail_id']}'";
			$res=$this->_db->selectAssoc($query);
			if($res['count']<1) $need_delete=true;
		}
		
		if($need_delete)
		{
			//$query="update jails set deleted='true' where id={$task['jail_id']}";
			$query="delete from jails where id={$task['jail_id']}";
			$this->_db->update($query);
			//$query="update modules set deleted='true' where jail_id={$task['jail_id']}";
			$query="delete from modules where jail_id={$task['jail_id']}";
			$this->_db->update($query);
		}
	}
	
	function modulesRemoveFromDb($modules_id)
	{
		//$query="update modules set deleted='true' where id in ({$modules_id})";
		//echo $query;
		//$this->_db->update($query);
	}
	
	function updateModulesListInJail()
	{
		$for_add=array();
		$for_del=array();
		$installed=array();
		$db=new Db('pkg','jail'.$this->jailId);
		$res=$db->select("select origin from packages");
		if(!empty($res))foreach($res as $i) $installed[]=$i['origin'];
		
		$old=$this->_db->select("select m.id,pgl.packagename from modules m,pkg_groups_link pgl where m.pkg_groups_link_id=pgl.id and m.jail_id={$this->jailId}");	//and m.deleted='false'
		if(!empty($old) && !empty($installed))
		{
			foreach($old as $key=>$o)
			{
				$pos=array_search($o['packagename'],$installed);
				if($pos!==false)
				{
					unset($installed[$pos]);
				}else{
					$for_del[]=$o['id'];
				}
			}
			$for_add=$installed;
		}
		
		if(!empty($for_del))
		{
			$ids=join(',',$for_del);
			//$query="update modules set deleted='true' where id in ({$ids})";
			$query="delete from modules where id in ({$ids})";
			$this->_db->update($query);
		}
		
		if(!empty($for_add))
		{
			$names=join("','",$for_add);
			$query="select packagename from pkg_groups_link where packagename in ('{$names}')";
			$res=$this->_db->select($query);
			$for_add=array();
			if(!empty($res))
			{
				foreach($res as $r) $for_add[]=$r['packagename'];
				$this->registerModules($for_add);
			}
		}
		
		$jails=$this->getJailsList();
		$modules=$this->getModulesList();
		return array('jails'=>$jails,'modules'=>$modules);		
	}
	
	
	function getTasksStatus($jsonObj)
	{
		$tasks=array();
		$ops_array=array('start','stop','edit','remove','export','import','modremove');
		$stat_array=array('Starting','Stopping','Saving','Removing','Exporting','Importing','Removing');
		$obj=json_decode($jsonObj,true);
		if(!empty($obj)) foreach($obj as $key=>$task)
		{
			if(in_array($task['operation'],$ops_array))
			{
				if($task['operation']=='start' && $task['status']==-1)
				{
					$res=$this->jailStart('jail'.$key,$key);
				}elseif($task['operation']=='stop' && $task['status']==-1){
					$res=$this->jailStop('jail'.$key,$key);
				}elseif($task['operation']=='edit' && $task['status']==-1){
					$res=$this->jailEdit('jail'.$key);
				}elseif($task['operation']=='remove' && $task['status']==-1){
					$res=$this->jailRemove('jail'.$key,$key);
				}elseif($task['operation']=='export' && $task['status']==-1){
					$res=$this->jailExport('jail'.$key,$task['jname'],$key);
				}elseif($task['operation']=='import' && $task['status']==-1){
					$res=array($task['jail_id']=>array(
						'jail_id'=>$task['jail_id'],
						'operation'=>'Importing',
						'status'=>1,
						'task_id'=>$task['task_id']
					));
					return $res;
				}elseif($task['operation']=='modremove' && $task['status']==-1){
					$res=$this->removeModule($key);
				}
				if($res['error'])
				{
					$obj[$key]['retval']=$res['retval'];
					$obj[$key]['error_message']=$res['error_message'];
					//$obj[$key]['operation']='Error';
				}
				if(isset($res['message']))	//task_id
				{
					$task_id=$res['message'];
					$tasks[]=$task_id;	// $res['task_id'];
					$obj[$key]['task_id']=$task_id;	//$res['task_id'];
					$obj[$key]['obj']='';
					if($task_id>0 && $task['operation']=='start') $obj[$key]['operation']='Starting';	//$res['task_id']
					if($task_id>0 && $task['operation']=='stop') $obj[$key]['operation']='Stopping';	//$res['task_id']
					if($task_id>0 && $task['operation']=='edit') $obj[$key]['operation']='Saving';	//$res['task_id']
					if($task_id>0 && $task['operation']=='remove') {$obj[$key]['operation']='Removing'; $obj[$key]['obj']='jail';}	//$res['task_id']
					if($task_id>0 && $task['operation']=='export') $obj[$key]['operation']='Exporting';	//$res['task_id']
					if($task_id>0 && $task['operation']=='import') $obj[$key]['operation']='Importing';	//$res['task_id']
					if($task_id>0 && $task['operation']=='modremove') {$obj[$key]['operation']='Removing'; $obj[$key]['obj']='module';}
					if(!empty($res['error_message'])) $obj[$key]['error_message']=$res['error_message'];
				}
			}else if(in_array($task['operation'],$stat_array)){	//$task['operation']=='Starting' || $task['operation']=='Stopping'
				$tasks[]=$task['task_id'];
			}
		}
		
		$ids=join(',',$tasks);
		if(!empty($ids))
		{
			$query="select id,status,logfile,errcode from taskd where id in ({$ids})";
			$statuses=$this->_db_tasks->select($query);
			if(!empty($obj)) foreach($obj as $key=>$task)
			{
				if(!empty($statuses)) foreach($statuses as $stat)
				{
					if($obj[$key]['task_id']==$stat['id'])
					{
						$obj[$key]['status']=$stat['status'];
						if($stat['errcode']>0)
						{
							$obj[$key]['errmsg']=file_get_contents($stat['logfile']);
							$obj[$key]['operation']='Error';
						}
					#	Удаляем джейл
						if($stat['status']==2 && $task['operation']=='Removing' && $task['obj']=='jail')
						{
							$need_delete=false;
							if($stat['errcode']==0)
							{
								$need_delete=true;
							}else{
								$query="select count(*) from jails where jname='jail{$task['jail_id']}'";
								$res=$this->_db->selectAssoc($query);
								if($res['count']<1) $need_delete=true;
							}
							if($need_delete)
							{
								//$query="update jails set deleted='true' where id={$task['jail_id']}";
								$query="delete from jails where id={$task['jail_id']}";
								$this->_db->update($query);
								//$query="update modules set deleted='true' where jail_id={$task['jail_id']}";
								$query="delete from modules where jail_id={$task['jail_id']}";
								$this->_db->update($query);
							}
						}
					#	Удаляем модуль
						if($stat['status']==2 && $task['operation']=='Removing' && $task['obj']=='module')
						{
							if($stat['errcode']==0)
							{
								//$query="update modules set deleted='true' where id={$task['module_id']}";
								//echo $query;
								//$this->_db->update($query);
							}
						}
					}
				}
			}
		}
		
		return $obj;
	}

	
	function getProjectsList()
	{
		$projects=$this->_db->select('select * from projects');
		if(!empty($projects)) foreach($projects as $key=>$p) $projects[$key]['size']=$this->fileSizeConvert($p['size']);
		return $projects;
	}
	
	function getJailsList()
	{
		$ids=array();
		$jails=$this->_db->select("select * from jails where project_id={$this->projectId}");	// and deleted='false'
		if(!empty($jails))
		{
			foreach($jails as $key=>$j)
			{
				$jails[$key]['size']=$this->fileSizeConvert($j['size']);
				$id=$j['id'];
				$ids[]=$id;
				
				if($this->checkDescrMtimeById($id,$j['descr_mtime']))
				{
					//$jails[$key]['description'].=' <span style="color:red;font-weight:bold;">(changed!)</span>';
					$res=$this->getDescrFileAndPath($id);
					if($res!==false)
					{
						$new_descr=file_get_contents($res['file']);
						$time=time();
						$query="update jails set description='{$new_descr}', descr_mtime={$time} where id={$id}";
						$this->_db->update($query);
						//$jails[$key]['description']=$new_descr.' <span style="color:red;font-weight:bold;">(changed!)</span>';
						//echo $query;
					}
				}
			}
		}
		if(!empty($ids))
		{
			$tid=join("','jail",$ids);
			$statuses=$this->_db_jails->select("select jname,status from jails where jname in ('jail{$tid}')");
			if(!empty($statuses)) foreach($statuses as $stat)
			{
				$id=str_replace('jail','',$stat['jname']);
				foreach($jails as $key=>$jail)
				{
					if($jail['id']==$id) $jails[$key]['status']=$stat['status'];
				}
			}
			
			$tid=join(',',$ids);
			$query="select id,cmd,status,client_id from taskd where status<2 and client_id in ({$tid})";
			$tasks=$this->_db_tasks->select($query);
			if(!empty($tasks)) foreach($tasks as $task)
			{
				if(!empty($jails))foreach($jails as $key=>$jail)
				{
					if($jail['id']==$task['client_id'])
					{
						$jails[$key]['task_status']=$task['status'];
						if(strpos($task['cmd'],'jstart')!==false) {$cmd='jstart';$txt_status='Starting';}
						if(strpos($task['cmd'],'jstop')!==false) {$cmd='jstop';$txt_status='Stopping';}
						if(strpos($task['cmd'],'jremove')!==false) {$cmd='jremove';$txt_status='Removing';}
						if(strpos($task['cmd'],'jexport')!==false) {$cmd='jexport';$txt_status='Exporting';}
						$jails[$key]['task_cmd']=$cmd;
						$jails[$key]['txt_status']=$txt_status;
						$jails[$key]['task_id']=$task['id'];
					}
				}
			}
		}
		
		unset($statuses);
/*
	[0] => Array
        (
            [id] => 49
            [st_time] => 20141024171441
            [end_time] => 20141024171815
            [user] => root
            [cmd] => cbsd jstart inter=0 movejail jail22 jail23 jail24 jail25 jail26 jail27 jail28
            [status] => 2
            [errcode] => 512
            [owner] => syscbsd
            [logfile] => /tmp/taskd.49.log
            [logtype] => auto
            [notify] => 0
            [autoflush] => 
            [after] => 0
        )

    [1] => Array
        (
            [id] => 50
            [st_time] => 20141024174050
            [end_time] => 20141024174050
            [user] => root
            [cmd] => /usr/local/bin/cbsd jstart inter=0 jname=jail26
            [status] => 2
            [errcode] => 256
            [owner] => cbsdwebsys
            [logfile] => /tmp/taskd.50.log
            [logtype] => auto
            [notify] => 1
            [autoflush] => 
            [after] => 0
        )
*/
		//$statuses=$this->_db_tasks->select("select * from taskd");
		return $jails;
	}
	
	function getDescrFileAndPath($id=0)
	{
		if($id==0) return false;
		$path=$this->workdir.'/jails-system/jail'.$id.'/';
		$file_name=$path.'descr';
		return array(
			'path'=>$path,
			'file'=>$file_name
		);
	}
	
	function checkDescrMtimeById($id=0,$dbmtime=0)
	{
		$res=$this->getDescrFileAndPath($id);
		if($res===false) return false;
		$path=$res['path'];
		$file_name=$res['file'];
		if(file_exists($path) && file_exists($file_name))
		{
			$mtime=filemtime($file_name);
			return $mtime>$dbmtime;
		}
		return false;
	}
	
	function getServicesList()
	{
	/*
		"прописать в автостарт, выписать из автостартоа" - это чекбоксом я вижу
		"запустить сервис"
		"остановить сервис"
		"рестарт"
	*/
		//cbsd service jname=XXX mode=list
		$arr=array();
		$res=$this->cbsd_cmd('service jname=jail'.$this->jailId.' mode=list');
		if($res['retval']==0)
		{
			$lst=explode("\n",$res['message']);
			$n=0;
			if(!empty($lst)) foreach($lst as $item)
			{
				$arr[$n]['id']=$n+1;
				$arr[$n]['name']=$item;
				$arr[$n]['autostart']=0;
				$arr[$n]['comment']='Описание мы придумаем позже&hellip;';
				$n++;
			}
		}
		/*
		$arr=array(
			array(
				'id'=>1,
				'name'=>'sshd',
				'comment'=>'Security connection',
				'autostart'=>0,
			),
			array(
				'id'=>2,
				'name'=>'ftpd',
				'comment'=>'File transfer protocol',
				'autostart'=>0,
			),
		);
		*/
		
		$statuses=array('Launched','Not running');
		
		if(!empty($arr)) foreach($arr as $key=>$item)
		{
			$res=$this->cbsd_cmd('service jname=jail'.$this->jailId.' '.$item['name'].' onestatus');
			$arr[$key]['jid']=$this->jailId;
			$arr[$key]['status']=$res['retval'];
			$arr[$key]['status_message']=$statuses[$res['retval']];
		}
		
		return $arr;
	}
	
	function getUsersList()
	{
		$info=array('login','home','gecos','shell');
		$tinfo=join(',',$info);
		$arr=array();
		$res=$this->cbsd_cmd('userlist jname=jail'.$this->jailId.' display='.$tinfo);
		//print_r($res);
		if($res['retval']==0)
		{
			$lst=explode("\n",$res['message']);
			$n=0;
			if(!empty($lst)) foreach($lst as $item)
			{
				$inf=explode('|',$item);
				if(!empty($inf)) foreach($inf as $key=>$i)
				{
					$arr[$n][$info[$key]]=$i;
				}
				$arr[$n]['id']=$n+1;
				//$arr[$n]['name']=$item;
				$arr[$n]['comment']='Описание мы придумаем позже&hellip;';
				$n++;
			}
		}
		return $arr;
	}
	
	function addNewUser($v)
	{
		/*
		поменять fullname пользователя root:
		cbsd pw jname=jail24 usermod name root -c 'Oleg Minin'

		Поменять пароль пользователя:
		cbsd passwd jname=jail24 user=root pw='test'    << пароль обязательно в одинарных кавычках, иначе shell может какие-нибудь "$ символы раскрыть
		*/
		$login=$v['login'];
		$password=$v['password'];
		$fullname=$v['fullname'];
		
		$tmp="user_add='{$login}'\nuser_pw_{$login}='{$password}'\nuser_gecos_{$login}='{$fullname}'\nuser_home_{$login}='/home/{$login}'\nuser_shell_{$login}='/bin/csh'\nuser_member_groups_{$login}='wheel'\n";
		
		$file=tmpfile();
		fwrite($file,$tmp);
		fclose($file);
		
		$res=array();
		
		$fn=tempnam('/tmp','usr-');
		if($fn)
		{
			$file=fopen($fn,'w+');
			if($file)
			{
				fwrite($file,$tmp);
				fclose($file);
				$res=$this->cbsd_cmd("adduser jname=jail{$this->jailId} mode=add fromfile='{$fn}'");
			}
			unlink($fn);
		}
		
		return $res;
	}
	
	function editUser($v)
	{
		$login=$v['login'];
		$password=$v['password'];
		$fullname=$this->toTranslit($v['fullname']);
		
		$res=array();
		
	#	поменять fullname пользователя root:
		if(!empty($fullname))
			$res=$this->cbsd_cmd("pw jname=jail{$this->jailId} usermod name {$login} -c '{$fullname}'");
		
	#	Поменять пароль пользователя:
		if(!empty($password))
			$res=$this->cbsd_cmd("passwd jname=jail{$this->jailId} user={$login} pw='{$password}'");
		
		return $res;
	}
	
	function getModulesList()
	{
		$query="select packagename from packages";
		$names=$this->_db->select($query);
		$names1=array();
		if(!empty($names))
		{
			foreach($names as $val) $names1[]=$val['packagename'];
			$names_txt=join("','",$names1);
			unset($names);
			unset($names1);
			$db=new Db('pkg','jail'.$this->jailId);
			if(!$db->error)
			{
				$res=$db->select("select comment,flatsize,id,name,origin,version from packages where origin in ('{$names_txt}')");
				if(!empty($res)) foreach($res as $key=>$item) $res[$key]['size']=$this->fileSizeConvert($item['flatsize']);
				return $res;
			}
		}
		return false;

	/*
		$query="select pgl.id, pgl.packagename, m.version, m.size, m.install_date, p.comment
				from modules m, pkg_groups_link pgl, packages p
				where m.pkg_groups_link_id=pgl.id
				and p.packagename=pgl.packagename
				and jail_id={$this->jailId}
				and deleted='false'";
	*/
	/*
		$query="
			select pgl.id, pgl.packagename, pgl.comment, m.version, m.size, m.install_date, pgl.is_new
			from modules m, pkg_groups_link pgl
			where m.pkg_groups_link_id=pgl.id
			and jail_id={$this->jailId}
			/-*and deleted='false'*-/
		";
		
		$modules=$this->_db->select($query);
		if(!empty($modules)) foreach($modules as &$m)
		{
			$m['size']=$this->fileSizeConvert($m['size']);
		}
		return $modules;
	*/
	}
	function getModuleSettings()
	{
		$html='Module info is unavailable&hellip;';
		$module_id=$this->_vars['module'];
		$db=new Db('pkg','jail'.$this->jailId);
		if(!$db->error)
		{
			$query="select * from packages where id={$module_id}";
			$res=$db->selectAssoc($query);
			$html='<h1>'.$res['name'].'</h1>';
			$html.='<h2>'.$res['origin'].'</h2>';
			$html.='<dl>';
			$html.='<dt>Version:</dt><dd>'.$res['version'].'</dd>';
			$html.='<dt>Comment:</dt><dd>'.$res['comment'].'</dd>';
			$html.='<dt>Description:</dt><dd>'.$res['desc'].'</dd>';
			$html.='<dt>Link:</dt><dd><a href="'.$res['www'].'">'.$res['www'].'</a></dd>';
			$html.='<dt>Size:</dt><dd>'.$this->fileSizeConvert($res['flatsize']).'</dd>';
			$html.='<dt>Time:</dt><dd>'.date('d.m.Y H:i',$res['time']).'</dd>';
			$html.='</dl>';
		}
		return $html;
	}
	function getModulesListForInstall()
	{
		$query="
			select
				pgl.id, mg.name as group_name, mg.comment as group_comment, p.name as module_name, p.comment, p.version, p.flatsize, pgl.packagename,
				m.version as old_version, m.size as old_size, m.installed, m.jail_id, m.install_date
			from
				modules_groups mg, packages p, pkg_groups_link pgl
				left join modules m on m.pkg_groups_link_id=pgl.id
			where
				mg.id=pgl.group_id
			and	p.packagename=pgl.packagename
			order
				by mg.id asc, pgl.packagename asc";
		$modules=$this->_db->select($query);
		$db=new Db('pkg','jail'.$this->jailId);
		if($db->error) return $modules;	// если БД недоступна, то возвращаем список модулей без отметки об установленных
		$res=$db->select("select origin from packages");
//print_r($modules);exit;
		$installed=array();
		if(!empty($res))foreach($res as $item){$installed[]=$item['origin'];}
		unset($res);
		
		if(!empty($modules))foreach($modules as $key=>$m)
		{
			$modules[$key]['installed']=in_array($m['packagename'],$installed);
		}
		
		return $modules;
	}
	function getModulesListForInstallHtml()
	{
		$html='';
		$last_group_name='';
		$modules=$this->getModulesListForInstall();
		if(!empty($modules)) foreach($modules as $key=>$module)
		{
			if($module['group_name']!=$last_group_name)
			{
				$gn=$module['group_name'];
				$html.='<h3>'.$gn.' <small>('.$module['group_comment'].')</small></h3>'.PHP_EOL;
				$last_group_name=$gn;
			}
			
			$disable=$installed='';
			if($module['installed'])
			{
				$disable=' disabled="disabled"';
				$installed=' installed';
			}
			
			$html.='<div class="mrow'.$installed.'"><span class="size">'.$this->fileSizeConvert($module['flatsize']).'</span><input type="checkbox" value="'.$module['id'].'" name="m'.$key.'" id="mod-'.$module['id'].'"'.$disable.'><label for="mod-'.$module['id'].'">'.$module['module_name'].' v.'.$module['version'].' <small>('.$module['comment'].')</small></label></div>';
		}
		return $html;
	}
	
	function getInstalledModules()
	{
		$query="";
		return array();
	}
	
	function getAllModulesList()
	{
	/*
		$modules=$this->_db->select("select * from modules");
		return $modules;
	*/
	}
	
	function addProject()
	{
		$form=$this->_vars['form_data'];
		$name=$form['name'];
		if(!empty($name))
		{
			$res=$this->_db->insert("insert into projects (name) values ('{$name}')");
			if($res!==false)
			{
				$projects=$this->getProjectsList();
				return array('lastID'=>$res['lastID'],'projects'=>$projects);
			}else{
			#	Ошибка в запросе
				return array('error'=>$res);
			}
		}else{
		#	Пришло пустое название проекта
		}
	}
	
	function addJail()
	{
		$form=$this->_vars['form_data'];
		if($this->projectId<1) return;
		
		$name=trim($form['name']);
		$hostname=trim($form['hostname']);
		$ip=trim($form['ip']);
		$description=trim($form['description']);
		
		$query="insert into jails (project_id,name,hostname,ip,description) values ({$this->projectId},'{$name}','{$hostname}','{$ip}','{$description}')";
		$res=$this->_db->insert($query);
		if($res['error'])
		{
			return array('error'=>$res);
		}else{
			$this->updateJailsCount();
			$jails=$this->getJailsList();
			$jail_name='jail'.$res['lastID'];
			$jres=$this->jailCreate($jail_name,$hostname,$ip);
			
			$err='Jail was created!';
			if($jres['retval']==0)
			{
				$err='Jail is not create!';
			}
			//$jsres=$this->jailStart($jail_name);
			$this->saveJailDescription($res['lastID'],$description);
			return array('lastID'=>$res['lastID'],'jails'=>$jails,'errorMessage'=>$err);	//,'jail_start'=>$jsres
		}
		
		$query="update projects set jails_count=(select count(*) from jails where project_id={$this->projectId}) where id={$this->projectId}";
		$this->_db->update($query);
		return;
	}
	function editJail()
	{
		$form=$this->_vars['form_data'];
		if($this->projectId<1) return;
		
		$id=intval($form['jail_id']);
		if($id<1) return;
		$name=trim($form['name']);
		$hostname='';
		if(isset($form['hostname']))
		{
			$hostname=",hostname='{$form['hostname']}'";	//.trim($form['hostname']);
			$hostname1=trim($form['hostname']);
		}else{
			$hostname='';
			$hostname1='';
		}
		
		$query="select ip4_addr,astart,host_hostname from jails where jname='jail{$id}'";
		$old=$this->_db_jails->selectAssoc($query);
		
		$ip=trim($form['ip']);
		$description=trim($form['description']);
		$astart=0;
		if(isset($form['astart'])) $astart=($form['astart']=='on'?1:0);
		
		$query="update jails set name='{$name}'{$hostname},ip='{$ip}',description='{$description}' where id={$id}";
		$this->_db->update($query);
		
		$arr=array();
		if(isset($ip) && isset($old['ip4_addr']) && $ip<>$old['ip4_addr']) $arr[]='ip4_addr="'.$ip.'"';
		if(isset($astart) && isset($old['astart']) && $ip<>$old['astart']) $arr[]='astart="'.$astart.'"';
		if($hostname1!='')
		{
			if(isset($old['host_hostname']) && $old['host_hostname']!=$hostname1)
			{
				$arr[]='host_hostname="'.$hostname1.'"';
			}
		}
		$cmd='jset jname=jail'.$id.' '.join(' ',$arr);
		$res=$this->cbsd_cmd($cmd);
		if($res['retval']!=0)
		{
			$errorMessage=$res['error_message'];
		}else{
			$errorMessage='';
		}
		
		$this->saveJailDescription($id,$description);
		return array('id'=>$id,'name'=>$name,'hostname'=>$hostname1,'ip'=>$ip,'description'=>$description,'editMode'=>'edit','errorMessage'=>$errorMessage,'cmd'=>$cmd);
	}
	function saveJailDescription($id,$description)
	{
		if($id<1) return;
		if(!empty($description))
		{
			$workdir=$this->workdir;
			$path=$workdir.'/jails-system/jail'.$id.'/';
			$file_name=$path.'descr';
			$time=time();
			if(file_exists($path))
			{
				file_put_contents($file_name,$description);	//b"\xEF\xBB\xBF".
			}
		}
	}
	function jailCreate($name,$hostname,$ip)
	{
		$tpl=$this->getJailTemplate($name,$hostname,1,$ip);
		$file_name='/tmp/'.$name.'.conf';
		file_put_contents($file_name,$tpl);
	//echo '<pre>',$file_name,PHP_EOL,$tpl;
		$res=$this->cbsd_cmd('jcreate inter=0 jconf='.$file_name);
		return $res;
	}
	function jailStart($name,$jail_id)
	{
		// task owner=cbsdwebsys autoflush=2 mode=new /usr/local/bin/cbsd jstart inter=0 jname=$jname
		//$res=$this->cbsd_cmd('jstart inter=0 jname='.$name);
		$res=$this->cbsd_cmd('task owner=cbsdwebsys mode=new client_id='.$jail_id.' /usr/local/bin/cbsd jstart inter=0 jname='.$name);	// autoflush=2
		return $res;
	}
	function jailStop($name,$jail_id)
	{
		//task owner=cbsdwebsys autoflush=2 mode=new env NOCOLOR=1 /usr/local/bin/cbsd jstop inter=0 jname=$jname"
		$res=$this->cbsd_cmd('task owner=cbsdwebsys mode=new client_id='.$jail_id.' /usr/local/bin/cbsd jstop inter=0 jname='.$name);	// autoflush=2
		return $res;
	}
	function jailRemove($name,$jail_id)
	{
		$res=$this->cbsd_cmd('task owner=cbsdwebsys mode=new client_id='.$jail_id.' /usr/local/bin/cbsd jremove inter=0 jname='.$name);	// autoflush=2
		return $res;
	}
	function jailExport($name,$new_name,$jail_id)
	{
		//$path=$this->workdir.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR;
		//$path.
	#	!!!!!!!!!!!!!!!!!!!!!
	#	ДОДЕЛАТЬ ЗАМЕНУ ВСЕХ ЗАПРЕЩЁННЫХ СИМВОЛОВ В ИМЕНАХ ФАЙЛОВ! А ТАК ЖЕ РУССКИХ БУКВ В $new_name
		$new_name=$name.'-'.$new_name;	//.'.img';
		$new_name=$this->toTranslit($new_name);
		$new_name=str_replace(array(' '),array('_'),$new_name);
		$cmd='task owner=cbsdwebsys mode=new client_id='.$jail_id.' /usr/local/bin/cbsd jexport inter=0 jname='.$name.' imgname='.$new_name;
		$res=$this->cbsd_cmd($cmd);	// autoflush=2
		return $res;
	}
	function jailEdit($name)
	{
		//cbsd jset jname=jail1 astart="1" ip4_addr="192.168.0.11/24,10.0.0.2/24"
		$form=$this->_vars['form_data'];
		print_r($form);exit;
		//$res=$this->cbsd_cmd('jset jname='.$name.' astart="'..'" ip4_addr="'..'"');	// autoflush=2
		return $res;
	}
	function getJailSettings($id=0)
	{
		if($id<1)return array('astart'=>'unknown','ip4_addr'=>'unknown','host_hostname'=>'unknown');
		
		$query="select id,name,ip,description,hostname from jails where id={$id}";
		$jail_iface=$this->_db->selectAssoc($query);
		if(empty($jail_iface)) $jail_iface=array();
		$query="select astart,ip4_addr,host_hostname from jails where jname='jail{$id}'";
		$jail_sys=$this->_db_jails->selectAssoc($query);
		if(empty($jail_sys)) $jail_sys=array();
		$res=array_merge($jail_iface,$jail_sys);
		return $res;
	}
	
	function serviceStart($task)
	{
		$res=$this->cbsd_cmd('task owner=cbsdwebsys mode=new client_id='.$this->jailId.' /usr/local/bin/cbsd service jname=jail'.$this->jailId.' '.$task['service_name'].' onestart');	// autoflush=2
		return $res;
	}
	function serviceStop($task)
	{
		$res=$this->cbsd_cmd('task owner=cbsdwebsys mode=new client_id='.$this->jailId.' /usr/local/bin/cbsd service jname=jail'.$this->jailId.' '.$task['service_name'].' onestop');	// autoflush=2
		return $res;
	}
	
	function addModuleGroup($name,$comment)
	{
		$query="insert into modules_groups (name,comment) values ('{$name}','{$comment}')";
		$lastID=$this->_db->insert($query);
		return $lastID;
	}
	
	function registerModules($modules)
	{
		if(!empty($modules))
		{
			$names=join("','",$modules);
			$query="select pgl.id, p.packagename, p.version, p.flatsize from packages p, pkg_groups_link pgl where pgl.packagename=p.packagename and pgl.packagename in ('{$names}')";
			$res=$this->_db->select($query);
			if(!empty($res)) foreach($res as $m)
			{
				$time=time();
				$query="insert into modules (pkg_groups_link_id,project_id,jail_id,version,size,install_date)
					values ({$m['id']},{$this->projectId},{$this->jailId},'{$m['version']}','{$m['flatsize']}',{$time})";
				$this->_db->insert($query);
			}
		}
	}
	
	/*
	function addModule()
	{
		$form=$this->_vars['form_data'];
		foreach($form as $key=>$id)
		{
			$query="select p.packagename, p.version, p.flatsize from packages p, pkg_groups_link pgl where pgl.id={$id} and pgl.packagename=p.packagename";
			$res=$this->_db->selectAssoc($query);
			if($res!==false)
			{
#	добавляем модуль! Здесь добавить проверку на добавление его в CBSD и в базу!
			#	Добавляем в task установку модуля
				$ires=$this->installModule($res['packagename']);
				//print_r($ires);exit;
				if(!$ires['error'])
				{
					$task_id=$ires['message'];
					
					$time=time();
					$query="insert into modules (pkg_groups_link_id,project_id,jail_id,version,size,install_date,task_id)
						values ({$id},{$this->projectId},{$this->jailId},'{$res['version']}','{$res['flatsize']}',{$time},{$task_id})";
					$lastID=$this->_db->insert($query);

				}else{
					$lastID=-1;
				}
			}
		}
		
		if($lastID>0)
		{
			$this->updateCountsModules();
		}
		
		$jails=$this->getJailsList();
		$modules=$this->getModulesList();
		echo json_encode(array('jails'=>$jails,'modules'=>$modules));
		return;
	}
	*/
	
	function getModuleNamesByIds($ids)
	{
		if(empty($ids)) return;
		$names=array();
		$query="select packagename from pkg_groups_link where id in ({$ids})";
		$res=$this->_db->select($query);
		if(!empty($res)) foreach($res as $item)
		{
			$names[]=$item['packagename'];
		}
		return $names;
	}
	
	function modulesInstall($modules_id)
	{
		if(empty($modules_id)) return false;
		
		$names=$this->getModuleNamesByIds($modules_id);
		if(empty($names)) return false;
		
		$txt_names=join(' ',$names);
		$jail_name='jail'.$this->jailId;
		$cmd='task owner=cbsdwebsys mode=new /usr/local/bin/cbsd pkg mode=install jname='.$jail_name.' '.$txt_names;
		$res=$this->cbsd_cmd($cmd);
		return $res;
	/*
		$query="select packagename from pkg_groups_link where id in ({$modules_id})";
		if($qres=$this->_db->select($query))
		{
			$names=array();
			if(!empty($qres))
			{
				foreach($qres as $r) $names[]=$r['packagename'];
				$txt_names=join(' ',$names);
				$jail_name='jail'.$this->jailId;
				$cmd='task owner=cbsdwebsys mode=new /usr/local/bin/cbsd pkg mode=install jname='.$jail_name.' '.$txt_names;
			//echo $cmd;
				$res=$this->cbsd_cmd($cmd);
				return $res;
			}
		}
		return false;
	*/
	}
	/*
		удалить пакет php55 в клетке jail1:
		cbsd pkg mode=remove name=php55 jname=jail1

		поставить пакет php55 туда же:
		cbsd pkg mode=install name=php55 jname=jail1
	*/
	
	function modulesRemove($modules_id)
	{
		if(empty($modules_id)) return false;
		$jail_name='jail'.$this->jailId;
		$txt_names=str_replace(';',' ',$modules_id);
		$res=$this->cbsd_cmd('task mode=new owner=cbsdwebsys /usr/local/bin/cbsd pkg mode=remove jname='.$jail_name.' '.$txt_names);	// autoflush=2
		return $res;
		/*
		$names=array();
		$query="select packagename from pkg_groups_link where id in ({$modules_id})";
		$res=$this->_db->select($query);
		if(!empty($res))
		{
			foreach($res as $item) $names[]=$item['packagename'];
			$jail_name='jail'.$this->jailId;
			$txt_names=join(' ',$names);
			$res=$this->cbsd_cmd('task mode=new owner=cbsdwebsys /usr/local/bin/cbsd pkg mode=remove jname='.$jail_name.' '.$txt_names);	// autoflush=2
			return $res;
		}
		return false;
		*/
	}
	
	function updateCountsModules()
	{
		$db=new Db('pkg','jail'.$this->jailId);
		if($db->error) return false;	// если БД недоступна, то выходим
		$res=$db->select("select * from packages");
		print_r($res);
		exit;
		
	#	Обновляем количество модулей в джейле
		$query="update jails set modules_count=(select count(*) from modules where jail_id={$this->jailId}) where id={$this->jailId}";
		$this->_db->update($query);
	#	Обновляем объём данных в джейле
		$query="update jails set size=(select sum(size) from modules where jail_id={$this->jailId}) where id={$this->jailId}";
		$this->_db->update($query);
	#	Обновляем количество модулей в проекте
		$query="update projects set modules_count=(select count(*) from modules where project_id={$this->projectId}) where id={$this->projectId}";
		$this->_db->update($query);
	#	Обновляем объём данных в проекте
		$query="update projects set size=(select sum(size) from modules where project_id={$this->projectId}) where id={$this->projectId}";
		$this->_db->update($query);
	}
	
	function updateJailsCount()
	{
		if($this->projectId<1) return false;
		$query="update projects set jails_count=(select count(*) from jails where project_id={$this->projectId}) where id={$this->projectId}";
		$rowCount=$this->_db->update($query);
		return $rowCount;
	}
	
	function getAllPackagesList()
	{
		$query="select packagename as name,comment from packages order by packagename asc";
		$packages=$this->_db->select($query);
		return $packages;
	}
	function updatePkgGroupsLink()
	{
		$packages=$this->getAllPackagesList();
		//echo '<pre>';
		//print_r($packages);exit;
		//$query="select * from pkg_groups_link where packagename='php55-gd'";
		//$res=$this->_db->select($query);
		$ids=array();
		if(!empty($packages))
		{
			foreach($packages as $pkg)
			{
				$query="select id,comment from packages where packagename='{$pkg['name']}'";
				$res=$this->_db->selectAssoc($query);
				$names[]=$pkg['name'];
				//print_r($res);
				if($res===false)
				{
					$query="insert into pkg_groups_link (packagename,comment,is_new) values ('{$pkg['name']}','{$pkg['comment']}','true')";
					//echo $query,'<br />';
					$this->_db->insert($query);
				}else{
					//$query="update pkg_groups_link set comment='{$res['comment']}',is_new='true' where packagename='{$pkg['name']}'";
					//echo $query,PHP_EOL;
					//$this->_db->insert($query);
				}
			}
		#	Помечаем все предыдущие модули в системе как старые, чтобы можно было впоследствии от них избавляться в базе
		#	Надеюсь это будет правильно работать :)
			//$this->_db->update("update pkg_groups_link set is_new='true'");
			if(!empty($names))
			{
				$names_txt=join("','",$names);
				$query="update pkg_groups_link set is_new='false' where is_new='true' and packagename not in ('{$names_txt}')";
				//echo $query;
				$this->_db->update($query);
			}
		}
	}
	function getPkgGroupsLink()
	{
		$query="select * from pkg_groups_link order by packagename asc";
		$res=$this->_db->select($query);
		return $res;
	}
	
	function getPackagesGroupsList()
	{
		$query="select * from modules_groups";
		$groups=$this->_db->select($query);
		return $groups;
	}
	
	function updateModulesGroups($vars)
	{
		if(!empty($vars['group'])) foreach($vars['group'] as $key=>$gr)
		{
			$query="update pkg_groups_link set group_id={$gr} where id={$key}";
			$this->_db->update($query);
		}
	}
	
	function getExportedFiles()
	{
		//cbsd imgpart jname=/usr/jails/export/drupal.img part=header mode=extract
		//cbsd imgpart jname=/usr/jails/export/drupal.img part=descr mode=extract
		
		$workdir=$this->workdir;
		$path=$workdir.DIRECTORY_SEPARATOR."export".DIRECTORY_SEPARATOR;
		$files=array_diff(scandir($path), array('..', '.'));
		$arr=array();
		if(!empty($files)) foreach($files as $key=>$file)
		{
			$file_name=$path.$file;
			$size=filesize($file_name);
			$res=$this->cbsd_cmd('imgpart jname='.$file_name.' part=descr mode=extract');
			$arr[]=array('name'=>$file,'size'=>$this->fileSizeConvert($size),'description'=>$res['message']);
		}
		
		return $arr;
	}
	
	function getImportedFileInfo($jnames)
	{
		if(empty($jnames)) return array('error'=>true,'errorMessage'=>'Imported file name is empty!');
		
		foreach($jnames as $key=>$jname)
		{
			$workdir=$this->workdir;
			$path=$workdir.DIRECTORY_SEPARATOR."export".DIRECTORY_SEPARATOR;
			$file_name=$path.trim($jname);
			if(!file_exists($file_name)) return array('error'=>true,'errorMessage'=>'Imported file not founded!');
			
			$descr_start=false;
			
			$file=file($file_name);
			for($n=0;$n<count($file);$n++)
			{
				$str=$file[$n];
				if(strpos($str,'jname')!==false) $arr[$key]['jname']=$this->getParamFromString($str,'jname');
				if(strpos($str,'host_hostname')!==false) $arr[$key]['host_hostname']=$this->getParamFromString($str,'host_hostname');
				if(strpos($str,'ip4_addr')!==false) $arr[$key]['ip4_addr']=$this->getParamFromString($str,'ip4_addr');
				if(strpos($str,'NCSTART_DATA')!==false) break;
				
				if(strpos($str,'NCSTART_INFO')!==false) $descr_start=false;
				if($descr_start) $arr[$key]['description'].=$str;
				if(strpos($str,'NCSTART_DESCR')!==false) {$descr_start=true;$arr[$key]['description']='';}
			}
			
			$name='Imported from '.trim($jname);
			$description=trim($arr[$key]['description']);
			$query="insert into jails (project_id,name,ip,description,hostname)
				values
				({$this->projectId},'{$name}','{$arr[$key]['ip4_addr']}','{$description}','{$arr[$key]['host_hostname']}')";
			$dbres=$this->_db->insert($query);
			if($dbres!==false && !$dbres['error'])
			{
				$lastId=$dbres['lastID'];
				if($lastId<1)
				{
					unset($arr[$key]);
				}else{
					$res=$this->cbsd_cmd('task owner=cbsdwebsys mode=new client_id='.$lastId.' /usr/local/bin/cbsd jimport jname='.$file_name.' newjname=jail'.$lastId);
					$arr[$key]['id']=$lastId;
					$arr[$key]['name']=$name;
					if($res['retval']==0)
					{
						if(!is_numeric($res['message']))
						{
							$arr['error']=true;
							$arr['errorMessage']=$res['message'];
						}else{
							$arr[$key]['task_id']=$res['message'];
							$arr[$key]['cmd']=$res['cmd'];
						}
					}else{
						$arr['error']=true;
						$arr['errorMessage']=$res['error_message'];
					}
				}
			}
		}
		return $arr;
	}
	function getParamFromString($str,$var)
	{
		$pat='#'.$var.'\s?=\s?"?([^"]+)"?;?#';
		preg_match($pat,$str,$res);
		return $res[1];
	}
	function jailImport()
	{
		//$cmd='task owner=cbsdwebsys mode=new client_id='.$jail_id.' /usr/local/bin/cbsd jexport inter=0 jname='.$name.' imgname='.$new_name;
		//$res=$this->cbsd_cmd($cmd);	// autoflush=2
		return $res;
	}
	
	function getTaskLog()
	{
		$query="select id,st_time,end_time,cmd,status,errcode,logfile from taskd where owner='cbsdwebsys' order by id desc;";
		$log=$this->_db_tasks->select($query);
		if(!empty($log)) foreach($log as $key=>$item)
		{
			$log[$key]['st_time']=date('d.m.Y H:i',$item['st_time']);
			$log[$key]['end_time']=date('d.m.Y H:i',$item['end_time']);
			$size=0;
			$logfile='';
			if(file_exists($item['logfile']))
			{
				$size=$this->fileSizeConvert(filesize($item['logfile']));
				//$logfile='<span class="link">'.$item['logfile'].'</span>';
			}
			$log[$key]['filesize']=$size;
			//if(!empty($logfile)) $log[$key]['logfile']=$logfile;
		}
		return $log;
	}
	function getTaskLogItem()
	{
		$log_id=$this->form['log_id'];
		$name='/tmp/taskd.'.$log_id.'.log';
		if(file_exists($name))
		{
			$file=file_get_contents($name);
			return $file;
		}
		return false;
	}
	
	
	function toTranslit($str)
	{
		$rus=array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ъ','ь','э','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ъ','Ь','Э','ч','Ч','ш','Ш','щ','Щ','ы','Ы','ю','Ю','я','Я');
		$eng=array('a','b','v','g','d','e','e','j','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','b','b','e','A','B','V','G','D','E','E','J','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','C','b','b','e','ch','CH','sh','SH','shc','SHC','yi','YI','yu','YU','ya','YA');
		for($n=0,$nl=count($rus);$n<$nl;$n++) $str=str_replace($rus,$eng,$str);
		return $str;
	}
	
	function fileSizeConvert($bytes)
	{
		$bytes = floatval($bytes);
		$arBytes = array(
			0 => array(
				"UNIT" => "TB",
				"VALUE" => pow(1024, 4)
			),
			1 => array(
				"UNIT" => "GB",
				"VALUE" => pow(1024, 3)
			),
			2 => array(
				"UNIT" => "MB",
				"VALUE" => pow(1024, 2)
			),
			3 => array(
				"UNIT" => "KB",
				"VALUE" => 1024
			),
			4 => array(
				"UNIT" => "B",
				"VALUE" => 1
			),
		);

		$result='0 MB';
		foreach($arBytes as $arItem)
		{
			if($bytes >= $arItem["VALUE"])
			{
				$result = $bytes / $arItem["VALUE"];
				$result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
				break;
			}
		}
		return $result;
	}
	
	/* jsFunc convert
	function getReadableFileSizeString(fileSizeInBytes)
	{
		var i = -1;
		var byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
		do {
			fileSizeInBytes = fileSizeInBytes / 1024;
			i++;
		} while (fileSizeInBytes > 1024);
		return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
	};
	*/
	
	
	function getJailTemplate($jailname,$hostname,$astart="1",$ipv4='DHCP')	//,$ipv4='172.17.0.10/24'
	{
		$file=file_get_contents($this->realpath.'/webdev/_jailConstructTemplate.txt');
		if(!empty($file))
		{
			$file=str_replace('#workdir#',$this->workdir,$file);
			$file=str_replace('#jailname#',$this->toTranslit($jailname),$file);
			$file=str_replace('#hostname#',$this->toTranslit($hostname),$file);
			$file=str_replace('#astart#',$astart,$file);
			$file=str_replace('#ipv4#',$ipv4,$file);
		}
		return $file;
	}
	
	
	function getForm()
	{
		$db=new Db('forms');
		$res=$db->select('select group_id, param, type, desc, def, cur, new, mandatory, attr, xattr from forms order by group_id asc, order_id asc');
		return $this->generateForm($res);
	}
	function generateForm($items)
	{
		$html='<h1>Loading form</h1><h2>Settings</h2>';
		//print_r($items);exit;
		if(!empty($items))
		{
			$html.='<form class="win" method="post">';
			foreach($items as $key=>$item)
			{
				$addit='';
				$val=$item['cur'];
				if(empty($item['cur'])) $val=$item['def'];
				$attr=$item['attr'];
				if(!empty($attr)) $attr=' '.$attr;
				//if(!empty($item['desc'])) $addit='<small class="astart-warn">— '.$item['desc'].'</small>';
				
				switch($item['type'])
				{
					case 'inputbox':
						$input="<input type=\"text\" name=\"{$item['param']}\" value=\"{$val}\"{$attr}>";
						break;
					case 'textarea':
						$input="<textarea name=\"{$item['param']}\"{$attr}>{$val}</textarea>";
						break;
				}
				
				$html.="<p> <span class=\"field-name\">{$item['desc']}:</span> {$input} {$addit} </p>";
			}
			$html.=PHP_EOL.'</form>';
		}
		//$html=str_replace(array("\r","\n","\t"),'',$html);
		$form_id='nginx-form';
		return array('html'=>$html,'form_id'=>$form_id);
	}
}

/*
Array
(
    [0] => Array
        (
            [id] => 1
            [group_id] => 0
            [packagename] => php55-tokenizer
        )

    [1] => Array
        (
            [id] => 2
            [group_id] => 0
            [packagename] => php55-sysvshm
        )

    [2] => Array
        (
            [id] => 3
            [group_id] => 0
            [packagename] => php55-fileinfo
        )

    [3] => Array
        (
            [id] => 4
            [group_id] => 0
            [packagename] => php55-wddx
        )
*/