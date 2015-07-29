<?php
/*
 * Created on 07.11.2008
 *
 * File: article.php
 * 
 * Author: proggen
 */
class Article
{
	private $dataArray;
    private $languageArray=array();
	
	public function __construct()
	{
		 $this->languageArray = Language::getInstance()->getLanguageArray();
	} 
	
	public function __get($name)
	{
		if (isset($this->dataArray[$name]))
			return $this->dataArray[$name];
		else
			return NULL;
	}
	
	public function __set($name, $value)
	{
		$this->dataArray[$name]=$value;
	}
	
	public function setDataArray($dataArray)
	{
		
	}
    
    public function insertArticleinDatabase($myArray)
    {
        // [-] 22.07.2008 JA
        require_once(PATH."lib/article/GenerateInsertUpdateArray.php");
        
        $time=getdate();
        $today=$time['year']."-".$time['mon']."-".$time['mday'];
        if (!isset($myArray['article_type']))
            $myArray['article_type'] = 1;

        $articelArray=array();
        $articelArray['date_added']=$today;
        $articelArray['last_modified']=$today;
        if (isset($myArray['date_expired']))
            $articelArray['date_expired']=$myArray['date_expired'];
        if (isset($myArray['date_available']))
            $articelArray['date_available']=$myArray['date_available'];
        $articelArray['published']=1 ;
        $articelArray['viewable']=1;
        $articelArray['article_type']=$myArray['article_type'] ;
        AGDO::getInstance()->AutoExecute(AGDO::getInstance()->getDBConnector()->getArticleTable(),$articelArray,'INSERT');
        $myArray['article_id']=AGDO::getInstance()->Insert_ID( );
        for ($counter=1; $counter <= Language::getInstance()->getNumberOfLanguages(); $counter++)
        {
            $generater = new GenerateInsertUpdateArray(); 
            //$insertArray = $this->generateInsertUpdateArray($myArray,$this->languageArray[$counter]['short'], $counter, 'INSERT');
            $insertArray = $generater->generate($myArray,$this->languageArray[$counter]['short'], $counter, 'INSERT');
            AGDO::getInstance()->AutoExecute(AGDO::getInstance()->getDBConnector()->getDescriptionTable(),$insertArray,'INSERT');
        }
        $parentTableInsertArray['article_id']= $myArray['article_id'];
        $parentTableInsertArray['parent_id']= 7;
        AGDO::getInstance()->AutoExecute(AGDO::getInstance()->getDBConnector()->getParentIDTable(),$parentTableInsertArray, 'INSERT');
        if (isset($myArray['add_alias_1_check']))
        {
            $this->addAlias($myArray['article_id'], $myArray['add_alias_1']);
            unset($myArray['add_alias_1_check']);
        }
        if (isset($myArray['add_alias_2_check']))
        {
            $this->addAlias($myArray['article_id'], $myArray['add_alias_2']);
            unset($myArray['add_alias_2_check']);
        }
        if (isset($myArray['add_alias_3_check']))
        {
            $this->addAlias($myArray['article_id'], $myArray['add_alias_3']);
            unset($myArray['add_alias_3_check']);
        }
        return $myArray['article_id'];
    }
    
