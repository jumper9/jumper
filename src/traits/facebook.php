<?php
namespace jumper;

trait facebookTrait 
{

    private static function fb_init() 
    {
		$configClass=APP_NAMESPACE."\config";
        $configClass::setFBDefaultApplication();
    }
	
    public static function fb_getProfile() 
    {
		$profile = false;
		try {
			self::fb_init();
			$session = new \Facebook\FacebookSession(f::getParam("fbUserAccessToken"));
			$profile = (new \Facebook\FacebookRequest( $session, 'GET', '/me' ))->execute()->getResponse();
		} catch (\Facebook\FacebookAuthorizationException $ex) {
			$profile = false;
		} catch (Exception $ex) {
			$profile = false;
		} 
		
        return (array) $profile;
    }
    
    public static function fb_getAlbums() 
    {
		$albums = false;
		try {
			self::fb_init();
			$session = new \Facebook\FacebookSession(f::getParam("fbUserAccessToken"));
			$albums = (new \Facebook\FacebookRequest( $session, 'GET', '/me?fields=albums.limit(100).fields(id,name,photos.limit(1).fields(id,source,height,width))' ))->execute()->getGraphObject()->asArray();
		} catch (\Facebook\FacebookAuthorizationException $ex) {
			$albums = false;
		} catch (Exception $ex) {
			$albums = false;
		} 
			
        return (array) $albums;
    }    
    
    public static function fb_getPhotos($albumId) 
    {
		$photos = false;
		try {
			self::fb_init();
			$session = new \Facebook\FacebookSession(f::getParam("fbUserAccessToken"));
			$photos = (new \Facebook\FacebookRequest( $session, 'GET', "/$albumId/photos?limit=500&fields=id,source,height,width" ))->execute()->getGraphObject()->asArray();
		} catch (\Facebook\FacebookAuthorizationException $ex) {
			$photos = false;
		} catch (Exception $ex) {
			$photos = false;
		} 
	
        return (array) $photos;
    }    
    
    public static function fb_savePhoto($photoId,$filename) 
    {
		$out=false;
		try {
			self::fb_init();
			$session = new \Facebook\FacebookSession(f::getParam("fbUserAccessToken"));
			$image = (new \Facebook\FacebookRequest( $session, 'GET', "/$photoId" ))->execute()->getGraphObject()->asArray();
			$image1=(array)($image["images"][0]);
			$imageUrl=$image1["source"];
			$imageContents=file_get_contents($imageUrl);
			
			$fp = fopen($filename, 'w');
			fwrite($fp, $imageContents);
			fclose($fp);
			$out=true;
		} catch (\Facebook\FacebookAuthorizationException $ex) {
			$out=false;
		} catch (Exception $ex) {
			$out=false;
		} 
		return $out;
    }
    
    public static function fb_getUserId() 
    {
        self::fb_init();
        $h=getallheaders();
        $fbUserId=0;
		try {
			if (isset($h["user-data"]) and isset($h["user-data"]["_facebook_userid"]) and $h["user-data"]["_facebook_userid"]>0) {
				// user is logged in, and user has FB connect
				$fbUserId=$h["user-data"]["_facebook_userid"];

			} else if (f::getParam("fbUserAccessToken")) {
				// login user FB connect is not found, so: verify with fbUserAccessToken
				
				try {
					$session = new \Facebook\FacebookSession(f::getParam("fbUserAccessToken"));
					$user_profile = (new \Facebook\FacebookRequest( $session, 'GET', '/me' ))->execute()->getGraphObject(\Facebook\GraphUser::className());
					$fbUserId = $user_profile->getId();
				} catch (\Facebook\FacebookAuthorizationException $ex) {
					$fbUserId = -1;
				} catch (Exception $ex) {
					$fbUserId = -1;
				}
			}
		} catch (\Facebook\FacebookServerException $ex) {
			$fbUserId=0;
		} catch (\Facebook\FacebookAuthorizationException $ex) {
			$fbUserId=0;
		} catch (Exception $ex) {
			$fbUserId=0;
		} 
			
        return $fbUserId;
    }
}