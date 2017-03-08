<?php

/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 08.03.17
 * Time: 13:46
 */
class gemaPDF
{
    public function __construct()
    {

    }

    public function getContent()
    {
        $playlist = new playlist();
        $playlist->getPlaylistByID(Request::getInstance()->getGetRequests('pl_id'));
        require_once PATH . 'classes/Playlist/gemaPDFgenerator.php';
        $gemaPDFgenerator = new gemaPDFgenerator();
        $gemaPDFgenerator->generatePDF($playlist);
        die();
    }
}