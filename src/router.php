<?php
namespace jumper;

class Router 
{
    
    public static function run() 
    {
        
		$frontFilterClassName = APP_NAMESPACE."\\FrontFilter";
        $url = $frontFilterClassName::getUrl();

        $module=J::strtoken($url,2,"/");
        $class=J::strtoken($url,3,"/");
		if($class=="") {
			$class = "index";
		}
        $className = APP_NAMESPACE."\\".str_replace("-","",ucfirst($class))."Controller";
        if (file_exists(APP_PATH."/$module/services/$class.php")) {
            include(APP_PATH."/$module/services/$class.php");
        } 

        if (!J::strtoken($url,4,"/")) {
            $method = strtolower($_SERVER["REQUEST_METHOD"]);
        } else if (method_exists($className,J::strtoken($url,4,"/")) ){
            $method = J::strtoken($url,4,"/");
        } else {
            $method = strtolower($_SERVER["REQUEST_METHOD"]);
            J::setParam("p1", J::strtoken($url,4,"/"));
        }


        if (file_exists(APP_PATH."/$module/services/$class.php")) {
             if(method_exists ($className, $method)) { 
				$className::$method();
			} else {
				J::setError(404,"Not Found");
			}
         } else {
            J::setError(404,"Not Found");
        }

    }
    
}
