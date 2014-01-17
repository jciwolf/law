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

		$dbConfig = array(
			'dsn'            => 'mysql:dbname=xplatform;host=10.0.0.202;port=3306;charset=UTF-8',
			'username'       => 'xdev',
			'password'       => 'ftXtKQuAIC',
			'initStatements' => array('SET NAMES \'utf8\';')
		);

		return array(
			'404Controller' => 'XP_Controller_NotFound',
			'routeMapping'  => array(
				'/$'                                     => 'XP_Controller_Site/index', //首页
				'/login/process$'                        => 'XP_Controller_Ajax_Login/process', //登录动作
				'/login/permission$'                     => 'XP_Controller_Ajax_Login/permission', //获取权限信息
				'/login'                                 => 'XP_Controller_Login/login', // 登录页面
				'/logout'                                => 'XP_Controller_Site/logout', // 退出登录
				'/weixin/callback$'                      => 'XP_Controller_Weixin/callback',
				'/weixin/callback/<PublicAccountId>'     => 'XP_Controller_Weixin/callback',
				'/weixin/send/message$'                  => 'XP_Controller_Weixin/sendMessage',
				'/weixin/create/menu$'                   => 'XP_Controller_Weixin/createMenu',
				'/baseReply/test'                        => 'XP_Controller_BaseReply/Test',
				'/distributor/index/$'                   => 'XP_Controller_Distributor/Index',
				'/distributor/add/$'                     => 'XP_Controller_Distributor/Add',
				'/publicAccount/index'                   => 'XP_Controller_PublicAccount/Index',
				'/publicAccount/info$'                   => 'XP_Controller_PublicAccountAjax/info',
				'/publicAccount/test/$'                  => 'XP_Controller_PublicAccount/Test',
				'/publicAccount/add/$'                   => 'XP_Controller_PublicAccount/AddOrUpdate',
				'/publicAccount/modify/'                 => 'XP_Controller_PublicAccount/AddOrUpdate',
				'/publicAccount/batchUpdateStatus/'      => 'XP_Controller_PublicAccountAjax/batchUpdateStatus',
				'/publicAccount/acc/<publicAccountId>/$' => 'XP_Controller_PublicAccount/accIndex',
				'/baseReply/ajaxGet'                     => 'XP_Controller_BaseReplyAjax/Get',
				'/baseReply/ajaxUpdate'                  => 'XP_Controller_BaseReplyAjax/Update',
				'/baseReply/uploader'                    => 'XP_Controller_BaseReplyAjax/uploader',
				'/publicAccount/ajaxUpdateOrSave'        => 'XP_Controller_PublicAccountAjax/UpdateOrSave',
				'/publicAccount/ajaxUpdateStatus'        => 'XP_Controller_PublicAccountAjax/UpdateStatus',
				'/publicAccount/ajaxGet$'                => 'XP_Controller_PublicAccountAjax/get',
				'/publicAccount/ajaxGetOne$'             => 'XP_Controller_PublicAccountAjax/getOne',
				'/publicAccount/checkName'               => 'XP_Controller_PublicAccountAjax/checkExist',
				'/publicAccount/checkOriginalId'         => 'XP_Controller_PublicAccountAjax/checkOriginalId',
				'/publicAccount/checkWeixin'             => 'XP_Controller_PublicAccountAjax/checkWeixinId',
				'/publicAccount/del'                     => 'XP_Controller_PublicAccountAjax/delete',
				'/publicAccount/batchDel'                => 'XP_Controller_PublicAccountAjax/batchDelete',
				'/merchant/index'                        => 'XP_Controller_Merchant/Index',
				'/merchant/modify/'                      => 'XP_Controller_Merchant/AddOrUpdate',
				'/merchant/ajaxUpdateOrSave'             => 'XP_Controller_MerchantAjax/UpdateOrSave',
				'/merchant/ajaxUpdateStatus'             => 'XP_Controller_MerchantAjax/UpdateStatus',
				'/merchant/ajaxGet$'                     => 'XP_Controller_MerchantAjax/get',
				'/merchant/ajaxGetOne$'                  => 'XP_Controller_MerchantAjax/getOne',
				'/merchant/test/'                        => 'XP_Controller_Merchant/Test',
				'/merchant/add/'                         => 'XP_Controller_Merchant/AddOrUpdate',
				'/merchant/checkEmail/'                  => 'XP_Controller_MerchantAjax/CheckEmail',
				'/merchant/checkName/'                   => 'XP_Controller_MerchantAjax/CheckName',
				'/merchant/del/'                         => 'XP_Controller_MerchantAjax/delete',
				'/merchant/batchUpdateStatus/'           => 'XP_Controller_MerchantAjax/batchUpdateStatus',
				'/merchant/batchDel'                     => 'XP_Controller_MerchantAjax/batchDelete',
				'/operator/index/$'                      => 'XP_Controller_Operator/Index',
				'/operator/add/$'                        => 'XP_Controller_Operator/Add',
				'/profile/index/$'                       => 'XP_Controller_Profile/Index',
				'/profile/add/$'                         => 'XP_Controller_Profile/Add',
				/** 单个公众号管理 **/
				'/reply/<type>'      					 => 'XP_Controller_BaseReply/reply',
				'/wxmenu/<publicAccountId>/$'            => 'XP_Controller_PublicAccount/accIndex',
				/*关键词*/
				'/keyword/index$'                        => 'XP_Controller_Keyword/index',
				'/keyword/list$'                         => 'XP_Controller_Ajax_Keyword/list',
				'/keyword/detail$'                       => 'XP_Controller_Ajax_Keyword/detail',
				//'/keyword/info$'                        => 'XP_Controller_Keyword/info',
				'/keyword/add$'                          => 'XP_Controller_Ajax_Keyword/add',
				'/keyword/update$'                       => 'XP_Controller_Ajax_Keyword/update',
				'/keyword/status$'                       => 'XP_Controller_Ajax_Keyword/status',
				'/keyword/statusmulti$'                  => 'XP_Controller_Ajax_Keyword/statusMulti',
				'/keyword/delete$'                       => 'XP_Controller_Ajax_Keyword/delete',
				'/keyword/deletemulti$'                  => 'XP_Controller_Ajax_Keyword/deleteMulti',
				'/keyword/updatesecondaryname$'          => 'XP_Controller_Ajax_Keyword/updateSecondaryName',
				/*系统账号*/
				'/systemaccount/selfinfo$'          	 => 'XP_Controller_Ajax_SystemAccount/selfInfo',
				'/systemaccount/updateselfinfo$'         => 'XP_Controller_Ajax_SystemAccount/updateSelfInfo',
				'/user/replies/<publicAccountId>/$'      => 'XP_Controller_PublicAccount/accIndex',
				'/menu/index'                            => 'XP_Controller_Menu/getMenu',
				'/menu/createMenu/<publicAccountId>$'    => 'XP_Controller_PublicAccountAjax/createMenu',

				/** 飞拓 雪佛兰项目 */
				'/fracta-chevrolet/notify/callback$'     => 'XP_Extension_FractaChevrolet_Notify/callback',
                '/fracta-chevrolet/notify/accesstoken$'  => 'XP_Extension_FractaChevrolet_Notify/accesstoken',
				'/test/<method>'                         => 'XP_Test_Feituo/handler',
				/*对外接口*/
				'/notify/accessToken'      				 => 'XP_Controller_Notify/accessToken',
			),
			'callbackHandler'	=> array(	// 对应公众号的关键词
				'gh_809e6189f2b0'	=> array(	// 飞拓-雪佛兰项目测试账户 我买票
					'积分查询'		=> array('XP_Extension_FractaChevrolet_Handler', 'queryUserPoints'),
					'账号绑定/激活'	=> array('XP_Extension_FractaChevrolet_Handler', 'bindUser'),
					//'维修保养预约'	=> array('XP_Extension_FractaChevrolet_Handler', 'userBindStatus'),
					'维修保养预约'	=> array('XP_Extension_FractaChevrolet_Handler', 'maintenanceReservation'),
				)
			),
			'callbackCommonHandler'	=>array(	// 对应的公众号都需要调用的Handler，比如特定上报日志给商家等
				'gh_809e6189f2b0'	=> array('XP_Extension_FractaChevrolet_Handler', 'reportLog'),
			),
			'dbConfig'      => array(
				'reply'          => array(
					'name'   => 'Reply',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),
				'menu'           => array(
					'name'   => 'Menu',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),
				'public_account' => array(
					'name'   => 'PublicAccount',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),
				'keyword'        => array(
					'name'   => 'Keyword',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),
				'keyword_list'   => array(
					'name'   => 'KeywordList',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),
				'message_record' => array(
					'name'   => 'MessageRecord',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),
				'module'         => array(
					'name'   => 'Module',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),
				'permission'     => array(
					'name'   => 'Permission',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),
				'system_account' => array(
					'name'   => 'SystemAccount',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				),
				'fracta_chevrolet_user' => array(
					'name'   => 'FractaChevroletUser',
					'master' => $dbConfig,
					'slave'  => $dbConfig
				)
			),
			'webservice'    => array(
				'createMenu'      => 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s',
				'getAccessToken'  => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',
				'loginPermission' => '/login/permission',
				'hasWorkOrder'=>'http://mapi.gaopeng.com/wtg/has_feedback'
			),
			/*默认微信URL、Token配置，可以个性化*/
			'wxConfig'      => array(
				'url'   => 'http://xp.gaopeng.com/weixin/callback',
				'token' => 'XPlatform_Client_GaoPeng20131104',
			),
			'secret'        => array(
				'cookie' => 'X3nd7U9r',
				'password' => 'Y3N9rx56t',
			),
			'mapiConfig'                => array(
				'id'   => '13', //CLIENT_ID_WTG
				'secret' => ':ec)if#<{*123%I1w4s&3seU'//const AES_MAPI_KEY = ':ec)if#<{*123%I1w4s&3seU'; //WTG
			),
			'image'              => array(
				'host'      => 'http://xp.gaopeng.com',
				'path'    => '/images/XP/'
			),
            'memcache'      => array(
                'ip'      => '10.0.2.159',
                'port'    => 11211,
                'timeout' => 10
            ),
            'redis'         => array(
                'ip'      => '10.0.2.157',
                'port'    => 63790,
                'timeout' => 10
            ),
		);
	}
}
