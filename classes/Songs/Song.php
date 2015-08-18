<?php

class Song
{
    private $id;
    private $probe =5;
    private $title = 'neuer Title';
    private $interpret = 'neuer Interpret';
    private $genre ='unbekannt';
    private $website = 0;
    private $g_id = 0;
    private $bpm = 0;
    private $instrumentKlaus;
    private $fertig;
    private $kennung = '';
    private $letzeProbe;
    private $notiz = '';
    private $musiker = array('r' => null, 'b' => null, 'k' => null, 'p' => null, 't' => null);
    private $txt = '';
    private $mp3 = '';
    private $highlight = 0;
    private $qualitaet = 0;
    private $ps_played;
    private $zaehler;
    private $statusClass;
    private $duration = 200;
    private $pl_datum = null;
    private $txt_link = '';
    private $mp3_link = '';
    private $angefangen = '0000-00-00';
    private $angefangen_gen = '';
    public $erschienen = '0000-00-00';
    public $erschienen_gen;
    public $erschienen_gen_short;
    private $demolink = '';
    private $demo=0;
    private static $geamtZaehler = 1;
    private $classFifty = '';


    public function __construct()
    {
        $this->zaehler = self::$geamtZaehler++;
        foreach (array_keys($this->musiker) AS $key)
            $this->musiker[$key] = new Musiker($key);
    }

    public function __set($var, $value)
    {
        if (property_exists($this, $var))
            $this->$var = trim($value);;
    }

    public function __get($var)
    {
        if (property_exists($this, $var))
            return $this->$var;
    }

    public function getID()
    {
        return $this->id;
    }

    public function setClassForFifty()
    {
        if($this->erschienen < (date("Y")-35).'-'.date("m-d"))
            $this->classFifty = 'text-muted';
    }

    public function getCentury()
    {
        return substr($this->erschienen, 0,3).'0';
    }

    public function setSong($song)
    {
        $song['b'] = $song['c'];
        $song['arr_b'] = $song['arr_c'];
        $song['arr_t'] = '';
        $song['arr_p'] = '';
        $inst = array('Western', 'E-Gitarre', 'Akkordeon', 'Keyboard');
        foreach ($song AS $key => $value)
            if (property_exists($this, $key))
                $this->$key = trim($value);
        $this->duration = $song['duration'] == 0 ? 200 : $song['duration'];
        $this->genre = $song['g_name'];
        $this->g_id = $song['g_id'];

        $this->instrumentKlaus = $inst[$song['instrument']];
        $this->setMusiker($song);
        $this->setStatusClass();
        $this->angefangen_gen = TimestampConverter::getInstance()->convertSQLtoLesbar($this->angefangen);
        $this->erschienen_gen = TimestampConverter::getInstance()->convertSQLtoLesbar($this->erschienen);
        $this->erschienen_gen_short = substr($this->erschienen, 0,4);
        $this->txt_link =
            $this->txt!=''?'<a href="/files/'.$this->txt.'">txt</a>':'';
        $this->mp3_link = $this->mp3!=''?'<a href="/files/'.$this->mp3.'">mp3</a>':'';
    }

    public function setKennung()
    {
        $this->kennung = $this->clean($this->title) . '_' . $this->clean($this->interpret);
    }

    private function clean($str)
    {
        $str = trim(strtolower($str));
        $str = str_replace(' ','-', $str);
        $str = str_replace('ä','ae', $str);
        $str = str_replace('ö','oe', $str);
        $str = str_replace('ü','ue', $str);
        $str = str_replace('ß','ss', $str);

        return $str;
    }


    public function getSongByID($song_id)
    {
        if ($song_id == 'new')
        {

        }
        else
        {
            $song = AGDO::getInstance()->GetFirst("SELECT * FROM SVsongs LEFT OUTER JOIN sv_song_genres USING (g_id)  WHERE id = " . $song_id);
            $song['b'] = $song['c'];
            $song['arr_b'] = $song['arr_c'];
            $song['arr_t'] = '';
            $song['arr_p'] = '';
            $this->setSong($song);
        }
    }

