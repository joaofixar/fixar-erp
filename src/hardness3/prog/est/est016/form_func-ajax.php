<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * est - EST015 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */
$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();
require_once('bibliotecas/classes/EST015.php');
$EST015 = new EST015();
require_once('bibliotecas/classes/EST016.php');
$EST016 = new EST016();

/**
 * Ações
 */
switch($g['r'][3]) {

	case 'alterarStatusPedido':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		log("T005_Id no trocarStatusPedido= ".$T005_Id);
		$T005=mysql_query("UPDATE T005 set T005_Flag_Status = '0' WHERE T005_Id='{$T005_Id}'"); 
		$resposta['code'] = true;
			

	break;

	case 'desmarcarCheckbox':
		log("entra no desmarcar checkbox");
		$sql = base64_decode($r_extra);
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;

		$IdsRemoverFlag = array();
		$D049s = array();
		$res = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res)) {
			if ($r_origem == 'estoque') {
				if ($row['D009_Flag_Cotar'] == 'S' && $row['D009_C004_Id'] == $g['empresaAtual'] && $row['D009_C007_Id_Comprar'] == $g['usuarioAtual']) {
					$IdsRemoverFlag[] = $row['D009_Id'];
					$D049s[] = array(
						'D049_Id' => $row['D049_Id'],
						'Quantidade' => $row['D009_Quantidade_Comprar']
					);
				}
			} else {
				if (empty($row['T219_T218_Id']) && $row['T006_Flag_Gerar_Cotacao'] == 'S' && $row['T006_C007_Id_Comprador'] == $g['usuarioAtual']) {
					$IdsRemoverFlag[] = $row['T006_Id'];
					$D049s[] = array(
						'D049_Id' => $row['D049_Id'],
						'Quantidade' => $row['Falta'],
						'T006_Id' => $row['T006_Id'] // é utilizado para setar o T006_T225_Id
					);
				}
			}
		}
		foreach ($IdsRemoverFlag as $id) {
			if ($r_origem == 'estoque') {
				log("D009_Id no ajax= ".$id);
				mysql_query("UPDATE D009 SET D009_Flag_Cotar = 'N' WHERE D009_Id = '{$id}'");
			} else {
				mysql_query("UPDATE T006 SET T006_T224_Id = '{$T224_Id}', T006_Flag_Gerar_Cotacao = 'N' WHERE T006_Id = '{$id}'");
			}
		}
	break;

	case 'gerarOT':
		log("entra no gerarOT");

		$C004_Id = isset($r_C004_Id) ? $r_C004_Id : false;
		$gerarOTM5 = isset($r_gerarOTM5) ? $r_gerarOTM5 : 'S';
		$sql = base64_decode($r_extra);
		log("origem no ajax= ".$r_origem);
		$IdsRemoverFlag = array();
		$D049 = array();
		$estoqueInsuficiente = "";
		$res = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res)) {
			if ($r_origem == '1') {
				log("entra no estoque GerarOTM5 " . $gerarOTM5);
				if ($row['D009_Flag_Cotar'] == 'S' && $row['D009_C004_Id'] == $g['empresaAtual'] && $row['D009_Quantidade_Comprar']>0 && $row['D009_C007_Id_Comprar'] == $g['usuarioAtual']) {
					
					// Pega o D049_Id com o D009_Id do pdt na empresa atual, 
					// Busca o estoque liquido com o D049_Id e o Id da empresa que vai transferir o pdt
					$sqlD009 = "SELECT D009_Quantidade_Estoque_Liquido FROM D009
							WHERE D009_D049_Id = (
								SELECT D009_D049_Id FROM D009
								WHERE D009_Id = '{$row['D009_Id']}'
							) AND D009_C004_Id = '{$C004_Id}'";
									
					$D009 = mysql_query($sqlD009);
					$mD009 = mysql_fetch_assoc($D009);
					($mD009['D009_Quantidade_Estoque_Liquido'] <= 0) ? $mD009['D009_Quantidade_Estoque_Liquido'] = "0.00" : $mD009['D009_Quantidade_Estoque_Liquido'];

					if ($row['D009_Quantidade_Comprar']>$mD009['D009_Quantidade_Estoque_Liquido'] && $gerarOTM5=='N'){
						$estoqueInsuficiente .= "O produto {$row['D001_Codigo_Produto']} não possui estoque suficiente, requerido: {$row['D009_Quantidade_Comprar']} > disponível: {$mD009['D009_Quantidade_Estoque_Liquido']}<br/>";
					} else {
						$IdsRemoverFlag[] = $row['D009_Id'];
						$D049[] = array(
							'D049_Id' => $row['D049_Id'],
							'Quantidade' => $row['D009_Quantidade_Comprar']
						);
					}
				}
			} else if($r_origem == '2') {

				if (empty($row['T219_T218_Id']) && $row['T006_Flag_Gerar_Cotacao'] == 'S' && $row['Falta'] > 0 && $row['T006_C007_Id_Comprador'] == $g['usuarioAtual']) {

					// Pega o D049_Id com o D009_Id do pdt na empresa atual, 
					// Busca o estoque liquido com o D049_Id e o Id da empresa que vai transferir o pdt
					$sqlD009 = "SELECT D009_Quantidade_Estoque_Liquido FROM D009
							WHERE D009_D049_Id = (
								SELECT D009_D049_Id FROM D009
								WHERE D009_Id = '{$row['D009_Id']}'
							) AND D009_C004_Id = '{$C004_Id}'";
									
					$D009 = mysql_query($sqlD009);
					$mD009 = mysql_fetch_assoc($D009);
					($mD009['D009_Quantidade_Estoque_Liquido'] <= 0) ? $mD009['D009_Quantidade_Estoque_Liquido'] = "0.00" : $mD009['D009_Quantidade_Estoque_Liquido'];

					if ($row['Falta'] > $mD009['D009_Quantidade_Estoque_Liquido'] && $gerarOTM5 == 'N'){
						$estoqueInsuficiente .= "O produto {$row['D001_Codigo_Produto']} não possui estoque suficiente, requerido: {$row['Falta']} - disponível: {$mD009['D009_Quantidade_Estoque_Liquido']}<br/>";
					} else {
						$IdsRemoverFlag[] = $row['T006_Id'];
						$D049[] = array(
							'D049_Id'    => $row['D049_Id'],
							'Quantidade' => $row['Falta'],
							'T006_Id'    => $row['T006_Id'] // é utilizado para setar o T006_T225_Id
						);
					}
				}
			}
		}

		if (!empty($estoqueInsuficiente))
		{
			$resposta['code'] = false;
			$resposta['data'] = $estoqueInsuficiente;

		} else if (count($D049)<=0) {

			$resposta['code'] = false;
			$resposta['data'] = 'Não exitem itens marcados com quantidade para comprar';

		} else {
			
			$retorno = $EST016->gerarOT($C004_Id, $D049, $gerarOTM5);

			if (is_array($retorno)) {
				$resposta['code'] = true;
				$resposta['T224_Id'] = $retorno['T224_Id'];
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}

		}

	break;

	case 'dividirOrdemCompra':
		
		log("entrou no ajax dividirOrdemCompra");
		$T224_Id_Antigo = isset($r_T224_Id) ? $r_T224_Id : false;
		$C004_Id_Novo = isset($r_C004_Id) ? $r_C004_Id : false;
		$r_sql = base64_decode($r_extra1);
		log("sql= ".$r_sql);
		log("C004_Id_Novo= ".$C004_Id_Novo);
		
		
		
		
		$retorno = $EST016->dividirOrdemCompra($T224_Id_Antigo, $C004_Id_Novo, $r_sql);
		if (is_array($retorno)) {
			log("entra aqui");
			$resposta['code'] = true;
			$resposta['T224_Id'] = $retorno['T224_Id'];
			$resposta['T224_Id_Antigo'] = $retorno['T224_Id_Antigo'];
			log("T224_Id antigo no ajax= ".$retorno['T224_Id_Antigo']);
		} else {
			log("entra aqui1");
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'totalizarOrdemCompra':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		log("T224_Id no totalziarordemcompra= ".$T224_Id);
		mysql_query("CALL T224_Gravar_Totalizacao_3('{$T224_Id}')");
		$cErro=mysql_error();
		if (!empty($cErro))
		{
		  	$resposta['code'] = false;
			$resposta['data'] = "Erro ao totalizar ordem de compra: $cErro";
		}else{
			$resposta['code'] = true;
			$resposta['T224_Id'] = $T224_Id;	
		}
	break;

	case 'reprocessaHistorico':
		log("entrou no ajax reprocessaHistorico");
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
        $resposta['T224_Id'] = $T224_Id;
        $T224_Id_Antigo = isset($r_T224_Id_Antigo) ? $r_T224_Id_Antigo : false;

		$resposta['code'] = true;
		if ($r_inicio==1){
	        $CAD002 = new CAD002();
	        log("T224_Id_antigo no ajax de reprocessa= ".$T224_Id_Antigo);
	        mysql_query("CALL T224_Gravar_Totalizacao_3('{$T224_Id_Antigo}')");

	        $sqlT224 = "SELECT T225_D009_Id, 
	                           D009_Id, 
	                           D001_Id, 
	                           T225_Id
						  FROM T224 
					 LEFT JOIN T225 ON T225_T224_Id= T224_Id 
					 LEFT JOIN D009 ON T225_D009_Id= D009_Id 
					 LEFT JOIN D049 ON D009_D049_Id = D049_Id
					 LEFT JOIN D001 ON D001_Id = D049_D001_Id
					     WHERE T224_Id='{$T224_Id_Antigo}'"; 

			$resultT224 = mysql_query($sqlT224);					

	        $count = 0;
	      	while($rowCache = mysql_fetch_array($resultT224)){
	        	$cache[$count]['D001_Id']= $rowCache['D001_Id'];
	        	$cache[$count]['T225_Id']= $rowCache['T225_Id'];
				$count++;
				log("count= ".$count);
			}

			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			apc_delete("cacheD009-{$g['usuarioAtual']}");
			apc_delete("cacheD009-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheD009-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			$cache = unserialize(apc_fetch("cacheD009-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheD009-resposta-{$g['usuarioAtual']}"));
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
	   
	    $opcoes = json_decode($r_opcoes, true);
	    if (!is_array($opcoes)) $opcoes = null;

	   	$ok = 0;
		$erro = 0;
		$erros ="";

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	log("T225_Id no reprocessaHistorico= ".$cache[$i]['T225_Id']);
	    	log("D001_Id no reprocessaHistorico= ".$cache[$i]['D001_Id']);
	    	$retorno = $CAD002->D001_reprocessa_historico($cache[$i]['D001_Id']);
	    	mysql_query("CALL T225_Gravar_Totalizacao('{$cache[$i]['T225_Id']}')");	           
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
			apc_delete("cacheD009-{$g['usuarioAtual']}");
			apc_delete("cacheD009-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheD009-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheD009-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;

	case 'atualizarCamposProdutos':
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		
		/*
		T225_Ultimo_Preco(D001_Id,'$mT224[T218_D024_Id]','$T218_Id',1) as Preco_Consultado,
		T225_Ultimo_Preco(D001_Id,'$mT218[T218_D024_Id]','$T218_Id',2) as Preco_Confirmado
		*/
			
		$sql =	"SELECT T224_D024_Id,T224_Flag_Destino_Produto from T224 WHERE T224_Id = '{$T224_Id}'";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		$T224_Flag_Destino_Produto = $row['T224_Flag_Destino_Produto'];
		
		$sql = <<<EOT
			SELECT *
	   				from D001
	   			left join D049 on D049_D001_Id=D001_Id
	   			left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['T224_D024_Id']}'
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   				WHERE D009_Id = '{$D009_Id}'
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		$row['estoqueLiquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
		$row['estoqueLoja'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Loja']);
		$row['estoqueTotal'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']+$row['D009_Quantidade_Estoque_Loja']);
		$row['ordemCompra'] = gCorrigeNumero($row['D009_Quantidade_OC']);
		$row['estoqueFora'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Fora']);
		$row['T224_Flag_Destino_Produto'] = $T224_Flag_Destino_Produto;

		$D005_Id=mysql_query("SELECT D009_Classificacao_Fiscal(D009_Id) AS Classificacao_Fiscal FROM D009 WHERE D009_Id='{$D009_Id}'");
		$mD005=mysql_fetch_array($D005_Id);

		$var=mysql_query("SELECT D005_Id FROM D005 WHERE D005_Classificacao_Fiscal='{$mD005['Classificacao_Fiscal']}' and D005_Flag_Ativo='S'");
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
		
		//$row['T225_Valor_Preco_Sem_Desconto_Unitario'] = '';
		
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
			          D024_Flag_Contribuinte ";
		$cClausula.=" from T224 ";
		$cClausula.=" left join D024 on D024_Id=T224_D024_Id ";
		$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
		$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T224_C004_Id ";
		$cClausula.=" left join C004 on C004_Id=T224_C004_Id ";
		$cClausula.=" where T224_Id='{$T224_Id}'";

		$T218=mysql_query($cClausula);
		$cErro=mysql_error();
		if (!empty($cErro))
		{
		   return "$cErro/p/p$cClausula";
		   exit;
		}
		$mT218=mysql_fetch_array($T218);	
		$sql = <<<EOT
			SELECT 
				D009_Id, 
				D009_IPI('{$mT218['T224_C004_Id']}',D009_Id,'{$mT218['T224_D024_Id']}','{$mT218['T224_D006_Id']}','1','{$mT218['T224_Flag_ACP']}') as Aliquota_IPI 
			FROM 
				D009 
			where D009_Id='{$D009_Id}'
EOT;
		$result = mysql_query($sql);
		$row2 = mysql_fetch_assoc($result);
		$row['Aliquota_IPI'] = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'],2,',','.');
		
		/**
		 * DEFININDO QUAL O PREÇO QUE SERÁ RETORNADO
		 * 
		 * $row['D041_Valor_Base'] = number_format($row['D041_Valor_Base'],2,',','.');
		 */
		if(!empty($mT218['T224_Flag_Lista_Preco'])){
			$row['T225_Valor_Preco_Sem_Desconto_Unitario'] = $CAD002->cad002RetornaPrecoCompra($row['D009_Id'], $mT218['T224_Flag_Lista_Preco'], $mT218['T224_D024_Id'], $mT218['T224_Flag_ACP']);
		}else{
			$row['T225_Valor_Preco_Sem_Desconto_Unitario'] = $CAD002->cad002RetornaPrecoCompra($row['D009_Id'], null, $mT218['T224_D024_Id']);
		}

		// Método que irá retonar o último preço que o produto foi estdido para o cliente
		$row['UltimoPrecoCompra'] = $EST015->retornaUltimoPrecoCompra($row['D049_Id'], $mT218['T224_D024_Id']);

		$row['D006_Id'] = $EST015->retornaCFOP($mT218['T224_D024_Id'], $row['D009_Id']);
			
		if (!empty($row)) {
			$resposta['code'] = true;
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
			
			
			$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
			$IPV = isset($r_IPV) ? $r_IPV : false;
			
			$IPV = str_replace('.', '', $r_IPV);
			$IPV = str_replace(',', '.', $IPV);
			
			$retorno = $EST016->est016aplicarIpvMinimo($T224_Id, $IPV);
			
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
		
		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$T224_Id_Origem = isset($r_T224_Id_Origem) ? $r_T224_Id_Origem : false;
		
		
		
		
		$retorno = $EST016->importarItens($T224_Id, $T224_Id_Origem);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Pedidos importado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizarAliquota':
		
		
		
		global $g;
		$resposta['code'] = true;	
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		log("T224_Id no atualizarAliquota".$T224_Id);
		$resposta['T224_Id'] = $T224_Id;

		if ($r_inicio==1){
			if (empty($T224_Id)) {
            	$resposta['code'] = false;
            	$resposta['data'] = 'Informe o ID da ordem de compra';
        	}

	        //=====================================================================
	        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        //-- RODA A REGRA CFOP
	        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        
	        $cSql_T225 = mysql_query("SELECT D009_Id,
	        	                             T219_Id,
	                                         T225_Id,
	                                         D001_Codigo_Produto,
	                                         T225_Item,
	                                         T219_Flag_Origem_Produto,
	                                         D001_D037_Id,
	                                         D024_Situacao_Tributaria_PIS_COFINS,
	                                         D024_Flag_Isento_PIS_COFINS,
	                                         D001_Peso_Unitario_Kg,
	                                         T224_Flag_Devolucao,
	        	                             T224_Flag_Calcular_IPI,
	                                         T224_Flag_Calcular_ICMS,
	                                         T224_Flag_Calcular_ST       
	        	                        FROM T224
	        	                   LEFT JOIN D006 ON D006_Id=T224_D006_Id 
	                               LEFT JOIN T225 ON T224_Id=T225_T224_Id
	                               LEFT JOIN D024 ON D024_Id=T225_D024_Id
	                               LEFT JOIN D009 ON D009_Id=T225_D009_Id
	                               LEFT JOIN D049 ON D049_Id=D009_D049_Id
	                               LEFT JOIN D001 ON D001_Id=D049_D001_Id
	                               LEFT JOIN T219 ON T219_Id=T225_T219_Id 
	                                   WHERE T224_Id='{$T224_Id}'");
	       	$erroMysql = mysql_error();
	        if (!empty($erroMysql)) 
	        {
	        	$resposta['code'] = false;
	            $resposta['data'] = "Erro consultando itens da ordem de compra: $erroMysql $cClausula";
	        }

	        $count = 0;
	      	while($rowCache = mysql_fetch_array($cSql_T225)){
	        	$cache[$count]['D009_Id']= $rowCache['D009_Id'];
	        	$cache[$count]['T219_Id']= $rowCache['T219_Id'];
		       	$cache[$count]['T225_Id']= $rowCache['T225_Id'];
		       	$cache[$count]['D001_Codigo_Produto']= $rowCache['D001_Codigo_Produto'];
		   		$cache[$count]['T225_Item'] = $rowCache['T225_Item'];	
		   		$cache[$count]['T219_Flag_Origem_Produto'] = $rowCache['T219_Flag_Origem_Produto'];	
		   		$cache[$count]['D001_D037_Id'] = $rowCache['D001_D037_Id'];	
		   		$cache[$count]['D024_Situacao_Tributaria_PIS_COFINS'] = $rowCache['D024_Situacao_Tributaria_PIS_COFINS'];	
		   		$cache[$count]['D024_Flag_Isento_PIS_COFINS'] = $rowCache['D024_Flag_Isento_PIS_COFINS'];	
		   		$cache[$count]['D001_Peso_Unitario_Kg'] = $rowCache['D001_Peso_Unitario_Kg'];
		   		$cache[$count]['T224_Flag_Devolucao'] = $rowCache['T224_Flag_Devolucao'];	
		   		$cache[$count]['T224_Flag_Calcular_IPI'] = $rowCache['T224_Flag_Calcular_IPI'];	
		   		$cache[$count]['T224_Flag_Calcular_ICMS'] = $rowCache['T224_Flag_Calcular_ICMS'];	
		   		$cache[$count]['T224_Flag_Calcular_ST'] = $rowCache['T224_Flag_Calcular_ST'];	
				$count++;
				log("count= ".$count);
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			@unlink("/tmp/cacheT224-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT224-resposta-{$g['usuarioAtual']}");
	        $retorno = file_put_contents("/tmp/cacheT224-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			$cache = unserialize(file_get_contents("/tmp/cacheT224-{$g['usuarioAtual']}"));
			$resposta = unserialize(file_get_contents("/tmp/cacheT224-resposta-{$g['usuarioAtual']}"));
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
	        $retorno = $EST016->atualizarAliquotasPedidos($T224_Id, $C004_Id, $r_usarTabelaFornecedor, $cache[$i], $opcoes);
           	if($retorno !== true){
           		log("entra aqui");
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
			@unlink("/tmp/cacheT224-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT224-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        @unlink("/tmp/cacheT224-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        file_put_contents("/tmp/cacheT224-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;
	
	case 'faturarPedido':
		
		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		
		
		
		
		$mensagem = '';
		$retorno = $EST016->faturarPedido($T224_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = $mensagem;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'carregaCFOP_Contatos':
            
        global $g;
        
        $T224_D024_Id = isset($r_T224_D024_Id) ? $r_T224_D024_Id : false;
		if (empty($T224_D024_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum cliente selecionado";
		} else {
			$sql = "SELECT D024_D006_Id, 
			               D024_D018_Id,
                           D024_D021_Id,
			               D024_Flag_ReVenda_Consumidor,
			               D024_Flag_Contribuinte
			          FROM D024 WHERE D024_Id=" . $T224_D024_Id;
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);
			
			echo mysql_error();

			
			require_once("bibliotecas/classes/EST015.php");
			$EST015 = new EST015();
			$D006_Id=$EST015->retornaCFOP($T224_D024_Id, false, false);

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
				$resposta['data']['T224_D006_Id'] = gProcessaSelect($arrayDentroEstado);
			}
			// se nenhuma regra tiver sido satisfeita (critério novo e antigo)
			else if ($D006_Id===false)
			{
				$resposta['data']['T224_D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true));
			}
			// se mais de uma regra tiver sido satisfeita
			else
			{
				$resposta['code'] = false;
				$resposta['data']['erro'] = $D006_Id;	
			}
			//$resposta['data']['T224_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato',"where D013_D024_Id='{$T224_D024_Id}'",true));
			$resposta['data']['T224_Flag_ReVenda_Consumidor'] = "$aD024[D024_Flag_ReVenda_Consumidor]";
			$resposta['data']['T224_C007_Id_Vendedor_Interno'] = "{$g['usuarioAtual']}";
			
			// carrega os contatos do cliente selecionado
			if($g['C031']['orcamentoCarregarPrimeiroContato'] == 'N'){
				//select para carregar os contatos e deixar "..." selecionado na combo
				$resposta['data']['T224_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T224_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true));		
				$resposta['data']['T224_D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T224_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true)));
			}else{
				//select para carregar os contatos e deixar o primeiro contato selecionado na combo
				$resposta['data']['T224_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T224_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Id',false));		
				$resposta['data']['T224_D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T224_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Id',false)));
			}
			
			//carrega os veículos do cliente selecionado
            $resposta['data']['T224_D116_Id'] = gProcessaSelect(gGeraSelect('D116','D116_Id','D116_Placa,D133_Modelo,D134_Cor', 'LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id='.$T224_D024_Id,true));
            $resposta['data']['T224_D116_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D116','D116_Id','D116_Placa,D133_Modelo,D134_Cor',' JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id='.$T224_D024_Id,true)));
            
            //carrega o representante
            $resposta['data']['T224_D021_Id'] = gProcessaSelect(gGeraSelect('D021','D021_Id','D021_Nome_Empresa', '',true,$aD024['D024_D021_Id']));
			
		}
        
    break;

    case 'carregaListaPreco':
    	global $g;
    	$D024_Id = isset($r_T224_D024_Id) ? $r_T224_D024_Id : false;

		$result = mysql_query("SELECT D024_Flag_Lista_Preco from D024 WHERE D024_Id='{$D024_Id}'");
		$D024 = mysql_fetch_assoc($result);
		
		switch($D024['D024_Flag_Lista_Preco']){
			case '1':
				$resposta['data']['T224_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1', 'selected' => true),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
						));
				$resposta['code'] = true;
				break;
			case '2':
				$resposta['data']['T224_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2', 'selected' => true),   
							array('title' => 'Preço 3', 'value' => '3'),   
						));
				$resposta['code'] = true;
				break;
			case '3':
				$resposta['data']['T224_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3', 'selected' => true),   
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
        $resposta['data']['T224_D036_Id'] = $aD022['D022_D036_Id'];
        
        
    break;
		
	case 'inserirParcela':
		
		
		$form=$r_est016FormPagamento;		
				
		
		$retorno = $EST016->inserirParcela($form);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;	

	case 'calcularParcelaSimulacao':
		
		
		$form=$r_est016FormPagamento;		
				
		
		$retorno = $EST016->calcularParcelaSimulacao($form);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha no calculo da parcela...';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'salvarObservacao':
		
		
		$form=$r_est016FormPagamento;		
		$T224_Id = (isset($r_T224_Id)) ? $r_T224_Id : false; 
				
		
		$retorno = $EST016->gravarObservacao($form, $T224_Id);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da Observação. Por favor, tente novamente';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'gravarDescontoPedido':
		
		
		$T224_Id=(isset($r_T224_Id)) ? $r_T224_Id : false;
		$T224_Valor_Desconto = (isset($r_T224_Valor_Desconto)) ? $r_T224_Valor_Desconto : false;
		$valor = (isset($r_Valor)) ? $r_Valor : false;
				
		
		$retorno = $EST016->gravarDescontoPedido($T224_Id, $T224_Valor_Desconto, $valor);
		
		if ($retorno !== true) {			
			$resposta['code'] = false;
			$resposta['data'] = $retorno;			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'enviarParaAnalise':
		
		
		
		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
		$coleta = (isset($r_coleta) && $r_coleta == 'S') ? 'S' : false;
		
		$retorno = $EST016->enviarParaAnalise($T224_Id,$alcadaUpdate,$coleta);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Ordem de compra enviada para análise";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'est016aplicarDesconto':
		
		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$Desconto = isset($r_Desconto) ? $r_Desconto : false;
		$CampoAplicarDesconto = isset($r_CampoAplicarDesconto) ? $r_CampoAplicarDesconto : false;
		
		$Desconto = str_replace('.', '', $r_Desconto);
		$Desconto = str_replace(',', '.', $Desconto);
		
		
		
		
		$retorno = $EST016->est016aplicarDesconto($T224_Id, $Desconto, $CampoAplicarDesconto);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est016aplicarDescontoReais':
		
		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$DescontoReais = isset($r_DescontoReais) ? $r_DescontoReais : false;
		
		
		
		
		$retorno = $EST016->est016aplicarDescontoReais($T224_Id, $DescontoReais);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est016aplicarDescontoTotal':
		
		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$DescontoTotal = isset($r_DescontoValorTotal) ? $r_DescontoValorTotal : false;
		
		
		
		
		$retorno = $EST016->est016aplicarDescontoTotal($T224_Id, $DescontoTotal);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est016DeletarPDFOrdemCompra':
		global $g;
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;

		$C004 = mysql_query("SELECT C004_Nome_Empresa FROM C004 WHERE C004_Id = {$g['empresaAtual']}");
		if($erroSql = mysql_error()){log($erroSql);}
		$mC004 = mysql_fetch_assoc($C004);

		$strEmpresaAtual = explode(' ',$mC004['C004_Nome_Empresa'])[0];

		$T224 = mysql_query("SELECT D024_Nome_Empresa FROM T224 LEFT JOIN D024 ON D024_Id=T224_D024_Id WHERE T224_Id = {$T224_Id}");
		if($erroSql = mysql_error()){log($erroSql);}
		$mT224 = mysql_fetch_assoc($T224);

		$strFornecedorOC = explode(' ', $mT224['D024_Nome_Empresa'])[0];

		$dir = $g['pathDados'] . 'tmp/';
		$filename = "ordem-compra-{$T224_Id}-{$strEmpresaAtual}-{$strFornecedorOC}.pdf";
		$fileFull             = $g['pathRaiz'] . '/' . $dir . $filename;
		if(file_exists($fileFull)){
			unlink($fileFull);
		}
	break;
		
	case 'est016ImprimirPedido':
		global $g;
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;

		$C004 = mysql_query("SELECT C004_Nome_Empresa FROM C004 WHERE C004_Id = {$g['empresaAtual']}");
		if($erroSql = mysql_error()){log($erroSql);}
		$mC004 = mysql_fetch_assoc($C004);

		$strEmpresaAtual = explode(' ',$mC004['C004_Nome_Empresa'])[0];

		$T224 = mysql_query("SELECT D024_Nome_Empresa FROM T224 LEFT JOIN D024 ON D024_Id=T224_D024_Id WHERE T224_Id = {$T224_Id}");
		if($erroSql = mysql_error()){log($erroSql);}
		$mT224 = mysql_fetch_assoc($T224);

		$strFornecedorOC = explode(' ', $mT224['D024_Nome_Empresa'])[0];

		// Arquivo
		$dir = $g['pathDados'] . 'tmp/';
		is_dir($dir) or mkdir($dir, 0777, true);
		$filename = "ordem-compra-{$T224_Id}-{$strEmpresaAtual}-{$strFornecedorOC}.pdf";
		$file = $dir . $filename;
		$fileFull = $g['pathRaiz'] . '/' . $file;
		$retorno['Anexos'] = array($file);
		$retorno['AnexosWeb'] = array('/hardness3/' . $file);

		if (isset($r_gerarHtml)) {
			echo $EST016->montarEmailPdf($T224_Id);
			die();
		} else {
			$r_link = '/est/est016/form_func-ajax/est016ImprimirPedido/?ajax=true&gerarHtml=1&T224_Id='.$T224_Id.'&alcadaUpdate='.$alcadaUpdate;
			$r_link = str_replace(' ', '+', $r_link);
			//echo "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}\" \"{$fileFull}\"";
			//die();
			shell_exec("xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
			
			$T226 = mysql_query("SELECT SUM(T226_Valor_Parcela) as TotalParcelas, T224_Valor_Total FROM T226 LEFT JOIN T224 ON T224_Id = T226_T224_Id WHERE T226_T224_Id='{$T224_Id}'");
			$resT226 = mysql_fetch_array($T226);

			$sql="SELECT D006_Flag_Gera_Contas FROM T224 LEFT JOIN D006 on D006_Id=T224_D006_Id WHERE T224_Id='{$T224_Id}'";
			$resultado=mysql_query($sql);
			$row=mysql_fetch_array($resultado);

			if($row['D006_Flag_Gera_Contas'] == "R"){
				if($resT226['TotalParcelas'] == 0){
					$erro = "É necessário preencher a forma de pagamento";
				} else {
					if($resT226['T224_Valor_Total'] != $resT226['TotalParcelas']){
						$erro = "Valor das parcelas não confere com o total do pedido.";
					}
				}
			}

			if(isset($erro)){
				$resposta['code'] = false;
				$resposta['data'] = $erro;
			} else {
				$resposta['code'] = true;
				$resposta['data'] = isset($retorno['AnexosWeb'][0]) ? $retorno['AnexosWeb'][0] : '';
			}
		}
	break;
	
	case 'verificarParcelas':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		
		$T226 = mysql_query("SELECT SUM(T226_Valor_Parcela) as TotalParcelas, T224_Valor_Total FROM T226 LEFT JOIN T224 ON T224_Id = T226_T224_Id WHERE T226_T224_Id='{$T224_Id}'");
		$resT226 = mysql_fetch_array($T226);
        
        $sql="SELECT D006_Flag_Gera_Contas FROM T224 LEFT JOIN D006 on D006_Id=T224_D006_Id WHERE T224_Id='{$T224_Id}'";
        $resultado=mysql_query($sql);
        $row=mysql_fetch_array($resultado);
        
        if($row['D006_Flag_Gera_Contas'] == "R"){
		    if($resT226['TotalParcelas'] == 0){
			    $erro = "É necessário preencher a forma de pagamento";
		    } else {
			    if($resT226['T224_Valor_Total'] != $resT226['TotalParcelas']){
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
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		
		$T224 = mysql_query("UPDATE T224 SET T224_Flag_Status = '', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id) WHERE T224_Id = '{$T224_Id}'");
		
		$T225 = mysql_query("SELECT * FROM T225 WHERE T225_T224_Id = '{$T224_Id}'");
		
		if(mysql_num_rows($T225) > 0){
			while($row = mysql_fetch_array($T225)){
				$D009 = mysql_query("SELECT * FROM D009 WHERE D009_Id = '{$row['T225_D009_Id']}' AND D009_C004_Id = '{$g['empresaAtual']}'");
				$resD009 = mysql_fetch_array($D009);
				
				$D049 = mysql_query("SELECT * FROM D049 WHERE D049_Id = '{$resD009['D009_D049_Id']}'");
				$resD049 = mysql_fetch_array($D049);
				
				$CAD002->D001_reprocessa_historico($resD049['D049_D001_Id'], $resD009['D009_Id']);
				
			}
		}
		mysql_query("insert into T228(T228_T224_Id,T228_C007_Id,T228_Data,T228_Descricao,T228_Observacao) values ('{$T224_Id}','{$g['usuarioAtual']}',CURRENT_TIMESTAMP(),'Reativado','')");
		$resposta['code'] = true;
	break;

	case 'alterarDataEntregaCabecalho':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$data = isset($r_data) ? $r_data : false;
		mysql_query("UPDATE T224 SET T224_Data_Entrega = '{$data}' WHERE T224_Id = '{$T224_Id}'");
		echo mysql_error();
		$resposta['code'] = true;
	break;

	case 'est016atualizarCamposProdutosEditar':

		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$T224_Id = isset($_REQUEST['T224_Id']) ? $_REQUEST['T224_Id'] : false;

		$Aliquotas = $EST015->retornaAliquotas($D009_Id, false, $T224_Id);

		$T224=mysql_query("SELECT * FROM T224 
		                  LEFT JOIN D024 on D024_Id=T224_D024_Id
		                  WHERE T224_Id='{$T224_Id}'");
		$mT224=mysql_fetch_assoc($T224);


	    $D001=mysql_query("SELECT * FROM D001 
	                      LEFT JOIN D049 ON D001_Id=D049_D001_Id 
	                      LEFT JOIN D009 ON D049_Id=D009_D049_Id
	                      LEFT JOIN D082 ON D082_Id=D049_D082_Id
	                      left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$mT224['T224_D024_Id']}'
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

		$sql =	"SELECT T224_D024_Id, T224_Flag_Lista_Preco, T224_Flag_Acp from T224 WHERE T224_Id = '{$T224_Id}'";
		$resultado = mysql_query($sql);
		$mT224 = mysql_fetch_assoc($resultado);

		if(!empty($mT224['T224_Flag_Lista_Preco'])){
			$row['Preco_Produto'] = $CAD002->cad002RetornaPrecoCompra($D009_Id, $mT224['T224_Flag_Lista_Preco'], $mT224['T224_D024_Id'], $mT224['T224_Flag_Acp']);
		}else{
			$row['Preco_Produto'] = $CAD002->cad002RetornaPrecoCompra($D009_Id, null, $mT224['T224_D024_Id'], $mT224['T224_Flag_Acp']);
		}

		// Método que irá retonar o último preço que o produto foi estdido para o cliente
		$row['ultimoPrecoVenda'] = $EST015->retornaltimoPrecoVenda($mT224['T224_D024_Id'], $D009_Id);
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

	case 'verificaAlcada':
		global $g;
		$msg="";
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		
		//Verifica os dados dos itens do pedido
		
		
		
		$retornoRestricao = $EST016->verificaDadosOrdemCompra($T224_Id);
        if($retornoRestricao !== true){
            $resposta['code'] = 'erro';
			$resposta['data'] = $retornoRestricao;
			break;
        }
	
		$retorno = $EST016->verificaAlcada($T224_Id);

		$T224=mysql_query("SELECT T224_Flag_Status_Liberacao,T224_Prazos_Pagamentos,T224_Prazos(T224_Id) as T224_Prazo,T224_Flag_Frete,T224_D022_Id,T224_Flag_Status,T224_Valor_Frete,T224_Flag_Transporte_Coleta,T224_Flag_Alcada FROM T224 WHERE T224_Id='{$T224_Id}'");
		$mT224=mysql_fetch_array($T224);

		$T225=mysql_query("SELECT SUM(T225_Valor_Frete) as ValorFrete FROM T225 WHERE T225_T224_Id = '{$T224_Id}'");
		$mT225=mysql_fetch_array($T225);

		if ($mT224['T224_Flag_Status'] != '3'){
			if (empty($mT224['T224_Prazos_Pagamentos']) and empty($mT224['T224_Prazo'])){
				$resposta['code'] = 'erro';
				$resposta['data'] = 'Informe a forma de pagamento';
				break;
			}
			if ($mT224['T224_Flag_Frete']==''){
				$resposta['code'] = 'erro';
				$resposta['data'] = 'Informe o tipo do frete';
				break;
			}
			if (empty($mT224['T224_D022_Id']) && $mT224['T224_Flag_Frete'] != '9' && $g['C031']['obrigatorioOCTransportadoraPreenchido'] == 'S'){
				$resposta['code'] = 'erro';
				$resposta['data'] = 'Informe a transportadora';
				break;
			}
			if ($mT224['T224_Flag_Frete'] != '9' && $mT224['T224_Valor_Frete'] <= 0 && $mT225['ValorFrete'] <= 0 && $g['C031']['obrigatorioOCValorFretePreenchido'] == 'S'){
				$resposta['code'] = 'erro';
				$resposta['data'] = 'De acordo com o tipo do frete escolhido, você deve informar o valor do frete';
				break;
			}
			if ($mT224['T224_Flag_Frete']=='9' and $mT224['T224_Valor_Frete'] > 0 || $mT225['ValorFrete'] > 0){
				$resposta['code'] = 'erro';
				$resposta['data'] = 'De acordo com o tipo do frete escolhido, você NÃO DEVE informar o valor do frete';
				break;
			}
			if (empty($mT224['T224_D022_Id']) && ($mT224['T224_Valor_Frete'] > 0 || $mT225['ValorFrete'] > 0)){
				$resposta['code'] = 'erro';
				$resposta['data'] = 'O valor do frete está preenchido, informe a transportadora';
				break;
			}
		}

		if($mT224['T224_Flag_Status_Liberacao'] == 4 && $g['C031']['controleAlcadasOrdemCompra'] == 'S'){
			if($g['C007']['C007_Flag_Vendedor'] == $mT224['T224_Flag_Alcada'] || $g['C007']['C007_Flag_Vendedor'] == 'D' || ($g['C007']['C007_Flag_Vendedor'] == 'S' && $mT224['T224_Flag_Alcada'] == 'C')){
				$resposta['code'] = 'analise';
				break;
			} else {
				$msg = "A liberação desta Ordem de compra exige maior alçada, pedir liberação?";
			}
		}
		
		if ($mT224['T224_Flag_Status_Liberacao'] != 4 || $g['C031']['controleAlcadasOrdemCompra'] == 'N') {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $msg;
		}

		if(isset($r_coleta) && $r_coleta == 'S' && ($mT224['T224_Flag_Transporte_Coleta'] <= 0 || $mT224['T224_Flag_Transporte_Coleta'] == '')){
			$resposta['code'] = 'erro';
			$resposta['data'] = "Para solicitar coleta, o campo tipo coleta deve estar preenchido no cabeçalho";	
		}
	break;

	case 'excluirItensMarcados':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$sqlT225 = mysql_query("SELECT * FROM T225 WHERE T225_T224_Id = '{$T224_Id}' and T225_Flag_Excluir = 1");
		while($T225 = mysql_fetch_array($sqlT225)){
			$retorno = $EST016->excluirProduto($T225['T225_Id'], $T224_Id);
		}
		if(mysql_num_rows($sqlT225) > 0 && $retorno === true){
			$resposta['code'] = true;
			$resposta['data'] = "Itens excluidos com sucesso";
		} else {
			$resposta['code'] = false;
			if(mysql_num_rows($sqlT225) > 0){
				$resposta['data'] = $retorno;
			} else {
				$resposta['data'] = "Nenhum item selecionado!";
			}
		}
	break;

	case 'atualizarCFOP':
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;

		
		
		$opcoes = json_decode($r_opcoes, true);
		$retorno = $EST016->est016atualizarCFOP($T224_Id, $opcoes);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['T005_Id'] = $T005_Id;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizarCFOPManual':
		
		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$D006_Id = isset($r_D006_Id) ? $r_D006_Id : false;
		$resposta['code'] = true;
		$opcoes = json_decode($r_opcoes, true);
		if (is_array($opcoes) && isset($opcoes['cfop']) && $opcoes['cfop'] === false) {
			break;
		}

		mysql_query("UPDATE T224 SET T224_D006_Id = '{$D006_Id}' WHERE T224_Id = '{$T224_Id}'");
        $erroSQLT224    = mysql_error();
        if (!empty($erroSQLT224)) {
        	$resposta['code'] = false;
            $resposta['data'] = $erroSQLT224;
        }
        mysql_query("UPDATE T225 SET T225_D006_Id = '{$D006_Id}' WHERE T225_T224_Id = '{$T224_Id}'");
        $erroSQLT225    = mysql_error();
        if (!empty($erroSQLT225)) {
        	$resposta['code'] = false;
            $resposta['data'] = $erroSQLT225;
        }
	break;


	case 'gerarPedidoTransferencia':
		
		$T224_Id  = isset($r_T224_Id) ? $r_T224_Id : false;
		$T005_Id  = false;
		
		$retorno1 = $EST016->gerarOTPedido($T224_Id, true);
		$retorno2 = $EST016->gerarOTPedido($T224_Id, false);
				
		if (is_array($retorno1) || is_array($retorno2)) {
			$resT005 = mysql_query("SELECT T005_Id, T005_Status_Pedido(T005_Flag_Status,1) as status, C004_Nome_Abreviado FROM T005 LEFT JOIN C004 ON T005_C004_Id=C004_Id WHERE T005_T224_Id = '{$T224_Id}'");
			
			$resposta['code'] = true;
			$resposta['data'] = '';
			$resposta['T005'] = array();
			while ($rowT005 = mysql_fetch_assoc($resT005)) {
				$resposta['T005_Id'] = $rowT005['T005_Id'];
				$resposta['data']   .= 'Pedido <b>' .  $rowT005['T005_Id'] . '</b> gerado em <b>' . $rowT005['C004_Nome_Abreviado'] . '</b> status <b>' . $rowT005['status'] . '</b>.<br>';
				$resposta['empresa'] = $rowT005['C004_Nome_Abreviado'];
				$resposta['T005'][]  = $rowT005['T005_Id'];
			}
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno1 . '<br>' . $retorno2;
		}	
	break;

	case 'solicitarColeta':
		
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;

		
		
		$T005_Id = false;
		$retorno = $EST016->solicitarColeta($T224_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = 'Coleta solicitada';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'finalizarOCManual':
		require_once("bibliotecas/classes/EST004.php");
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;

		$EST004 = new EST004();
		$retorno = $EST004->alterarStatusOCVinculada(0,$T224_Id);
		
		$resposta['code'] = true;
		$resposta['data'] = $retorno;
		
	break;

	case 'finalizarOCManualSemNota':

		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;

		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();

		
		mysql_query("UPDATE T224 SET T224_Flag_Status = '13', 
									 T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id)
						       WHERE T224_Id = '{$T224_Id}'");

		$T225 = mysql_query("SELECT T225_D009_Id 
							   FROM T225
							  WHERE T225_T224_Id = '{$T224_Id}'
						   GROUP BY T225_D009_Id");
		
		while($mT225 = mysql_fetch_array($T225)){
			$CAD002->reprocessaOCPedidos($mT225['T225_D009_Id']);
		}
		
		$resposta['code'] = true;
		$resposta['data'] = 'OC finalizada manual';
		
	break;

	case 'retornarOCPendente':
		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;

		mysql_query("UPDATE T224 SET T224_Flag_Status = '0', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id), T224_Liberou_Alcada_Data = '0000-00-00 00:00:00', T224_Flag_Status_Liberacao = '0', T224_Liberou_Alcada_C007_Id = '0' WHERE T224_Id = '{$T224_Id}'");
		$error = mysql_error();

		if (empty($error)) {
			$resposta['code'] = true;
			$resposta['data'] = 'Ordem de Compra retornada!';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'atualizarCustoVenda':
		require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		require_once('bibliotecas/classes/VEN002.php');
		$VEN002 = new VEN002();

		global $g;

		$T224_Id = $r_T224_Id;
		mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

		//Atualizando Custo do produto no Pedido
		/*
		$T005 = mysql_query("SELECT T005_Id
							FROM T006
							LEFT JOIN D009 ON D009_Id=T006_D009_Id 
							LEFT JOIN T005 ON T005_Id=T006_T005_Id
							LEFT JOIN T001 ON T001_Id=D009_T001_Id
							LEFT JOIN T219 ON T219_Id=T006_T219_Id
							LEFT JOIN T218 ON T218_Id=T219_T218_Id
							LEFT JOIN T224 ON T218_Id=T224_T218_Id
							WHERE T224_Id = '{$T224_Id}'");

		while($mT005 = mysql_fetch_array($T005)){
			$VEN002->ven002atualizarCFOP($mT005['T005_Id']);
		}
		*/

		$T006 = mysql_query("SELECT D009_Valor_Custo_Unitario,
									D009_Valor_Custo_Compra,
									D009_T001_Id,
									D009_Id,
									D009_ICF_1,
									T005_Flag_Lista_Preco,
									T005_D024_Id,
									T005_Flag_ACP,
									T006_D006_Id,
									T006_D005_Id,
									T006_Aliquota_ICMS,
									T006_Codigo_Produto,
									T006_Id,
									D009_Id,
									T219_Id,
									T005_Id,
									T006_Aliquota_ICMS,
									T006_Aliquota_PIS,
									T006_Aliquota_COFINS,
									T006_Aliquota_IPI,
									T219_Valor_Custo_Unitario,
									T225_Valor_Custo_Unitario,
									T225_D006_Id,
									T225_D005_Id,
									T225_Flag_Origem_Produto,
									T006_Flag_Origem_Produto,
									T225_Id,
									T225_Item,
									T006_Item,
									T006_ST_VA,
                                    T006_Percentual_Reducao_ICMS_ST,
                                    T006_ST_ICMS_Interno,
                                    T006_Percentual_Reducao_ICMS,
                                    T006_ICMS_Inter_Estadual,
                                    T006_Aliquota_IRPJ,
                                    T006_Aliquota_CSLL
									FROM T225
									LEFT JOIN T219 ON T219_Id=T225_T219_Id
									LEFT JOIN T218 ON T218_Id=T219_T218_Id
									LEFT JOIN T006 ON T006_Id=T219_T006_Id
									LEFT JOIN T224 ON T224_Id=T225_T224_Id
									LEFT JOIN T008 ON T006_Id=T008_T006_Id
									LEFT JOIN D009 ON D009_Id=T006_D009_Id
									LEFT JOIN T005 ON T005_Id=T006_T005_Id
									LEFT JOIN D024 ON D024_Id=T005_D024_Id
									LEFT JOIN T001 ON T001_Id=D009_T001_Id
									WHERE T224_Id = '{$T224_Id}'
									AND T006_Id IS NOT NULL
									AND T008_Id IS NULL;");
		$erroPedido='';

		while($mT006 = mysql_fetch_array($T006)){					 
			$count++;

			$precoCotacao      	  = $CAD002->cad002RetornaPrecoCotacao($mT006['D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], true, $mT006['T006_D006_Id'], $mT006['T006_Aliquota_ICMS'], 0, $mT006['T225_Id'], 4, $mT006['T006_Aliquota_PIS'], $mT006['T006_Aliquota_COFINS'], $mT006['T006_Aliquota_IPI']);
			$demonstrativoCotacao = $precoCotacao[1];
			$custoMedio 	   	  = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[2] : 0;
			$quantidadeEstoque 	  = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[3] : 0;
			$precoCotacao 	   	  = gCorrigeNumeroInverte($precoCotacao[0]);
			$demonstrativoCotacao = mysql_real_escape_string(serialize($demonstrativoCotacao));
			
			$dadosPrecoVenda 	  	 = $CAD002->retornaPrecoVenda($mT006['D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], true, $mT006['T006_D006_Id'], $mT006['T006_Aliquota_ICMS'], 0, $mT006['T225_Id'], 4, $mT006['T006_Aliquota_PIS'], $mT006['T006_Aliquota_COFINS'], $mT006['T006_Aliquota_IPI'], $mT006['T006_ST_VA'], $mT006['T006_Percentual_Reducao_ICMS_ST'], $mT006['T006_ST_ICMS_Interno'], $mT006['T006_Percentual_Reducao_ICMS'], $mT006['T006_ICMS_Inter_Estadual'], $mT006['T006_Aliquota_IRPJ'], $mT006['T006_Aliquota_CSLL']);
			$precoVendaProduto    	 = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
			$demonstrativoPrecoVenda = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
			$custoMedio 	   	     = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $dadosPrecoVenda[2] : 0;
			$quantidadeEstoque 	     = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $dadosPrecoVenda[3] : 0;
			$custoProduto            = $dadosPrecoVenda[4];
			
			if ($mT006['T006_D006_Id']<=0 || $mT006['T006_D005_Id']<=0 || !isset($mT006['T006_Flag_Origem_Produto'])) {
				$erroPedido.="O item {$mT006['T006_Item']} (Código {$mT006['T006_Codigo_Produto']}) no pedido de venda {$mT006['T005_Id']}, está sem CFOP, NCM ou ORIGEM. Favor verificar!<br />";
			}
			if ($mT006['T225_D006_Id']<=0 || $mT006['T225_D005_Id']<=0 || !isset($mT006['T225_Flag_Origem_Produto'])) {
				$erroPedido.="O item {$mT006['T225_Item']} (Código {$mT006['T006_Codigo_Produto']}) na OC {$mT006['T225_Id']}, está sem CFOP, NCM ou ORIGEM. Favor verificar!<br />";
			}

			mysql_query("UPDATE T006 
						SET T006_Valor_Preco_Cotacao 	 = '{$precoCotacao}',
						T006_Valor_Preco_Original 		 = '{$precoVendaProduto}',
						T006_Valor_Preco_Original_Bruto	 = '{$precoVendaProduto}',
						T006_Valor_Custo_Unitario		 =  {$mT006['T225_Valor_Custo_Unitario']} * IF({$mT006['D009_ICF_1']}>0,{$mT006['D009_ICF_1']},1),
						T006_Percentual_Desconto 		 =  ((T006_Valor_Preco_Original-T006_Valor_Preco_Unitario)/T006_Valor_Preco_Original)*100,
						T006_Demonstrativo_Preco_Cotacao = '{$demonstrativoCotacao}',
						T006_T001_Id              		 = '{$mT006['D009_T001_Id']}',
						T006_Flag_Origem_Produto  		 = '{$mT006['T225_Flag_Origem_Produto']}',
						T006_T225_Id 					 = '{$mT006['T225_Id']}',
						T006_Demonstrativo_Preco_Venda 	 = '{$demonstrativoPrecoVenda}'
						WHERE T006_Id = '{$mT006['T006_Id']}'");

			mysql_query("UPDATE T219
						LEFT JOIN T006 ON T006_T219_Id=T219_Id
						SET T219_Valor_Preco_Venda 		 = T006_Valor_Preco_Original, 
						T219_Valor_Preco_Venda_Negociado = T006_Valor_Preco_Unitario,
						T219_Valor_Custo_Medio_Unitario  = '{$custoMedio}',
						T219_Quantidade_Estoque 		 = '{$quantidadeEstoque}',
						T219_Valor_Custo_Momento_Compra  = '{$mT006['D009_Valor_Custo_Compra']}'
						WHERE T006_Id = '{$mT006['T006_Id']}'");

			mysql_query("UPDATE T225
						LEFT JOIN T219 ON T219_Id=T225_T219_Id
						LEFT JOIN T006 ON T006_T219_Id=T219_Id
						SET T225_Valor_Preco_Venda 		 = T006_Valor_Preco_Original, 
						T225_Valor_Preco_Venda_Negociado = T006_Valor_Preco_Unitario,
						T225_Valor_Custo_Medio_Unitario  = '{$custoMedio}',
						T225_Quantidade_Estoque 		 = '{$quantidadeEstoque}',
						T225_Valor_Custo_Momento_Compra  = '{$mT006['D009_Valor_Custo_Compra']}'
						WHERE T006_Id = '{$mT006['T006_Id']}'");
			
			$VEN002->atualizarAliquotasPedidos($mT006['T005_Id'], false , $mT006['T006_Id']);

		}


		if ($erroPedido != ''){
			$resposta['code'] = false;
			$resposta['data'] = "ATENÇÃO!!! <BR /><BR />";
			$resposta['data'] .= $erroPedido;
			mysql_query("ROLLBACK");
		} else {
			$erro = mysql_error();
			if(empty($erro) and $count>0){
				$resposta['code'] = true;
				$resposta['data'] = "Custo(s) atualizado com sucesso.";
				mysql_query("COMMIT");
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "ATENÇÃO!!! <BR /><BR />OS PRODUTOS NÃO ESTÃO RELACIONADOS A UMA VENDA, FAVOR VERIFICAR!!!";
				mysql_query("ROLLBACK");
			}
		}
		mysql_query("SET AUTOCOMMIT=1");
	break;

	case 'pedidoWhats':
		global $g;

		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$phone = $r_phone;

		$T225 = mysql_query("select T225_Codigo_Produto, 
									T225_Descricao_Produto, 
									T225_Quantidade, 
									T225_Valor_Preco_Sem_Desconto_Unitario,
									T225_Valor_Preco_Bruto
							   from T225
							  where T225_T224_Id='{$T224_Id}'");

		$retorno = "Ordem compra N. " . $T224_Id . chr(10).chr(13);
		$retorno .= "---------------------------------" . chr(10);
		while ($mT225 = mysql_fetch_array($T225)){
			   $preco = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? $mT225['T225_Valor_Preco_Bruto'] : $mT225['T225_Valor_Preco_Sem_Desconto_Unitario'];
			   $retorno .= 'Código: ' . $mT225['T225_Codigo_Produto'] . chr(10) .
						   'Descrição: ' . $mT225['T225_Descricao_Produto'] . chr(10) .
						   'Quantidade: ' . gCorrigeNumero($mT225['T225_Quantidade']) . chr(10) .
						   'Preço unitário: ' . gCorrigeNumero($preco) . chr(10);
			   $retorno .= "---------------------------------" . chr(10);
		}

		$T224 = mysql_query("select C007_Nome, T224_D013_Id, T224_D024_Id from T224 left join C007 on C007_Id=T224_C007_Id where T224_Id='$T224_Id'");
		$mT224 = mysql_fetch_array($T224);

		$assunto   = 'Ordem compra N. ' . $T224_Id;
		$de        = $mT224['C007_Nome'];
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
										T139_T224_Id, 
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
										'".mysql_real_escape_string($mT224['T224_D024_Id'])."', 
										'".mysql_real_escape_string($mT224['T224_D013_Id'])."', 
										'".mysql_real_escape_string($T224_Id)."', 
										'".mysql_real_escape_string($g['usuarioAtual'])."')");


		$resposta['code'] = true;
		$resposta['data'] = urlencode($retorno);


	break;

	case 'est016PhoneWhats':
		global $g;

		$T224=mysql_query("select T224_Id,T224_D013_Id, D024_DDD_Celular, D024_Celular, D013_DDD_Celular, D013_Celular from T224 left join D024 on D024_Id=T224_D024_Id left join D013 on D013_Id=T224_D013_Id where T224_Id='{$r_T224_Id}'");
		$mT224 = mysql_fetch_assoc($T224);

		$D013_Id=$mT224['T224_D013_Id'];

		$phoneWhatsApp = '';
		$notificaSemPhone = '';
		if (!empty($mT224['D013_Celular'])){
			$phoneWhatsApp = '+55'.trim($mT224['D013_DDD_Celular']).trim($mT224['D013_Celular']);		
		} else if (!empty($mT224['D024_Celular'])) {
			$phoneWhatsApp = '+55'.trim($mT224['D024_DDD_Celular']).trim($mT224['D024_Celular']);		
		} else {
			$notificaSemPhone = " ** Contato não possui celular cadastrado, atualize agora! ** ";
		}
		$phoneWhatsApp = str_replace('-','',$phoneWhatsApp);

		$resposta['phoneWhatsApp'] = urlencode($phoneWhatsApp);
		$resposta['notificaSemPhone'] = htmlspecialchars($notificaSemPhone);

	break;

	case 'pedidoWhatsPDF':
		global $g;

		$T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
		$moeda = isset($r_moeda) ? $r_moeda : 1;
		$phone = $r_phone;

        $T226 = mysql_query("SELECT SUM(T226_Valor_Parcela) as TotalParcelas, T224_Valor_Total FROM T226 LEFT JOIN T224 ON T224_Id = T226_T224_Id WHERE T226_T224_Id='{$T224_Id}'");
        $resT226 = mysql_fetch_array($T226);

        $sql="SELECT D006_Flag_Gera_Contas FROM T224 LEFT JOIN D006 on D006_Id=T224_D006_Id WHERE T224_Id='{$T224_Id}'";
        $resultado=mysql_query($sql);
        $row=mysql_fetch_array($resultado);

        if($row['D006_Flag_Gera_Contas'] == "R"){
            if($resT226['TotalParcelas'] == 0){
                $erro = "É necessário preencher a forma de pagamento";
            } else {
                if($resT226['T224_Valor_Total'] != $resT226['TotalParcelas']){
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
			
			$filename             = md5('T224'.$T224_Id.uniqid('',true)).".pdf";
			$file                 = $dir . $filename;
			$fileFull             = $g['pathRaiz'] . '/' . $file;
			$urlFile              = $g['urlRaiz'] . 'hardness3/' . $file;
			
            $r_link = '/est/est016/form_func-ajax/est016ImprimirPedido/?ajax=true&gerarHtml=1&T224_Id='.$T224_Id.'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
		
			$texto  = "Olá, segue link da nossa ordem compra pedido N. " . $T224_Id . chr(10);
			$texto .= $urlFile;

			$T224 = mysql_query("select C007_Nome, T224_D013_Id, T224_D024_Id from T224 left join C007 on C007_Id=T224_C007_Id where T224_Id='$T224_Id'");
			$mT224 = mysql_fetch_array($T224);

			$assunto   = 'Ordem compra N. ' . $T224_Id;
			$de        = $mT224['C007_Nome'];
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
											T139_T224_Id, 
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
											'".mysql_real_escape_string($mT224['T224_D024_Id'])."', 
											'".mysql_real_escape_string($mT224['T224_D013_Id'])."', 
											'".mysql_real_escape_string($T224_Id)."', 
											'".mysql_real_escape_string($g['usuarioAtual'])."')");

			$resposta['code'] = true;
			$resposta['data'] = urlencode($texto);
		}
	break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