    public function updateArticleinDatabase($myArray)
    {    
        require_once(PATH."lib/article/GenerateInsertUpdateArray.php");
        Logger::getInstance()->Log('updateArticleinDatabase', LOG_ABLAUF);
        // [-] 22.07.2008 JA
        $time=getdate();
        $myArray['last_modified']=$time['year']."-".$time['mon']."-".$time['mday'];
        if (isset($myArray['facebook']))
            $myArray['facebook']=1;
        else
            $myArray['facebook']=0;
        if (isset($myArray['twitter']))
            $myArray['twitter']=1;
        else
            $myArray['twitter']=0;
        if (isset($myArray['viewable']))
            $myArray['viewable']=1;
        else
            $myArray['viewable']=0;
        if (isset($myArray['published']))
            $myArray['published']=1;
        else
            $myArray['published']=0;
        Logger::getInstance()->Log("updateArticleinDatabase", LOG_ABLAUF);
        Logger::getInstance()->Log($myArray, LOG_ABLAUF);
        AGDO::getInstance()->AutoExecute(AGDO::getInstance()->getDBConnector()->getArticleTable(), $myArray, 'UPDATE', 'article_id = '.$myArray['ID']);


        /*$SQLQuery="UPDATE `".AGDO::getInstance()->getDBConnector()->getArticleTable()."` SET "    .(isset($myArray['publish'])?"`published` = '1'":"`published` = '0'")." , "
                                            .(isset($myArray['viewable'])?"`viewable` = '1'":"`viewable` = '0'")
                                            .(", `last_modified` = '".$today."'")
                                            ." WHERE `article_id` = '".$myArray['ID']."'";
         $temp=AGDO::getInstance()->Execute($SQLQuery);*/
         for ($counter=1; $counter <= Language::getInstance()->getNumberOfLanguages(); $counter++)
         {

                $myArray['article_id']=$myArray['ID'];
                $generater = new GenerateInsertUpdateArray(); 
                //$insertArray = $this->generateInsertUpdateArray($myArray,$this->languageArray[$counter]['short'], $counter, 'UPDATE');
                $insertArray = $generater->generate($myArray,$this->languageArray[$counter]['short'], $counter, 'UPDATE');
                Logger::getInstance()->Log('insertArchive', LOG_ABLAUF);
                $article = AGDO::getInstance()->GetAll("SELECT * FROM ".AGDO::getInstance()->getDBConnector()->getDescriptionTable()." WHERE article_id = ".$myArray['ID']." and language_id = ".$counter);
                AGDO::getInstance()->AutoExecute(AGDO::getInstance()->getDBConnector()->getArchiveTable(), $article['0'], 'INSERT');
                AGDO::getInstance()->AutoExecute(AGDO::getInstance()->getDBConnector()->getDescriptionTable(),$insertArray,'UPDATE', '`article_id` = '.$myArray['ID'].' AND language_id = '.$counter);
                if (isset($myArray['kopfbild_rekursiv_'.$this->languageArray[$counter]['short']]))
                    $this->updateRecursive($myArray['ID'], "kopfbild", $myArray['kopfbild_'.$this->languageArray[$counter]['short']], $counter);
                if (isset($myArray['article_teaser_link_1_rekursiv_'.$this->languageArray[$counter]['short']])&& isset($myArray['article_teaser_link_1_'.$this->languageArray[$counter]['short']]))
                    $this->updateRecursive($myArray['ID'], "article_teaser_link_1", $myArray['article_teaser_link_1_'.$this->languageArray[$counter]['short']], $counter);
                if (isset($myArray['article_teaser_link_2_rekursiv_'.$this->languageArray[$counter]['short']])&& isset($myArray['article_teaser_link_1_'.$this->languageArray[$counter]['short']]))
                    $this->updateRecursive($myArray['ID'], "article_teaser_link_2", $myArray['article_teaser_link_2_'.$this->languageArray[$counter]['short']], $counter);
                if (isset($myArray['article_teaser_link_3_rekursiv_'.$this->languageArray[$counter]['short']])&& isset($myArray['article_teaser_link_1_'.$this->languageArray[$counter]['short']]))
                    $this->updateRecursive($myArray['ID'], "article_teaser_link_3", $myArray['article_teaser_link_3_'.$this->languageArray[$counter]['short']], $counter);
                if (isset($myArray['article_teaser_link_4_rekursiv_'.$this->languageArray[$counter]['short']])&& isset($myArray['article_teaser_link_1_'.$this->languageArray[$counter]['short']]))
                    $this->updateRecursive($myArray['ID'], "article_teaser_link_4", $myArray['article_teaser_link_4_'.$this->languageArray[$counter]['short']], $counter);
                if (isset($myArray['template_rekursiv']))
                    $this->updateRecursive($myArray['ID'], "template", $myArray['template'], $counter);
                if (isset($myArray['farbe_rekursiv_de']))
                    $this->updateRecursive($myArray['ID'], "farbe", $myArray['farbe_de'], $counter);

                if (isset($myArray['Additional_Content_rekursiv_'.$this->languageArray[$counter]['short']]))
                {
                    if (isset($myArray['Additional_Content_'.$this->languageArray[$counter]['short']]))
                        $this->updateRecursive($myArray['ID'], "Additional_Content", $myArray['Additional_Content_'.$this->languageArray[$counter]['short']], $counter);
                    else
                        $this->updateRecursive($myArray['ID'], "Additional_Content", 0, $counter);
                }
                if (isset($myArray['Modul_rekursiv_'.$this->languageArray[$counter]['short']]))
                {
                    $this->updateRecursive($myArray['ID'], "Module", $myArray['Modul_'.$this->languageArray[$counter]['short']], $counter);
                }

                if (isset($myArray['social_media_recursive']))
                {
                    if (isset($myArray['facebook']) && $myArray['facebook']==1)
                    {
                        $this->updateRecursiveArticleTable($myArray['ID'], 'facebook', 1 , $counter);
                        $this->updateRecursive($myArray['ID'], 'facebook_type', $myArray['facebook_type_'.$this->languageArray[$counter]['short']], $counter);
                    }
                    else
                    {
                        $this->updateRecursiveArticleTable($myArray['ID'], 'facebook', 0 , $counter);
                        $this->updateRecursive($myArray['ID'], 'facebook_type', $myArray['facebook_type_'.$this->languageArray[$counter]['short']], $counter);
                    }
                    if (isset($myArray['twitter']) && $myArray['twitter']==1)
                        $this->updateRecursiveArticleTable($myArray['ID'], 'twitter', 1 , $counter);
                    else
                        $this->updateRecursiveArticleTable($myArray['ID'], 'twitter', 0 , $counter);
                }
                if (isset($myArray['login_rekursiv']))
                {
                    if (isset($myArray['GroupID']))
                        $this->updateRecursive($myArray['ID'], "GroupID", $myArray['GroupID'], $counter);
                    else
                        $this->updateRecursive($myArray['ID'], "GroupID", 0, $counter);
                    if (isset($myArray['UserID']))
                        $this->updateRecursive($myArray['ID'], "UserID", $myArray['UserID'], $counter);
                    else
                        $this->updateRecursive($myArray['ID'], "UserID", 0, $counter);
                }

         }

         if (isset($myArray['add_alias_1_check']))
        {
            $this->addAlias($myArray['ID'], $myArray['add_alias_1']);
            $this->addAlias($myArray['ID'], $myArray['add_alias_1']);
            unset($myArray['add_alias_1_check']);
        }
        if (isset($myArray['add_alias_2_check']))
        {
            $this->addAlias($myArray['ID'], $myArray['add_alias_2']);
            unset($myArray['add_alias_2_check']);
        }
        if (isset($myArray['add_alias_3_check']))
        {
            $this->addAlias($myArray['ID'], $myArray['add_alias_3']);
            unset($myArray['add_alias_3_check']);
        }
    }
    
