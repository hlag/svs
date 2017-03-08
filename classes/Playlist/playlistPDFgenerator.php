<?php
require_once PATH . 'extern/tcpdf/tcpdf.php';



class playlistPDFgenerator EXTENDS TCPDF
{
    private $playlist;
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
        $this->bloecke();
        $this->stats();
        $this->Output();
    }

    private function ueberschrift()
    {
        //z($this->playlist);
        $this->SetFont('Helvetica', 'B', 18);
        $this->Cell(180, 0, 'Playlist: '.$this->playlist->pl_datum_gen.' '.$this->playlist->name, 0, 1);
    }

    private function bloecke()
    {
       foreach(array_keys($this->playlist->bloecke) AS $key)
       {
           $this->ln(2);
           $this->SetFont('Helvetica', 'B', 11);
           $this->Cell(80, 0, $this->playlist->bloecke[$key]->number.'. '.$this->playlist->bloecke[$key]->pb_name, 0, 0);
           $this->SetFont('Helvetica', '', 8);
           $this->Cell(60, 0, $this->playlist->bloecke[$key]->getUhrzeit(), 0, 0, 'R');
           $this->SetFont('Helvetica', '', 11);
           $this->Cell(20, 0, $this->playlist->bloecke[$key]->getMinutes(), 0, 1, 'R');
           $this->SetFont('Helvetica', '', 10);

           foreach($this->playlist->bloecke[$key]->songs AS $song)
           {
               $this->Cell(80, 0, $song->title, 0, 0);
               $this->Cell(60, 0, $song->instrumentKlaus, 0, 0);
               $this->Cell(20, 0, $song->bpm, 0, 0, 'R');
               $this->ln();
           }

       }
    }

    private function stats()
    {
        $this->ln();
        $this->SetFont('Helvetica', 'B', 14);
        $this->Cell(180, 0, 'Stats', 0, 1);
        $this->SetFont('Helvetica', '', 12);
        $this->Cell(80, 0, 'Spielzeit', 0, 0);

        $this->Cell(80, 0, $this->playlist->getMinutes(), 0, 1, 'R');
        $this->Cell(80, 0, 'Songs', 0, 0);
        $this->Cell(80, 0, $this->playlist->getCountSongs(), 0, 1, 'R');
        $this->Cell(80, 0, 'Ruth', 0, 0);
        $this->Cell(80, 0, $this->playlist->getCountRuth(), 0, 1, 'R');
                $this->Cell(80, 0, 'Klaus', 0, 0);
        $this->Cell(80, 0, $this->playlist->getCountKlaus(), 0, 1, 'R');
                $this->Cell(80, 0, 'Duetts', 0, 0);
        $this->Cell(80, 0, $this->playlist->getCountDuetts(), 0, 1, 'R');
    }
}