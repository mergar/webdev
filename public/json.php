<?php

include('webdev/start.php');
$wd=new WebDev();

/*
class Request
{
	public $projectId=0;
	public $jailId=0;
	public $moduleId=0;
	public $mode='';

	private $_post=false;
	private $_vars=array();
	private $_db=null;
	
	function __construct()
	{
		$this->_post=($_SERVER['REQUEST_METHOD']=='POST');
		if($this->_post)
		{
			$this->_vars=$_POST;
			unset($_POST);
			
			$this->projectId=intval($this->_vars['project']);
			$this->jailId=intval($this->_vars['jail']);
			$this->moduleId=intval($this->_vars['module']);
			$this->mode=$this->_vars['mode'];
			
			include('webdev/db.php');
			$this->_db=new Db('sqlite');
			
			switch($this->mode)
			{
				case 'getJailsList':
					$jails=$this->getJailsList();
					echo json_encode(array('jails'=>$jails));
					return;break;
				case 'getModulesList':
					$jails=$this->getJailsList();
					$modules=$this->getModulesList();
					echo json_encode(array('jails'=>$jails,'modules'=>$modules));
					return;break;
				case 'addProject':
					echo json_encode($this->addProject());
					return;break;
				case 'addJail':
					echo json_encode($this->addJail());
					return;break;
				case 'addModule':
					echo json_encode($this->addModule());
					return;break;
			}
		}
	}

	function getProjectsList()
	{
		$projects=$this->_db->query('select * from projects');
		return $projects;
	}
	
	function getJailsList()
	{
		$jails=$this->_db->query("select * from jails where project_id={$this->projectId}");
		return $jails;
	}
	
	function getModulesList()
	{
		$modules=$this->_db->query("select * from modules where project_id={$this->projectId} and module_id={$this->moduleId}");
		return $modules;
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
		if($res===false)
		{
			return array('error'=>$res);
		}else{
			$jails=$this->getJailsList();
			return array('lastID'=>$res['lastID'],'jails'=>$jails);
		}
		
		return;
	}
	
	function addModule()
	{
		
		return;
	}
}

$req=new Request();
*/