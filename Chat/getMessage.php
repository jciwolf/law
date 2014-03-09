<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 14-3-9
 * Time: 下午3:12
 */

require_once 'db.php';
if(!empty($_POST["userName"]))
{
    $r=DB::query("select * from messages where userName=%s",$_POST["userName"]);
    if(!empty($r))
    {
        foreach ($r as $row) {
            echo "Name: " . $row['dateTime'] . "\n";

        }
    }
}
?>