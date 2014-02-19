<?php

class System_Lib_DataObject
{
	public $isLoaded = false;
	protected $srcFieldPool = array();

	/**
	 * 
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 *
	 * @param <string> $name
	 * @return <mix>
	 */
	public function  __get($name)
	{
		return $this->$name;
	}

	/**
	 *
	 * @return <bool>
	 */
	public function save()
	{
		if (!$this->isLoaded)
		{
			return $this->insert();
		}
		else
		{
			return $this->update();
		}
	}

	/**
	 *
	 * @return <bool>
	 */
	public function delete()
	{
		$d = call_user_func(array(get_class($this), 'dataAccess'));
		return $this->addFilter($d->locateObj($this))->delete();
	}

	/**
	 *
	 * @return <bool>
	 */
	protected function insert()
	{
		$d = call_user_func(array(get_class($this), 'dataAccess'));
		foreach ($this->columns() as $objColumn => $dbColumn)
			if (!is_null($this->$objColumn))
				$d->setField($objColumn, $this->$objColumn);
		$rs = $d->locateObj($this)->insert();
		$key = $this->key();
		if (!is_array($key) && is_null($this->$key))
		{
			$this->$key = $d->lastInsertId();
		}
		$this->afterInitObj();
		return $rs;
	}

	/**
	 *
	 * @return <bool>
	 */
	protected function update()
	{
		$d = call_user_func(array(get_class($this), 'dataAccess'));
		foreach ($this->columns() as $name => $value)
		{
			if ($this->srcFieldPool[$name] != $this->$name)
			{
				$d->setField($name, $this->$name);
			}
		}
		$this->afterInitObj();
		return $this->addFilter($d->locateObj($this))->update();
	}

	/**
	 *
	 * @param System_Lib_DataAccessor $d
	 * @return System_Lib_DataAccessor
	 */
	protected function addFilter($d)
	{
		$keys = $this->key();
		if (!is_array($keys))
		{
			$keys = array($keys);
		}
		
		foreach ($keys as $key)
		{
			$d->filter($key, $this->$key);
		}
		
		return $d;
	}
	
	/**
	 * 
	 */
	protected function init()
	{
		foreach ($this->columns() as $k => $v)
		{
			$this->$k = null;
		}
	}
	
	public function initSrcField()
	{
		foreach ($this->columns() as $objColumn => $dataColumn)
		{
			$this->srcFieldPool[$objColumn] = $this->$objColumn;
		}
	}

	/**
	 *
	 * @return System_Lib_DataObject 
	 */
	public function afterInitObj()
	{
		$this->initSrcField();
		$this->isLoaded = true;
		return $this;
	}

	/**
	 *
	 * @return <string>
	 */
	public function key()
	{
		//if php>=5.3 use this
		//$mapping = static::getMapping();
		//or ugly way
		$mapping = call_user_func(array(get_class($this), 'getMapping'));
		return $mapping['key'];
	}

	/**
	 *
	 * @return <array>
	 */
	protected function columns()
	{
		//if php>=5.3 use this
		//$mapping = static::getMapping();
		//or ugly way
		$mapping = call_user_func(array(get_class($this), 'getMapping'));
		return $mapping['columns'];
	}

	/**
	 *
	 * @return <string>
	 */
	protected function table()
	{
		//if php>=5.3 use this
		//$mapping = static::getMapping();
		//or ugly way
		$mapping = call_user_func(array(get_class($this), 'getMapping'));
		return $mapping['table'];
	}

	/**
	 * @return <array>
	 */
	public static function getSourceConfig()
	{
		throw new Exception('getSourceConfig() must be override in ORM obj');
	}

	/**
	 * @return <array>
	 */
	public static function getMapping()
	{
		throw new Exception('getMapping() must be override in ORM obj');
	}

	/**
	 *
	 * @return System_Lib_MysqlAccessor
	 */
	public static function dataAccess()
	{
		//if php>=5.3 use this
		//return System_Lib_DataAccessor::useModel(get_called_class());
		//or ugly way
		throw new Exception('dataAccess() must be override in ORM obj');
	}
	
	/**
	 *
	 * @return <string>
	 */
	public static function getDataAccessName()
	{
		//if php>=5.3 dont need this func
		//or ugly way
		throw new Exception('getDataAccessName() must be override in ORM obj');
	}
	
}