    private function setStatusClass()
    {
        $forbidden = false;
        foreach (array_keys($this->musiker) AS $key)
        {
            if (!$this->musiker[$key]->isAllowed())
            {
                $this->statusClass = 'text-deleted';
                $forbidden = true;
                break;
            }
        }
        if (!$forbidden)
        {
            switch ($this->probe)
            {
                case 1:
                    $this->statusClass = 'text-muted';
                    break;
                case 2:
                    $this->statusClass = 'text-success strong';
                    break;
                case 3:
                    $this->statusClass = 'text-danger strong nächsteProbe ';
                    break;
                case 4:
                    $this->statusClass = 'strong';
                    break;
                case 5:
                    $this->statusClass = 'text-warning strong';
                    break;
            }
        }


    }

    public function getDuration()
    {
        return $this->duration;
    }

    private function setMusiker($song)
    {
        foreach (array_keys($this->musiker) AS $key)
            $this->musiker[$key]->setMusiker($song);
    }

    public function renderSong($template = 'songListItem')
    {
        $vars = get_object_vars($this);
        foreach (array_keys($this->musiker) AS $key)
        {
            $vars['icon_' . $key] = $this->musiker[$key]->getStatusIcon();
            $vars['class_' . $key] = $this->musiker[$key]->getStatusClass();
        }
        $vars['q']= str_replace(' ', '+', $this->title.' '.$this->interpret);
        $vars['statusClass'] = $this->statusClass;
        return TemplateParser::getInstance()->parseTemplate($vars, 'Song/'.$template.'.html');
    }

    public function renderArrangement()
    {
        $vars = get_object_vars($this);
        foreach (array_keys($this->musiker) AS $key)
        {
            $vars['arr_' . $key] = $this->musiker[$key]->getArrangement();
        }


        for ($x = 0; $x < 6; $x++)
            $vars[$x . '_checked'] = $this->probe == $x ? ' checked="checked"' : '';
        for ($x = 0; $x < 4; $x++)
            $vars[$x . '_i_checked'] = $this->instrument == $x ? ' checked="checked"' : '';

        return TemplateParser::getInstance()->parseTemplate($vars, 'Song/arrangementInfo.html', PATH);

    }

    public function renderMuckermeinung($musiker)
    {
        $data['id'] = $this->id;
        $data['musiker'] = $musiker;
        $data['title'] = $this->title;
        $data['interpret'] = $this->interpret;
        for ($x = 0; $x < 6; $x++)
            $data['meinung_' . $x . '_checked'] = $this->musiker[$musiker]->getStatus() == $x ? ' checked="checked"' : '';
        return TemplateParser::getInstance()->parseTemplate($data, 'Song/muckerMeinung.html', PATH);
    }

    public function renderUebrigeSong($blockNumber)
    {
        $vars = get_object_vars($this);
        $vars['playedButton']='';
        $vars['lastBlockNumber'] = $blockNumber;

        return TemplateParser::getInstance()->parseTemplate($vars, 'Song/playlistSongItem.html', PATH);
    }

    public function getEdit()
    {
        $vars = get_object_vars($this);
        return TemplateParser::getInstance()->parseTemplate($vars, 'Song/editSong.html');
    }

    public function updateSong($post)
    {
        foreach($post AS $var=>$value)
            if (property_exists($this, $var))
                $this->$var = trim($value);
        $this->saveSong();

    }

    public function saveSong()
    {
        $vars = get_object_vars($this);
        if ($vars['id'] != 'new')
            AGDO::getInstance()->AutoExecute('SVsongs', $vars, 'UPDATE', 'id=' . $vars['id']);
        else
        {
            $vars['probe'] = 5;
            AGDO::getInstance()->AutoExecute('SVsongs', $vars, 'INSERT');
            $this->id = AGDO::getInstance()->Insert_ID();
        }
        $this->clearCache();
    }

    public function delete()
    {
        $mp3 = PATH.'files/'.$this->mp3;
        if(file_exists($mp3))
            unlink($mp3);
        $txt = PATH.'files/'.$this->txt;
        if(file_exists($txt))
            unlink($txt);
        AGDO::getInstance()->Execute("DELETE  FROM SVsongs  WHERE id = " . $this->id);
        $this->clearCache();
        header("location:index.php?idt=Liste&status=1");
    }

    private function clearCache()
    {
        $genres = AGDO::getInstance()->GetAll("SELECT * FROM sv_song_genres");
        foreach($genres AS $genre)
            unlink('../sweetvillage/cache/'.$genre['g_link.html']);
        unlink('../sweetvillage/cache/partymusik.html');
    }

}