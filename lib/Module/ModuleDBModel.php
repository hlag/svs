<?php
/*
 * Created on 16.01.2006
 *
 * Author: Mats
 *
 * Version 1.2
 *
 * File: ModuleDBModel.php
 *
 */
class ModuleDBModel
{
	var $DBConnection;
	protected $error;
	protected $errorList;
	protected $language;
	protected $user;
	protected $group;

	function ModuleBDModel()
	{

	}

	function getErrorList()
	{
		return $this->errorList;
	}

	function setUser($user)
	{
		$this->user=$user;
	}

	function setGroup($group)
	{
		$this->group=$group;
	}

	function isError()
	{
		return $this->error;
	}

	function printError()
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

	function setConnection($connection)
	{
		$this->DBConnection=$connection;
	}

	function getConnection()
	{
		return $this->DBConnection;
	}

	function getDataFromDatabase($array)
	{
	}

	function setLanguage($lang)
	{
		$this->language=$lang;
	}
}
?>
