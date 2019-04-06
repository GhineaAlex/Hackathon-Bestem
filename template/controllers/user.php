<?php

if ( ! defined ( 'ALLOW' ) ) exit ;

use engine\core as core ;
use engine\database as DB ;
use engine\error as Error ;
use engine\functions as __F ;
use engine\user as __U ;


if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;
if ( ! isset ( $configs [ 1 ] ) ) new Error ( 404 ) ;
$email = __F::__protected_string ( $configs [ 1 ] ) ;
if ( strlen($email) < 3 ) new Error ( 404 ) ;

if (core::$body[0] === 'POST') {
	if (isset(core::$body[1]['submit']) || isset(core::$body[1]['user_id'])) {
		__U::add_friend(core::$body[1]['user_id']);
	}
}

$result = DB::__db_query (
	core::$mysql_handle ,
	DB::$DB_FETCH ,
	DB::$DB_PROTECTED ,
	'SELECT * FROM `accounts` WHERE `email`=:sid' ,
	$email
) ;
if ( empty ( $result ) ) new Error ( 404 ) ;

self::$_page_array [ 'ACCOUNT_INFORMATION' ] = $result ;

$me = self::$_user_information['ID'];
if (self::$_user_information['friends'] === 'null' || self::$_user_information['friends'] === '')
    $array = array ();
else
    $array = json_decode(self::$_user_information['friends']);
if ($result['ID'] == self::$_user_information['ID']) 
	self::$_page_array [ 'FRIEND' ] = 3 ;
else {
	if (array_search($result['ID'], $array) !== FALSE) 
		self::$_page_array [ 'FRIEND' ] = 1 ;
	else self::$_page_array [ 'FRIEND' ] = 0 ;
}


$logs = DB::__db_query (
	core::$mysql_handle ,
	DB::$DB_FETCH_ALL ,
	DB::$DB_PROTECTED ,
	'SELECT * FROM `points_logs` WHERE `id`=:id' ,
	$result [ 'ID' ]
) ;

if ( empty ( $logs ) ) self::$_page_array [ 'LOGS_EMPTY' ] = 1 ;
else self::$_page_array [ 'LOGS_EMPTY' ] = 0 ;

self::$_page_array [ 'LOGS' ] = $logs ;
