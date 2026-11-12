<?php
$conf['camposTipo']['I002_Previsao_OS_Data'] = array('data');
$conf['camposTipo']['I002_Inicio_OS_Data'] = array('data');
$conf['camposTipo']['I002_Conclusao_OS_Data'] = array('data');
$conf['camposTipo']['I002_Inicio_OS_Hora'] = array('tempo');
$conf['camposTipo']['I002_Conclusao_OS_Hora'] = array('tempo');

/**
* camposNaoUsar
* Lista de campos que não serão utilizados. (No FORM, GRID, etc.)
*/
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
	'I002_Previsao_OS_Data'
));

/**
* camposValorPadrao
* Defina valores padrão para campos no formulário de inclusão.
*/
$conf['camposValorPadrao']['I002_Prioridade'] = 10;
?>