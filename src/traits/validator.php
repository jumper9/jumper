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
}