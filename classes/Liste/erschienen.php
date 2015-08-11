<?php
class erschienen
{
    public function __construct()
    {

    }

    public function getContent()
    {
        $liste = new Liste();
        $songs  =  $liste->getSongs('erschienen');
        $retval = '<br><br><h1>erschienen</h1>';
        foreach($songs AS $song)
            $retval .= $song->renderSong('songErschienenListItem');
        return $retval;
    }
}