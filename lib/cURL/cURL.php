<?php
//require_once(PATH."lib/Logging/Logger.php");

class cURL 
{
    private static $instance;
    private $userAgent = "avaris | godot (http://www.avaris-godot.de/)";
    private $timeout = 120;
    private $connectTimeout = 60;
    private $followLocation = true;
    private $userName = null;
    private $password = null;
    private $language = null;

    /**
     *
     * @return cURL
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new cURL();
        }
        return self::$instance;
    }

    
    private function __construct()
    {
    }
    
    public function setLanguage($language)
    {
        $this->language=$language;   
    }
    
    public function setUsername($username)
    {
        $this->userName=$username;
    }
    
    public function setPassword($password)
    {
        $this->password=$password;
    }
    
    public function getFile($file, $localPath, $newFileName)
    {
        if (!is_dir($localPath))
            mkdir ($localPath);
        $out = fopen($localPath.$newFileName,'wb');
        //bfopen
        $ch = curl_init($file); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);    
        curl_setopt($ch, CURLOPT_FILE, $out); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_URL, $file); 

        curl_exec($ch); 
        //echo "<br>Error is : ".curl_error ( $ch); 

        curl_close($ch); 
        fclose($out);
    }


    public function getURL($url)
    {
        //echo $url;
        //$url = rawurlencode($url);
        //echo $url;
        $ch = curl_init();
        $ret = curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        if ($this->language!=null)
            $ret = curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: '.$this->language));
        $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        $ret = curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        $ret = curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $ret = curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->followLocation);
        //echo $this->password;
        if ($this->password!=null && $this->userName!=NULL)
        {
            $ret = curl_setopt($ch, CURLOPT_USERPWD, $this->userName.":".$this->password);    
        }
        $inhalt = curl_exec($ch);
        //echo $url;
	  //  echo $inhalt;
        curl_close($ch);
        return $inhalt;
    }
}
?>
