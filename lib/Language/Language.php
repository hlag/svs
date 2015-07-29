<?php
/*
 * Created on 30.05.2006
 *
 * Author: Mats
 *
 * File: Language.php
 *
 */
class Language
{
	private $languageArray;
	private $numberOfLanguages;
	private  $defaultlanguage;
	private static $instance;
    
    /**
    * Singleton Pattern 
    * 
    * @returns Language
     * @return Language
    */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
            self::$instance = new Language();
        }
        return self::$instance;
	}
    
	private function __construct()
	{
		/*$this->numberOfLanguages = Configuration::getInstance()->get('Language','numberOfLanguages');
        $langData = Configuration::getInstance()->getArray('language'); 
             $settings = unserialize(SETTINGS);   */
        $langData =  Configuration::getInstance()->getArray('language');
        
        // isolate default language
        $default = empty($langData['default']) ? '1' : $langData['default'];
        if(isset($langData['default']))
            unset($langData['default']);
        
        // build langArray
        $langArray = array();
        $items = array('short','Long','Name');
        foreach($langData as $index => $set)
            $langArray[$index] = array_combine($items,explode(',',$set));
        
        // set class vars
        $this->languageArray = $langArray;
        $this->numberOfLanguages = count($langArray);
        $this->defaultlanguage = $default;
	}
    
	public function getLanguageArray()
	{
		return $this->languageArray;
	}
    
	public function getNumberOfLanguages()
	{
		return $this->numberOfLanguages;
	}
	
    public function getDefaultLanguage()
	{
		return $this->defaultlanguage;
	}
	
    public function translateLanguage($lang)
	{
		for ($counter=1; $counter <= count($this->languageArray); $counter++)
		{
			if ($this->languageArray[$counter]['Name']==$lang)
				return $counter;
		}
		return $this->defaultlanguage;
	}
    
	public function renderLanguageTabs($pathToTemplate)
    {
	$returnvalue="";
        if ($this->getNumberOfLanguages()>1)
        {
            $returnvalue .= '<div id="RightPanel" class="container right" dojoType="dijit.layout.TabContainer" region="center">';
            for ($counter=1; $counter<=$this->getNumberOfLanguages(); $counter++)
            {
                $returnvalue .= '<div id="LangLayer'.$counter.'" dojoType="dijit.layout.TabContainer" title="'.$this->languageArray[$counter]['Name'].'" class="articleTabContent" >';
                $returnvalue .= $this->renderLangTabContent($counter,$pathToTemplate);
                $returnvalue .= '</div>';
            }
            $returnvalue .= '</div>';
        }
        else
            $returnvalue .= '<div id="RightPanel" class="container right articleTabContent" dojoType="dijit.layout.TabContainer" region="center">'.$this->renderLangTabContent(1,$pathToTemplate).'</div>';
        return $returnvalue;
    }
    
    public function renderLangTabContent($counter,$pathToTemplate)
    {
        $tags=array('Langshort'=> $this->languageArray[$counter]['short'],
                    'LangID'=> $counter
                    );
        $tp=new templateParser(PATH.$pathToTemplate);
        $tp->parseTemplate($tags);
        return $tp->display();
    }
}
?>
