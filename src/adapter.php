<?PHP
namespace jumper;

include(__DIR__."/traits/db.php");
include(__DIR__."/traits/log.php");
include(__DIR__."/traits/parameters.php");
include(__DIR__."/traits/basic.php");
include(__DIR__."/traits/facebook.php");
include(__DIR__."/traits/amazonS3.php");
include(__DIR__."/traits/image.php");
include(__DIR__."/traits/date.php");
include(__DIR__."/traits/google-analytics.php");

class J 
{

    private static $responseJson;
    private static $view;
    private static $errorCode;
    private static $errorMessages;
    private static $params;

	public static function bogusJustForValidation() 
	{
		$a = array(self::$responseJson, self::$view, self::$errorCode, self::$errorMessages, self::$params);
		$a = null;
		return $a;
	}
	
    use basicTrait;
    use dbTrait;
    use facebookTrait;
    use amazonS3Trait;
    use parametersTrait;
    use imageTrait;
    use dateTrait;
    use logTrait;
    use googleAnalyticsTrait;
}
