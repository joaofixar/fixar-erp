<?php
namespace hardness;
/*
Integração com o e-commerce da WebStorm

http://tudoe4br.acessotemp.net/admin/
tudoesportes
tudo@esportes2013
http://tudoe4br.acessotemp.net/webservice/importacao.wsdl
http://tudoe4br.acessotemp.net/webservice/exportacao.wsdl

http://www.tudoesportes.com.br/webservice/importacao.wsdl
login: hardness
senha: @hardness1010
*/

// Inicio: necessário para emular as variáveis de framework (utilizar personalizações, globais, etc)
set_time_limit(0);
$time_start = microtime(true);
$g = array();
$g['usuarioAtual'] = 63;
$g['empresaAtual'] = 1;
$g['time'] = array();
$g['time']['sql'] = '';
$_SERVER['REMOTE_ADDR'] = '';
// Verificações
$argv = $_SERVER['argv'];
if (!isset($argv[1]) || empty($argv[1])) {
	echo "Faltou o parametro com nome da empresa a ser executado!\nExemplo: fixar\n";
	die();
}
$dominio = $argv[1];
$conf = "/var/www/sites/{$dominio}.hardness.com.br-confUsuario.php";
$local = '/var/www/html/hardness3/';
if ($dominio == 'flycker') {
	$conf = "/home/flycker/html/hardness3/bibliotecas/confUsuario.php";
	$local = '/home/flycker/html/hardness3/';
}
if ($dominio == 'tudo') {
	$conf = "/var/www/sites/tudo.hardness.com.br-confUsuario.php";
	$local = '/var/www/html';
}
if (!file_exists($conf)) {
	echo "Empresa não encontrada!\n";
	die();
}
// Carrega as bibliotecas necessárias...
require_once($conf);
$home = $confUsuario['pathRaiz'];
require_once($home . 'bibliotecas/conexaoBanco.php');
require_once($home . 'sistema/confSistema.php');
require_once($home . 'bibliotecas/funcoesGerais.php');
$g['conf'] = new confSistema();
$g['pathRaiz'] = $confUsuario['pathRaiz'];
$g['urlRaiz'] = $confUsuario['urlRaiz'];
chdir($g['pathRaiz']);
// Caminhos
// dados_usuarios/{DB}/
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
$g['pathDadosUsuario'] = $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6-strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
$g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDados'] . '/';
$g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDadosUsuario'] . '/';
$g['pathDados'] = $home . $g['pathDados'];
$g['pathDadosUsuario'] = $home . $g['pathDadosUsuario'];
// Classes
require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();

// Conexão com o webservices
$client = new \SoapClient("http://www.tudoesportes.com.br/webservice/exportacao.wsdl", array('login' => 'hardness', 'password' => '@hardness1010'));
//$client = new \SoapClient("http://www.tudoesportes.com.br/webservice/exportacao.wsdl", array('login' => 'tudoesportes', 'password' => 'tudo@esportes2013'));
// Requisitar lista de pedidos
//$dataInicial = date("Y-m-d 00:00:00", strtotime("-1 month", time()));
$dataInicial = date("Y-m-d 00:00:00");
$dataFinal = date("Y-m-d 23:59:59");
$xml = <<<EOT
<ws_integracao xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<acao>select</acao>
	<modulo>pedido</modulo>
	<filtro>
	<dataInicial>{$dataInicial}</dataInicial>
	<dataFinal>{$dataFinal}</dataFinal>
	</filtro>
</ws_integracao>
EOT;
$resposta = $client->pedidoSelect($xml);
file_put_contents('/tmp/pedidos.xml', serialize($resposta));
//$resposta = unserialize(file_get_contents('/tmp/pedidos.xml'));

// Carrega o XML
$dom = new \DomDocument();
$dom->loadXML($resposta);

// Cache clientes (porque o webservice não permite muitas requisições individuais)
$filtroCliente = '';
$array = array();
foreach ($dom->getElementsByTagName('pedido') as $key => $pedido) {
	$cliente = $pedido->getElementsByTagName('cliente')->item(0);
	if (!in_array($cliente->getElementsByTagName('idCliente')->item(0)->nodeValue, $array)) {
		$filtroCliente .= "<filtro><idCliente>{$cliente->getElementsByTagName('idCliente')->item(0)->nodeValue}</idCliente></filtro>\n";
		$array[] = $cliente->getElementsByTagName('idCliente')->item(0)->nodeValue;
	}
}
$xml = <<<EOT
<ws_integracao xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<acao>select</acao>
	<modulo>cliente</modulo>
	{$filtroCliente}
</ws_integracao>
EOT;
$resposta2 = $client->clienteSelect($xml);
file_put_contents('/tmp/clientes.xml', serialize($resposta2));
//$resposta2 = unserialize(file_get_contents('/tmp/pedidos.xml'));
$clientes = new \DomDocument();
$clientes->loadXML($resposta2);

