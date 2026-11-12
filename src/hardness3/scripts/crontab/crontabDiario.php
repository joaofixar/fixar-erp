<?php
namespace hardness;

include('configuracoesCrontab.php');
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Fechamento diário (Processamento de dados do B.I., verificação de orçamentos e pedidos vencidos...)')",$g['conexaoBancoAuditoria']);
$idCrontab = mysql_insert_id($g['conexaoBancoAuditoria']);
*/
/**
* Associação Venda com Compra
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Associação dos itens de vendas com o histórico de compra do produto')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-associacaoVendaCompra ...\n";
include($local . 'scripts/crontab/scripts-associacaoVendaCompra.php');
echo "Fim scripts-associacaoVendaCompra: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
// /**
// * Reprocessa histórico dos produtos - Sera realizado no crontabSemanal
// */
// mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Reprocessamento do histórico dos produtos')",$g['conexaoBancoAuditoria']);
// $idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
// $time = microtime(true);
// echo "Executando Reprocessa historico ...\n";
// include($local . 'scripts/crontab/scripts-produtosReprocessaHistorico.php');
// echo "Fim scripts-produtosReprocessaHistorico: " . round(microtime(true) - $time) . "\n";
// mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);

/**
* Outros
* 
* Queries e pequenos scripts
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Homologação de fornecedores, reprocessamento do saldo de crédito dos clientes')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-outros ...\n";
include($local . 'scripts/crontab/scripts-outros.php');
echo "Fim scripts-outros: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
* Preço Loja
*
* D009_Preco_Loja_Real
*/
//O PREÇO LOJA AGORA É CALCULADO DENTRO DO REPROCESSA_HISTORICO
/*
$time = microtime(true);
echo "Executando scripts-precoLoja ...\n";
include($local . 'scripts/crontab/scripts-precoLoja.php');
echo "Fim scripts-precoLoja: " . round(microtime(true) - $time) . "\n";
*/

/**
* Emails
*
* Faz a limpeza da tabela de e-mails
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Limpeza da lixeira de e-mails e spams')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-emails ...\n";
include($local . 'scripts/crontab/scripts-emails.php');
echo "Fim scripts-emails: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
* Atualização campo T003_Flag_Status_Orcamento
*
* Este script atualiza o status de todos os orçamentos vencidos (V) - orçamento aberto por mais dias que 'diasMaxOrcamentoPendente'
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Verificação de orçamentos vencidos')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-orcamentoVencido ...\n";
include($local . 'scripts/crontab/scripts-orcamentoVencido.php');
echo "Fim scripts-orcamentoVencido: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
* Atualização campo T003_Flag_Status_Orcamento
*
* Este script atualiza o status de todos os pedidos para vencidos(26) - pedidos aberto por mais dias que 'diasVencPedido'
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Verificação de pedidos vencidos')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-pedidosVencidos ...\n";
include($local . 'scripts/crontab/scripts-pedidosVencidos.php');
echo "Fim scripts-pedidosVencidos: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
* Clientes - Curva ABC
*
* Seta a curva abc para todos os clientes
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Processamento da curva ABC dos clientes')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-clientesCurvaABC ...\n";
include($local . 'scripts/crontab/scripts-clientesCurvaABC.php');
echo "Fim scripts-clientesCurvaABC: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
* Clientes - Classe
*
* Seta a classe para todos os clientes
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Processamento da classe dos clientes')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-clientesProcessaClasse ...\n";
include($local . 'scripts/crontab/scripts-clientesProcessaClasse.php');
echo "Fim scripts-clientesProcessaClasse: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
* Clientes - Processa Carteira
*
* Determina quais clientes serão inativados de acordo com as regras da Empresa 01
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Processamento da carteira de clientes')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-clientesProcessaCarteira ...\n";
include($local . 'scripts/crontab/scripts-clientesProcessaCarteira.php');
echo "Fim scripts-clientesProcessaCarteira: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
* Produto - Processa Carteira (old)
*
* Seta a curva abc para todos os produtos
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Processamento da curva ABC de produtos')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-produtosProcessaCarteira ...\n";
include($local . 'scripts/crontab/scripts-produtosProcessaCarteira.php');
echo "Fim scripts-produtosProcessaCarteira: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
*	Clientes em pré cadastro - Inativa clientes com mais de X dias em pré cadastro
**/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Inativação de clientes em pré cadastro a mais de {$g['C031']['diasMaxClientePreCadastro']} dias')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-clientesPreCadastro ...\n";
include($local . 'scripts/crontab/scripts-clientesPreCadastro.php');
echo "Fim scripts-clientesPreCadastro: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
*	Produtos em pré cadastro - Inativa produtos com mais de X dias em pré cadastro
**/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Inativação de produtos em pré cadastro a mais de {$g['C031']['diasMaxProdutoPreCadastro']} dias')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-produtosPreCadastro ...\n";
include($local . 'scripts/crontab/scripts-produtosPreCadastro.php');
echo "Fim scripts-produtosPreCadastro: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/

