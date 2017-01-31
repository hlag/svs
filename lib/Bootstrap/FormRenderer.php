<?php

class BootstrapForms
{
    protected static $instance = null;
    private $usePlacehoder = true;
    private $labelCols = 3;

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

    public function usePlacehoder($bool)
    {
        $this->usePlacehoder = $bool;
    }

    public function setLabelcols($countCols)
    {
        $this->labelCols = $countCols;
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
            case 'textarea':
                return TemplateParser::getInstance()->parseTemplate($field, 'textArea.html', PATH . 'lib/Bootstrap/');
                break;
            default:
                return false;
        }
    }

    public function select($name, $label = false, $value = false, $id = false, $class = false, $selected = false, $tabindex = false)
    {
        $field = $this->makeVars($name, $label, '', $id, $class, '', 'select', false, $tabindex);
        $field['select'] = htmlGenerator::getInstance()->renderOptions($value, $selected, $tabindex);
        if ($label)
            return TemplateParser::getInstance()->parseTemplate($field, 'selectSimple.html', PATH . 'lib/Bootstrap/');
        else
            return TemplateParser::getInstance()->parseTemplate($field, 'selectSimpleOL.html', PATH . 'lib/Bootstrap/');
    }

    public function laenderSelect($name, $label = false, $value = false, $id = false, $class = false, $selected = false, $firstGroupArray = array('DE'))
    {
        $field = $this->makeVars($name, $label, '', $id, $class, '', 'select');
        $field['select'] = LaenderSelect::getInstance()->getOptions($selected, $firstGroupArray);
        return TemplateParser::getInstance()->parseTemplate($field, 'selectSimple.html', PATH . 'lib/Bootstrap/');
    }

    public function radios($name, $label = false, $value = false, $id = false, $class = false, $selected = false, $tabindex = false)
    {
        $field = $this->makeVars($name, $label, '', $id, $class, '', 'radios', false, $tabindex);
        $field['radios'] = htmlGenerator::getInstance()->renderRadios($value, $name, $selected);
        return TemplateParser::getInstance()->parseTemplate($field, 'radios.html', PATH . 'lib/Bootstrap/');
    }

    public function checkbox($name, $value = false, $id = false, $class = false, $checked = false, $tabindex = false)
    {
        $field = $this->makeVars($name, false, $value, $id, $class, '', 'radios', false, $tabindex);
        $field['checked'] = $checked?'checked="checked"':'';
        return TemplateParser::getInstance()->parseTemplate($field, 'checkbox.html', PATH . 'lib/Bootstrap/');
    }

    public function inputText($name, $label = false, $value = false, $id = false, $class = false, $placeholder = false, $tabindex = false)
    {
        $field = $this->makeVars($name, $label, $value, $id, $class, $placeholder, 'varchar', false, $tabindex);
        if ($label)
            return TemplateParser::getInstance()->parseTemplate($field, 'textInput.html', PATH . 'lib/Bootstrap/');
        else
            return TemplateParser::getInstance()->parseTemplate($field, 'textInputOL.html', PATH . 'lib/Bootstrap/');
    }

    public function hidden($name, $value, $id = false)
    {
        $field = $this->makeVars($name, false, $value, $id, false, false, 'hidden', false, false);
        return TemplateParser::getInstance()->parseTemplate($field, 'hidden.html', PATH . 'lib/Bootstrap/');
    }

    public function textArea($name, $label = false, $value = false, $id = false, $class = false, $placeholder = false, $rows = 4, $tabindex = false)
    {
        $field = $this->makeVars($name, $label, $value, $id, $class, $placeholder, 'textarea', $rows, $tabindex);
        return $this->generateField($field);
    }

    public function submit($name, $value = false, $id = false, $class = false)
    {
        $field = $this->makeVars($name, false, $value, $id, $class, false, 'varchar');
        return TemplateParser::getInstance()->parseTemplate($field, 'submit.html', PATH . 'lib/Bootstrap/');
    }

    private function makeVarsFromArray($field)
    {
        $field['type'] = isset($field['type']) ? $field['type'] : $field['varchar'];
        $field['value'] = isset($field['value']) ? $field['value'] : '';
        $field['class'] = isset($field['class']) ? $field['class'] : '';
        $field['id'] = isset($field['id']) ? $field['id'] : 'id_' . $field['name'];
        $field['label'] = isset($field['label']) ? $field['label'] : ucfirst($field['name']);
        $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : $field['label'];
        return $field;
    }

    private function makeVars($name, $label, $value, $id, $class, $placeholder, $type, $rows = false, $tabindex = false)
    {
        $retval['name'] = $name;
        $retval['type'] = $type;
        $retval['value'] = $value;
        $retval['class'] = $class;
        $retval['id'] = $id ? $id : 'id_' . $name;
        $retval['label'] = $label ? $label : ucfirst($name);
        if ($this->usePlacehoder)
            $retval['placeholder'] = $placeholder ? $placeholder : ucfirst($name);
        else
            $retval['placeholder'] = '';
        $retval['rows'] = $rows;
        $retval['tabindex'] = $tabindex ? ' tabindex="' . $tabindex . '" ' : '';
        return $retval;
    }

}