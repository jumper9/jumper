<?php
namespace jumper;

trait logTrait 
{
    public static function logEmergency($message, array $context = array())
	{
		self::log($message, $context);
	} 

    public static function logAlert($message, array $context = array())
	{
		self::log($message, $context);
	} 
 
    public static function logCritical($message, array $context = array())
	{
		self::log($message, $context);
	} 

    public static function logError($message, array $context = array())
	{
		self::log($message, $context);
	} 
	
    public static function logWarning($message, array $context = array())
	{
		self::log($message, $context);
	} 	
	
    public static function logNotice($message, array $context = array()) 
	{
		self::log($message, $context);
	} 
	
    public static function logInfo($message, array $context = array()) 
	{
		self::log($message, $context);
	} 
	 
    public static function logDebug($message, array $context = array())
	{
		self::log($message, $context);
	} 
	
    public static function log($message, array $context = array()) 
	{
		echo $message.$context;
	} 
}
