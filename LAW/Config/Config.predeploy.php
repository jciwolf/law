<?php
	define('LOG_ENABLE', true); //日志开关
	define('CACHE_ENABLE', true); //缓存开关

	class XP_Config_Config
	{
		/**
		 * 返回全局配置
		 */
		public static function getConfig()
		{
			return array(
				'404Controller'		=> 'XP_Controller_NotFound',
				'routeMapping'		=> array(
					'/$'												=> 'XP_Controller_Index/index', //首页
				),
				'serverHost'		=> 'http://xp.gaopeng.com',
				'weixinConfig'		=> array(
					'appId'			=> 'wx8e4c7364a259befb', //wx78f27913e0c901d1  wx8e4c7364a259befb
					'appSecret'		=> '2f5410c37356ae35d93a35215ba40a79', //d82a9258da49ceee7ced1f1a12c01e6c  2f5410c37356ae35d93a35215ba40a79
					'token'			=> '458ErfdlvfAeof7rR2oe',
				),
                'dbConfig'			=> array(
                    'system_user'	=> array(
                        'name'   	=> 'SystemUser',
                        'master' 	=> array(
                            'dsn'			=> 'mysql:dbname=xplatform;host=10.0.2.158;port=3306;charset=UTF-8',
                            'username'		=> 'dev',
                            'password'		=> 'ftXtKQuAIC',
                            'initStatements'=> array('SET NAMES \'utf8\';')
                        ),
                        'slave'		=> array(
                            'dsn'			=> 'mysql:dbname=xplatform;host=10.0.2.158;port=3306;charset=UTF-8',
                            'username'		=> 'dev',
                            'password'		=> 'ftXtKQuAIC',
                            'initStatements'=> array('SET NAMES \'utf8\';')
                        )
                    ),
                ),
				'memcache'		=> array(
					'ip'		=> '10.0.2.159',
					'port'		=> 11211,
					'timeout'	=> 10
				),
				'redis'			=> array(
					'ip'		=> '10.0.2.157',
					'port'		=> 63790,
					'timeout'	=> 10
				),
			);
		}
	}
