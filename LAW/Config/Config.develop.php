<?php
define('LOG_ENABLE', true); //日志开关
define('CACHE_ENABLE', true); //缓存开关

class LAW_Config_Config
{
	/**
	 * 返回全局配置
	 */

	public static function getConfig()
	{

		$dbConfig = array(
			'dsn'            => 'mysql:dbname=law;host=127.0.0.1;port=3307;charset=UTF-8',
			'username'       => 'root',
			'password'       => '123qian.',
			'initStatements' => array('SET NAMES \'utf8\';')
		);

		return array(
			'404Controller'         => 'XP_Controller_NotFound',
			'routeMapping'          => array(
				'/$'                                     => 'LAW_Controller_Site/index', //首页
				'/chat$'                                 => 'LAW_Controller_Chat/index', //首页

			),

			'dbConfig'              => array(
				'reply'                 => array(
					'name'   => 'Reply',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),

			),

            'memcache'              => array(
                'ip'      => '10.253.0.52',
                'port'    => 11211,
                'timeout' => 10
            ),
            'attachment'              => array(
                'host'      => 'http://xpdev.weituangou.mobi',
                'path'    => '/images/XP/'
            ),
			'redis'                 => array(
				'ip'      => '127.0.0.1',
				'port'    => 6379,
				'timeout' => 10
			),
		);
	}
}
