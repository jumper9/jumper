<?php
namespace jumper;

trait imageTrait 
{
    
    public static function base64_to_jpeg($base64_string, $output_file) 
    {
        $ifp = fopen($output_file, "wb"); 
        fwrite($ifp, base64_decode($base64_string)); 
        fclose($ifp); 
        return $output_file; 
    }    

    public static function png2jpg($input_file) 
    {
        if (strtolower(substr($input_file,-4))!=".png") {
            return false;
        }

        $output_file = strtolower(substr($input_file,0,-4)).".jpg";

        $input = imagecreatefrompng($input_file);
        list($width, $height) = getimagesize($input_file);
        $output = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($output,  255, 255, 255);
        imagefilledrectangle($output, 0, 0, $width, $height, $white);
        imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
        imagejpeg($output, $output_file);
        unlink($input_file);

        return $output_file;
    }    
    
}