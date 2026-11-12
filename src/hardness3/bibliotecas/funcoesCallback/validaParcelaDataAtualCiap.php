<?php
namespace hardness;

function validaParcelaDataAtualCiap($array) {	

	// Data do banco de dados
	$dataInicio = new \DateTime($array['todosCampos']['T316_Data_Inicio_Apropriacao_Credito']);

	// Número de parcelas
	$numeroParcelas = $array['todosCampos']['T316_Numero_Parcelas_Apropriacao'];
    $baixado        = $array['todosCampos']['T316_Flag_Ativo_Baixado'];
	// Data atual
	$dataAtual      = new \DateTime();

	$parcelaAtual   = (($dataAtual->format('Y') - $dataInicio->format('Y')) * 12) + $dataAtual->format('m') - $dataInicio->format('m');
	if($parcelaAtual > $numeroParcelas || $baixado == 'S' ){
		$parcelaAtual = 'FINALIZADO';
	} else if($parcelaAtual == $numeroParcelas){
		$parcelaAtual = $parcelaAtual . ' (Última)';

    }

	return $parcelaAtual; 
}