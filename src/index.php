<?php
namespace jumper;

$dir = explode("/",str_replace("\\","/",__DIR__));
$appPath="";
for($i=0; $i<sizeof($dir)-4; $i++) {
    $appPath .= $dir[$i]."/";
}
define("APP_PATH",$appPath."src");


if(file_exists(APP_PATH."/../vendor/autoload.php")) {
    include(APP_PATH."/../vendor/autoload.php"); 
}
include(__DIR__."/router.php"); 
if(file_exists(APP_PATH."/../conf/conf.php")) {
    include(APP_PATH."/../conf/conf.php");
}
if(file_exists(APP_PATH."/../conf/frontFilter.php")) {
    include(APP_PATH."/../conf/frontFilter.php");
}

include(__DIR__."/adapter.php");

if(defined("APP_NAMESPACE")) {
    class_alias('\\jumper\\J', APP_NAMESPACE.'\\j');
}

J::initialize();
Router::run();
J::execute();
