<?php
/**
* camposTipo
* Define qual é o tipo do campo. Com essa informação é feita a formatação dele no FORM, GRID, etc.
*/
$conf['camposTipo']['T090_Data_Vencimento'] = array('data');

/**
* restricaoTravar
* Código PHP para validar se uma tabela pode: ser editada, incluida ou removida
* Para ver variáveis e funções disponíveis dentro do código, veja:
*    http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoTravar']['T090'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = "SELECT T090_Id, T005_Flag_Status FROM T090 LEFT JOIN T005 ON T005_Id=T090_T005_Id WHERE T090_Id = '{$valorPk}' AND (T005_Flag_Status > 0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL AND T005_Flag_Status!=14 AND T005_Flag_Status!=15 AND T005_Flag_Status!=26 AND T005_Flag_Status!=9)";
		$res = mysql_query($sql);
		if (mysql_num_rows($res) > 0) {
			$row = mysql_fetch_array($res);
			$retorno = 'Este pedido já foi faturado.';
		}
		if(($g['nivelAlcada'] == '4' && $row['T005_Flag_Status'] == '3') || $row['T005_Flag_Status'] == '3'){
        	$retorno = true;
        }
	} elseif ($operacao == 'incluir') {
		$sql = "SELECT T005_Id, T005_Flag_Status FROM T005 WHERE T005_Id = '{$valorPkParent}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL AND T005_Flag_Status!=14 AND T005_Flag_Status!=15 AND T005_Flag_Status!=26 AND T005_Flag_Status!=9)";
		$res = mysql_query($sql);
		if (mysql_num_rows($res) > 0) {
			$row = mysql_fetch_array($res);
			$retorno = 'Este pedido já foi faturado.';
		}
		if(($g['nivelAlcada'] == '4' && $row['T005_Flag_Status'] == '3') || $row['T005_Flag_Status'] == '3'){
        	$retorno = true;
        }
	}
EOT;
