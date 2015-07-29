<?php
/*
 * Created on 29.10.2008
 *
 * File: TeaserRenderer.php
 * 
 * Author: proggen
 */
class TeaserRenderer
{
	private static $instance;
	private $settings;
	private $maxcount;
	private $teaserArray;

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
    	$this->settings	= parse_ini_file(PATH."godot/.htconfig.ini", true);
    	$this->teaserArray=array();
    	$this->maxcount = 0;
	}
	
	public function setTeaserArray($teaserArray)
	{
		$this->teaserArray=$teaserArray;
	}
	
	public function setMaxCount($maxCount)
	{
		$this->maxcount = $maxCount;
	}
	
	public function renderTeaser()
	{
		$returnvalue = "";
		if (!empty($this->teaserArray))
		{
			$lang = Language::getInstance();
			if ($this->maxcount!=0)
				$count = $this->maxcount;
			else
				$count = count($this->teaserArray);
			for ($counter=0; $counter<$count; $counter++)
			{
				if ($this->settings['Teaser']['Nummeriert']==0)
				{
					$this->teaserArray[$counter]['counter']="";
				}
				else
					$this->teaserArray[$counter]['counter']=$counter+1;
				if (!($this->teaserArray[$counter]['newsdate']=='0000-00-00' || $this->teaserArray[$counter]['newsdate']==""))
				{
					$date=explode("-",$this->teaserArray[$counter]['newsdate']);
					$this->teaserArray[$counter]['newsdate'] = '<span class="date">'.$date[2].".".$date[1].".".$date[0]."</span>";
				}
				else
					$this->teaserArray[$counter]['newsdate']="";
				$this->teaserArray[$counter]['article_url'].= ".html"; 
				$tp = new templateParser(PATH.'templates/FrontEnd/TeaserTemplate.htm');
				$tp->parseTemplate($this->teaserArray[$counter]);
				$returnvalue.=$tp->display();
			}
			
		}
		return $returnvalue;
	}
}
?>
