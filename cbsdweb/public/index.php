<?php
require_once("auth.php");
?>


<?php
require_once("auth.php");
?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Control Panel</title>

  <script src="./jquery/jquery.js" type="text/javascript"></script>
  <script src="./jquery/jquery-ui.custom.js" type="text/javascript"></script>
  <script src="./jquery/jquery.cookie.js" type="text/javascript"></script>

  <link href="./src/skin/ui.dynatree.css" rel="stylesheet" type="text/css">
  <script src="./src/jquery.dynatree.js" type="text/javascript"></script>

  <link href="/css/styles.css" rel="stylesheet" type="text/css">

  <!-- (Irrelevant source removed.) -->

   <style type="text/css">
    #tree {
	vertical-align: top;
	width: 250px;
    }
    iframe {
	border: 1px dotted gray;
    }
  </style>
  <!-- Add code to initialize the tree when the document is loaded: -->
  <script type="text/javascript">
  $(function(){
    // Attach the dynatree widget to an existing <div id="tree"> element
    // and pass the tree options as an argument to the dynatree() function:
    $("#tree").dynatree({
//          autoCollapse: true,
      minExpandLevel: 1,
//          persist: true,
      onPostInit: function(isReloading, isError) {
        this.reactivate();
      },
      onActivate: function(node) {
        // Use <a> href and target attributes to load the content:
        if( node.data.href ){
          // Open target
          window.open(node.data.href, node.data.target);
          // or open target in iframe
//                $("[name=contentFrame]").attr("src", node.data.href);
        }
      }
    });
  });
  </script>
</head>
<body class="example">
	<div class="head-div">
		<h1>Control panel</h1>
		<p class="description">
			Please visit <a href="https://www.bsdstore.ru" target="_blank">www.bsdstore.ru</a> and <a href="https://telegram.me/cbsdofficial" target="_blank">CBSD Telegram channel</a> for support; 
		</p>
		<br>
	</div>
	
	<div class="body-div">
	  <table border=2 class="body-table">
	  <colgroup>
		<col width="300px" valign="top">
		<col width="90%">
	  </colgroup>
	  <tr  valign="top">
		<td>
		  <!-- Add a <div> element where the tree should appear: -->
		  <div id="tree">
		  <ul>
			<li class="expanded folder">Summary
			<ul>
			  <li><a href="dashboard.php" target="contentFrame">Dashboard</a>
			</ul>
			<li class="expanded folder">Virtual
			<ul>
			  <li><a href="jlist.php" target="contentFrame">Jail containers</a>
			  <li><a href="blist.php" target="contentFrame">Bhyve VMs</a>
			  <li><a href="xlist.php" target="contentFrame">XEN VMs</a>
			  <li><a href="vlist.php" target="contentFrame">VirtualBox VMs</a>
			</ul>
			<li class="expanded folder">Resources
			<ul>
			  <li><a href="nodes.php" target="contentFrame">Nodes</a>
			  <li><a href="vpnet.php" target="contentFrame">Virtual Private Network</a>
			  <li><a href="authkey.php" target="contentFrame">Authkey</a>
			  <li><a href="repo.php" target="contentFrame">Repository</a>
			  <li><a href="bases.php" target="contentFrame">Bases</a>
			  <li><a href="sources.php" target="contentFrame">Sources</a>
			</ul>
			<li class="expanded folder">Instances
			<ul>
			  <li><a href="instance_jail.php" target="contentFrame">Jails</a>
			  <li><a href="instances_vm.php" target="contentFrame">Bhyves</a>
			</ul>
			<li class="expanded folder">System
			<ul>
			  <li><a href="system/userlist.php" target="contentFrame">Host Userlist...</a>
			  <li><a href="system/adduser.php" target="contentFrame">Host Add User...</a>
			  <li><a href="settings.php" target="contentFrame">Settings...</a>
			  <li><a href="taskls.php" target="contentFrame">Task Log</a>
			</ul>
		  </ul>
		  <ul>
			<!-- Log out option -->
			Logged as: <?php echo $_SESSION["username"]; ?>
			<form class="controlbox" name="log out" id="logout" action="login.php" method="POST">
				<input type="hidden" name="op" value="logout"/>
				<input type="hidden" name="username"value="<?php echo $_SESSION["username"]; ?>" />
				<input type="submit" value="log out"/>
			</form>
		  </ul>
		  </div>
		</td>

		<td>
		  <iframe src="dashboard.php" name="contentFrame" width="100%" height="600"
			  scrolling="yes" marginheight="0" marginwidth="0" frameborder="0">
			<p>Your browser does not support iframes</p>
		  </iframe>
		</td>
	  </tr>
	  </table>
	</div> <!-- body-div -->


  <!-- (Irrelevant source removed.) -->
</body>
</html>

