<?php
/**
 * Created by PhpStorm.
 * User: klaus
 * Date: 15.07.15
 * Time: 18:45
 */

class uebrigePDFgenerator EXTENDS TCPDF
{
    private $duration =0;
    private $countSongs =0;
    public function __construct()
    {
        parent::__construct();
        $this->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $this->setFooterData(array(35, 44, 121), array(255, 255, 255));
        $this->SetTopMargin(25);
        $this->SetLeftMargin(25);
        $this->SetRightMargin(30);
        $this->AddPage();
        $this->AcceptPageBreak();
    }
    public function generatePDF($playlist)
    {
        $this->playlist = $playlist;
        $this->ueberschrift();
        $this->genresAndSongs();
        $this->stats();
        $this->Output();
    }

    private function ueberschrift()
    {
        //z($this->playlist);
        $this->SetFont('roboto', 'B', 18);
        $this->Cell(180, 0, 'Übrige Songs: '.$this->playlist->datum.' '.$this->playlist->name, 0, 1);

    }

    private function genresAndSongs()
    {
        foreach(array_keys($this->playlist->uebrigeSongsArray) AS $key)
        {
            $this->ln(3);
            $this->SetFont('roboto', 'B', 12);
            $this->Cell(80, 0, $this->playlist->uebrigeSongsArray[$key]['name'], 0, 0);
            $this->SetFont('roboto', '', 12);

            $this->Cell(80, 0, TimestampConverter::getInstance()->secToMinute($this->playlist->uebrigeSongsArray[$key]['duration']), 0, 1, 'R');

                     foreach($this->playlist->uebrigeSongsArray[$key]['songs'] AS $song)
                     {
                         $this->Cell(80, 0, $song->title, 0, 0);
                         $this->Cell(60, 0, $song->instrumentKlaus, 0, 0);
                         $this->Cell(20, 0, $song->bpm, 0, 0, 'R');
                         $this->ln();

                         $this->countSongs++;
                         $this->duration += $song->getDuration();
                     }


        }
    }

    private function stats()
    {
        $this->ln();
        $this->SetFont('roboto', 'B', 14);
        $this->Cell(180, 0, 'Stats', 0, 1);
        $this->SetFont('roboto', '', 12);
        $this->Cell(80, 0, 'Spielzeit', 0, 0);

        $this->Cell(80, 0, TimestampConverter::getInstance()->secToMinute($this->duration), 0, 1, 'R');
        $this->Cell(80, 0, 'Songs', 0, 0);
        $this->Cell(80, 0, $this->countSongs, 0, 1, 'R');

    }
}