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
require_once('bibliotecas/classes/CAD061.php');
$CAD061 = new CAD061();
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'atualizarCamposProdutos':
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$D157_Id = isset($D157_Id) ? $D157_Id : false;
		/*
		D158_Ultimo_Preco(D001_Id,'$D157[T003_D024_Id]','$T003_Id',1) as Preco_Consultado,
		D158_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',2) as Preco_Confirmado
		*/
			
		$sql =	"SELECT * from D157 WHERE D157_Id = '{$D157_Id}'";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		$D157_D006_Id = $row['D157_D006_Id'];
		$D157_Flag_Destino_Produto = $row['D157_Flag_Destino_Produto'];
		
		$sql = <<<EOT
			SELECT *
	   				from D001
	   			left join D049 on D049_D001_Id=D001_Id
	   			left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['D157_D024_Id']}'
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   			left join D069 on D069_Id=D009_D069_Id
			    left join D070 on D070_Id=D069_D070_Id
	   			WHERE D009_Id = '{$D009_Id}'
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

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
		
		//$row['D158_Valor_Preco_Sem_Desconto_Unitario'] = '';
		
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
					  D157_Ordem_Compra";
		$cClausula.=" from D157 ";
		$cClausula.=" left join D024 on D024_Id=D157_D024_Id ";
		$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
		$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=D157_C004_Id ";
		$cClausula.=" left join C004 on C004_Id=D157_C004_Id ";
		$cClausula.=" where D157_Id='{$D157_Id}'";
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
				D009_IPI('{$mT003['D157_C004_Id']}',D009_Id,'{$mT003['D157_D024_Id']}','{$mT003['D157_D006_Id']}','1','{$mT003['D157_Flag_ACP']}') as Aliquota_IPI 
			FROM 
				D009 
			where D009_Id='{$D009_Id}'
