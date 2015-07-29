<?php
class upload
{
    public function __construct()
    {

    }

    public function getContent()
    {
        return TemplateParser::getInstance()->parseTemplate(array(), 'Upload/songUpload.html');
    }
}