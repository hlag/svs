<?php
require_once PATH . 'extern/tcpdf/tcpdf.php';

class gemaPDFgenerator EXTENDS TCPDF
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
        $this->Output();
    }

    private function ueberschrift()
    {
        $this->SetFont('Helvetica', 'B', 18);
        $this->Cell(180, 0, 'Musikfolge: ' . $this->playlist->pl_datum_gen, 0, 1);
    }

    private function bloecke()
    {
        $this->SetFont('Helvetica', '', 10);
        $songs = array();
        foreach (array_keys($this->playlist->bloecke) AS $key)
        {
            foreach ($this->playlist->bloecke[$key]->songs AS $song)
            {
                $songs[$song->title] = $song;
            }
        }
        ksort($songs);
        foreach ($songs AS $song)
        {
            $this->Cell(80, 0, $song->title, 0, 0);
            $this->Cell(60, 0, $song->interpret, 0, 0);
            $this->Cell(20, 0, TimestampConverter::getInstance()->secToMinute($song->duration), 0, 0, 'R');
            $this->ln();
        }
    }
}