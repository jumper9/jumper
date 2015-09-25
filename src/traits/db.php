<?php
namespace jumper;

trait dbTrait 
{
	public static function dbFullRes($sql, $params=array(), $option1=null, $option2=null, $option3=null) 
	{ 
		$result = self::dbFetchAll($sql, $params, $option1, $option2, $option3);
        return $result; 
	}
	
	public static function dbFirstRow($sql, $params=array(), $option1=null, $option2=null, $option3=null) 
	{ 
		$result = self::dbFetch($sql, $params, $option1, $option2, $option3);
        return $result; 
	}
	
	public static function dbRes($sql, $params=array(), $option1=null, $option2=null, $option3=null) 
	{
		$result = self::dbFetch($sql, $params, $option1, $option2, $option3);
		if(is_array($result)) {
			foreach($result as $k=>$v) {
				return $v;
			}
		}
	}
	
	public static function dbJson($sql, $params=array(), $option1=null, $option2=null, $option3=null) 
	{ 
		$result = self::dbFetch($sql, $params, $option1, $option2, $option3);
		if(is_array($result)) {
			foreach($result as $k=>$v) {
				return json_decode($v,true);
			}
		}
	}
	
	public static function dbQuery($sql, $params=array(), $option1=null, $option2=null, $option3=null) 
	{ 
		self::dbFetch($sql, $params, $option1, $option2, $option3);
	}
	
	public static function dbInsert($sql, $params=array(), $option1=null, $option2=null, $option3=null)  { 
		self::dbFetch($sql, $params, $option1, $option2, $option3);
		return Db::$dbo->lastInsertId()*1;
	}
	
	public static function dbInsertId() 
	{ 
		return Db::$dbo->lastInsertId();
	}
	
	public static function dbEscape($string) 
	{ 
		return substr(Db::$dbo->quote($string),1,-1);
	}

	private static function dbPrepareSql($sql, $params) {
		self::superLog(array("type"=>"sql", "sql"=>$sql));
		return $sql;
		$sql .= " ";
		if(is_array($params)) {
			foreach ($params as $k => $v) {
				$params[strtolower($k)] = $v;
			}
		}

		$pos = mb_strpos($sql, "{", 0, "UTF-8");
		$i=0;
		while($pos and $i++<1000) {
			$pos2 = mb_strpos($sql, "}", $pos, "UTF-8"); 
			$var = mb_substr($sql, $pos+1, $pos2 - $pos - 1, "UTF-8");
			$type = strtolower(self::strtoken($var, 1, ":"));
			$name = strtolower(self::strtoken($var, -1, ":"));
			$value = '';
			if($type == "p") {
				$value = self::getParam($name);
			} else if (isset($params[$name])){
				if(is_array($params[$name])) {
					$value = json_encode($params[$name], JSON_UNESCAPED_UNICODE);
				} else {
					$value = $params[$name];
				}
			} 
			$value = Db::$dbo->quote($value);
			$sql = mb_substr($sql, 0, $pos, "UTF-8") . $value . mb_substr($sql, $pos2+1, null, "UTF-8");

			if ($pos2 + 1 >= mb_strlen($sql, "UTF-8")) {
				break;
			}
			$pos = mb_strpos($sql, "{", $pos2, "UTF-8");
		}

		if(defined("DB_DEBUG")) {
			echo "\n".$sql."\n";
		}
		return $sql;
	}
	
	private static function dbFetchAll($sql, $params=array(), $option1=null, $option2=null, $option3=null) 
	{
		$convert = defined("SQL_ESCAPE") && ($option1 == SQL_ESCAPE or $option2 == SQL_ESCAPE or $option3 == SQL_ESCAPE);
		if($convert) {}

		$sql = self::dbPrepareSql($sql, $params);
		$sth = Db::$dbo->prepare($sql);

		$sth->execute();

		$result = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $result;
	}
	
	private static function dbFetch($sql, $params=array(), $option1=null, $option2=null, $option3=null) 
	{
		$convert = defined("SQL_ESCAPE") && ($option1 == SQL_ESCAPE or $option2 == SQL_ESCAPE or $option3 == SQL_ESCAPE);
		if($convert) {}
		
		$sql = self::dbPrepareSql($sql, $params);
		$sth = Db::$dbo->prepare($sql);

		$sth->execute();

		$result = $sth->fetch(\PDO::FETCH_ASSOC);
		return $result;
	}
}


class Db {
    public static $dbo=null;

    public static function initialize($dbo) 
    {
        self::$dbo = $dbo;
    }
}
