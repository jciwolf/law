<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jacky Fan
 * Date: 12-11-24
 * Time: 上午9:48
 * To change this template use File | Settings | File Templates.
 */

define('MINUTE',	60);
define('HOUR',		3600);
define('DAY',		86400);
define('MONTH',	2592000);
define('YEAR',		31536000);

function debug($var = false, $showHtml = false, $outputFile = false, $showFrom = true) {
	$output = "<div style='background:#ccc;padding:5px;font-size:12px;'>\n";
	if ($showFrom) {
		$calledFrom = debug_backtrace();
		$file = $calledFrom[0]['file'];
		$output.= '<strong>' . substr($file, 1) . '</strong>';
		$output.= ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
	}
	$output.= "\n<pre style=\"background:#eee;padding:5px;font-size:12px;\">\n";

	$var = print_r($var, true);
	if ($showHtml) {
		$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
	}
	$output.= $var . "\n</pre>\n";
	$output.= "</div>\n";
	if (strtoupper(ini_get('display_errors')) == 'ON') {
		echo $output;
	}

}
