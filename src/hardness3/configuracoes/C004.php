<?php
$conf['restricaoIncluirUpdate']['C004_Cnpj'] = <<<'EOT'
	$retorno = rValidarCnpj($valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Nome_Empresa'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Nome_Abreviado'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_D018_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D018_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_D020_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D020_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Inscricao_Estadual'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Matriz'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Logradouro'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Numero'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Bairro'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Cep'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Telefone'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Emite_NF_Caixa'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
	$conf['restricaoIncluirUpdate']['C004_Flag_Emitir_MDFe'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
	$conf['restricaoIncluirUpdate']['C004_Flag_Emitente'] = <<<'EOT'
	if(isset($g['c029Ids'][242])){
		if(!isset($valores['C004_Flag_Emitente']) || empty($valores['C004_Flag_Emitente'])){
			$retorno = rRegx('naoVazio', $valor);
		}
	}
EOT;

//restrição comentada no sistema antigo
// $conf['restricaoIncluirUpdate']['C004_Flag_Decimais_Sugestao_Compra'] = <<<'EOT'
	// $retorno = rRegx('naoVazio', $valor);
// EOT;

$conf['restricaoIncluirUpdate']['C004_Codigo_Regime_Tributario'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Empresa_Simples'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Isento_ICMS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Isento_ISS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Isento_IPI'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_PEDVEN_Botao_Liberar_Separacao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Caminho_Sistema'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Banco_Dados'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Banco_Configuracao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Responsavel_Contato'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Ambiente_NFe'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Natureza_Pessoa_Juridica'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Tipo_Atividade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Incidencia_Tributaria'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Metodo_Apropriacao_Credito'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Tipo_Contribuicao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Flag_Escrituracao_Contribuicao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['C004_Casas_Decimais_Preco'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>