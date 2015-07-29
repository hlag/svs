<?php
/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 15.07.15
 * Time: 18:44
 */

class uebrigePDF
{

    public function __construct()
    {

    }

    public function getContent()
    {
        $playlist = new playlist();
        $playlist->getPlaylistByID(Request::getInstance()->getGetRequests('pl_id'));
        $playlist->getUebrigeSongsSorted();
        require_once PATH.'classes/Songs/uebrigePDFgenerator.php';
        $uebrigePDFgenerator = new uebrigePDFgenerator();
        $uebrigePDFgenerator->generatePDF($playlist);
        die();
    }

}