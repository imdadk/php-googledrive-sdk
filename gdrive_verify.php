<?php

//set these three php params for check system/syntax error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//path to gdrive_config.php file which contains all credentials
include_once("gdrive_config.php");

require_once constant('lib_path');

if($_GET && $_GET["code"])
{
	create_access_token($_GET['code']);
}
else
{

	if(!check_existing_tokens())
	{
		$client = new Google_Client();
	    $client->setApplicationName('Google Drive API PHP Quickstart');
	    $client->setScopes('https://www.googleapis.com/auth/drive.file');
	    $client->setClientId(constant('clientId'));
		$client->setClientSecret(constant('clientSecret'));
		$client->setRedirectUri(constant('redirectUri'));
	    $client->setAccessType('offline');
	    $client->setApprovalPrompt('force');

	    $authUrl = $client->createAuthUrl();

	    header('Location: '.$authUrl);


	}

}




function create_access_token($authCode)
{
	$client = new Google_Client();
	$client->setClientId(constant('clientId'));
	$client->setClientSecret(constant('clientSecret'));
	$client->setRedirectUri(constant('redirectUri'));

	$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);


    print_r($accessToken);
}


function check_existing_tokens()
{
	try{

		$client = new Google_Client();

		$client = $client;
		
		$client->setClientId(constant('clientId'));
		$client->setClientSecret(constant('clientSecret'));
		$client->setRedirectUri(constant('redirectUri'));
				 
		$client->refreshToken(constant('refreshToken'));
		$tokens = $client->getAccessToken();

		if(is_array($tokens) && isset($tokens['access_token']))
		{

			echo "All set in config file";

			return true;

		}
		else
		{
			return false;
		}

	}
	catch(Exception $e)
	{
		echo "An error occurred: " . $e->getMessage();

	}
}


		