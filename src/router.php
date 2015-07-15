<?php
namespace jumper;

class Router 
{
    
    public static function run() 
    {
        
		$frontFilterClassName = APP_NAMESPACE."\\frontFilter";
        $url = $frontFilterClassName::getUrl();

        $module=J::strtoken($url,2,"/");
        $class=J::strtoken($url,3,"/");
        $className = APP_NAMESPACE."\\".str_replace("-","",ucfirst($class))."Controller";

        if (file_exists(APP_PATH."/$module/services/$class.php")) {
            include(APP_PATH."/$module/services/$class.php");
        } else if (file_exists(APP_PATH."/$module/$class.php")){
            include(APP_PATH."/$module/$class.php");
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
            $className::$method();
        } else {
            J::setError(404,"Not Found");
        }

    }
    
}
