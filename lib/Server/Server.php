<?php
class Server
{
    public static function isLocalServer()
    {
        $localServers = Configuration::getInstance()->get('Server', 'localServer');
        $serverArray = explode(',',$localServers);
        //print_r($_SERVER);
        foreach ($serverArray as $servers)
        {
            if ($_SERVER['SERVER_ADDR']== $servers)
                return true;
            elseif(strpos($_SERVER['SERVER_NAME'], $servers) > 1)
                return true;
        }
        return false;
    }
}