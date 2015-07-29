<?php


/*
 * Created on 15.07.2008
 *
 * File: TemplateParserHlag.php
 *
 * Author: hlag
 */
class TemplateParser
{


    private static $instance;

    /**
     *
     * @returns TemplateParser
     * @return TemplateParser
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new TemplateParser();
        }
        return self::$instance;
    }

    private function __construct()
    {

    }

    public function parseTemplate($data, $template = 'mainTemplate.html', $path=false, $showTemplate = true)
    {
        $fileContent = $this->getTemplate($template, $path, $showTemplate);
        if ($fileContent)
        {
            foreach ($data AS $key => $value)
            {
                if (!is_array($value))
                    $fileContent = str_replace('{' . $key . '}', $value, $fileContent);
            }
            return $fileContent;
        }
        else
            return '404: '.$path."templates/" . $template.' not found';
    }

    private function getTemplate($template, $path ,$showTemplate)
    {
        if (!isset($this->templates[$template]))
        {
            if (file_exists($path."templates/" . $template))
            {
                $this->templates[$template] = file_get_contents($path."templates/" . $template);
                if($template != 'mainTemplate.html' AND $showTemplate)
                {
                    $this->templates[$template] = "\n"."\n".'<!--  '. $path.' '. $template.'  -->'."\n".$this->templates[$template]."\n".'<!--  '. $template.' EOF  -->'."\n"."\n";
                }
                return $this->templates[$template];
            }
            else
                return false;
        }
        else
            return $this->templates[$template];
    }
}
