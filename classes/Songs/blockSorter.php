<?php
/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 16.07.15
 * Time: 12:22
 */

class blockSorter {
    private $bloecke;
    private $g;
    public function sortBloecke($get)
    {
        $this->g = $get;
        $this->getBlocke($get['pl_id']);
    }

    private function getBlocke($pl_id)
    {
        $bloecke = AGDO::getInstance()->GetAll("SELECT * FROM playlist_bloecke WHERE pl_id = ".$pl_id." ORDER BY pb_sort_order");
        $countbloecke = count($bloecke);
        $newPosition =  $this->g['nr'] + $this->g['change'];
        if($newPosition < 1)
            $newPosition = 1;
        if($newPosition > $countbloecke)
            $newPosition = $countbloecke;
        $x=1;
        foreach($bloecke AS $block)
        {
            $b = new playlist_bloecke();
            $b->setBlock($block);
            if($b->pb_id == $this->g['pb_id'])
            {
                $b->pb_sort_order = $newPosition;
                $b->saveBlock();
            }
            else
            {
                $this->bloecke[$x++] = $b;
            }

        }

        for($sortorder = 1;$sortorder < $countbloecke; $sortorder++)
        {
            if($sortorder == $newPosition)
            {
            }
            elseif($sortorder > $newPosition)
            {
                $block = $this->bloecke[$sortorder-1];
                $block->pb_sort_order = $sortorder;
                $block->saveBlock();

            }
            else
            {
                $block = $this->bloecke[$sortorder];
                $block->pb_sort_order = $sortorder;
                $block->saveBlock();
            }

        }

    }

}