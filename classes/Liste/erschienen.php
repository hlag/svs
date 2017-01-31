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

        $data['erschienenListitems'] ='';
        foreach($this->centuries AS $year=> $Songs)
        {
            $data['erschienenListitems'] .= '<h2>'.substr($year,2,2).'\'er</h2>';
            foreach ($Songs AS $song)
            {
                $song->setClassForFifty();
                $data['erschienenListitems'] .= $song->renderSong('songErschienenListItem');
            }
        }
        return TemplateParser::getInstance()->parseTemplate($data, 'Liste/erschienen.html');
    }

    private function sortSongToCentury($songs)
    {
        foreach($songs AS $song)
        {
            $this->centuries[$song->getCentury()][]=$song;
        }
    }
}