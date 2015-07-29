<?php
class GoogleCodeGenerator
{
    public static function generateAnalytics()
    {
        $google=array();
        $google['google_account']=Configuration::getInstance()->get('Google', 'google_account');
        if (Server::isLocalServer())
            return "";
        $theme = Configuration::getInstance()->get('Design', 'theme');
        $tp = new templateParser(PATH."FrontEnd/".$theme."/snippets/google_code.htm");
        if (empty($google['google_account']))
            return '';
        else
        {
            $tp->parseTemplate($google);
            return $tp->display();
        }
    }
    
    public static function generateWebmasterTools()
    {
        $google=array();
        
        $google['webmasterTools']=Configuration::getInstance()->get('Google', 'webmasterTools');
        $theme = Configuration::getInstance()->get('Design', 'theme');
        $tp = new templateParser(PATH."FrontEnd/".$theme."/snippets/google_webmaster_tools.htm");
        if (Server::isLocalServer())
            return "";
        if (empty($google['webmasterTools']))
            return '';
        else
        {
            $tp->parseTemplate($google);
            return $tp->display();
        }
    }
}
?>