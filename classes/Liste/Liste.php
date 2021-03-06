<?php
class Liste
{
    private $songs = array();
    public function __construct()
    {
       // z(Request::getInstance());

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
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id) ORDER BY title");
        }
        elseif($status == 'repertoire')
        {
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE probe != 1 AND probe != 5 ORDER BY title");
        }
        elseif($status == 'uebrige')
        {
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE probe != 1 AND probe != 5  AND probe != 6 ORDER BY g_id,  title");
        }
        elseif($status == 'erschienen')
        {
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)   ORDER BY erschienen DESC, interpret");
        }
        elseif($status == 2)
        {
            $dringend = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE probe = 3 ORDER BY title");
            $proben = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE probe = 2 ORDER BY title");
            $sonstige = $this->getSonstige();
            $songs = array_merge($dringend, $proben, $sonstige);
        }
        elseif($status == 5)
        {
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE
                    angefangen > '".date("Y-m-d", time()-3600*24*60)."' OR probe = '5' ORDER BY angefangen DESC");
        }
        else
        {
            $songs = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE probe = '".$status."' ORDER BY title");
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

    private function getSonstige()
    {
        $songs = array();
        $res = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id) WHERE probe = 4 ");
        foreach($res AS $s)
            $songs[$s['id']] = $s;

        $m_id = Login::getInstance()->getUserID();
        $sql = "SELECT * FROM letzteProbe WHERE m_id = ".$m_id." ORDER BY lp_datum";
        $dates = AGDO::getInstance()->GetAll($sql);
        $index = array();
        foreach($dates AS $date)
            $index[$date['id']] = true;
        $played = array();


        foreach(array_keys($index) AS $key)
        {
            if(isset($songs[$key]))
            {
                $played[$key] = $songs[$key];
                unset($songs[$key]);
            }
        }


        $merged = array_merge($songs, $played);
        return $merged;

    }

    private function renderSongs()
    {
        $retval = '';
        foreach($this->songs AS $S)
           $retval .= $S->renderSong();
        return $retval;
    }
}