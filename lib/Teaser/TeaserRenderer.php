<?php
class TeaserRenderer
{
    private static $instance;
    
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new TeaserRenderer();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }
    
    public function renderTeaser($teaserArray,$maxcount=0, $type)
    {
        $returnvalue=NULL;
        if (!empty($teaserArray))
        {
            $lang = Language::getInstance();

            $returnvalue.='<div class="'.Configuration::getInstance()->get($type, 'class') .'_outer">';
            $returnvalue.='<div class="'.Configuration::getInstance()->get($type, 'class') .'_inner">'; 
            if ($maxcount!=0)
                $count = $maxcount;
            else
                $count = count($teaserArray);
            if ($maxcount > count($teaserArray) )
                $count = count($teaserArray);
            for ($counter=0; $counter<$count; $counter++)
            {
                 $langArray=$lang->getLanguageArray();
                if ($teaserArray[$counter]['language_id']==$lang->getDefaultLanguage())
                    $langString="";
                else
                    $langString=$langArray[$teaserArray[$counter]['language_id']]['Name']."/";
                $tp = new templateParser(TEMPLATEPATH.Configuration::getInstance()->get("Design","theme").'/'.Configuration::getInstance()->get($type, 'template'));
                $templateArray=array();
                $templateArray['article_teaserhead']  = $teaserArray[$counter]['article_teaserhead'];
                $templateArray['article_teaser_content']  = $teaserArray[$counter]['article_teaser_content'];
                $templateArray['article_url']=($teaserArray[$counter]['article_url']=="/"?"/":$teaserArray[$counter]['article_url'].'.html"');
                $templateArray['newsdate']="";
                if ($teaserArray[$counter]['newsdate']!='' || $teaserArray[$counter]['newsdate']!='0000-00-00')
                {
                    $date=explode("-",$teaserArray[$counter]['newsdate']);
                    $templateArray['newsdate'] = $date[2].".".$date[1].".".$date[0];
                }
                $modulo= Configuration::getInstance()->get($type, 'modulo');
                if ($modulo==1)
                    $templateArray['counter']=$counter+1;
                else
                    $templateArray['counter']=$counter%Configuration::getInstance()->get($type, 'modulo');
                $tp->parseTemplate($templateArray);
                $returnvalue.= $tp->display();
            }
            $returnvalue.='</div>';
            $returnvalue.='</div>';
        }
        return $returnvalue;
    }
}
?>
