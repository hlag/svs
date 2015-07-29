<?php
require_once(PATH."lib/article/checkURL.php");

class GenerateInsertUpdateArray
{
    private $urlchecker;
    public function __construct()
    {
        $this->urlchecker= new checkURL();
    }
    
    public function generate($array, $langshort, $langID, $mode)
    {
        //print_r($array);
	//echo $langID;
        Logger::getInstance()->Log($_POST, LOG_ABLAUF);
        //$array['article_url_de'] = iconv('ISO-8859-1',"UTF-8",$array['article_url_de']);
        Logger::getInstance()->Log($array, LOG_ABLAUF);
        $arrayKeys = array_keys($array);
        $returnArray=array();
        if (ModuleManager::getInstance()->getModuleByName('Rechteverwaltung'))
        {
            $rechteverwaltung = ModuleManager::getInstance()->getModuleByName('Rechteverwaltung');
            $rechteverwaltung->setConnection(AGDO::getInstance());
            $rechteverwaltung->setLanguage(1);
            $rechteverwaltung->calculate();
            $permissions = array();
            foreach ($arrayKeys as $keys)
            {

                if (!(strpos($keys,'role_')===false))
                {
                    //echo strpos($keys,'e_');
                    $roleID = substr($keys,5);
                    //echo $roleID;
                    $permission = $array['GroupSelect_'.$roleID];
                    $permissions[]=array('permission'=>$permission,'role_id'=>$roleID);
                    //
                }
            }
            if (isset( $array['ID']))
              $rechteverwaltung->setPermissions($permissions, $array['ID']);
            if (isset($array['rolerekursiv']))
            {
                $this->updateRecPermission($rechteverwaltung, $permissions, $array['ID']);
            }
        }
        if (isset($array['tag_new']))
            $returnArray['tags']=$array['tag_new'];
        else
	{
           if (isset($array['tags'])) 
		$returnArray['tags']=$array['tags'];
	   else 
		$returnArray['tags']="";
	}
        if (isset($array['kopfbild_de']))
                $returnArray['kopfbild']=$array['kopfbild_de'];
        if(empty($array['kopfbildgalerie_on_de']))
            $returnArray['kopfbildgalerie_on'] = 'no';
	if (isset($array['kopfbild_slideshow']))
	     $returnArray['kopfbild_slideshow']=$array['kopfbild_slideshow'];
        else
            $returnArray['kopfbild_slideshow']='';
        if (isset($array['facebook']))
            $returnArray['facebook']=1;
        else
            $returnArray['facebook']=0;
        if (isset($array['twitter']))
            $returnArray['twitter']=1;
        else
            $returnArray['twitter']=0;
        if (empty($array['twitter_text']))
            $returnArray['twitter_text']="";
        if (empty($array['facebook_title']))
            $returnArray['facebook_title']="";
        if (empty($array['facebook_description']))
            $returnArray['facebook_description']="";
        if (empty($array['facebook_image']))
            $returnArray['facebook_image']="";
        if (isset($array['tags']) && $array['tags']=='Keine')
            $returnArray['tags']=NULL;
        // JA 09.03.2010 Rating

        $returnArray['rating_enabled'] = !empty($array['rating_enabled']) + '';
        $returnArray['show_rating'] = !empty($array['show_rating']) + '';
        if(isset($array['avg_rating']))
            $returnArray['avg_rating'] = $array['avg_rating']; 
        if(!empty($array['reset_rating'])){
            // reset data
            $returnArray['avg_rating'] = $returnArray['rating_count'] = 0;
            
            // delete rating info
            $settings = unserialize(SETTINGS);
            $table = $this->dbconnector->getPrefix() . $settings['rating']['table'];
            
            $query = "DELETE FROM $table 
                    WHERE article_id = ".$array['article_id'];
             $result = $this->dbverbindung->Execute($query);
        }
        
        foreach ($arrayKeys as $keys)
        {
            // JA 23.06.2009: that method is unsafe and kills description.
            /* 
            if (strpos($keys,"_".$langshort)>0)
            {
                $newkey=substr($keys,0,strpos($keys,"_".$langshort));
                $returnArray[$newkey]=$array[$keys];
            }
            */
            $parts = explode('_',$keys);
            if( count($parts) > 1 and end($parts) === $langshort)
            {
                array_pop($parts);
                $returnArray[join($parts,'_')]=$array[$keys];
            }
        }
        if (isset($returnArray['Modul']))                    //05.07.2007
            $returnArray['Module']=$returnArray['Modul'];
        else                                                //10.10.2007
            $returnArray['Module']="";                        //10.10.2007
        if (isset($returnArray['Modul_parameter']))                    //05.07.2007
            $returnArray['module_parameter']=$returnArray['Modul_parameter'];
        else                                                //10.10.2007
            $returnArray['module_parameter']="";
        
         if (isset($returnArray['Modul_parameter_2']))                    //05.07.2007
            $returnArray['module_parameter_2']=$returnArray['Modul_parameter_2'];
        else                                                //10.10.2007
            $returnArray['module_parameter_2']="";
        $bereinigterArtikelName="";
        if (!isset($returnArray['article_as_link']))
            $returnArray['article_as_link']='0';
        if (isset($returnArray['article_name']))
        {
            //echo "update";
            /** No automated change of page title and menu title. JA 30.01.2009
            if ($mode=='UPDATE')
            {
                $isChangedArticleName= $this->checkArticleName(mb_convert_encoding($returnArray['article_name'],"HTML-ENTITIES","auto"),$array['article_id'],$langID);
                 if ($isChangedArticleName)
                 {
                    unset($returnArray['article_linktext']);
                    unset($returnArray['article_title']);
                    //unset($myArray['article_url_'.$this->languageID[$counter]['short']]);
                 }
            }
            **/
            $bereinigterArtikelName = str_replace("\'","",$returnArray['article_name']);
            $bereinigterArtikelName = str_replace("\\\"","",$bereinigterArtikelName);
            if (!isset($returnArray['article_linktext']))
            {
                $returnArray['article_linktext']=$bereinigterArtikelName;
            }
            if (!isset($returnArray['article_title']))
            {
                $returnArray['article_title']=$bereinigterArtikelName;
            }
            
            if (!isset($returnArray['article_url']))
            {

                $returnArray['article_url']=$this->convertArticleNameToArticleUrl($returnArray['article_name']);
                $returnArray['article_url']=$this->urlchecker->check($returnArray['article_url'], $array['article_id'],$langID);
            }
            else
            {
                $returnArray['article_url']=$this->urlchecker->check($returnArray['article_url'], $array['article_id'],$langID);
            }
        }
        else
        {
            
            $returnArray['article_name']="";
            if (!isset($returnArray['article_linktext']))
                $returnArray['article_linktext']="";
        }
        if (!isset($returnArray['Additional_Content']))                //MG 10.10.2007
            $returnArray['Additional_Content']=0;
        if (!isset($returnArray['article_teaser_link_1']))            //MG 02.10.2007
            $returnArray['article_teaser_link_1']=0;
        if (!isset($returnArray['article_teaser_link_2']))
            $returnArray['article_teaser_link_2']=0;
        if (!isset($returnArray['article_teaser_link_3']))
            $returnArray['article_teaser_link_3']=0;
        if (!isset($returnArray['article_teaser_link_4']))
            $returnArray['article_teaser_link_4']=0;
        if ($array['article_id']==4 && $langID==1)
            $returnArray['article_url']="/";
        //$returnArray['article_url']=$array['article_url_de'];
        if (isset($array['jslib']) && $array['jslib']!='keine')
            $returnArray['jslib']=$array['jslib'];
        else
            $returnArray['jslib']=NULL;
        $returnArray['article_name']=$this->checkString($returnArray['article_name']);
        //$returnArray['article_name']=str_replace("\'","&#39;",$returnArray['article_name']);
        $returnArray['article_id']=$array['article_id'];
        if (!isset($array['show_navi']))
            $returnArray['show_navi']=0;
        else
            $returnArray['show_navi']=1;
        if (!isset($array['template']))
            $returnArray['template']='MainTemplate.htm';
        else
            $returnArray['template']=$array['template'];
        $returnArray['language_id']=$langID;
        if (isset($returnArray['ta']) && $returnArray['ta']!="")                    //MG 04.10.2007
            $returnArray['article_content']=$this->checkString(mb_convert_encoding($returnArray['ta'],"HTML-ENTITIES","auto"));
        else
            $returnArray['article_content']="";    
        if (isset($returnArray['article_description']) && $returnArray['article_description']!="")                    //MG 04.10.2007
            $returnArray['article_description']=$this->checkString(mb_convert_encoding($returnArray['article_description'],"HTML-ENTITIES","auto"));
        else
            $returnArray['article_description']="";
        if (isset($array['article_type']))
            $returnArray['article_type']=$array['article_type'];
        else
            $returnArray['article_type']=1;
        $returnArray['article_name']=mb_convert_encoding($returnArray['article_name'],"HTML-ENTITIES","auto");
        if (isset($array['GroupID']))
            $returnArray['GroupID']=$array['GroupID'];
        else
            $returnArray['GroupID']=0;
        if (isset($array['UserID']))
            $returnArray['UserID']=$array['UserID'];
        else
            $returnArray['UserID']=0;
        if (isset($returnArray['article_linktext']))
            $returnArray['article_linktext']=mb_convert_encoding($returnArray['article_linktext'],"HTML-ENTITIES","auto");
        if (isset($returnArray['article_title']))
            $returnArray['article_title']=mb_convert_encoding($returnArray['article_title'],"HTML-ENTITIES","auto");
        if (!isset($returnArray['article_teaserhead']))
            $returnArray['article_teaserhead'] = $this->getTeaderHeadFromArticleName($returnArray['article_name']);
        else
            $returnArray['article_teaserhead']=$this->checkString($returnArray['article_teaserhead']);
        if (!isset($returnArray['article_teaser_content']))
            $returnArray['article_teaser_content']= $this->checkString($this->getTeaserContentFromArticleContent($returnArray['article_content']));
        $returnArray['article_teaser_content']= $this->checkString($returnArray['article_teaser_content']);                 //MG 26.09.2007
        if (isset($returnArray['picture_name']))
        {
            $returnArray['picture_name']= mb_convert_encoding($returnArray['picture_name'],"HTML-ENTITIES","auto");
            $returnArray['picture_name']=$this->checkString($returnArray['picture_name']);
        }
        else
            $returnArray['picture_name']="";
            
        
        $returnArray['newsdate']= empty($array['newsdate']) ? '0000-00-00' : $array['newsdate']; // 03.09.2008 JA
        Logger::getInstance()->Log("returnArray", LOG_ABLAUF);
        Logger::getInstance()->Log($returnArray, LOG_ABLAUF);
        //echo "langshort".$langshort;
        //echo "<!--\n".print_r($returnArray,true)."-->";
        return $returnArray;
    }
    
