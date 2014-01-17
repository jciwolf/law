<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
echo "开始";
$document_file = '/var/ftp/pub/law/file/法律及有关问题的决定20131025/法律及有关问题的决定1/全国人民代表大会常务委员会关于进一步加强法制宣传教育的决议.doc ';
$text_from_doc = shell_exec('/usr/local/bin/antiword  '.$document_file);
echo $text_from_doc;
echo "end";
session_start();
error_reporting(E_ERROR);

ini_set("display_errors","ON");
ini_set('mbstring.internal_encoding','UTF-8');

//部署根目录
define('PROJECT_PATH', dirname(dirname(dirname(__FILE__))).'/');
//慢日志记录开关
define('SLOW_LOG_OPEN', true);
//慢日志输出目录
define('SLOW_LOG_PATH', dirname(dirname(__FILE__)).'/Log/');
define('LOG_PATH', (dirname(__FILE__)).'/logs/');
//缓存开关
//define('CACHE_ENABLE', true);

$appStartTime = microtime('true');

spl_autoload_register(array('Autoload', 'load'));

class Autoload
{
    public static function load($class)
    {
		//echo $class;
		//echo "<br>";
        require_once(PROJECT_PATH . str_replace('_', '/', $class . '.php'));
    }
}

//Let's Run
System_Lib_App::createApp(XP_Config_Config::getConfig())->run();
