<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Add user</title>

	<style type="text/css">
		label {float:left; padding-right:10px;}
		.field {clear:both; text-align:right; line-height:25px;} 
		.main,.checkbox-list {float:left;}
		.s_checkbox {width:10px; height:10px}
		body {
			font-size:14px;
		}
	</style>

	<script>
		function goBack(){
			window.history.back()
		}
	</script>

	<script type="text/javascript">
		function checkForm(form)
		{
			if(!form) return false;
			var inps=form.getElementsByTagName('input');
			for(n=0,nl=inps.length;n<nl;n++)
			{
				var inp=inps[n];
				if(inp.value=='')
				{
					alert('You must fill all fields!');
					inp.focus();
					return false;
				}
			}
			return true;
		}
	</script>
</head>
<body>
	<h2>Add hosting user</h2>
	<form action="jstart.php" method="post" onsubmit="return checkForm(this);">
	<div class="main">

		<div class="field">
	    		<label for="jname">jname:</label>
	    		<input type="text" name="jname" value="" size="15"/>
		</div>

		<input type="submit" name="jstart" value="jstart" >
		<a href="/index.html">Main</a>
	</div>
	</form>
	<div class="main">
	</div>
</body>
</html>
