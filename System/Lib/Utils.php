<?php
class System_Lib_Utils
{
	const SORT_TYPE_DESC = '<';
	const SORT_TYPE_ASC = '>';

	public static function orderObj($objs, $name, $sortType = self::SORT_TYPE_DESC)
	{
		usort($objs, create_function('$a, $b', "return (\$a->{$name} {$sortType} \$b->{$name});"));
		return $objs;
	}

	public static function filterObj($objs, $name, $val)
	{
		foreach ($objs as $obj)
		{
			if ($obj->$name == $val)
			{
				return $obj;
			}
		}
		return false;
	}

	public static function getValuesFromObjs($objs, $key)
	{
		$tmp = array();
		foreach ($objs as $obj)
		{
			$tmp[] = $obj->$key;
		}
		return $tmp;
	}

	public static function cn_strlen($string, $charset = 'utf-8')
	{
	    return mb_strlen($string, $charset);
	}

	public static function cn_truncate($string, $strlen = 20, $etc = '...', $keep_first_style = false, $charset = 'utf-8')
	{
	    $slen = mb_strlen($string, $charset);
	    if ($slen > $strlen+2)
	    {
	        $tstr = mb_substr($string, 0, $strlen, $charset);
	        $matches = array();
	        $mcount = preg_match_all("/[\x{4e00}-\x{9fa5}]/u", $tstr, $matches);
	        unset($matches);
	        $offset = ($strlen - $mcount) * 0.35;//0;//intval((3*mb_strlen($tstr,$charset)-strlen($tstr))*0.35);
	        return preg_replace('/\&\w*$/', '', mb_substr($string, 0, $strlen + $offset, $charset)) . $etc;
	    }
	    else
	    {
	        return $string;
	    }
	}

	public static function cashFormat($cash)
	{
	    return rtrim(rtrim($cash,'0'),'.');
	}

	public static function cashCN($cash)
	{
	    $yi = intval($cash/100000000);
	    $wan = intval($cash%100000000/10000);
	    $yuan = intval($cash%10000);
	    $result ='';
	    if($yi>0)
	    {
	        $result .= "<span>$yi</span>亿";
	    }
	    if($wan>0)
	    {
	        $result .= "<span>$wan</span>万";
	    }
	    $result .= "<span>$yuan</span>元";
	    return $result;
	}

	public static function toUTF8($text)
	{
	    return @iconv("GBK","UTF-8//ignore",$text);
	}

	public static function toGBK($text)
	{
	    return @iconv("UTF-8","GBK//ignore",$text);
	}

	public static function getAntiCSRFToken($aConf = array())
	{
		$TPHP_CSRF_TOKEN_SALT = 5381;
		$TPHP_CSRF_TOKEN_MD5_KEY = 'tencentQQVIP123443safde&!%^%1282';

		$hash  = array();
		$ASCIICode = 0;
		$salt   =  isset($aConf['salt']) ? $aConf['salt'] : $TPHP_CSRF_TOKEN_SALT;
		$skey   =  isset($aConf['skey']) ? $aConf['skey'] : @$_COOKIE["skey"];
		$md5key =  isset($aConf['md5key']) ? $aConf['md5key'] : $TPHP_CSRF_TOKEN_MD5_KEY;
		$hash[] = ($salt << 5);
		for($i=0, $len = strlen($skey); $i<$len; ++$i)
		{
			$ASCIICode = ord(substr($skey,$i,1));
			$hash[] = (($salt << 5) + $ASCIICode);
			$salt = $ASCIICode;
		}
		return md5(implode('', $hash) . $md5key);
	}

	public static function htmlspecialchars($string)
	{
		return htmlspecialchars($string, ENT_QUOTES);
	}

	public static function getExcuteTime($startTime)
	{
		return number_format(microtime(true) - $startTime, 3, '.', '');
	}
	
	/**
	 * 根据概率取随机数的算法（仅抽取1个）
	 * 用法：
	 * $proArr = array(10,20,30,40);
	 * $result = pro_rand($proArr);
	 * echo '你抽到的$proArr数组索引是'. $result. '，其预设概率数是'. $proArr[$result];
	 * @param array $proArr 概率数组。格式为array('A'=>10, 'B'=>40, 'C'=>50)，或者array(10,40,50)。数组的每个键值（value）必须为大于1的整数；所有数组键值（value）加起来即为 其总概率精度
	 * @return mixed $result 结果，将返回抽取到的概率数组索引值。
	 */
	public static function pro_rand( $proArr )
	{
		$result = '';
	
		//概率数组的总概率精度
		$proSum = array_sum($proArr);
	
		/*概率数组循环。算法为youd提供，以下为youd的口述流程（假设为array(100,200,300，400)）：
		 开始是从1,1000这个概率范围内筛选第一个数是否在他的出现概率范围之内，
		如果不在，则将概率空间，也就是k的值减去刚刚的那个数字的概率空间，在本例当中就是减去100，也就是说第二个数是在1，900这个范围内筛选的。
		我想应该很容易理解，这样筛选到最终，总会有一个数满足要求
		（比如说前三个都不幸成为了非Luck Num，那么k已经-100-200-300=400了，那么最后一个数无论如何也会满足要求的）。
		相当于拿东西，第一个不是，第二个不是，第三个还不是，那最后一个一定是。
		这个算法的优点是，对于没有概率重叠的数字进行筛选，最多只需要遍览一次数组就足够了。程序简单，效率高
		*/
		foreach ( $proArr as $key => $proCur ){
			$randNum = mt_rand(1, $proSum);
			if( $randNum <= $proCur ){
				$result = $key;
				break;
			}else{
				$proSum -= $proCur;
			}
		}
	
		return $result;
	
	}
	
	
	/**
	 * 根据概率取随机数的算法（抽取多个，并且不重复）。
	 * 依赖于函数pro_rand
	 * 用法：
	 * $proArr = array(10,20,30,40);
	 * $result = pro_rand_unique_multi($proArr, 2);
	 * var_export($result);
	 * @param array $proArr 概率数组。格式为array('A'=>10, 'B'=>40, 'C'=>50)，或者array(10,40,50)。数组的每个键值（value）必须为大于1的整数；所有数组键值（value）加起来即为 其总概率精度
	 * @param integer $num 指定抽取数目。数值不能大于概率数组的个数
	 * @return array $result 结果，将返回指定抽取数目的概率数组索引值
	 */
	public static function pro_rand_unique_multi( $proArr, $num = 1 )
	{
		$result = array();
		if( $num > count($proArr) ){
			trigger_error('The stack number of Probability Array is GREATER THAN you set!', 256);
		}
	
		while(1){
			if($num < 1){
				break;
			}
			$curResult = self::pro_rand($proArr);
			$result[] = $curResult;
			//重置总概率精度，有待概率论验证
			unset($proArr[$curResult]);
			$num -= 1;
		}
	
		return $result;
	
	}

	public static 	function system($cmd, $maxSize = 1024)
	{
		$fp = popen($cmd, 'r');
		$str = '';
		$num = 0;
		while(!feof($fp) && $num < $maxSize) {
			$str .= fread($fp, 1024);
			$num += 1024;
		}
		fclose($fp);
		return $str;
	}

}