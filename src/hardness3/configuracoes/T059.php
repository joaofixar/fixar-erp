<?php
/**
* restricaoIncluirUpdate
* 
* Código PHP para validar um campo de uma tabela na hora de Incluir ou Atualizar.
* Para ver variáveis e funções disponíveis dentro do código, veja:
* 		http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoIncluirUpdate']['T059_D009_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

/**
* camposTipo
*/
$conf['camposTipo']['T059_Quantidade'] = array('numero', 2);

/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T059']['deleteBefore'] = <<<'EOT'
	mysqli_query("DELETE FROM T174 WHERE T174_T059_Id='{$chaveValor}'");
EOT;

$conf['triggers']['T059']['updateAfter'] = <<<'EOT'
	$T059=mysqli_query("select * from T059 where T059_Id='$chaveValor'");
	$mT059=mysql_fetch_array($T059);
	$quantidade=$mT059['T059_Quantidade'];
	$T174=mysqli_query("select * from T174 where T174_T059_Id='$chaveValor'");
	while ($mT174=mysql_fetch_assoc($T174)){					
		mysql_query("UPDATE T174 SET T174_Quantidade=$quantidade WHERE T174_Id='{$mT174['T174_Id']}'");		
	}	
EOT;

/**
* restricaoTravar
* Código PHP para validar se uma tabela pode: ser editada, incluida ou removida
* Para ver variáveis e funções disponíveis dentro do código, veja:
*    http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoTravar']['T059'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir' || $operacao == 'incluir') {
		//$valorPkParent
		if(!empty($valorPk)){
			$sqlT059 = "SELECT T059_T010_Id FROM T059 WHERE T059_Id = '{$valorPk}'";
			$resultT059 = mysql_query($sqlT059);
			$linhaT059 = mysql_fetch_array($resultT059);
			
			// Verificando se já foi gerado uma ordem de compra para aquela cotação
			$sqlT173 = "SELECT * FROM T173 WHERE T173_T010_Id='{$linhaT059['T059_T010_Id']}'";
			$resultT173 = mysql_query($sqlT173);
			while($linhaT173 = mysql_fetch_array($resultT173)){
				$sqlT011 = "SELECT * FROM T011 WHERE T011_T010_Id='{$linhaT173['T173_T010_Id']}' AND T011_D024_Id='{$linhaT173['T173_D024_Id']}'";
				$resultT011 = mysql_query($sqlT011);
				$totalT011 = mysql_num_rows($resultT011);
				$linhaT011 = mysql_fetch_array($resultT011);
				if($totalT011 > 0){
					$retorno = "Já foi gerada uma OC para esta cotação. Não é possível alterar";
				}
			}
			
			// Verificando se a cotação já foi finalizada
			//$sqlT010 = "SELECT T010_Flag_Cotacao_Finalizada FROM T010 WHERE T010_Id='{$linhaT059['T059_T010_Id']}'";
//			$resultT010 = mysql_query($sqlT010);
//			$linhaT010 = mysql_fetch_array($resultT010);
//			if ($linhaT010['T010_Flag_Cotacao_Finalizada'] == 'S') {
//				$retorno = 'Cotação Finalizada.';
//			}
		} else {
			//$sqlT010 = "SELECT T010_Flag_Cotacao_Finalizada FROM T010 WHERE T010_Id='{$valorPkParent}'";
//			$resultT010 = mysql_query($sqlT010);
//			$linhaT010 = mysql_fetch_array($resultT010);
//			if ($linhaT010['T010_Flag_Cotacao_Finalizada'] == 'S') {
//				$retorno = 'Cotação Finalizada.';
//			}
		}
	} else {
		$retorno = true;
	}
EOT;
