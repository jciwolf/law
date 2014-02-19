<?php
class System_Lib_PdoFactory extends System_Lib_Factory
{
	protected $currentConfig;

	/**
	 *
	 * @param <string> $name
	 * @return PDO
	 */
	public function get($config, $readOnly)
	{
		if ($readOnly)
		{
			$this->currentConfig = $config['slave'];
			$conName = $config['name'] . '_slave';
		}
		else
		{
			$this->currentConfig = $config['master'];
			$conName = $config['name'] . '_master';
		}
		return parent::get($conName);
	}
	
	/**
	 *
	 * @param <string> $name
	 * @return PDO
	 */
	protected function create($name)
	{
		return $this->connect($this->currentConfig);
	}

	/**
	 *
	 * @param array $config
	 * @return PDO
	 */
	protected function connect($config)
	{
		$pdo = new PDO(
			$config['dsn'],
			$config['username'],
			$config['password'],
			isset($config['driverOptions']) ? $config['driverOptions'] : array()
		);
		if (isset($config['initStatements']))
		{
			foreach ($config['initStatements'] as $sql)
			{
				$pdo->exec($sql);
			}
		}

		return $pdo;
	}

}