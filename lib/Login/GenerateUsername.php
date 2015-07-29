<?php
class GenerateUsername
{
    public function __construct()
    {
        
    }
    
    public function calculate($name, $vorname)
    {
        return $this->bereinige(substr($vorname,0,1).$name);
    }
    
    private function bereinige($username)
    {
        $returnvalue=NULL;
        $returnvalue=mb_strtolower($username,"UTF-8");
        $umlaute =  Array("/".iconv("ISO-8859-1","UTF-8",chr(228))."/","/".iconv("ISO-8859-1","UTF-8",chr(246))."/","/".iconv("ISO-8859-1","UTF-8",chr(252))."/"        //03.02.2007 MG
                        ,"/".iconv("ISO-8859-1","UTF-8",chr(196))."/","/".iconv("ISO-8859-1","UTF-8",chr(214))."/","/".iconv("ISO-8859-1","UTF-8",chr(220))."/"
                        ,"/".iconv("ISO-8859-1","UTF-8",chr(223))."/");
        $replace = Array("ae","oe","ue","ae","oe","ue","ss");
        $value = preg_replace($umlaute, $replace, $returnvalue);
        $value = str_replace(" ","-",$value);
        return $value;
    }
}
?>