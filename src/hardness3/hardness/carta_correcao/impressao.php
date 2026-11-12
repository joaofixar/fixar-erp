<?php

include "../include/sessao.inc";
//--  Conexão com o banco de dados
include "../include/conexao.inc";
// Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

include "../include/pasta_usuario.inc";

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

/**
* SELECT's
*/
$T199_Id = mysql_real_escape_string($T199_Id);
$sql = "SELECT * FROM T199 
		LEFT JOIN T007 ON T007_Id=T199_T007_Id 
		LEFT JOIN D024 ON D024_Id=T007_D024_Id 
		LEFT JOIN T200 ON T200_T199_Id=T199_Id 
		LEFT JOIN C007 ON C007_Id=T199_C007_Id 
		WHERE T199_Id='{$T199_Id}'";
$T199 = mysql_query($sql);
$T199 = mysql_fetch_array($T199);              

$sql = "SELECT D024_E_Mail FROM D024 WHERE D024_Id='{$T199['T007_D024_Id']}'";
$D024 = mysql_query($sql);
$D024 = mysql_fetch_array($D024);    

$sql = "SELECT * FROM D013 WHERE D013_D024_Id='{$T199['D024_Id']}' ORDER BY D013_Id ASC LIMIT 1";
$D013 = mysql_query($sql);
$D013 = mysql_fetch_array($D013);

/**
* PDF
*/
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hardness Sistemas');
// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(true);
// set font
$pdf->SetFont('helvetica', '', 7);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// add a page
$pdf->AddPage();

$html = '';
/**
* PDF - STYLE
*/
$html .= <<<EOT
<style>
	.header .text {
		font-size: 35px;
	}
	.header .text2 {
		font-size: 45px;
		font-weight: bold;
	}
	.titulo {
		background-color: #E1ECF1;
	}
	.titulo_superior {
		font-size: 40px;
	}
	.itens td {
		border: 1px solid black;
	}
	.textIncluso {
		font-size: 28px;
		color: red;
		background-color: #E1ECF1;
	}
	
</style>
EOT;

$sqlC004 = "SELECT * FROM C004 left join D018 on D018_Id=C004_D018_Id left join D020 on D020_Id=C004_D020_Id WHERE C004_Id='{$Empresa_Atual}'";
$resultC004 = mysql_query($sqlC004);
$linhaC004 = mysql_fetch_array($resultC004);
foreach ($linhaC004 as $key => $value) {
	$linhaC004[$key] = utf8_encode($value);
}

/**
* PDF - HEADER
*/
if(!empty($linhaC004['C004_Logo_Marca'])){
	$caminhoImagem = "{$Diretorio_Empresa}/imagens/{$linhaC004['C004_Logo_Marca']}";
	$caminhoImagem = "<img src=\"{$caminhoImagem}\">";
}else{
	$caminhoImagem="";
}
// (TODO) passar para o arq. conf
$caminhoImagem = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
$caminhoImagem = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
if(!empty($T199['D024_Cnpj'])){
	$cpfCnpj = $T199['D024_Cnpj'];
}else{
	$cpfCnpj = $T199['D024_Cpf'];
}
$tel="";
if(!empty($D013['D013_Telefone_1'])){
	$tel = '('.$D013['D013_DDD_Telefone_1'].') '.$D013['D013_Telefone_1'];
}else if(!empty($T199['D024_Telefone_1'])){
	$tel = '('.$T199['D024_DDD_Telefone_1'].') '.$T199['D024_Telefone_1'];
}

$sqlC007 = "SELECT * FROM C007 WHERE C007_Id='{$T199['T199_C007_Id']}'";
$resultC007 = mysql_query($sqlC007);
$linhaC007 = mysql_fetch_array($resultC007);

$documento = (empty($T199['D024_Cnpj']) ? "<b>CPF:</b> {$T199['D024_Cpf']}" : "<b>CNPJ:</b> {$T199['D024_Cnpj']}");
$dataEmissao = troca_data_dma($T199['T007_Data_Emissao']);
$html .=<<<EOT
<table>
	<tr>
		<td align="left">{$caminhoImagem}</td>
		<td align="right" style="font-size: 220%;"></td>
	</tr>
