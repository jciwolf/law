<?php

class System_Lib_MysqlAccessor extends System_Lib_DataAccessor
{


	const SORT_TYPE_DESC = 'desc';
	const SORT_TYPE_ASC = 'asc';

	public static $lastSql = null;
	
	/**
	 *
	 * @var PDO
	 */
	public $connection = null;
	public $readOnly = false;
	public $forceMaster = false;
	protected $sql;
	protected $pdoValues = array();
	protected $userDefineWhere = '';
	protected $userDefineValues = array();
	protected $userDefineFlag = false;

	/**
	 *
	 * @return <string>
	 */
	public static function getLastSql()
	{
		return System_Lib_MysqlAccessor::$lastSql;
	}

	/**
	 *
	 * @return System_Lib_MysqlAccessor
	 */
	public function forceMaster()
	{
		$this->forceMaster = true;
		return $this;
	}

	/**
	 *
	 * @param <string> $sql
	 * @param <array> $values
	 * @param <bool> $readOnly
	 * @return <mix>
	 */
	public function nativeSql($sql, $values, $readOnly = true)
	{
		$this->readOnly = $readOnly;
		$className = $this->modelName;
		$this->connection = $this->getConnection(call_user_func(array($this->modelName, 'getSourceConfig')));
		$stmt = $this->connection->prepare($sql);
		$stmt->execute($values);
		if ($readOnly)
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $stmt->rowCount();
	}

	/**
	 *
	 * @param <string> $sqlWhere
	 * @param <array> $values
	 * @return System_Lib_MysqlAccessor
	 */
	public function userDefineWhere($sqlWhere, $values)
	{
		$this->userDefineWhere = $sqlWhere;
		$this->userDefineValues = $values;
		$this->userDefineFlag = true;
		return $this;
	}

