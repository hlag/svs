<?php
define('PATH', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR);
define('LOCAL', $_SERVER['SERVER_ADDR'] == '127.0.0.1' ? true : false);
require_once PATH . 'classes/controller.php';
require_once PATH . 'lib/HTML/htmlGenerator.php';
require_once PATH . 'lib/Database/AGDO.php';
require_once PATH . 'lib/Request/Request.php';
require_once PATH . 'lib/template/TemplateParserHlag.php';
require_once PATH . 'lib/LaenderSelect/LaenderSelect.php';
require_once PATH . 'lib/Bootstrap/FormRenderer.php';
require_once PATH . 'classes/Songs/Song.php';
require_once PATH . 'classes/Songs/playlist.php';
require_once PATH . 'classes/Songs/playlistSongs.php';
require_once PATH . 'classes/Songs/playlist_bloecke.php';
require_once PATH . 'classes/Musiker/Musiker.php';
require_once PATH . 'ajax/Songsorter.php';
require_once PATH . 'lib/Timestamp/TimestampConverter.php';


new ajax();

class ajax
{

    public function __construct()
    {
        $this->getContent();
    }

    private function getContent()
    {
        if (Request::getInstance()->getGetRequests('cmd'))
        {
            switch (Request::getInstance()->getGetRequests('cmd'))
            {
                case 'getArrangement':
                    $this->getArrangement(Request::getInstance()->getGetRequests('id'));
                    break;
                case 'getMuckerMeinung':
                    $this->getMuckerMeinung(Request::getInstance()->getGetRequests('id'), Request::getInstance()->getGetRequests('m'));
                    break;
                case 'editName':
                    $this->editName(Request::getInstance()->getGetRequests('pb_id'));
                    break;
                case 'getSingleDatum':
                    echo $this->getSingleDatum(Request::getInstance()->getGetRequests('table'), Request::getInstance()->getGetRequests('field'), Request::getInstance()->getGetRequests('id'), Request::getInstance()->getGetRequests('id_name'));
                    break;
                case 'getPause':
                    echo $this->getPause(Request::getInstance()->getGetRequests('pb_id'));
                    break;
                case 'getPlaylistStart':
                    echo $this->getPlaylistStart(Request::getInstance()->getGetRequests('pl_id'));
                    break;
                case 'getPlayedStatus':
                    echo $this->getPlayedStatus(Request::getInstance()->getGetRequests('id'),Request::getInstance()->getGetRequests('pl_id'));
                    break;
                default:
                    z(Request::getInstance()->getGetRequests());//
                    break;
            }
        }
        if (Request::getInstance()->getPostRequests('cmd'))
        {
            $p = Request::getInstance()->getPostRequests();
            switch ($p['cmd'])
            {
                case 'saveArrangement':
                    AGDO::getInstance()->Execute("UPDATE SVsongs SET " . $p['feld'] . " = '" . trim($p['text']) . "' WHERE id=" . $p['id']);
                    break;
                case 'saveInstrument':
                    AGDO::getInstance()->Execute("UPDATE SVsongs SET instrument = '" . $p['instrument'] . "' WHERE id=" . $p['id']);
                    break;
                case 'saveStatus':
                    AGDO::getInstance()->Execute("UPDATE SVsongs SET probe = '" . $p['status'] . "' WHERE id=" . $p['id']);
                    break;
                case 'saveMuckermeinung':
                    $p['musiker'] = $p['musiker'] == 'b' ? 'c' : $p['musiker'];
                    AGDO::getInstance()->Execute("UPDATE SVsongs SET " . $p['musiker'] . " = '" . $p['status'] . "' WHERE id=" . $p['id']);
                    break;
                case 'sortSong':
                    $songSorter = new Songsorter();
                    echo $songSorter->sortSong($p);
                    break;

                case 'saveSingleDatum':
                    AGDO::getInstance()->Execute("UPDATE " . $p['table'] . " SET  " . $p['field'] . " = '" . $p['value'] . "' WHERE " . $p['id_name'] . " = '" . $p['id'] . "'");
                    echo $p['value'];
                    break;
                case 'newBlock':
                    $this->newBlock($p['pl_id']);
                    break;
                case 'deleteBlock':
                    $block = new playlist_bloecke();
                    $block->deleteBlock($p['pb_id']);
                    break;
                case 'savePause':
                    echo $this->savePause($p['pb_id'], $p['pause']);
                    break;
                case 'savePlaylistStartzeit':
                    echo $this->savePlaylistStartzeit($p['pl_id'], $p['uhrzeitStart']);
                    break;
                case 'savePlayedStatus':
                    echo $this->savePlayedStatus($p['ps_id'], $p['status']);
                    break;
                case 'updateProbeDatum':
                    AGDO::getInstance()->Execute("UPDATE SVsongs SET letzteProbe = '".date('Y-m-d')."', probe=4 WHERE id=".$p['id']);
                    break;



                default:
                    z(Request::getInstance()->getPostRequests());
                    break;
            }
        }
    }

