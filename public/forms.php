<?php
$rp=realpath('');
include_once($rp.'/webdev/db.php');

class Forms
{
	private $name='';
	private $db='';
	
	function __construct($name)
	{
		$this->name=$name;
		$this->db=new Db('helpers',$name);
		print_r($this->db);
	}
}

$form=new Forms('php');