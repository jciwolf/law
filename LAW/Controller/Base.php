<?

/**
 * 控制器基类
 * @author Nick.He (hehaipeng@group-net.cn)
 * @date 2013.06.20
 */

class XP_Controller_Base extends System_Lib_Controller
{
	protected $layoutName = 'XP_Layout_System';
	protected $accountId;
	protected $userId;

	/**
	 * 构造函数，必须执行
	 */
	public function __construct()
	{
		System_Lib_App::app()->recordRunTime('baseAction begin');
	}

	/**
	 * 析构函数，必须执行
	 */
	public function __destruct()
	{
		System_Lib_App::app()->recordRunTime('baseAction end');
	}

	/**
	 * 全局beforeFilter，可选执行
	 */
	public function beforeFilter($action)
	{
		System_Lib_App::app()->recordRunTime('beforeFilter begin');
		if (!$this->checkLogin()) System_Lib_App::app()->redirect('/login?redirectUrl=' . urlencode(System_Lib_App::app()->request()->getUri()));
		if (!empty($_GET['accountId'])) {
			$this->accountId = $_GET['accountId'];
		} else {
			//debug("current request don't have accountId");
			//System_Lib_App::redirect("/", false);
		}
		if(!empty($_SESSION['id']))
		{
			$this->userId=$_SESSION['id'];
		}
		System_Lib_App::app()->recordRunTime('beforeFilter end');
	}

	/**
	 * 全局beforeRender，可选执行
	 */
	public function _beforeRender($action)
	{
		$this->assignData('referer', System_Lib_Request::getReferer());
		$this->assignData('S', $this->state_S);
	}

	/********** 公共方法 **********/

	protected function _retErr500($errCode, $errMsg = '')
	{
		if (!empty($errCode)) {
			$errMsg = WTG_Lib_Error::getErrMsg($errCode);
		}
		$this->assignData('errorMsg', $errMsg);

		return $this->render('Err500');
	}

	protected function _retErr500ByMapi()
	{
		self::_retErr500(WTG_Lib_Error::MAPI_DATA_ERROR);

	}

	protected function checkLogin()
	{
		$r = false;
		try {
			/*cookie转session*/
			if (empty($_SESSION['id']) && !empty($_COOKIE['LoginInfo']['id'])) {
				$secret = System_Lib_App::app()->getConfig('secret');
				$id     = System_Lib_AES::decrypt($_COOKIE['LoginInfo']['id'], $secret['cookie']);
				$name   = System_Lib_AES::decrypt($_COOKIE['LoginInfo']['name'], $secret['cookie']);
                $email  = System_Lib_AES::decrypt($_COOKIE['LoginInfo']['email'], $secret['cookie']);
				$token  = $_COOKIE['LoginInfo']['token'];
				$_token = md5(sprintf('%s|%s|%s|%s', $id, $name, $email,$secret['cookie']));

				if ($_token == $token) {
					$_SESSION['id']    = $id;
					$_SESSION['name']  = $name;
                    $_SESSION['email'] = $email;
				}
			}

			/*判断用户权限是否包含当前URL*/
			if (!empty($_SESSION['id'])) {
				/*再次调取用户权限*/
				if (empty($_SESSION['permission'])) {
					$data       = sprintf('{"accountId":"%s","type":"%s"}', $_SESSION['id'], XP_Lib_Enum::AccountType_SystemAccount);
					$webservice = System_Lib_App::app()->getConfig('webservice');
					$result     = XP_Lib_Utility::urlRequest($webservice['loginPermission'], $data);
					if (isset($result['errcode']) && $result['errcode'] == '0') {
						$_SESSION['permission'] = $result['data'];
						//XP_Lib_Utility::log(json_encode($result['data']));
					}
				}
				foreach ($_SESSION['permission'] as $item) {
					//XP_Lib_Utility::log('a='.System_Lib_App::app()->request()->getUri().'?'."\n".'b='.$item['id'].$item['url'].'?'."\n".strripos(System_Lib_App::app()->request()->getUri().'?',$item['url'].'?'));
					if (true || !empty($item['url']) && strripos(System_Lib_App::app()->request()->getUri() . '?', $item['url'] . '?') !== false) {
						$r = true;
						break;
					}
				}
			}
		} catch (Exception $e) {}
		return $r;
	}

	/**
	 * 判断用户是否关注，如果没有关注，跳转到关注引导页面
	 */
	public function _checkContactState($contactState)
	{
		if (empty($contactState)) {

			$url = System_Lib_App::app()->request()->getUri();
			WTG_Lib_Tcss::redirect('/site/contact?url=' . urlencode($url));
		}
	}
}

