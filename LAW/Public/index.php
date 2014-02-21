<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

//session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set('Asia/Chongqing') ;
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
        require_once(PROJECT_PATH . str_replace('_', '/', $class . '.php'));
    }
}

//Let's Run

System_Lib_App::createApp(LAW_Config_Config::getConfig())->run();
