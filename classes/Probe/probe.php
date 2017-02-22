<?php

class probe
{
    private $songs;

    public function getContent()
    {
        $this->getSongs(Request::getInstance()->getGetRequests('status'));
        $data['rows'] = $this->renderSongs();
        $data['options'] = $this->getActualPlaylists();
        return TemplateParser::getInstance()->parseTemplate($data, 'Probe/ProbeRahmen.html');
    }

    private function getSongs()
    {
        $dringend = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE probe = 3 ORDER BY title");
        $proben = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE probe = 2 ORDER BY title");
        $sonstige = $this->getSonstige();
        $songs = array_merge($dringend, $proben, $sonstige);
        foreach ($songs AS $song)
        {
            $song['b'] = $song['c'];
            $song['arr_b'] = $song['arr_c'];
            $song['arr_t'] = '';
            $song['arr_p'] = '';
            $S = new Song();
            $S->setSong($song);
            $this->songs[$song['id']] = $S;
        }
    }
    private function renderSongs()
    {
        $retval = '';
        foreach($this->songs AS $S)
           $retval .= $S->renderSong('probeListItem');
        return $retval;
    }
    private function getSonstige()
    {
        $songs = array();
        $res = AGDO::getInstance()->GetAll("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id) WHERE probe = 4 ");
        foreach ($res AS $s)
            $songs[$s['id']] = $s;
        $m_id = Login::getInstance()->getUserID();
        $sql = "SELECT * FROM letzteProbe WHERE m_id = " . $m_id . " ORDER BY lp_datum";
        $dates = AGDO::getInstance()->GetAll($sql);
        $index = array();
        foreach ($dates AS $date)
            $index[$date['id']] = true;
        $played = array();
        foreach (array_keys($index) AS $key)
        {
            if (isset($songs[$key]))
            {
                $played[$key] = $songs[$key];
                unset($songs[$key]);
            }
        }
        $merged = array_merge($songs, $played);
        return $merged;
    }

    private function getActualPlaylists()
    {
        $res = AGDO::getInstance()->GetAll("SELECT * FROM playlists WHERE pl_datum >= '".date("Y-m-d")."' ORDER BY pl_datum");
            $retval = '<option value="0">Playlists Kommender Jobs</option>';
        foreach($res AS $pl)
        {
            $retval .= '<option value="'.$pl['pl_id'].'">'.TimestampConverter::getInstance()->convertSQLtoLesbar($pl['pl_datum']).' '.$pl['pl_name'].'</option>';
        }
        return $retval;
    }

}