// Loop nos pedidos
$total_pedidos = 0;
foreach ($dom->getElementsByTagName('pedido') as $key => $pedido) {
	mysqli_query("BEGIN");
	
	$idPedido = $pedido->getElementsByTagName('idPedido')->item(0)->nodeValue;
	$dataPedido = strtotime($pedido->getElementsByTagName('dataPedido')->item(0)->nodeValue);
	echo "\n-----------------------------------------------------------------------\nID Pedido: {$idPedido} - " . date('d/m/Y', $dataPedido) . " ...\n";

	// Entrega (pegar do lugar diferente)
	// Cliente
	$cliente = $pedido->getElementsByTagName('cliente')->item(0);
	$entrega = $pedido->getElementsByTagName('entrega')->item(0);
	$cpjfCnpj = $cliente->getElementsByTagName('cpf_cnpj')->item(0)->nodeValue;
	echo "\tProcurando cliente CPF: {$cpjfCnpj} ...\n";
	$idCliente = clienteProcura($cpjfCnpj);

	// Pedido
	$descontoTotal = str_replace(',','.', $pedido->getElementsByTagName('desconto')->item(0)->nodeValue);
	$observacao_t005 = '';
	$frete = str_replace(',','.',$pedido->getElementsByTagName('frete')->item(0)->nodeValue);
	$valor_total_produtos=0;

	// Loop produtos
	echo "\tProdutos...\n";
	$produtos = $pedido->getElementsByTagName('produto');
	foreach($produtos AS $produto)
	{
		$idProduto = $produto->getElementsByTagName('idProduto')->item(0)->nodeValue;
		echo "\t\t{$idProduto} ...\n";
		$valor_total_produtos += str_replace(',','.',$produto->getElementsByTagName('quantidade')->item(0)->nodeValue * str_replace(',','.',$produto->getElementsByTagName('valorUnitario')->item(0)->nodeValue));
	}

	/* VERIFICA QUAL CFOP VAI SER UZADA */
	$sql = mysql_query("
    					SELECT
                        	C004_Id,
							D018_UF,
							C004_CFOP_Venda_Dentro_Estado,
							C004_CFOP_Venda_Fora_Estado
                        FROM C004
							LEFT JOIN D018 ON D018_Id=C004_D018_Id
                        WHERE
							C004_Id='".$g['empresaAtual']."'
		  ");  	
	$mC004 = mysql_fetch_assoc($sql);
	$sql2 = mysql_query("
    					SELECT
                        	D024_Id,
							D018_UF
                        FROM D024
							LEFT JOIN D018 ON D018_Id=D024_D018_Id
                        WHERE
							D024_Id='".$idCliente."'
				  ");
	$mD024 = mysql_fetch_assoc($sql2);
	if( $mC004['D018_UF'] == $mD024['D018_UF']) // Colocado FIXO por estar com problemas, verificar!!!
	{
    	$CFOP = $mC004['C004_CFOP_Venda_Dentro_Estado'];
	}
	else
	{
    	$CFOP = $mC004['C004_CFOP_Venda_Fora_Estado'];
	}
	/* FIM CFOP */

	// Adiciona entereço de entrega nas observações
	$Entrega_Rua = utf8_decode($entrega->getElementsByTagName('logradouro')->item(0)->nodeValue) . ', ' . $entrega->getElementsByTagName('numero')->item(0)->nodeValue;
	$Entrega_Complemento = utf8_decode($entrega->getElementsByTagName('complemento')->item(0)->nodeValue);
	$Entrega_Rua = $Entrega_Complemento ? $Entrega_Rua . ' - ' . $Entrega_Complemento : $Entrega_Rua;
	$Entrega_Bairro = utf8_decode($entrega->getElementsByTagName('bairro')->item(0)->nodeValue);
	$cidade = $entrega->getElementsByTagName('cidade')->item(0)->nodeValue;
	$estado = $entrega->getElementsByTagName('estado')->item(0)->nodeValue;
	$Entrega_Cep = utf8_decode($entrega->getElementsByTagName('cep')->item(0)->nodeValue);
	$observacao_t005 = <<<EOT
Endereço de Entrega:;
{$Entrega_Rua};
{$Entrega_Bairro} - {$cidade} - {$estado};
CEP: {$Entrega_Cep};
EOT;
	
	/* Array a ser passado para criar o scabecalho do pedido */
	$to_cabecalho = array(
          'idCliente'=>$idCliente,
          'idPedido'=>trim($idPedido),
          'dataPedido'=>$dataPedido,
          'desconto'=>$descontoTotal,
          'frete'=>$frete,
          'valor_total_produtos'=>$valor_total_produtos,
          'CFOP'=>$CFOP,
          'observacao'=>$observacao_t005
    );
    
    /* Verifica se o cabecalho com o numero do pedido vindo do ecommerce ja existe */
	/* Se nao existir cria e retorna o ID do T005, senao apenas retorna o ID do T005 */
	$sql = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Numero_Pedido_Venda='".$to_cabecalho['idPedido']."'");
	if(mysql_num_rows($sql)==0)
	{
		$idCabecalho = cria_cabecalho_pedido($to_cabecalho);
		$item_numero=0;
		foreach( $produtos AS $produto )
		{
        	$item = array(
				'idProduto'=>$produto->getElementsByTagName('idProduto')->item(0)->nodeValue,
				'idPedido'=>$idPedido,
				'codigo'=>$produto->getElementsByTagName('codigo')->item(0)->nodeValue,
				/* Retirado a descricao, para caso seja um KIT, coloca uma */
                //'descricao'=>$produto->getElementsByTagName( $export['pedido']['descricao'] )->item(0)->nodeValue,
                'descricao'=>'',
                'quantidade'=>str_replace(',','.',$produto->getElementsByTagName('quantidade')->item(0)->nodeValue),
				'valorUnitario'=>str_replace(',','.',$produto->getElementsByTagName('valorUnitario')->item(0)->nodeValue),
				'idCabecalho'=>$idCabecalho,
				'idKit'=>'',
				'type'=>'NORMAL',
				'CFOP'=>$CFOP
				);
            $item_numero++;
            if (!add_produto_pedido($item,$item_numero)) {
            	echo "\n*** ABORTADO! - Não foi possível adicionar um produto ao pedido. ***\n";
            	mysql_query("ROLLBACK");
            	continue 2;
            }
            
		}
		
		atualizarAliquotas($idCabecalho); 
		$total_pedidos++;

        if (!is_dir("{$g['pathDados']}/ecommerce/xmlPedido/")) {
            mkdir("{$g['pathDados']}/ecommerce/xmlPedido/", 0777,true);
        }

        $cArquivoSalvar = "{$g['pathDados']}/ecommerce/xmlPedido/PEDIDO_{$idCabecalho}.xml";

        $fp = fopen($cArquivoSalvar, "w");
        if (!@fwrite($fp,$resposta))
        { 
            mysql_query("ROLLBACK");
            echo "Não foi possível continuar, o arquivo:" . $cArquivoSalvar . "Não pode ser salvo. Verique as permissões na pasta do arquivo.\n";
            continue;
        }
	} else {
		echo "Pedido ja foi processado!\n-------------------------------------------------------------\n";
		continue;
	}

 	/* SO ENTRA NO ELSE SE O PEDIDO JA EXISTIR */
    // echo "<br>O pedido ja existe";
    // file_put_contents('/tmp/SIS_ERRO_'.$idPedido.'_PedidoExistente', $xml);
	
	/* Atualizando aliquotas */
		echo "\tAtualizando aliquotas VEN002 ...\n";
		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();
		$retorno = $VEN002->atualizarAliquotasPedidos($idCabecalho);

	if ($retorno === true) {
		echo "\t\tAtualizado!\n";
	} else {
		echo "\t\tErro: {$retorno}\n";
	}
	/* Fim atualizar aliquotas */
    calculaDesconto($idCabecalho);
    mysql_query("COMMIT");

    // Atualiza frete apenas no final para não contar no total e desconto
    $sql = "UPDATE T005 SET T005_Valor_Frete = '{$to_cabecalho['frete']}' WHERE T005_Id = '{$idCabecalho}'";
    mysql_query($sql);

	echo "Finalizado com sucesso!\n-------------------------------------------------------------\n";
}

/*
* Funções
*/
function clienteProcura($cpf_cnpj){
	$cpf = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
	$cpf = substr( $cpf, 0, 3 ).".".substr( $cpf, 3, 3 ).".".substr( $cpf, 6, 3 )."-".substr( $cpf, 9, 2 );
	return clienteCadastra($cpf);
}

function clienteCadastra($cpf)
{
	global $g, $client, $cliente, $clientes, $entrega;

	// consulta cliente no webservice
	/*
	$xml = <<<EOT
<ws_integracao xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<acao>select</acao>
	<modulo>cliente</modulo>
	<filtro>
		<idCliente>{$cliente->getElementsByTagName('idCliente')->item(0)->nodeValue}</idCliente>
	</filtro>
</ws_integracao>
EOT;
	$resposta = $client->clienteSelect($xml);
	file_put_contents('/tmp/cliente-' . $cpf . '.xml', serialize($resposta));
	//$resposta = unserialize(file_get_contents('/tmp/cliente-' . $cpf . '.xml'));

	$dom = new \DomDocument();
	$dom->loadXML($resposta);
	*/
	$dom = false;
	foreach ($clientes->getElementsByTagName('cliente') as $key => $clienteTmp) {
		if ($clienteTmp->getElementsByTagName('idCliente')->item(0)->nodeValue == $cliente->getElementsByTagName('idCliente')->item(0)->nodeValue) {
			$dom = $clienteTmp;
		}
	}
	if (!$dom) {
		echo "\t\tErro encontrando cliente no webservice!\n";
		die();
	}

	/*
	* Dados do cliente
	*/
	$acao = 'I';
	$dadosCliente = array();
	$sql = mysql_query("SELECT * FROM D024 WHERE D024_Cpf='{$cpf}'");
	if(mysql_num_rows($sql) != 0) {
		$acao = 'U';
		$dadosClienteAntigo = mysql_fetch_assoc($sql);
	}

	$dadosCliente['D024_Flag_Pessoa_Fisica_Juridica'] = 'F';
	$dadosCliente['D024_Nome_Empresa'] = strtoupper(utf8_decode($dom->getElementsByTagName('nome')->item(0)->nodeValue));
	$dadosCliente['D024_Nome_Fantasia'] = strtoupper(utf8_decode($dom->getElementsByTagName('nome')->item(0)->nodeValue));
	$dadosCliente['D024_Flag_Contribuinte'] = '1';
	$dadosCliente['D024_Flag_Restricao'] = 'N';
	$dadosCliente['D024_Flag_Liberado'] = 'S';
	$dadosCliente['D024_Cnpj'] = '';
	$dadosCliente['D024_Cpf'] = $cpf;
	$dadosCliente['D024_Rg'] = '';
	$dadosCliente['D024_Inscricao_Estadual'] = '';
	$dadosCliente['D024_Endereco'] = strtoupper(utf8_decode($dom->getElementsByTagName('endereco')->item(0)->nodeValue));
	$dadosCliente['D024_Numero'] = $dom->getElementsByTagName('numero')->item(0)->nodeValue;
	$dadosCliente['D024_Complemento'] = strtoupper(utf8_decode($dom->getElementsByTagName('complemento')->item(0)->nodeValue));
	$dadosCliente['D024_Bairro'] = strtoupper(utf8_decode($dom->getElementsByTagName('bairro')->item(0)->nodeValue));
	$dadosCliente['D024_Cep'] = strtoupper(utf8_decode($dom->getElementsByTagName('cep')->item(0)->nodeValue));
	$dadosCliente['D024_Entrega_Rua'] = strtoupper(utf8_decode($entrega->getElementsByTagName('logradouro')->item(0)->nodeValue)) . ' - ' . $entrega->getElementsByTagName('numero')->item(0)->nodeValue;
	$dadosCliente['D024_Entrega_Complemento'] = strtoupper(utf8_decode($entrega->getElementsByTagName('complemento')->item(0)->nodeValue));
	$dadosCliente['D024_Entrega_Bairro'] = strtoupper(utf8_decode($entrega->getElementsByTagName('bairro')->item(0)->nodeValue));
	$dadosCliente['D024_Entrega_Cep'] = strtoupper(utf8_decode($entrega->getElementsByTagName('cep')->item(0)->nodeValue));
	$dadosCliente['D024_Observacoes'] = strtoupper(utf8_decode($dom->getElementsByTagName('endereco')->item(0)->nodeValue)) . " Cidade: ".utf8_decode($dom->getElementsByTagName('cidade')->item(0)->nodeValue) . " Região: " . utf8_decode($dom->getElementsByTagName('estado')->item(0)->nodeValue);
	$dadosCliente['D024_C004_Id'] = $g['empresaAtual'];
	// Cidade / Estado / Pais
	$cidade = $dom->getElementsByTagName('cidade')->item(0)->nodeValue;
	$estado = $dom->getElementsByTagName('estado')->item(0)->nodeValue;
	$sql = mysqli_query("SELECT D018_Id FROM d018 WHERE D018_UF LIKE '%".utf8_decode($estado)."%'");
	$open = mysql_fetch_assoc($sql);
	mysql_free_result($sql);
	$estado = $open['D018_Id'];
	unset($open);
	$sql = mysqli_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado}' AND D020_Nome_Cidade='".utf8_decode($cidade)."'");
	$open = mysql_fetch_assoc($sql);
	mysql_free_result($sql);
	$cidade = $open['D020_Id'];
	unset($open);
	$sql = mysqli_query("SELECT d030_Id FROM d030 WHERE D030_Nome_Pais='Brasil'");
	$open = mysql_fetch_assoc($sql);
	mysql_free_result($sql);		
	$pais = $open['d030_Id'];
	unset($open);
	$dadosCliente['D024_D020_Id'] = $cidade;
	$dadosCliente['D024_D018_Id'] = $estado;
	$dadosCliente['D024_D030_Id'] = $pais;
	$cidade = $entrega->getElementsByTagName('cidade')->item(0)->nodeValue;
	$estado = $entrega->getElementsByTagName('estado')->item(0)->nodeValue;
	$sql = mysqli_query("SELECT D018_Id FROM d018 WHERE D018_UF LIKE '%".utf8_decode($estado)."%'");
	$open = mysql_fetch_assoc($sql);
	mysql_free_result($sql);
	$estado = $open['D018_Id'];
	unset($open);
	$sql = mysqli_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado}' AND D020_Nome_Cidade='".utf8_decode($cidade)."'");
	$open = mysql_fetch_assoc($sql);
	mysql_free_result($sql);
	$cidade = $open['D020_Id'];
	unset($open);
	$sql = mysqli_query("SELECT d030_Id FROM d030 WHERE D030_Nome_Pais='Brasil'");
	$open = mysql_fetch_assoc($sql);
	mysql_free_result($sql);		
	$pais = $open['d030_Id'];
	unset($open);
	$dadosCliente['D024_D020_Entrega'] = $cidade;
	$dadosCliente['D024_D018_Entrega'] = $estado;
	$dadosCliente['D024_D030_Entrega'] = $pais;
	//
	$dadosCliente['D024_Flag_Lista_Preco'] = '1';
	$dadosCliente['D024_Flag_Cliente'] = 'S';
	$dadosCliente['D024_Flag_Fornecedor'] = 'N';
	$dadosCliente['D024_Flag_Revenda_Consumidor'] = '1';
	$dadosCliente['D024_Flag_Pre_Cadastro'] = 'N';
	$dadosCliente['D024_Data_Cadastro'] = date('Y-m-d H:i:s');
	// Telefone
	$telefone = $dom->getElementsByTagName('telefone1')->item(0)->nodeValue;
	$telefone = str_replace('(', ' ', $telefone);
	$telefone = str_replace(')', ' ', $telefone);
	$telefone = trim($telefone);
	$ddd = substr($telefone, 0, 2);
	$telefone = substr($telefone, 2);
	$telefone = str_replace("-", "", $telefone);
	$dadosCliente['D024_DDD_Telefone_1'] = $ddd;
	$dadosCliente['D024_Telefone_1'] = trim($telefone);
	$telefone = $entrega->getElementsByTagName('telefone')->item(0)->nodeValue;
	$telefone = str_replace('(', ' ', $telefone);
	$telefone = str_replace(')', ' ', $telefone);
	$telefone = trim($telefone);
	$ddd = substr($telefone, 0, 2);
	$telefone = substr($telefone, 2);
	$telefone = str_replace("-", "", $telefone);
	$dadosCliente['D024_DDD_Entrega_Telefone'] = $ddd;
	$dadosCliente['D024_Entrega_Telefone'] = $telefone;
	// Telefone 2
	$telefone2 = $dom->getElementsByTagName('telefone2')->item(0)->nodeValue;
	$telefone2 = str_replace('(', ' ', $telefone2);
	$telefone2 = str_replace(')', ' ', $telefone2);
	$telefone2 = trim($telefone2);
	$ddd2 = substr($telefone2, 0, 2);
	$telefone2 = substr($telefone2, 2);
	$telefone2 = str_replace("-", "", $telefone2);
	$dadosCliente['D024_DDD_Telefone_2'] = $ddd2;
	$dadosCliente['D024_Telefone_2'] = $telefone2;
	//
	$dadosCliente['D024_E_Mail'] = strtoupper(utf8_decode($dom->getElementsByTagName('email')->item(0)->nodeValue));

	// Ações
	if ($acao == 'I') {
		$campos = '';
		$valores = '';
		foreach ($dadosCliente as $key => $value) {
			$campos .= $key . ',';
			$valores .= "'" . mysql_real_escape_string($value) . "',";
		}
		$campos = trim($campos, ',');
		$valores = trim($valores, ',');
		$sql = "INSERT INTO D024 ({$campos}) VALUES ({$valores});";
	} else {
		$campos = '';
		$valores = '';
		foreach ($dadosCliente as $key => $value) {
			if (!isset($dadosCliente[$key]) || !empty($dadosClienteAntigo[$key])) { continue; }
			$campos .= $key . ' = ' . "'" . mysql_real_escape_string($value) . "',";
		}
		if (!empty($campos)) {
			$campos = trim($campos, ',');
			$valores = trim($valores, ',');
			$sql = "UPDATE  D024 SET {$campos} WHERE D024_Id = '{$dadosClienteAntigo['D024_Id']}'";
		}
	}
	mysql_query($sql);
	$cErro = mysql_error();
	if (!empty($cErro))
	{
		mysqli_query("ROLLBACK");
		echo "\tErro ao cadastrar o cliente! {$nome}";
	}
	if ($acao == 'U') {
		$g['mysqlLastId'] = $dadosClienteAntigo['D024_Id'];
	}
	echo "\t\t{$acao}: {$g['mysqlLastId']}\n";
	return $g['mysqlLastId'];
}

