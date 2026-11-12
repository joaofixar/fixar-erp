<?php
namespace hardness;
/**
 * Processamento de ações AJAX
 */
$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();
require_once('bibliotecas/classes/VEN001.php');
$VEN001 = new VEN001();
require_once('bibliotecas/classes/VEN002.php');
$VEN002 = new VEN002();
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'atualizarCamposProdutos':
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		/*
		T006_Ultimo_Preco(D001_Id,'$mT005[T003_D024_Id]','$T003_Id',1) as Preco_Consultado,
		T006_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',2) as Preco_Confirmado
		*/
			
		$sql =	"SELECT * from T005 WHERE T005_Id = '{$T005_Id}'";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		$T005_D006_Id = $row['T005_D006_Id'];
		$T005_Flag_Destino_Produto = $row['T005_Flag_Destino_Produto'];
		$T005_D022_Id = $row['T005_D022_Id'];
		$T005_T208_Id = $row['T005_T208_Id'];

		$sqlD024 = mysql_query("select D024_CNPJ_Matriz from D024 where D024_Id='{$row['T005_D024_Id']}'");
		$mD024 = mysql_fetch_assoc($sqlD024);		
		
		$sql = <<<EOT
			SELECT *
	   				from D001
	   			left join D049 on D049_D001_Id=D001_Id
	   			left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['T005_D024_Id']}'
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   			left join D069 on D069_Id=D009_D069_Id
			    left join D070 on D070_Id=D069_D070_Id
			    left join D002 on D002_Id=D001_D002_Id
			    left join D002A on D002_Id=D002A_D002_Id
	   			WHERE D009_Id = '{$D009_Id}'
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		if (empty($row['D083_Codigo_Produto_Fornecedor']) and !empty($mD024['D024_CNPJ_Matriz'])){

			$sqlD083 = mysql_query("select D083_Codigo_Produto_Fornecedor
									  from D083 
									 where D083_D001_Id='{$row['D001_Id']}'
									   and D083_D024_Id IN (select D024_Id FROM D024 WHERE D024_CNPJ_Matriz='{$mD024['D024_CNPJ_Matriz']}')
									   and D083_Codigo_Produto_Fornecedor!=''
									 limit 1");
			$mD083 = mysql_fetch_assoc($sqlD083);

			$row['D083_Codigo_Produto_Fornecedor'] = $mD083['D083_Codigo_Produto_Fornecedor'];
		
		}

		$row['validadePrecoTabela'] = 'true';
		if(strtotime($row['D070_Data_Vencimento']) < strtotime(date("Y-m-d"))){
			$row['validadePrecoTabela'] = 'false';
		}

		$row['estoqueLiquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
		$row['estoqueLiquido2'] = $row['D009_Quantidade_Estoque_Liquido'];
		$row['estoqueLoja'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Loja']);
		$row['estoqueTotal'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']+$row['D009_Quantidade_Estoque_Loja']);
		$row['ordemCompra'] = gCorrigeNumero($row['D009_Quantidade_OC']);
		$row['estoqueFora'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Fora']);

		$D005_Id=mysql_query("SELECT D009_Classificacao_Fiscal(D009_Id) AS Classificacao_Fiscal FROM D009 WHERE D009_Id='{$D009_Id}'");
		$mD005=mysql_fetch_array($D005_Id);

		$var=mysql_query("SELECT D005_Id FROM D005 WHERE D005_Classificacao_Fiscal='{$mD005['Classificacao_Fiscal']}'");
		$mVar = mysql_fetch_array($var);
		$row['D001_D005_Id'] = $mVar['D005_Id'];
		
		// $sql = <<<EOT
				// select * 
					// from D009 
				// left join D049 on D049_Id=D009_D049_Id 
				// left join D082 on D082_Id=D049_D082_Id 
				// left join D001 on D001_Id=D049_D001_Id
					// where D009_Id='$D009_Id'
// EOT;
		// $resultado = mysql_query($sql);
		// $rowD009 = mysql_fetch_assoc($resultado);
// 		
		// $row = array_merge($rowD001, $rowD009);
		
		//$row['T006_Valor_Preco_Sem_Desconto_Unitario'] = '';
		
		//$row['IPV'] = round($row['Preco_Reais']/$row['Custo_Reais'], 4);
		//$row['IPV'] = '';
		
		// Aliquota IPI
		$cClausula =" select *, ";
		$cClausula.=" D053_ST_ICMS_Interno, 
			          D053_ST_ICMS_Inter_Estadual, 
			          D053_Flag_ST, 
			          D018_Id, 
			          C004_D018_Id, 
			          C004_CFOP_ST_Venda_Dentro_Estado, 
			          C004_CFOP_ST_Venda_Fora_Estado,
			          C004_CFOP_CONSUMO_Venda_Dentro_Estado,
			          C004_CFOP_CONSUMO_Venda_Fora_Estado,
			          D024_Flag_Contribuinte,
					  T005_Ordem_Compra";
		$cClausula.=" from T005 ";
		$cClausula.=" left join D024 on D024_Id=T005_D024_Id ";
		$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
		$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T005_C004_Id ";
		$cClausula.=" left join C004 on C004_Id=T005_C004_Id ";
		$cClausula.=" where T005_Id='{$T005_Id}'";
		$T003=mysql_query($cClausula);
		$cErro=mysql_error();
		if (!empty($cErro))
		{
		   return "$cErro/p/p$cClausula";
		   exit;
		}
		$mT003=mysql_fetch_array($T003);	
		$sql = <<<EOT
			SELECT 
				D009_Id, 
				D009_IPI('{$mT003['T005_C004_Id']}',D009_Id,'{$mT003['T005_D024_Id']}','{$mT003['T005_D006_Id']}','1','{$mT003['T005_Flag_ACP']}') as Aliquota_IPI 
			FROM 
				D009 
			where D009_Id='{$D009_Id}'
EOT;
		$result = mysql_query($sql);
		$row2 = mysql_fetch_assoc($result);
		$row['Aliquota_IPI'] = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'],2,',','.');
		

		$row['T005_Flag_Destino_Produto'] = $T005_Flag_Destino_Produto;
		$row['T005_D022_Id'] = $T005_D022_Id;
		$row['T005_T208_Id'] = $T005_T208_Id;
		/**
		 * DEFININDO QUAL O PREÇO QUE SERÁ RETORNADO
		 * 
		 * $row['D041_Valor_Base'] = number_format($row['D041_Valor_Base'],2,',','.');
		 */
		$cfop      = $VEN001->retornaCFOP($mT003['T005_D024_Id'], $row['D009_Id'], $mT003['T005_C004_Id'], $T005_Flag_Destino_Produto, false, false, false, $mT003['T005_C004_Id_Estoque']);
		$aliquotas = $VEN001->retornaAliquotas($D009_Id, false, $T005_Id, false, false, false, false, $cfop);
		
        //Buscando as aliquotas digitadas na tela de montagem do ipv no orçamento
        $T251 = mysql_query("SELECT SUM(T251_Aliquota-T251_Aliquota_Original) AS AliquotasIPV 
							   FROM T251 
							  WHERE T251_T005_Id = '{$T005_Id}' 
							    AND T251_Flag_Altera_Preco='S' 
								AND T251_Grupo='IPV'");
        $mT251 = mysql_fetch_array($T251);
        $TotalPorcentagemIPV = $mT251['AliquotasIPV'];
        
        //Buscando as aliquotas digitadas na tela de montagem do icf no orçamento
        $T251 = mysql_query("SELECT SUM(T251_Aliquota-T251_Aliquota_Original) AS AliquotasICF 
		                       FROM T251 
							  WHERE T251_T005_Id = '{$T005_Id}' 
							    AND T251_Flag_Altera_Preco='S' 
								AND T251_Grupo='ICF'");
        $mT251 = mysql_fetch_array($T251);
        $TotalPorcentagemICF = $mT251['AliquotasICF'];

		$preco 		 = $CAD002->retornaPrecoVenda($row['D009_Id'], $mT003['T005_Flag_Lista_Preco'], $mT003['T005_D024_Id'], false, $cfop, $aliquotas['Aliquota_ICMS'], 0, 0, $mT003['T005_Casas_Decimais_Preco'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL'], 0, $TotalPorcentagemIPV, $TotalPorcentagemICF);
		$precoTabela = $CAD002->cad002RetornaPrecoTabela($row['D009_Id'], $mT003['T005_Flag_Lista_Preco'], $mT003['T005_D024_Id'], $mT003['T005_Flag_Acp'], $cfop, $aliquotas['Aliquota_ICMS'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $TotalPorcentagemIPV, $TotalPorcentagemICF, $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
		
		$row['Preco_Produto'] = $preco[0];
		$precoCalc = gCorrigeNumeroInverte($preco[0]);
		$row['PrecoMinimo'] = $VEN002->CalcularPrecoMinimo($precoCalc, $row);

      	$row['precoTabela'] = $precoTabela[0];
		$row['T006_Valor_Preco_Sem_Desconto_Unitario'] = $preco[0];

		$row['T006_Numero_Pedido_Compra'] = (isset($mT003['T005_Ordem_Compra']) && $mT003['T005_Ordem_Compra'] != '') ? $mT003['T005_Ordem_Compra'] : '';

		// Método que irá retonar o último preço que o produto foi vendido para o cliente
		$row['ultimoPrecoVenda'] = $VEN001->retornaltimoPrecoVenda($mT003['T005_D024_Id'], $row['D009_Id'], 0, $T005_Id);

		$row['D006_Id'] = $VEN001->retornaCFOP($mT003['T005_D024_Id'], $row['D009_Id']);
		if($row['D006_Id'] <= 0 || $row['D006_Id'] === false){
			$row['D006_Id'] = $T005_D006_Id;
		}

		if($g['C031']['utilizarDescProdutoUltimaVenda'] == 'S'){
			$sqlT003 = mysql_query("SELECT T004_Descricao_Produto FROM T004 LEFT JOIN T003 ON T003_Id = T004_T003_Id WHERE T003_D024_Id ='{$mT003['T005_D024_Id']}' AND T004_D009_Id = '{$row['D009_Id']}' ORDER BY T004_Id DESC LIMIT 1");
			if(mysql_num_rows($sqlT003) > 0){
				$resT003 = mysql_fetch_array($sqlT003);
				
				if($row['D001_Descricao_Produto'] != $resT003['T004_Descricao_Produto']){
					$row['D001_Descricao_Produto'] = $resT003['T004_Descricao_Produto'];
				}
			}
		}
		$row['utilizaQuantidadeConversao'] = $g['C031']['utilizaQuantidadeConversao'] === 'S';
		$row['campoFocus'] = $g['C031']['editarDescricaoProdutoVenda']=='S' ? 'T006_Descricao_Produto' : 'T006_Quantidade';
		if (!empty($row)) {
			$resposta['code'] = true;


			$params = array(
				"T005_Id" => $T005_Id,
				"D009_Id" => $D009_Id,
			);
			$row = $VEN002->ven002ModPreencheFormProdutosPedido($row, $params);
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;
			
	case 'atualizarCamposServicos':
		$D112_Id = isset($_REQUEST['D112_Id']) ? $_REQUEST['D112_Id'] : false;
		$D112_Id = mysql_real_escape_string($D112_Id);

		$sql = <<<EOT
			SELECT 
				D112_Titulo,
	   			D112_Descricao,
	   			D112_Tempo_Estimado,
	   			D112_Valor_Unitario,
	   			D112_Id,
	   			D112_D110_Id
	   				from D112
   				WHERE D112_Id = '{$D112_Id}'
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

			
		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;
	
	case 'aplicarIPV':
			require_once("bibliotecas/classes/VEN002.php");
			
			$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
			$IPV = isset($r_IPV) ? $r_IPV : false;
			
			$IPV = str_replace('.', '', $r_IPV);
			$IPV = str_replace(',', '.', $IPV);
			
			$VEN002 = new VEN002();
			
			$retorno = $VEN002->ven002aplicarIpvMinimo($T005_Id, $IPV);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "IPV aplicado com sucesso!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'importarItens':
		require_once("bibliotecas/classes/VEN002.php");
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$T005_Id_Origem = isset($r_T005_Id_Origem) ? $r_T005_Id_Origem : false;
		
		$VEN002 = new VEN002();
		
		$retorno = $VEN002->importarItens($T005_Id, $T005_Id_Origem);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Pedidos importado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'VEN002TrocaEmpresa':
		// Troca os C004 da tabela e tabelas filhos
		$retorno =  gMudarC004($Tabela, $Id, $C004_Id_Novo);
	break;
	
	case 'atualizarAliquota':
		global $g;
		$resposta['code'] = true;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$resposta['T005_Id'] = $T005_Id;

        if (empty($T005_Id)) {
        	$resposta['code'] = false;
	        $resposta['data'] = 'Não foi passado o Id do Pedido, contate o suporte técnico.';
        }
        $cSql_T005 = mysql_query("SELECT T005_Flag_Devolucao FROM T005 LEFT JOIN D006 ON D006_Id=T005_D006_Id LEFT JOIN D024 ON D024_Id=T005_D024_Id WHERE T005_Id='{$T005_Id}'");
        $mT005     = mysql_fetch_array($cSql_T005);

        if ($mT005['T005_Flag_Devolucao'] == 'S') {
        	$resposta['code'] = false;
	        $resposta['data'] = 'Este é um pedido para emissão de nota de devolução, não é possivel executar a atualização de aliquotas';
        }


        if ($r_inicio==1){
            $sqlCache = mysql_query("SELECT
                                        T006_Id,
                                        D009_Id,
                                        D001_Codigo_Produto,
                                        D001_Peso_Unitario_Kg,
                        		        D001_Cubagem_Unitaria,
                                        T005_Flag_Lista_Preco,
                                        T005_D024_Id,
                                        T006_D006_Id,
										D009_Preco_Pauta_ST,
                                        ifnull(T006_T219_Id,0) as T006_T219_Id,
                                        ifnull(T006_T225_Id,0) as T006_T225_Id,
                                        IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria,
										T006_Flag_Origem_Produto
                                        FROM T005
                                        LEFT JOIN T006 ON T005_Id=T006_T005_Id
                                        LEFT JOIN D024 ON D024_Id=T006_D024_Id
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                        WHERE T005_Id='{$T005_Id}'");
            $erro = mysql_error();
	        if (!empty($erro)) {
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro consultando itens do pedido: $erro';
	        }
        	$count=0;

        	while($rowCache = mysql_fetch_array($sqlCache)){
		       	$cache[$count]['T006_Id']= $rowCache['T006_Id'];
		   		$cache[$count]['D009_Id'] = $rowCache['D009_Id'];	
		        $cache[$count]['D009_Origem_Mercadoria']= $rowCache['D009_Origem_Mercadoria'];
		        $cache[$count]['T005_Flag_Lista_Preco']= $rowCache['T005_Flag_Lista_Preco'];
		        $cache[$count]['T005_D024_Id']= $rowCache['T005_D024_Id'];
		        $cache[$count]['T006_D006_Id']= $rowCache['T006_D006_Id'];
		        $cache[$count]['T006_T219_Id']= $rowCache['T006_T219_Id'];
		        $cache[$count]['T006_T225_Id']= $rowCache['T006_T225_Id'];
		        $cache[$count]['D001_Peso_Unitario_Kg']= $rowCache['D001_Peso_Unitario_Kg'];
				$cache[$count]['D001_Cubagem_Unitaria']= $rowCache['D001_Cubagem_Unitaria'];
				$cache[$count]['D009_Preco_Pauta_ST']= $rowCache['D009_Preco_Pauta_ST'];
				$cache[$count]['T006_Flag_Origem_Produto']= $rowCache['T006_Flag_Origem_Produto'];
				$count++;

			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 2);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;

			//apc_delete("cacheT005-{$g['usuarioAtual']}");
			//apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	        //$retorno = apc_store("cacheT005-{$g['usuarioAtual']}", serialize($cache));
			@unlink("/tmp/cacheT005-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT005-resposta-{$g['usuarioAtual']}");
			$retorno = file_put_contents("/tmp/cacheT005-{$g['usuarioAtual']}", serialize($cache));

	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			//$cache = unserialize(apc_fetch("cacheT005-{$g['usuarioAtual']}"));
			//$resposta = unserialize(apc_fetch("cacheT005-resposta-{$g['usuarioAtual']}"));
			$cache = unserialize(file_get_contents("/tmp/cacheT005-{$g['usuarioAtual']}"));
			$resposta = unserialize(file_get_contents("/tmp/cacheT005-resposta-{$g['usuarioAtual']}"));

	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao recuperar variável no cache resposta';
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

	    $ok = 0;
		$erro = 0;
		$erros ="";

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$retorno = $VEN002-> atualizarAliquotas($T005_Id,$cache[$i]);	         
           	if($retorno !== true){
           		$resposta['code'] = false;
       	     	$erro++;
           	 	$erros .= $retorno."<br />";
       		}else{
       			$ok++;
       		}
        }
   
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){
			//apc_delete("cacheT005-{$g['usuarioAtual']}");
			//apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT005-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT005-resposta-{$g['usuarioAtual']}");
			
			$resposta['concluido'] = 1;
		} else {
	        //apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT005-resposta-{$g['usuarioAtual']}");
	        
			$resposta['etapa'] = $resposta['etapa'] + 1;

	        //apc_store("cacheT005-resposta-{$g['usuarioAtual']}", serialize($resposta));
			file_put_contents("/tmp/cacheT005-resposta-{$g['usuarioAtual']}", serialize($resposta));

		}	        
	break;

	case 'atualizarAliquotaPedidos':
		log("entra no atualizarAliquota Pedidos");
		$resposta['code'] = true;
		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();

		if ($r_inicio==1){
			$extra = base64_decode($r_extra);
			$extra = gInsertExtraWhere($extra, "T005_Flag_Marcado = 'S'");
			$extraSemGroupBy = substr($extra,0,-20); //retira o group by T005_Id
	        $sqlCache = mysql_query("SELECT
	        							T005_Id,			
	                                    T006_Id,
	                                    D009_Id,
	                                    D001_Codigo_Produto,
	                                    D001_Peso_Unitario_Kg,
	                                    D001_Cubagem_Unitaria,
	                                    T005_Flag_Lista_Preco,
	                                    T005_D024_Id,
	                                    T006_D006_Id,
	                                    ifnull(T006_T219_Id,0) as T006_T219_Id,
	                                    ifnull(T006_T225_Id,0) as T006_T225_Id,
	                                    IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria
	                                    FROM T005
	                                    LEFT JOIN T006 ON T005_Id=T006_T005_Id
	                                    LEFT JOIN D009 ON D009_Id=T006_D009_Id
	                                    LEFT JOIN D049 ON D049_Id=D009_D049_Id
	                                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
	                                    {$extraSemGroupBy}
	                                    ORDER BY T005_Id");
	        $erro = mysql_error();
	        if (!empty($erro)) {
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro consultando itens dos pedidos: $erro";
	        }

	    	$count=0;

	    	while($rowCache = mysql_fetch_array($sqlCache)){
	    		$cache[$count]['T005_Id'] = $rowCache['T005_Id'];
		       	$cache[$count]['T006_Id']= $rowCache['T006_Id'];
		   		$cache[$count]['D009_Id'] = $rowCache['D009_Id'];	
		        $cache[$count]['D009_Origem_Mercadoria']= $rowCache['D009_Origem_Mercadoria'];
		        $cache[$count]['T005_Flag_Lista_Preco']= $rowCache['T005_Flag_Lista_Preco'];
		        $cache[$count]['T005_D024_Id']= $rowCache['T005_D024_Id'];
		        $cache[$count]['T006_D006_Id']= $rowCache['T006_D006_Id'];
		        $cache[$count]['T006_T219_Id']= $rowCache['T006_T219_Id'];
		        $cache[$count]['T006_T225_Id']= $rowCache['T006_T225_Id'];
		        $cache[$count]['D001_Peso_Unitario_Kg']= $rowCache['D001_Peso_Unitario_Kg'];
		        $cache[$count]['D001_Cubagem_Unitaria']= $rowCache['D001_Cubagem_Unitaria'];
				$count++;

			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 20);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
		    $resposta['erro'] = 0;
	    	$resposta['concluido'] = 0;
			apc_delete("cacheT005-{$g['usuarioAtual']}");
			apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT005-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }	    
			$resposta['T005_Id'] = 0;
	    }else {
			$cache = unserialize(apc_fetch("cacheT005-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT005-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao recuperar variável no cache resposta';
	        }
	    }

	    $resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

		if($resposta['etapa'] >= $resposta['etapas']){
			$resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
		} else {
			$resposta['limit2'] += $resposta['registrosEtapa'] ;
		}

	    $resposta['percentual'] = ceil((100/$resposta['etapas']) * $resposta['etapa']);

		if($resposta['limit2'] < $resposta['totalRegistros']){
	    	$resposta['etapa'] = $resposta['etapa'] + 1;	
	    }else{
	    	$resposta['concluido'] = 1;
	    }
	   
	    $r_opcoes = json_decode($r_opcoes);

	    if(!is_array($r_opcoes)) { 
	        $opcoes = get_object_vars($r_opcoes);
	    }

	    $ok = 0;
		$erro = 0;
		$erros ="";
		$pedidos = 0;
		log("entra aqui nessa parte");
		log("limit1= ".$resposta['limit1']);
		log("limit2= ".$resposta['limit2']);
	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) {  
	    log("entra no for");  
	    	$retorno = $VEN002-> atualizarAliquotas($cache[$i]['T005_Id'],$cache[$i]);           
           	if($retorno !== true){
           		$resposta['code']=false;
       	     	$erro++;
           	 	$erros .= $erro ." - ". $retorno."<br />";
       		}else{
       			if ($resposta['T005_Id']!=$cache[$i]['T005_Id']) {
       				$resposta['T005_Id'] = $cache[$i]['T005_Id'];
       				mysqli_query("UPDATE T005 SET T005_Flag_Marcado='N' WHERE T005_Id='{$resposta['T005_Id']}'");	
       				log("pedidos= ".$pedidos);  
       				$pedidos++;
       			}
       			$ok++;
       		}
        }
        $resposta['pedidos'] += $pedidos;       
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] > $resposta['etapas']){
			apc_delete("cacheT005-{$g['usuarioAtual']}");
			apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
		} else {
	        apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	        apc_store("cacheT005-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        

	break;
	
	case 'validarNFDebitoCredito':
		$T005_Id      = isset($_REQUEST['T005_Id']) ? (int) $_REQUEST['T005_Id'] : 0;
		$tipoOperacao = isset($_REQUEST['tipoOperacao']) ? strtolower(trim($_REQUEST['tipoOperacao'])) : '';
		$D024_Id      = isset($_REQUEST['D024_Id']) ? (int) $_REQUEST['D024_Id'] : 0;
		$D006_Id      = isset($_REQUEST['D006_Id']) ? (int) $_REQUEST['D006_Id'] : 0;
		$destino      = isset($_REQUEST['destino']) ? (int) $_REQUEST['destino'] : 0;
		$tipoFiscal   = isset($_REQUEST['tipoFiscal']) ? trim($_REQUEST['tipoFiscal']) : '';
		$quantidades  = isset($_REQUEST['quantidades']) ? json_decode($_REQUEST['quantidades'], true) : array();
		$referencias  = isset($_REQUEST['referencias']) ? json_decode($_REQUEST['referencias'], true) : array();
		$chaveReferenciaCabecalho = preg_replace('/\D/', '', isset($_REQUEST['chaveReferenciaCabecalho']) ? $_REQUEST['chaveReferenciaCabecalho'] : '');
		$erros        = array();
		if ($tipoOperacao !== 'debito' || $tipoFiscal !== '06') {
			$erros[] = 'A partir do pedido somente é permitida a NF de débito tipo 06 - Pagamento antecipado.';
		}
		if (!in_array($tipoOperacao, array('debito', 'credito'))) {
			$erros[] = 'Selecione se a nota será de débito ou de crédito.';
		}
		$tiposPermitidos = ($tipoOperacao === 'credito') ? array('01', '02', '03', '04', '05', '06') : array('01', '02', '03', '04', '05', '06', '07', '08');
		if (!in_array($tipoFiscal, $tiposPermitidos)) {
			$erros[] = 'Selecione um tipo fiscal válido para a operação.';
		}
		$exigeReferenciaCabecalho = $tipoOperacao === 'credito' && in_array($tipoFiscal, array('01', '03', '04'), true);
		if ($exigeReferenciaCabecalho && strlen($chaveReferenciaCabecalho) !== 44) {
			$erros[] = 'Este tipo de NF de crédito exige uma chave de NF-e referenciada de 44 dígitos no cabeçalho.';
		}
		if ($tipoOperacao === 'credito' && $tipoFiscal === '02' && (int) date('Y') < 2029) {
			$erros[] = 'NF de crédito tipo 02 somente pode ser emitida a partir de janeiro de 2029.';
		}
		if ($exigeReferenciaCabecalho && strlen($chaveReferenciaCabecalho) === 44) {
			$modeloReferencia = substr($chaveReferenciaCabecalho, 20, 2);
			$modelosPermitidos = $tipoFiscal === '03' ? array('55', '65') : array('55');
			if (!in_array($modeloReferencia, $modelosPermitidos, true)) {
				$erros[] = 'A chave referenciada possui modelo incompatível com este tipo de NF de crédito.';
			}
		}
		$pedido = mysql_fetch_assoc(mysql_query("SELECT T005_Id, T005_Flag_Destino_Produto FROM T005 WHERE T005_Id = '{$T005_Id}' AND T005_C004_Id = '{$g['empresaAtual']}'"));
		if (empty($pedido['T005_Id'])) {
			$erros[] = 'Pedido não encontrado para a empresa atual.';
		} else {
			// A tela padrão não solicita destino; herda o cadastro do pedido.
			if (!isset($_REQUEST['destino'])) {
				$destino = (int) $pedido['T005_Flag_Destino_Produto'];
			}
			if ($destino < 1 || $destino > 20) {
				$erros[] = 'Confira o destino da operação no pedido antes de gerar a NF de débito.';
			}
		}
		$cliente = mysql_fetch_assoc(mysql_query("SELECT D024_Id FROM D024 WHERE D024_Id = '{$D024_Id}' AND D024_Flag_Cliente = 'S' AND D024_Flag_Ativo = 'S'"));
		if (empty($cliente['D024_Id'])) {
			$erros[] = 'Selecione um cliente ativo.';
		}
		$cfop = mysql_fetch_assoc(mysql_query("SELECT D006_Id FROM D006 WHERE D006_Id = '{$D006_Id}' AND IFNULL(D006_Flag_Inativo,'N') != 'S'"));
		if (empty($cfop['D006_Id'])) {
			$erros[] = 'Selecione uma CFOP ativa.';
		}

		$itensSelecionados = 0;
		$quantidadeTotal = 0;
		$finalidade = ($tipoOperacao === 'credito') ? '5' : '6';
		if (!is_array($quantidades)) {
			$quantidades = array();
		}
		foreach ($quantidades as $T006_Id => $quantidadeInformada) {
			$T006_Id = (int) $T006_Id;
			$quantidadeTexto = trim((string) $quantidadeInformada);
			if (strpos($quantidadeTexto, ',') !== false) {
				$quantidadeTexto = str_replace('.', '', $quantidadeTexto);
				$quantidadeTexto = str_replace(',', '.', $quantidadeTexto);
			}
			$quantidade = is_numeric($quantidadeTexto) ? (float) $quantidadeTexto : -1;
			if ($quantidade == 0) {
				continue;
			}
			if ($quantidade < 0) {
				$erros[] = "A quantidade informada para o item {$T006_Id} é inválida.";
				continue;
			}

			$item = mysql_fetch_assoc(mysql_query("SELECT T006_Id, T006_Codigo_Produto, T006_Quantidade,
												 IFNULL((SELECT SUM(T008_Quantidade)
														   FROM T008
												  LEFT JOIN T007 ON T007_Id = T008_T007_Id
											 INNER JOIN T007A ON T007A_T007_Id = T007_Id
												  WHERE T008_T006_Id = T006_Id
													AND T007_Flag_Finalidade_NFe = '{$finalidade}'
													AND T007A_Tipo_NF_Debito = '06'
													AND IFNULL(T007_Flag_Cancelada,'N') != 'S'
															AND IFNULL(T007_Flag_Inutilizado,'N') != 'S'), 0) AS Quantidade_Gerada
											FROM T006
										   WHERE T006_Id = '{$T006_Id}' AND T006_T005_Id = '{$T005_Id}'"));
			if (empty($item['T006_Id'])) {
				$erros[] = "O item {$T006_Id} não pertence ao pedido.";
				continue;
			}
			if (($quantidade + (float) $item['Quantidade_Gerada']) > ((float) $item['T006_Quantidade'] + 0.00001)) {
				$erros[] = "O produto {$item['T006_Codigo_Produto']} ultrapassa a quantidade do pedido considerando as notas já geradas.";
				continue;
			}
			$referencia = isset($referencias[$T006_Id]) && is_array($referencias[$T006_Id]) ? $referencias[$T006_Id] : array();
			$chaveReferencia = preg_replace('/\D/', '', isset($referencia['chave']) ? $referencia['chave'] : '');
			$itemReferencia = (int) (isset($referencia['item']) ? $referencia['item'] : 0);
			$exigeChave = ($tipoOperacao === 'debito' && in_array($tipoFiscal, array('03', '04'))) || ($tipoOperacao === 'credito' && $tipoFiscal === '06');
			$exigeItem = ($tipoOperacao === 'debito' && $tipoFiscal === '04') || ($tipoOperacao === 'credito' && $tipoFiscal === '06');
			if ($exigeChave && strlen($chaveReferencia) !== 44) {
				$erros[] = "Informe uma chave DF-e válida de 44 dígitos para o produto {$item['T006_Codigo_Produto']}.";
				continue;
			}
			if ($exigeItem && $itemReferencia <= 0) {
				$erros[] = "Informe o item do DF-e referenciado para o produto {$item['T006_Codigo_Produto']}.";
				continue;
			}
			$itensSelecionados++;
			$quantidadeTotal += $quantidade;
		}

		if ($itensSelecionados === 0) {
			$erros[] = 'Informe uma quantidade maior que zero em pelo menos um produto.';
		}

		if (!empty($erros)) {
			$resposta['code'] = false;
			$resposta['data'] = implode('<br />', $erros);
		} else {
			$retorno = $VEN002->gerarNFDebitoCreditoPedido(array(
				'T005_Id' => $T005_Id,
				'tipoOperacao' => $tipoOperacao,
				'D024_Id' => $D024_Id,
				'D006_Id' => $D006_Id,
				'destino' => $destino,
				'tipoFiscal' => $tipoFiscal,
				'observacao' => isset($_REQUEST['observacao']) ? $_REQUEST['observacao'] : '',
				'chaveReferenciaCabecalho' => $chaveReferenciaCabecalho,
				'quantidades' => $quantidades,
				'referencias' => $referencias
			));
			if (is_array($retorno)) {
				$identificacaoNF = !empty($retorno['numero']) ? $retorno['numero'] : 'Em elaboração #' . $retorno['T007_Id'];
				$resposta['code'] = true;
				$resposta['data'] = "NF de {$retorno['operacao']} gerada em elaboração.<br />NF: <strong>{$identificacaoNF}</strong> &nbsp; Série: {$retorno['serie']}<br />Itens: {$retorno['itens']}<br /><br />A NF ainda deve ser revisada e transmitida pelo processo fiscal normal. O pedido continua disponível para o faturamento da NF de venda.";
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
	break;

	case 'faturarPedido':
		require_once("bibliotecas/classes/VEN002.php");
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		$VEN002 = new VEN002();
		
		$mensagem = '';
		$retorno = $VEN002->faturarPedido($T005_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = $mensagem;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'abortouFaturarPedido':
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Usuário não confirmou faturamento','')");

		$resposta['code'] = true;
		$resposta['data'] = '';
	break;

	case 'validarCamposNFe':

 		if ($g['C031']['validarNFEFaturarPedido']=='S'){	
			
			require_once("bibliotecas/validadorNFe/validadorNFe.php");

			$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;

			$validadorNFe = new validadorNFe();

			$mensagem = '';
			$retorno = $validadorNFe->validadorNFe(null, $T005_Id, null);

		} else {

			$mensagem = '';
			$retorno = true; 

		}

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = $mensagem;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}

	break;
	
	case 'carregaCFOP_Contatos':
            
        global $g;
		
        $T005_D024_Id = isset($r_T005_D024_Id) ? $r_T005_D024_Id : false;
		
		if (empty($T005_D024_Id)) {
			$resposta['code'] = false;
			$resposta['data']['erro'] = "Nenhum cliente selecionado";
		} else {
			$sql = "SELECT * FROM D024 WHERE D024_Id=" . $T005_D024_Id;
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);
			
			echo mysql_error();

			$resposta['data']['setaDestinoVenda'] = false;
			if($g['C031']['definirDestinoVendaTipoCliente'] == 'S'){
				$resposta['data']['setaDestinoVenda'] = true;
				switch ($aD024['D024_Flag_Contribuinte']) {
					case '5':
						$resposta['data']['T005_Flag_Destino_Produto'] = '1';
						break;
					case '2':
						$resposta['data']['T005_Flag_Destino_Produto'] = '2';
						break;
					case '1':
						$resposta['data']['T005_Flag_Destino_Produto'] = '3';
						break;
				}
			}

			if(!empty($aD024['D024_Flag_Destino_Produto'])) {
				$resposta['data']['setaDestinoVenda'] = true;
				$resposta['data']['T005_Flag_Destino_Produto'] = $aD024['D024_Flag_Destino_Produto'];
			}


			require_once("bibliotecas/classes/VEN001.php");
			$VEN001 = new VEN001();
			$D006_Id=$VEN001->retornaCFOP($T005_D024_Id, false, false);

			$resposta['code'] = true;
			// se nenhuma regra satisfizer, usa o critério antigo	
			if ($D006_Id===false){
				$sql = "SELECT C004_CFOP_Venda_Dentro_Estado, 
				               C004_CFOP_Venda_Fora_Estado, 
				               C004_CFOP_ST_Venda_Fora_Estado, 
				               C004_CFOP_ST_Venda_Dentro_Estado, 
				               C004_CFOP_CONSUMO_Venda_Fora_Estado, 
				               C004_CFOP_CONSUMO_Venda_Dentro_Estado, 
				               C004_D018_Id 
				          FROM C004 WHERE C004_Id=" . $g['c004']['C004_Id'];
				$result = mysql_query($sql);
				$aC004 = mysql_fetch_assoc($result);
				
				echo mysql_error();
				
				$D006_Id = $aD024['D024_D006_Id'];
				
                if ($D006_Id<=0)
                {	                	
			        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
			        {
			            $D006_Id = $aC004['C004_CFOP_Venda_Fora_Estado'];
					}
					else
					{       
			            $D006_Id = $aC004['C004_CFOP_Venda_Dentro_Estado'];
					}	
				    if ($aD024['D024_Flag_Contribuinte'] == '1')
				    {
				        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
				        {
				            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Fora_Estado'];
						}
						else
						{
				            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Dentro_Estado'];
						}	
					}
				}
				if ($D006_Id<=0)
					$D006_Id=false;					
			}	
			// se apenas uma regra tiver sido satisfeita
		    if ($D006_Id>0) 
			{
				$arrayDentroEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true, $D006_Id);
				$resposta['data']['T005_D006_Id'] = gProcessaSelect($arrayDentroEstado);
			}
			// se nenhuma regra tiver sido satisfeita (critério novo e antigo)
			else if ($D006_Id===false)
			{
				$resposta['data']['T005_D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true));
			}
			// se mais de uma regra tiver sido satisfeita
			else
			{
				//$resposta['code'] = false;
				//$resposta['data']['erro'] = $D006_Id;	
			}			
			//$resposta['data']['T005_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato',"where D013_D024_Id='{$T005_D024_Id}'",true));
			$resposta['data']['T005_Flag_Revenda_Consumidor'] = "$aD024[D024_Flag_Revenda_Consumidor]";
			//$resposta['data']['T005_C007_Id_Vendedor_Interno'] = "{$g['usuarioAtual']}";

			$resposta['data']['notificarVendedor'] = 0;
			
			if($aD024['D024_C007_Vendedor_Interno'] != $g['usuarioAtual']){
				$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM C007 WHERE C007_Id = '{$aD024['D024_C007_Vendedor_Interno']}'");
				if(mysql_num_rows($sqlC007) > 0){
					$resC007 = mysql_fetch_array($sqlC007);
					if($resC007['C007_Flag_Ausente'] == 'S'){
						$resposta['data']['T005_C007_Id_Vendedor_Interno'] = $g['usuarioAtual'];
						$resposta['data']['notificarVendedor'] = 2;
					} else {
						$resposta['data']['T005_C007_Id_Vendedor_Interno'] = $aD024['D024_C007_Vendedor_Interno'];
						$resposta['data']['notificarVendedor'] = 1;
					}
				} else {
					$resposta['data']['T005_C007_Id_Vendedor_Interno'] = $g['usuarioAtual'];
				}
			} else {
				$resposta['data']['T005_C007_Id_Vendedor_Interno'] = $aD024['D024_C007_Vendedor_Interno'];
			}	
			$resposta['data']['T005_C007_Id_Vendedor_Externo'] = $aD024['D024_C007_Vendedor_Externo'];

			if($g['C031']['modeloExpedicao'] == '2'){
				$C007=mysql_query("SELECT C007_Flag_Expedicao FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
				$mC007=mysql_fetch_array($C007);
				error_log("entrou modelo expedicao ".$mC007['C007_Flag_Expedicao']);

				if($mC007['C007_Flag_Expedicao']=="1"){
					error_log("e comercial");
	
					$resposta['data']['T005_Flag_Expedicao'] = gProcessaSelect(
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Comercial', 'value' => '1', 'selected'=>true),
							array('title' => 'Balcão', 'value' => '2'),
						)
					);
				} else if($mC007['C007_Flag_Expedicao']=="2"){
	
					$resposta['data']['T005_Flag_Expedicao'] = gProcessaSelect(
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Comercial', 'value' => '1'),
							array('title' => 'Balcão', 'value' => '2', 'selected'=>true),
						)
					);
				} else {
					$resposta['data']['T005_Flag_Expedicao'] = gProcessaSelect(
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Comercial', 'value' => '1'),
							array('title' => 'Balcão', 'value' => '2'),
						)
					);
				}
			}

			// carrega os contatos do cliente selecionado
			if($g['C031']['orcamentoCarregarPrimeiroContato'] == 'N'){
				//select para carregar os contatos e deixar "..." selecionado na combo
				$resposta['data']['T005_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T005_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true));		
				$resposta['data']['T005_D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T005_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true)));
			}else{
				//select para carregar os contatos e deixar o primeiro contato selecionado na combo
				$resposta['data']['T005_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T005_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Id',false));		
				$resposta['data']['T005_D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T005_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Id',false)));
			}

			// Carrega transportadora
			if(!empty($aD024['D024_D022_Id'])){
				$sqlD022 = mysql_query("SELECT D022_Nome_Empresa,D020_Nome_Cidade,D018_UF FROM D022 LEFT JOIN D020 ON D020_Id=D022_D020_Id LEFT JOIN D018 ON D018_Id=D022_D018_Id WHERE D022_Id='{$aD024['D024_D022_Id']}'");
				$D022 	 = mysql_fetch_assoc($sqlD022);
				$resposta['data']['T005_D022_Id_Pesquisa'] = $D022['D022_Nome_Empresa']." - ".$D022['D020_Nome_Cidade']." - ".$D022['D018_UF'];
				$resposta['data']['T005_D022_Id'] = $aD024['D024_D022_Id'];
			}

			//carrega tipo frete
			$resposta['data']['T005_Flag_Frete'] = $aD024['D024_Flag_Frete'];
			
			//carrega os veículos do cliente selecionado
            $resposta['data']['T005_D116_Id'] = gProcessaSelect(gGeraSelect('D116','D116_Id','D116_Placa,D133_Modelo,D134_Cor', 'LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id='.$T005_D024_Id,true));
            $resposta['data']['T005_D116_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D116','D116_Id','D116_Placa,D133_Modelo,D134_Cor',' JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id='.$T005_D024_Id,true)));

            // endereço de entrega
			$D148_Id_Entrega = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id='{$T005_D024_Id}' AND D148_Flag_Tipo = 'E'");
			if (mysql_num_rows($D148_Id_Entrega) > 0) {
				$D148_Id_Entrega = mysql_fetch_assoc($D148_Id_Entrega);
				$D148_Id_Entrega = $D148_Id_Entrega['D148_Id'];
			} else {
				$D148_Id_Entrega = false;
			}
			$array = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T005_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Entrega);
			$array[0]['title'] = 'PADRÃO';
			$resposta['data']['T005_D148_Id_Entrega'] = gProcessaSelect($array);
			$resposta['data']['T005_D148_Id_Entrega_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T005_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Entrega)));

			// endereço de cobrança
			$D148_Id_Cobranca = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id='{$T005_D024_Id}' AND D148_Flag_Tipo = 'C'");
			if (mysql_num_rows($D148_Id_Cobranca) > 0) {
				$D148_Id_Cobranca = mysql_fetch_assoc($D148_Id_Cobranca);
				$D148_Id_Cobranca = $D148_Id_Cobranca['D148_Id'];
			} else {
				$D148_Id_Cobranca = false;
			}
			$array = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T005_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Cobranca);
			$array[0]['title'] = 'PADRÃO';
			$resposta['data']['T005_D148_Id_Cobranca'] = gProcessaSelect($array);
			$resposta['data']['T005_D148_Id_Cobranca_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T005_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Cobranca)));
            
			// endereço de redespacho
			$D148_Id_Redespacho = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id='{$T005_D024_Id}' AND D148_Flag_Tipo = 'R'");
			if (mysql_num_rows($D148_Id_Redespacho) > 0) {
				$D148_Id_Redespacho = mysql_fetch_assoc($D148_Id_Redespacho);
				$D148_Id_Redespacho = $D148_Id_Redespacho['D148_Id'];
			} else {
				$D148_Id_Redespacho = false;
			}
			$array = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T005_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Redespacho);
			$array[0]['title'] = 'PADRÃO';
			$resposta['data']['T005A_D148_Id_Redespacho'] = gProcessaSelect($array);
			$resposta['data']['T005A_D148_Id_Redespacho_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T005_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Redespacho)));
			
            //carrega o representante
            $resposta['data']['T005_D021_Id'] = gProcessaSelect(gGeraSelect('D021','D021_Id','D021_Nome_Empresa', '',true,$aD024['D024_D021_Id']));
			
		}
        
    break;

    case 'carregaListaPreco':
    	global $g;
    	$D024_Id = isset($r_T005_D024_Id) ? $r_T005_D024_Id : false;

		$result = mysql_query("SELECT D024_Flag_Lista_Preco from D024 WHERE D024_Id='{$D024_Id}'");
		$D024 = mysql_fetch_assoc($result);
		
		switch($D024['D024_Flag_Lista_Preco']){
			case '1':
				$resposta['data']['T005_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1', 'selected' => true),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
							array('title' => 'Preço 4', 'value' => '4'),
							array('title' => 'Preço 5', 'value' => '5'),
						));
				$resposta['code'] = true;
				break;
			case '2':
				$resposta['data']['T005_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2', 'selected' => true),   
							array('title' => 'Preço 3', 'value' => '3'), 
							array('title' => 'Preço 4', 'value' => '4'),  
							array('title' => 'Preço 5', 'value' => '5'),
						));
				$resposta['code'] = true;
				break;
			case '3':
				$resposta['data']['T005_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3', 'selected' => true),   
							array('title' => 'Preço 4', 'value' => '4'),
							array('title' => 'Preço 5', 'value' => '5'),
						));
				$resposta['code'] = true;
				break;
			case '4':
				$resposta['data']['T005_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
							array('title' => 'Preço 4', 'value' => '4', 'selected' => true),
							array('title' => 'Preço 5', 'value' => '5'),
						));
				$resposta['code'] = true;
				break;
			case '4':
				$resposta['data']['T005_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
							array('title' => 'Preço 4', 'value' => '4'),
							array('title' => 'Preço 5', 'value' => '5', 'selected' => true),
						));
				$resposta['code'] = true;
				break;
		}
	break;
    
    case 'carregaTipoTransp':
        
        global $g;
        
        $D022_Id = isset($r_D022_Id) ? $r_D022_Id : false;
        $sql = "SELECT D022_D036_Id FROM D022 WHERE D022_Id=" . $D022_Id;
        $result = mysql_query($sql);
        $aD022 = mysql_fetch_assoc($result);
        
        $resposta['code'] = true;
        $resposta['data']['T005_D036_Id'] = $aD022['D022_D036_Id'];
        
        
    break;
		
	case 'xinserirParcela':
		require_once("bibliotecas/classes/VEN002.php");
		
		$form=$r_ven002FormPagamento;		
		$VEN002 = new VEN002();		
		$retorno = $VEN002->inserirParcela($form);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;	

	case 'inserirParcela':
		require_once("bibliotecas/classes/VEN001.php");
		$form_tabela=(isset($r_form_tabela)) ? $r_form_tabela : false;
		$T005_Id=(isset($r_T005_Id)) ? $r_T005_Id : false;
		$form_campo_pk=(isset($r_form_campo_pk)) ? $r_form_campo_pk : false;
		$dadosTabela = array('tabela' => $form_tabela, 
							 'tabelaId' => 	$T005_Id,
							 'campos' => 'T005_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
                                 T005_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
                                 T005_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
                                 T005_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
                                 T005_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
                                 T005_D027_Id as D027_Id,
                                 T005_Pgto_Parcelas as Total_Parcelas,
                                 T005_Valor_Total as valorTotal', 
                             'where' => $form_campo_pk."='{$T005_Id}'", 
                             'tabelaPgto' => 'T090', 
                             'camposPgto' => 'T090_Prazos_Dias as prazoDias,T090_D027_Id as D027Pagamento,T090_Numero_Parcela as numeroParcela, T090_Data_Vencimento as dataVencimento', 
                             'wherePgto' => "T090_T005_Id='{$T005_Id}'");

		$VEN001 = new VEN001();		
		$retorno = $VEN001->inserirParcela($dadosTabela);
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;

	case 'calcularParcelaSimulacao':
		require_once("bibliotecas/classes/VEN002.php");
		
		$form=$r_ven002FormPagamento;		
		$VEN002 = new VEN002();		
		$retorno = $VEN002->calcularParcelaSimulacao($form);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha no calculo da parcela...';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'salvarObservacao':
		require_once("bibliotecas/classes/VEN002.php");
		
		$form=$r_ven002FormPagamento;		
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false; 
		$VEN002 = new VEN002();		
		$retorno = $VEN002->gravarObservacao($form, $T005_Id);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da Observação. Por favor, tente novamente';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'gravarDescontoPedido':
		require_once("bibliotecas/classes/VEN002.php");
		
		$T005_Id=(isset($r_T005_Id)) ? $r_T005_Id : false;
		$T005_Valor_Desconto = (isset($r_T005_Valor_Desconto)) ? $r_T005_Valor_Desconto : false;
		$valor = (isset($r_Valor)) ? $r_Valor : false;
		$VEN002 = new VEN002();		
		$retorno = $VEN002->gravarDescontoPedido($T005_Id, $T005_Valor_Desconto, $valor);
		
		if ($retorno !== true) {			
			$resposta['code'] = false;
			$resposta['data'] = $retorno;			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'verificaAlcada':
		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$retorno = $VEN002->verificaAlcada($T005_Id);
		
		//Calcula Desconto por alçada
		$descontoPedido = $VEN002->calculaPercentualDescontoPedido($T005_Id);
		$descontoPedido = number_format($descontoPedido, 2, '.', ' ');
		$descontoVendedor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
		$descontoSupervisor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));
		//Calcula prazo médio por alçada
		$prazoMedioAlcadaVendedor = $VEN002->calculaPrazoMedioAlcadaVendedor($T005_Id);
		$prazoMedioAlcadaSupervisor = $VEN002->calculaPrazoMedioAlcadaSupervisor($T005_Id);
		$ret=mysql_query("SELECT AVG(T090_Prazos_Dias) FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
		$T005=mysql_fetch_array($ret);				
		$prazoMedio=$T005['AVG(T090_Prazos_Dias)'];
			//Verificações de alçada
        $retornoSaldoMinimo = $VEN002->verificaFuturoSaldoProduto($T005_Id);

        // variáveis de usar alçada por produto
        $usarAlcadaPorProduto = $g['conf']->ini('usarAlcadaPorProduto');
        $alcadaDescontoVendedorPorProduto = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedorPorProduto'));
		$alcadaDescontoSupervisorPorProduto = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisorPorProduto'));
		$T005MaiorDesconto = $VEN002->retornaMaiorDescontoPedido($T005_Id);
		$T005MaiorDesconto = number_format($T005MaiorDesconto, 2, ".", " ");


        //die("Desc Orç: ".$descontoPedido." Supoervisor".$descontoSupervisor." Vendedor: ".$descontoVendedor);
			
		if(($descontoPedido <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor)  && ($retornoSaldoMinimo==0)){

			$alcadaUpdate = "V";

		} elseif(($descontoPedido <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)){

			$alcadaUpdate = "S";

		}else{
			
			 $alcadaUpdate = "D";
		}

		//die("T005_Maior_Desconto: " . $T005MaiorDesconto . " alçada supervisor: ".$alcadaDescontoSupervisorPorProduto);

		// Verificação apenas para quando a global $usarAlcadaPorProduto for igual a sim
		if($usarAlcadaPorProduto == "S"){

			if($T005MaiorDesconto <= $alcadaDescontoVendedorPorProduto){

				$alcadaUpdate = "V";		

			}else if($T005MaiorDesconto <= $alcadaDescontoSupervisorPorProduto){

				$alcadaUpdate = "S";

			} else {

				$alcadaUpdate = "D";
			}
		}
		//die($alcadaUpdate);
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Você tem certeza que deseja faturar o pedido?";
		}  else if($retorno === false) {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Você não tem permissão para gerar Pedidos!";
		} else {
			$resposta['code'] = $retorno;
			$resposta['data'] = $retorno;
			$resposta['alcadaUpdate'] = $alcadaUpdate;
		}
	break;
	
	case 'enviarParaAnalise':
		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
		$acao = isset($r_acao) ? $r_acao : false;
		
		$retorno = $VEN002->enviarParaAnalise($T005_Id,$alcadaUpdate,$acao);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Pedido enviado para análise";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
		
	case 'ven002ImprimirPedido':
		global $g;

		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
		$moeda = isset($r_moeda) ? $r_moeda : false;


		if (isset($r_gerarHtml)) {
			echo $VEN002->montarEmailPdf($T005_Id, true, $moeda);
		} else {
			$resposta = array('code' => true, 'data' => array());
			
			$T090 = mysql_query("SELECT SUM(T090_Valor_Parcela) as TotalParcelas, T005_Valor_Total FROM T090 LEFT JOIN T005 ON T005_Id = T090_T005_Id WHERE T090_T005_Id='{$T005_Id}'");
			$resT090 = mysql_fetch_array($T090);

			$sql="SELECT D006_Flag_Gera_Contas FROM T005 LEFT JOIN D006 on D006_Id=T005_D006_Id WHERE T005_Id='{$T005_Id}'";
			$resultado=mysql_query($sql);
			$row=mysql_fetch_array($resultado);

			if($row['D006_Flag_Gera_Contas'] == "R"){
				if($resT090['TotalParcelas'] == 0){
					$erro = "É necessário preencher a forma de pagamento";
				} else {
					if($resT090['T005_Valor_Total'] != $resT090['TotalParcelas']){
						$erro = "Valor das parcelas não confere com o total do pedido.";
					}
				}
			}
			if(isset($erro)){
				$resposta['code'] = false;
				$resposta['data'] = $erro;
			} else {
				$retorno = $VEN002->montarEmailPdf($T005_Id, false, $moeda);
				
				$resposta['code'] = true;
				$resposta['data'] = isset($retorno['AnexosWeb'][0]) ? $retorno['AnexosWeb'][0] : '';
			}
			
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
		}
		die();
	break;

	case 'ven002DeletarPDFPedido':
		global $g;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$dir = $g['pathDados'] . 'tmp/';
		$filename             = "pedido-{$T005_Id}.pdf";
		$fileFull             = $g['pathRaiz'] . '/' . $dir . $filename;
		if(file_exists($fileFull)){
			unlink($fileFull);
		}
	break;
	
	case 'verificarParcelas':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		$T090 = mysql_query("SELECT SUM(T090_Valor_Parcela) as TotalParcelas, T005_Valor_Total FROM T090 LEFT JOIN T005 ON T005_Id = T090_T005_Id WHERE T090_T005_Id='{$T005_Id}'");
		$resT090 = mysql_fetch_array($T090);
        
        $sql="SELECT D006_Flag_Gera_Contas FROM T005 LEFT JOIN D006 on D006_Id=T005_D006_Id WHERE T005_Id='{$T005_Id}'";
        $resultado=mysql_query($sql);
        $row=mysql_fetch_array($resultado);
        
        if($row['D006_Flag_Gera_Contas'] == "R"){
		    if($resT090['TotalParcelas'] == 0){
			    $erro = "É necessário preencher a forma de pagamento";
		    } else {
			    if($resT090['T005_Valor_Total'] != $resT090['TotalParcelas']){
				    $erro = "Valor das parcelas não confere com o total do pedido.";
			    }
		    }
        }
        
		if(isset($erro)){
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		} else {
			$resposta['code'] = true;
		}
	break;
	
	case 'reativarPedido':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		$T005 = mysql_query("UPDATE T005 SET T005_Flag_Status = '', T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = '{$T005_Id}'");
		
		$T006 = mysql_query("SELECT * FROM T006 WHERE T006_T005_Id = '{$T005_Id}'");
		
		if(mysql_num_rows($T006) > 0){
			while($row = mysql_fetch_array($T006)){
				$D009 = mysql_query("SELECT * FROM D009 WHERE D009_Id = '{$row['T006_D009_Id']}' AND D009_C004_Id = '{$g['empresaAtual']}'");
				$resD009 = mysql_fetch_array($D009);
				
				$D049 = mysql_query("SELECT * FROM D049 WHERE D049_Id = '{$resD009['D009_D049_Id']}'");
				$resD049 = mysql_fetch_array($D049);
				
				$CAD002->D001_reprocessa_historico($resD049['D049_D001_Id'], $resD009['D009_Id']);
				
			}
		}
		mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$T005_Id}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'Reativado','')");
		$resposta['code'] = true;
	break;

	case 'alterarDataEntregaCabecalho':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$data = isset($r_data) ? $r_data : false;
		mysql_query("UPDATE T005 SET T005_Data_Entrega = '{$data}' WHERE T005_Id = '{$T005_Id}'");
		echo mysql_error();
		$resposta['code'] = true;
	break;

	case 'VEN002atualizarCamposProdutosEditar':

		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;

		$Aliquotas = $VEN001->retornaAliquotas($D009_Id, false, $T005_Id);

		$T005=mysql_query("SELECT * FROM T005 
		                  LEFT JOIN D024 on D024_Id=T005_D024_Id
		                  WHERE T005_Id='{$T005_Id}'");
		$mT005=mysql_fetch_assoc($T005);


	    $D001=mysql_query("SELECT * FROM D001 
	                      LEFT JOIN D049 ON D001_Id=D049_D001_Id 
	                      LEFT JOIN D009 ON D049_Id=D009_D049_Id
	                      LEFT JOIN D082 ON D082_Id=D049_D082_Id
	                      left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$mT005['T005_D024_Id']}'
	                      WHERE D009_Id='{$D009_Id}'");
	    $mD001=mysql_fetch_assoc($D001);
	    $mD001['D009_Valor_Custo_Unitario'] = gCorrigeNumero($mD001['D009_Valor_Custo_Unitario']);

	    $row=array();

	    $row['D006_Id'] 					= $Aliquotas['D006_Id'];
	    $row['D005_Id'] 					= $Aliquotas['D005_Id'];
	    $row['D009_C004_Id'] 				= $mD001['D009_C004_Id'];
	    $row['Aliquota_IPI'] 				= gCorrigeNumero($Aliquotas['Aliquota_IPI']);
	    $row['Situacao_Tributaria_IPI'] 	= $Aliquotas['Situacao_Tributaria_IPI'];
	    $row['D082_Id'] 					= $Aliquotas['D082_Id'];
	    $row['D059_Id'] 					= $Aliquotas['D059_Id'];
	    $row['ST_ICMS_Interno'] 			= gCorrigeNumero($Aliquotas['ST_ICMS_Interno']);
	    $row['ST_ICMS_Interno_MVA'] 		= gCorrigeNumero($Aliquotas['ST_ICMS_Interno_MVA']);
	    $row['ICMS_Inter_Estadual'] 		= gCorrigeNumero($Aliquotas['ICMS_Inter_Estadual']);
	    $row['ICMS_Inter_Estadual_MVA'] 	= gCorrigenumero($Aliquotas['ST_ICMS_Inter_Estadual_MVA']);
	    $row['ST_VA'] 						= gCorrigeNumero($Aliquotas['ST_VA']);
	    $row['Percentual_PIS'] 				= gCorrigeNumero($Aliquotas['Aliquota_PIS']);
	    $row['Flag_Isento_PIS'] 			= $Aliquotas['Flag_Isento_PIS'];
	    $row['Situacao_Tributaria_PIS'] 	= $Aliquotas['Situacao_Tributaria_PIS'];
	    $row['Percentual_COFINS'] 			= $Aliquotas['Aliquota_COFINS'];
	    $row['Flag_Isento_COFINS'] 			= $Aliquotas['Flag_Isento_COFINS'];
	    $row['Situacao_Tributaria_COFINS'] 	= $Aliquotas['Situacao_Tributaria_COFINS'];
	    $row['Aliquota_ICMS_Correto'] 		= gCorrigeNumero($Aliquotas['Aliquota_ICMS']);
	    $row['Percentual_Reducao_ICMS_Correto'] 	= gCorrigeNumero($Aliquotas['Percentual_Reducao_ICMS']);
	    $row['Flag_ST'] 					= $Aliquotas['Flag_ST'];
	    // print_r($row);
	    // die("AAAA");
	    //die("ST->".$row['Flag_ST']);

		/**
		 * DEFININDO QUAL O PREÇO QUE SERÁ RETORNADO
		 * 
		 */
		// verifica se está setado a flag lista preço do cabeçalho do orçamento, se estiver, manda ela, senão manda o id do cliente

/* 		$sql =	"SELECT T005_D024_Id, 
						T005_Flag_Lista_Preco, 
						T005_Flag_Acp, 
						T005_Casas_Decimais_Preco,
						T005_C004_Id,
						T005_Flag_Destino_Produto
				   FROM T005 
				  WHERE T005_Id = '{$T005_Id}'";
		$resultado = mysql_query($sql);
		$mT005 = mysql_fetch_assoc($resultado); */

		$cfop = $VEN001->retornaCFOP($mT005['T005_D024_Id'], $D009_Id, $mT005['T005_C004_Id'], $mT005['T005_Flag_Destino_Produto'], false, false, false, $mT005['T005_C004_Id_Estoque']);
		$row['D006_Id'] = $cfop;

		$dadosPrecoVenda      = $CAD002->retornaPrecoVenda($D009_Id, $mT005['T005_Flag_Lista_Preco'], $mT005['T005_D024_Id'], false, $cfop, $Aliquotas['Aliquota_ICMS'], 0, 0, $mT005['T005_Casas_Decimais_Preco'], $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
		$row['Preco_Produto'] = $dadosPrecoVenda[0];
		$row['demonstrativoPrecoVenda'] = serialize($dadosPrecoVenda[1]);
		$row['Custo_Produto'] = $dadosPrecoVenda[4];

		// Método que irá retonar o último preço que o produto foi vendido para o cliente
		$row['ultimoPrecoVenda'] = $VEN001->retornaltimoPrecoVenda($mT005['T005_D024_Id'], $D009_Id);
		$row2 = array_merge($row, $mD001);
		
		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row2;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'verificaAlcadaNovo':
		global $g;
		$msg="";
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$acao = isset($r_acao) ? $r_acao : false;

		//Verifica os dados dos itens do pedido
		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();
		$retornoRestricao = $VEN002->verificaDadosPedido($T005_Id);
        if($retornoRestricao !== true){
            $resposta['code'] = 'erro';
			$resposta['data'] = $retornoRestricao;
			break;
        }

        $T005  = mysql_query("SELECT T005_FLag_Status_Liberacao, 
									 T005_D024_Id, 
									 T005_Flag_Frete, 
									 T005_Valor_Frete, 
									 ROUND(SUM(T006_Valor_Total_Frete),2) AS FreteProdutos,
									 T005_Valor_Total,
									 D006_Flag_Gera_Contas,
									 T224_Flag_Tipo
								FROM T005 
						   LEFT JOIN T006 ON T006_T005_Id=T005_Id
						   LEFT JOIN D006 ON T005_D006_Id=D006_Id
						   LEFT JOIN T224 ON T005_T224_Id=T224_Id 
						       WHERE T005_Id='{$T005_Id}' GROUP BY T005_Id");

		$mT005 = mysql_fetch_array($T005);

		//Verifica o frete dos produtos caso obrigatório rateio do frete quando por conta do emitente
		if($mT005['T005_Valor_Frete'] != $mT005['FreteProdutos'] && $g['C031']['ratearFreteVendaNoCusto'] == 'S' && $mT005['T005_Flag_Frete'] == '0'){
			$resposta['code'] = 'erro';
			$resposta['data'] = "A soma do frete dos produtos é diferente do valor do frete no cabeçalho do pedido. <p>Para corrigir clique no botão \"Atualizar\" em seguida a opção \"Atualizar preço tabela\".";
			break;
		}

		// Irá verificar se deve validar dados do pedido de transferência, hoje não valida pagamento e transportadora. / T224_Flag_Tipo = 2 OC de transferencia
		$validaPedidoTransferencia = ($mT005['T224_Flag_Tipo'] == '2' && $g['C031']['validaDadosAoFaturarPedidoTransferencia'] == 'N') ? 'N' : 'S';
		// Precisa validar se tem parcela, se valida a alçada e pedido está sem parcela vai cair na análise
        if ($mT005['D006_Flag_Gera_Contas'] == "R" && $g['C031']['permissaoFaturarSemPreco'] == 'N' && $validaPedidoTransferencia == 'S') {
            $T090 = mysql_query("SELECT SUM(T090_Valor_Parcela) as TotalParcelas FROM T090 WHERE T090_T005_Id='{$T005_Id}'");
			if (mysql_num_rows($T090) <= 0) {
				$resposta['code'] = 'erro';
				$resposta['data'] = 'É necessário preencher a forma de pagamento';
				break;
            } else {
                $resT090 = mysql_fetch_array($T090);
                if ($mT005['T005_Valor_Total'] != $resT090['TotalParcelas']) {
					$resposta['code'] = 'erro';
					$resposta['data'] = 'O valor total da forma de pagamento difere do valor total do pedido';
					break;
				}
            }
        }

		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();
		if(!$VEN001->ven001TriggerPersonalizadaPularVerificacaoAlcada(false, $T005_Id)){
			$retorno = $VEN001->verificaAlcadaNovo(false, $T005_Id,$acao);
		}

		$T005=mysql_query("SELECT T005_FLag_Status_Liberacao, T005_D024_Id FROM T005 WHERE T005_Id='{$T005_Id}'");
		$mT005=mysql_fetch_array($T005);

		if ($mT005['T005_D024_Id'] == $g['C020']['C020_Cupom_Fiscal_Cliente']) {
			$mT005['T005_FLag_Status_Liberacao'] = 1;
		}

		if($mT005['T005_FLag_Status_Liberacao'] == 4){

			$motivo_Liberacao = mysql_query("SELECT T005_Motivo_Pedido_Liberacao FROM T005 WHERE T005_Id='{$T005_Id}'");
			$mMotivo_Liberacao = mysql_fetch_array($motivo_Liberacao);

			if(!empty($mMotivo_Liberacao['T005_Motivo_Pedido_Liberacao'])){
				$msg ="<br />".$mMotivo_Liberacao['T005_Motivo_Pedido_Liberacao'];
			}
			$msg="A liberação deste pedido exige maior alçada, pedir liberação? {$msg}";
		}

		if ($mT005['T005_FLag_Status_Liberacao'] != 4) {

			$resposta['code'] = true;
			$resposta['data'] = "Você tem certeza que deseja gerar o pedido?";

		} else {
			$resposta['code'] = false;
			$resposta['data'] = $msg;
		}
	break;

	case 'excluirItensMarcados':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$sqlT006 = mysql_query("SELECT * FROM T006 WHERE T006_T005_Id = '{$T005_Id}' and T006_Flag_Excluir = 1");
		while($T006 = mysql_fetch_array($sqlT006)){
			$retorno = $VEN002->excluirProduto($T006['T006_Id'], $T005_Id);
		}
		if(mysql_num_rows($sqlT006) > 0 && $retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Itens excluidos com sucesso";
		} else {
			$resposta['code'] = false;
			if(mysql_num_rows($sqlT006) > 0){
				$resposta['data'] = $retorno;
			} else {
				$resposta['data'] = "Nenhum item selecionado!";
			}
		}
	break;

	case 'atualizarCFOP':
		require_once("bibliotecas/classes/VEN002.php");
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$resposta['T005_Id'] = $T005_Id;

		$VEN002 = new VEN002();
		$retorno = $VEN002->ven002atualizarCFOP($T005_Id);
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizarCFOPPedidos':
		require_once("bibliotecas/classes/VEN002.php");
		$VEN002 = new VEN002();

		$extra = base64_decode($r_extra);
		$extra = gInsertExtraWhere($extra, "T005_Flag_Marcado = 'S'");

		$sqlT005 = mysql_query("SELECT T005_Id FROM T005 {$extra}");
			
		$fail=0;

		while($mT005 = mysql_fetch_array($sqlT005)){
			$retorno = $VEN002->ven002atualizarCFOP($mT005['T005_Id']);
			if ($retorno === true) {
				$resposta['code'] = true;
			}else{
				$resposta['code'] = false;
				$fail++;
			}
		}

		$mensagemAtualizacao = '<b><font color="red">Erro ao atualizar CFOPs</font><b><br>Quantidade de pedidos não atualizados:'.$fail;

		$resposta['data'] = $mensagemAtualizacao;
	break;

	case 'novaLiberacao':
		$T005_Id = mysql_real_escape_string($r_T005_Id);
		mysql_query("update T005 set T005_Flag_Status=6, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Flag_Separacao_Liberar='S', T005_Flag_Separacao='S',T005_Flag_Primeira_Liberacao_Credito = '0'  where T005_Id='{$T005_Id}'");
        mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Aguardando Nova Liberação','')");
	break;

	case 'imprimirDav':
		global $g;

		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;

		if (isset($r_gerarHtml)) {
			echo $VEN002->gerarDAV($T005_Id, true);
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $VEN002->gerarDAV($T005_Id);
			
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
		}
		die();
	break;
	
	case 'imprimirDav':
		global $g;

		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;

		if (isset($r_gerarHtml)) {
			echo $VEN002->gerarDAV($T005_Id, true);
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $VEN002->gerarDAV($T005_Id);
			
			echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
		}
		die();
	break;

	case 'ven002VerificarMoedaImpressao':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$sql = mysql_query("SELECT D044_Id, D044_Moeda, T005_Cotacao_Moeda FROM T005 LEFT JOIN D044 ON D044_Id=T005_D044_Id WHERE T005_Id = '{$T005_Id}'");
		$row = mysql_fetch_array($sql);

		if($row['D044_Id'] == '1'){
			$resposta['code'] = true;
		} else if($row['T005_Cotacao_Moeda'] <= 0){
			$resposta['code'] = false;
			$resposta['data'] = 'A cotação da moeda estrangeira está em branco, verifique a cotação da moeda na aba "Moeda".';
		} else {
			$resposta['code'] = 'verificarMoeda';
			$resposta['moeda'] = $row['D044_Moeda'];
			$resposta['data'] = $row['D044_Id'];
		}
	break;

	case 'carregarStatusPedido':
		$sqlT005 = mysql_query("SELECT T005_Flag_Status, T005_Nome_Status FROM T005 WHERE T005_Id = '{$r_T005_Id}'");
		$rowT005 = mysql_fetch_array($sqlT005);

		switch ($rowT005['T005_Flag_Status']) {
			case '1':
				$resposta['data'] = array('0' => 'Pendente', '6' => 'Análise de Crédito');
			break;
			case '2':
				$resposta['data'] = array('0' => 'Pendente', '6' => 'Análise de Crédito', '1' => 'Impressão');
			break;
			case '3':
				if($g['C031']['modeloExpedicao'] == '1'){
					$resposta['data'] = array('0' => 'Pendente', '6' => 'Análise de Crédito', '1' => 'Impressão', '2' => 'Separação', '10' => 'Conferência');
				} else if($g['C031']['modeloExpedicao'] == '2'){
					$resposta['data'] = array('0' => 'Pendente', '6' => 'Análise de Crédito', '1' => 'Impressão', '21' => 'Separação', '10' => 'Conferência', '22' => 'Faturamento');
				}
			break;
			case '5':
				$resposta['data'] = array('4' => 'Aguardando Coleta');
			break;
			case '10':
				if($g['C031']['modeloExpedicao'] == '1'){
					$resposta['data'] = array('0' => 'Pendente', '6' => 'Análise de Crédito', '1' => 'Impressão', '2' => 'Separação');
				} else if($g['C031']['modeloExpedicao'] == '2'){
					$resposta['data'] = array('0' => 'Pendente', '6' => 'Análise de Crédito', '21' => 'Separação');
				}
			break;
			case '21':
				$resposta['data'] = array('0' => 'Pendente', '6' => 'Análise de Crédito');
			break;
			case '22':
				$resposta['data'] = array('0' => 'Pendente', '6' => 'Análise de Crédito', '21' => 'Separação', '10' => 'Conferência');
			break;
		}
		$resposta['code'] = true;
	break;

	case 'alterarStatusPedido':
		$T005_Id = (!empty($r_T005_Id)) ? $r_T005_Id : false;
		$status = (!empty($r_status)) ? $r_status : false;
		mysql_query("UPDATE T005 SET T005_Flag_Status = '{$status}', T005_Nome_Status = T005_Status_Pedido(T005_Flag_Status, 1), T005_Flag_Separacao = 'S' WHERE T005_Id = '{$T005_Id}'");
		$cErro=mysql_error();
		if (!empty($cErro)) {
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao alterar status do pedido. $cErro";
		} else {
			$resposta['code'] = true;
		}
	break;

	case 'ven002VerificarEstoquePedido':
		$D009_Id = $r_D009_Id;
		$T006_Quantidade = gCorrigeNumeroInverte($r_T006_Quantidade);
		$sqlD009 = mysql_query("SELECT D009_Quantidade_Estoque_Liquido, D037_Unidade FROM D009 
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 ON D001_Id = D049_D001_Id 
			LEFT JOIN D037 ON D037_Id = D001_D037_Id
			WHERE D009_Id = '{$D009_Id}'");
		$rowD009 = mysql_fetch_array($sqlD009);
		if($rowD009['D009_Quantidade_Estoque_Liquido'] < $T006_Quantidade){
			$rowD009['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($rowD009['D009_Quantidade_Estoque_Liquido']);
			$resposta['data'] = "Estoque insuficiente, disponível {$rowD009['D009_Quantidade_Estoque_Liquido']} {$rowD009['D037_Unidade']}";
			$resposta['code'] = false;
		} else {
			$resposta['code'] = true;
		}
	break;

	case 'atualizarComponentesProdutos':
		require_once('bibliotecas/classes/EST017.php');
		$EST017 = new EST017();
		$retorno = $EST017->atualizarComponentes($r_T005_Id);
		if($retorno == true){
			$resposta['code'] = true;
		} else {
			$resposta['data'] = $retorno;
			$resposta['code'] = false;
		}
	break;

	case 'atualizarPedidoExterno':
		$T005_Id = (!empty($r_T005_Id)) ? $r_T005_Id : false;
		if(empty($T005_Id)){
			$resposta['data'] = 'Pedido não informado';
			$resposta['code'] = false;
		} else {
			require_once('bibliotecas/webservices/pluggto/integracaoPluggto.php');
			$integracao = new Pluggto();
			$retorno = $integracao->atualizarPedidoPluggto($T005_Id);
			if($retorno === true){
				$resposta['data'] = 'Pedido atualizado com sucesso!';
			} else {
				$resposta['data'] = "Erro ao atualizar pedido - Erro: {$retorno}";
				$resposta['code'] = false;
			}

		}
	break;

	case 'atualizarPedidoExternoMagento':
		log("entrou no atualizar pedido magento");
		$T005_Id = (!empty($r_T005_Id)) ? $r_T005_Id : false;
		if(empty($T005_Id)){
			$resposta['data'] = 'Pedido não informado';
			$resposta['code'] = false;
		} else {
			require_once('bibliotecas/webservices/magento/magentoSoap.php');
			$MagentoSoap = new MagentoSoap();

			$retorno = $MagentoSoap->atualizarPedidoMagento($T005_Id);
			log("RETORNO NO ATUALIZAR PEDIDO MAGENTO:".$retorno);
			if($retorno === true){
				$resposta['data'] = 'Pedido atualizado com sucesso!';
				$resposta['code'] = true;
			} else {
				$resposta['data'] = $retorno;
				$resposta['code'] = false;
			}

		}
	break;

	case 'atualizarPedidoTray':
		log("entrou no atualizar pedido tray");
		$T005_Id = (!empty($r_T005_Id)) ? $r_T005_Id : false;
		if(empty($T005_Id)){
			$resposta['data'] = 'Pedido não informado';
			$resposta['code'] = false;
		} else {
			require_once('bibliotecas/webservices/tray/comunicacaoTray.php');
			$Tray = new ComunicacaoTray();

			//Verifica os pedidos
			$retorno = $Tray->autenticacaoTray($T005_Id);
			log("RETORNO NO ATUALIZAR PEDIDO TRAY:".$retorno);
			if($retorno === true){
				$resposta['data'] = 'Pedido atualizado com sucesso!';
				$resposta['code'] = true;
			} else {
				$resposta['data'] = $retorno;
				$resposta['code'] = false;
			}
		}
	break;

	case 'atualizarPedidosMarcadosTray':
		global $g;
		require_once('bibliotecas/webservices/tray/comunicacaoTray.php');
		$Tray = new ComunicacaoTray();
		
		if ($r_inicio==1){

			$extra = base64_decode($r_extra);
			$extra = gInsertExtraWhere($extra, "T005_Flag_Marcado = 'S'");
			$extraSemGroupBy = substr($extra,0,-17); //retira o group by T005_Id

	        $sqlCache = mysql_query("SELECT T005_Id,T005_Flag_Plataforma_Ecommerce FROM T005 {$extra}");
	        $erro = mysql_error();

	        if (!empty($erro)) {
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro consultando itens dos pedidos: $erro';
	        }

	    	$count=0;

	    	while($rowCache = mysql_fetch_array($sqlCache)){
	    		$cache[$count]['T005_Id'] = $rowCache['T005_Id'];
		        $cache[$count]['T005_Flag_Plataforma_Ecommerce']= $rowCache['T005_Flag_Plataforma_Ecommerce'];
				$count++;

			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
		    $resposta['erro'] = 0;
	    	$resposta['concluido'] = 0;
			apc_delete("cacheT005-{$g['usuarioAtual']}");
			apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT005-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro ao criar variável no cache';
	        }else{
	        	$resposta['code'] = true;
	        }		    
			$resposta['T005_Id'] = 0;
	    }else {
			$cache = unserialize(apc_fetch("cacheT005-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT005-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro ao criar variável no cache resposta';
	        }else{
	        	$resposta['code'] = true;
	        }
	    }

	    $resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

		if($resposta['etapa'] >= $resposta['etapas']){
			$resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
		} else {
			$resposta['limit2'] += $resposta['registrosEtapa'] ;
		}

	    $resposta['percentual'] = ceil((100/$resposta['etapas']) * $resposta['etapa']);

		if($resposta['limit2'] < $resposta['totalRegistros']){
	    	$resposta['etapa'] = $resposta['etapa'] + 1;	
	    }else{
	    	$resposta['concluido'] = 1;
	    }
	   
	    $r_opcoes = json_decode($r_opcoes);

	    if(!is_array($r_opcoes)) { 
	        $opcoes = get_object_vars($r_opcoes);
	    }

	    $ok = 0;
		$erro = 0;
		$erros ="";
		
	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) {  

	    	if($cache[$i]['T005_Flag_Plataforma_Ecommerce']!=2){
					$erro++;
	           	 	$erros .= "Pedido ".$cache[$i]['T005_Id']." - Está com plataforma errada.<br />";
			}else{				
				$retorno = $Tray->autenticacaoTray($cache[$i]['T005_Id']);           
	           	if($retorno !== true){
	       	     	$erro++;
	           	 	$erros .= "Pedido ".$cache[$i]['T005_Id']." - ". $retorno."<br />";
	       		}else{
	       			mysqli_query("UPDATE T005 SET T005_Flag_Marcado='N' WHERE T005_Id='{$cache[$i]['T005_Id']}'");	  	
	       			$ok++;
	       		}
			}	    	
        }

		$resposta['data'] = '';        
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($etapa > $etapas){
			apc_delete("cacheT005-{$g['usuarioAtual']}");
			apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
		} else {
	        apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	        apc_store("cacheT005-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}
	break;

	case 'atualizarPedidosMarcadosMagento':
		global $g;
		require_once('bibliotecas/webservices/magento/magentoSoap.php');
		$MagentoSoap = new MagentoSoap();
		
		if ($r_inicio==1){

			$extra = base64_decode($r_extra);
			$extra = gInsertExtraWhere($extra, "T005_Flag_Marcado = 'S'");
			$extraSemGroupBy = substr($extra,0,-17); //retira o group by T005_Id

	        $sqlCache = mysql_query("SELECT T005_Id,T005_Flag_Plataforma_Ecommerce FROM T005 {$extra}");
	        $erro = mysql_error();

	        if (!empty($erro)) {
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro consultando itens dos pedidos: $erro';
	        }

	    	$count=0;

	    	while($rowCache = mysql_fetch_array($sqlCache)){
	    		$cache[$count]['T005_Id'] = $rowCache['T005_Id'];
		        $cache[$count]['T005_Flag_Plataforma_Ecommerce']= $rowCache['T005_Flag_Plataforma_Ecommerce'];
				$count++;
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
		    $resposta['erro'] = 0;
	    	$resposta['concluido'] = 0;
			apc_delete("cacheT005-{$g['usuarioAtual']}");
			apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT005-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro ao criar variável no cache';
	        }else{
	        	$resposta['code'] = true;
	        }		    
			$resposta['T005_Id'] = 0;
	    }else {
			$cache = unserialize(apc_fetch("cacheT005-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT005-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro ao criar variável no cache resposta';
	        }else{
	        	$resposta['code'] = true;
	        }
	    }

	    $resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

		if($resposta['etapa'] >= $resposta['etapas']){
			$resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
		} else {
			$resposta['limit2'] += $resposta['registrosEtapa'] ;
		}

	    $resposta['percentual'] = ceil((100/$resposta['etapas']) * $resposta['etapa']);

		if($resposta['limit2'] < $resposta['totalRegistros']){
	    	$resposta['etapa'] = $resposta['etapa'] + 1;	
	    }else{
	    	$resposta['concluido'] = 1;
	    }
	   
	    $r_opcoes = json_decode($r_opcoes);

	    if(!is_array($r_opcoes)) { 
	        $opcoes = get_object_vars($r_opcoes);
	    }

	    $ok = 0;
		$erro = 0;
		$erros ="";
		
	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) {
	    	log("ID:".$cache[$i]['T005_Id']."   plataforma:".$cache[$i]['T005_Flag_Plataforma_Ecommerce']);

	    	if($cache[$i]['T005_Flag_Plataforma_Ecommerce']!=3){
					$erro++;
	           	 	$erros .= "Pedido ".$cache[$i]['T005_Id']." - Está com plataforma errada.<br />";
			}else{				
				$retorno = $MagentoSoap->atualizarPedidoMagento($cache[$i]['T005_Id']);           
	           	if($retorno !== true){
	       	     	$erro++;
	           	 	$erros .= "Pedido ".$cache[$i]['T005_Id']." - Erro ao enviar dados, verifique o histórico do pedido.<br />";
	       		}else{
	       			mysqli_query("UPDATE T005 SET T005_Flag_Marcado='N' WHERE T005_Id='{$cache[$i]['T005_Id']}'");	  	
	       			$ok++;
	       		}
			}	    	
        }

		$resposta['data'] = '';        
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($etapa > $etapas){
			apc_delete("cacheT005-{$g['usuarioAtual']}");
			apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
		} else {
	        apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	        apc_store("cacheT005-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}
	break;

	case 'criarPedidoIntelipost':
		log("entrou no enviar dados inlipost");
		$T005_Id = (!empty($r_T005_Id)) ? $r_T005_Id : false;
		if(empty($T005_Id)){
			$resposta['data'] = 'Pedido não informado';
			$resposta['code'] = false;
		} else {
			require_once('bibliotecas/webservices/intelipost/apiIntelipost.php');
        	$Intelipost = new Intelipost();

			$retorno = $Intelipost->criarPedido($T005_Id);

			log("RETORNO NO ENVIAR DADOS INTELIPOST:".$retorno[1]."         retorno:$retorno[0]");
 
			if($retorno[0]=="SUCCESS"){
				mysql_query("UPDATE T005 set T005_Data_Envio_Elogistics=CURRENT_TIMESTAMP where T005_Id={$T005_Id}");
				$resposta['data'] = 'Dados enviados com sucesso!';
				$resposta['code'] = true;
			} else {
				$resposta['data'] = $retorno[1];
				$resposta['code'] = false;
			}
		}
	break;

	case 'criarPedidosMarcadosIntelipost':
		global $g;
		require_once('bibliotecas/webservices/intelipost/apiIntelipost.php');
        $Intelipost = new Intelipost();
		
		if ($r_inicio==1){

			$extra = base64_decode($r_extra);
			$extra = gInsertExtraWhere($extra, "T005_Flag_Marcado = 'S'");
			$extraSemGroupBy = substr($extra,0,-17); //retira o group by T005_Id

	        $sqlCache = mysql_query("SELECT T005_Id FROM T005 {$extra}");
	        $erro = mysql_error();

	        if (!empty($erro)) {
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro consultando itens dos pedidos: $erro';
	        }

	    	$count=0;

	    	while($rowCache = mysql_fetch_array($sqlCache)){
	    		$cache[$count]['T005_Id'] = $rowCache['T005_Id'];
				$count++;
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
		    $resposta['erro'] = 0;
	    	$resposta['concluido'] = 0;
			apc_delete("cacheT005-{$g['usuarioAtual']}");
			apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT005-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro ao criar variável no cache';
	        }else{
	        	$resposta['code'] = true;
	        }		    
			$resposta['T005_Id'] = 0;
	    }else {
			$cache = unserialize(apc_fetch("cacheT005-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT005-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro ao criar variável no cache resposta';
	        }else{
	        	$resposta['code'] = true;
	        }
	    }

	    $resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

		if($resposta['etapa'] >= $resposta['etapas']){
			$resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
		} else {
			$resposta['limit2'] += $resposta['registrosEtapa'] ;
		}

	    $resposta['percentual'] = ceil((100/$resposta['etapas']) * $resposta['etapa']);

		if($resposta['limit2'] < $resposta['totalRegistros']){
	    	$resposta['etapa'] = $resposta['etapa'] + 1;	
	    }else{
	    	$resposta['concluido'] = 1;
	    }
	   
	    $r_opcoes = json_decode($r_opcoes);

	    if(!is_array($r_opcoes)) { 
	        $opcoes = get_object_vars($r_opcoes);
	    }

	    $ok = 0;
		$erro = 0;
		$erros ="";
		
	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) {
	    	log("ID:".$cache[$i]['T005_Id']."   plataforma:".$cache[$i]['T005_Flag_Plataforma_Ecommerce']);
	    				
			$retorno = $Intelipost->criarPedido($cache[$i]['T005_Id']);				      
           	if($retorno[0]=="SUCCESS"){
           	 	mysqli_query("UPDATE T005 set T005_Data_Envio_Elogistics=CURRENT_TIMESTAMP where T005_Id='{$cache[$i]['T005_Id']}'"); 	
       			$ok++;
       		}else{
       	     	$erro++;
           	 	$erros .= "Pedido ".$cache[$i]['T005_Id']." - Erro ao enviar dados, verifique o histórico do pedido.<br />";
       		}
				    	
        }

		$resposta['data'] = '';        
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($etapa > $etapas){
			apc_delete("cacheT005-{$g['usuarioAtual']}");
			apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
		} else {
	        apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	        apc_store("cacheT005-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}
	break;

	case 'atualizarPedidosMarcadosPluggto':
		log("entrou no atualizar pedidos pluggto");
		require_once('bibliotecas/webservices/pluggto/integracaoPluggto.php');
		$integracao = new Pluggto();
		$extra = base64_decode($r_extra);
		$extra = gInsertExtraWhere($extra, "T005_Flag_Marcado = 'S'");

		$sqlT005 = mysql_query("SELECT T005_Id,T005_Flag_Plataforma_Ecommerce FROM T005 {$extra}");
		
		$success = 0;
		$fail = 0;
		$errado = 0;

		while($mT005 = mysql_fetch_array($sqlT005)){

			if($mT005['T005_Flag_Plataforma_Ecommerce']!=1){
				$errado++;
			}else{
				
				$retorno = $integracao->atualizarPedidoPluggto($mT005['T005_Id']);

				mysqli_query("UPDATE T005 SET T005_Flag_Marcado='N' WHERE T005_Id='{$mT005['T005_Id']}'");
				
				if ($retorno === true) {
					$success++;
				}else{
					$fail++;
				}
			}
		}

		$mensagemAtualizacao = "<br>Quantidade de pedidos atualizados:".$success."<br>Quantidade de pedidos não atualizados:".$fail."<br>Quantidade de pedidos com plataforma errada:".$errado;

		$resposta['code'] = true;
		$resposta['data'] = $mensagemAtualizacao;

		
	break;

	case 'atualizarPrecoTabela':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;

		$resposta = $VEN002->atualizarPrecoTabela($T005_Id);

	break;

	case 'pedidoWhats':
		global $g;

		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$phone = $r_phone;

		$T006 = mysql_query("select T006_Codigo_Produto, 
									T006_Descricao_Produto, 
									T006_Quantidade, 
									T006_Valor_Preco_Sem_Desconto_Unitario,
									T006_Valor_Preco_Bruto
							   from T006
							  where T006_T005_Id='{$T005_Id}'");

		$retorno = "Pedido N. " . $T005_Id . chr(10).chr(13);
		$retorno .= "---------------------------------" . chr(10);
		while ($mT006 = mysql_fetch_array($T006)){
			   $preco = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? $mT006['T006_Valor_Preco_Bruto'] : $mT006['T006_Valor_Preco_Sem_Desconto_Unitario'];
			   $retorno .= 'Código: ' . $mT006['T006_Codigo_Produto'] . chr(10) .
						   'Descrição: ' . $mT006['T006_Descricao_Produto'] . chr(10) .
						   'Quantidade: ' . gCorrigeNumero($mT006['T006_Quantidade']) . chr(10) .
						   'Preço unitário: ' . gCorrigeNumero($preco) . chr(10);
			   $retorno .= "---------------------------------" . chr(10);
		}

		$T005 = mysql_query("select C007_Nome, T005_D013_Id, T005_D024_Id from T005 left join C007 on C007_Id=T005_C007_Id where T005_Id='$T005_Id'");
		$mT005 = mysql_fetch_array($T005);

		$assunto   = 'Pedido N. ' . $T005_Id;
		$de        = $mT005['C007_Nome'];
		$para      = 'Whatsapp: ' .$phone;

		mysql_query("INSERT INTO T139 ( T139_Pasta, 
										T139_Uid, 
										T139_Titulo, 
										T139_De, 
										T139_Para, 
										T139_Data_Hora, 
										T139_Estrutura, 
										T139_Mensagem_Corpo, 
										T139_Flag_Lido, 
										T139_D024_Id, 
										T139_D013_Id, 
										T139_T005_Id, 
										T139_C007_Id) 
								VALUES ('S', 
										0, 
										'".mysql_real_escape_string($assunto)."', 
										'".mysql_real_escape_string($de)."', 
										'".mysql_real_escape_string($para)."', 
										'".date('Y-m-d H:i:s')."', 
										'" . mysql_real_escape_string($retorno) . "', 
										'" . mysql_real_escape_string($retorno) . "', 
										'1', 
										'".mysql_real_escape_string($mT005['T005_D024_Id'])."', 
										'".mysql_real_escape_string($mT005['T005_D013_Id'])."', 
										'".mysql_real_escape_string($T005_Id)."', 
										'".mysql_real_escape_string($g['usuarioAtual'])."')");

		$sqlT259 = mysql_query("SELECT T259_Id, 
		   							   T259_C007_Id 
								  FROM D085 
							 LEFT JOIN T259 ON T259_Id=D085_T259_Id 
							 	 WHERE D085_Entidade = 'T005' 
								   AND D085_Id_Entidade = '{$T005_Id}'");

		$rowT259 = mysql_fetch_assoc($sqlT259);
		require_once('bibliotecas/funcoesCallback/assuntoTarefa.php');
		$arrayAssuntoTarefa = array(
				'campoAtual' => '',
				'todosCampos' => array(
				'T259_Id' => $rowT259['T259_Id'],
				'T259_C007_Id' => $rowT259['T259_C007_Id']
			)
		);
		assuntoTarefa($arrayAssuntoTarefa, false);

		$resposta['code'] = true;
		$resposta['data'] = urlencode($retorno);


	break;

	case 'ven002PhoneWhats':
		global $g;

		$T005=mysql_query("select T005_Id,T005_D013_Id, D024_DDD_Celular, D024_Celular, D013_DDD_Celular, D013_Celular from T005 left join D024 on D024_Id=T005_D024_Id left join D013 on D013_Id=T005_D013_Id where T005_Id='{$r_T005_Id}'");
		$mT005 = mysql_fetch_assoc($T005);

		$D013_Id=$mT005['T005_D013_Id'];

		$phoneWhatsApp = '';
		$notificaSemPhone = '';
		if (!empty($mT005['D013_Celular'])){
			$phoneWhatsApp = '+55'.trim($mT005['D013_DDD_Celular']).trim($mT005['D013_Celular']);		
		} else if (!empty($mT005['D024_Celular'])) {
			$phoneWhatsApp = '+55'.trim($mT005['D024_DDD_Celular']).trim($mT005['D024_Celular']);		
		} else {
			$notificaSemPhone = " ** Contato não possui celular cadastrado, atualize agora! ** ";
		}
		$phoneWhatsApp = str_replace('-','',$phoneWhatsApp);

		$resposta['phoneWhatsApp'] = urlencode($phoneWhatsApp);
		$resposta['notificaSemPhone'] = htmlspecialchars($notificaSemPhone);

	break;

	case 'pedidoWhatsPDF':
		global $g;

		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
		$moeda = isset($r_moeda) ? $r_moeda : 1;
		$phone = $r_phone;

		$T090 = mysql_query("SELECT SUM(T090_Valor_Parcela) as TotalParcelas, T005_Valor_Total FROM T090 LEFT JOIN T005 ON T005_Id = T090_T005_Id WHERE T090_T005_Id='{$T005_Id}'");
		$resT090 = mysql_fetch_array($T090);

		$sql="SELECT D006_Flag_Gera_Contas FROM T005 LEFT JOIN D006 on D006_Id=T005_D006_Id WHERE T005_Id='{$T005_Id}'";
		$resultado=mysql_query($sql);
		$row=mysql_fetch_array($resultado);

		if($row['D006_Flag_Gera_Contas'] == "R"){
			if($resT090['TotalParcelas'] == 0){
				$erro = "É necessário preencher a forma de pagamento";
			} else {
				if($resT090['T005_Valor_Total'] != $resT090['TotalParcelas']){
					$erro = "Valor das parcelas não confere com o total do pedido.";
				}
			}
		}
		if(isset($erro)){

			$resposta['code'] = false;
			$resposta['data'] = $erro;

		} else {

			// Arquivo
			$dir = 'whatsapp/';
			is_dir($dir) OR mkdir($dir, 0777, true);
			
			$filename             = md5('T005'.$T005_Id.uniqid('',true)).".pdf";
			$file                 = $dir . $filename;
			$fileFull             = $g['pathRaiz'] . '/' . $file;
			$urlFile              = $g['urlRaiz'] . 'hardness3/' . $file;
			
            $r_link = '/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&gerarHtml=1&T005_Id=' . $T005_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --minimum-font-size 13 --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
			
			if(file_exists($file)){
				$texto  = "Olá, segue link do nosso pedido N. " . $T005_Id . chr(10);
				$texto .= $urlFile;

				$T005 = mysql_query("select C007_Nome, T005_D013_Id, T005_D024_Id from T005 left join C007 on C007_Id=T005_C007_Id where T005_Id='$T005_Id'");
				$mT005 = mysql_fetch_array($T005);

				$assunto   = 'Pedido N. ' . $T005_Id;
				$de        = $mT005['C007_Nome'];
				$para      = 'Whatsapp: ' .$phone;

				mysql_query("INSERT INTO T139 ( T139_Pasta, 
												T139_Uid, 
												T139_Titulo, 
												T139_De, 
												T139_Para, 
												T139_Data_Hora, 
												T139_Estrutura, 
												T139_Mensagem_Corpo, 
												T139_Flag_Lido, 
												T139_D024_Id, 
												T139_D013_Id, 
												T139_T005_Id, 
												T139_C007_Id) 
										VALUES ('S', 
												0, 
												'".mysql_real_escape_string($assunto)."', 
												'".mysql_real_escape_string($de)."', 
												'".mysql_real_escape_string($para)."', 
												'".date('Y-m-d H:i:s')."', 
												'" . mysql_real_escape_string($texto) . "', 
												'" . mysql_real_escape_string($texto) . "', 
												'1', 
												'".mysql_real_escape_string($mT005['T005_D024_Id'])."', 
												'".mysql_real_escape_string($mT005['T005_D013_Id'])."', 
												'".mysql_real_escape_string($T005_Id)."', 
												'".mysql_real_escape_string($g['usuarioAtual'])."')");


				$sqlT259 = mysql_query("SELECT T259_Id, 
											T259_C007_Id 
										FROM D085 
									LEFT JOIN T259 ON T259_Id=D085_T259_Id 
										WHERE D085_Entidade = 'T005' 
										AND D085_Id_Entidade = '{$T005_Id}'");

				$rowT259 = mysql_fetch_assoc($sqlT259);
				require_once('bibliotecas/funcoesCallback/assuntoTarefa.php');
				$arrayAssuntoTarefa = array(
						'campoAtual' => '',
						'todosCampos' => array(
						'T259_Id' => $rowT259['T259_Id'],
						'T259_C007_Id' => $rowT259['T259_C007_Id']
					)
				);
				assuntoTarefa($arrayAssuntoTarefa, false);

				$resposta['code'] = true;
				$resposta['data'] = urlencode($texto);
			}else{
				$resposta['code'] = false;
				$resposta['data'] = "Houve um problema ao gerar o PDF, favor tentar novamente";
			}
		}
	break;

	case 'gatilhoPrecoPedidoPorQtd':
		// Pode ser personalizado conforme necessidade do cliente		
		$resposta['code'] = false;
	break;

	case 'enviarParaWMS':
		require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");

		$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;

		if(empty($T005_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = "Pedido não encontrado!";
		}

		$WMS = new WMS();
		$WMS->integrarPedido('INSERT', $T005_Id);

		if(!empty($WMS->getErrorMessage())) {
			$resposta['code'] = false;
			$resposta['data'] = $WMS->getErrorMessage();
		} else {
			mysql_query("UPDATE T005A SET T005A_Data_Envio_WMS = NOW() WHERE T005A_T005_Id='{$T005_Id}'");
			
			$resposta['code'] = true;
			$resposta['data'] = "Pedido enviado para WMS com sucesso!";
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
