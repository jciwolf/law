<?php
require_once 'db.php';
session_start();
if(isset($_SESSION['name'])){
	$text = $_POST['text'];
	$name=$_SESSION['name'];
	$type=$_POST["type"];
	if(empty($type)) $type=1;

  $r=  DB::insert('messages',array(
        'userName'=>$name,
        'hostName'=>"cao",
        '`type`'=>$type,
	    'message'=>$_POST["text"],
        'dateTime'=>date('Y-m-d H:i:s')
    ));


}
?>