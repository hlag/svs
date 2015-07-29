<?php

class BootstrapForms
{
    protected static $instance = null;

    /**
     *
     * @returns BootstrapForms
     * @return BootstrapForms
     */
    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new BootstrapForms();
        }
        return self::$instance;
    }

    public function generateField($field)
    {
        $field = $this->makeVarsFromArray($field);
        switch ($field['type'])
        {
            case 'varchar':
            case 'int':
            case 'float':
                return TemplateParser::getInstance()->parseTemplate($field, 'textInput.html', PATH . 'lib/Bootstrap/');
                break;
            case 'submit':
                return TemplateParser::getInstance()->parseTemplate($field, 'submit.html', PATH . 'lib/Bootstrap/');
                break;
            default:
                return false;
        }
    }

    public function inputText($name, $label=false, $value=false, $id=false, $class=false, $placeholder = false)
    {
        $field = $this->makeVars($name, $label, $value, $id, $class, $placeholder, 'varchar');
        return $this->generateField($field);
    }

    private function makeVarsFromArray($field)
    {
        $field['type'] = isset($field['type'])?$field['type']:$field['varchar'];
        $field['value'] = isset($field['value'])?$field['value']:'';
        $field['class'] = isset($field['class'])?$field['class']:'';
        $field['id'] = isset($field['id'])?$field['id']:'id_'.$field['name'];
        $field['label'] = isset($field['label'])?$field['label']:ucfirst($field['name']);
        $field['placeholder'] = isset($field['placeholder'])?$field['placeholder']:$field['label'];
        if(!isset($field['pflicht']))
            $field['pflicht'] = false;
        if($field['pflicht'])
            $field['label'] = $field['label'].' *';
        $field['pflichtClass'] = $field['pflicht']?'pflichtfeld':'';
        $field['required'] = $field['pflicht']?'required':'';


        return $field;
    }


    private function makeVars($name, $label, $value, $id, $class, $placeholder, $type)
    {
        $retval['name'] = $name;
        $retval['type'] = $type;
        $retval['value'] = $value;
        $retval['class'] = $class;
        $retval['id'] = $id?$id:'id_'.$name;
        $retval['label'] = $label?$label:ucfirst($name);
        $retval['placeholder'] = $placeholder?$placeholder:ucfirst($name);
        return $retval;
    }


}