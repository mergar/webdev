<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CBSD Project</title>
	<link type="text/css" href="./css/all.css" rel="stylesheet" />
	<style>
		body {
			font-size:14px;
		}
	</style>
</head>
<body>
<a href="javascript:location.reload(true)">[ Refresh Page ]</a>
<script>
</script>

<?php
require('cbsd.php');
?>
<table class="images">
    <thead>
    <tr>
	<th>arch</th>
	<th>target_arch</th>
	<th>ver</th>
    </tr>
</thead><tbody>
<?php

$fp=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd basels", "r");
if ($fp) {
	while(!feof($fp)) {

		echo "<tr>";
		$data=fgets($fp, 2048); 

		if (strlen($data)>3 ) {
			list($base, $arch, $target_arch, $ver ) = explode('_', "${data}");
			if (!isset($arch)) $arch="unknown";
			if (!isset($target_arch)) $targeT_arch="unknown";
			if (!isset($ver)) $ver="unknown";

			$str = <<<EOF
			<td>$arch</td>
			<td>$target_arch</td>
			<td>$ver</td>
			</tr>
EOF;
		echo $str;
		}

	} 
}

echo "\n";