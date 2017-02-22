<?php

class playlistSong EXTENDS Song
{
    private $pl_id;
    private $pb_id;
    private $ps_id;
    private $songs;
    private $playlist;
    private $pl_datum;
    private $ps_played;
    private $played_class;
    private $played_icon;

    private $ps_erfolg;
    private $erfolg_class;
    private $erfolg_icon;

    public function __construct()
    {
        $this->organizePost(Request::getInstance()->getPostRequests('command'));
        parent::__construct();
    }

    public function getSongByPlaylist($ps_id)
    {
        $song = AGDO::getInstance()->GetFirst("SELECT * FROM playlist_songs JOIN SVsongs USING (id) JOIN  playlists USING (pl_id) JOIN  sv_song_genres USING (g_id)  WHERE ps_id = " . $ps_id);
        $this->setSong($song);
    }

    public function isDeletable()
    {
        return $this->ps_played == 5 || $this->ps_played == 0;
    }

    public function deleteSong()
    {
        if($this->isDeletable())
            AGDO::getInstance()->Execute("DELETE FROM playlist_songs WHERE ps_id = " . $this->ps_id);
    }

    public function setPlayedStatus($status)
    {
        z($this->id);
        z($status);
        $this->ps_played = $status;
        $this->setPlayedButton();
        AGDO::getInstance()->Execute("UPDATE playlist_songs SET ps_played= " . $status . " WHERE ps_id=" . $this->ps_id);
        switch ($status)
        {
            case 1:
            case 2:
                AGDO::getInstance()->Execute("UPDATE SVsongs SET letzteProbe = '" . $this->pl_datum . "', probe=3 WHERE id=" . $this->id);
                break;
            case 3:
                AGDO::getInstance()->Execute("UPDATE SVsongs SET letzteProbe = '" . $this->pl_datum . "', probe=4 WHERE id=" . $this->id);
                break;
        }
    }

    public function setErfolgStatus($status)
    {
        $this->ps_erfolg = $status;
        $this->setErfolgButton();
        AGDO::getInstance()->Execute("UPDATE playlist_songs SET ps_erfolg= " . $status . " WHERE ps_id=" . $this->ps_id);
    }

    public function getPlayedStatus()
    {
        return array('class' => $this->played_class, 'iconClass' => $this->played_icon, 'ps_id' => $this->ps_id);
    }

    public function getSongByPS_ID($ps_id)
    {
        $song = AGDO::getInstance()->GetFirst("SELECT * FROM playlist_songs JOIN SVsongs USING (id) LEFT OUTER JOIN  sv_song_genres ON website = sv_song_genres.g_id JOIN playlists USING (pl_id) WHERE ps_id = " . $ps_id);
        $this->setSong($song);
        $this->setPlayedButton();
        $this->setErfolgButton();
    }

    public function setSong($song)
    {
        parent::setSong($song);
        $this->ps_id = $song['ps_id'];
        $this->pl_id = $song['pl_id'];
        $this->pb_id = $song['pb_id'];
        $this->ps_played = $song['ps_played'];
        $this->ps_erfolg = $song['ps_erfolg'];
        $this->pl_datum = $song['pl_datum'];
        $this->setPlayedButton();
    }

    public function getPosition($pl_id)
    {
        $pos = AGDO::getInstance()->GetFirst("SELECT * FROM playlist_songs WHERE pl_id = " . $pl_id . " AND id=" . $this->id);
        if (isset($pos['ps_id']))
        {
            $this->ps_id = $pos['ps_id'];
        }
        else
        {
            $this->ps_id = 'new';
        }
    }

    public function eraseFromPlaylist()
    {
        AGDO::getInstance()->Execute("DELETE FROM playlist_songs WHERE ps_id=" . $this->ps_id);
    }

    public function renderPlayedStatusEdit()
    {
        $data['ps_id'] = $this->ps_id;
        return TemplateParser::getInstance()->parseTemplate($data, 'Playlist/playedStatusEdit.html', PATH);
    }