/**
*	Processamento da whitelist de email
**/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Atualização da whitelist de e-mails')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando scripts-processarWhitelist ...\n";
include($local . 'scripts/crontab/scripts-processarWhitelist.php');
echo "Fim scripts-processarWhitelist: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);
*/
/**
* Dataware house
*
* Este script limpa e popula o dataware house
*/

// Recontruir a base do B.I.
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Atualizando base de dados do B.I.')");
$idUltimoScript = mysql_insert_id();
$time = microtime(true);
echo "Executando scripts-datawareHouse ...\n";
include($local . 'scripts/crontab/scripts-datawareHouse.php');
echo "Fim scripts-datawareHouse: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'");
/*
// Regerar os grids salvos nos dashboard para cada usuário
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Atualizando relatórios salvos do B.I.')");
$idUltimoScript = mysql_insert_id();
$time = microtime(true);
echo "Executando scripts-datawareHouse cache ...\n";
include($local . 'scripts/crontab/scripts-datawareHouseCache.php');
echo "Fim scripts-datawareHouse cache: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'");
*/
/**
* Tabela R1
*
* Cria da tabela R1 / R3 (mês) / R4 (ano) - necessário por causa dos campos DISTINCT...
*/
/*
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Atualizando tabela de resumos 1')");
$idUltimoScript = mysql_insert_id();
$time = microtime(true);
echo "Executando scripts-tabelaR1 ...\n";
include($local . 'scripts/crontab/scripts-tabelaR1.php');
echo "Fim scripts-tabelaR1: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'");

mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Atualizando tabela de resumos 2')");
$idUltimoScript = mysql_insert_id();
$time = microtime(true);
echo "Executando scripts-tabelaR3 ...\n";
include($local . 'scripts/crontab/scripts-tabelaR3.php');
echo "Fim scripts-tabelaR3: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'");

mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Atualizando tabela de resumos 3')");
$idUltimoScript = mysql_insert_id();
$time = microtime(true);
echo "Executando scripts-tabelaR4 ...\n";
include($local . 'scripts/crontab/scripts-tabelaR4.php');
echo "Fim scripts-tabelaR4: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'");

/**
* Tabela R2
*
* Cria da tabela R2
*/
//$time = microtime(true);
//echo "Executando scripts-tabelaR2 ...\n";
//include($local . 'scripts/crontab/scripts-tabelaR2.php');
//echo "Fim scripts-tabelaR2: " . round(microtime(true) - $time) . "\n";
/*
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idCrontab}'",$g['conexaoBancoAuditoria']);

$h = fopen('/tmp/crontabTempo', 'a');
$time_end = microtime(true);
$time = round($time_end - $time_start);
fwrite($h, "{$dominio}: {$time} segundos\n");
fclose($h);
*/
// die("Fim!\n");
