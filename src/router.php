<?php
namespace jumper;

class Router 
{
    
    public static function run() 
    {
        
        $url = frontFilter::getUrl();

        $module=f::strtoken($url,2,"/");
        $class=f::strtoken($url,3,"/");
        $className = APP_NAMESPACE."\\".str_replace("-","",ucfirst($class))."Controller";

        if (file_exists(APP_PATH."/$module/services/$class.php")) {
            include(APP_PATH."/$module/services/$class.php");
        } else if (file_exists(APP_PATH."/$module/$class.php")){
            include(APP_PATH."/$module/$class.php");
        } 

        if (!f::strtoken($url,4,"/")) {
            $method = strtolower($_SERVER["REQUEST_METHOD"]);
        } else if (method_exists($className,f::strtoken($url,4,"/")) ){
            $method = f::strtoken($url,4,"/");
        } else {
            $method = strtolower($_SERVER["REQUEST_METHOD"]);
            f::setParam("p1", f::strtoken($url,4,"/"));
        }


        if (file_exists(APP_PATH."/$module/services/$class.php")) {
            $className::$method();
        } else {
            f::setError(404,"Not Found");
        }

    }
    
}
