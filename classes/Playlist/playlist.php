<?php

class playlist
{

    private $pl_id;
    private $name;
    private $pl_datum;
    private $pl_start = 0;
    private $startHM;
    private $duration = 0;
    private $bloecke = array();
    private $usedSongs;
    private $uebrigeSongs;
    private $uebrigeSongsArray;
    private $genreStats = array();
    private $tempoStats = array();
    private $songCount = 0;

    public function __construct()
    {
        if(Request::getInstance()->getGetRequests('change'))
        {
            $blockSorter = new blockSorter();
            $blockSorter->sortBloecke(Request::getInstance()->getGetRequests());

        }
    }

    public function __get($var)
    {
        if (property_exists($this, $var))
            return $this->$var;
    }

    public function getContent()
    {
        if(Request::getInstance()->getGetRequests('pl_id'))
            return $this->getPlaylist(Request::getInstance()->getGetRequests('pl_id'));
        else
            return $this->getAllPlaylists();



    }

    public function getDatum()
    {
        return TimestampConverter::getInstance()->convertSQLtoLesbar($this->pl_datum);
    }

    public function setDatum($datumLesbar)
    {
        $this->pl_datum = TimestampConverter::getInstance()->convertLesbarToSQL($datumLesbar);
    }

    private function getAllPlaylists()
    {
        $listen = AGDO::getInstance()->GetAll("SELECT * FROM playlists ORDER BY pl_datum DESC");
        $data['listen'] = '';
        foreach($listen AS $liste)
        {
            $liste['datumLesbar'] = TimestampConverter::getInstance()->convertSQLtoLesbar($liste['pl_datum']);
            $data['listen'] .= TemplateParser::getInstance()->parseTemplate($liste, 'Playlist/playlistListItem.html');
        }
        return TemplateParser::getInstance()->parseTemplate($data, 'Playlist/playlistUebersicht.html');
    }

    private function getPlaylist($pl_id)
    {
        $this->getPlaylistByID($pl_id);
        return $this->renderHTML();
    }

    public function getCountSongs()
    {
        return count($this->usedSongs);
    }

    public function getNextSortorder()
    {
        return count($this->bloecke)+1;
    }
    public function getPlaylistByID($pl_id)
    {
        $this->getListe($pl_id);
        $this->getBloecke();
        $this->getSongs();
        $this->setUhrzeiten();
        $this->calculateDuration();
        //z($this);
    }

    public function getMinutes()
    {
        return TimestampConverter::getInstance()->secToMinute($this->duration);
    }

    private function getListe($pl_id)
    {
        $playlist = AGDO::getInstance()->GetFirst("SELECT * FROM playlists WHERE pl_id = " . $pl_id);
        if(!isset($playlist['pl_id']))
        {
            AGDO::getInstance()->Execute("INSERT INTO playlists SET pl_name = 'neu', pl_datum='".date('Y-m-d')."', pl_id = ".$pl_id);
            $pl_id = AGDO::getInstance()->Insert_ID();
            $playlist = AGDO::getInstance()->GetFirst("SELECT * FROM playlists WHERE pl_id = " . $pl_id);
        }

        $this->pl_id = $playlist['pl_id'];
        $this->name = $playlist['pl_name'];
        $this->pl_datum = $playlist['pl_datum'];
        $this->pl_start = $playlist['pl_start'];
        $this->pl_datum_gen = TimestampConverter::getInstance()->convertSQLtoLesbarMitTag($playlist['pl_datum']);
    }

    private function getBloecke()
    {
        $bloecke = AGDO::getInstance()->GetAll("SELECT * FROM playlist_bloecke WHERE pl_id = " . $this->pl_id . " ORDER BY pb_sort_order");
        $number = 1;
        foreach ($bloecke AS $data)
        {
            $data['number'] = $number++;
            $block = new playlist_bloecke();
            $block->setBlock($data);
            $this->bloecke[$data['pb_id']] = $block;
        }
    }

    public function setStartUhrzeit($uhrzeitStart)
    {
        $this->pl_start  = TimestampConverter::getInstance()->convertDatumAnUhrzeitToUnix($this->pl_datum, $uhrzeitStart);
    }

    public function setUhrzeiten()
    {
        $uhrzeitTS = $this->pl_start;
        foreach(array_keys($this->bloecke) AS $key)
        {
            $this->bloecke[$key]->pb_start = $uhrzeitTS;
            $uhrzeitTS += $this->bloecke[$key]->duration;
            $uhrzeitTS += $this->bloecke[$key]->pb_pause;
        }
    }

