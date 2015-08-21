<?php

class playlistPDF {

    public function __construct()
    {

    }

    public function getContent()
    {
        $playlist = new playlist();
        $playlist->getPlaylistByID(Request::getInstance()->getGetRequests('pl_id'));
        require_once PATH . 'classes/Playlist/playlistPDFgenerator.php';
        $playlistPDFgenerator = new playlistPDFgenerator();
        $playlistPDFgenerator->generatePDF($playlist);
        die();
    }
}
