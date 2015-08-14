<?php
class upload
{
    public function __construct()
    {
        $this->organizePost(Request::getInstance()->getPostRequests('command'));
    }

    public function getContent()
    {
        return TemplateParser::getInstance()->parseTemplate(array(), 'Upload/songUpload.html');
    }

    private function organizePost($command)
    {
        if($command)
        {
            $P = Request::getInstance()->getPostRequests();
            switch($command)
            {
                case 'saveSongNotiz':
                    $song = new Song();
                    $song->title  =$P['title'];
                    $song->interpret  =$P['interpret'];
                    $song->setKennung();
                    $song->saveSong();
                    header("location:index.php?idt=editSong&song_id=".$song->getID());
                    break;

            }
        }
    }
}