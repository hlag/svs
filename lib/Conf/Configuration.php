<?php
/*
 * Created on 30.10.2008
 *
 * File: Configuration.php
 * 
 * Author: proggen
 * 
 * Letzte ?nderung von: $LastChangedBy: proggen $
 * Revision           : $LastChangedRevision: 14 $
 * Letzte ?nderung am : $LastChangedDate: 2009-02-12 13:35:04 +0100 (Do, 12 Feb 2009) $
 * 
 */
class Configuration
{
	private static $instance;
	private $settingArray;
	private $complexArray;

	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
            self::$instance = new Configuration();
        }
        return self::$instance;
	}
    
	private function __construct()
	{
		$this->settingArray = parse_ini_file(PATH."godot/.htconfig.ini", false);
		$this->complexArray = parse_ini_file(PATH."godot/.htconfig.ini", true);
        //print_r($this->complexArray);
	}
	
	public function get($name1, $name2)
	{
		if (isset($this->complexArray[$name1][$name2]))
			return $this->complexArray[$name1][$name2];
		else
			return NULL;
	}
    
    public function getArray($name)
    {
        if (isset($this->complexArray[$name]))
            return $this->complexArray[$name];
        else
            return NULL;
    }
	
	public function __get($name)
	{
		if (isset($this->settingArray[$name]))
			return $this->settingArray[$name];
		else
			return NULL;
	}
}
?>