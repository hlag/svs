<?php
/*
 * Created on 04.01.2006
 *
 * Author: Mats
 *
 * File: ModulManager.php
 *
 *
 *	Version 1.1
 *
 * Changed MG 12.09.2007
 * BugFix ModuleTreeName wird jetzt auch bei der Auswahl der Module korrekt angezeigt.
 * Aber der Name der Klasse/Verzeichnis wird in der DB gespeichert!!
 */
/**
 * Class ModuleManager:
 * This is the Basic Class for managing the Modules in Administrator
 *
 *	Version 1.2
 *
 *	Changed 10.09.2007
 *	Implementierung der Frontend und Backendmodule
 */
//require_once ("Module.php");
require_once (PATH."lib/Module/Module.php");
class ModuleManager
{
	private $modules = array();
	private $modulesPath= NULL;
	private static $instance;

  /**
   * Initialising the ModuleManger
   *
   * The ModuleManager reads the possible Modules from the ModulesDirectory
   * @return ModuleManager
   */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
                self::$instance = new ModuleManager();
            }
        return self::$instance;
	}
	
	private function ModuleManager()
	{
		$this->setModulesPath();
		$modules=$this->getModuleNamesFromDirectory();
		$constructedModules = $this->constructModules($modules);
		foreach ($constructedModules as $module)
		{
			$this->registerModule($module);
		}
		//sort($this->modules,SORT_STRING);
		//print_r($this->modules);
		//print_r($this->modules);
	}
	
	function getModuleNameListBackend()
	{
		$returnvalue=array();
		foreach ($this->modules as $Module)
		{
			if ($Module->showModuleInBackend())
				$returnvalue[]=$Module->getModuleName();
		}
		sort($returnvalue,SORT_STRING);
		return $returnvalue;
	}

	function getModuleNameList()
	{
		$returnvalue=array();
                //$this->modules = sort($this->modules, SORT_STRING);
		foreach ($this->modules as $Module)
		{
			if ($Module->showModuleInFrontend())
			{
				$temp['ModuleName']=$Module->getModuleName();  				//MG 12.09.2007
				$temp['ModuleTreeName']=$Module->getModuleTreeName();		//MG 12.09.2007
				$returnvalue[]=$temp;										//MG 12.09.2007
			}
		}
		sort($returnvalue,SORT_STRING);
		return $returnvalue;
	}

    function getModuleTreeName($moduleName)
	{
		foreach ($this->modules as $module)
	    {
	      if ($module->getModuleName()==$moduleName)
	          return $module->getModuleTreeName();
	    }
	    //No Module with the specific name is found
	    return false;
	}

  function getModuleByName($name)
  {
    foreach ($this->modules as $module)
    {
      if ($module->getModuleName()==$name)
          return $module;
    }
    //No Module with the specific name is found
    return false;
  }

  function getModuleByID($id)
  {
    if (isset($this->modules[$id]))
      return $this->modules[$id];
    else
      return false;
  }

  function registerModule($module)
  {
    $this->modules[]=$module;
  }

  	function getModuleNamesFromDirectory()
  	{
  		$returnvalue=array();
		if ($handle = opendir($this->modulesPath))
		{
			while (false !== ($file = readdir($handle)))
   			{
   				if ($file!="." && $file!=".." && $file!=".svn" && is_dir($this->modulesPath.$file) && $file!='kompass.jpg')
       				$returnvalue[]=$file;
   			}
		}

   		closedir($handle);
   		//print_r($returnvalue);
   		return $returnvalue;
  	}

  	function constructModules($ModuleNames)
  	{
  		$returnvalue=array();
  		foreach ($ModuleNames as $Module)
  		{
  			if (file_exists($this->modulesPath.$Module."/".$Module.".php"))
  			{

  				require ($this->modulesPath.$Module."/".$Module.".php");
  				$returnvalue[]=new $Module;
  			}
  		}
		return $returnvalue;
  	}

	function setModulesPath()
	{
		$this->modulesPath=MODULEPATH;
	}

}
?>