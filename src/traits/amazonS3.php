<?php
namespace jumper;

trait amazonS3Trait 
{

	public static function amazonS3Upload($uploadFile, $prefix = "") 
	{
		if(!$prefix && defined("AMAZON_DEFAULT_PREFIX")) {
			$prefix = AMAZON_DEFAULT_PREFIX;
		}
        $configClass=APP_NAMESPACE."\config";
        $s3 = $configClass::getAmazonS3();
		
        $out="";
        if ($s3->putObjectFile($uploadFile, AMAZON_BUCKET_NAME, $prefix.baseName($uploadFile), \S3::ACL_PUBLIC_READ)) {
            $out = AMAZON_BASE_PATH . $prefix.baseName($uploadFile);
        }
        return $out;
    }	
}
