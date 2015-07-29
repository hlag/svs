<?php
define('PATH', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR);
require_once PATH . 'lib/Database/AGDO.php';
require_once PATH . 'lib/Request/Request.php';
require_once PATH . 'lib/template/TemplateParserHlag.php';
require_once PATH . 'classes/Songs/Song.php';
require_once PATH . 'classes/Musiker/Musiker.php';

new Uploader();


class uploader
{
    private $song;
    private $headers;
    private $FileInfo;

    public function __construct()
    {
        // $this->getSong(Request);
        $this->start();
    }

    public function start()
    {
        $this->getAndProcessHeaders();
        $this->getSong();
        switch($this->headers['fileType'])
        {
            case 'mp3':
                $this->song->duration = round($this->FileInfo['playtime_seconds']);
                $this->song->mp3 = $this->song->kennung . '.mp3';
                file_put_contents(PATH . 'files/' . $this->song->kennung . '.mp3', file_get_contents("php://input"));
                break;
            case 'doc':
                $this->song->txt = $this->song->kennung . '.doc';
                file_put_contents(PATH . 'files/' . $this->song->kennung . '.doc', file_get_contents("php://input"));
                break;

        }
        $this->song->saveSong();
        echo $this->song->id;



    }

    private function getAndProcessHeaders()
    {
        $this->headers = getallheaders();
        $ext = pathinfo($this->headers['X-File-Name'], PATHINFO_EXTENSION);
        switch($ext)
        {
            case 'doc':
                $this->headers['fileType'] = 'doc';
                break;
            case 'mp3':
                $this->headers['fileType'] = 'mp3';
                break;
            default:
                die('falsche Datei');
                break;
        }

    }

    private function getSong()
    {

        switch($this->headers['fileType'])
        {
            case 'mp3':
            {
                $content = file_get_contents("php://input");
                file_put_contents(PATH . 'files/temp/temp.mp3', $content);
            }
        }


        $this->song = new Song();
        if ($this->headers['song_id'] != 'new')
            $this->song->getSongByID($this->headers['song_id']);
        else
        {
            switch($this->headers['fileType'])
            {
                case 'mp3':
                    $this->processSong();
                    break;
            }
        }
    }

    private function processSong()
    {
        require_once PATH.'extern/getID3-1.9.9/getid3/getid3.php';
        require_once PATH.'extern/getID3-1.9.9/getid3/module.audio.mp3.php';
        $getID3 = new getID3();
        $this->FileInfo = $getID3->analyze(PATH . 'files/temp/temp.mp3');
        $this->song->title = $this->FileInfo['tags']['id3v1']['title'][0];
        $this->song->interpret = $this->FileInfo['tags']['id3v1']['artist'][0];
        $this->song->setKennung();
        $this->song->id = 'new';
       // return  round($this->FileInfo['playtime_seconds']);
    }

}


function z($r)
{
    echo '<pre>';
    print_r($r);
    echo '</pre>';

}