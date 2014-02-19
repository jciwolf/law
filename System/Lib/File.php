<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jacky Fan
 * Date: 13-1-8
 * Time: 上午9:28
 * To change this template use File | Settings | File Templates.
 */

class System_Lib_File {

	public static function copy($src, $tar)
	{
		if (!file_exists($src)) {
			return false;
		}
		$dir = dirname($tar);
		if (!file_exists($dir)) {
			if (!self::createDir($dir)) {
				return false;
			}
		}
		return copy($src, $tar);
	}

	public static function save($content, $file)
	{
		$dir = dirname($file);
		if (!file_exists($dir)) {
			if (!self::createDir($dir)) {
				return false;
			}
		}
		return file_put_contents($file, $content);
	}

	public static function load($file)
	{
		if (!file_exists($file)) {
			return false;
		}
		return file_get_contents($file);
	}

	public static function createDir($dir)
	{
		if (!file_exists(dirname($dir))) {
			self::createDir(dirname($dir));
		}
		if (@mkdir($dir) == false) {
			return false;
		}
		return true;
	}

}
