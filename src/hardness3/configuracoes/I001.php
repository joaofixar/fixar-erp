<?php
$conf['camposTipo']['I001_Previsao_Analise_Data'] = array('data');
$conf['camposTipo']['I001_Inicio_Analise_Data'] = array('data');
$conf['camposTipo']['I001_Conclusao_Analise_Data'] = array('data');
$conf['camposTipo']['I001_Inicio_Analise_Hora'] = array('tempo');
$conf['camposTipo']['I001_Conclusao_Analise_Hora'] = array('tempo');

/**
* camposNaoUsar
* Lista de campos que não serão utilizados. (No FORM, GRID, etc.)
*/
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
	'I001_Previsao_Analise_Data'
));

/**
* camposValorPadrao
* Defina valores padrão para campos no formulário de inclusão.
*/
$conf['camposValorPadrao']['I001_Prioridade'] = 10;
?>