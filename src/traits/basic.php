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

	public static function validateToken($token) {
		
		if(defined("ENV") && (ENV=="LOCAL" || ENV=="DEV") && $code == "AAAA") {
			return true;
		}
		
		self::dbQuery("delete from sc_captcha where created_date < DATE_SUB(NOW(),INTERVAL 15 MINUTE)");
		$ok = (self::dbRes("select 1 from sc_captcha where code = {token} and remote_ip = {remote_ip}", array("token" => $token, "remote_ip" => $_SERVER["REMOTE_ADDR"])) == 1 );
		self::dbQuery("delete from sc_captcha where code = {token} and remote_ip = {remote_ip}", array("token" => $token, "remote_ip" => $_SERVER["REMOTE_ADDR"]));
				
		return $ok;
	}
	
	public static function getToken() {
	
		$token = uniqId();
		self::dbQuery("insert into sc_captcha set code = {token}, created_date = now(), remote_ip = {remote-ip}", array("token" => $token, "remote-ip" => $_SERVER["REMOTE_ADDR"]));
		
		return array("token" => $token);
	}
	
	public static function setExcelOutput($filename, $out) {
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		echo utf8_decode($out);
	}

	public static function superLog($p) {
		if(defined("SUPERLOG_SERVER") && (ENV=="LOCAL" || ENV=="DEV" || ENV=="QA"|| ENV=="STAGE")) {
			$type = isset($p["type"]) ? $p["type"] : "txt";
			$url = "http://".SUPERLOG_SERVER."?type=$type&key=".(defined("SUPERLOG_KEY")?SUPERLOG_KEY:"");
			if(isset($p["sql"])) { $url .= "&query=".urlencode($p["sql"]); }
			file_get_contents($url);
		}
	}

    public static function convBase($numberInput, $fromBaseInput, $toBaseInput) {
        if ($fromBaseInput==$toBaseInput) return $numberInput;
        $fromBase = str_split($fromBaseInput,1);
        $toBase = str_split($toBaseInput,1);
        $number = str_split($numberInput,1);
        $fromLen=strlen($fromBaseInput);
        $toLen=strlen($toBaseInput);
        $numberLen=strlen($numberInput);
        $retval='';
        if ($toBaseInput == '0123456789')
        {
            $retval=0;
            for ($i = 1;$i <= $numberLen; $i++)
                $retval = bcadd($retval, bcmul(array_search($number[$i-1], $fromBase),bcpow($fromLen,$numberLen-$i)));
            return $retval;
        }
        if ($fromBaseInput != '0123456789')
            $base10=convBase($numberInput, $fromBaseInput, '0123456789');
        else
            $base10 = $numberInput;
        if ($base10<strlen($toBaseInput))
            return $toBase[$base10];
        while($base10 != '0')
        {
            $retval = $toBase[bcmod($base10,$toLen)].$retval;
            $base10 = bcdiv($base10,$toLen,0);
        }
        return $retval;
    }

}
