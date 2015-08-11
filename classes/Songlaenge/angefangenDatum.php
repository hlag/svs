<?php

class angefangenDatum {

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
                    z($f);
                    $datum = date("Y-m-d", filemtime($f));
                    z($datum);
                    $song->__set('angefangen', $datum);
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