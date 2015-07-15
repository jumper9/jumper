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
		return Db::$dbo->lastInsertId();
	}
	
	public static function dbInsertId() 
	{ 
		return Db::$dbo->lastInsertId();
	}
	
	public static function dbEscape($string) 
	{ 
		return substr(Db::$dbo->quote($string),1,-1);
	}

	private static function dbFetchAll($sql, $params=array(), $option1=null, $option2=null, $option3=null) 
	{
		$convert = ($option1 == SQL_ESCAPE or $option2 == SQL_ESCAPE or $option3 == SQL_ESCAPE);
		if($convert) {}
		$sth = Db::$dbo->prepare($sql);
		if(is_array($params) && sizeof($params)>0) {
			$sth->execute($params);
		} else {
			$sth->execute();
		}
		$result = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $result;
	}
	
	private static function dbFetch($sql, $params=array(), $option1=null, $option2=null, $option3=null) 
	{
		$convert = ($option1 == SQL_ESCAPE or $option2 == SQL_ESCAPE or $option3 == SQL_ESCAPE);
		if($convert) {}
		$sth = Db::$dbo->prepare($sql);
		if(is_array($params) && sizeof($params)>0) {
			$sth->execute($params);
		} else {
			$sth->execute();
		}
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