    private function newBlock($pl_id)
    {
        $pl = new playlist();
        $pl->getPlaylistByID($pl_id);


        $block = new playlist_bloecke();
        $block->pl_id = $pl_id;
        $block->pb_sort_order = $pl->getNextSortorder();
        $block->pb_id = 'new';
        $block->saveBlock();
        echo $block->renderHTML($block->pb_sort_order) . '<div id="playlistBlockZiel"></div>';


    }

    private function getSingleDatum($table, $field, $id, $id_name)
    {
        $res = AGDO::getInstance()->GetFirst("SELECT " . $id_name . ", " . $field . " FROM " . $table . " WHERE " . $id_name . " = '" . $id . "'");
        $data['table'] = $table;
        $data['field'] = $field;
        $data['id'] = $id;
        $data['id_name'] = $id_name;
        $data['value'] = $res[$field];


        return TemplateParser::getInstance()->parseTemplate($data, 'ajax/ajaxSingleForm.html', PATH);;
    }

    //saveInstrument
    private function getArrangement($song_id)
    {
        $song = new Song();
        $song->getSongByID($song_id);
        echo $song->renderArrangement();
    }

    private function getMuckerMeinung($song_id, $musiker)
    {
        $song = new Song();
        $song->getSongByID($song_id);
        echo $song->renderMuckermeinung($musiker);
    }

    private function editName($pb_id)
    {
        //$block =AGDO::getInstance()->GetFirst("SELECT * FROM playlist_bloecke WHERE pb_id = ".$pb_id);
        $block = new playlist_bloecke();
        $block->getBlockByID($pb_id);
        echo $block->renderEditMaske();
    }

    private function getPause($pb_id)
    {
        $block = new playlist_bloecke();
        $block->getBlockByID($pb_id);
        echo $block->getPauseEdit();
    }

    private function savePause($pb_id, $pause)
    {
        $block = new playlist_bloecke();
        $block->getBlockByID($pb_id);
        $block->setPause($pause);
        $block->saveBlock();

        $pl = new playlist();
        $pl->getPlaylistByID($block->pl_id);
        $retval = $pl->getDataForJson();
        echo json_encode($retval);
    }

    private function getPlaylistStart($pl_id)
    {
        $pl = new playlist();
        $pl->getPlaylistByID($pl_id);
        echo $pl->getStartEditForm();
    }

    private function savePlaylistStartzeit($pl_id, $uhrzeitStart)
    {

        $pl = new playlist();
        $pl->getPlaylistByID($pl_id);
        $pl->setStartUhrzeit($uhrzeitStart);
        $pl->savePlaylist();
        $pl->setUhrzeiten();
        $retval = $pl->getDataForJson();
        echo json_encode($retval);
    }

    private function getPlayedStatus($ps_id, $pl_id)
    {
        $song = new playlistSong();
        $song->getSongByPlaylist($ps_id);
        echo $song->renderPlayedStatusEdit();
    }

    private function savePlayedStatus($ps_id, $status)
    {
        $song = new playlistSong();
        $song->getSongByPS_ID($ps_id);
        $song->setPlayedStatus($status);
        echo json_encode($song->getPlayedStatus());

    }


}


function z($r)
{
    echo '<pre>';
    print_r($r);
    echo '</pre>';

}
