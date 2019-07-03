<?php
define('PATH', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR );
define('LOCAL', $_SERVER['SERVER_ADDR'] == '127.0.0.1'?true:false);
require_once PATH.'classes/controller.php';
require_once PATH.'lib/HTML/htmlGenerator.php';
require_once PATH.'lib/Timestamp/TimestampConverter.php';
require_once PATH.'lib/Login/Login.php';
require_once PATH.'lib/Database/AGDO.php';
require_once PATH.'lib/varTester/varTester.php';
require_once PATH.'lib/Request/Request.php';
require_once PATH.'lib/template/TemplateParserHlag.php';
require_once PATH.'lib/LaenderSelect/LaenderSelect.php';
require_once PATH.'lib/Bootstrap/FormRenderer.php';
require_once PATH.'extern/tcpdf/tcpdf.php';
// gitHubTest
direktories();
function direktories($pfad = '')
{

    if ($handle = opendir(PATH . '/classes' . $pfad . '/'))
    {
        while (false !== ($entry = readdir($handle)))
        {
            if (is_file(PATH . '/classes' . $pfad . '/' . $entry))
            {
                if (strpos($entry, '.php') > 1)
                {
                    require_once PATH . '/classes' . $pfad . '/' . $entry;
                }
            }
            elseif (is_dir(PATH . '/classes' . $pfad . '/' . $entry) AND $entry != '.' AND $entry != '..')
            {
                direktories($pfad . '/' . $entry);
            }


        }
        closedir($handle);
    }
}



$controller = new controller();
echo $controller->getSite();

function z($r)
{
    echo '<pre><br><br>';
    print_r($r);
    echo '</pre>';

}
