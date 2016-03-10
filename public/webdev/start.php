<?php
$rp=realpath('');
include_once($rp.'/webdev/forms.php');

class WebDev
{
	public $projectId=0;
	public $jailId=0;
	public $moduleId=0;
	public $helper='';
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
		$process = proc_open(self::CBSD_CMD.trim($cmd),$descriptorspec,$pipes,null,null);

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
			if(isset($this->_vars['helper']))
				$this->helper=$this->_vars['helper'];
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
				case 'getHelpersList':
					$jails=$this->getJailsList();
					$helpers=$this->getHelpersList();
					echo json_encode(array('jails'=>$jails,'helpers'=>$helpers));
					return;break;
				case 'getHelper':
					//$jails=$this->getJailsList();
					$modules=$this->getHelpersList();
					$helper=$this->getHelper();
					echo json_encode(array('modules'=>$modules,'helpers'=>$helper));
					return;break;
				case 'installHelper':
					$res=$this->installHelper();
					$modules=$this->getHelpersList();
					$helper=$this->getHelper();
					echo json_encode(array('modules'=>$modules,'helpers'=>$helper,'res'=>$res));
					return;break;
				case 'saveHelperValues':
					$res=$this->saveHelperValues();
					echo json_encode($res);
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
					echo json_encode($this->projectAdd());
					return;break;
				case 'editProject':
					echo json_encode($this->projectEdit());
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
		
		if(isset($obj['proj_ops'])) return $this->GetProjectTasksStatus($obj);
		if(isset($obj['mod_ops'])) return $this->GetModulesTasksStatus($obj);
		
		$ops_array=array('jcreate','jstart','jstop','jedit','jremove','jexport','jimport','madd','sstart','sstop','projremove');	//,'mremove'
		$stat_array=array(
			'jcreate'=>array('Creating...','Not running'),
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
			//'projremove'=>array('Removing','Removed'),
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
						//case 'projremove':	$res=$this->projectRemove($key,$task);break;
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
	
	function GetProjectTasksStatus($obj)
	{
		if(isset($obj['proj_ops']))
		{
			$ops=$obj['proj_ops'];
			unset($obj['proj_ops']);
			$res=$this->projectRemove($obj);
			return $res;
		}
		return false;
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
					$ops['txt_sta