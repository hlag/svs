<?php
/*
 * Created on 11.05.2005
 * Projekt:project_name
 *
 * by Matthias G�nther
 *
 */
require_once (PATH.'lib/Conf/Configuration.php');
require_once (PATH.'lib/Logging/Logger.php');
class templateParser
{
	var $output;

	function templateParser($templateFile='default_template.htm', $file=true)
	{
            Logger::getInstance()->Log($templateFile, LOG_TEMPLATE);
            if ($file)
                (file_exists($templateFile))?$this->output=file_get_contents($templateFile):die('Error:Template file '.$templateFile.' not found');
            else
                $this->output=$templateFile;
	}

	function parseTemplate($tags=array())
	{
		if (!empty($tags))
		{
			foreach($tags as $tag=>$data)
				if (!is_array($data))
					$this->output=str_replace('{'.$tag.'}',$data,$this->output);
		}
	}

	function parseTemplateAndTidy($tags=array())
	{
		$this->parseTemplate($tags);
		//if(strpos($this->output, '{')) // delete leftover replacement-tags
			$this->output=preg_replace("/\{[^\s]+\}/U","",$this->output);

	}

	function parseFile($file)
	{
		ob_start();
		include($file);
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
	}

	function display()
	{
		return $this->output;
	}
}
?>
