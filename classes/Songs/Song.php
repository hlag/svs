<?php

class Song
{
    private $id;
    private $probe = 5;
    private $title = 'neuer Title';
    private $interpret = 'neuer Interpret';
    private $geschlecht = 0;
    private $genre = 'unbekannt';
    private $website = 0;
    private $website_activ_class = 'text-muted';
    private $website_activ_icon;
    private $highlight = 0;
    private $highlight_activ_class = 'text-muted';
    private $highlight_activ_icon;
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
    private $demo = 0;
    private static $geamtZaehler = 1;
    private $classFifty = '';
    private $icons = array('nix', 'fa-venus', 'fa-mars', 'fa-venus-mars');

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
        if ($this->erschienen < (date("Y") - 35) . '-' . date("m-d"))
            $this->classFifty = 'text-muted';
    }

    public function getCentury()
    {
        return substr($this->erschienen, 0, 3) . '0';
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
        if ($this->txt == '_.doc')
            $this->txt = '';
        if ($this->mp3 == '_.mp3')
            $this->mp3 = '';
        $this->setKennung();
        $this->duration = $song['duration'] == 0 ? 200 : $song['duration'];
        $this->genre = $song['g_name'];
        $this->g_id = $song['g_id'];
        $this->instrumentKlaus = $inst[$song['instrument']];
        $this->setMusiker($song);
        $this->setStatusClass();
        $this->angefangen_gen = TimestampConverter::getInstance()->convertSQLtoLesbar($this->angefangen);
        $this->erschienen_gen = TimestampConverter::getInstance()->convertSQLtoLesbar($this->erschienen);
        $this->erschienen_gen_short = substr($this->erschienen, 0, 4);
        $this->txt_link = $this->txt != '' ? '<a class="{class_txt}" href="/files/' . $this->txt . '">txt</a>' : '';
        $this->mp3_link = $this->mp3 != '' ? '<a  class="{class_mp3}" href="/files/' . $this->mp3 . '">mp3</a>' : '';
        $this->website_activ_class = $this->website == 1 ? 'text-success' : 'text-muted';
        $this->website_activ_icon = $this->website == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
        $this->highlight_activ_class = $this->highlight == 1 ? 'text-success' : 'text-muted';
        $this->highlight_activ_icon = $this->highlight == 1 ? 'fa-thumbs-up' : 'fa-thumbs-o-up';
    }

    public function setKennung()
    {
        $this->kennung = $this->clean($this->title) . '_' . $this->clean($this->interpret);
    }

    private function clean($str)
    {
        $str = trim(strtolower($str));
        $str = str_replace(' ', '-', $str);
        $str = str_replace('ä', 'ae', $str);
        $str = str_replace('ö', 'oe', $str);
        $str = str_replace('ü', 'ue', $str);
        $str = str_replace('ß', 'ss', $str);
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
        $vars['class_txt'] = file_exists(PATH . 'files/' . $vars['txt']) ? '' : 'text-danger strong';
        $vars['class_mp3'] = file_exists(PATH . 'files/' . $vars['mp3']) ? '' : 'text-danger strong';
        $vars['q'] = str_replace(' ', '+', $this->title . ' ' . $this->interpret);
        $vars['statusClass'] = $this->statusClass;
        return TemplateParser::getInstance()->parseTemplate($vars, 'Song/' . $template . '.html');
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
        $vars['playedButton'] = '';
        $vars['erfolgButton'] = '';
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
        foreach ($post AS $var => $value)
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

    public function toggleWebsiteActive($var)
    {
        switch ($var)
        {
            case 'website':
                $this->website = $this->website == 1 ? 0 : 1;
                $this->website_activ_class = $this->website == 1 ? 'text-success' : 'text-muted';
                $this->website_activ_icon = $this->website == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
                return array('class' => $this->website_activ_class, 'icon' => $this->website_activ_icon);
                break;
            case 'highlight':
                $this->highlight = $this->highlight == 1 ? 0 : 1;
                $this->highlight_activ_class = $this->highlight == 1 ? 'text-success' : 'text-muted';
                $this->highlight_activ_icon = $this->highlight == 1 ? 'fa-thumbs-up' : 'fa-thumbs-o-up';
                return array('class' => $this->highlight_activ_class, 'icon' => $this->highlight_activ_icon);
                break;
            default:
                return $var . $var;
        }
    }

    public function delete()
    {
        $mp3 = PATH . 'files/' . $this->mp3;
        if (file_exists($mp3))
            unlink($mp3);
        $txt = PATH . 'files/' . $this->txt;
        if (file_exists($txt))
            unlink($txt);
        AGDO::getInstance()->Execute("DELETE  FROM SVsongs  WHERE id = " . $this->id);
        $this->clearCache();
        header("location:index.php?idt=Liste&status=1");
    }

    private function clearCache()
    {
        $genres = AGDO::getInstance()->GetAll("SELECT * FROM sv_song_genres");
        foreach ($genres AS $genre)
        {
            $file = $_SERVER['HOME'] . 'sweetvillage/cache/' . $genre['g_link'];
            if (file_exists($file))
                unlink($file);
        }
        $file = $_SERVER['HOME'] . 'sweetvillage/cache/partymusik.html';
        if (file_exists($file))
            unlink($file);
    }

}