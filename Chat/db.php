<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 14-3-9
 * Time: 下午3:12
 */
date_default_timezone_set("Asia/Chongqing") ;
ini_set('mbstring.internal_encoding', "UTF-8");
require_once 'meekrodb.2.2.class.php';
DB::$user = 'root';
DB::$password = '123qian.';
DB::$dbName = 'law';