<?php

if ( ! defined ( 'ALLOW' ) ) exit ;

use engine\core as core ;
use engine\database as DB ;
use engine\error as Error ;
use engine\functions as __F ;


if ( self::$_user_type == 0 ) exit ( 'Denied' ) ;
if ( ! isset ( $configs [ 1 ] ) ) new Error ( 404 ) ;
$userid = __F::__protected_string ( $configs [ 1 ] ) ;
if ( !is_numeric ( $userid ) ) new Error ( 404 ) ;
$result = DB::__db_query (
	core::$mysql_handle ,
	DB::$DB_FETCH ,
	DB::$DB_PROTECTED ,
	'SELECT * FROM `accounts` WHERE `openid`=:sid' ,
	$userid
) ;
if ( empty ( $result ) ) new Error ( 404 ) ;

self::$_page_array [ 'ACCOUNT_INFORMATION' ] = $result ;

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

