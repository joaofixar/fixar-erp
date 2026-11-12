<?php
namespace hardness;
$cAno_Mes_NFe  = substr($rowT224['T007_Data_Emissao'], 0, 7);
$Diretorio_NFe = $g['pathDadosAntigo'] . 'nfe';
$cArquivo      = "$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/" . $rowT224['T007_Numero_Protocolo_Nfe'] . ".xml";
echo "Arquivo XML para importar: " . $cArquivo . "\n";

$g['empresaAtual'] = $rowT224['C004_Id'];

$EST004 = new \hardness\EST004();
$arquivo = explode('/', $cArquivo);
$arquivo = array(
	'name' => $arquivo[count($arquivo)-1],
	'tmp_name' => $cArquivo,
	'interno' => true
);

$nT013_Id = false;
$retorno = $EST004->ImportarXML($arquivo, 'on', true);

echo "Retorno EST004->ImportarXML: " . $retorno . "\n";
echo "T013_Id : {$nT013_Id}\n";

if ($nT013_Id) {
	// Pula a verificação de bater a OC com a Nota
	$sql = "UPDATE T014 SET T014_Flag_Divergencia = 'N' WHERE T014_T013_Id = '{$nT013_Id}'";
	mysqli_query($sql);
	if (mysql_error()) {
		echo 'Erro: ' . mysql_error() . "\n";
	}

	$retorno = $EST004->Calcular($nT013_Id);
	echo "Retorno EST004->Calcular: " . $retorno . "\n";

	$retorno = $EST004->finalizarNotaCompras($nT013_Id);
	echo "Retorno EST004->finalizarNotaCompras: " . $retorno . "\n";

	// T225_Flag_Autorizado
}
