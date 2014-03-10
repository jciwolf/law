<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 14-3-9
 * Time: 下午3:12
 */

require_once 'db.php';
	if(!empty($_POST["d"]))
	$time=$_POST["d"];
	if(empty($time)) $time='2000-10-10';
	$r=DB::query("select * from messages where dateTime>%s order by dateTime",$time);
	if(!empty($r))
	{
		echo json_encode($r);
	}

?>