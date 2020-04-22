googledrive-api-php-sdk derived from Original Google API client written by ImdadAli


Get GOOGLE API CLIEN v2 via composer:

- composer require google/apiclient:"^2.0"
- add composer's "autoload.php" file with absolute path in "gdrive_config.php" file.



Get Credentials From Google Developer Console:

1. go to https://console.developers.google.com/
2. Enable Google Drive API from Library.
3. Create "OAuth Conset screen" from developer console.
4. Create "OAuth client ID" From "Credentials" in developer console.
5. Add redirect URI in OAuth Client ID edit like "http://YOUR_DOMAIN/PATH_TO_FILE/gdrive_verify.php".



Verify any google drive account (Only for first time):

1. Set all credentials in to "gdrive_config.php" which you got from google developer console.
2. Run gdrive_config.php, From that you will get the refreshToken.
3. Configure refreshToken in "gdrive_config.php" file.



Examples:

1. For upload any file (No size limitation) check example in "upload.php".
2. For delete any file check example in "delete.php".

Note: All functions only support one parent folder in this version (Not recursively).



Got it ????

Have Fun !!!!!