<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Test page</title>
	<link href="/images/favicon.ico?" rel="shortcut icon" type="image/x-icon" />
	<script src="/js/jquery.js" type="text/javascript"></script>
	<script type="text/javascript">
	var router={
		start:function()
		{
			var hash=window.location.hash;
			if(hash=='') hash='#';
			var rx=new RegExp(/#([^\/]+)/g);
			if(res=hash.match(rx))
			{
				debugger;
				alert(res);
			}
		}
	}
	router.start();
	</script>
<!--
	<script src="/js/scripts.js" type="text/javascript"></script>
	<script src="/js/lang/ru.js" type="text/javascript"></script>
	<link type="text/css" href="/css/reset.css" rel="stylesheet" />
	<link type="text/css" href="/css/styles.css" rel="stylesheet" />
	<link type="text/css" href="/font/webdev.css" rel="stylesheet" />
	<link type="text/css" href="/font/animation.css" rel="stylesheet" />
-->
	<style type="text/css">html{background-color:#ddd;} .hide{display:none;}</style>
	
	<style type="text/css">.log{position:absolute;left:102%;top:10%;width:20%;height:500px;background:rgba(200,200,200,.8);
		font-size:x-small;}</style>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>


</body>
</html>