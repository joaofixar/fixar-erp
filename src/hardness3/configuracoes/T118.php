<?php
/**
* camposTipo
* Define qual é o tipo do campo. Com essa informação é feita a formatação dele no FORM, GRID, etc.
*/
$conf['camposTipo']['T118_Data_Vencimento'] = array('data');

/**
* restricaoTravar
* Código PHP para validar se uma tabela pode: ser editada, incluida ou removida
* Para ver variáveis e funções disponíveis dentro do código, veja:
*    http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
 $conf['restricaoTravar']['T118'] = <<<'EOT'
    $retorno = true;
	if ($operacao == 'alterar') {
		$sqlT007 = "SELECT T007_Numero_Protocolo_Nfe, T007_Flag_Travar_Nota FROM T118 LEFT JOIN T007 on T007_Id=T118_T007_Id WHERE T118_Id='{$valorPk}'";
		$resultT007 = mysql_query($sqlT007);
		$linhaT007 = mysql_fetch_array($resultT007);
		if (!empty($linhaT007['T007_Numero_Protocolo_Nfe'])) {
			$retorno = 'Nota já transmitida. Não é possível alterar.';
		} else if ($linhaT007['T007_Flag_Travar_Nota'] == 'S') {
			$retorno = 'Nota já finalizada, para altera-lá utilize o botão "Habilitar edição"';
		}
	} else {
		$sqlT007 = "SELECT T007_Numero_Protocolo_Nfe, T007_Flag_Travar_Nota FROM T118 LEFT JOIN T007 on T007_Id='{$valorPkParent}'";
		$resultT007 = mysql_query($sqlT007);
		$linhaT007 = mysql_fetch_array($resultT007);
		if (!empty($linhaT007['T007_Numero_Protocolo_Nfe'])) {
			$retorno = 'Nota já transmitida. Não é possível alterar.';
		} else if ($linhaT007['T007_Flag_Travar_Nota'] == 'S') {
			$retorno = 'Nota já finalizada, para altera-lá utilize o botão "Habilitar edição"';
		}
	}
EOT;
