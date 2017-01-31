<?php

/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 31.01.17
 * Time: 15:33
 */
class duplicatePlaylist
{
    public function __construct()
    {
    }

    public function getCopyForm($pl_id)
    {
        $pl = new playlist();
        $pl->getPlaylistByID($pl_id);
        $data['playListNameAlt'] = $pl->getVar('name');
        $data['inputName'] = BootstrapForms::getInstance()->inputText('pl_name', 'Name');
        $data['inputDate'] = BootstrapForms::getInstance()->inputText('pl_datum', 'Datum');
        $data['hiddenOldID'] = BootstrapForms::getInstance()->hidden('old_pl_id', $pl_id);
        $data['hiddenCommand'] = BootstrapForms::getInstance()->hidden('command', 'copyPlaylist');
        return TemplateParser::getInstance()->parseTemplate($data, 'Playlist/copyForm.html', PATH);
    }

    public function copyPlayList($old_pl_id, $pl_name, $pl_datum_lesbar)
    {
        $pl_datum = TimestampConverter::getInstance()->convertLesbarToSQL($pl_datum_lesbar);

        $sql = "INSERT INTO playlists SET pl_name ='" . $pl_name . "', pl_datum ='".$pl_datum."'";
        AGDO::getInstance()->Execute($sql);
        $pl_id = AGDO::getInstance()->Insert_ID();

        $bloecke = AGDO::getInstance()->GetAll("SELECT * FROM playlist_bloecke WHERE pl_id=".$old_pl_id);

        foreach($bloecke AS $block)
        {
            $sql = "INSERT INTO playlist_bloecke SET pl_id=".$pl_id.", pb_name='".$block['pb_name']."', pb_pause='".$block['pb_pause']."', pb_sort_order='".$block['pb_sort_order']."' ";
            AGDO::getInstance()->Execute($sql);
            $pb_id =  AGDO::getInstance()->Insert_ID();

            $songs = AGDO::getInstance()->GetAll("SELECT * ,id, ps_sort_order FROM playlist_songs WHERE pb_id = ".$block['pb_id']);
            foreach($songs AS $song)
            {
                $sql = "INSERT INTO playlist_songs SET pl_id = ".$pl_id.",pb_id =".$pb_id.", id=".$song['id'].",  ps_sort_order=".$song['ps_sort_order'];
                AGDO::getInstance()->Execute($sql);
            }
        }

        header("location:/index.php?idt=playlist&pl_id=".$pl_id);



    }
}