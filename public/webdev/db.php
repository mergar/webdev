<?php
class Db
{
	private $_pdo=null;
	private $_workdir='';
	public $error=false;
	public $error_message='';
	
	function __construct($driver='sqlite_webdev',$database='')
	{
		$this->_workdir=getenv('WORKDIR');
		
		$databases=array(
			'tasks'=>'cbsdtaskd',
			'jails'=>'local',
		);
		
		switch($driver)
		{
			case 'sqlite_webdev':
				$connect='sqlite:/var/db/webdev/webdev.sqlite';
				break;
			case 'forms':
				$connect='sqlite:/var/db/webdev/forms.sqlite';
				break;
			case 'sqlite_cbsd':
				if($database!='')
				{
					if(!isset($databases[$database])) break;
					$db=$databases[$database];
					$connect='sqlite:'.$this->_workdir.'/var/db/'.$db.'.sqlite';
				}
				break;
			case 'pkg':
				$connect='sqlite:'.$this->_workdir.'/jails-data/'.$database.'-data/var/db/pkg/local.sqlite';
				break;
			default:
				throw new Exception('Unknown database driver!');
				break;
		}
		
		if(!empty($connect))
		{
			try
			{
				$this->_pdo = new PDO($connect);
				$this->_pdo->setAttribute(PDO::ATTR_TIMEOUT,5000);
			}catch (PDOException $e){
				//die ('DB Error');
				$this->error=true;
				$this->error_message='DB Error';
				return false;
			}
		}
	}
	
	function getWorkdir()
	{
		return $this->_workdir;
	}
	
	function select($query)
	{
		if($quer=$this->_pdo->query($query))
		{
			$res=$quer->fetchAll(PDO::FETCH_ASSOC);
			return $res;
		}
		return array();
	}
	
	function selectAssoc($query)
	{
		if($quer=$this->_pdo->query($query))
		{
			$res=$quer->fetch(PDO::FETCH_ASSOC);
			return $res;
		}
		return array();
	}
	
	function insert($query)
	{
		if($quer=$this->_pdo->query($query))
		{
			$lastID=$this->_pdo->lastInsertId();
			return array('error'=>false,'lastID'=>$lastID);
		}else{
			$error=array('error'=>true,'info'=>$this->_pdo->errorInfo());
			return $error;
		}
		return false;
	}
	
	function update($query)
	{
		if($quer=$this->_pdo->query($query))
		{
			$rowCount=$quer->rowCount();
			return array('rowCount'=>$rowCount);
		}else{
			$error=$this->_pdo->errorInfo();
			return $error;
		}
		return false;
	}
}