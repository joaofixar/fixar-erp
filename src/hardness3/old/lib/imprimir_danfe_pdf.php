<?php
/*
  Programador..............: Evantoni Fuchs
  Data Criacao.............: 16/04/2010
*/

    include "../class/danfe_pdf.php";
    $mArq=explode(".",$mT007[C004_Logo_Marca]);
    $nExt=count($mArq)-1;
    $cExtensao_Arquivo=$mArq[$nExt];

	$oDanfe->setT007_Id($mT007[T007_Id]);
	$oDanfe->setLinhasPrimeiraPagina(32);
	$oDanfe->setLinhasSegundaPagina(100);
	$oDanfe->setNomeArquivoPDF("danfe.pdf");
	$oDanfe->setSaidaArquivo("I");
	$oDanfe->canhoto   = true;
	$oDanfe->casas_decimais_preco=$mT007[T007_Casas_Decimais_Preco];
	$oDanfe->casas_decimais_quantidade=$mT007[T007_Casas_Decimais_Quantidade];
	$oDanfe->issqn     = false;
	$oDanfe->pdf       = new PDF_Code128("P", 'mm', 'A4');
	$oDanfe->logomarca = "$Diretorio_Imagens/$Empresa_Atual"."_nfe.$cExtensao_Arquivo";
	$oDanfe->gera();

?>
