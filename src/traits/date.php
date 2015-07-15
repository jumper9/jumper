<?php
namespace jumper;

trait dateTrait 
{
    public static function datetime2sql($datetime) {
		$date = self::strtoken($datetime,1," ");
		$time = self::strtoken($datetime,2," ");
		
		$date = self::date2sql($date);
		
		return $date."T".$time;
	}
	
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
        if ($day>0 && $day<=31 && $month>0 && $month<=12 && $year>1900) {
            $out=date("Y-m-d",mktime(0,0,0,$month,$day,$year));
        }
        return $out;
    }    
    
    
}
