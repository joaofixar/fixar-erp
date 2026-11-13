<?php
namespace hardness;

use Error;

$resposta = array('code' => true, 'data' => array());

// Classes
require_once('bibliotecas/classes/EST014.php');
$EST014 = new EST014();
require_once('bibliotecas/classes/EST008.php');
$EST008 = new EST008();
require_once('bibliotecas/classes/RES001.php');
$RES001 = new RES001();

/**
 * Ações
 */
switch($g['r'][3]) {

	case 'crm001AtualizarResumoR1':

		$valorTotal = $r_valorTotal; 
		$registrosEtapa = floor($valorTotal / $r_etapas);
		$etapa = $r_etapa;
		$dataAtualizar = date('Y-m-d', strtotime("+$etapa days", strtotime($r_dataInicial)));
		$limit1 = floor(( $registrosEtapa * $etapa ) - $registrosEtapa);
		if($etapa >= $r_etapas){
			$limit2 = ceil($registrosEtapa + ($valorTotal - ($limit1 + $registrosEtapa)));
		} else {
			$limit2 = $registrosEtapa ;
		}
		$concluido = 0;

		$resposta['percentual'] = ceil((100/$r_etapas) * $etapa);

		if($limit2 <= $valorTotal){
			$etapa = $r_etapa + 1;	
			$RES001->atualizarDadosResumoR1($dataAtualizar);

		}else{
			$concluido = 1;
		}
		$resposta['etapa'] = $etapa;
		$resposta['concluido'] = $concluido;
		// --

		$ok = 0;
		$erro = 0;
		$erros ="";

 	    $ok++;

		$resposta['data']="Ocorreu um erro inesperado!";
		$resposta['code'] = true;
		$resposta['ok'] = $ok;
		$resposta['erro'] = $erro;
		$resposta['erros'] = $erros;
		
	break;

	case 'crm001AgruparPedidosEmEtapas':
		require_once('bibliotecas/classes/VEN002.php');
		$VEN002 = new VEN002();

		if ($g['C031']['agruparPedidosEmEtapas'] != 'S') {
			$resposta['code'] = false;
			$resposta['data'] = 'O agrupamento em etapas por item não está habilitado.';
			break;
		}

		if (empty($r_processoKey)) {
			$retorno = $VEN002->VEN002ValidaAgruparPedidosEmEtapas($r_sqlSelecao);
			if (!is_array($retorno) || empty($retorno['processoKey'])) {
				$resposta['code'] = false;
				$resposta['data'] = is_string($retorno) ? $retorno : 'Não foi possível iniciar o agrupamento em etapas.';
				break;
			}

			$resposta['code'] = true;
			$resposta['processoKey'] = $retorno['processoKey'];
			$resposta['totalEtapas'] = (int) $retorno['totalEtapas'];
			$resposta['ok'] = 0;
			$resposta['restante'] = (int) $retorno['totalEtapas'];
			$resposta['percentual'] = 0;
			$resposta['concluido'] = 0;
			break;
		}

		$arquivoCache = "/tmp/ven002-agrupar-pedidos-etapas-{$g['usuarioAtual']}-{$r_processoKey}";
		if (!file_exists($arquivoCache)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Não foi possível recuperar o cache do agrupamento em etapas.';
			break;
		}

		$cache = @unserialize(file_get_contents($arquivoCache));
		$cache = is_array($cache) ? $cache : false;
		if ($cache === false || empty($cache['etapas'])) {
			$resposta['code'] = false;
			$resposta['data'] = 'Não foi possível recuperar os itens do agrupamento em etapas.';
			break;
		}

		$indiceEtapa = (int) $cache['etapaAtual'];
		$totalEtapas = (int) $cache['totalEtapas'];
		if ($indiceEtapa >= $totalEtapas) {
			$resposta['code'] = false;
			$resposta['data'] = 'O processamento dos itens terminou, mas a finalização do agrupamento não foi confirmada no banco.';
			break;
		}

		$dadosEtapa = $cache['etapas'][$indiceEtapa];
		$dadosEtapa['Primeira_Etapa'] = ($indiceEtapa == 0) ? 1 : 0;
		$dadosEtapa['Ultimo_Item'] = ($indiceEtapa == ($totalEtapas - 1)) ? 1 : 0;
		$dadosEtapa['Processo_Key'] = $r_processoKey;
		$retorno = $VEN002->VEN002AgruparPedidosEmEtapas($dadosEtapa);
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
			break;
		}

		$processados = $indiceEtapa + 1;
		$resposta['code'] = true;
		$resposta['ok'] = $processados;
		$resposta['restante'] = $totalEtapas - $processados;
		$resposta['percentual'] = ceil((100 / $totalEtapas) * $processados);
		$resposta['concluido'] = ($processados >= $totalEtapas) ? 1 : 0;
	break;

	case 'est014CheckboxCotar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;
		$EST014->updateCheckboxGerarCotar($T189_Id, $selected);
	break;
	
	case 'est014CheckboxComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T189_Id = isset($r_T189_Id) ? $r_T189_Id : false;
		$EST014->updateCheckboxGerarComprar($T189_Id, $selected);
	break;
	
	case 'est014FlagMarcar':
		 mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		$sql = base64_decode($r_extra);
		$sql = stripslashes($sql);
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$result = mysql_query($sql);
		if(mysql_error()){
			mysql_query("ROLLBACK");
			return mysql_error();
		}
		if($r_operacao==1) {
			$campo='T189_Flag_Cotar';
			while($linha = mysql_fetch_array($result)){
				$sql = "update T189 set {$campo}='S' where T189_Id='{$linha['T189_Id']}' AND T189_Flag_Tipo != 'R'";
				mysql_query($sql);
				if(mysql_error()){
					mysql_query("ROLLBACK");
					return mysql_error();
				}
			}
		} else {
		    $campo='T189_Flag_Comprar';
			while($linha = mysql_fetch_array($result)){
				$sql = "update T189 set {$campo}='S' where T189_Id='{$linha['T189_Id']}' AND (T189_D009_Id>0 OR T189_D009_Id IS NOT NULL AND T189_D009_Id != '') AND T189_Flag_Tipo != 'R'";
				mysql_query($sql);
				if(mysql_error()){
					mysql_query("ROLLBACK");
					return mysql_error();
				}
			}
		}
		mysql_query("COMMIT");
	break;
	
	case 'est014FlagDesmarcar':
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		$sql = base64_decode($r_extra);
		$sql = stripslashes($sql);
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$result = mysql_query($sql);
		if(mysql_error()){
			mysql_query("ROLLBACK");
			return mysql_error();
		}
		if($r_operacao==1) {
			$campo='T189_Flag_Cotar';
		} else {
		    $campo='T189_Flag_Comprar';
		}
		while($linha = mysql_fetch_array($result)){
			$sql = "update T189 set {$campo}='N' where T189_Id='{$linha['T189_Id']}'  AND T189_Flag_Tipo != 'R'";
			mysql_query($sql);
			if(mysql_error()){
				mysql_query("ROLLBACK");
				return mysql_error();
			}
		}
		mysql_query("COMMIT");
	break;
	
	case 'est014GerarOC':
		$sql="select count(T189_Id) as Total_Comprar from T189 where T189_Flag_Comprar='S'";
		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);
		
		if ($row['Total_Comprar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para compra.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma ordem de compra dos {$row['Total_Comprar']} itens selecionados?";
		}
	break;
	
	case 'est014GerarCotacao':
		$sql="select count(T189_Id) as Total_Cotar from T189 where T189_Flag_Cotar='S'";
		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);
		
		if ($row['Total_Cotar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para cotar.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma cotação dos {$row['Total_Cotar']} itens selecionados?";
		}
	break;

	case 'crm001ImportarTray':
		require_once('bibliotecas/webservices/tray/comunicacaoTray.php');
		$Tray = new ComunicacaoTray();

		//Verifica os pedidos
		$retorno = $Tray->autenticacaoTray();
		
		$resposta['data'] = $retorno;
		$resposta['code'] = true;
	break;

	case 'enviarNotificacao':
		global $confUsuario;
		$D181_Id = isset($r_D181_Id) ? $r_D181_Id : false;

		require_once('bibliotecas/classes/API001.php');
		$API001 = new API001();

		//Verifica os pedidos
		$retorno = $API001->enviarNotificacaoTopico($D181_Id,$confUsuario['topicoFirebase']);
		
		$resposta['data'] = $retorno['message'];
		$resposta['code'] = $retorno['code'];
	break;

	case 'crm001ImportarMagento2':
		require_once('bibliotecas/webservices/magento2/integracaoMagento2.php');
		$Magento = new Magento();

		//Verifica os pedidos
		$retorno = $Magento->buscarPedido();

		if ($retorno === false) {
			$resposta['data'] = $retorno;
			$resposta['code'] = false;
		}else{
			$resposta['data'] = $retorno;
			$resposta['code'] = true;
		}
	break;

	case 'crm001ImportarMagentoSoapx':
		require_once('bibliotecas/webservices/magento/magentoSoap.php');

		$MagentoSoap = new MagentoSoap();

		if($g['C031']['MagentoIntegrador']=="I"){
			$retorno = $MagentoSoap->importacao();
		}elseif($g['C031']['MagentoIntegrador']=="A"){
			$retorno = $MagentoSoap->importacaoAnymarket();
		}		

		if ($retorno === false) {
			$resposta['code'] = false;
		}else{
			$resposta['data'] = $retorno;
			$resposta['code'] = true;
		}
	break;

    case 'crm001ImportarMagentoSoap':
    	global $g;
		require_once('bibliotecas/webservices/magento/magentoSoap.php');
        $MagentoSoap = new MagentoSoap();

		require_once("bibliotecas/classes/VEN014.php");
        $VEN014 = new VEN014();
		$resposta['code'] = true;

		$Dir = $g['pathDados'] . 'tmp/';
		is_dir($Dir) or mkdir($Dir, 0777, true);
		$arquivoNome = "{$Dir}cachePedidosBuscarMagento-{$g['usuarioAtual']}.txt";

		if ($r_inicio==1){
			$listaPedidos = $MagentoSoap->buscarPedidosMagento();
            $numPedidos = count($listaPedidos);
            $resposta['inicioProcesso'] = date('Y-m-d H:i:s');

            if ($numPedidos<=0) {
                $resposta['code'] = false;
                $resposta['erros'] = 'Não há nenhum pedido para importação com o status '.$g['C031']['MagentoStatusPedido'].".";
            }

            $count = 0;
            foreach ($listaPedidos as $key => $order) {
        	    $item = (array) $order;
        		$cache[$count]['order'] = $item;
                $count++;
            }

            $resposta['mensagem'] = "Buscando";
			$resposta['encontrados'] = $numPedidos;
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
    	    $resposta['repetidos'] = 0;
    	    $resposta['tratados'] = 0;
        	$resposta['concluido'] = 0;
        	$resposta['ok'] = 0;
			$resposta['erro'] = 0;
			
			//salvando pedidos para a importação no cache			
			// apc_delete("cachePedidosBuscarMagento-{$g['usuarioAtual']}");
			apc_delete("cachePedidosBuscarMagento-resposta-{$g['usuarioAtual']}");
			// $retorno = apc_store("cachePedidosBuscarMagento-{$g['usuarioAtual']}", serialize($cache));
			
			//salvando pedidos para a importação em arquivo texto
			$h = fopen($arquivoNome, "a");
			$r = fwrite($h, serialize($cache));
			fclose($h);
			$retorno = file_exists($arquivoNome);
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar arquivo local.';
	        	$resposta['concluido'] = 1;
	        	break;
	        }
        } else {
			$cache = unserialize(file_get_contents($arquivoNome));
			// $cache = unserialize(apc_fetch("cachePedidosBuscarMagento-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cachePedidosBuscarMagento-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao recuperar variável no cache resposta';
	        	$resposta['concluido'] = 1;
	        	break;
			}

			if(!is_array($cache)){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao recuperar arquivo com pedidos.';
	        	$resposta['concluido'] = 1;
	        	break;
			}
			
	    }
	    $resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

		if($resposta['etapa'] >= $resposta['etapas']){
			$resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
		} else {
			$resposta['limit2'] += $resposta['registrosEtapa'] ;
		}

	    $resposta['percentual'] = ceil((100/$resposta['etapas']) * $resposta['etapa']);

	    $r_opcoes = json_decode($r_opcoes);

	    if(!is_array($r_opcoes)) { 
	        $opcoes = get_object_vars($r_opcoes);
	    }

		$repetidos = 0;
		$tratados = 0;
		$erro = 0;
		$ok = 0;
		$qteNaoFiltrado = 0;
		$sucesso = "";
		$fracasso = "";
		$naoFiltrado = "";

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) {

			$item = (array) $cache[$i]['order'];
			$increment_id = $item['increment_id'];

            if ($VEN014->procuraPedido($increment_id)){
                $repetidos++;
            }else{
	        	if($g['C031']['MagentoIntegrador']=="I"){
	             	$T005 = $MagentoSoap->tratarDadosOrder($item);
	            }else if($g['C031']['MagentoIntegrador']=="A"){
	             	$T005 = $MagentoSoap->tratarDadosOrderAnymarket($item);
					
					$pedidosManipulados = array(
					
					//Codigo produtos para teste
					/*
					1051210 GMP = 5; DELTATOP = 9; GMK8 = 30;
					1051209 GMP = 0; DELTATOP = 4; GMK8 = 5;
					1001653 GMP = 15; DELTATOP = 1; GMK8 = 10;
					1045271 GMP = 0; DELTATOP = 19; GMK8 = 0;
					1043387 GMP = 3; DELTATOP = 13; GMK8 = 0;
					*/

					/* ORDER 001 MAGAZINE LUIZA -> GMP */						
					array ( 'T005_Prazo_Entrega' => '16/04/20 23:59:59', 'T005_Valor_Total_Produtos' => '42.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '42.0000', 'T005_Valor_Total' => '78.9200', 'T005_Valor_Frete' => '36.9200', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024535 Codigo pedido Marketplace:701 4889179 7589843 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '24683', 'T005_Id_Pedido_Externo' => 'order001', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '701 4889179 7589843', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '37', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'CARTÃODECRÉDITO', 'D024' => array ( 'D024_Cpf' => '03230332458', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Thales marinho campos', 'D024_Nome_Empresa' => 'Thales marinho campos', 'D024_Endereco' => 'Rua Francisco simplicio', 'D024_Numero' => '145', 'D024_Cep' => '59090315', 'D024_Complemento' => '19 andar, bloco b, apartamento 1901', 'D024_Bairro' => 'Ponta Negra', 'Cidade' => 'CURITIBA', 'UF' => 'RN', 'Pais' => 'BR', 'D024_E_Mail' => 'hmj1bmt4ddkqcjh@marketplace.amazon.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '84999211601', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'hmj1bmt4ddkqcjh@marketplace.amazon.com.br', 'UF' => 'RN', 'Cidade' => '.', 'Pais' => 'BR', 'D148_Nome' => 'Thales marinho campos', 'D148_Cep' => '59090315', 'D148_Rua' => 'Rua Francisco simplicio', 'D148_Numero' => '145', 'D148_Complemento' => '19 andar, bloco b, apartamento 1901', 'D148_Bairro' => 'Ponta Negra', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '84999211601', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 
					'T006' => array ( 
						0 => array ('Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Protetor facial policarbonato Incolor 210mm - Nove54', 'T006_Quantidade' => '9998', 'T006_Valor_Preco_Original' => '21.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 21.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 42.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024535', 'T006_Preco_Venda_Ecommerce' => 21.0, 'T006_Preco_Tabela_Ecommerce' => '21.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'VONDER'), 
					),
					'T090' => array ( 0 => array ( 'T090_D027_Id' => '37', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 78.92, 'T090_Prazos_Dias' => 30, ), ), 'MagentoSoap_T005_C004_Id' => 4, ),

					/* ORDER 002 */
					// array ( 'T005_Prazo_Entrega' => '16/04/20 23:59:59', 'T005_Valor_Total_Produtos' => '42.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '42.0000', 'T005_Valor_Total' => '78.9200', 'T005_Valor_Frete' => '36.9200', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024535 Codigo pedido Marketplace:701 4889179 7589843 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '24683', 'T005_Id_Pedido_Externo' => 'order001.1', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '701 4889179 7589843', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '37', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'CARTÃODECRÉDITO', 'D024' => array ( 'D024_Cpf' => '03230332458', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Thales marinho campos', 'D024_Nome_Empresa' => 'Thales marinho campos', 'D024_Endereco' => 'Rua Francisco simplicio', 'D024_Numero' => '145', 'D024_Cep' => '59090315', 'D024_Complemento' => '19 andar, bloco b, apartamento 1901', 'D024_Bairro' => 'Ponta Negra', 'Cidade' => '.', 'UF' => 'RN', 'Pais' => 'BR', 'D024_E_Mail' => 'hmj1bmt4ddkqcjh@marketplace.amazon.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '84999211601', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'hmj1bmt4ddkqcjh@marketplace.amazon.com.br', 'UF' => 'RN', 'Cidade' => '.', 'Pais' => 'BR', 'D148_Nome' => 'Thales marinho campos', 'D148_Cep' => '59090315', 'D148_Rua' => 'Rua Francisco simplicio', 'D148_Numero' => '145', 'D148_Complemento' => '19 andar, bloco b, apartamento 1901', 'D148_Bairro' => 'Ponta Negra', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '84999211601', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 
					// 'T006' => array ( 
					// 	0 => array ('Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1025644', 'T006_Descricao_Produto' => 'Protetor facial policarbonato Incolor 210mm - Nove54', 'T006_Quantidade' => '30', 'T006_Valor_Preco_Original' => '21.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 21.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 42.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024535', 'T006_Preco_Venda_Ecommerce' => 21.0, 'T006_Preco_Tabela_Ecommerce' => '21.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'NOVE54'), 
					// ),
					// 'T090' => array ( 0 => array ( 'T090_D027_Id' => '37', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 78.92, 'T090_Prazos_Dias' => 30, ), ), 'MagentoSoap_T005_C004_Id' => 4, ),

					///* ORDER 002 MAGAZINE LUIZA -> GMP */						array ( 'T005_Prazo_Entrega' => '30/03/20 22:27:24', 'T005_Valor_Total_Produtos' => '42.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '42.0000', 'T005_Valor_Total' => '84.1600', 'T005_Valor_Frete' => '42.1600', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024550 Codigo pedido Marketplace:20240364201 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '24698', 'T005_Id_Pedido_Externo' => 'order002', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '20240364201', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '21', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => 'MAGAZINE_LUIZA-MAGAZINE LUIZA', 'Comment_Forma_de_Pagamento' => 'CARTÃODECRÉDITO', 'D024' => array ( 'D024_Cpf' => '07977049813', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'paulo roberto tanibata', 'D024_Nome_Empresa' => 'paulo roberto tanibata', 'D024_Endereco' => 'Rua Rua Maria Emília Alves dos Santos de Ângelis', 'D024_Numero' => '121', 'D024_Cep' => '13044901', 'D024_Complemento' => 'apto 162, torre 1', 'D024_Bairro' => 'Parque Prado', 'Cidade' => 'Campinas', 'UF' => 'SP', 'Pais' => 'BR', 'D024_E_Mail' => 'atendimento+OS_324040600212@mktp.extra.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '1932791578', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'atendimento+OS_324040600212@mktp.extra.com.br', 'UF' => 'SP', 'Cidade' => 'Campinas', 'Pais' => 'BR', 'D148_Nome' => 'paulo roberto tanibata', 'D148_Cep' => '13044901', 'D148_Rua' => 'Rua Rua Maria Emília Alves dos Santos de Ângelis', 'D148_Numero' => '121', 'D148_Complemento' => 'apto 162, torre 1', 'D148_Bairro' => 'Parque Prado', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '1932791578', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Protetor facial policarbonato Incolor 210mm - Nove54', 'T006_Quantidade' => '1.0000', 'T006_Valor_Preco_Original' => '21.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 21.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 21.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024550', 'T006_Preco_Venda_Ecommerce' => 21.0, 'T006_Preco_Tabela_Ecommerce' => '21.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'NOVE54', ), 1 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Protetor facial policarbonato Incolor 210mm - Nove54', 'T006_Quantidade' => '1.0000', 'T006_Valor_Preco_Original' => '21.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 21.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 21.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024550', 'T006_Preco_Venda_Ecommerce' => 21.0, 'T006_Preco_Tabela_Ecommerce' => '21.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'NOVE54', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '21', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 84.16, 'T090_Prazos_Dias' => 30, ), ), 'MagentoSoap_T005_C004_Id' => 4, ),
					///* ORDER 003 RENNER-Guimepa -> GMK8 */						array ( 'T005_Prazo_Entrega' => '07/04/20 04:04:09', 'T005_Valor_Total_Produtos' => '19.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '19.0000', 'T005_Valor_Total' => '123.7100', 'T005_Valor_Frete' => '104.7100', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024561 Codigo pedido Marketplace:8215500566246034 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '24709', 'T005_Id_Pedido_Externo' => 'order003', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '8215500566246034', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '29', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => 'RENNER-Guimepa', 'Comment_Forma_de_Pagamento' => 'BOLETOBANCARIO', 'D024' => array ( 'D024_Cpf' => '01337284475', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Renata Maria Didier gazineu', 'D024_Nome_Empresa' => 'Renata Maria Didier gazineu', 'D024_Endereco' => 'IRMA LUCIA', 'D024_Numero' => '112', 'D024_Cep' => '52070030', 'D024_Complemento' => '2301 Próximo hospital de olhos santa luzia', 'D024_Bairro' => 'CASA AMARELA', 'Cidade' => 'RECIFE', 'UF' => 'PE', 'Pais' => 'BR', 'D024_E_Mail' => 'LU-8215500566246034@alias.integracommerce.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '81996560656', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'LU-8215500566246034@alias.integracommerce.com.br', 'UF' => 'PE', 'Cidade' => 'RECIFE', 'Pais' => 'BR', 'D148_Nome' => 'Renata Maria Didier gazineu', 'D148_Cep' => '52070030', 'D148_Rua' => 'IRMA LUCIA', 'D148_Numero' => '112', 'D148_Complemento' => '2301 Próximo hospital de olhos santa luzia', 'D148_Bairro' => 'CASA AMARELA', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '81996560656', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1043387', 'T006_Descricao_Produto' => 'Protetor facial policarbonato incolor 160mm - Nove54', 'T006_Quantidade' => '1.0000', 'T006_Valor_Preco_Original' => '19.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 19.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 19.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024561', 'T006_Preco_Venda_Ecommerce' => 19.0, 'T006_Preco_Tabela_Ecommerce' => '19.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'NOVE54', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '29', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 123.71, 'T090_Prazos_Dias' => 30, ), ), 'MagentoSoap_T005_C004_Id' => 2, ),
					///* ORDER 004 RENNER-Guimepa -> GMK8 */						array ( 'T005_Prazo_Entrega' => '31/03/20 04:13:51', 'T005_Valor_Total_Produtos' => '38.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '38.0000', 'T005_Valor_Total' => '73.7300', 'T005_Valor_Frete' => '35.7300', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024562 Codigo pedido Marketplace:8214500566145057 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '24710', 'T005_Id_Pedido_Externo' => 'order004', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '8214500566145057', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '29', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => 'RENNER-Guimepa', 'Comment_Forma_de_Pagamento' => 'BOLETOBANCARIO', 'D024' => array ( 'D024_Cpf' => '57358796934', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'jairo pasqual anzolin', 'D024_Nome_Empresa' => 'jairo pasqual anzolin', 'D024_Endereco' => 'ALMIRANTE LAMEGO', 'D024_Numero' => '830', 'D024_Cep' => '88015600', 'D024_Complemento' => 'ap 607a proximo supermerc. Hippo', 'D024_Bairro' => 'CENTRO', 'Cidade' => 'FLORIANOPOLIS', 'UF' => 'SC', 'Pais' => 'BR', 'D024_E_Mail' => 'LU-8214500566145057@alias.integracommerce.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '48999039336', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'LU-8214500566145057@alias.integracommerce.com.br', 'UF' => 'SC', 'Cidade' => 'FLORIANOPOLIS', 'Pais' => 'BR', 'D148_Nome' => 'jairo pasqual anzolin', 'D148_Cep' => '88015600', 'D148_Rua' => 'ALMIRANTE LAMEGO', 'D148_Numero' => '830', 'D148_Complemento' => 'ap 607a proximo supermerc. Hippo', 'D148_Bairro' => 'CENTRO', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '48999039336', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1001653', 'T006_Descricao_Produto' => 'Protetor facial policarbonato incolor 160mm - Nove54', 'T006_Quantidade' => '2.0000', 'T006_Valor_Preco_Original' => '19.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 19.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 38.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024562', 'T006_Preco_Venda_Ecommerce' => 19.0, 'T006_Preco_Tabela_Ecommerce' => '19.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'NOVE54', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '29', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 73.73, 'T090_Prazos_Dias' => 30, ), ), 'MagentoSoap_T005_C004_Id' => 2, ),
					///* ORDER 005 CURITIBA, VONDER -> QUANTIDADE 10, 20 */ 		array ( 'T005_Prazo_Entrega' => '02/04/20 05:42:42', 'T005_Valor_Total_Produtos' => '50.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '50.0000', 'T005_Valor_Total' => '76.8800', 'T005_Valor_Frete' => '26.8800', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024565 Codigo pedido Marketplace:20235051701 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '24713', 'T005_Id_Pedido_Externo' => 'order005', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '20235051701', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '21', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'BOLETOBANCÁRIO', 'D024' => array ( 'D024_Cpf' => '10942055918', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'REBECCA CANETE DE FARIAS', 'D024_Nome_Empresa' => 'REBECCA CANETE DE FARIAS', 'D024_Endereco' => 'Rua das Orcas', 'D024_Numero' => '566', 'D024_Cep' => '85855470', 'D024_Complemento' => '', 'D024_Bairro' => 'Parque Ouro Verde', 'Cidade' => 'CURITIBA', 'UF' => 'PR', 'Pais' => 'BR', 'D024_E_Mail' => 'atendimento+OS_021030550712@mktp.extra.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '45988106231', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'atendimento+OS_021030550712@mktp.extra.com.br', 'UF' => 'PR', 'Cidade' => 'CURITIBA', 'Pais' => 'BR', 'D148_Nome' => 'de Farias', 'D148_Cep' => '85855470', 'D148_Rua' => 'Rua das Orcas', 'D148_Numero' => '566', 'D148_Complemento' => '', 'D148_Bairro' => 'Parque Ouro Verde', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '45988106231', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Protetor facial policarbonato incolor 200mm com carneira sem catraca ca13540 - Vonder', 'T006_Quantidade' => '10', 'T006_Valor_Preco_Original' => '25.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 25.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 25.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024565', 'T006_Preco_Venda_Ecommerce' => 25.0, 'T006_Preco_Tabela_Ecommerce' => '25.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'VONDER', ), 1 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1043387', 'T006_Descricao_Produto' => 'Protetor facial policarbonato incolor 200mm com carneira sem catraca ca13540 - Vonder', 'T006_Quantidade' => '20', 'T006_Valor_Preco_Original' => '25.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 25.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 25.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024565', 'T006_Preco_Venda_Ecommerce' => 25.0, 'T006_Preco_Tabela_Ecommerce' => '25.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'VONDER', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '21', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 76.88, 'T090_Prazos_Dias' => 30, ), ), ),
					// /* ORDER 006 CURITIBA, ECCOFER -> QUANTIDADE 999999 */		array ( 'T005_Prazo_Entrega' => '31/03/20 08:43:34', 'T005_Valor_Total_Produtos' => '100.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '100.0000', 'T005_Valor_Total' => '148.4000', 'T005_Valor_Frete' => '48.4000', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024569 Codigo pedido Marketplace:8216500566286772 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '24717', 'T005_Id_Pedido_Externo' => 'order006', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '8216500566286772', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '29', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'CARTÃODECRÉDITO', 'D024' => array ( 'D024_Cpf' => '06397502902', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Georgia Trevisani', 'D024_Nome_Empresa' => 'Georgia Trevisani', 'D024_Endereco' => 'VIRGILIO TREVISANI', 'D024_Numero' => '193', 'D024_Cep' => '89464038', 'D024_Complemento' => 'Casa Proximo bodega bar', 'D024_Bairro' => 'JARDIM ESPERANCA', 'Cidade' => 'CURITIBA', 'UF' => 'SC', 'Pais' => 'BR', 'D024_E_Mail' => 'LU-8216500566286772@alias.integracommerce.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '47996361312', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'LU-8216500566286772@alias.integracommerce.com.br', 'UF' => 'SC', 'Cidade' => 'CURITIBA', 'Pais' => 'BR', 'D148_Nome' => 'Georgia Trevisani', 'D148_Cep' => '89464038', 'D148_Rua' => 'VIRGILIO TREVISANI', 'D148_Numero' => '193', 'D148_Complemento' => 'Casa Proximo bodega bar', 'D148_Bairro' => 'JARDIM ESPERANCA', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '47996361312', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Protetor facial policarbonato incolor 200mm com carneira sem catraca ca13540 - Vonder', 'T006_Quantidade' => '5', 'T006_Valor_Preco_Original' => '25.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 25.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 100.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024569', 'T006_Preco_Venda_Ecommerce' => 25.0, 'T006_Preco_Tabela_Ecommerce' => '25.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'ECCOFER', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '29', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 148.4, 'T090_Prazos_Dias' => 30, ), ), 'MagentoSoap_T005_C004_Id' => 2, ),
					// /* ORDER 007 ALMIRANTE, KOMELON -> QUANTIDADE 40 */			array ( 'T005_Prazo_Entrega' => '03/04/20 05:12:32', 'T005_Valor_Total_Produtos' => '57.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '57.0000', 'T005_Valor_Total' => '169.3800', 'T005_Valor_Frete' => '112.3800', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024776 Codigo pedido Marketplace:8213500565908489 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '24923', 'T005_Id_Pedido_Externo' => 'order007', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '8213500565908489', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '29', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'BOLETOBANCARIO', 'D024' => array ( 'D024_Cpf' => '07517784862', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'CLARICE HIROMI SUGITA', 'D024_Nome_Empresa' => 'CLARICE HIROMI SUGITA', 'D024_Endereco' => 'JOAO GRAEBER', 'D024_Numero' => '131', 'D024_Cep' => '03277070', 'D024_Complemento' => 'trav Av. vila ema', 'D024_Bairro' => 'VILA EMA', 'Cidade' => 'ALMIRANTE', 'UF' => 'SP', 'Pais' => 'BR', 'D024_E_Mail' => 'LU-8213500565908489@alias.integracommerce.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '11972041577', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'LU-8213500565908489@alias.integracommerce.com.br', 'UF' => 'SP', 'Cidade' => 'ALMIRANTE', 'Pais' => 'BR', 'D148_Nome' => 'CLARICE HIROMI SUGITA', 'D148_Cep' => '03277070', 'D148_Rua' => 'JOAO GRAEBER', 'D148_Numero' => '131', 'D148_Complemento' => ' trav Av. vila ema', 'D148_Bairro' => 'VILA EMA', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '11972041577', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Protetor facial policarbonato incolor 160mm - Nove54', 'T006_Quantidade' => '30', 'T006_Valor_Preco_Original' => '19.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 19.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 57.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024776', 'T006_Preco_Venda_Ecommerce' => 19.0, 'T006_Preco_Tabela_Ecommerce' => '19.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'KOMELON', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '29', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 169.38, 'T090_Prazos_Dias' => 30, ), ), 'MagentoSoap_T005_C004_Id' => 2, ),
					// /* ORDER 008 ECCOFER BELBRINK*/								array ( 'T005_Prazo_Entrega' => '02/04/20 05:12:35', 'T005_Valor_Total_Produtos' => '133.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '133.0000', 'T005_Valor_Total' => '209.6100', 'T005_Valor_Frete' => '76.6100', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024777 Codigo pedido Marketplace:8214500565970111 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '24924', 'T005_Id_Pedido_Externo' => 'order008', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '8214500565970111', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '29', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'BOLETOBANCARIO', 'D024' => array ( 'D024_Cpf' => '09993114847', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Viviane pereira marques', 'D024_Nome_Empresa' => 'Viviane pereira marques', 'D024_Endereco' => 'CONCEICAO', 'D024_Numero' => '841', 'D024_Cep' => '13025355', 'D024_Complemento' => '91 Centro de convivencia', 'D024_Bairro' => 'CAMBUI', 'Cidade' => '.', 'UF' => 'SP', 'Pais' => 'BR', 'D024_E_Mail' => 'LU-8214500565970111@alias.integracommerce.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '19981394224', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'LU-8214500565970111@alias.integracommerce.com.br', 'UF' => 'SP', 'Cidade' => '.', 'Pais' => 'BR', 'D148_Nome' => 'Viviane pereira marques', 'D148_Cep' => '13025355', 'D148_Rua' => 'CONCEICAO', 'D148_Numero' => '841', 'D148_Complemento' => '91 Centro de convivencia', 'D148_Bairro' => 'CAMBUI', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '19981394224', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1043387', 'T006_Descricao_Produto' => 'Protetor facial policarbonato incolor 160mm - Nove54', 'T006_Quantidade' => '999999', 'T006_Valor_Preco_Original' => '19.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 19.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 133.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024777', 'T006_Preco_Venda_Ecommerce' => 19.0, 'T006_Preco_Tabela_Ecommerce' => '19.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'VONDER', ), 1 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1001653', 'T006_Descricao_Produto' => 'Protetor facial policarbonato incolor 160mm - Nove54', 'T006_Quantidade' => '9999999', 'T006_Valor_Preco_Original' => '19.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 19.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 133.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024777', 'T006_Preco_Venda_Ecommerce' => 19.0, 'T006_Preco_Tabela_Ecommerce' => '19.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'VONDER', ),), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '29', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 209.61, 'T090_Prazos_Dias' => 30, ), ), 'MagentoSoap_T005_C004_Id' => 2, ),
					// /* ORDER 009 */												array ( 'T005_Prazo_Entrega' => '23/04/20 23:59:59', 'T005_Valor_Total_Produtos' => '5.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '5.0000', 'T005_Valor_Total' => '30.2000', 'T005_Valor_Frete' => '25.2000', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100024998 Codigo pedido Marketplace:701 5204476 6389860 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '25145', 'T005_Id_Pedido_Externo' => 'order009', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '701 5204476 6389860', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '37', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'CARTÃODECRÉDITO', 'D024' => array ( 'D024_Cpf' => '02501377710', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Ronaldo Campos e Silva', 'D024_Nome_Empresa' => 'Ronaldo Campos e Silva', 'D024_Endereco' => 'Rua Carlos Gois', 'D024_Numero' => '401/501', 'D024_Cep' => '22440040', 'D024_Complemento' => '', 'D024_Bairro' => 'Leblon', '.' => 'Leblon', 'UF' => 'RJ', 'Pais' => 'BR', 'D024_E_Mail' => 'w831z4llj639qr5@marketplace.amazon.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '2125356504', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'w831z4llj639qr5@marketplace.amazon.com.br', 'UF' => 'RJ', 'Cidade' => '.', 'Pais' => 'BR', 'D148_Nome' => 'Ronaldo Campos e Silva', 'D148_Cep' => '22440040', 'D148_Rua' => 'Rua Carlos Gois', 'D148_Numero' => '401/501', 'D148_Complemento' => '', 'D148_Bairro' => 'Leblon', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '2125356504', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Pulverizador spray 450ml jato regulável - Belfix', 'T006_Quantidade' => '9999', 'T006_Valor_Preco_Original' => '5.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 5.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 5.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100024998', 'T006_Preco_Venda_Ecommerce' => 5.0, 'T006_Preco_Tabela_Ecommerce' => '5.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'VONDER', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '37', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 30.2, 'T090_Prazos_Dias' => 30, ), ), ),
					// /* ORDER 010 */												array ( 'T005_Prazo_Entrega' => '20/04/20 23:59:59', 'T005_Valor_Total_Produtos' => '39.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '39.0000', 'T005_Valor_Total' => '67.6000', 'T005_Valor_Frete' => '28.6000', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100025026 Codigo pedido Marketplace:701 1960935 6242647 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '25173', 'T005_Id_Pedido_Externo' => 'order010', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '701 1960935 6242647', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '37', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'OUTROS', 'D024' => array ( 'D024_Cpf' => '36968693844', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Lenita Salgado', 'D024_Nome_Empresa' => 'Lenita Salgado', 'D024_Endereco' => 'Rua Ministro Godói', 'D024_Numero' => '1020', 'D024_Cep' => '05015001', 'D024_Complemento' => 'apartamento 51', 'D024_Bairro' => 'Perdizes', 'Cidade' => '.', 'UF' => 'SP', 'Pais' => 'BR', 'D024_E_Mail' => '2lvk8t7hg85cb56@marketplace.amazon.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '11976067733', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => '2lvk8t7hg85cb56@marketplace.amazon.com.br', 'UF' => 'SP', 'Cidade' => '.', 'Pais' => 'BR', 'D148_Nome' => 'Lenita Salgado', 'D148_Cep' => '05015001', 'D148_Rua' => 'Rua Ministro Godói', 'D148_Numero' => '1020', 'D148_Complemento' => 'apartamento 51', 'D148_Bairro' => 'Perdizes', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '11976067733', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1045271', 'T006_Descricao_Produto' => 'Kit cesta de legumes com faca com 11 peças - Bel Brink', 'T006_Quantidade' => '9999', 'T006_Valor_Preco_Original' => '39.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 39.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 39.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100025026', 'T006_Preco_Venda_Ecommerce' => 39.0, 'T006_Preco_Tabela_Ecommerce' => '39.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'NOVE54', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '37', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 67.6, 'T090_Prazos_Dias' => 30, ), ), ),
					// /* ORDER 011 */												array ( 'T005_Prazo_Entrega' => '20/04/20 23:59:59', 'T005_Valor_Total_Produtos' => '39.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '39.0000', 'T005_Valor_Total' => '67.6000', 'T005_Valor_Frete' => '28.6000', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100025063 Codigo pedido Marketplace:701 6585909 0055452 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '25210', 'T005_Id_Pedido_Externo' => 'order011', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '701 6585909 0055452', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '37', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'CARTÃODECRÉDITO', 'D024' => array ( 'D024_Cpf' => '31407546805', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Fabiana Terlizzi', 'D024_Nome_Empresa' => 'Fabiana Terlizzi', 'D024_Endereco' => 'Rua Carlos Weber', 'D024_Numero' => '890', 'D024_Cep' => '05303000', 'D024_Complemento' => '234B Vila Leopoldina', 'D024_Bairro' => 'Vila Leopoldina', 'Cidade' => '.', 'UF' => 'SP', 'Pais' => 'BR', 'D024_E_Mail' => 'y9fdp4740f6y9np@marketplace.amazon.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '11976061602', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'y9fdp4740f6y9np@marketplace.amazon.com.br', 'UF' => 'SP', 'Cidade' => '.', 'Pais' => 'BR', 'D148_Nome' => 'Fabiana Terlizzi', 'D148_Cep' => '05303000', 'D148_Rua' => 'Rua Carlos Weber', 'D148_Numero' => '890', 'D148_Complemento' => '234B Vila Leopoldina', 'D148_Bairro' => 'Vila Leopoldina', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '11976061602', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Kit cesta de legumes com faca com 11 peças - Bel Brink', 'T006_Quantidade' => '9999', 'T006_Valor_Preco_Original' => '39.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 39.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 39.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100025063', 'T006_Preco_Venda_Ecommerce' => 39.0, 'T006_Preco_Tabela_Ecommerce' => '39.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'VONDER PLUS', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '37', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 67.6, 'T090_Prazos_Dias' => 30, ), ), ),
					// /* ORDER 012 QUANTIDADE 1337*/								array ( 'T005_Prazo_Entrega' => '23/04/20 00:00:00', 'T005_Valor_Total_Produtos' => '73.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '73.0000', 'T005_Valor_Total' => '93.7600', 'T005_Valor_Frete' => '20.7600', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100025140 Codigo pedido Marketplace:2371380619 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '25287', 'T005_Id_Pedido_Externo' => 'order012', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '2371380619', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '23', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'MERCADOPAGO', 'D024' => array ( 'D024_Cpf' => '10456522883', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Adílson Fernandes de Freitas', 'D024_Nome_Empresa' => 'Adílson Fernandes de Freitas', 'D024_Endereco' => 'Rua Barueri', 'D024_Numero' => '170', 'D024_Cep' => '07713115', 'D024_Complemento' => 'casa 4', 'D024_Bairro' => 'Serpa', 'Cidade' => 'Caieiras', 'UF' => 'SP', 'Pais' => 'BR', 'D024_E_Mail' => 'afreita.dsjj8z+2-ogiztomjthaydmnbq@mail.mercadolivre.com', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '1144420758', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => 'afreita.dsjj8z+2-ogiztomjthaydmnbq@mail.mercadolivre.com', 'UF' => 'SP', 'Cidade' => 'Caieiras', 'Pais' => 'BR', 'D148_Nome' => 'Adílson Fernandes de Freitas', 'D148_Cep' => '07713115', 'D148_Rua' => 'Rua Barueri', 'D148_Numero' => '170', 'D148_Complemento' => 'casa 4', 'D148_Bairro' => 'Serpa', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '1144420758', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Lamina serra circular vídea 200x30x3,0mm 48 dentes 6600 rpm para madeira - Vonder', 'T006_Quantidade' => '1337.0000', 'T006_Valor_Preco_Original' => '73.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 73.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 73.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100025140', 'T006_Preco_Venda_Ecommerce' => 73.0, 'T006_Preco_Tabela_Ecommerce' => '73.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'ECCOFER', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '23', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 93.76, 'T090_Prazos_Dias' => 30, ), ), ),
					// /* ORDER 013 QUANTIDADE 1337*/								array ( 'T005_Prazo_Entrega' => '27/04/20 23:59:59', 'T005_Valor_Total_Produtos' => '39.0000', 'T005_Valor_Total_Produtos_Sem_Desconto' => '39.0000', 'T005_Valor_Total' => '80.2000', 'T005_Valor_Frete' => '41.2000', 'T005_Observacao_Nota_Fiscal' => 'Codigo pedido magento: 100025219 Codigo pedido Marketplace:702 5032500 1128239 Dados da entrega:NÃO IDENTIFICADA', 'Id_Loja' => '1', 'T005_Pedido_Ecommerce' => '25366', 'T005_Id_Pedido_Externo' => 'order013', 'C007_Id_Vendedor_Interno' => '288', 'T005_Ordem_Compra' => '702 5032500 1128239', 'T005_Flag_Plataforma_Ecommerce' => 3, 'T005_D022_Id' => '', 'T005_D036_Id' => '', 'T005_D027_Id' => '37', 'T005_Nome_Transportadora' => 'NÃO IDENTIFICADA', 'T005_Canal_Vendas_Ecommerce' => '.', 'Comment_Forma_de_Pagamento' => 'CARTÃODECRÉDITO', 'D024' => array ( 'D024_Cpf' => '22520603879', 'D024_Cnpj' => '', 'D024_Inscricao_Estadual' => '', 'D024_Nome_Fantasia' => 'Diana Oliveira', 'D024_Nome_Empresa' => 'Diana Oliveira', 'D024_Endereco' => 'Rua Doutor Antônio da Costa Carvalho, 500', 'D024_Numero' => '500', 'D024_Cep' => '13024900', 'D024_Complemento' => '500, 500 - Apto 151 Cambuí', 'D024_Bairro' => 'Cambuí', 'Cidade' => 'Campinas', 'UF' => 'SP', 'Pais' => 'BR', 'D024_E_Mail' => '8y1k28tnv30v89v@marketplace.amazon.com.br', 'D024_DDD_Telefone_1' => '', 'D024_Telefone_1' => '62999010823', 'D024_DDD_Telefone_2' => '', 'D024_Telefone_2' => NULL, 'D024_Web_Site' => '', 'C007_Id_Vendedor_Interno' => '288', 'D024_Flag_Contribuinte' => 0, 'D148' => array ( 'D148_Email' => '8y1k28tnv30v89v@marketplace.amazon.com.br', 'UF' => 'SP', 'Cidade' => 'Campinas', 'Pais' => 'BR', 'D148_Nome' => 'Diana Oliveira', 'D148_Cep' => '13024900', 'D148_Rua' => 'Rua Doutor Antônio da Costa Carvalho, 500', 'D148_Numero' => '500', 'D148_Complemento' => '500, 500 - Apto 151 Cambuí', 'D148_Bairro' => 'Cambuí', 'D148_DDD_Telefone' => '', 'D148_Telefone' => '62999010823', 'D148_DDD_Fax' => '', 'D148_Fax' => NULL, ), ), 'T006' => array ( 0 => array ( 'Peso_Unitario' => '0.0000', 'T006_Codigo_Produto' => '1051210', 'T006_Descricao_Produto' => 'Kit cesta de legumes com faca com 11 peças - Bel Brink', 'T006_Quantidade' => '1337.0000', 'T006_Valor_Preco_Original' => '39.0000', 'T006_Valor_Preco_Sem_Desconto_Unitario' => 39.0, 'T006_Valor_Total_Preco_Sem_Desconto' => 39.0, 'T006_Percentual_Desconto' => '0.0000', 'Codigo_Barras' => '', 'T006_Numero_Pedido_Compra' => '100025219', 'T006_Preco_Venda_Ecommerce' => 39.0, 'T006_Preco_Tabela_Ecommerce' => '39.0000', 'T006_Valor_Desconto_Ecommerce' => '0.0000', 'D082_Marca' => 'BEL BRINK', ), ), 'T090' => array ( 0 => array ( 'T090_D027_Id' => '37', 'T090_Numero_Parcela' => 1, 'T090_Data_Vencimento' => '2022-05-25', 'T090_Valor_Parcela' => 80.2, 'T090_Prazos_Dias' => 30, ), ), )
					
					);

					// Pedido manipulado
					//$T005 = $pedidosManipulados[$i];
				}

				// Start Filtro Novo

				// Varíavel que define se o pedido possui os filtros, TRUE = PEDIDO SE ENCAIXA COM FILTROS, FALSE = PEDIDO NÃO SE ENCAIXA COM OS FILTROS
				$encontrou = true;

				$retornoFiltro = $MagentoSoap->filtroPedido($T005);

				if(!empty($retornoFiltro['targetEmpresa']) && gettype($retornoFiltro['targetEmpresa']) === 'integer')
				{
					$targetEmpresa = $retornoFiltro['targetEmpresa'];

					if(!empty($retornoFiltro['filtroUtilizado']))
					{
						$T005['MAGENTOSOAP_filtroUtilizado'] = $retornoFiltro['filtroUtilizado'];
					}

					$encontrou = true;
				}
				else
				{
					$encontrou = false;
				}
				log($retornoFiltro['finalLog']);
				
				// Fim filtro novo

				if($encontrou === false) {
					log('Loop filtro continue');
					if(!empty($resposta['naoFiltrado'])){
						$naoFiltrado.=', ';
					}
					$array_data = explode(',',$resposta['naoFiltrado']);
					if(count($array_data) % 10 == 0){
						$naoFiltrado.="<br/>";
					}
					$naoFiltrado.= $T005["T005_Id_Pedido_Externo"] . "-" . $retornoFiltro['msgAjax'];
					$qteNaoFiltrado++;	                
					$tratados++;

				}else{
					$retorno = $VEN014->cadastrarPedido($T005, $targetEmpresa);
					if($retorno === false){
						$erro++;
						$fracasso .= $T005["T005_Id_Pedido_Externo"] . ", ";
					}else{
						if (!empty($resposta['sucesso'])){
							$sucesso.=', ';
							$array_data = explode(',',$resposta['sucesso']);
							if(count($array_data) % 10 == 0){
								$sucesso.="<br/>";
							}
						} 
						$sucesso.= $T005["T005_Id_Pedido_Externo"] . "-" . $retornoFiltro['msgAjax'];
						$ok++;
					} 
					$tratados++;
				}
            }  
        }
        
		$resposta['repetidos'] += $repetidos;
		$resposta['tratados'] += $tratados;
		$resposta['erro'] += $erro;
		$resposta['ok'] += $ok;
		$resposta['sucesso'] .= $sucesso;
		$resposta['fracasso'] .= $fracasso;
		$resposta['naoFiltrado'] .= $naoFiltrado;
		$resposta['qteNaoFiltrado'] += $qteNaoFiltrado;

		//CÁLCULO PARA VERIFICAR O TEMPO DE PROCESSAMENTO
		$finalProcesso = date('Y-m-d H:i:s');
		$sql = mysql_query("SELECT TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, '{$resposta['inicioProcesso']}', '{$finalProcesso}')),'%H:%i:%s')");
		$duracao = mysql_fetch_array($sql);
		$resposta['duracao'] = $duracao[0];

		if($resposta['etapa'] >= $resposta['etapas']){
            if(empty($resposta['sucesso'])){
				$resposta['sucesso'] = "Nenhum";
			}
            if(empty($resposta['fracasso'])){
				$resposta['fracasso'] = "Nenhum";
			}	
			if(empty($resposta['naoFiltrado'])){
				$resposta['naoFiltrado'] = "Nenhum";
          	}	 		
			unlink("{$Dir}cachePedidosBuscarMagento-{$g['usuarioAtual']}.txt");
			// apc_delete("cachePedidosBuscarMagento-{$g['usuarioAtual']}");
			apc_delete("cachePedidosBuscarMagento-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cachePedidosBuscarMagento-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cachePedidosBuscarMagento-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}
	break;
	
	case 'est014VerificaGerarOC':
		$sql = base64_decode($r_sql);
		$sql = str_replace("\'", "'", $sql);
		$retorno = mysql_query($sql);
		$retorno2 = mysql_query($sql);
		
		echo mysql_error();
		$row = mysql_fetch_array($retorno);
		$D024 = $row['D024_Id'];        
		
		while($row2 = mysql_fetch_array($retorno2)){
			if($D024 != $row2['D024_Id']){
				$resposta['code'] = false;
				$resposta['data'] = "Fornecedores selecionados não são os mesmos";
			} else {
				$resposta['code'] = true;
				$resposta['data'] = "Você tem certeza que deseja gerar uma ordem de compra?";
				$resposta['D024_Id'] = $D024;
			}
		}   
	break;
	
	case 'CheckboxFlagIndisponivel':
		global $g;
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
		$EST008->updateT174_Flag_Indisponivel($T174_Id, $selected);
	break;
	
	case 'CheckboxFlagGerarOC':
		global $g;
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T174_Id = isset($_REQUEST['T174_Id']) ? $_REQUEST['T174_Id'] : false;
		$EST008->updateT174_Flag_Gerar_OC($T174_Id, $selected);
	break;
	
	case 'est014aplicarPrecoVenda':
		$T174_Id 		= isset($r_T174_Id) ? $r_T174_Id : false;
		$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
		$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
		$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
		
		//echo "174: ".$T174_Id."<br />campoEditado: ".$campoEditado."<br />valorAntigo: ".$valorAntigo."<br />NovoValor".$novoValor;
		
		$retorno = $EST014->est014AplicarIPV($T174_Id, $campoEditado, $valorAntigo, $novoValor);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'crm001checkGerarOrcamento':
		global $g;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$T198_Id = isset($r_T198_Id) ? $r_T198_Id : false;
		
		if($T198_Id == 0){
			$insT198 = <<<EOT
			INSERT INTO T198 (T198_D009_Id, T198_C007_Id)
			VALUES
			('{$D009_Id}',
			'{$g['usuarioAtual']}');
EOT;
			$retorno = mysql_query($insT198);
		}else{
			$delT198 = <<<EOT
			DELETE FROM T198 WHERE T198_Id='{$T198_Id}';
EOT;
			$retorno = mysql_query($delT198);
		}
			
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
		break;
	
	case 'crm001checkDesmarcarTodos':
		$retorno = mysql_query("DELETE FROM T198");
		$erro = mysql_error();
		if ($retorno == true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		}
		break;
	
	case 'crm001checkMarcarTodos':
		global $g;
		$sqlFiltro = (isset($r_sqlFiltro)) ? $r_sqlFiltro : false;
		$sqlFiltroNormal = base64_decode($sqlFiltro);
		$sqlFiltroNormal = str_replace("\\", "", $sqlFiltroNormal);
		
		$retorno = mysql_query($sqlFiltroNormal);
		
		while($linha = mysql_fetch_array($retorno)){
			$T198 = <<<EOT
			INSERT INTO T198 (
				T198_D009_Id,
				T198_C007_Id
			) VALUES (
				'{$linha['D009_Id']}',
				'{$g['usuarioAtual']}'
			);
EOT;
			$retIns = mysql_query($T198);
			$erro = mysql_error();
		}
		if($retIns){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		}
	break;

	case 'crm001CheckboxDuplicarPedido':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		$T005=mysql_query("UPDATE T005 SET T005_Flag_Marcado='{$selected}' where T005_Id='{$T005_Id}'");
		if($T005_Id){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
		}
	break;

	case 'crm001CheckboxGerarNF':

		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		$sqlT005 = mysql_query("SELECT * FROM T005 WHERE T005_Id = '{$T005_Id}'");
		$T005 = mysql_fetch_array($sqlT005);
		$T005['T005_Flag_Gerar_NF'] = (empty($T005['T005_Flag_Gerar_NF'])) ? 'N' : $T005['T005_Flag_Gerar_NF'];
		if(($T005['T005_Flag_Gerar_NF'] == 'S' && ($T005['T005_C007_Gerar_NF'] == $g['usuarioAtual'] || empty($T005['T005_C007_Gerar_NF']))) || $T005['T005_Flag_Gerar_NF'] == 'N'){
			if($selected == 'S'){ $usuario = $g['usuarioAtual']; } else { $usuario = 0; }
			mysql_query("UPDATE T005 SET T005_Flag_Gerar_NF='{$selected}', T005_C007_Gerar_NF = '{$usuario}' where T005_Id='{$T005_Id}'");
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
		
	break;

	case 'crm001ClientesProcessar':
		global $g;
		require_once('bibliotecas/classes/VEN001.php');
		$VEN001 = new VEN001();
		$retorno = $VEN001->VEN001ClientesProcessar();

		if($retorno===true){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'abreXMLPedidos':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;		

		$cArquivo = "{$g['pathDados']}/ecommerce/xmlPedido/PEDIDO_{$T005_Id}.xml";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="PEDIDO_' . $T005_Id . '.xml"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte técnico.";
			return false;
		}
		die();
	break;

	case 'crm001SelecionarTodos':
		$extra = isset($r_extra) ? $r_extra : false;
		$sqlFiltroNormal = base64_decode($extra);
		$extra = str_replace("\\", "", $sqlFiltroNormal);
		$extra = gPegarParteWhere($extra);
		mysql_query("UPDATE T005 SET T005_Flag_Marcado = 'S' WHERE {$extra}");
		$resposta['code'] = true;
	break;

	case 'crm001MarcarTodosPedidos':
		$extra = isset($r_extra) ? $r_extra : false;
		$sqlFiltroNormal = base64_decode($extra);
		$extra = str_replace("\\", "", $sqlFiltroNormal);
		$extra = gPegarParteWhere($extra);
		$sql = "UPDATE T005 
		LEFT JOIN D024 AS Cliente ON Cliente.D024_Id=T005_D024_Id
		LEFT JOIN D020 ON D020_Id=D024_D020_Id
		LEFT JOIN D018 ON D018_Id=D024_D018_Id
		LEFT JOIN D021 ON D021_Id=T005_D021_Id
		LEFT JOIN D022 ON D022_Id=T005_D022_Id
		LEFT JOIN D006 ON D006_Id=T005_D006_Id
 		LEFT JOIN D024 AS ClienteNF ON ClienteNF.D024_Id = T005_D024_Id_NFe
		LEFT JOIN C007 AS Vendedor ON Vendedor.C007_ID=T005_C007_Id_Vendedor_Interno
		LEFT JOIN C004 ON C004_Id=T005_C004_Id
		LEFT JOIN D036 ON T005_D036_Id=D036_Id 
		LEFT JOIN T007 ON T007_T005_Id=T005_Id AND T007_FLAG_CANCELADA!='S'
		LEFT JOIN C007 AS Liberou ON Liberou.C007_Id=T005_Liberacao_Credito_Id
		LEFT JOIN C007 AS Separou ON Separou.C007_Id=T005_Usuario_Pecas 
		SET T005_Flag_Gerar_NF = 'S', T005_C007_Gerar_NF = '{$g['usuarioAtual']}' WHERE {$extra} 
		AND T005_C004_Id = '{$g['empresaAtual']}' 
		AND (T005_C007_Gerar_NF = 0 OR T005_C007_Gerar_NF IS NULL)";
		mysql_query($sql);
		$resposta['code'] = true;
	break;

	case 'crm001DesmarcarTodosPedidos':
		$extra = isset($r_extra) ? $r_extra : false;
		$sqlFiltroNormal = base64_decode($extra);
		$extra = str_replace("\\", "", $sqlFiltroNormal);
		$extra = gPegarParteWhere($extra);
		mysql_query("UPDATE T005 SET T005_Flag_Gerar_NF = 'N', T005_C007_Gerar_NF = 0 WHERE T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
		$resposta['code'] = true;
	break;

	case 'crm001VerificaGerarNotaFiscal':
		$resposta['code'] = true;
		$continuar = true;

		$res = mysql_query("SELECT T005_D024_Id FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
		if(mysql_num_rows($res) == 0){
			$resposta['data'] = "Nenhum pedido selecionado";
			$resposta['code'] = false;
			$continuar = false;
		}

		if ($continuar) {
			$res = mysql_query("SELECT DISTINCT T005_Flag_ACP FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
			if(mysql_num_rows($res) > 1){
				$resposta['data'] = "Foi selecionado mais de um tipo de documento";
				$resposta['code'] = false;
				$continuar = false;
			}
		}

		if ($continuar) {
			// Tira matrizes diferentes
			$matriz = false;
			$count=1;
			$res = mysql_query("SELECT T005_D024_Id, T005_D024_Id_Nfe FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND (T005_D024_Id_Nfe IS NULL OR T005_D024_Id_Nfe = '') AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
			log("SQL: ","SELECT T005_D024_Id, T005_D024_Id_Nfe FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND (T005_D024_Id_Nfe IS NULL OR T005_D024_Id_Nfe = '') AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
			while ($row = mysql_fetch_assoc($res)) {
				if ($matriz && $matriz != $row['T005_D024_Id']) { 
					log("COUNT: ",$count);
					$resposta['data'] = "Pedidos de matrizes diferentes";
					$resposta['code'] = false;
					$continuar = false;
				}
				$matriz = $row['T005_D024_Id'];
				$count++;
			}
		}

		// Ou tem matriz ou não tem
		if ($continuar) {
			// Se tiver matriz
			if ($matriz) {
				$res = mysql_query("SELECT T005_D024_Id, T005_D024_Id_Nfe FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND (T005_D024_Id_Nfe IS NOT NULL AND T005_D024_Id_Nfe != '') AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
				while ($row = mysql_fetch_assoc($res)) {
					if ($row['T005_D024_Id_Nfe'] != $matriz) {
						$resposta['data'] = "Empresas diferentes";
						$resposta['code'] = false;
						$continuar = false;
					}
				}
			} else {
				$T005_D024_Id_Nfe = false;
				$res = mysql_query("SELECT T005_D024_Id, T005_D024_Id_Nfe FROM T005 WHERE T005_Flag_Gerar_NF = 'S' AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}'");
				while ($row = mysql_fetch_assoc($res)) {
					if (!$T005_D024_Id_Nfe) { $T005_D024_Id_Nfe = $row['T005_D024_Id_Nfe']; }
					if ($row['T005_D024_Id_Nfe'] != $T005_D024_Id_Nfe) {
						$resposta['data'] = "Empresas diferentes";
						$resposta['code'] = false;
						$continuar = false;
					}
				}
			}
		}
	break;

	case 'crm001GerarNotaFiscal':
		//Verifica qual pedido vai ser utilizado como cabeçalho
		$sqlT005 = mysql_query("SELECT T005_Id,T007_Id FROM T005 
			LEFT JOIN T007 on T007_T005_Id=T005_Id
		WHERE T005_Flag_Gerar_NF = 'S' AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}' AND (T007_Id IS NULL OR T007_Flag_Cancelada = 'S') AND T005_Flag_Status!=8;");
		log("SELECT T005_Id,T007_Id FROM T005 
			LEFT JOIN T007 on T007_T005_Id=T005_Id
		WHERE T005_Flag_Gerar_NF = 'S' AND T005_C007_Gerar_NF = '{$g['usuarioAtual']}' AND T005_C004_Id = '{$g['empresaAtual']}' AND (T007_Id IS NULL OR T007_Flag_Cancelada = 'S')AND T005_Flag_Status!=8;");
		$T005 = array();
		while($res = mysql_fetch_array($sqlT005)){
			$T005[] .= $res['T005_Id'];
		}
		require_once('bibliotecas/classes/VEN012.php');
		$VEN012 = new VEN012();
		$retorno = $VEN012->gerarNotaFiscalPedidos($T005);
		if($retorno !== true){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
		}
	break;

	case 'crm001DesmarcarTodos':
		$extra = isset($r_extra) ? $r_extra : false;
		$sqlFiltroNormal = base64_decode($extra);
		$extra = str_replace("\\", "", $sqlFiltroNormal);
		$extra = gPegarParteWhere($extra);
		mysql_query("UPDATE T005 SET T005_Flag_Marcado = 'N' WHERE {$extra}");
		$resposta['code'] = true;
	break;

	case 'crm001CheckBoxEnviarAgenda':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		mysql_query("UPDATE D024 SET D024_Flag_Enviar_Agenda='{$selected}' WHERE D024_Id='{$D024_Id}'");
		$resposta['code'] = true;
	break;

	case 'incluirProdutosTabelaPrecoCliente':
		if(empty($r_D161_Id)){
			$resposta['data'] = "O cliente não foi selecionado!";
			$resposta['code'] = false;
			break;
		}

		$rowD024 = mysql_fetch_assoc(mysql_query("SELECT D024_Id, D024_Flag_Lista_Preco FROM D024 LEFT JOIN D161 ON D161_D024_Id=D024_Id WHERE D161_Id = '{$r_D161_Id}'"));

		if(empty($rowD024['D024_Flag_Lista_Preco'])){
			$resposta['data'] = "A lista de preço do cadastro do cliente está em branco, <br>preencha a lista na aba \"Comercial\" do cadastro do cliente e inclua os produtos novamente";
			$resposta['code'] = false;
			break;
		}
		$preco = "D009_Preco_{$rowD024['D024_Flag_Lista_Preco']}";

		$arrayD160 = array();
		$sqlD160 = mysql_query("SELECT D160_D009_Id FROM D160 WHERE D160_D161_Id = '{$r_D161_Id}'");
		while($rowD160 = mysql_fetch_assoc($sqlD160)){
			$arrayD160[] = $rowD160['D160_D009_Id'];
		}

		$extra = base64_decode($r_extraSql);
		$extra = gInsertExtraWhere($extra, "D009_Flag_Incluir_Produto = 'S' AND D009_C007_Id_Incluir_Produto = '{$g['usuarioAtual']}'");
		$extra = preg_split('/FROM/i', $extra);
		$extra = $extra[count($extra)-1];
		$extra = gRetirarGroupBy($extra);

		$sql = "SELECT D009_Id, D009_Preco_1, D009_Preco_2, D009_Preco_3, D009_Preco_4, D009A_Preco_5 FROM {$extra} GROUP BY D009_Id";
		$sqlD009 = mysql_query($sql);
		if(mysql_num_rows($sqlD009) > 0){
			while($rowD009 = mysql_fetch_assoc($sqlD009)){
				if(in_array($rowD009['D009_Id'], $arrayD160)) { continue; }
				mysql_query("INSERT INTO D160 (D160_D161_Id, D160_D024_Id, D160_D009_Id, D160_C007_Id, D160_Data_Hora, D160_Valor_Preco) VALUES ('{$r_D161_Id}', '{$rowD024['D024_Id']}', '{$rowD009['D009_Id']}', '{$g['usuarioAtual']}', CURRENT_TIMESTAMP(), '{$rowD009[$preco]}')");
			}
			mysql_query("UPDATE D009 SET D009_Flag_Incluir_Produto = 'N', D009_C007_Id_Incluir_Produto = '0' WHERE D009_C007_Id_Incluir_Produto = '{$g['usuarioAtual']}'");
			$resposta['code'] = true;
		} else {
			$resposta['data'] = "Não foi selecionado nenhum produto!";
			$resposta['code'] = false;
		}
	break;

	case 'crm001VerificaFaturarPedidos':
		$extra = base64_decode($r_extra);
		$extra = gInsertExtraWhere($extra, "T005_Flag_Marcado = 'S'");

		$sqlT005 = mysql_query("SELECT T005_Id FROM T005 {$extra}");
		$rows = mysql_num_rows($sqlT005);
		if($rows > 0){
			$resposta['code'] = true;
			$resposta['data'] = ($rows == 1) ? "Deseja faturar 1 pedido selecionado?" : "Deseja faturar {$rows} pedidos selecionados?";
		} else {
			$resposta['data'] = "Nenhum pedido foi selecionado para faturar!";
			$resposta['code'] = false;
		}
	break;

	case 'crm001FaturarPedidos':
		require_once('bibliotecas/classes/VEN002.php');
		$VEN002 = new VEN002();
		$retorno = $VEN002->faturarPedidosSelecionados($r_extra);

		if(!is_array($retorno)){
			$resposta['code'] = true;
			$resposta['data'] = 'Pedidos faturados com sucesso!';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Os pedidos abaixo não puderam ser faturados: <br>';
			foreach ($retorno as $T005_Id => $erro) {
				$resposta['data'] .= "{$T005_Id} - {$erro}<p>";
			}
		}
	break;

	case 'baixaCSVTabelaPreco':
		global $g;
		$pathinfo = "modeloCSVTabelaPreco.csv";
		$cArquivo = $confUsuario['urlRaiz'] . "crm/crm001/modeloCSVTabelaPreco.csv";
		$file = file_exists($cArquivo);

		$finfo = new \finfo();
	    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
	    header('Content-Disposition: attachment; filename="modeloCSVTabelaPreco.csv" ');
	    readfile($cArquivo);

		die();
	break;


	/**
	 * Atualiza status em massa das entregas do Intelipost.
	 * Recebe como paramêtro quais pedidos foram marcados pelo
	 * T005_Flag_Marcado
	 */
	case 'crm001AtualizarStatusIntelipost':

		// Caso seja a primeira requisição
		if(!is_array($r_extra))
		{
			/* Retira o Group By para conseguirmos adicionar a nossa condição
			e também para podermos usar COUNT(*) */
			$extra = gRetirarGroupBy(base64_decode($r_extra));

			// Salva os ids da query
			$idsQuery = mysql_query("SELECT T005_Id FROM T005 $extra AND T005_Flag_Marcado = 'S'"); # <------- MUDAR ESSA QUERY SE PRECISAR
			if($erro = mysql_error()){error_log($erro);log($erro);}

			$idArray = array_unique(array_column(mysqli_fetch_all($idsQuery), 0));
			$idStr = implode(',', $idArray);
			$contagem = count($idArray);

			if($contagem > 0)
			{
				// Desmarca todos os pedidos que foram previamente selecionados
				mysql_query("UPDATE T005 SET T005_Flag_Marcado = 'N' WHERE T005_Id IN ({$idStr})"); # <------- MUDAR ESSA QUERY SE PRECISAR
				if($erro = mysql_error()){error_log($erro);log($erro);}
			}

			/* Retorna a contagem 
			e os códigos dos pedidos que foram selecionados*/
			$resposta = array(
				'info' => array(
					'contagem' => $contagem,
					'cods' => base64_encode($idStr),
				),
			);
		}
		else
		{
			$codigos = explode(',', base64_decode($r_extra['cods'])); // Recebe os códigos do ajax
			$contagem = $r_extra['contagem']; // Extrai a contagem

			/* VARIAVEIS DA REPETIÇÃO */
			$mainId = array_splice($codigos, 0, 1)[0]; # <---- CHAVE PRIMARIA DA REPETIÇÃO
			$code = ''; # < --- QUAL CÓDIGO VAI RETORNAR PARA O AJAX
			$title = ''; # < --- QUAL TEXTO VAI RETORNAR PARA A CHAVE PRIMARIA

			if(count($codigos) > 0 || !empty($mainId))
			{
				/* CÓDIGO AQUI */
					require_once('bibliotecas/webservices/intelipost/apiIntelipost.php');
					$Intelipost = new Intelipost();

					/* Precisamos verificar se o T005_Id_Pedido_Externo
					existe, porque se ele não existir precisamos então enviar pelo id principal
					do pedido */
					$T005 = mysql_query("SELECT T005_Id_Pedido_Externo
										 FROM T005
										 WHERE T005_Id = '{$mainId}'");
					$T005_Id_Pedido_Externo = mysql_fetch_assoc($T005)['T005_Id_Pedido_Externo'];

					$mainId = (empty($T005_Id_Pedido_Externo) ? $mainId : $T005_Id_Pedido_Externo);

					$r = $Intelipost->BaixaPedidoIntelipost($mainId);

					/* Se deu certo baixar o pedido */
					if($r['code'])
					{		
						$r2 = $Intelipost->AtualizaStatus($mainId, $r['MACRO_STATUS'], $r['MICRO_STATUS'], $r['DATA_STATUS'], $r['DATA_PREVISAO_ENTREGA_TRANSPORTADORA'], $r['CODIGO_PLP']);

						// Status ok
						if($r2)
						{
							$code = 'ok';
						}
						else
						{
							/* Pedido já está com esse
							mesmo status, por tanto não precisa
							atualizar */
							$code = 'msmStatus';
						}
					}
					else
					{
						// Se caso der erro no download do pedido
						$code = 'erro';
						$title = (empty($r['erro']) ? '' : $r['erro']);
					}
				/* ----------- */
			}
			else
			{
				$code = 'concluido';
			}


			$resposta = array(
				'info' => array(
					'contagem' => $contagem,
					'cods' => base64_encode(implode(',', $codigos))
				),
				'data' => array(
					'mainId' => $mainId,
					'code' => $code,
					'title' => $title,
					'percent' => (int)((($contagem - count($codigos)) * 100) / $contagem),
					'etapa' => ($contagem - count($codigos)) . '/' . $contagem,
				)
			); 
		}

		break;
		
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
