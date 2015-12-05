<?php
echo "<pre>";
echo "Jail start for jail10\n";

$jname="jail10";

$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd jstatus jname=$jname", 'r');
echo "'$handle'; " . gettype($handle) . "\n";
$read = fread($handle, 2096);
echo $read;
pclose($handle);
?>
