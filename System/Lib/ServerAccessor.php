<?php

//server调用封装
class System_Lib_ServerAccessor
{
	const SOCKETTYPE_TCP = 1;
	const SOCKETTYPE_UDP = 2;

	const TYPE_STRING = 1;
	const TYPE_BINARY = 2;

	const DEFAULT_CURL_TIMEOUT = 2;

	public static function CallServer($arrSend, &$arrRecv, &$errMsg, $server, $timeout = 8, $socketType = self::SOCKETTYPE_TCP, $type = self::TYPE_STRING, $mcId = null)
	{

	}

	/**
	 * CURL请求封装函数
	 * 若不指定host，这需要传入完整的请求路径，带http://
	 *
	 * @param string $reqURI
	 * @param array $arrayHttpParam
	 * @param string $resp
	 * @param string $errMsg
	 * @param array $server
	 * @param int $timeout
	 * @param array $extParams
	 * @return int 成功 0 没有返回 -1 返回http code不为200 -2
	 */
	public static function CallCURL($reqURI, $arrayHttpParam, &$resp, $server, $timeout = self::DEFAULT_CURL_TIMEOUT)
	{
		$host = empty($server['ip']) ? '' : $server['ip'];
		$port = empty($server['port']) ? '' : $server['port'];

		$ch = curl_init();

		foreach ($arrayHttpParam as $key => $value)
		{
			curl_setopt($ch, $key, $value);
		}

		//若不指定host，$host需要传入完整的请求路径，带http://
		if (empty($host))
		{
			$reqURL = $reqURI;
		}
		else if (empty($port))
		{
			$reqURL = 'http://' . $host  . $reqURI;
		}
		else
		{
			$reqURL = 'http://' . $host . ':' . $port . $reqURI;
		}

		curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		$res = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		if ($res == NULL)
		{
			return -1;
		}
		else if ($responseCode != "200")
		{
			return -2;
		}
		$resp = $res;
		return 0;
	}

}