    private function checkString($string)
    {
        $returnvalue=NULL;
        $string=str_replace("\'","&#39;",$string);
        $string=str_replace("\\\"","&quot;",$string);
        for ($counter=0; $counter<strlen($string); $counter++)
        {
            $temp=substr($string,$counter,1);
            switch (ord($temp))
            {
                case 34:
                    $returnvalue.="&quot;";
                    break;
                case 39:
                    $returnvalue.="&#39;";
                    break;
                default:
                    $returnvalue.=$temp;
            }

        }
        return $returnvalue;
    }
    
    private function convertURLFromConvertTable($string)
    {
        
        $returnvalue=NULL;
        return $returnvalue;
        for ($counter=0; $counter<strlen($string); $counter++)
        {
            $temp=substr($string,$counter,1);
           // if ((ord($temp)>96 && ord($temp)<123) || ord($temp)==95 || ord($temp)==45 || (ord($temp)>47 && ord($temp)<58))
                $returnvalue.=$temp;
        }
        return $returnvalue;
    }
    
    private function getTeaderHeadFromArticleName($articleName)
    {
        return $articleName;
    }
    
    private function convertArticleNameToArticleUrl($NameToConvert)
    {
        $returnvalue=NULL;
        $returnvalue=strtolower($NameToConvert);
        $umlaute =  Array("/".iconv("ISO-8859-1","UTF-8",chr(228))."/","/".iconv("ISO-8859-1","UTF-8",chr(246))."/","/".iconv("ISO-8859-1","UTF-8",chr(252))."/"        //03.02.2007 MG
                        ,"/".iconv("ISO-8859-1","UTF-8",chr(196))."/","/".iconv("ISO-8859-1","UTF-8",chr(214))."/","/".iconv("ISO-8859-1","UTF-8",chr(220))."/"
                        ,"/".iconv("ISO-8859-1","UTF-8",chr(223))."/","/".iconv("ISO-8859-1","UTF-8",' ')."/");
        $replace = Array("ae","oe","ue","ae","oe","ue","ss","-");
        $value = preg_replace($umlaute, $replace, $returnvalue);
        //echo "space?".ord(32)."end";
        $value = str_replace(ord(20),"-",$value);
        //echo "replaced".$value;
        $temp=urlencode($value);
        if (strpos($temp,"%")>=0)
            $returnvalue= $this->convertURLFromConvertTable($value);
        return $NameToConvert;
    }
    