/*
* Funcao que cria o cabecalho do pedido, e retorna o ID do cabecalho
* Recebe como parametros:
*      $xml            -> E o XML total do pedido, para caso aconteca algum erro
*      $content           -> Array com as informacoes para o cabecalho, e gerado pela funcao processa_pedidos
*/
function cria_cabecalho_pedido($content)
{
	global $g, $pedido;

	echo "\n\tCriando cabecalho do pedido...\n";
	print_r($content);
	echo "\n";
	
    $com_desconto = $content['valor_total_produtos'] - $content['desconto'];
    $percentual_desconto = (100*$com_desconto) / $content['valor_total_produtos'];
    $percentual_desconto = 100 - $percentual_desconto;
	
    $valor_percentual_desconto = ($content['valor_total_produtos']*$percentual_desconto) / 100;
    
    $dataPedido = date('Y-m-d', $content['dataPedido']);
        
    /* Verifica se existe o usuario ecommerce, se tiver pega o ID dele, senao joga o pedido para o user ROOT */
    $sql_user = mysql_query("SELECT C007_Id FROM c007 WHERE C007_Nome='ecommerce' OR C007_Primeiro_Nome='ecommerce'");
    if( @mysql_num_rows($sql_user) )
    {
    	$open = mysql_fetch_assoc($sql_user);
        $usuario = $open['C007_Id'];
    }else
    	$usuario = '63';
	
	$sql = "
		INSERT INTO T005 (
                        T005_Numero_Pedido_Venda,
                        T005_Flag_Lista_Preco,
                        T005_Flag_Revenda_Consumidor,
                        T005_D024_Id,
                        T005_C004_Id,
                        T005_D006_Id,
                        T005_C007_Id_Vendedor_Interno,
                        T005_Data_Emissao,
                        T005_Data_Saida,
                        T005_Flag_ACP,
                        T005_Flag_Situacao,
                        T005_Valor_Desconto,
                        T005_Percentual_Desconto,
                        T005_Valor_Percentual_Desconto,
                        T005_Valor_Total_Produtos,
                        T005_Valor_Total_Produtos_Sem_Desconto,
                        T005_Valor_Total,
                        T005_Flag_Travar_Pedido,          					
                        T005_Flag_Status,
                        T005_Flag_Expedicao,
                        T005_Observacao_Comercial
		) VALUES (
                        '".$content['idPedido']."',
                        '1',
                        '1',
                        '".$content['idCliente']."',
                        '".$g['empresaAtual']."',
                        '".$content['CFOP']."',
                        '".$usuario."',
                        '".$dataPedido."',
                        current_date(),
                        '6',
                        '1',
                        '".$content['desconto']."',
                        '".$percentual_desconto."',
                        '".$valor_percentual_desconto."',
                        '".($content['valor_total_produtos']-$content['desconto'])."',
                        '".$content['valor_total_produtos']."',
                        '".($content['valor_total_produtos']-$content['desconto']+$content['frete'])."',
                        '2',
                        '0',
                        '2',
                        '".$content['observacao']."'
		)";
    mysql_query ($sql);
	/*
	T005_Valor_Frete = '".$content['frete']."'
    T005_Peso_Total,
    T005_Nome_Transportadora,
    T005_Peso_Liquido,
    T005_Peso_Bruto,
    */
				  
    $cErro = mysql_error();
    if (!empty($cErro))
    {
    	//mysql_query("ROLLBACK");
		echo "\t\tErro ao criar cabecalho do pedido! idPedido vindo do webservice: {$content['idPedido']}\n{$cErro}\n";
        file_put_contents('/tmp/WEBSTORM_ERRO_'.$content['idPedido'].'_CriandoCabecalho', $xml);
		//exit;
    }
    else
    {
    	$sql = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Numero_Pedido_Venda='".$content['idPedido']."'");
		$open = mysql_fetch_assoc($sql);
        //Cria histórico do pedido
        mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$open['T005_Id']}','{$usuario}',CURRENT_TIMESTAMP(),'Importado Ecommerce','')");
		return $open['T005_Id'];
    }
}

