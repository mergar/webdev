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
		$fields=$this->db->select($query);
		echo '<pre>';print_r($fields);
/*
            [idx] => 2
            [group_id] => 1
            [order_id] => 2
            [param] => expose_php
            [desc] => default is Off
            [def] => Off
            [cur] => 
            [new] => 
            [mandatory] => 1
            [attr] => maxlen=60
            [xattr] => 
            [type] => inputbox
*/
		foreach($fields as $key=>$field)
		{
			$tpl=$this->getElement($field['type']);
			$params=array('param','desc','attr','cur');
			foreach($params as $param)
			{
				$tpl=str_replace('${'.$param.'}',$field[$param],$tpl);
			}
			$required=($field['mandatory']==1)?' required':'';
			$tpl=str_replace('${required}',$required,$tpl);
			echo $tpl;
		}
	}
	
	function getElement($el)
	{
		$tpl='';
		switch($el)
		{
			case 'inputbox':
				$tpl='<div class=""><input type="text" name="${param}" value="${cur}" ${attr}${required} /><span class="small">${desc}</span></div>';
				break;
			case 'tpl':
				$tpl='<h1>${param}</h1>';
				break;
		}
		return $tpl;
	}
}

$form=new Forms('php');
$form->generate();