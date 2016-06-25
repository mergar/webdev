<?php
require('cbsd.php');

if (!isset($_POST['astart'])) {
	$astart="0";
} else {
	$astart = "1";
}

echo $astart;
?>
