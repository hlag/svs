<?php
/*
 * Created on 11.01.2006
 *
 * Author: Mats
 *
 * File: Module.php
 *
 */
/**
 * This is the class that descripes the Modules
 * Each Module has to be derived from Module to ensure it has the right interface and some functionality.
 *
 *	Version 1.3
 *
 *	Changed 10.09.2007
 *	Neue Methoden: showModuleInBacken() und showModuleInFrontend(). Sind f�r die Unterscheidung, ob Module im Backendtree
 *	dargestellt werden und ob sie im Frontend eingeh�ngt werden k�nnen.
 *
 *  Changed 14.08.2008 JA
 *  Neue Methoden
 * 
 *  Changed 30.10.2008 MG
 *  Module können jetzt headerRelocation steuern
 *  
 *  Changed 21.08.2009 JA
 *  Added XHR Handling
 */

require_once (PATH."lib/Module/ModuleDBModel.php");
require_once (PATH."lib/Module/ModuleView.php");

class Module
{
	protected $moduleName;
	protected $moduleTreeName;
	protected $LinkName;
	protected $DBModel;
	protected $HTMLView;
	protected $dataArray;
	protected $requester;
	protected $user;
	protected $language;
	protected $controller;
	protected $DBConnection;
    protected $linktext;
    protected $permission;
	protected $group;
	protected $error;
	protected $errorList;
	protected $headerRelocation;
	protected $header;
        protected $renderForm;
        protected $isOverrideTemplate;
        protected $overrideTemplate;


	public function __construct()
	{
		$this->moduleName="";
		$this->moduleNameTree="";
		$this->LinkName="";
		$this->DBModel=new ModuleDBModel($this);
		$this->HTMLView= new ModuleView($this);
		$this->headerRelocation=false;
$this->dataArray=Request::getInstance()->getRequests();
		$this->requester=Request::getInstance();
                $this->renderForm = true;
                $this->isOverrideTemplate=false;
		$this->init();
	}
	
	public function isHeaderRelocation()
	{
		return $this->headerRelocation;
	}
	
	public function getHeader()
	{
		return $this->header;
	}
        
        public function isOverrideTemplate()
        {
            return $this->isOverrideTemplate;
        }
        
        public function setOverrideTemplate()
        {
            $this->isOverrideTemplate=true;
        }
        
        public function getOverrideTemplate()
        {
            return $this->overrideTemplate;
        }

        public function renderForm()
        {
            return $this->renderForm;
        }
        
        public function overrideContent()
        {
            return false;
        }

	public function setLanguage($lang)
	{
		$this->language=$lang;
		$this->DBModel->setLanguage($lang);
		$this->HTMLView->setLanguage($lang);
		$this->loadIniFile();
	}

	public function isRightRight()
	{
		return false;
	}

	public function getRightRight()
	{
		return "";
	}
	
	public function getHiddenInputs() // JA
	{
		Responder::getInstance()->register('Module', $this->moduleName);
		return array("Module" => $this->moduleName);
	}
	
	public function hasModuleButtons() // JA
	{
		return false;
	}

        public function isCachable()
        {
            return true;
        }

	public function getModuleButtons() // JA
	{
		return array();
	}
	
	public function monitorChangesInBackend() // JA
	{
		return false;
	}
	
	public function usesGlobalSaveButton() // JA
	{
		return true;
	}
	
	public function handleXhr() // JA 21.08.2009
	{
		$this->badRequest();
	}
	
	public function badRequest() // JA 21.08.2009
	{
		header("HTTP/1.1 400 Bad Request");
		die();
	}

	protected function printError()
	{
		$returnvalue=NULL;
		if ($this->error)
		{
			foreach ($this->errorList as $line)
			{
				$returnvalue.=$line."<br>";
			}
		}
		return  $returnvalue;
	}

	protected function loadIniFile()
	{
		return array();
	}

	function setController($Controller)
	{
		$this->controller=$Controller;
	}

	function setPermission($permission)
	{
		$this->permission=$permission;
	}

	function setArray($array)
	{
		$this->dataArray=$array;
	}

	function setRequester($requester)
	{
		$this->requester=$requester;
	}

	function setUser($user)
	{
		$this->user=$user;
		$this->DBModel->setUser($user);
	}

	function setGroup($group)
	{
		$this->group=$group;
		$this->DBModel->setGroup($group);
	}

	function showModuleInFrontend()
	{
		return true;
	}
        
        
        public function hasJavascript()
        {
            return false;
        }
        
        public function getJavascript()
        {
            return '';
        }
    
    public function hasFrontendNavigation()
    {
        return false;
    }

    public function getFrontendNavigationData($dataArray, $aktArticle)
    {
        return array();
    }
    
    public function hasModuleParameter()
    {
        return false;
    }
    
    public function getModuleParameterHTML($render = false)
    {
        return "";
    }
    
    public function getModuleParameter2HTML($render = false)
    {
        return "";
    }
    
	function showModuleInBackend()
	{
		return true;
	}

	/**
	 * This Method is to calculate the Data for this Module
	 */
	function calculate()
	{
	}

	/**
	 *
	 */
	function getArray()
	{
		return $this->dataArray;
	}

	/**
	 * This Method is to do additional initialisation for this Module
	 *
	 * @return void
	 */
	function init()
	{
	}

	/**
	 * If you need to show Information in the NavigationTree
	 *
	 * @return Tree-Array
	 */
	function getTree()
	{
		return "";
	}

	/**
	 * Here the HTML-String for the Frontend (the Website) has to be returned
	 *
	 * @return HTML-String
	 */
	function getFrontend()
	{
	}

	/**
	 * Here the HTML-String for the Backend (Administrator on the right) has to be returned
	 *
	 * @return HTML-String
	 */
	function getBackend()
	{
	}

	/**
	 * Here the HTML-String for the "subkategorie" has to be returned.
	 * It is necessary to get the complete String and not an Array!!!!
	 *
	 * @return HTML-String
	 */
	function getMidContent()
	{
	}

	/**
	 * The Connection for the Modules is set. It is not needed to overload it in the specific module.
	 */
	function setConnection($conn)
	{
		$this->DBConnection=$conn;
		$this->DBModel->setConnection($conn);

	}

	function setLinkName($link)
	{
		$this->LinkName=$link;
		$this->HTMLView->setLinkName($link);
	}

	function setLinkText($text)
	{
		$this->LinkText = $text;
		$this->HTMLView->setLinkText($text);
	}

	function getLinkName()
	{
		return $this->LinkName;
	}

	function getModuleName()
	{
		return $this->moduleName;
	}

	function getModuleTreeName()
	{
		if ($this->moduleTreeName=="")
		{
			return $this->moduleName;
		}
		else
		{
			return $this->moduleTreeName;
		}

	}

}
?>