<?php
/**
 * @version 2.11
 */
class Request
{
    private $RequestGetArray = array();
    private $RequestPostArray = array();
    private $RequestSessionArray = array();
    private $FilesArray = array();
    private $Cookies = array();
    private static $instance;

    /**
     * @returns Request
     * @return Request
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new Request();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->init();
    }

    private function init()
    {
        foreach (array_keys($_GET) as $temp)
            $this->RequestGetArray[$temp] = $_GET[$temp];

        foreach (array_keys($_POST) as $temp)
        {
            $this->RequestPostArray[$temp] = $_POST[$temp];
            if (array_key_exists('save_x', $_POST))
                $this->RequestPostArray['save'] = 'Speichern';
            if (array_key_exists('cmdexit_x', $_POST) || array_key_exists('cmdexit', $_POST))
                $this->RequestPostArray['cmdexit'] = "abbrechen";
            if (array_key_exists('cmddelmulti_x', $_POST) || array_key_exists('cmddelmulti', $_POST))
                $this->RequestPostArray['cmddel'] = "true";
            if (array_key_exists('cmddelsingle_x', $_POST) || array_key_exists('cmddelsingle', $_POST))
                $this->RequestPostArray['cmddelsingle'] = "true";
            if (array_key_exists('cmddel_x', $_POST) || array_key_exists('cmddel', $_POST))
                $this->RequestPostArray['cmd'] = 'deleteOK';
        }
        foreach (array_keys($_FILES) as $temp)
        {
            if (isset($_FILES[$temp]) && !(empty($_FILES[$temp])))
            {
                $this->FilesArray[$temp] = $_FILES[$temp];
            }
        }
        $this->updateSessionArray();
        $this->initializeCookies();
    }

    /**
     * Methoide ist N�tig, da in HTTP_COOKIE Werte ge�ndert werden!!!!!!
     * So werden teilweise Doppelpunkte zu Spaces.... Deshalb werden die Cookies aus der ServerVariablen erzeugt
     *
     */
    private function initializeCookies()
    {
        if (isset($_SERVER['HTTP_COOKIE']))
        {
            $cookieArray = explode('; ', $_SERVER['HTTP_COOKIE']);
            foreach ($cookieArray as $cookieline)
            {
                $key = substr($cookieline, 0, strpos($cookieline, '='));
                $value = substr($cookieline, strpos($cookieline, '=') + 1);
                $this->Cookies[$key] = $value;
            }
        }
    }

    /**
     *    Adds a Variable to the POST, SESSION or GET Array
     *
     * @param String $name Name of Variable
     * @param String $value The Value
     * @param String $whichArray POST,GET,SESSION
     *
     * @return void
     */
    function addVariable($name, $value, $whichArray)
    {
        switch ($whichArray)
        {
            case 'POST':
                $this->RequestPostArray[$name] = $value;
                break;
            case 'GET':
                $this->RequestGetArray[$name] = $value;
                break;
            case 'SESSION':
                $_SESSION[$name] = $value;
                $this->updateSessionArray();
                break;

        }
    }

    public function getCookieArray()
    {
        return $this->Cookies;
    }

    public function deleteArray($whichArray)
    {
        switch ($whichArray)
        {
            case 'POST':
                $this->RequestPostArray = array();
                break;
            case 'GET':
                $this->RequestGetArray = array();
                break;
            case 'SESSION':
                $this->updateSessionArray();
                break;

        }
    }

    function getVariable($name, $Array)
    {
        switch ($Array)
        {
            case 'POST':
                if (isset($this->RequestPostArray[$name]))
                    return $this->RequestPostArray[$name];
            case 'GET':
                if (isset($this->RequestGetArray[$name]))
                    return $this->RequestGetArray[$name];
            case 'SESSION':
                if (isset($this->RequestSessionArray[$name]))
                    return $this->RequestSessionArray[$name];

        }
    }

    function updateSessionArray()
    {
        if (!empty($_SESSION))
        {
            foreach (array_keys($_SESSION) as $temp)
            {
                if (isset($_SESSION[$temp]) && !(empty($_SESSION[$temp])))
                {
                    $this->RequestSessionArray[$temp] = $_SESSION[$temp];
                }
            }
        }
    }

    function getHiddenInputs()
    {
        $array = $this->getPostRequests();
        $returnvalue = NULL;
        if (!(empty($array)))
        {
            foreach (array_keys($array) as $name)
            {
                if ($name != 'ID' && $name != 'Picture_x' && $name != 'Picture_y')
                    $returnvalue .= '<input type="hidden" name="' . $name . '" value="' . $array[$name] . '" >' . "\n";
                //<input type="hidden" name="Name" value="Wert">
            }
            return $returnvalue;
        }
    }

    function getFileRequests($var = false)
    {
        if ($var)
            if (isset($this->FilesArray[$var]))
                return $this->FilesArray[$var];
            else
                return false;
        else

            return $this->FilesArray;
    }

    function getGetRequests($data = false)
    {

        if ($data)
            if (isset($this->RequestGetArray[$data]))
                return $this->RequestGetArray[$data];
            else
                return false;
        else
            return $this->RequestGetArray;
    }

    function getGetRequestsComplete()
    {
        return $_GET;
    }

    function getPostRequests($data = false)
    {

        if ($data)
            if (isset($this->RequestPostArray[$data]))
                return $this->RequestPostArray[$data];
            else
                return false;
        else
            return $this->RequestPostArray;
    }

    function getPostRequestsComplete()
    {
        return $_POST;
    }

    function getSessionRequests($data = false)
    {
        if ($data)
            return $this->RequestSessionArray[$data];
        else
            return $this->RequestSessionArray;
    }

    public function getPrefixRequests($prefix)
    {
        $returnvalue = array();
        foreach (array_keys($_POST) as $keys)
        {
            if (strpos($keys, $prefix) === 0)
                $returnvalue[$keys] = $_POST[$keys];
        }
        return $returnvalue;
    }

    function getRequests()
    {
        $this->updateSessionArray();
        $returnArray = $this->RequestSessionArray;
        if (!empty($this->RequestGetArray))
            $returnArray = array_merge($returnArray, $this->RequestGetArray);
        if (!empty($this->RequestPostArray))
            $returnArray = array_merge($returnArray, $this->RequestPostArray);
        return $returnArray;
    }

}

?>
