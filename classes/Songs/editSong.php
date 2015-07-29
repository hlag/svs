<?php
class editSong
{
    private $song;
    public function __construct()
    {
        $this->song = new Song();
        $this->song->getSongByID(Request::getInstance()->getGetRequests('song_id'));
        $this->organizePost(Request::getInstance()->getPostRequests('command'));
    }

    public function getContent()
    {
        $data['id'] = $this->song->id;
        return $this->song->getEdit();
    }

    private function organizePost($command)
    {
        switch ($command)
        {
            case 'deleteSong':
                $this->song->delete();
                break;
            case 'updateSong':
                $this->song->updateSong(Request::getInstance()->getPostRequests());
                break;
            default:
                z(Request::getInstance()->getPostRequests());



        }
    }

}