<?php
/**
 * Tabela D148
 * 
 * Documentação: 
 * 		http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
 */
 
$conf['restricaoIncluirUpdate']['D148_Nome'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_Rua'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_D018_Id'] = <<<'EOT'
 	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_D020_Id'] = <<<'EOT'
 	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_D030_Id'] = <<<'EOT'
 	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_Numero'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_Bairro'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_Flag_Pessoa_Fisica_Juridica'] = <<<'EOT'
 	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_Cnpj'] = <<<'EOT'
	if ($valores['D148_Flag_Pessoa_Fisica_Juridica'] == 'J') {
		$retorno = rRegx('naoVazio', $valor);
	} else {
		$retorno = true;
	}
EOT;
$conf['restricaoIncluirUpdate']['D148_Cpf'] = <<<'EOT'
	if ($valores['D148_Flag_Pessoa_Fisica_Juridica'] == 'F') {
		$retorno = rRegx('naoVazio', $valor);
	} else {
		$retorno = true;
	}
EOT;
$conf['restricaoIncluirUpdate']['D148_Cep'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_DDD_Telefone'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D148_Telefone'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoTravar']['D148'] = <<<'EOT'
	global $g;
	if ($operacao == 'excluir' || $operacao == 'alterar' || $operacao == 'incluir') {
		if($g['c029Ids'][233]){
			$retorno = 'Você não tem permissão para Incluir/Excluir/Alterar endereços';
		}else{
			$retorno = true;
		}
	}
EOT;
