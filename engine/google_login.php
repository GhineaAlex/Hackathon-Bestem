<?php
	include_once 'src/Google_Client.php';
	include_once 'src/contrib/Google_Oauth2Service.php';
	include_once '../global_config.php';
	
	$clientId = '23509325499-ltog0t8f81kaipqsn898rojqt1h3h4g5.apps.googleusercontent.com';
	$clientSecret = '2PDOzPfohhqVs8YmNQCORHRn'; 
	$redirectURL = $GLOBAL_CONFIG [ 'OPEN_URL' ] . '/internal/register_google.php';
	
	$gClient = new Google_Client();
	$gClient->setApplicationName('Antsy.xyz');
	$gClient->setClientId($clientId);
	$gClient->setClientSecret($clientSecret);
	$gClient->setRedirectUri($redirectURL);
	$google_oauthV2 = new Google_Oauth2Service($gClient);
?>
