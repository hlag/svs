<?php
/*
 * Created on 16.01.2006
 *
 * Author: Mats
 *
 * Version 1.2
 *
 * File: ModuleView.php
 *
 */
//require ("templateParser.php");

class ModuleView
{
	var $backEndTemplate;
	var $frontEndTemplate;
	var $templateFile;
	var $linktext;
	var $LinkName;
	protected $language;

	function ModuleView()
	{
		$this->init();
	}

	function init()
	{
	}

	function renderTemplate($array)
	{
		//print_r($array);
		//echo $this->templateFile;
		$tp= new templateParser($this->templateFile);
		$tp->parseTemplate($array);	    // display generated page
		return $tp->display();
	}

	function renderTemplateAndTidy($array)
	{
		//print_r($array);
		//echo $this->templateFile;
		$tp = new templateParser($this->templateFile);
		$tp->parseTemplateAndTidy($array);	    // display generated page
		return $tp->display();
	}

	function setLinkName($link)
	{
		$this->LinkName=$link;
	}

	function setLinkText($text)
	{
		$this->LinkText = $text;
	}

	function renderFrontEnd($array)
	{
		$this->setTemplate($this->frontEndTemplate);
		return $this->renderTemplate($array);
	}

	function renderBackEnd($array)
	{
		$this->setTemplate($this->backEndTemplate);
		return $this->renderTemplate($array);
	}

	function setTemplate($template)
	{
		$this->templateFile=$template;
	}


	function setLanguage($lang)
	{
		$this->language=$lang;
	}

	function setFrontendTemplate($template)
	{
		$this->frontEndTemplate=$template;
	}

	function setBackendTemplate($template)
	{
		$this->backEndTemplate=$template;
	}
}
?>
