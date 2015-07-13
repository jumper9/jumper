<?php
namespace jumper;

trait parametersTrait 
{
    private static function setParams($params) 
    {
        self::$params=$params;
    }
    
    public static function getParams() 
    {
        return self::$params;
    }
    
    public static function setParam($p,$v) 
    {
        self::$params[$p]=$v;
    }
    
    public static function getParam($p) 
    {
        return isset(self::$params[$p]) ? self::$params[$p]  : "" ;
    }

}