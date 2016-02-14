<?php
$rp=realpath('');
include_once($rp.'/webdev/db.php');

class Forms
{
	private $name='';
	private $db='';
	private $html='';
	
	function __construct($jname,$helper)
	{
		$this->name=$name;
		$this->db=new Db('helpers',array('jname'=>$jname,'helper'=>$helper));
	}
	
	function generate()
	{
		$query="select * from forms order by group_id asc, order_id asc";
		$fields=$this->db->select($query);
		//echo '<pre>';print_r($fields);
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
			
            [idx] => 1
            [group_id] => 1
            [order_id] => 1
            [param] => -
            [desc] => PHP Settings
            [def] => PHP Settings
            [cur] => PP
            [new] => 
            [mandatory] => 1
            [attr] => maxlen=60
            [xattr] => 
            [type] => delimer
			
            [idx] => 22
            [group_id] => 1
            [order_id] => 1
            [param] => -
            [desc] => PHP-FPM Settings
            [def] => -
            [cur] => -
            [new] => 
            [mandatory] => 1
            [attr] => maxlen=60
            [xattr] => 
            [type] => delimer
*/
		$this->html='<form name=""><div class="form-fields">';
		foreach($fields as $key=>$field)
		{
			$tpl=$this->getElement($field['type']);
			$params=array('param','desc','attr','cur');
			foreach($params as $param)
			{
				if(isset($field[$param]))
					$tpl=str_replace('${'.$param.'}',$field[$param],$tpl);
			}
			
			$value=$field['def'];
			if(isset($field['cur']) && !empty($field['cur'])) $value=$field['cur'];
			$tpl=str_replace('${value}',$value,$tpl);
			
			$required=($field['mandatory']==1)?' required':'';
			$tpl=str_replace('${required}',$required,$tpl);
			$this->html.=$tpl;
		}
		$this->html.='</div>';
		
		$this->setButtons();
		$this->html.='</form>';
		return $this->html;
	}
	
	function getElement($el)
	{
		$tpl='';
		switch($el)
		{
			case 'inputbox':
				$tpl='<div class="form-field"><input type="text" name="${param}" value="${value}" ${attr}${required} /><span class="small">${desc}</span></div>';
				break;
			case 'delimer':
				$tpl='<h1>${desc}</h1>';
				break;
		}
		return $tpl;
	}
	
	function setButtons($arr=array())
	{
		$this->html.='<div class="buttons"><input type="button" value="Apply" class="save-helper-values" /> &nbsp; <input type="button" value="Clear" /></div>';
	}
}

/*

$form=new Forms('php');
?>
<html>
<style>
body {font-size:100%;font-family:Tahoma,'Sans-Serif',Arial;}
h1 {color:white;background:silver;margin:0;padding:10px;}
.small {font-size:x-small;}
.form-field {padding:4px 10px 0 10px;margin:0 4px; background:#fafafa;}
.form-field span {margin-left:10px;}
.form-field input {width:300px;}
form {border:1px solid gray;padding:0;margin-bottom:10px;width:500px;border-radius:8px;overflow:hidden;box-shadow:4px 4px 6px rgba(0,0,0,0.2);}
.buttons {padding:20px 10px;text-align:center;}
</style>
<?php
$form->generate();
//$form->setButtons(array('save','cancel'));

*/