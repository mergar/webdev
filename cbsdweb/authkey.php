<!doctype html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<title>CBSD Dashboard</title>
<script type="text/javascript" src="jquery.js"></script>
<style type="text/css">
        table td{
                min-width: 25px;
        }

        #editor{
                background: none repeat scroll 0 0 #FFFFFF;
                border: 1px solid #FF0000;
                display: none;
                padding: 10px;
                position: absolute;
                text-align: center;
        }
</style>

<style type="text/css">
                body {font-size:80%;font-family:Tahoma,'Sans-Serif',Arial;}
                label {float:left; padding-right:10px;}
                .field {clear:both; text-align:right; line-height:25px;}
                .main,.checkbox-list {float:left;}
</style>

</head>
<body>
<a href="javascript:location.reload(true)">[ Refresh Page ]</a>
<br>

<form action="authkey_add.php" method="post">

<table class="images">
 <thead>
  <tr>
   <th>name</th>
   <th>key</th>
   <th></th>
  </tr>
 </thead>
<tbody align="center">

<?php
function fetch_key($dbfilepath)
{
	$stat = file_exists($dbfilepath);
	$str = "";
	$idx = 0;

	if (!$stat) {
		$nodetable .= "<tr><td bgcolor=\"#CCFFCC\">$allnodes</td><td colspan=10><center>$dbfilepath not found</center></td></tr>";
		return 0;
	}

	$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
	$authkeyres = $db->query('SELECT idx,name,authkey FROM authkey;');

	if (!($authkeyres instanceof Sqlite3Result)) {
		echo "Error: $dbfilepath";
		$gwinfo="unable to fetch authkey";
	} else {
		while ($row = $authkeyres->fetchArray()) {
			list( $idx , $name, $authkey ) = $row;
$str .= <<<EOF
 <tr>
  <td><div class="field"><input type="text" name="authkey_name" size="20" value="$name"></div></td>
  <td><div class="field"><input type="text" name="authkey" size="60" value="$authkey"></div></td>
  <td><a href="authkey_remove.php?idx=$idx">remove</a></td></a>
 </tr>
EOF;

		}
	}

	$idx++;

// add empty string
$str .= <<<EOF
 <tr>
  <td><div class="field"><input type="text" name="authkey_name" size="20" value=""></div></td>
  <td><div class="field"><input type="text" name="authkey" size="60" value=""></div></td>
  <td><input type="submit" name="create" value="Add" >
 </tr>
EOF;


	echo $str;

	$db->close();
}


/// MAIN
require('cbsd.php');
require('nodes.inc.php');
echo "<strong>SSH public key:</strong>";

if (!extension_loaded('sqlite3')) {
	if (!dl('sqlite3.so')) {
		echo "No such sqlite3 extension";
		exit(1);
	}
}

fetch_key("/var/db/webdev/authkey.sqlite");

?>
</div>
</table>
</form>
</body>
</html>