</table>
<br />
<table class="header" width="100%" align="left">
		<tr>
			<td class="titulo_superior"><b>Razão Social:</b> {$linhaC004['C004_Nome_Empresa']}</td>
		</tr>
		<tr>
			<td class="titulo_superior"><b>Fone:</b> ({$linhaC004['C004_DDD_Telefone']}) {$linhaC004['C004_Telefone']}</td>
		</tr>
		<tr>
			<td class="titulo_superior"><b>E-mail:</b> {$linhaC004['C004_E_Mail']}</td>
		</tr>
		<hr>
</table>

<div style="height:20px; width:100%;"></div>
<span align="center" style="font-size: 200%;"><b>CC-e: Carta de Correção Eletrônica</b></span>
<div style="height:20px; width:100%;"></div>

<table cellspacing="0" cellpadding="2">
	<tr>
		<td width="30%" align="left" style="font-size: 30px;">{$documento}</td>
		<td width="70%" align="right" style="font-size: 30px;"><b>Chave de Acesso:</b> {$T199['T007_Chave_Acesso_Nfe']}</td>
	</tr>
	<tr>
		<td width="50%" align="left" style="font-size: 30px;"><b>Data:</b> {$dataEmissao}</td>
		<td width="50%" align="right" style="font-size: 30px;"><b>NF-e:</b> {$T199['T007_Numero_Nota_Fiscal']}</td>
	</tr>
	<tr>
		<td width="50%" align="left" style="font-size: 30px;"><b>Sequencial do evento:</b> {$T199['T199_Sequencial_Evento']}</td>
	</tr>
	<hr/>
</table>
<div style="height:40px; width:100%;"></div>


<div style="height:40px; width:100%;"></div>
<span align="center" style="font-size: 170%;"><b>Informações da Carta de Correção Eletrônica</b></span>
<div style="height:20px; width:100%;"></div>

<table cellspacing="0" cellpadding="2">            
	<tr>
		<td width="60%" align="left" style="font-size: 30px;"><b>Texto da Carta de Correção:<br /></b>{$T199['T199_Correcao']}</td>
	</tr>
	<hr/>
</table>
<div style="height:20px; width:100%;"></div>


<div style="height:40px; width:100%;"></div>
<span align="center" style="font-size: 170%;"><b>Condições de Uso da Carta de Correção Eletrônica</b></span>
<div style="height:20px; width:100%;"></div>

<table cellspacing="0" cellpadding="2">            
	<tr>
		<td width="100%" align="left" style="font-size: 30px;">A Carta de Correcao e disciplinada pelo paragrafo 1o-A do
									art. 7o do Convenio S/N, de 15 de dezembro de 1970 e
									pode ser utilizada para regularizacao de erro ocorrido na
									emissao de documento fiscal, desde que o erro nao esteja
									relacionado com: I - as variaveis que determinam o valor
									do imposto tais como: base de calculo, aliquota, diferenca
									de preco, quantidade, valor da operacao ou da prestacao;
									II - a correcao de dados cadastrais que implique mudanca
									do remetente ou do destinatario; III - a data de emissao ou
									de saida.
		</td>
	</tr>
</table>
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<div style="height:20px; width:100%;"></div>
<span style="text-align:center; font-size: 80%;">Emitido por Hardness Sistemas - www.hardness.com.br</span><br />

		
EOT;

/**
* PDF - GERA!
*/
$pdf->writeHTML($html, true, false, true, false, 'C');
// reset pointer to the last page
$pdf->lastPage();
// get data
$data = $pdf->getPDFData();


// Gravar arquivo no disco

$anexoDir = $Diretorio_Empresa . '/tmp/';
is_dir($anexoDir) or mkdir($anexoDir, 0777, true);

$anexoNome = "{$anexoDir}CCe{$T199['T007_Numero_Protocolo_Nfe']}.pdf";
@unlink($anexoNome);
$h = fopen($anexoNome, 'w');
fwrite($h, $data);
fclose($h);

header('Content-Type: application/pdf');
readfile($anexoNome);