    public function renderErfolgStatusEdit()
    {
        $data['ps_id'] = $this->ps_id;
        return TemplateParser::getInstance()->parseTemplate($data, 'Playlist/erfolgStatusEdit.html', PATH);
    }

    public function renderPlaylistSong()
    {
        $vars = get_object_vars($this);
        $vars['title'] = $this->title;
        $vars['interpret'] = $this->interpret;
        $vars['instrumentKlaus'] = $this->instrumentKlaus;
        $vars['bpm'] = $this->bpm;
        $vars['g_id'] = $this->g_id;
        $vars['id'] = $this->id;
        $vars['playedButton'] = $this->managePlayedButton();
        $vars['erfolgButton'] = $this->manageErfolgButton();
        return TemplateParser::getInstance()->parseTemplate($vars, 'Song/playlistSongItem.html', PATH);
    }

    private function setPlayedButton()
    {
        switch ($this->ps_played)
        {
            case 0:
                $this->played_class = 'text-muted';
                $this->played_icon = 'fa-question';
                break;
            case 1:
                $this->played_class = 'text-danger';
                $this->played_icon = 'fa-thumbs-down';
                break;
            case 2:
                $this->played_class = 'text-warning';
                $this->played_icon = 'fa-thumbs-up ';
                break;
            case 3:
                $this->played_class = 'text-success';
                $this->played_icon = 'fa-thumbs-up ';
                break;
            case 5:
                $this->played_class = 'text-muted';
                $this->played_icon = 'fa-minus';
                break;
        }
    }

    private function managePlayedButton()
    {
        if (date("Y-m-d") < $this->pl_datum)
            return '';
        else
        {
            $data = array();
            $data['ps_id'] = $this->ps_id;
            $data['class'] = $this->played_class;
            $data['iconClass'] = $this->played_icon;
            return TemplateParser::getInstance()->parseTemplate($data, 'Playlist/playedButton.html');
        }
    }

    private function manageErfolgButton()
    {
        if (date("Y-m-d") < $this->pl_datum || $this->ps_played == 5)
            return '';
        else
        {
            return $this->setErfolgButton();
        }
    }

    private function setErfolgButton()
    {
        for ($x = 0; $x < 5; $x++)
        {
            if ($this->ps_erfolg >= $x)
            {
                $data['icon_color_class_' . $x] = 'text-warning';
                $data['iconClass_' . $x] = 'fa-star';
            }
            else
            {
                $data['icon_color_class_' . $x] = 'text-muted';
                $data['iconClass_' . $x] = 'fa-star-o';
            }
        }
        $data['ps_id'] = $this->ps_id;
        return TemplateParser::getInstance()->parseTemplate($data, 'Playlist/erfolgButton.html', PATH);
    }

    public function saveErfolgStatus($status)
    {
        $this->ps_erfolg = $status;
        AGDO::getInstance()->Execute("UPDATE playlist_songs SET ps_erfolg = " . $status . " WHERE ps_id=" . $this->ps_id);
        return $this->setErfolgButton();
    }

    private function organizePost($command)
    {
        if ($command)
        {
            $p = Request::getInstance()->getPostRequests();
            switch ($command)
            {
                case 'insertSong':
                    $lastEntry = AGDO::getInstance()->GetFirst("SELECT * FROM playlists WHERE pl_number = " . $p['pl_id'] . " AND  block_number = " . $p['block_number'] . " ORDER BY sort_order DESC");
                    if (isset($lastEntry['sort_order']))
                        $sort_order = $lastEntry['sort_order'] + 10;
                    else
                        $sort_order = 10;
                    AGDO::getInstance()->Execute("INSERT INTO playlists SET pl_number = " . $p['pl_id'] . ", id = " . $p['id'] . ", block_number = " . $p['block_number'] . ", sort_order = " . $sort_order . " ");
                    break;
                default:
                    z($p);
            }
        }
    }
}