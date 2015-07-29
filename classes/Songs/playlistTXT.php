<?php
/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 15.07.15
 * Time: 15:53
 */

class playlistTXT {

    public function __construct()
    {

    }

    public function getContent()
    {
        $playlist = new playlist();
        $playlist->getPlaylistByID(Request::getInstance()->getGetRequests('pl_id'));
        echo 'PLAYLIST '.$playlist->name.'<br>';
        foreach($playlist->bloecke AS $b)
        {
            echo $b->pb_sort_order.'<br>';
            foreach($b->songs AS $s)
                echo $s->title.'<br>';

        }
        die();
    }
}
