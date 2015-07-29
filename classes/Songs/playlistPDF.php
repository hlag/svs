<?php
/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 15.07.15
 * Time: 15:53
 */

class playlistPDF {

    public function __construct()
    {

    }

    public function getContent()
    {
        $playlist = new playlist();
        $playlist->getPlaylistByID(Request::getInstance()->getGetRequests('pl_id'));
        require_once PATH.'classes/Songs/playlistPDFgenerator.php';
        $playlistPDFgenerator = new playlistPDFgenerator();
        $playlistPDFgenerator->generatePDF($playlist);
        die();
    }
}
