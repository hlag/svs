<?php
require (PATH."extern/adodb/adodb.inc.php");
require(PATH."extern/adodb/adodb-exceptions.inc.php");
require_once(PATH."lib/Database/DBConnector.php");
require_once(PATH."lib/Conf/Configuration.php");
require_once(PATH."lib/Logging/Logger.php");
require_once(PATH."lib/Server/Server.php");


/*
 * Created on 15.07.2008
 *
 * File: AGDO.php
 *
 * Author: Matthias Günther
 */
class AGDO
{
	private static $instance;
	private $mode;
	private $conn;
	private $DBConnector;

        /**
         *
         * @returns AGDO
         * @return AGDO
         */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
            self::$instance = new AGDO();
        }
        return self::$instance;
	}

	private function __construct()
	{
		$this->DBConnector = new DBConnector;
		$this->mode = ADODB_FETCH_ASSOC;
		$this->conn = NewADOConnection($this->DBConnector->getDBMS());
		if (!($this->conn->Connect($this->DBConnector->getHost(),
									$this->DBConnector->getUser(),
									$this->DBConnector->getPasswd(),
									$this->DBConnector->getDBName())))
		{
			echo "Probleme mit der Datenbank";
			exit();
		}
		else
		{
			$SQLQuery ="SET NAMES 'UTF8'";					//MG 11.09.2007
			try
			{
				$this->conn->debug=false;
				$this->conn->Execute($SQLQuery);

			}
			catch (exception $e)
			{
    			//print_r($e);
			}
		}
	}

	public function getConn()
	{
		//echo $this->conn;
		return $this;
	}

	public function SetFetchMode($mode)
	{
		$this->mode = $mode;
		return $this->conn->SetFetchMode($mode);
	}

	public function AutoExecute($table, $fields_values, $mode = 'INSERT', $where = FALSE, $forceUpdate=true, $magicq=false)
	{
	    $returnvalue="";
		try
		{
			$sql = 'SELECT * FROM '.$table;
			$rs = $this->conn->SelectLimit($sql,1);
			switch((string) $mode)
			{
				case 'UPDATE':
				case '2':
					$sql = $this->conn->GetUpdateSQL($rs, $fields_values, $forceUpdate, $magicq);
					break;
				case 'INSERT':
				case '1':
					$sql = $this->conn->GetInsertSQL($rs, $fields_values, $magicq);
					break;
			}

			Logger::getInstance()->Log($sql, LOG_SQL);
			$returnvalue = $this->conn->AutoExecute($table,$fields_values,$mode,$where, $forceUpdate, $magicq);
		}
		catch (exception $e)
		{
			Logger::getInstance()->Log($e->getMessage(), LOG_ERROR);
			Logger::getInstance()->Log("ERROR-Query: ".$sql, LOG_ERROR);
		}
		return $returnvalue;
	}

	public function ErrorMsg()
	{
		return $this->conn->ErrorMsg();
	}

    public function GetFirst($sql, $inputarr=false)
    {
        $res = $this->GetAll($sql, $inputarr);
        if(isset($res[0]))
            return $res[0];
        else
            return false;
    }

	public function GetAll($sql, $inputarr=false)
	{
		$returnvalue="";
		Logger::getInstance()->Log($sql, LOG_SQL);
		try
		{
			//echo $this->mode;
			if ($this->mode==ADODB_FETCH_NUM)
			{
				$this->conn->SetFetchMode(ADODB_FETCH_NUM);
				$returnvalue = $this->conn->GetAll($sql,$inputarr);
				$this->SetFetchMode(ADODB_FETCH_ASSOC);
			}
			else
			{
				$this->SetFetchMode(ADODB_FETCH_ASSOC);
				$returnvalue = $this->conn->GetAll($sql,$inputarr);
			}
			//$this->conn->SetFetchMode(ADODB_FETCH_BOTH);

		}
		catch (exception $e)
		{
			Logger::getInstance()->Log($e->getMessage(), LOG_ERROR);
			Logger::getInstance()->Log("ERROR-Query: ".$sql, LOG_ERROR);
		}
		return $returnvalue;
	}

	public function Insert_ID()
	{
		return $this->conn->Insert_ID();
	}

	public function Execute($sql,$inputarr=false)
	{
		$returnvalue="";
		Logger::getInstance()->Log($sql, LOG_SQL);
		try
		{
			if ($this->mode==ADODB_FETCH_NUM)
			{
				$this->conn->SetFetchMode(ADODB_FETCH_NUM);
				$returnvalue = $this->conn->Execute($sql,$inputarr);
				$this->SetFetchMode(ADODB_FETCH_ASSOC);
			}
			else
			{
				$this->SetFetchMode(ADODB_FETCH_ASSOC);
				$returnvalue = $this->conn->Execute($sql,$inputarr);
			}
		}
		catch (exception $e)
		{
			Logger::getInstance()->Log($e->getMessage(), LOG_ERROR);
			Logger::getInstance()->Log("ERROR-Query: ".$sql, LOG_ERROR);
		}
		return $returnvalue;
	}

	public function getDBConnector()
	{
		return $this->DBConnector;
	}

	public function getAutoIncrement()
	{
		$SQLQuery	= "SHOW TABLE STATUS LIKE '%".AGDO::getInstance()->getDBConnector()->getArticleTable()."'";
		$returnvalue = AGDO::getInstance()->GetAll($SQLQuery);
 		return $returnvalue[0]['Auto_increment'];
	}


    public function getAutoIncrementFromTable($table)
    {
        $query = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".$this->DBConnector->getDBName()."' AND TABLE_NAME   = '".$table."'";
        $res = AGDO::getInstance()->GetFirst($query);
        return $res['AUTO_INCREMENT'];
    }

	public function updateRecursive($parentID, $updateField, $updateValue, $languageID)
	{
		$query	= "SELECT ".$this->DBConnector->getDescriptionTable().".article_id " .
				"FROM ".$this->DBConnector->getDescriptionTable()." JOIN ".$this->DBConnector->getParentIDTable()." " .
				"ON ".$this->DBConnector->getDescriptionTable().".article_id = ".$this->DBConnector->getParentIDTable().".article_id " .
				"WHERE parent_id='".$parentID."' AND language_id='".$languageID."'";
		$returnvalue=AGDO::getInstance()->GetAll($query);
		if (empty($returnvalue))
		{
			return "";
		}
		else
		{
			foreach ($returnvalue as $line)
			{
				$query="UPDATE ".$this->DBConnector->getDescriptionTable()." SET `".$updateField."`='".$updateValue."' WHERE article_id='".$line['article_id']."' AND language_id='".$languageID."'";
				$returnvalue=AGDO::getInstance()->Execute($query);
				//echo $query."<br>";
				$this->updateRecursive($line['article_id'],$updateField, $updateValue, $languageID );
			}
		}
	}
}