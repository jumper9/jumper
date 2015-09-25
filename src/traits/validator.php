<?php
namespace jumper;

trait validatorTrait 
{
	
	public static function validateParam($param, $rules, $errorText = "invalid data") 
	{
		return self::validate(self::getParam($param), $rules, $errorText);
		
	}
	
	public static function validate($value, $rules, $errorText = "invalid data") 
	{
		$ok = true;
		foreach ($rules as $rule) {
			$type = self::strtoken($rule,1,":");
			$number = self::strtoken($rule,2,":");
			
			if ($type == "letters") {
				for($i=0; $i<mb_strlen($value); $i++) {
					if(mb_strpos(" abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZñÑáéíóúÁÉÍÓÚüÜ", mb_substr($value, $i, 1)) === false) {
						$ok = false;
						break;
					}
				}
				
			} else if ($type == "integer") {
				for($i=0; $i<mb_strlen($value); $i++) {
					if(mb_strpos("01234567890", mb_substr($value, $i, 1)) === false) {
						$ok = false;
						break;
					}
				}
				
			} else if ($type == "address") {
				for($i=0; $i<mb_strlen($value); $i++) {
					if(mb_strpos(" abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZñÑáéíóúÁÉÍÓÚüÜ01234567890º.", mb_substr($value, $i, 1)) === false) {
						$ok = false;
						break;
					}
				}
				
			} else if ($type == "minlength" && mb_strlen($value, "UTF-8") < $number) {
				$ok = false;
				
			} else if ($type == "maxlength" && mb_strlen($value, "UTF-8") > $number) {
				$ok = false;

			} else if ($type == "minvalue" && $value < $number) {
				$ok = false;

			} else if ($type == "maxvalue" && $value > $number) {
				$ok = false;

			} else if ($type == "date" && !checkdate ( self::strtoken($value,2,"-")*1, self::strtoken($value,3,"-")*1, self::strtoken($value,1,"-")*1 )) {
				$ok = false;

			} else if ($type == "18years") {
				$dateCheck =  date("Y-m-d", mktime(0,0,0,date("m"),date("d"),date("Y")-18));
				if(!checkdate ( self::strtoken($value,2,"-")*1, self::strtoken($value,3,"-")*1, self::strtoken($value,1,"-")*1 ) || $value > $dateCheck) {
					$ok = false;
				}
				
			} else if ($type == "captcha") {
				if(!self::validateCaptcha(self::strtoken($value,1,":"), self::strtoken($value,2,":")) ) {
					$ok = false;
				}

			} else if ($type == "email") {
				if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$ok = false;
				}
			}
			
		}
		
		if(!$ok) {
			self::setError(400, $errorText);
		}
	}
	
	public static function validateCaptcha($id, $code) {
		
		if(defined("ENV") && (ENV=="LOCAL" || ENV=="DEV" || ENV=="QA" || ENV=="PROD-TEST") && $id == "9999") {
			return true;
		}
		if(!defined("CAPTCHA_TABLE")) {
			return true;
		}
		
		self::dbQuery("delete from {d:captcha} where created_date < DATE_SUB(NOW(),INTERVAL 15 MINUTE)",array("captcha"=>CAPTCHA_TABLE));
		$ok = (self::dbRes("select 1 from {d:captcha} where id = {id} and code = {code} and remote_ip = {remote_ip}", array("id" => $id, "code" => $code, "remote_ip" => $_SERVER["REMOTE_ADDR"], "captcha" => CAPTCHA_TABLE)) == 1 );
		self::dbQuery("delete from {d:captcha} where id = {id} and remote_ip = {remote_ip}", array("id" => $id, "remote_ip" => $_SERVER["REMOTE_ADDR"], "captcha" => CAPTCHA_TABLE));
				
		return $ok;
	}

	public static function validateUniqId($id, $uniqid) {

		if(defined("ENV") && (ENV=="LOCAL" || ENV=="DEV" || ENV=="QA" || ENV=="PROD-TEST") && $id == "AAAA") {
			return true;
		}
		if(!defined("CAPTCHA_TABLE")) {
			return true;
		}
		
		self::dbQuery("delete from {d:captcha} where created_date < DATE_SUB(NOW(),INTERVAL 15 MINUTE)",array("captcha"=>CAPTCHA_TABLE));
		$ok = (self::dbRes("select 1 from {d:captcha} where id = {id} and uniqid = {uniqid} and remote_ip = {remote_ip}", array("id" => $id, "uniqid" => $uniqid, "remote_ip" => $_SERVER["REMOTE_ADDR"], "captcha" => CAPTCHA_TABLE)) == 1 );
		self::dbQuery("delete from {d:captcha} where  id = {id} and remote_ip = {remote_ip}", array("id" => $id, "remote_ip" => $_SERVER["REMOTE_ADDR"], "captcha" => CAPTCHA_TABLE));
				
		return $ok;
	}
	
	public static function getCaptcha($params = array()) {
	
		if(!defined("CAPTCHA_TABLE")) {
			return false;
		}
		
		$width = isset($params["width"]) ? $params["width"] : 55;
		$height = isset($params["height"]) ? $params["height"] : 25;
		$image = imagecreatetruecolor($width, $height);
		$bg = imagecolorallocate($image, 255, 255,255);
		imagefill($image, 0, 0, $bg);
		$code = rand(str_repeat("1", (isset($params["digits"]) ? $params["digits"] : 25) ) * 1, str_repeat("9", (isset($params["digits"]) ? $params["digits"] : 25) ) * 1 ); 
		$len = mb_strlen($code, "UTF-8");
		$x = 8;
		$y = 5;
		for ($i = 0; $i < $len; $i++) {
			$char = mb_substr($code, $i, 1, "UTF-8");
			$color = imagecolorallocate($image, mt_rand(0, 125), mt_rand(0, 125), mt_rand(0, 125));
			imagestring ($image , 4 , $x , $y , $char , $color );
			$x += 10;
		}
		ob_start();
		imagejpeg($image, null, 90);
		$jpgImage = ob_get_clean();

		$uniqId = uniqid("",true);
		
		$data = "data:image/jpeg;base64," . base64_encode($jpgImage);
		$id = self::dbInsert("insert into {d:captcha} set uniqid = {uniqId}, code = {code}, created_date = now(), remote_ip = {remote-ip}", array("uniqId" => $uniqId, "captcha"=>CAPTCHA_TABLE, "code" => $code, "remote-ip" => $_SERVER["REMOTE_ADDR"]));
		
		return array("id" => $id, "data" => $data, "uniqId" => $uniqId);
	}

}