    public function updateRecursiveArticleTable($parentID, $updateField, $updateValue, $languageArray)
    {
        $query    = "SELECT ".AGDO::getInstance()->getDBConnector()->getDescriptionTable().".article_id " .
                "FROM ".AGDO::getInstance()->getDBConnector()->getDescriptionTable()." JOIN ".AGDO::getInstance()->getDBConnector()->getParentIDTable()." " .
                "ON ".AGDO::getInstance()->getDBConnector()->getDescriptionTable().".article_id = ".AGDO::getInstance()->getDBConnector()->getParentIDTable().".article_id " .
                "WHERE parent_id='".$parentID."' AND language_id='".$languageArray."'";
        $returnvalue=AGDO::getInstance()->GetAll($query);
        if (empty($returnvalue))
        {
            return "";
        }
        else
        {
            foreach ($returnvalue as $line)
            {
                $query="UPDATE ".AGDO::getInstance()->getDBConnector()->getArticleTable()." SET `".$updateField."`='".$updateValue."' WHERE article_id='".$line['article_id']."'";
                $returnvalue=AGDO::getInstance()->Execute($query);
                $this->updateRecursiveArticleTable($line['article_id'],$updateField, $updateValue, $languageArray );
            }
        }
    }

    public function updateRecursive($parentID, $updateField, $updateValue, $languageArray)
    {
        $query    = "SELECT ".AGDO::getInstance()->getDBConnector()->getDescriptionTable().".article_id " .
                "FROM ".AGDO::getInstance()->getDBConnector()->getDescriptionTable()." JOIN ".AGDO::getInstance()->getDBConnector()->getParentIDTable()." " .
                "ON ".AGDO::getInstance()->getDBConnector()->getDescriptionTable().".article_id = ".AGDO::getInstance()->getDBConnector()->getParentIDTable().".article_id " .
                "WHERE parent_id='".$parentID."' AND language_id='".$languageArray."'";
        $returnvalue=AGDO::getInstance()->GetAll($query);
        if (empty($returnvalue))
        {
            return "";
        }
        else
        {
            foreach ($returnvalue as $line)
            {
                $query="UPDATE ".AGDO::getInstance()->getDBConnector()->getDescriptionTable()." SET `".$updateField."`='".$updateValue."' WHERE article_id='".$line['article_id']."' AND language_id='".$languageArray."'";
                $returnvalue=AGDO::getInstance()->Execute($query);
                $this->updateRecursive($line['article_id'],$updateField, $updateValue, $languageArray );
            }
        }
    }
    
    private function addAlias($id, $newParentID)
    {
        $id = $this->insertAliasInDatabase($id);
        $SQLQuery = "SELECT * FROM ".$this->parentIDTable." WHERE parent_id = ".$newParentID;
        $this->dbverbindung->SetFetchMode(ADODB_FETCH_ASSOC);
        $result=$this->dbverbindung->GetAll($SQLQuery);
        $parent['sort_order']=count($result);
        $parent['parent_id']=$newParentID;
        $this->dbverbindung->AutoExecute($this->parentIDTable, $parent, 'UPDATE', 'article_id = '.$id.'');
    }
}
?>
