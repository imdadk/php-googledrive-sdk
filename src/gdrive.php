<?php

class gdrive{
	
	var $clientId;
	var $clientSecret;
	var $redirectUri;
	var $refreshToken;
	
	//variables
	var $fileRequest;
	var $mimeType;
	var $filename;
	var $path;
	var $client;
	var $folderName;
	var $folderDesc;
	var $parentID;
	var $oldfileName;
	var $response = array();
	var $error = array();
	
	
	function __construct($lib_path = ""){
		require_once $lib_path;
		
		$this->client = new Google_Client();

	}
	
	
	function initialize($action,$folderName = "",$fileRequest = ""){
		$client = $this->client;
		
		$client->setClientId($this->clientId);
		$client->setClientSecret($this->clientSecret);
		$client->setRedirectUri($this->redirectUri);
				 
		$client->refreshToken($this->refreshToken);
		$tokens = $client->getAccessToken();
		$client->setAccessToken($tokens);

		if($action == "" OR $fileRequest == "")
		{
			$response['status'] = "failed";
			$response['msg'] = "Please provide atleast mandatory fields.";

			return $response;
		}




		try{


				switch ($action) {
				    case "upload":{

				    	$this->folderName = $folderName;
				    	$this->fileRequest = $fileRequest;

				    	if($this->folderName != "")
						{
							$this->parentID = $this->getFolderExistsCreate();
						}

				        $client->setDefer(true);
						$this->processFile();

						$response['status'] = "success";
						$response['msg'] = "File ".$this->fileName." Uploaded Successfully.";

						return $response;

				        break;

				        }
				    case "delete":{

				    	$this->folderName = $folderName;
				    	$this->oldfileName = $fileRequest;

				    	$msg = $this->deleteOldFile();

				    	if($msg == "")
				    	{
				    		$response['status'] = "success";
				    		$response['msg'] = "File ".$this->oldfileName." Deleted Successfully.";
				    	}
				    	else
				    	{
				    		$response['status'] = "failed";
				    		$response['msg'] = $msg;
				    	}


						return $response;

				    	break;

				    }
				    default:{

				        $response['status'] = "failed";
						$response['msg'] = "Invalid Request";

						return $response;
				    }
				}

			


		}
		catch (\Error $e) {
				$error =  "An error occurred: " . $e->getMessage();

				$response['status'] = "error";
				$response['msg'] = $error;
				return $response;
		}	


			
		
	}
	
	function processFile(){
		
		$fileRequest = $this->fileRequest;
		$path_parts = pathinfo($fileRequest);
		$this->path = $path_parts['dirname'];
		$this->fileName = $path_parts['basename'];

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$this->mimeType = finfo_file($finfo, $fileRequest);
		finfo_close($finfo);
				
		$this->upload();
			
	}
	
	function upload(){

		
		$file = new Google_Service_Drive_DriveFile($this->client);
		$file->setName($this->fileName);
		$chunkSizeBytes = 1 * 1024 * 1024;
		
		$fileRequest = $this->fileRequest;
		$mimeType = $this->mimeType;
		
		$service = new Google_Service_Drive($this->client);

	
		if($this->parentID != "") {
	
				$file->setParents(array($this->parentID));
			}
			


			

				$request = $service->files->create($file);

				

				// Create a media file upload to represent our upload process.
				$media = new Google_Http_MediaFileUpload(
				  $this->client,
				  $request,
				  $mimeType,
				  null,
				  true,
				  $chunkSizeBytes
				);

				$media->setFileSize(filesize($fileRequest));

				// Upload the various chunks. $status will be false until the process is
				// complete.
				$status = false;
				$handle = fopen($fileRequest, "rb");
								
				$filesize = filesize($fileRequest);
				
				// while not reached the end of file marker keep looping and uploading chunks
				while (!$status && !feof($handle)) {
					$chunk = fread($handle, $chunkSizeBytes);
					$status = $media->nextChunk($chunk);  
				}
				
				// The final value of $status will be the data from the API for the object
				// that has been uploaded.
				$result = false;
				if($status != false) {
				  $result = $status;
				}

				fclose($handle);
				// Reset to the client to execute requests immediately in the future.
				$this->client->setDefer(false);

			
	}

	function getFolderExistsCreate() {
		// List all user files (and folders) at Drive root
		$service = new Google_Service_Drive($this->client);

		$found = false;

			$optParams = array(
			  'pageSize' => 999,
			  'fields' => 'nextPageToken, files(id, name)',
			  'q'=> "mimeType = 'application/vnd.google-apps.folder' and trashed=false"
			);

				$get_folders = $service->files->listFiles($optParams);

			

			if (count($get_folders->getFiles()) != 0) {
			    foreach ($get_folders->getFiles() as $folder) {
			        

			    	if($folder->getName() == $this->folderName)
			    	{

			    		return $folder->getId();
			    		break;

			    		$found = true;


			    	}

			    }
			}



		// If not, create one
		if ($found == false) {
			$folder = new Google_Service_Drive_DriveFile();

			//Setup the folder to create
			$folder->setName($this->folderName);

			if(!empty($this->folderDesc))
				$folder->setDescription($this->folderDesc);

			$folder->setMimeType('application/vnd.google-apps.folder');

			//Create the Folder
				$createdFile = $service->files->create($folder, array(
					'mimeType' => 'application/vnd.google-apps.folder',
					));

				// Return the created folder's id
				return $createdFile->id;
	
		}
	}
	function deleteOldFile()
	{

		if($this->oldfileName == "")
			return false;


		// List all user files (and folders) at Drive root
		$service = new Google_Service_Drive($this->client);

		$found = false;

			$optParams = array(
			  'pageSize' => 999,
			  'fields' => 'nextPageToken, files(id, name)',
			  'q'=> "mimeType = 'application/vnd.google-apps.folder' and trashed=false"
			);

				$get_folders = $service->files->listFiles($optParams);


			if (count($get_folders->getFiles()) != 0) {
			    foreach ($get_folders->getFiles() as $folder) {
			        

			    	if($folder->getName() == $this->folderName)
			    	{

			    		$parentID = $folder->getId();
			    		break;

			    		$found = true;


			    	}

			    }
			}


		$service = new Google_Service_Drive($this->client);

		$optParams = array(
			  'pageSize' => 999,
			  'fields' => 'nextPageToken, files(id, name)',
			  'q'=> "parents in '$parentID'"
			  
			);
			$get_files = $service->files->listFiles($optParams);

			$msg = "";

			if (count($get_files->getFiles()) != 0) {
			    foreach ($get_files->getFiles() as $files) {
			        

			    	if($files->getName() == $this->oldfileName)
			    	{

			    		try {
						   	$service->files->delete($files->getId());
						  } catch (\Error $e) {
						    return "An error occurred: " . $e->getMessage();
						  }

						  return;
						  break;

			    	}

			    	

			    }

			    return "This file is not exists";
			}


	}
	

	
}

?>
