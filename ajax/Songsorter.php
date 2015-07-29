<?php
class Songsorter
{
    public function __construct()
    {

    }
    public function sortSong($post)
    {
        $id = str_replace('song_', '', $post['song_id']);
        $newPosition = $post['position'];
        $temp_ids = explode('_',$post['parent_id']);




        $pb_id = $temp_ids[1];
        $pl_id = $temp_ids[2];
        $song = new playlistSong();
        $song->getSongByID($id);
        $song->getPosition($pl_id);

        if($pb_id == 'uebrige')
        {
            $song->eraseFromPlaylist();
        }
        else
        {
            $block = new playlist_bloecke();
            $block->getBlockByID($pb_id);
            $block->getSongs();
            $block->resortSongs($newPosition, $id);
        }

        $this->getAndReturnPlayZeiten($pl_id);

    }
    private function getAndReturnPlayZeiten($pl_id)
    {
        $pl = new playlist();
        $pl->getPlaylistByID($pl_id);

        echo json_encode($pl->getDataForJson());
    }
}