    private function getSongs()
    {
        $songs = AGDO::getInstance()->GetAll("SELECT * FROM playlist_songs JOIN SVsongs USING (id) LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE pl_id=" . $this->pl_id . " ORDER BY  ps_sort_order");
        foreach ($songs AS $song)
        {
            $song['pl_datum'] = $this->pl_datum;
            $this->bloecke[$song['pb_id']]->setSong($song);
            $this->usedSongs[$song['id']] = $song['id'];
        }
    }

    public function renderHTML()
    {

        $data['songs'] = '';
        $number = 1;
        foreach ($this->bloecke AS $block)
        {
            $data['songs'] .= $block->renderHTML($number++);
        }
        $data['startHM'] = $this->calculateStartHM();
        $data['pl_id'] = $this->pl_id;
        $data['pl_name'] = $this->name;
        $data['uebrigeSongs'] = $this->getUebrigeSong();
        $data['dauer_min'] = $this->getMinutes();
        $data['pl_datum_gen'] = $this->pl_datum_gen;
        $data['uhrzeitStart'] = date("G:i", $this->pl_start);


        return TemplateParser::getInstance()->parseTemplate($data, 'Playlist/PlaylistRahmen.html');
    }

    private function calculateStartHM()
    {
        if ($this->statTS == 0)
        {
            $this->startHM = mktime(21, 0, 0);
        }
    }

    public function getUebrigeSong()
    {
        $liste = new Liste();
        $songs = $liste->getSongs('uebrige');
        $retval = '';
        $headline='';
        foreach ($songs AS $S)
        {
            if($S->genre !=  $headline)
            {
                $headline = $S->genre;
                $retval.= '<h2>'.$headline.'</h2>';
            }

            if (!isset($this->usedSongs[$S->getID()]))
            {
                $retval .= $S->renderUebrigeSong(0);
                $this->uebrigeSongs[] = $S;
            }
        }
        return $retval;

    }
    public function getUebrigeSongsSorted()
    {
        $this->getUebrigeSong();
        foreach($this->uebrigeSongs AS $song)
        {
            $this->uebrigeSongsArray[$song->genre]['name']= $song->genre;
            $this->uebrigeSongsArray[$song->genre]['songs'][$song->id] = $song;
            if(!isset($this->uebrigeSongsArray[$song->genre]['duration']))
                $this->uebrigeSongsArray[$song->genre]['duration'] = $song->getDuration();
            else
                $this->uebrigeSongsArray[$song->genre]['duration'] += $song->getDuration();

        }
    }

    private function calculateDuration()
    {
        foreach (array_keys($this->bloecke) AS $key)
        {
            $this->duration += $this->bloecke[$key]->getDuration();
            $this->duration += $this->bloecke[$key]->pb_pause;

        }
    }

    public function getDataForJson()
    {
        $retval = array();
        foreach(array_keys($this->bloecke) AS $key)
        {
            $retval['v'][] = array('id' => 'block_dauer_' . $this->bloecke[$key]->pb_id,        'v' => $this->bloecke[$key]->getMinutes());
            $retval['v'][] = array('id' => 'pause_' . $this->bloecke[$key]->pb_id,              'v' => $this->bloecke[$key]->getPausenMin());
            $retval['v'][] = array('id' => 'block_startUhrzeit_' . $this->bloecke[$key]->pb_id, 'v' => $this->bloecke[$key]->getUhrzeit());
            $retval['c'][] = array('id' => 'delete_block_' . $this->bloecke[$key]->pb_id,       'v' => $this->bloecke[$key]->getDeleteClass());
        }


        $retval['v'][] = array('id' =>'playlist_dauer_'.$this->pl_id, 'v'=>$this->getMinutes());
        $retval['v'][] = array('id' =>'playlist_uhrzeitStart_'.$this->pl_id, 'v'=>date("G:i", $this->pl_start));
        return $retval;
    }

    public function getStartEditForm()
    {
        $data['pl_id'] = $this->pl_id;
        $data['uhrzeitStart'] = date("G:i", $this->pl_start);
        return  TemplateParser::getInstance()->parseTemplate($data, 'Playlist/startEditform.html', PATH);

    }

    public function savePlaylist()
    {
        AGDO::getInstance()->Execute("UPDATE playlists SET pl_name = '".$this->pl_name."',
         pl_datum = '".$this->pl_datum."',
          pl_start = '".$this->pl_start."'
          WHERE  pl_id = '".$this->pl_id."'");
    }


}