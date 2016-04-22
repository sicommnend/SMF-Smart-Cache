<?php
/*
Compatibility file, keeps the moans and groans down.
*/
if (!defined('SMF'))
	die('Hacking Attempt');

// No Windoze, sorry.
if ('WIN' == strtoupper(substr(PHP_OS, 0, 3)))
	fatal_error('This mod will not work on your server<br /><br />No Windows Support');

// We have to have the right PHP version. PHP >= 5.1.3
if (!function_exists('sys_getloadavg'))
	fatal_error('This mod will not work on your server<br /><br />Unsupported PHP setup');

// Now to figure out the number of CPU's
if (is_file('/proc/cpuinfo')) {
	$cpuinfo = file_get_contents('/proc/cpuinfo');
	preg_match_all('/^processor/m', $cpuinfo, $matches);
	$cpu = count($matches[0]);
}else{
	$process = @popen('sysctl -a', 'rb');
	if (false !== $process){
		$output = stream_get_contents($process);
		preg_match('/hw.ncpu: (\d+)/', $output, $matches);
		if ($matches)
			$cpu = intval($matches[1][0]);

		pclose($process);
	}
}

// Problem, No CPU's detected, better not assume. :(
if (!isset($cpu) || $cpu < 1)
	fatal_error('This mod will not work on your server<br /><br />CPU support not detected');

// Save the optimal settings. 75% lvl 2, 100% lvl 3
$mod_settings = array(
	'smartcache_l2' => 0.75*$cpu,
	'smartcache_l3' => $cpu,
	'cache_enable' => 1,
);
updateSettings($mod_settings);
?>
