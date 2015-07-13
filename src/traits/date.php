<?php
namespace jumper;

trait dateTrait 
{
    
    public static function date2sql($date) 
    {
        $date=self::strtoken($date,1," ");
        if (strpos($date,"/")) {
            $day=self::strtoken($date,1,"/")*1;
            $month=self::strtoken($date,2,"/")*1;
            $year=self::strtoken($date,3,"/")*1;
        } else if (strpos($date,".")) {
            $day=self::strtoken($date,1,".")*1;
            $month=self::strtoken($date,2,".")*1;
            $year=self::strtoken($date,3,".")*1;
        } else {
            $day=self::strtoken($date,3,"-")*1;
            $month=self::strtoken($date,2,"-")*1;
            $year=self::strtoken($date,1,"-")*1;
        }
        $out="";
        if ($day>0 and $day<=31 and $month>0 and $month<=12 and $year>1900) {
            $out=date("Y-m-d",mktime(0,0,0,$month,$day,$year));
        }
        return $out;
    }    
    
    
}