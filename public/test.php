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
			var r, res, args=[];
			var hash=window.location.hash;
			hash=hash.replace(new RegExp(/^#/),'');
			var rx=new RegExp(/([^\/]+)/g);
			if(res=hash.match(rx))
			{
				debugger;
				for(r in res)
				{
					var r1=res[r].split('-');
					if(r1.length==2) args[args.length]={'var':r1[0],'val':r1[1]};
				}
				this.route(args);
			}
		},
		
		route:function(args)
		{
			if(typeof args=='undefined') return;
			alert(args.length);
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