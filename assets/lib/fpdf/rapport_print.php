<?php

class PDF extends FPDF {

    public $numQuestPercent = 0;
    public $numFightPercent = 0;

    // Kopfzeile
    function Header()
    {
        $this->SetFont('Arial','B',15);
        $this->Cell(80,10,'Gamification Rapport',1,0,'L');
        $this->SetX(90);
        $this->SetFont('Arial','B',10);
        $this->Cell(55,15,"Quest: " . $this->numQuestPercent . "%",1,0,'C');
        $this->SetX(145);
        $this->Cell(55,15,"Fight: " . $this->numFightPercent . "%",1,0,'C');
        $this->Ln(10);

        $this->SetFont('Arial','B',10);
        $this->Cell(80,5,'Bitte alle Fehler beheben.',1,0,'L');
        $this->Ln(10);
    }

    // Fusszeile
    function Footer()
    {
         // Position 1,5 cm von unten
         $this->SetY(-15);
         // Arial kursiv 8
         $this->SetFont('Arial','I',8);
         // Seitenzahl
         $this->Cell(0,10,'Seite '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

function printRapport($arrQuest, $arrFight, $numQuestPercent, $numFightPercent) {
    // Print PDF
    $pdf=new PDF();
    $pdf->numQuestPercent = $numQuestPercent;
    $pdf->numFightPercent = $numFightPercent;
    $pdf->AliasNbPages();

    // Questing Ausgabe
    if(count($arrQuest) > 0) {
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,10,'Quest Auftrag',0,1);
        foreach($arrQuest as $arrRecord) {
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0, 5, $arrRecord['title'], 1, 1);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(0, 5, $arrRecord['file'], 1, 1);
            $pdf->Cell(0, 5, $arrRecord['message'][1], 1, 1);
            $pdf->Ln(5);
        }
    }

    // Fighting Ausgabe
    if(count($arrFight) > 0) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Boss Fight Auftrag', 0, 1);
        foreach ($arrFight as $arrRecord) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 5, $arrRecord['title'], 1, 1);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(0, 5, $arrRecord['message'][0], 1, 1);
            $pdf->Cell(0, 5, $arrRecord['message'][1], 1, 1);
            $pdf->Ln(5);
        }
    }

    $pdf->Output();
}