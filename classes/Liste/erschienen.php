<?php
class erschienen
{
    private         $centuries = array();

    public function __construct()
    {
        $start = substr(date("Y"), 0,3);
        for($x = $start; $x >= 195; $x--)
            $this->centuries[$x.'0']= array();
        $this->centuries['0000'] = array();

    }

    public function getContent()
    {
        $liste = new Liste();
        $songs  =  $liste->getSongs('erschienen');
        $this->sortSongToCentury($songs);

        $retval = '<br><br><h2>erschienen</h2>';
        foreach($this->centuries AS $year=> $Songs)
        {
            $retval .= '<h1>'.substr($year,2,2).'\'er</h1>';
            foreach ($Songs AS $song)
            {
                $song->setClassForFifty();
                $retval .= $song->renderSong('songErschienenListItem');
            }
        }
        return $retval;
    }

    private function sortSongToCentury($songs)
    {

        foreach($songs AS $song)
        {
            $this->centuries[$song->getCentury()][]=$song;
        }
    }
}