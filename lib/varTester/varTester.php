<?php

/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 10.02.17
 * Time: 07:01
 */
class varTester
{
    private static $instance;

    /**
     *
     * @returns varTester
     * @return varTester
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new varTester();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function test($var)
    {
        if (is_array($var))
        {
            echo 'Array<br>';
        }
        else
        {
            echo 'Inhalt der Variablen ist "' . $var . '"<br>';
            if ($var === false)
                echo 'Bool false<br>';
            if ($var === true)
                echo 'Bool true<br>';
            if ($var === null)
                echo '<strong>null</strong><br>';
            if ($var === '')
                echo 'leer<br>';
            if ($var !== null && $var !== false && $var !== true && $var !== trim($var))
                echo 'Whitespaces<br>';
        }
    }
}