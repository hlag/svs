<?php
class genres
{
    private $songs = array();
    private $nichtZugeordneteSongs = array();
    public function __construct()
    {

    }

    public function getContent()
    {
        $ls = new Liste();
        $songs = $ls->getSongs('all');
        $this->sortSongs($songs);
        $data['songs'] = $this->renderGroups();
        $data['nichtZugerodnet'] = $this->renderGroups(true);

        return TemplateParser::getInstance()->parseTemplate($data, 'Liste/genres.html');
    }

    private function renderGroups($reste = false)
    {
        $retval = '';
        foreach($this->songs AS $genre)
        {
            if($reste == ($genre['g_id'] == 1))
            {
                $data = array();
                $data['header'] = $genre['g_name'];
                $data['songs'] = $this->renderSongs($genre['songs']);
                $retval .= TemplateParser::getInstance()->parseTemplate($data, 'Liste/Block.html');
            }
        }
        return $retval;
    }

    private function renderSongs($songs)
    {
        $retval = '';
        foreach($songs AS $song)
        {
            $retval .= $song->renderSong('genreListItem');
        }
        return  $retval;
    }


    private function sortSongs($songs)
    {
        foreach($songs AS $song)
        {
            $this->songs[$song->g_id]['g_name'] = $song->genre;
            $this->songs[$song->g_id]['g_id'] = $song->g_id;
            $this->songs[$song->g_id]['songs'][] = $song;
        }
    }
}