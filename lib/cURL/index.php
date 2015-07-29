<?php
class testCurl
{
	public function __construct()
	{
		require_once("cURL.php");
                $file = 'http://www.hhu.de/home/fileadmin/redaktion/Oeffentliche_Medien/Presse/Pressemeldungen/Bilder/Haeussinger_www.bmp';
                $fileArray = explode('/',$file);
                echo "getFile".$file."NEW File".$fileArray[count($fileArray)-1];
                cURL::getInstance()->getFile($file, "/var/www/Administrator2.6.1/lib/cURL/test/", $fileArray[count($fileArray)-1]);
		
	}
}
new testCurl();
?>
