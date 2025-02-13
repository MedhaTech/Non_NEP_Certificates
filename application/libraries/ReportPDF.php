<?php
require_once APPPATH . 'libraries/Fpdf.php';

class ReportPDF extends FPDF {
    function Header() {}  // Keep the header empty

    function Footer() {
        $this->SetY(-15); // Move 15mm up from the bottom
        $this->SetFont('Arial', 'I', 8);
        // You can leave it blank or add a hidden text to reserve space
        $this->Cell(0, 10, '', 0, 0, 'C'); 
    }
}