    private function getTeaserContentFromArticleContent($content)  //changed MG 20.08.2007
    {
        $content = $this->strip_selected_tags($content,array('img','p'));
        $content = html_entity_decode($content);
        if (strlen($content)<100)
            return $content;                            //MG 27.09.2007
        else
        {
            $pos = strpos($content," ",99);
            //echo $pos;
            $teaser =substr($content,0,$pos);            //MG 27.09.2007
            return '<p>'.$teaser.'</p>';
        }
    }

    private function strip_selected_tags($text, $tags = array())
    {
        $args = func_get_args();
        $text = array_shift($args);
        $tags = func_num_args() > 2 ? array_diff($args,array($text))  : (array)$tags;
        foreach ($tags as $tag){
            while(preg_match('/<'.$tag.'(|\W[^>]*)>(.*)<\/'. $tag .'>/iusU', $text, $found)){
                $text = str_replace($found[0],$found[2],$text);
            }
        }

        return preg_replace('/(<('.join('|',$tags).')(|\W.*)\/>)/iusU', '', $text);
    }
    
    private function updateRecPermission($rechteverwaltung, $permissions, $parentID)
    {
        $SQLQuery="SELECT * FROM ".$this->parentIDTable." WHERE parent_id ='".$parentID."'";
        $this->dbverbindung->SetFetchMode(ADODB_FETCH_ASSOC);
        $result = $this->dbverbindung->GetAll($SQLQuery);
        if (empty($result))
            return 0;
        else
        {
            foreach ( $result as $line)
            {
                $rechteverwaltung->setPermissions($permissions, $line['article_id']);
                $this->updateRecPermission($rechteverwaltung, $permissions,$line['article_id']);
            }
            return 0;
        }
    }
}
?>
