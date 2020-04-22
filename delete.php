<?php

//set these three php params for check system/syntax error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//set these two php params for upload/delete large file
set_time_limit(0);
ini_set('memory_limit', -1);


//path to gdrive_config.php file which contains all credentials
include_once("gdrive_config.php");

//path to gdrive.php file which contains all functionality
include_once("src/gdrive.php");


// From which folder you want to delete file (Not Mandatory)
$folderName =  "FOLDER_NAME";  


// Filename which you want to delete (Mandatory)
$fileName =  "FILE_NAME";  

try{

	$gdrive = new gdrive(constant('lib_path'));

	//pass config parameters

	$gdrive->clientId = constant('clientId');
	$gdrive->clientSecret = constant('clientSecret');
	$gdrive->redirectUri = constant('redirectUri');
	$gdrive->refreshToken = constant('refreshToken');

	//action param
	$action = "delete";

	//initialization
	$response = $gdrive->initialize($action,$folderName,$fileName);

	print_r($response);

}
catch(\Error $e)
{
	echo "An error occurred: " . $e->getMessage();
}
catch(Exception $e)
{
	echo "An error occurred: " . $e->getMessage();
}



?>