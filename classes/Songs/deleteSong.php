<?php

/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 19.01.17
 * Time: 18:08
 */
class deleteSong
{
    private $id;
    private $delTxt = true;
    private $delMp3 = true;
    private $del_song = true;
    private $data = array();

    public function __construct()
    {
        $this->organizePost(Request::getInstance()->getPostRequests('command'));
    }

    public function getContent()
    {
        $this->id = Request::getInstance()->getGetRequests('song_id');
        $this->controlSong();

        $this->data['song_id'] = $this->id;

        return TemplateParser::getInstance()->parseTemplate($this->data, 'Delete/confirmDelete.html');
    }

    private function controlSong()
    {
        $sql = "SELECT * FROM SVsongs  WHERE id = " . $this->id;
        $this->data = AGDO::getInstance()->GetFirst($sql);
        // Text verwendet?
        $sql = "SELECT * FROM SVsongs  WHERE  txt = '" . $this->data['txt'] . "' AND  id != " . $this->id;
        $text = AGDO::getInstance()->GetFirst($sql);
        if (isset($text['txt']))
            $this->delTxt = false;
        // mp3 verwendet?
        $sql = "SELECT * FROM SVsongs  WHERE  mp3 = '" . $this->data['mp3'] . "' AND  id != " . $this->id;
        $mp3 = AGDO::getInstance()->GetFirst($sql);
        if (isset($mp3['mp3']))
            $this->delMp3 = false;
        $sql = "SELECT * FROM playlist_songs WHERE id = " . $this->id;
        $playlists = AGDO::getInstance()->GetALl($sql);
        foreach ($playlists AS $playlist)
            if (isset($playlist['ps_played']) AND $playlist['ps_played'] != 0)
                $this->del_song = false;
        if ($this->del_song)
        {
            $this->data['meldung_sng'] = 'Song wird gelöscht';
            $this->data['class_sng'] = 'danger';
            if ($this->delTxt)
            {
                $this->data['meldung_txt'] = 'text wird gelöscht';
                $this->data['class_txt'] = 'danger';
            }
            else
            {
                $this->data['meldung_txt'] = 'text wird anderweitig verwendet';
                $this->data['class_txt'] = 'info';
            }
            if ($this->delMp3)
            {
                $this->data['meldung_mp3'] = 'mp3 wird gelöscht';
                $this->data['class_mp3'] = 'danger';
            }
            else
            {
                $this->data['meldung_mp3'] = 'mp3 wird anderweitig verwendet';
                $this->data['class_mp3'] = 'info';
            }
        }
        else
        {
            $this->data['meldung_sng'] = 'Song wurde schonmal gespielt';
            $this->data['class_sng'] = 'info';
            $this->data['meldung_txt'] = 'mp3 wird nicht gelöscht';
            $this->data['class_txt'] = 'info';
            $this->data['meldung_mp3'] = 'mp3 wird nicht gelöscht';
            $this->data['class_mp3'] = 'info';
        }
    }

    private function delete()
    {
        $this->controlSong();
        if ($this->delTxt && $this->del_song)
        {
            z(PATH . 'files/' . $this->data['txt']);
            //unlink(PATH . 'files/' . $this->data['txt']);
        }
        if ($this->delMp3 && $this->del_song)
        {
            z(PATH . 'files/' . $this->data['mp3']);
            //unlink(PATH . 'files/' . $this->data['mp3']);
        }
        if ($this->del_song)
        {
            z('DELETE FROM SVsongs');
            //AGDO::getInstance()->Execute("DELETE FROM SVsongs  WHERE id = " . $this->id);
            //AGDO::getInstance()->Execute("DELETE FROM playlist_songs  WHERE id = " . $this->id);
        }
    }

    private function organizePost($command)
    {
        if ($command AND $command  == 'deleteSong')
        {
            z('deleteSong');
            $post = Request::getInstance()->getPostRequests();
            $this->id = $post['song_id'];
            $this->delete();
        }
    }
}