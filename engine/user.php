<?php 

namespace engine ;

use engine\database as DB ;
use engine\view as view ;
use engine\hash as __H ;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class User extends view {

	static function __register ( $email , $password ) {
		DB::__db_query ( 
			core::$mysql_handle ,
			DB::$DB_FETCH_NONE ,
			DB::$DB_PROTECTED ,
			'INSERT INTO `registered_users` (`email`, `password`) VALUES (:email, :password)'  ,
			$email ,
			$password
		) ;
	}

	static function __check_logged ( ) {
		if ( isset ( $_SESSION [ 'openid' ] ) ) {
			return $_SESSION [ 'openid' ] ;
		}
		else return false ;
	}

	static function __user_information ( &$information = array ( ) ) {
		$openid = self::__check_logged ( ) ;
		if ( $openid !== false ) {
			$results = DB::__db_query ( 
				core::$mysql_handle ,
				DB::$DB_FETCH ,
				DB::$DB_PROTECTED ,
				'SELECT * FROM `accounts` WHERE `openid`=:openid'  ,
				$openid
			) ;
			$information = $results ;
		}
	}

	static function __update_user_information ( $openid ) {
		$identif = substr($openid, 0, 3);
		if ($identif == 765) {
			$file = file_get_contents ( 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . core::$global_configs [ 'API_KEY' ] . '&steamids=' . $openid ) ;
			$data = json_decode ( $file ) ;
			$data = $data -> { 'response' } -> { 'players' } [ 0 ] ;
			DB::__db_query ( 
				core::$mysql_handle ,
				DB::$DB_FETCH_NONE ,
				DB::$DB_PROTECTED ,
				'UPDATE `accounts` SET `personaname`=:pers, `profileurl`=:url, `avatar`=:a, `avatar_medium`=:am, `avatar_full`=:af, `chat_session`=:chat WHERE `openid`=:steamid' ,
				$data -> personaname ,
				$data -> profileurl ,
				$data -> avatar ,
				$data -> avatarmedium ,
				$data -> avatarfull ,
				__H::create ( 'md5' , $_SERVER [ 'REMOTE_ADDR' ] . '$' . $openid . '$' , core::$global_configs [ 'HASH_KEY' ] ) ,
				$openid
			) ;
		}
		else {
			DB::__db_query ( 
				core::$mysql_handle ,
				DB::$DB_FETCH_NONE ,
				DB::$DB_PROTECTED ,
				'UPDATE `accounts` SET `chat_session`=:chat WHERE `openid`=:steamid' ,
				__H::create ( 'md5' , $_SERVER [ 'REMOTE_ADDR' ] . '$' . $openid . '$' , core::$global_configs [ 'HASH_KEY' ] ) ,
				$openid
			) ;
			//google
		}
	}




	static function __add_email ( $email ) {


		if ( strlen ( parent::$_user_information [ 'email' ] ) > 0 ) return 0 ;
		if ( ! filter_var ( $email , FILTER_VALIDATE_EMAIL ) ) return 0 ;

		$result = DB::__db_query ( 
			core::$mysql_handle ,
			DB::$DB_FETCH_NONE ,
			DB::$DB_PROTECTED , 
			'UPDATE `accounts` SET `coins`=`coins`+\'50\', `email`=:email WHERE `openid`=:sid;' ,
			$email ,
			parent::$_user_information [ 'openid' ] 
		) ;
		return 1 ;

	}

}