/*
* Funcao que e chamada no foreach dos produtos, adiciona 1 produto de cada vez
* Recebe como parametros:
*      $xml            -> E o XML total do pedido, para caso aconteca algum erro
*      $item           -> Array com as informacoes do produto
*      $item_numero    -> numero do item (1,2,3,4 ...)
*/
$item_produto_count = 0;
function add_produto_pedido($item, $item_numero)
{
	global $g, $pedido, $produto, $item_produto_count;
	require_once('bibliotecas/classes/CAD002.php');
	$CAD002 = new CAD002();
	echo "\tAdicionando produtos do pedido...\n";
    print_r($item);
    echo "\n";
	    
    $sql = mysql_query("
						SELECT
                        	D001_D005_Id,
                            D001_D037_Id,
                            D009_Id,
                            D049_Id,
                            D049_Flag_Tipo,
                            D009_D049_Id,
                            D001_Descricao_Produto,
                            D082_Marca
                        FROM D001
                            LEFT JOIN D049 ON D049_D001_Id=D001_Id
                            LEFT JOIN D082 ON D082_Id=D049_D082_Id
                            LEFT JOIN D009 ON D009_D049_Id=D049_Id AND D009_C004_Id='".$g['empresaAtual']."'
						WHERE
            				D001_Codigo_Produto='".$item['codigo']."'
						LIMIT 1
                        ");
    $mD009=mysql_fetch_array($sql);

    if(mysql_num_rows($sql)==0)
    {
		mysql_query("ROLLBACK");
		echo "\t\tNao existe o produto {$item['codigo']} cadastrado no Sistema Hardness.\nImportacao deste pedido abortado!\n-------------------------------------------------------------\n";
        file_put_contents('/tmp/WEBSTORM_ERRO_'.$item['idPedido'].'_ProdutoInexistente', $xml);
        return false;
    }

    //else
    //{
    	/* Verifica se e um KIT */
    	if($mD009['D049_Flag_Tipo'] == 'MT')
    	{
			$sql_kit = mysql_query("
									SELECT * FROM D042
									LEFT JOIN D049 ON D049_Id=D042_D049_Id_Itens
									LEFT JOIN D082 ON D049_D082_Id=D082_Id
									LEFT JOIN D001 ON D001_Id=D049_D001_Id
									LEFT JOIN D009 ON D009_D049_Id=D042_D049_Id_Itens
									LEFT JOIN D041 ON D041_D049_Id=D049_Id
									AND D009_C004_Id=".$g['empresaAtual']."
								WHERE D042_D049_Id='".$mD009['D049_Id']."'
									AND D042_C004_Id=".$g['empresaAtual']."
									AND D009_C004_Id=".$g['empresaAtual']."
    		");
    		$kit_item_numero = 0;
    		while($open = mysql_fetch_array($sql_kit))
    		{
    			$D001_mD009_descricao_produto = empty($mD009['D001_Descricao_Produto']) ? $item['descricao'] : $mD009['D001_Descricao_Produto'];
    			
    			// Foi coocado fixo o lista preço porque no na inserção da T005 está inserindo o lista preço fixo
    			$preco = $CAD002->retornaPrecoVenda($open['D009_Id'], '1', null);
    			
    			$kit_itens = array(                                                                              
					'idPedido'=>$item['idPedido'],
					'codigo'=>$open['D001_Codigo_Produto'],
                    'descricao'=>"{$open['D001_Descricao_Produto']}", //'descricao'=>"{$open['D001_Descricao_Produto']} ({$D001_mD009_descricao_produto})", <- com nome do kit entre parenteses
                    'quantidade'=>$item['quantidade']*$open['D042_Quantidade'],
					'valorUnitario'=>$item['quantidade']*$preco,
					'idCabecalho'=>$item['idCabecalho'],
					'idKit'=>$mD009['D009_Id'],
					'type'=>'KIT',
					'CFOP'=>$item['CFOP']
					);        
				$kit_item_numero++;
				add_produto_pedido($kit_itens,$kit_item_numero);
    		}
    		//die();
    		return true;
    	}
    	
    	$descricao_produto = ( !empty($mD009['D001_Descricao_Produto']) AND $item['type']=='NORMAL' ) ? $mD009['D001_Descricao_Produto'] : $item['descricao'];
    	$id_kit = empty($item['idKit']) ? '0' : $item['idKit'];
    //}
	
	$item_produto_count++;
    mysql_query("
            	INSERT INTO T006(
                        T006_T005_Id,
                        T006_D006_Id,
                        T006_D009_Id,
                        T006_C004_Id,
                        T006_D049_Id,
                        T006_D005_Id,
                        T006_D037_Id,
                        T006_Item,
                        T006_Codigo_Produto,
                        T006_Descricao_Produto,
                        T006_Quantidade,
                        T006_Valor_Preco_Unitario,
                        T006_Valor_Preco_Sem_Desconto_Unitario,
                        T006_Marca,
                        T006_Flag_Status,
                        T006_D009_Id_Kit,
                        T006_Valor_Preco_Original
				) VALUES (
                        '".$item['idCabecalho']."',
                        '".$item['CFOP']."',
                        '".$mD009['D009_Id']."',
                        '".$g['empresaAtual']."',
                        '".$mD009['D009_D049_Id']."',
                        '".$mD009['D001_D005_Id']."',
                        '".$mD009['D001_D037_Id']."',
                        '".$item_produto_count."',
                        '".$item['codigo']."',
                        '".$descricao_produto."',
                        '".$item['quantidade']."',
                        '".$item['valorUnitario']."',
                        '".$item['valorUnitario']."',
                        '".$mD009['D082_Marca']."',
                        '',
                        '".$id_kit."',
                        '".$item['valorUnitario']."'
				)
		");
    /* T006_Peso_Unitario */
				  
    $cErro = mysql_error();
    if (!empty($cErro))
    {
    	#mysql_query("ROLLBACK");
        echo 'Erro ao cadastrar o produto "'.$item['codigo'].'". idCabecalho="'.$item['idCabecalho'].'"';
        file_put_contents('/tmp/SIS_ERRO_'.$item['idPedido'].'_CadastroProduto', $xml);
        //exit;    
    }     

    $T006_Id=$g['mysqlLastId'];
    mysql_query("call T006_Gravar_Totalizacao_3('$T006_Id')");

    return true;
}

/*
* Funcao que atualiza as aliquotas
* Recebe como parametro:
*      $T005_Id       -> O id do cabecalho
*/
function atualizarAliquotas($T005_Id)
{
	global $g;
	echo "\tAtualizando aliquotas {$T005_Id} ...\n";
	$cSql_T006 = mysql_query("
						SELECT 
                        	D009_IPI(T005_C004_Id,T006_D009_Id,T005_D024_Id,T006_D006_Id,1,T005_Flag_ACP) AS T006_Aliquota_IPI_Correto,
                            D009_IPI(T005_C004_Id,T006_D009_Id,T005_D024_Id,T006_D006_Id,2,T005_Flag_ACP)  AS T006_Percentual_Reducao_IPI_Correto,
                            D009_IPV_ICMS(T005_C004_Id,T005_D024_Id,T006_D009_Id,T006_D006_Id,D020_D018_Id,T005_Flag_Revenda_Consumidor,T005_Percentual_Comissao,T005_Flag_ACP,2) AS T006_Aliquota_ICMS_Correto,
                            D009_IPV_ICMS(T005_C004_Id,T005_D024_Id,T006_D009_Id,T006_D006_Id,D020_D018_Id,T005_Flag_Revenda_Consumidor,T005_Percentual_Comissao,T005_Flag_ACP,3) AS T006_Percentual_Reducao_ICMS_Correto,
                            T006_Id,
                            D049_Flag_Substituicao_Tributaria,
                            D005_Id,
							D005_Classificacao_Fiscal,
							D005_Flag_ST,
							D005_Valor_VA,
							D053_ST_VA,
							D053_ST_VA_2,
							D059_Codigo_ST,
							D059_Id,
							D088_Codigo_ST,
							T005_Flag_Revenda_Consumidor,
							D024_Situacao_Tributaria_PIS_COFINS,
							D024_Flag_Isento_PIS_COFINS,
							D006_Flag_Substituicao_Tributaria,
							T005_Flag_Acp,
							D006_Flag_ST_VA
                        FROM T005
							LEFT JOIN T006 ON T005_Id=T006_T005_Id
							LEFT JOIN D024 ON D024_Id=T005_D024_Id
							LEFT JOIN D020 ON D020_Id=D024_D020_Id
							LEFT JOIN D009 ON D009_Id=T006_D009_Id
							LEFT JOIN D049 ON D049_Id=D009_D049_Id
							LEFT JOIN D001 ON D001_Id=D049_D001_Id
							LEFT JOIN D006 ON D006_Id=T006_D006_Id
							LEFT JOIN D005 ON D005_Id=D009_D005_Id(D009_Id)
							LEFT JOIN D088 ON D088_Id=if(D024_D088_Id>0,D024_D088_Id,D005_D088_Id)
							LEFT JOIN D059 ON D059_Id=if(D024_D059_Id>0,D024_D059_Id,D006_D059_Id)
							LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id
							AND D053_C004_Id='{$g['empresaAtual']}'
                        WHERE T005_Id='{$T005_Id}'
	");


	while( $mT006 = mysql_fetch_array($cSql_T006) )
	{
		if( $mT006['D005_Valor_VA'] <= 0 )
		{
	    	if( $mT006['T005_Flag_Revenda_Consumidor'] == '1' )
				$mT006['D005_Valor_VA'] = $mT006['D053_ST_VA'];
	        else
				$mT006['D005_Valor_VA'] = $mT006['D053_ST_VA_2'];
		}
		if( $mT006['D006_Flag_ST_VA'] == "N" )
	    	$mT006['D005_Valor_VA'] = 0;

		if( ($mT006['T005_Flag_Acp']!='1' AND $mT006['T005_Flag_Acp']!='6') OR 
	    	($mT006['D049_Flag_Substituicao_Tributaria']!='S') OR 
	        ($mT006['D006_Flag_Substituicao_Tributaria']!='S') )
		{
	    	$mT006['D005_Flag_ST']="N";
	        $mT006['D005_Valor_VA']=0;
		}

		$cClausula = "
	    			UPDATE T006
	                	LEFT JOIN D006 ON D006_Id=T006_D006_Id
	    				LEFT JOIN D059 ON D059_Id=D006_D059_Id
	    				LEFT JOIN T005 ON T005_Id=T006_T005_Id
	    				LEFT JOIN C004 ON C004_Id=T005_C004_Id
	                SET
	    			T006_Aliquota_IPI='".$mT006['T006_Aliquota_IPI_Correto']."',
	    			T006_Percentual_Reducao_IPI='".$mT006['T006_Percentual_Reducao_IPI_Correto']."',
	    			T006_Situacao_Tributaria_IPI='".$mT006['D088_Codigo_ST']."', 
	    			T006_Aliquota_ICMS='".$mT006['T006_Aliquota_ICMS_Correto']."',
	    			T006_Percentual_Reducao_ICMS='".$mT006['T006_Percentual_Reducao_ICMS_Correto']."',
	    			T006_Flag_ST=D006_Flag_Substituicao_Tributaria,
	    			T006_D005_Id='".$mT006['D005_Id']."',
	    			T006_D059_Id='".$mT006['D059_Id']."',
	    			T006_ST_VA='".$mT006['D005_Valor_VA']."',
	    ";
		if( !empty( $mT006['D024_Situacao_Tributaria_PIS_COFINS'] ) )
	    {
			$cClausula .= "
							T006_Situacao_Tributaria_PIS='".$mT006['D024_Situacao_Tributaria_PIS_COFINS']."',
							T006_Situacao_Tributaria_COFINS='".$mT006['D024_Situacao_Tributaria_PIS_COFINS']."',
			";
	        if( $mT006['D024_Flag_Isento_PIS_COFINS'] == 'S' )
	        	$cClausula .= "
	                        	T006_Flag_Isento_PIS='".$mT006['D024_Flag_Isento_PIS_COFINS']."',
								T006_Aliquota_PIS='0',
								T006_Flag_Isento_COFINS='".$mT006['D024_Flag_Isento_PIS_COFINS']."',
								T006_Aliquota_COFINS='0',
	            ";
	        else
	        	$cClausula .= "
								T006_Flag_Isento_PIS=D006_Flag_Isento_PIS,
								T006_Aliquota_PIS=C004_Percentual_PIS,
								T006_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,
								T006_Aliquota_COFINS=C004_Percentual_COFINS,		
				";
		}else
	    	$cClausula .= "
	        				T006_Flag_Isento_PIS=D006_Flag_Isento_PIS,
	    					T006_Aliquota_PIS=C004_Percentual_PIS,
	    					T006_Situacao_Tributaria_PIS=D006_Situacao_Tributaria_PIS,
	    					T006_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,
	    					T006_Aliquota_COFINS=C004_Percentual_COFINS,
	    					T006_Situacao_Tributaria_COFINS=D006_Situacao_Tributaria_COFINS,
	        ";

		$cClausula .="
	    				T006_Classificacao_Fiscal='".$mT006['D005_Classificacao_Fiscal']."',
	    				T006_Flag_Situacao_Tributaria=concat('0','".$mT006['D059_Codigo_ST'].")
					WHERE T006_Id='".$mT006['T006_Id']."'
	    ";

	    mysql_query($cClausula);
	      
	    mysql_query("call T006_Gravar_Totalizacao_3('".$mT006['T006_Id']."')");
	    }/* Fim while */

	    $cSql_T005 = mysql_query("
	    			     SELECT
						 	D009_IPV_ICMS(T005_C004_Id,T005_D024_Id,0,T005_D006_Id,D020_D018_Id,T005_Flag_Revenda_Consumidor,T005_Percentual_Comissao,T005_Flag_ACP,2)
	                                    AS T005_Aliquota_ICMS_Correto,
							D009_IPV_ICMS(T005_C004_Id,T005_D024_Id,0,T005_D006_Id,D020_D018_Id,T005_Flag_Revenda_Consumidor,T005_Percentual_Comissao,T005_Flag_ACP,3)
	                                    AS T005_Percentual_Reducao_ICMS_Correto
	                     FROM T005
						 	LEFT JOIN D024 ON D024_Id=T005_D024_Id
							LEFT JOIN D020 ON D020_Id=D024_D020_Id
	                     WHERE T005_Id='".$T005_Id."'
						");
	    $mT005 = mysql_fetch_array($cSql_T005);

	    mysql_query("UPDATE T005
	                    SET
							T005_Aliquota_ICMS='".$mT005['T005_Aliquota_ICMS_Correto']."',
							T005_Percentual_Reducao_ICMS='".$mT005['T005_Percentual_Reducao_ICMS_Correto']."'
	                    WHERE T005_Id='$T005_Id'");	

	    mysql_query("call T005_Gravar_Totalizacao_3('$T005_Id')");

	}

function calculaDesconto($T005_Id){
	echo "\tCalculando desconto {$T005_Id} ...\n";

	$T005=mysql_query("SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'");
	$mT005=mysql_fetch_array($T005);

	$Percentual_Desconto = (($mT005['T005_Valor_Desconto']*100)/$mT005['T005_Valor_Total']);

	$T006=mysql_query("SELECT * FROM T006 WHERE T006_T005_Id='{$T005_Id}'");
	while($mT006=mysql_fetch_array($T006)){
	    
	    mysql_query("update T006 set 
	                T006_Percentual_Desconto=$Percentual_Desconto, 
	                T006_Valor_Preco_Sem_Desconto_Unitario=T006_Valor_Preco_Original-(T006_Valor_Preco_Original*($Percentual_Desconto/100)), 
	                T006_Valor_Preco_Unitario=T006_Valor_Preco_Sem_Desconto_Unitario, 
	                T006_Valor_Desconto_Unitario=(T006_Valor_Preco_Original*($Percentual_Desconto/100)) 
	                where T006_Id='{$mT006['T006_Id']}'");
	    
	    mysql_query("T006_Gravar_Totalizacao_3('{$mT006['T006_Id']}')");
	}
	mysql_query("T005_Gravar_Totalizacao_3('{$T005_Id}');");

}