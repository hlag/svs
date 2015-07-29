<?php
/*
 * Created on 31.10.2008
 *
 * File: Logger.php
 * 
 * Author: proggen
 */
define ("LOG_SQL",1);
define ("LOG_ERROR",2);
define ("LOG_ABLAUF",3);
define ("LOG_TEMPLATE", 4);

class Logger
{
	private static $instance;
	private $settingArray;

	/**
         *
         * @return Logger 
         */
        public static function getInstance()
	{
		if (!isset(self::$instance))
		{
                    self::$instance = new Logger();
        }
        return self::$instance;
	}

	private function __construct()
	{
		$this->settingArray = parse_ini_file(PATH."godot/.htconfig.ini", false);
	}
	
	public function Log($value, $logSelect=2)
	{
		//echo $logSelect;
		//echo $value; exit();
        if (is_array($value))
            $value = print_r($value,true);
        if (is_object($value))
            $value = print_r($value,true);
        if(!isset($_SERVER['SERVER_ADDR']))
        {
            $prefix = "Remote";
        }
        else
        {
        if ($_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '192.168.2.11' || $_SERVER['HTTP_HOST'] == '192.168.2.130' || $_SERVER['SERVER_ADDR'] == '192.168.91.131'  || $_SERVER['SERVER_ADDR'] == '192.168.91.128'|| $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == '192.168.2.165'|| $_SERVER['SERVER_ADDR'] == '192.168.2.110')
                $prefix = 'Local';
            else 
                $prefix = "Remote";
        }
		switch ($logSelect)
		{
			case LOG_SQL:
				if (Configuration::getInstance()->get('Logger',$prefix.'SQLLog'))
				{
					error_log($this->getTimeStamp()." ".$value."\n",3,PATH."logs/".Configuration::getInstance()->get('Logger',$prefix.'SQLFile'));
				}
				break;
			case LOG_ERROR:
				if (Configuration::getInstance()->get('Logger',$prefix.'ErrorLog'))
				{
					error_log($this->getTimeStamp()." ".$value."\n",3,PATH."logs/".Configuration::getInstance()->get('Logger',$prefix.'ErrorFile'));
				}
				break;
			case LOG_ABLAUF:
				if (Configuration::getInstance()->get('Logger',$prefix.'AblaufLog'))
				{
					error_log($this->getTimeStamp()." ".$value."\n",3,PATH."logs/".Configuration::getInstance()->get('Logger',$prefix.'AblaufFile'));
				}
				break;
                        case LOG_TEMPLATE:
				if (Configuration::getInstance()->get('Logger',$prefix.'TemplateLog'))
				{
					error_log($this->getTimeStamp()." ".$value."\n",3,PATH."logs/".Configuration::getInstance()->get('Logger',$prefix.'TemplateLogFile'));
				}
				break;
					
		}
		//echo "test";
	}
	
	private function getTimeStamp()
 	{
 		$time=getdate();
 		$mtime = array_sum(explode(' ', microtime()));
 		$mtime = number_format($mtime,3, '.', '');
 		return $time['mday'].'.'
 				.$time['mon'].'.'
 				.$time['year'].'|'
 				.$time['hours'].':'
 				.$time['minutes'].':'
 				.$time['seconds'].substr($mtime, strpos($mtime,'.'));
 	}
}