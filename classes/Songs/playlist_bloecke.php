<?php

class playlist_bloecke
{
    private $pb_id;
    private $id;
    private $pl_id;
    private $pb_sort_order;
    private $pb_name;
    private $pb_pause = 0;
    private $number;
    private $duration = 0;
    private $pb_start;
    private $pl_datum;
    private $startUhrzeit;
    private $songs = array();
    private $table = 'playlist_bloecke';
    private $id_name = 'pb_id';
    private $deleteButton = 'hidden';

    public function __construct()
    {

    }

    public function __get($var)
    {
        if (property_exists($this, $var))
            return $this->$var;
    }

    public function __set($var, $value)
    {
        if (property_exists($this, $var))
            $this->$var = trim($value);;
    }

    public function getMinutes()
    {
        return TimestampConverter::getInstance()->secToMinute($this->duration);
    }
    public function getPausenMin()
    {
        return TimestampConverter::getInstance()->secToMinute($this->pb_pause);
    }

    public function getDeleteClass()
    {
        return $data['deleteButton'] = count($this->songs) == 0 ? '' : 'hidden';
    }

    public function getUhrzeit()
    {
        return date("G:i",$this->pb_start);
    }


    public function getBlockByID($pb_id, $pl_id = false)
    {
        if ($pb_id == 'new')
        {
            AGDO::getInstance()->GetFirst("INSERT INTO playlist_bloecke SET pl_id =" . $pl_id);
            $pb_id = AGDO::getInstance()->Insert_ID();
        }
        $block = AGDO::getInstance()->GetFirst("SELECT * FROM playlist_bloecke WHERE pb_id =" . $pb_id);
        $this->setBlock($block);
    }

    public function setBlock($data)
    {
        foreach ($data AS $key => $value)
            $this->$key = $value;
        $this->id = $this->pb_id;
    }

    public function setPause($pause) // hh:mm:ss | mm:ss
    {
        $this->pb_pause = TimestampConverter::getInstance()->hMinSecToSec($pause);
    }

    public function getSongs()
    {
        $songs = AGDO::getInstance()->GetAll("SELECT * FROM playlist_songs JOIN SVsongs USING (id)  LEFT OUTER JOIN sv_song_genres ON g_id = website WHERE pl_id = " . $this->pl_id . " AND pb_id = " . $this->pb_id . " ORDER BY ps_sort_order");

        foreach ($songs AS $song)
            $this->setSong($song);
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setSong($data)
    {
        $S = new playlistSong();
        $S->setSong($data);
        $this->duration += $S->getDuration();
        $this->songs[$data['id']] = $S;
    }

    public function resortSongs($newPos, $id)
    {
        if (isset($this->songs[$id]))
        {
            unset($this->songs[$id]);
            $method = 'UPDATE';
        }
        else
        {
            $method = 'INSERT';
            AGDO::getInstance()->Execute("DELETE FROM playlist_songs WHERE  id=" . $id . " AND pl_id = " . $this->pl_id);

        }

        $count = 1;
        foreach ($this->songs AS $song)
        {
            if ($count == $newPos)
            {
                if ($method == 'UPDATE')
                {
                    AGDO::getInstance()->Execute("UPDATE playlist_songs SET ps_sort_order =  " . $newPos . " WHERE id=" . $id . " AND pb_id = " . $this->pb_id);
                }
                else
                {
                    AGDO::getInstance()->Execute("INSERT INTO playlist_songs SET ps_sort_order =  " . $newPos . ", id=" . $id . ", pb_id = " . $this->pb_id . ", pl_id = " . $this->pl_id);
                }
                $count++;
            }

            AGDO::getInstance()->Execute("UPDATE playlist_songs SET ps_sort_order =  " . $count . " WHERE id=" . $song->id . " AND pb_id = " . $this->pb_id);
            $count++;
        }

        if ($newPos > count($this->songs))
            AGDO::getInstance()->Execute("INSERT INTO playlist_songs SET ps_sort_order =  " . $newPos . ", id=" . $id . ", pb_id = " . $this->pb_id . ", pl_id = " . $this->pl_id);

        //z($song);
    }

    public function renderHTML($number)
    {
        $data['deleteButton'] = count($this->songs) == 0 ? '' : 'hidden';
        $data['number'] = $number;
        $data['pl_id'] = $this->pl_id;
        $data['pb_id'] = $this->pb_id;
        $data['name'] = $this->pb_name;
        $data['pb_sort_order'] = $this->pb_sort_order;
        $data['dauer'] = TimestampConverter::getInstance()->secToMinute($this->duration);
        $data['pb_pause_min'] = TimestampConverter::getInstance()->secToMinute($this->pb_pause);
        $data['songs'] = $this->renderSongHtml();
        $data['startUhrzeit'] = date('H:i', $this->pb_start);
        return TemplateParser::getInstance()->parseTemplate($data, 'Playlist/block.html', PATH);
    }

    private function renderSongHtml()
    {
        $retval = '';
        foreach ($this->songs AS $song)
            $retval .= $song->renderPlaylistSong();
        return $retval;
    }

    public function renderEditMaske()
    {

        $vars = get_object_vars($this);
        $vars['value'] = $this->pb_name;
        $vars['field'] = 'pb_name';
        $vars['id'] = $this->pb_id;

        return TemplateParser::getInstance()->parseTemplate($vars, 'Playlist/editSingleData.html', PATH);

    }

    public function getPauseEdit()
    {
        $data['pause'] = TimestampConverter::getInstance()->secToMinute($this->pb_pause);
        $data['pb_id'] = $this->pb_id;
        return TemplateParser::getInstance()->parseTemplate($data, 'Bloecke/editPause.html', PATH);
    }

    public function saveBlock()
    {
        $vars = get_object_vars($this);
        if ($vars['pb_id'] != '' AND $vars['pb_id'] != 'new')
            AGDO::getInstance()->AutoExecute('playlist_bloecke', $vars, 'UPDATE', 'pb_id=' . $vars['pb_id']);
        if ($vars['pb_id'] == 'new')
        {
            $this->pb_name = 'neu';
            $vars['pb_name'] = 'neu';
            $vars['pb_pause'] = 180;
            $this->pb_pause = 180;

            AGDO::getInstance()->AutoExecute('playlist_bloecke', $vars);
            $this->pb_id = AGDO::getInstance()->Insert_ID();
        }
    }

    public function deleteBlock($pb_id)
    {
        $this->getBlockByID($pb_id);
        $this->getSongs();
        if (count($this->songs) == 0)
            AGDO::getInstance()->Execute("DELETE FROM playlist_bloecke WHERE pb_id=" . $this->pb_id);

    }
}