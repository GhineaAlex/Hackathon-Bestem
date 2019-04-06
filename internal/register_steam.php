<?php

define ( 'ALLOW' , true ) ;
error_reporting ( E_ALL ) ;
$path = substr ( $_SERVER [ 'SCRIPT_FILENAME' ] , 0 , strpos ( $_SERVER [ 'SCRIPT_FILENAME' ] , '/internal/register.php' ) ) ;

require_once '../engine/lightopenid.php' ;
require_once '../engine/session.php' ;
require_once '../engine/functions.php' ;
require_once '../engine/hash.php' ;
require_once '../global_config.php' ; 

engine\session::init ( ) ;

if ( empty ( engine\session::get ( 'loggedin' ) ) ) {
    try {
        $openid = new engine\LightOpenID ( $GLOBAL_CONFIG [ 'OPEN_URL' ] ) ;
        if ( ! $openid -> mode ) {
            if ( isset ( $_POST [ 'submit' ] ) ) {
                $openid -> identity = 'http://steamcommunity.com/openid' ;
                $openid -> required = array ( 'namePerson/friendly' , 'contact/email' ) ;
                $openid -> optional = array ( 'namePerson/first' ) ;
                header ( 'Location: ' . $openid -> authUrl ( ) ) ;
            }
        }
        else if ( $openid -> mode == 'cancel' ) {
            header ( $_SERVER [ 'SERVER_PROTOCOL' ] .' 409 Conflict' ) ;
            exit ;
        }
        else {
        	if ( $openid -> validate ( ) ) {
        		$steamid = str_replace ( 'https://steamcommunity.com/openid/id/' , '' , $openid -> identity ) ;
                $file = file_get_contents ( 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $GLOBAL_CONFIG [ 'API_KEY' ] . '&steamids=' . $steamid ) ;
                $data = json_decode ( $file ) ;

                $data = $data -> { 'response' } -> { 'players' } [ 0 ] ;

                engine\session::set ( 'provider' , 'Steam' ) ;
                engine\session::set ( 'openid' , $steamid ) ;
                engine\session::set ( 'personaname' , $data -> personaname ) ;
                engine\session::set ( 'email' , '' ) ;
                engine\session::set ( 'loggedin' , 1 ) ;

                try {

                    $handle = new PDO ( $GLOBAL_CONFIG [ 'DB_TYPE' ] . ':host=' . $GLOBAL_CONFIG [ 'DB_HOST' ] . ';dbname=' . $GLOBAL_CONFIG [ 'DB_DB' ] , $GLOBAL_CONFIG [ 'DB_USER' ] , $GLOBAL_CONFIG [ 'DB_PASSWORD' ] ) ;

                    $sth = $handle -> prepare ( "SELECT `ID` FROM `accounts` WHERE `openid`=:steam AND `provider`='Steam'" ) ;
                    $sth -> execute ( array ( 
                        ':steam' => $steamid
                    ) ) ;
                    $count = $sth -> rowCount ( ) ;
                    $sth = null ;

                    if ( $count == 0 ) {
                        $sth = $handle -> prepare ( 'INSERT INTO `accounts` (`openid`, `provider`, `personaname`, `trade_link`, `profileurl`, `avatar`, `avatar_medium`, `avatar_full`, `cart`, `chat_session`, `referral_code`, `referral_ip`) VALUES (:openid, \'Steam\', :pers, \'\' , :url, :a, :am, :af, \'\' , \'\', :ref_c, \'\')' ) ;
                        $sth -> execute ( array ( 
                            ':openid' => $steamid ,
                            ':pers' => $data -> personaname ,
                            ':url' => $data -> profileurl ,
                            ':a' => $data -> avatar ,
                            ':am' => $data -> avatarmedium ,
                            ':af' => $data -> avatarfull ,
                            ':ref_c' => engine\Hash::create ( 'crc32' , '$' . $steamid . '$' , $GLOBAL_CONFIG [ 'HASH_KEY' ] )

                        ) ) ;
                    }
                    else {
                        $sth = $handle -> prepare ( 'UPDATE `accounts` SET `personaname`=:pers, `profileurl`=:url, `avatar`=:a, `avatar_medium`=:am, `avatar_full`=:af WHERE `openid`=:openid' ) ;
                        $sth -> execute ( array ( 
                            ':pers' => $data -> personaname ,
                            ':url' => $data -> profileurl ,
                            ':a' => $data -> avatar ,
                            ':a' => $data -> avatar ,
                            ':am' => $data -> avatarmedium ,
                            ':af' => $data -> avatarfull ,
                            ':openid' => $steamid
                        ) ) ;
                    }

                    $handle = null ;

                    header ( 'Location: ' . $GLOBAL_CONFIG [ 'OPEN_URL' ] ) ;

                }
                catch ( PDOException $e ) {
                    header ( $_SERVER [ 'SERVER_PROTOCOL' ] .' 409 Conflict' ) ;
                    exit ;
                }
    	    }
        }
    }
    catch ( ErrorException $e ) {
        echo $e -> getMessage ( ) ;
    }
}
else {
    header ( $_SERVER [ 'SERVER_PROTOCOL' ] .' 400 Bad Request' ) ;
    exit ;
}