EOT;
		$result = mysql_query($sql);
		$row2 = mysql_fetch_assoc($result);
		$row['Aliquota_IPI'] = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'],2,',','.');
		

		$row['D157_Flag_Destino_Produto'] = $D157_Flag_Destino_Produto;
		/**
		 * DEFININDO QUAL O PREÇO QUE SERÁ RETORNADO
		 * 
		 * $row['D041_Valor_Base'] = number_format($row['D041_Valor_Base'],2,',','.');
		 */
		$cfop      = $VEN001->retornaCFOP($mT003['D157_D024_Id'], $row['D009_Id'], $mT003['D157_C004_Id'], $D157_Flag_Destino_Produto, false, false, false, $mT003['D157_C004_Id_Estoque']);
		$aliquotas = $VEN001->retornaAliquotas($D009_Id, false, $D157_Id, false, false, false, false, $cfop);
		
		$preco 		 = $CAD002->retornaPrecoVenda($row['D009_Id'], $mT003['D157_Flag_Lista_Preco'], $mT003['D157_D024_Id'], false, $cfop, $aliquotas['Aliquota_ICMS'], 0, 0, $mT003['D157_Casas_Decimais_Preco'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
		$precoTabela = $CAD002->cad002RetornaPrecoTabela($row['D009_Id'], $mT003['D157_Flag_Lista_Preco'], $mT003['D157_D024_Id'], $mT003['D157_Flag_Acp'], $cfop, $aliquotas['Aliquota_ICMS'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI']);
		
		$row['Preco_Produto'] = $preco[0];
	  	$precoCalc = gCorrigeNumeroInverte($preco[0]);
	  	$percentualPrecoMinimo = $g['C031']['percentualPrecoMinimoVenda']/100;
	  	$row['PrecoMinimo'] = gCorrigeNumero($precoCalc - ($precoCalc*$percentualPrecoMinimo), 4);      	

      	$row['precoTabela'] = $precoTabela[0];
		$row['D158_Valor_Preco_Sem_Desconto_Unitario'] = $preco[0];

		$row['D158_Numero_Pedido_Compra'] = (isset($mT003['D157_Ordem_Compra']) && $mT003['D157_Ordem_Compra'] != '') ? $mT003['D157_Ordem_Compra'] : '';

		// Método que irá retonar o último preço que o produto foi vendido para o cliente
		$row['ultimoPrecoVenda'] = $VEN001->retornaltimoPrecoVenda($mT003['D157_D024_Id'], $row['D009_Id'], 0, $D157_Id);

		$row['D006_Id'] = $VEN001->retornaCFOP($mT003['D157_D024_Id'], $row['D009_Id']);
		if($row['D006_Id'] <= 0 || $row['D006_Id'] === false){
			$row['D006_Id'] = $D157_D006_Id;
		}

		if($g['C031']['utilizarDescProdutoUltimaVenda'] == 'S'){
			$sqlT003 = mysql_query("SELECT T004_Descricao_Produto FROM T004 LEFT JOIN T003 ON T003_Id = T004_T003_Id WHERE T003_D024_Id ='{$mT003['D157_D024_Id']}' AND T004_D009_Id = '{$row['D009_Id']}' ORDER BY T004_Id DESC LIMIT 1");
			if(mysql_num_rows($sqlT003) > 0){
				$resT003 = mysql_fetch_array($sqlT003);
				
				if($row['D001_Descricao_Produto'] != $resT003['T004_Descricao_Produto']){
					$row['D001_Descricao_Produto'] = $resT003['T004_Descricao_Produto'];
				}
			}
		}
		$row['campoFocus'] = $g['C031']['editarDescricaoProdutoVenda']=='S' ? 'D158_Descricao_Produto' : 'D158_Quantidade';
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
	
	case 'atualizarAliquota':
		require_once("bibliotecas/classes/VEN002.php");
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		$VEN002 = new VEN002();
		
		$retorno = $VEN002->atualizarAliquotasPedidos($T005_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Atualizado!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
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
				$resposta['code'] = false;
				$resposta['data']['erro'] = $D006_Id;	
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
						));
				$resposta['code'] = true;
				break;
			case '2':
				$resposta['data']['T005_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2', 'selected' => true),   
							array('title' => 'Preço 3', 'value' => '3'),   
						));
				$resposta['code'] = true;
				break;
			case '3':
				$resposta['data']['T005_Flag_Lista_Preco'] = gProcessaSelect(
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
        $resposta['data']['T005_D036_Id'] = $aD022['D022_D036_Id'];
        
        
    break;
		
	case 'inserirParcela':
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
	
	case 'ven002aplicarDesconto':
		require_once("bibliotecas/classes/VEN002.php");
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$Desconto = isset($r_Desconto) ? $r_Desconto : false;
		$CampoAplicarDesconto = isset($r_CampoAplicarDesconto) ? $r_CampoAplicarDesconto : false;
		
		$Desconto = str_replace('.', '', $r_Desconto);
		$Desconto = str_replace(',', '.', $Desconto);
		
		$VEN002 = new VEN002();
		
		$retorno = $VEN002->VEN002aplicarDesconto($T005_Id, $Desconto, $CampoAplicarDesconto);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'ven002aplicarDescontoReais':
		require_once("bibliotecas/classes/VEN002.php");
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$DescontoReais = isset($r_DescontoReais) ? $r_DescontoReais : false;
		
		$VEN002 = new VEN002();
		
		$retorno = $VEN002->VEN002aplicarDescontoReais($T005_Id, $DescontoReais);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'ven002aplicarDescontoTotal':
		require_once("bibliotecas/classes/VEN002.php");
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$DescontoTotal = isset($r_DescontoValorTotal) ? $r_DescontoValorTotal : false;
		
		$VEN002 = new VEN002();
		
		$retorno = $VEN002->VEN002aplicarDescontoTotal($T005_Id, $DescontoTotal);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
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

	case 'CAD061atualizarCamposProdutosEditar':

		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$D157_Id = isset($_REQUEST['D157_Id']) ? $_REQUEST['D157_Id'] : false;

		$Aliquotas = $VEN001->retornaAliquotas($D009_Id, false, $D157_Id);

		$D157=mysql_query("SELECT * FROM D157 
		                  LEFT JOIN D024 on D024_Id=D157_D024_Id
		                  WHERE D157_Id='{$D157_Id}'");
		$mD157=mysql_fetch_assoc($D157);


	    $D001=mysql_query("SELECT * FROM D001 
	                      LEFT JOIN D049 ON D001_Id=D049_D001_Id 
	                      LEFT JOIN D009 ON D049_Id=D009_D049_Id
	                      LEFT JOIN D082 ON D082_Id=D049_D082_Id
	                      left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$mD157['D157_D024_Id']}'
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

		$sql =	"SELECT D157_D024_Id, D157_Flag_Lista_Preco, D157_Flag_Acp from D157 WHERE D157_Id = '{$D157_Id}'";
		$resultado = mysql_query($sql);
		$mD157 = mysql_fetch_assoc($resultado);

		$cfop = $VEN001->retornaCFOP($mD157['T003_D024_Id'], $D009_Id, $mD157['T003_C004_Id'], $mD157['T003_Flag_Destino_Produto'], false, false, false, $mD157['T003_C004_Id_Estoque']);
		$row['D006_Id'] = $cfop;

		$dadosPrecoVenda      = $CAD002->retornaPrecoVenda($D009_Id, $mD157['D157_Flag_Lista_Preco'], $mD157['D157_D024_Id'], false, $cfop, $Aliquotas['Aliquota_ICMS'], 0, 0, $mD157['D157_Casas_Decimais_Preco'], $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
		$row['Preco_Produto'] = $dadosPrecoVenda[0];

		// Método que irá retonar o último preço que o produto foi vendido para o cliente
		$row['ultimoPrecoVenda'] = $VEN001->retornaltimoPrecoVenda($mD157['D157_D024_Id'], $D009_Id);
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

		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();

		$retorno = $VEN001->verificaAlcadaNovo(false, $T005_Id,$acao);

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

	case 'atualizarPrecoTabela':
		$T005_Id = (!empty($r_T005_Id)) ? $r_T005_Id : false;

		$T006 = mysql_query("SELECT D009_Valor_Custo_Unitario,
                                    D009_ICF_1,
                                    D049_Origem_Mercadoria,
                                    D009_T001_Id,
                                    D009_Id,
                                    D049_Id,
                                    T005_Flag_Lista_Preco,
                                    T005_D024_Id,
                                    T005_Flag_ACP,
                                    T005_C004_Id,
                                    T006_D006_Id,
                                    T005_Aliquota_ICMS,
                                    T006_Flag_Destino_Produto,
                                    D009_T001_Id,
                                    T006_Id,
                                    T005_Id,
                                    T219_Flag_Origem_Produto,
                                    T006_D005_Id,
                                    T006_Flag_Origem_Produto,
                                    T219_Flag_Comprar,
                                    T219_Valor_Custo_Unitario,
                                    T006_T219_Id,
                                    T006_T225_Id,
                                    T006_Aliquota_ICMS,
                                    T006_Aliquota_PIS,
                                    T006_Aliquota_COFINS,
                                    T006_Aliquota_IPI,
                                    T006_D009_Id,
                                    T006_T005_Id,
                                    T006_Valor_Preco_Original,
                                    T006_Valor_Custo_Unitario,
                                    ifnull(T005_T224_Id,0) as T005_T224_Id,
                                    T006_Valor_Preco_Tabela,
                                    T006_ST_VA,
                                    T006_Percentual_Reducao_ICMS_ST,
                                    T006_ST_ICMS_Interno,
                                    T006_Percentual_Reducao_ICMS,
                                    T006_ICMS_Inter_Estadual,
                                    T006_Aliquota_IRPJ,
                                    T006_Aliquota_CSLL,
                                    T006_Flag_Transferencia,
                                    T006_Quantidade_Estoque,
                                    T006_Quantidade,
                                    T006_C004_Id_Transferencia,
                                    T006_Valor_Preco_Transferencia,
                                    T006_T001_Id
                               FROM T006
                          LEFT JOIN T008 ON T008_T006_Id=T006_Id
                          LEFT JOIN D009 ON D009_Id=T006_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id
                          LEFT JOIN T005 ON T005_Id=T006_T005_Id
                          LEFT JOIN T001 ON T001_Id=D009_T001_Id
                          LEFT JOIN T219 ON T219_Id=T006_T219_Id
                              WHERE T006_T005_Id='{$T005_Id}'
                                AND T008_Id IS NULL
                                AND T005_Flag_Status!='8'");
        if(mysql_num_rows($T006) > 0){
	        while ($mT006 = mysql_fetch_array($T006)) {
	            $CAD002->atualizaPrecoTabelaPedido($mT006);
	        }
	        $resposta['code'] = true;
	    } else {
	    	$resposta['data'] = "Não foi possível atualizar o preço tabela dos produtos, verifique se o pedido já foi faturado.";
			$resposta['code'] = false;
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
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
