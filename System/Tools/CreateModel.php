<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

class System_Tools_CreateModel
{

	public static function create()
	{
		$server = '115.182.42.81:3307';
		$username = 'alvin';
		$password = 'qaz12wsx';
		$database_name = 'mobile_test';
		$table = 'retry_failed_log';
		$configKey = 'retry_failed_log';

		$con = mysql_connect($server, $username, $password);
		mysql_select_db($database_name, $con);

		$result = mysql_query('desc ' . $table , $con);

		$fields = array();
		$key = null;
		while ($rs = mysql_fetch_assoc($result))
		{
			if ($rs['Key'] === "PRI")
			{
				$key = $rs['Field'];
			}
			$fields[$rs['Field']] = (strpos($rs['Type'], 'int(') !== false) ? 'int' : 'string';
		//	var_dump($rs['Field']);
		//	var_dump($rs['Type']);
		//	var_dump($fields[$rs['Field']]);
		//	var_dump("\n\n\n");
		}
		$className = implode('', array_map('ucwords', explode('_', rtrim($table, 's'))));

		function echon($str)
		{
			echo $str . "\n";
		}

		function getConst($name)
		{
			return strtoupper(preg_replace("/^_?[is]{1}_/", '', preg_replace("/[A-Z]/", "_$0", $name)));
		}

		function getProperty($name)
		{
			return lcfirst(preg_replace("/^[is]{1}([A-Z]{1})/", "$1", $name));
		}

		//start print
		echon('<?php');
		echon('//Create by MySQL Model generator');
		echon('');
		echon("class MF_Model_{$className} extends System_Lib_DataObject");
		echon('{');
		foreach ($fields as $name => $type)
		{
			echon("\tconst " . getConst($name) . " = '" . getProperty($name) . "';");
		}
		echon('');

		foreach ($fields as $name => $type)
		{
			echon("\tpublic $" . getProperty($name) . ';');
		}
		echon('');

		echon("\tpublic static function getMapping()");
		echon("\t{");
		echon("\t\treturn array(");
		echon("\t\t\t'table' => '{$table}',");
		echon("\t\t\t'key' => self::" . getConst($key) . ",");
		echon("\t\t\t'columns' => array(");
		$str = '';
		foreach ($fields as $name => $type)
		{
			$str .= "\t\t\t\tself::" . getConst($name) . " => '{$name}',\n";
		}
		echon(rtrim($str, ",\n"));
		echon("\t\t\t),");
		echon("\t\t\t'columnTypes' => array(");
		$str = '';
		foreach ($fields as $name => $type)
		{
			$str .= "\t\t\t\tself::" . getConst($name) . " => '{$type}',\n";
		}
		echon(rtrim($str, ",\n"));
		echon("\t\t\t)");
		echon("\t\t);");
		echon("\t}");

		echon('');
		echon("\t/**");
		echon("\t *");
		echon("\t * @return <array>");
		echon("\t */");
		echon("\tpublic static function getSourceConfig()");
		echon("\t{");
		echon("\t\t\$config = System_Lib_App::app()->getConfig('dbConfig');");
		echon("\t\treturn \$config['{$configKey}'];");
		echon("\t}");

		echon('');
		echon("\t/**");
		echon("\t *");
		echon("\t * @return System_Lib_MysqlAccessor");
		echon("\t */");
		echon("\tpublic static function dataAccess()");
		echon("\t{");
		echon("\t\treturn System_Lib_MysqlAccessor::useModel(get_class());");
		echon("\t}");

		echon('');
		echon("\t/**");
		echon("\t *");
		echon("\t * @return <string>");
		echon("\t */");
		echon("\tpublic static function getDataAccessName()");
		echon("\t{");
		echon("\t\treturn 'System_Lib_MysqlAccessor';");
		echon("\t}");

		echon("}");

		//end print
	}
}

System_Tools_CreateModel::create();