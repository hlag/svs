<?php
class checkURL
{
    public function __construct()
	{
	}
    
    public function check($URL, $ID, $langID) // added sanitize check JA 23.01.2009
    {
        $URL = $this->sanitzeURL($URL);
        
        $SQLQuery = "SELECT * FROM `".AGDO::getInstance()->getDBConnector()->getDescriptionTable()."` WHERE article_url = '".$URL."' and language_id = ".$langID;
        $result= AGDO::getInstance()->GetAll($SQLQuery);
        $count = count($result);
        //echo $count;
        //print_r($result);
        if ($count==0 )        // Neuer Eintrag
            return $URL;
        else
        {
            if ($count==1 && $result[0]['article_id']==$ID)
            {
                return $URL;
            }
            $SQLQuery = "SELECT COUNT(*) as count FROM `".AGDO::getInstance()->getDBConnector()->getDescriptionTable()."` WHERE article_url LIKE '".$URL."%' and language_id = ".$langID;
            $result=AGDO::getInstance()->GetAll($SQLQuery);
            return $URL.(($result[0]['count'])+1);
        }

    }
    
    private function sanitzeURL($url) // JA 23.01.2009
    {
        // step 1: lowercase
        $url = strtolower($url);
        
        // step 2: replacement: things we want to keep
        $replace = array(
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'Ä' => 'ae',
            'Ö' => 'oe',
            'Ü' => 'ue',
            'ß' => 'ss',
            ' ' => '-',
            '/' => '-',
            '.' => '-'
        );
        $url = str_replace(array_keys($replace),array_values($replace),$url);
        
        // step 3: kill others
        return preg_replace("/[^a-z-_0-9]+/U",'',$url);
    }
}
?>
