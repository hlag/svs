<?php
/*
 * Created on 18.12.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class Responder
{
	private $responds;
	static $instance;
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
            self::$instance = new Responder();
        }
        return self::$instance;
	}

    private function __construct()
    {
		$this->responds=array();
	}
	
	public function register($name, $value)
	{
        $this->responds[$name]=$value;
        //print_r($this->responds);
	}
    
    public function unregister($name)
    {
        if (isset($this->responds[$name]))
            unset($this->responds[$name]);
    }
	
    public function getHiddenInputs()
    {
        return $this->responds;
    }
    
	public function renderHiddenInputs()
	{
		$returnvalue="";
		foreach (array_keys($this->responds) as $key)
		{
			$returnvalue.= '<input type="hidden" value="'.$this->responds[$key].'" name="'.$key.'">'."\r\n";
		}
		return $returnvalue;
	}
	
	public function init()
	{
		$this->responds=array();
	}
	
}
?>
