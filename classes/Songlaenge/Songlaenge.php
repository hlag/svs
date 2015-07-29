<?php
require_once PATH.'extern/getID3-1.9.9/getid3/getid3.php';
require_once PATH.'extern/getID3-1.9.9/getid3/module.audio.mp3.php';
class Songlaenge {

    public function getContent()
    {
        //$id3 = new getid3_mp3(new getID3());

        $liste = new Liste();
        $liste->getSongs('all');

        foreach($liste->songs AS $song)
        {
            if($song->mp3 != '' )
            {
                $f = PATH.'files/'.$song->mp3;

                if(file_exists($f))
                {
                    $seconds = $this->getLenght($f);
                    $song->duration = $seconds;
                    $song->saveSong();
                }



               // if(0 < ($seconds = $this->getLenght($song->mp3)))

            }


        }
    }


    function getLenght($file)
    {


        if(file_exists($file))
        {
            $getID3 = new getID3();
            $ThisFileInfo = $getID3->analyze($file);
            return  round($ThisFileInfo['playtime_seconds']);
        }
        else
            return 0;
    }
}