<?php

/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 31.01.17
 * Time: 17:07
 */
class testDuplicatePlaylist
{
    public function getContent()
    {
        $dp = new duplicatePlaylist();
        $dp->copyPlayList(9, 'Fantasiename', '30.4.2017');
    }
}