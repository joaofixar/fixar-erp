<?php
namespace hardness;

require_once('bibliotecas/tcpdf/config/lang/eng.php');
require_once('bibliotecas/tcpdf/tcpdf.php');

class MYTCPDF extends \TCPDF
{
    // Page footer
    public $my_footer;

    /**
     * Footer
     */
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        //$this->SetFont('helvetica', '', 8);
        // Aumenta o font-size que vem do editor html (niceditor)
        //echo $this->my_footer;
        $my_footer = preg_replace("/\<font size\=\"([0-9])+\"\>/e", '   "<font size=\"" . ("$1" * 4) . "\">"   ', $this->my_footer);
        
        // $my_footer;
        //die();
        //echo "\n\n\n" . $my_footer;
        // Page number
        //$this->Cell(0, 0, $this->my_footer, 'T', 0, 'C');
        $this->writeHTML($my_footer);
    }
}
