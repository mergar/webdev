<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>WebDev &mdash; Personal Web Development</title>
	<link rel="shortcut icon" href="/images/favicon.ico" />
	<script src="/js/jquery.js" type="text/javascript"></script>
	<script src="/js/scripts.js" type="text/javascript"></script>
	<link type="text/css" href="/css/reset.css" rel="stylesheet" />
	<link type="text/css" href="/css/styles.css" rel="stylesheet" />
	<link type="text/css" href="/font/webdev.css" rel="stylesheet" />
	<link type="text/css" href="/font/animation.css" rel="stylesheet" />
	<style type="text/css">html{background-color:#ddd;} .hide{display:none;}</style>
	
	<style type="text/css">.log{position:absolute;left:102%;top:10%;width:20%;height:500px;background:rgba(200,200,200,.8);
		font-size:x-small;}</style>
<!--
	<link rel="alternate" hreflang="x-default" href="/ru/">
	<link rel="alternate" hreflang="en-us" href="/en/">
-->
	<meta name="keywords" content="" />
	<meta name="description" content="WebDev &mdash; Personal Web Development" />
</head>
<body>
	<div class="main">
		<div class="body"> <div class="log">log:<br /></div>
			<h1 id="top-path">&hellip;</h1>
			<div class="menu" id="top-settings">
				<a href="" class="icon-help-circled"> Help</a>
			<!--
				<a href="#">Settings</a>
				<a href="#">Users</a>
				<a href="#">User groups</a>
			-->
				<a href="" class="icon-gift" style="display:none;"> Import</a>
				<a href="" class="icon-cubes" id="modules-menu" style="display:none;"> Modules</a>
				<a href="" class="icon-cog" id="service-menu" style="display:none;"> Services</a>
				<a href="" class="icon-users" id="users-menu" style="display:none;"> Users</a>
				<a href="" class="icon-pencil-squared" id="log-menu"> Task log</a>
			<!--	<a href="" class="icon-camera-alt"> Snapshots</a> -->
			</div>
			<div id="content">
				<table class="tbl-cnt projects"></table>
				<div id="module-info" class="hide"></div>
				<!-- <span class="icon-spin3 animate-spin" style="zoom:100%;width:16px;height:16px;line-height:16px;margin:1px 0 0 1px;"></span> -->
			</div>
			
		</div>
		
		<div class="left-menu">
			<div class="nav-back-box">
				<a id="nav-back" href="#"><strong><span class="circle">‹</span> <span class="nav-text">Projects list</span></strong></a>
			</div>
			<div class="head" id="left-menu-caption">JAILS</div>
			<ul id="left-menu"></ul>
		</div>
		
		<div class="header">
			<a class="logo" href="/"></a>
		</div>
		<div class="footer">
			<div class="mng">
				<span class="butt" id="play-but-2"><span class="ico icon-play"></span><span class="txt">RUN JAIL</span></span>
				<span class="butt" id="add-but"><span class="ico icon-plus"></span><span class="txt">ADD NEW</span></span>
				<!-- <span class="butt edit" id="edit-but"><span class="txt">EDIT</span></span> -->
				<span class="butt" id="play-but"><span class="ico icon-play"></span><span class="txt">RUN</span></span>
				<span class="butt" id="stop-but"><span class="ico icon-stop"></span><span class="txt">STOP</span></span>
				<span class="butt" id="del-but"><span class="ico icon-trash"></span><span class="txt">DELETE</span></span>
				<span class="butt" id="exp-but"><span class="ico icon-gift"></span><span class="txt">EXPORT</span></span>
				<span class="butt" id="move-but"><span class="ico icon-reply-all"></span><span class="txt">MOVE</span></span>
				<span class="butt" id="snap-but"><span class="ico icon-camera-alt"></span><span class="txt">SNAPSHOT</span></span>
			</div>
		</div>
	</div>
	<div id="overlap"></div>
	<div id="window"><div id="window-box">
		<span id="close-but">×</span>
		<div id="window-content"></div>
		<div id="buttons">
			<span id="ok-but" class="gray-icon-place"><span class="gray-icon ok"></span></span>
			<!-- <span id="cancel-but" class="gray-icon-place"><span class="gray-icon cancel"></span></span> -->
		</div>
	</div></div>
	<div id="project-settings" class="hide">
		<h1>Add new project</h1>
		<h2>Project Settings</h2>
		<form class="win" method="post">
			<p>
				<span class="field-name">Project name:</span>
				<input type="text" name="name" value="" />
			</p>
		</form>
	</div>
	<div id="jails-settings" class="hide">
		<h1>Add new jail</h1>
		<h2>Jail Settings</h2>
		<form class="win" method="post">
			<p>
				<span class="field-name">Jail name:</span>
				<input type="text" name="name" value="" />
			</p>
			<p>
				<span class="field-name">Hostname (FQDN):</span>
				<input type="text" name="hostname" value="" />
			</p>
			<p>
				<span class="field-name">IP address:</span>
				<input type="text" name="ip" value="" />
			</p>
			<p>
				<span class="field-name">Description:</span>
				<textarea type="text" name="description" value="" ></textarea>
			</p>
		</form>
	</div>
	<div id="modules-settings" class="hide">
		<h1>Add new module</h1>
		<h2>Module install</h2>
		<div class="scrolled">
			<form class="win" method="post" id="modulesForInstall">
				<p>&hellip;</p>
			</form>
		</div>
	</div>
	<div id="jail-settings" class="hide">
		<h1>Edit jail</h1>
		<h2>Jail Settings</h2>
		<form class="win" method="post">
			<p>
				<span class="field-name">Jail name:</span>
				<input type="text" name="name" value="" />
			</p>
			<p>
				<span class="field-name">Hostname (FQDN):</span>
				<input type="text" name="hostname" value="" />
				<small class="astart-warn">— available on disabled jail</small>
			</p>
			<p>
				<span class="field-name">IP address:</span>
				<input type="text" name="ip" value="" />
			</p>
			<p>
				<span class="field-name">Description:</span>
				<textarea type="text" name="description" value="" ></textarea>
			</p>
			<p>
				<span class="field-name">Autostart:</span>
				<input type="checkbox" name="astart" id="astart-id" /><label for="astart-id"> Autostart jail at system startup</label>
			</p>
		</form>
	</div>
	<div id="users-settings" class="hide">
		<h1>User add</h1>
		<h2>User Settings</h2>
		<form class="win" method="post">
			<p>
				<span class="field-name">User login:</span>
				<input type="text" name="login" value="" />
			</p>
			<p>
				<span class="field-name">Full name:</span>
				<input type="text" name="fullname" value="" />
			</p>
			<p>
				<span class="field-name">Password:</span>
				<input type="password" name="password" value="" />
			</p>
			<p>
				<span class="field-name">Repeat password:</span>
				<input type="password" name="password1" value="" />
			</p>
		</form>
	</div>
	<div id="exports-list" class="hide">
		<h1>Exported jails list</h1>
		<h2>Import jails in project</h2>
		<div class="exp-list"></div>
	</div>
	<div class="spinner hide" id="spinner">
		<div class="spinner-container container1">
			<div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div>
		</div>
		<div class="spinner-container container2">
			<div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div>
		</div>
		<div class="spinner-container container3">
			<div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div>
		</div>
	</div>
</html>