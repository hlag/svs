<?php
class Liste
{
    private $songs = array();
    public function __construct()
    {

    }

    public function __get($var)
    {
        if(property_exists($this, $var))
            return $this->$var;
    }
    public function getContent()
    {
        $this->getSongs(Request::getInstance()->getGetRequests('status'));
        $data['rows'] = $this->renderSongs();
        return TemplateParser::getInstance()->parseTemplate($data, 'Liste/liste.html');
    }


    public function getSongs($status)
    {
        if($status == 'all')
        {
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres ON g_id = website ORDER BY title");
        }
        elseif($status == 'repertoire')
        {
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres ON g_id = website WHERE probe != 1 AND probe != 5 ORDER BY title");
        }
        elseif($status == 'uebrige')
        {
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres ON g_id = website WHERE probe != 1 AND probe != 5 ORDER BY g_id,  title");
        }
        elseif($status == 2)
        {
            $dringend = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres ON g_id = website WHERE probe = 3 ORDER BY title");
            $proben = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres ON g_id = website WHERE probe = 2 ORDER BY title");
            $sonstige = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres ON g_id = website WHERE probe = 4 ORDER BY letzteProbe");
            $songs = array_merge($dringend, $proben, $sonstige);
        }
        else
        {
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres ON g_id = website WHERE probe = '".$status."' ORDER BY title");
        }


        foreach($songs AS $song)
        {


            $song['b'] = $song['c'];
            $song['arr_b'] = $song['arr_c'];
            $song['arr_t'] = '';
            $song['arr_p'] = '';
            $S = new Song();
            $S->setSong($song);
            $this->songs[$song['id']] = $S;
        }

        return $this->songs;
    }

    private function renderSongs()
    {
        $retval = '';
        foreach($this->songs AS $S)
           $retval .= $S->renderSong();
        return $retval;
    }
}