	/**
	 *
	 * @return <array>
	 */
	public function find()
	{
		$this->readOnly = true;
		$fields = '*';
		if (count($this->loadFields))
		{
			$fields = '';
			foreach ($this->loadFields as $field)
				$fields .= " `{$this->mappingToDb($field)}` ,";
			$fields = rtrim($fields, ',');
		}
		$this->sql = "select {$fields} from `{$this->mapping['table']}` ";
		$this->appendWhere();
		$this->appendOrder();
		$this->appendLimit();
		return $this->initObjs($this->executeRead()->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 *
	 * @return <int>
	 */
	public function update()
	{
		$this->sql = "update `{$this->mapping['table']}` ";
		$this->appendUpdate();
		$this->appendWhere();
		return $this->executeWrite();
	}

	/**
	 *
	 * @param <string> $type
	 * @return <int>
	 */
	public function insert($type = 'insert')
	{
		$this->sql = "{$type} into `{$this->mapping['table']}` ";
		$this->appendInsert();
		return $this->executeWrite();
	}

	/**
	 *
	 * @return <int>
	 */
	public function delete()
	{
		$this->sql = "delete from `{$this->mapping['table']}` ";
		$this->appendWhere();
		$this->appendLimit();
		return $this->executeWrite();
	}

	/**
	 *
	 * @return <int>
	 */
	public function count()
	{
		$this->sql = "select count(1) from `{$this->mapping['table']}` ";
		$this->appendWhere();
		return $this->executeRead()->fetchColumn();
	}

	public function groupCount()
	{
		if (empty($this->groups)) {
			throw new Exception('groupCount() must call group()');
		}
		$fields = '';
		foreach ($this->groups as $field) {
			if ($this->mappingToDb($field)) {
				$fields .= " `{$this->mappingToDb($field)}` ,";
			}
			else {
				$fields .= " {$field} ,";
			}
		}
		$fields .= " count(1) as `count`";
		$this->sql = "select {$fields} from `{$this->mapping['table']}` ";
		$this->appendWhere();
		$this->appendGroup();
		$this->appendOrder();
		$this->appendLimit();
		return $this->executeRead()->fetchAll();
	}

	public function groupSum($fieldName)
	{
		if (empty($this->groups)) {
			throw new Exception('groupSum() must call group()');
		}
		$fields = '';
		foreach ($this->groups as $field) {
			if ($this->mappingToDb($field)) {
				$fields .= " `{$this->mappingToDb($field)}` ,";
			}
			else {
				$fields .= " {$field} ,";
			}
		}
		$fields .= " sum(`{$this->mappingToDb($fieldName)}`) as `sum`";
		$this->sql = "select {$fields} from `{$this->mapping['table']}` ";
		$this->appendWhere();
		$this->appendGroup();
		$this->appendOrder();
		$this->appendLimit();
		return $this->executeRead()->fetchAll();
	}

	/**
	 *
	 * @return <int>
	 */
	public function sum($field)
	{
		$this->sql = "select sum(`".$field."`) from `{$this->mapping['table']}` ";
		$this->appendWhere();
		return $this->executeRead()->fetchColumn(0);
	}

	/**
	 *
	 * @return <int>
	 */
	public function replace()
	{
		return $this->insert('replace');
	}

	/**
	 *
	 * @param <array> $rs
	 * @return <mix>
	 */
	public function initObjs($rs)
	{
		$objs = array();
		foreach ($rs as $r)
		{
			$obj = new $this->modelName();
			foreach ($this->mapping['columns'] as $objColumn => $dbColumn)
				$obj->$objColumn = isset($r[$dbColumn]) ? $r[$dbColumn] : null;
			$obj->isLoaded = true;
			$objs[] = $obj->afterInitObj();
		}
		return $objs;
	}

	/**
	 *
	 * @param <array> $sourceConfig
	 * @return  PDO
	 */
	protected function getConnection($sourceConfig)
	{
		return System_Lib_App::app()->pdo()->get($sourceConfig, $this->readOnly);
	}

	/**
	 *
	 * @return PDOStatement
	 */
	protected function executeInternal()
	{
		$startTime = microtime(true);
		$this->connection = $this->getConnection(call_user_func(array($this->modelName, 'getSourceConfig')));
		$stmt = $this->connection->prepare($this->sql);
		System_Lib_MysqlAccessor::$lastSql = $this->sql;
		$stmt->execute($this->pdoValues);
		$endTime = microtime(true);
		$executeTime = number_format($endTime - $startTime, 3, '.', '');
		$sql = $this->parseSql($this->sql, $this->pdoValues);
		$GLOBALS['run_times'][] = System_Lib_App::app()->recordRunTime('sql '.$executeTime.' '.$sql);
		if ($executeTime > 0.1 || !empty($_GET['debug'])) {
			$GLOBALS['sql_logs'][] = array(
				'time'    => $startTime,
				'sql'     => $this->sql,
				'values'  => $this->pdoValues,
				'sec'     => $executeTime,
			);
		}
		return $stmt;
	}

	public function parseSql($sql, $values)
	{
		$a = explode('?', $sql);
		$sql = '';
		for ($i=0;$i<count($a);$i++) {
			$sql .= $a[$i];
			if (!empty($values[$i])) {
				$sql .= $values[$i];
			}
		}
		return $sql;
	}

	/**
	 *
	 * @return <int>
	 */
	public function lastInsertId()
	{
		return $this->connection->lastInsertId();
	}

	/**
	 *
	 * @return <int>
	 */
	protected function executeWrite()
	{
		return $this->executeInternal()->rowCount();
	}

	/**
	 *
	 * @return PDOStatement
	 */
	protected function executeRead()
	{
		return $this->executeInternal();
	}

	/**
	 *
	 */
	protected function appendWhere()
	{
		if ((!count($this->filterNames) || !count($this->filterOps) || !count($this->filterValues)) && !$this->userDefineFlag)
			return false;
		$this->sql .= 'where ';
		$tmp = array();
		foreach ($this->filterNames as $k => $name)
		{
			if (is_array($this->filterValues[$k]))
			{
				$valueString = implode(' , ', array_fill(0, count($this->filterValues[$k]), '?'));
				$tmp[] = "`{$this->mappingToDb($name)}` {$this->filterOps[$k]} ( {$valueString} ) ";
				$this->pdoValues = array_merge($this->pdoValues, $this->filterValues[$k]);
			}
			else
			{
				$tmp[] = "`{$this->mappingToDb($name)}` {$this->filterOps[$k]} ? ";
				$this->pdoValues[] = $this->filterValues[$k];
			}
		}
		//user define where
		if ($this->userDefineFlag)
		{
			$tmp[] = '(' . $this->userDefineWhere . ')';
			$this->pdoValues = array_merge($this->pdoValues, $this->userDefineValues);
		}
		$this->sql .= implode(' and ', $tmp) . ' ';
	}

	protected function appendOrder()
	{
		if (!count($this->sorts))
			return false;
		$this->sql .= 'order by ';
		$tmp = array();
		foreach ($this->sorts as $k => $t)
			$tmp[] = "{$k} {$t}";
		$this->sql .= implode(' , ', $tmp) . ' ';
	}

	protected function appendGroup()
	{
		if (!count($this->groups))
			return false;
		$this->sql .= 'group by ';
		$tmp = array();
		foreach ($this->groups as $k)
			$tmp[] = "{$k}";
		$this->sql .= implode(' , ', $tmp) . ' ';
	}

	protected function appendLimit()
	{
		if (is_null($this->limit))
			return false;
		$offset = is_null($this->offset) ? '' : $this->offset . ',';
		$this->sql .= "limit {$offset}{$this->limit}";
	}

	protected function appendUpdate()
	{
		$this->sql .= 'set ';
		$tmp = array();
		foreach ($this->setFields as $k => $v)
			$tmp[] = "`{$this->mappingToDb($k)}` = ?";
		$this->sql .= implode(' , ', $tmp) . ' ';
		$this->pdoValues = array_merge($this->pdoValues, array_values($this->setFields));
	}

	protected function appendInsert()
	{
		$columns = array();
		$values = array();
		foreach ($this->setFields as $k => $v)
		{
			$columns[] = "`{$this->mappingToDb($k)}`";
			$values[] = '?';
		}
		$this->sql .= '( ' . implode(' , ', $columns) . ' ) values ( ' . implode(' , ', $values) . ' ) ';
		$this->pdoValues = array_merge($this->pdoValues, array_values($this->setFields));
	}

}