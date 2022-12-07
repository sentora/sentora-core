<?php
/*
pSQLite - small SQLite database class

Requires SQLite PDO ext

Version     : 0.1-dev
Made by     : Momchil Bozhinov
Last Update : 30/08/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

class pSQLite
{
	var $DbSQLite;

	function __construct($DbPath)
	{
		$this->DbSQLite = new \PDO("sqlite:".$DbPath);
		$this->DbSQLite->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	function flush(string $table_name)
	{
		$this->execute("DELETE FROM $table_name;");
	}

	function quote(string $sql)
	{
		return $this->DbSQLite->quote($sql);
	}

	function execute(string $sql, array $params = [], $expects_return = FALSE, $select_one = TRUE)
	{
		if (isset($params[0])){
			$parse = $params[0];
		} else {
			$parse = $params;
			$params = [$params];
		}

		$sql = vsprintf($sql, array_keys($parse));
		$ret = TRUE;

		try{
			$this->DbSQLite->beginTransaction();
			$q = $this->DbSQLite->prepare($sql);

			foreach($params as $p){
				foreach($p as $name => $param){
					switch($param[1]){
						case 0:
							$type = \PDO::PARAM_INT;
							break;
						case 1:
							$type = \PDO::PARAM_STR;
							break;
						default:
							$type = \PDO::PARAM_STR;
					}
					$q->bindParam(':'.$name, $param[0], $type);
				}

				$q->execute();
			}

			if ($expects_return){
				$f = ($select_one) ? "fetch" : "fetchAll";
				$ret = $q->$f(\PDO::FETCH_ASSOC);
			}

			$this->DbSQLite->commit();

		} catch(\PDOException $e) {
			throw \pChart\pException::SQLiteException($e->getMessage());
		}

		return $ret;
	}
}

?>