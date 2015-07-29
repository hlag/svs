<?php
class htmlGenerator
{
    protected static $instance = null;

    /**
     * @returns htmlGenerator
     * @return htmlGenerator
     */
    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new htmlGenerator();
        }
        return self::$instance;
    }

    /*
     *  needs the data as array: id=>name
     */

    public function renderOptions($data, $selected = false)
    {
        $returnValue = '';
        foreach ($data AS $key => $name)
        {
            $returnValue .= '<option value = "' . $key . '" ';
            if ($key == $selected)
                $returnValue .= ' selected="selected" ';
            $returnValue .= ' >' . $name . '</option>';

        }
        return $returnValue;
    }

}
