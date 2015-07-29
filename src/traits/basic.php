<?php
namespace jumper;

trait basicTrait 
{
    
    public static function hasErrors() 
    {
        return self::$errorCode;
    }

    public static function setError($errorCode,$errorMessage,$errorMessage2="") 
    {
            
        if ($errorMessage2) {
            $errorType=$errorMessage;
            $errorMessage=$errorMessage2;
        } else {
            $errorType=0;
        }
        
        self::$errorCode=$errorCode;
        self::$errorMessages[]=array("code"=>$errorCode,"type"=>$errorType,"message"=>$errorMessage);
    }
    
    public static function initialize() 
    {
		$configClass = "\\Config";
		if(defined("APP_NAMESPACE")) {
			$configClass = APP_NAMESPACE."\\Config";
		}
		$configClass::dbConnect();
		
		if (defined("DEBUG")) { 
			set_error_handler('exceptions_error_handler');
		}

		function exceptions_error_handler($severity, $message, $filename, $lineno) {
		  if (error_reporting() == 0) {
			return;
		  }
		  self::setError(400,"Server Error: $message - Severity: $severity - File $filename, line $lineno");
		}

        $params=$_GET;
        foreach($_POST as $k=>$v) {
            if ($v) {
                $params[$k]=$v;
            }
        }
        $request_body = file_get_contents('php://input');
        try {
            $data = json_decode($request_body,true);
        } catch(\Exception $e) {
            // do nothing
        }
        if (is_array($data)) {
            foreach($data as $k=>$v) {
                if ($v) {
                    $params[$k]=$v;
                }
            }
        }
		foreach($params as $k=>$v) {
			$params[strtolower($k)] = $v;
		}
        self::setParams($params);

    }
    
    public static function dieError($p1,$p2=null,$p3=null) 
    {
        self::setError($p1,$p2,$p3);
        self::execute();
    }

    public static function execute() 
    {
        if (self::$errorCode) {
            http_response_code(self::$errorCode);
            header('Content-Type: application/json');
            $errorData=array("apiVersion"=> "2.0", "errors"=>self::$errorMessages);
            if (ENV!="PROD") {
                $errorData["env"]=ENV;
                $errorData["server"]=$_SERVER;
                $errorData["post"]=$_POST;
                $errorData["params"]=self::getParams();
            }
            echo json_encode($errorData);
        } else {    
            if (self::$responseJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(self::$responseJson,JSON_UNESCAPED_UNICODE);
            } 
            if (self::$view) {
                include(self::$view);
            }
        }
    }
    
    public static function setView($viewName) 
    {
        self::$view=$viewName;        
    }
    
    public static function setResponseJson($responseJson) 
    {
        self::$responseJson=$responseJson;
    }

    public static function responseTxtJson($txt) 
    {
        self::setResponseJson(json_decode($txt,true));
    }

    public static function strtoken($string, $pos, $token) 
    {
        $explode = explode($token, $string);
        if (abs($pos) > sizeof($explode) || $pos == 0) {
                $out = '';
        } else if ($pos > 0) {
                $out = $explode [$pos-1];
        } else if ($pos < 0) {
                $out = $explode [sizeof($explode) + $pos];
        }
        return trim($out);
    }

	public static function setExcelOutput($filename, $out) {
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		echo utf8_decode($out);
		die;
	}
}
