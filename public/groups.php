<?php
include('webdev/start.php');
$wd=new WebDev();

//$html=$wd->getModulesListForInstallHtml();echo $html;exit;
//$wd->addModuleGroup('Web-servers','web servers');

//echo '<pre>',$wd->getJailTemplate('jail9');exit;

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$wd->updateModulesGroups($_POST);
	header('Location: /groups.php');
	exit;
}
?>
<html>
<head>
	<style>body{margin:0 auto;width:600px;} p{margin:2px;border-bottom:1px solid #f0f0f0;}</style>
</head>
<h1>Select groups for modules:</h1>
<form method="post" name="groupsUpdate">
<?php

$ml=$wd->updatePkgGroupsLink();

$groups=$wd->getPackagesGroupsList();

$pkg_groups_link=$wd->getPkgGroupsLink();

/*
$pkg=$wd->getAllPackagesList();
echo '<pre>';print_r($pkg);exit;
*/
//echo '<pre>';print_r($pkg_groups_link);exit;
//echo '<pre>';print_r($groups);
if(!empty($pkg_groups_link)) foreach($pkg_groups_link as $item)
{
	$groups_html='<option value="0">&hellip;</option>';
	if(!empty($groups)) foreach($groups as $group)
	{
		if($item['group_id']==$group['id']) $sel=' selected'; else $sel='';
		$new=($item['is_new']=='true'?' <small>(new)</small>':'');
		$groups_html.='<option value="'.$group['id'].'"'.$sel.'>'.$group['name'].'</option>';
	}
	
	$p='<p><select name="group['.$item['id'].']">'.$groups_html.'</select> '.$item['packagename'].$new.'</p>';
	echo $p;
}

//echo '<pre>';print_r($groups);
?>
	<input type="submit" value="save" name="groupsUpdate" />
</form>