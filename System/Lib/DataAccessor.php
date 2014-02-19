<?php

class System_Lib_DataAccessor
{
	const OP_GREATER_THAN = '>';
	const OP_GREATER_THAN_OR_EQUAL_TO = '>=';
	const OP_LESS_THAN = '<';
	const OP_LESS_THAN_OR_EQUAL_TO = '<=';
	const OP_EQUAL = '=';
	const OP_NOT_EQUAL_TO = '!=';
	const OP_IN = 'in';
	const SORT_TYPE_DESC = 'desc';
	const SORT_TYPE_ASC = 'asc';

	protected $mapping;
	protected $modelName;
	protected $filterNames = array();
	protected $filterOps = array();
	protected $filterValues = array();
	protected $loadFields = array();
	protected $sorts = array();
	protected $setFields = array();
	protected $limit = null;
	protected $offset = 0;
	protected $groups = array();

	/**
	 *
	 * @param <string> $className
	 * @return System_Lib_DataAccessor
	 */
	public static function useModel($className)
	{
		$dataAccessorName = call_user_func(array($className, 'getDataAccessName'));
		$dataAccessor = new $dataAccessorName;
		$dataAccessor->modelName = $className;
		$dataAccessor->mapping = call_user_func(array($dataAccessor->modelName, 'getMapping'));
		return $dataAccessor;
	}

	/**
	 *
	 * @param <string> $sourceName
	 * @return <mix>
	 */
	protected function getConnection($sourceName)
	{
		throw new Exception('getConnection() must be override', '011');
	}

	/**
	 *
	 * @param <string> $name
	 * @param <string> $value
	 * @return  System_Lib_DataAccessor
	 */
	public function filter($name, $value)
	{
		$op = System_Lib_DataAccessor::OP_EQUAL;
		if (is_array($value))
		{
			if (empty($value))
				throw new Exception('values can not be empty array', '005');
			$op = System_Lib_DataAccessor::OP_IN;
		}
		return $this->filterByOp($name, $op, $value);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <string> $op
	 * @param <string> $value
	 * @return  System_Lib_DataAccessor
	 */
	public function filterByOp($name, $op, $value)
	{
		$this->filterNames[] = $name;
		$this->filterOps[] = $op;
		$this->filterValues[] = $value;
		return $this;
	}

	/**
	 *
	 * @param <mix> $fieldNames
	 * @return System_Lib_DataAccessor
	 */
	public function loadField($fieldNames)
	{
		if (!is_array($fieldNames))
			$fieldNames = array($fieldNames);
		$this->loadFields = array_merge($this->loadFields, $fieldNames);
		return $this;
	}

	/**
	 *
	 * @param <type> $name
	 * @param <type> $type
	 * @return System_Lib_DataAccessor
	 */
	public function sort($name, $type = self::SORT_TYPE_DESC)
	{
		$this->sorts[$name] = $type;
		return $this;
	}

	public function group($name)
	{
		$this->groups[] = $name;
		return $this;
	}
	/**
	 *
	 * @param <int> $offset
	 * @return System_Lib_DataAccessor
	 */
	public function offset($offset)
	{
		$this->offset = $offset;
		return $this;
	}

	/**
	 *
	 * @param <int> $limit
	 * @return System_Lib_DataAccessor
	 */
	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}

	/**
	 *
	 * @param <string> $name
	 * @param <string> $value
	 * @return System_Lib_DataAccessor
	 */
	public function setField($name, $value)
	{
		$this->setFields[$name] = $value;
		return $this;
	}

	/**
	 *
	 * @return System_Lib_DataObject
	 */
	public function findOne()
	{
		$rs = $this->limit(1)->find();
		if (empty($rs))
		{
			return false;
		}	
		return array_pop($rs);
	}

	/**
	 *
	 * @param <int> $id
	 * @return System_Lib_DataObject
	 */
	public function findByPk($id)
	{
		$rs = $this->limit(1)->findByPkInternal($id);
		if (!count($rs))
		{
			return false;
		}
		$rs = array_pop($rs);

		return $rs;
	}

	/**
	 *
	 * @param <array> $ids
	 * @return System_Lib_DataObject
	 */
	public function findByPks($ids)
	{
		return $this->findByPkInternal($ids);
	}

	/**
	 *
	 * @param <mix> $ids
	 * @return <array>
	 */
	protected function findByPkInternal($ids)
	{
		if (!isset($this->mapping['key']))
			throw new Exception('no primary key', '001');
		$this->filter($this->mapping['key'], $ids);
		$rs = $this->find();
		return $rs;
	}

	/**
	 *
	 * @param System_Lib_DataObject $obj
	 * @return System_Lib_DataAccessor
	 */
	public function locateObj($obj)
	{
		return $this;
	}
	
	/**
	 *
	 * @param <string> $objColumn
	 * @return <string>
	 */
	protected function mappingToDb($objColumn)
	{
		return $this->mapping['columns'][$objColumn];
	}

	/**
	 *
	 * @param <string> $dbColumn
	 * @return <string>
	 */
	protected function mappingToObj($dbColumn)
	{
		return array_search($dbColumn, $this->mapping['columns']);
	}

	protected function find()
	{
		throw new Exception('find() must be override');
	}

	protected function update()
	{
		throw new Exception('update() must be override');
	}

	protected function insert()
	{
		throw new Exception('insert() must be override');
	}

	protected function delete()
	{
		throw new Exception('delete() must be override');
	}

	protected function count()
	{
		throw new Exception('count() must be override');
	}
	
	protected function initObjs()
	{
		throw new Exception('initObjs() must be override');
	}

	protected function executeInternal()
	{
		throw new Exception('executeInternal() must be override');
	}

	protected function executeWrite()
	{
		throw new Exception('executeWrite() must be override');
	}

	protected function executeRead()
	{
		throw new Exception('executeRead() must be override');
	}

	public function lastInsertId()
	{
		throw new Exception('lastInsertId() must be override');
	}
}