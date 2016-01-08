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
	}
	
	function generate()
	{
		$query="select * from forms order by group_id asc, order_id asc";
		$res=$db->select($query);
		print_r($res);
	}
}

$form=new Forms('php');
$form->generate();