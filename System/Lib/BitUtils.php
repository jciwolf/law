<?php
/**
 * 处理位串的静态工具类
 * @author mcfogwang
 */
final class System_Lib_BitUtils
{
	private function __construct() {}
	/**
	 * 获取$str中的第$index位【0起始】
	 * @param   String  $str    位串
	 * @param   Integer $index  位索引
	 * @throws  Exception $index超出范围时
	 * @return  Interger 1 or 0
	 */
	public static function get($str, $index) {
		$i = intval($index/8);
		if($i < 0 || $i >= strlen($str)) throw new Exception('Index out of range');
		$j = $index % 8;
		return ord($str[$i]) >> (7 - $j) & 1;
	}

	/**
	 * 设置$str中的第$index位
	 * @param   String  $str    位串
	 * @param   Integer $index  位索引
	 * @param   Mixed   $is_set 为真时设1，否则设0
	 * @throws  Exception $index超出范围时
	 * @return  String  改变后的位串
	 */
	public static function set($str, $index, $is_set) {
		$i = intval($index/8);
		if($i < 0 || $i >= strlen($str)) throw new Exception('Index out of range');
		$j = $index % 8;
		$v = ord($str[$i]);
		$str[$i] = chr($is_set ? $v|(1<<(7-$j)) : $v & (0xFF - (1<<(7-$j))));//$is_set为真时设1，否则设0
		return $str;
	}

	/**
	 * 创建一个指定长度的空白位串
	 * @param  Integer $length 位串长度，不足8的倍数时会向上补满
	 * @param  Mixed   $is_set 为真时填充1，否则填充0(默认填0)
	 * @return String  全0或全1的位串
	 */
	public static function init($length, $is_set = false)
	{
		$length = ceil($length / 8);
		$c = chr($is_set ? 0xFF : 0);
		return str_repeat($c, $length);
	}

	/**
	 * 将位串整理为01组成的字符串
	 * @param  String  $str  位串
	 * @param  String  $char 字母表
	 * @return String
	 */
	public static function toString($str, $char = '01') {
		$result = '';
		for($i = 0; $i < strlen($str); $i++)
		{
			$c = ord($str[$i]);
			for($j=7; $j>=0; $j--)
			{
				$result .= $char[$c >> $j & 1];
			}
		}
		return $result;
	}

	/**
	 * 由01组成的字符串制作对应的位串
	 * @param  String  $str  字符串
	 * @param  String  $char 反向字母表
	 * @return String
	 */
	public static function fromString($str, $char = array('0'=>0,'1'=>1)) {
		$result = '';
		$buf = 0;
		$cur = 0;
		for($i = 0; $i < strlen($str); $i++)
		{
			if(!isset($char[$str[$i]])) throw Exception('Illegal string format');
			$b = $char[$str[$i]];
			$buf = $buf<<1 | $b;
			$cur++;
			if($cur==8)
			{
				$cur = 0;
				$result .= chr($buf);
				$buf = 0;
			}
		}

		if($cur!=0)
		{
			$buf = $buf<<(8-$cur);
			$result .= chr($buf);
		}
		return $result;
	}
}