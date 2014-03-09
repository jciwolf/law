<?php
require_once 'db.php';
session_start();
if(isset($_SESSION['name'])){
	$text = $_POST['text'];
	$name=$_SESSION['name'];

  $r=  DB::insert('messages',array(
        'userName'=>$name,
        'hostName'=>"kdf",
        'type'=>1,
        'dateTime'=>date('Y-m-d H:m:s')
    ));



	//$fp = fopen("log.html", 'a');
	//fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
	//fclose($fp);
}
?>