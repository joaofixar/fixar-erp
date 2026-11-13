<?php
namespace hardness;

// //funções gerais
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();

//funções pertinentes a api de relatórios
require_once('bibliotecas/classes/API003.php');
$API003 = new API003();

require_once("hardness/class/stoneclient/StoneClient.php");		
require_once("hardness/class/stoneclient/Request/FecharPedido.php");

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'StonePayment':
		// Recebe e verifica a chave da API enviada pelo link da requisição
		$retorno = $API003->auth($_GET['API_AUTH']);
		/* Se o $retorno foi um array significa que 
		a chave está incorreta*/
		if(is_array($retorno)){
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		// Recebe o JSON enviado pelo POST
		$json = file_get_contents('php://input');
		//$stoneClient  = new StoneClient('sk_BGDd46d7ietoYoV3');	

		$resultado =  (array) json_decode($json,true);

		// Dados da order (pedido pagamento)
		$order 		  	 	= $resultado['data']['order'];
		$numeroPedido 		= $order['id'];

		// Número do ID do registro de retorno de pagamento
		$idRetornoPagamento = $resultado['data']['id'];

		// Verifica se é recebimento ou cancelamento: charge.paid (pgto recebido) charge.refunded (pgto cancelamento)
		$tipoOperacao = $resultado['type'];

		if($tipoOperacao == 'charge.paid'){
			$paid_amount            = ($resultado['data']['paid_amount']/100);
			//tag metadata (dados de autorização de pagamento)
			$metaData 			    = $resultado['data']['metadata'];
			$authorization_code 	= $metaData['authorization_code'];
			$scheme_name       	    = $metaData['scheme_name'];
			$account_funding_source = $metaData['account_funding_source'];
			$installmentQuantity    = ($account_funding_source == 'Credit' && (!empty($metaData['installment_quantity']) || $metaData['installment_quantity'] > 0)) ? $metaData['installment_quantity'] : 1;
			$transaction_timestamp  = subStr($metaData['transaction_timestamp'],0,19);
			$transaction_timestamp  = str_replace('T',' ',$transaction_timestamp);
			// fim metada

			$registroLog .= 'Operação de recebimento (charge.paid):<br><br>';
			$registroLog .= '- Pedido: <b>'.$numeroPedido.'</b><br>';
			$registroLog .= '- Valor recebido: <b>R$'.gCorrigeNumero($paid_amount).'</b><br>';
			$registroLog .= '- Qtd Parcela: <b>'.$installmentQuantity.'</b><br>';
			$registroLog .= '- Tipo pagamento: <b>'.$account_funding_source.'</b><br>';
			$registroLog .= '- Bandeira: <b>'.$scheme_name.'</b><br>';
			$registroLog .= '- Código autorização: <b>'.$authorization_code.'</b><br>';
			$registroLog .= '- Código pagamento: <b>'.$idRetornoPagamento.'</b><br>';

		} elseif($tipoOperacao == 'charge.refunded'){
			// dados cancelamento de pagamento
			$canceled_at        = $resultado['data']['canceled_at'];
			$canceled_at        = str_replace('T',' ',$canceled_at);
			$canceled_amount    = ($resultado['data']['canceled_amount']/100);
			
			$registroLog .= 'Operação de cancelamento (charge.refunded):<br><br>';
			$registroLog .= '- Pedido: <b>'.$numeroPedido.'</b><br>';
			$registroLog .= '- Valor cancelado: <b>R$'.gCorrigeNumero($canceled_amount).'</b><br>';
		}

		$IPP004 = mysql_query("SELECT IPP004_Id,
									  IPP006_Chave_API 
								 FROM IPP004
							LEFT JOIN T007 ON T007_Id = IPP004_T007_Id
							LEFT JOIN IPP006 ON IPP006_C004_Id = T007_C004_Id
								WHERE IPP004_Id_Pedido_Pagamento = '{$numeroPedido}'");	

		if(mysql_num_rows($IPP004) > 0){
			// PEDIDO DIRETO
			$mIPP004 = mysql_fetch_array($IPP004);
			$stoneClient  = new StoneClient($mIPP004['IPP006_Chave_API']);	

			// Verifica se é recebimento ou cancelamento: charge.paid (pgto recebido) charge.refunded (pgto cancelamento)
			
			if($tipoOperacao == 'charge.paid'){
	
				mysql_query("UPDATE IPP004 SET IPP004_Codigo_Autorizacao_Pagamento = '{$authorization_code}',
											   IPP004_Bandeira_Pagamento 		   = '{$scheme_name}',
											   IPP004_Tipo_Pagamento 			   = '{$account_funding_source}',
											   IPP004_Data_Hora_Pagamento		   = '{$transaction_timestamp}',
											   IPP004_Id_Retorno_Pagamento		   = '{$idRetornoPagamento}',
											   IPP004_Flag_Cancelado		   	   = 'N'
										 WHERE IPP004_Id = '{$mIPP004['IPP004_Id']}'");
				$error = mysql_error();
				if(!empty($error)){
					echo $error;
				}

				// Deu tudo certo, fecha o pedido
				$fecharPedido = new FecharPedido($numeroPedido,'paid');
				$response 	  = $stoneClient->fecharPedido($fecharPedido);

			} elseif($tipoOperacao == 'charge.refunded'){
				
				// Quando cancelado, limpa os dados de autorização
				mysql_query("UPDATE IPP004 SET IPP004_Codigo_Autorizacao_Pagamento = '',
											   IPP004_Bandeira_Pagamento 		   = '',
											   IPP004_Tipo_Pagamento 			   = '',
											   IPP004_Data_Hora_Pagamento		   = '',
											   IPP004_Flag_Cancelado		   	   = 'S',
											   IPP004_Data_Hora_Cancelamento	   = '{$canceled_at}',
											   IPP004_Data_Hora_Cancelamento	   = '{$canceled_amount}'
										 WHERE IPP004_Id = '{$mIPP004['IPP004_Id']}'");
				$error = mysql_error();
				if(!empty($error)){
					echo $error;
				}
			}

		} else {
			
			$IPP005 = mysql_query("SELECT IPP005_T007_Id, 
										  T007_Valor_Total,
										  IPP006_Chave_API,
										  IPP005_Id
								     FROM IPP005
							    LEFT JOIN T007 ON T007_Id = IPP005_T007_Id
								LEFT JOIN IPP006 ON IPP006_C004_Id = T007_C004_Id
		   							WHERE IPP005_Id_Pedido_Pagamento = '{$numeroPedido}'");
			
			if(mysql_num_rows($IPP005) > 0){
				// LISTA PEDIDO

				$mIPP005 = mysql_fetch_array($IPP005);
				$stoneClient  = new StoneClient($mIPP005['IPP006_Chave_API']);	
			
				$registroLog .= '- Encontrou número do pedido na nota fiscal <b>ID '.$mIPP005['IPP005_T007_Id'].'</b><br>';

				//vai buscar o portador, o webhook retorna essas bandeireas
				//"Alelo", "AmericanExpress", "AvanCard", "BanesCard" ,"Ben", "Biq", "Cabal", "CooperCard", "DinersClub"
				//"Discover", "Elo", "FortBrasil", "GreenCard", "Hipercard", "JCB", "MasterCard", "NutriCard", "Senff"
				//"Sodexo", "SoroCred", "Ticket", "UnionPay", "UpBrasil", "ValeCard"," VerdeCard", "VeroCard", "Visa", "VR"

				$bandeiraPagamento = array("Visa" 			 => "01",
										   "MasterCard" 	 => "02",
										   "AmericanExpress" => "03",
			                               "SoroCred" 		 => "04",
			                               "Elo" 			 => "06",
			                               "Hipercard" 		 => "07",
										   "Cabal" 			 => "09",
										   "Alelo" 			 => "10",
										   "BanesCard" 		 => "11",
										   "Discover" 		 => '14',
										   "GreenCard" 		 => '16',
										   "JCB" 		     => "18",
										   "Sodexo" 		 => "23",
										   "ValeCard" 		 => "24",
										   "VR" 			 => "26",
										   "Ticket" 		 => '27');		

				$bandeiraPortador = $bandeiraPagamento[$scheme_name];		
				//$debitoCredito 	  = ($account_funding_source == 'Debit') ? '04' : '03';
				if($account_funding_source == 'Debit'){
					$clausulaTipoPagamento = "AND D027_Tipo_Forma_Pagamento_NFCE = '04'";
				} elseif($account_funding_source == 'Credit'){
					$clausulaTipoPagamento = "AND D027_Tipo_Forma_Pagamento_NFCE = '03'";
				} else {
					$clausulaTipoPagamento = '';
				}

				$D027 = mysql_query("SELECT D027_Id 
									   FROM D027
		   						      WHERE D027_Flag_Cupom = 'S'
									    AND D027_Codigo_Bandeira_NFCE = '{$bandeiraPortador}'
										$clausulaTipoPagamento");
				
				$mD027 = mysql_fetch_array($D027);

				//ALTERA PARA LETRA MAIÚSCULA
				$account_funding_source = strtoupper($account_funding_source);
				$scheme_name            = strtoupper($scheme_name);

				if($tipoOperacao == 'charge.paid'){

					// verifica se ja tem um pagamento lançado com o IPP004_Id_Retorno_Pagamento
					$IPP004 = mysql_query("SELECT IPP004_Id
											 FROM IPP004
											WHERE IPP004_Id_Retorno_Pagamento = '{$idRetornoPagamento}'");
					$mIPP004 = mysql_fetch_array($IPP004);
					
					if($mIPP004['IPP004_Id'] > 0){
							echo "entrou no update";
							//SE JÁ TIVER REGISTRO, FAZ UPDATE
							mysql_query("UPDATE IPP004 SET IPP004_Codigo_Autorizacao_Pagamento = '{$authorization_code}',
											   			   IPP004_Bandeira_Pagamento 		   = '{$scheme_name}',
											   			   IPP004_Tipo_Pagamento 			   = '{$account_funding_source}',
											   			   IPP004_Data_Hora_Pagamento		   = '{$transaction_timestamp}',
														   IPP004_Valor_Recebido			   = '{$paid_amount}',
														   IPP004_Quantidade_Parcelas		   = '{$installmentQuantity}',
														   IPP004_D027_Id					   = '{$mD027['D027_Id']}',
											   			   IPP004_Flag_Cancelado		   	   = 'N'
										 		     WHERE IPP004_Id = '{$mIPP004['IPP004_Id']}'
													   AND IPP004_T007_Id = '{$mIPP005['IPP005_T007_Id']}'");
						$error = mysql_error();
						if(!empty($error)){
							echo $error;
						}

						$registroLog .= '- Já existia parcela com o <b>ID '.$idRetornoPagamento.'</b> de retorno pagamento, atualizou pagamento ID parcela '.$mIPP004['IPP004_Id'].'<br>';

					}else{
						echo "entrou no insert";
						// INSERT
						mysql_query("INSERT INTO IPP004 (IPP004_T007_Id,
											             IPP004_D027_Id,
											             IPP004_Valor_Recebido,
											             IPP004_Quantidade_Parcelas,
											             IPP004_Data_Hora_Pagamento,
											             IPP004_Bandeira_Pagamento,
											             IPP004_Tipo_Pagamento,
											             IPP004_Codigo_Autorizacao_Pagamento,
														 IPP004_Flag_Cancelado,
											             IPP004_Id_Retorno_Pagamento													 													 
									             )VALUES(
											             '{$mIPP005['IPP005_T007_Id']}',
									    	             '{$mD027['D027_Id']}',
											             '{$paid_amount}',
											             '{$installmentQuantity}',
											             '{$transaction_timestamp}',
											             '{$scheme_name}',
											             '{$account_funding_source}',
											             '{$authorization_code}',
														 'N',
											             '{$idRetornoPagamento}')");
						$error = mysql_error();
						if(!empty($error)){
							echo 'No_insert- '.$error;
						}

						$registroLog .= '- Inseriu parcela de pagamento ID retorno <b>'.$idRetornoPagamento.'</b><br>';

					}					

					// Deu tudo certo, verifica para fechar o pedido
					$total = mysql_query("SELECT SUM(IPP004_Valor_Recebido) as totalRecebido
											 FROM IPP004
											WHERE IPP004_T007_Id = '{$mIPP005['IPP005_T007_Id']}'");

					$mTotal = mysql_fetch_array($total);

					echo "valor total recebido {$mTotal['totalRecebido']} e total da nota {$mIPP005['T007_Valor_Total']}";
					if($mTotal['totalRecebido'] == $mIPP005['T007_Valor_Total']){
 						//$stoneClient  = new StoneClient('sk_BGDd46d7ietoYoV3');	
						$fecharPedido = new FecharPedido($numeroPedido,'paid');
						$response 	  = $stoneClient->fecharPedido($fecharPedido); 

						$registroLog .= '- Recebeu o valor total de <b>R$'.gCorrigeNumero($mIPP005['T007_Valor_Total']).'</b>, fechando pedido na POS<br>';

					} else {
						$registroLog .= '- Não recebeu o valor total de <b>R$'.gCorrigeNumero($mIPP005['T007_Valor_Total']).'</b>, aguardando outros pagamentos para fechar pedido<br>';
					}			
					
					registrarLog(array("T007", $mIPP005['IPP005_T007_Id'], $g['empresaAtual'], "Integração pagamento POS (recebimento)", "Parcela incluída com sucesso referente ao pedido <b>".$numeroPedido."</b>", $registroLog, "N", "3"));

				}elseif($tipoOperacao == 'charge.refunded'){
					
					$IPP004 = mysql_query("SELECT IPP004_Id
											 FROM IPP004
											WHERE IPP004_Id_Retorno_Pagamento = '{$idRetornoPagamento}'
											  AND IPP004_T007_Id = '{$mIPP005['IPP005_T007_Id']}'");
											  
					$mIPP004 = mysql_fetch_array($IPP004);

					mysql_query("UPDATE IPP004 SET IPP004_Codigo_Autorizacao_Pagamento = '',
												   IPP004_Bandeira_Pagamento 		   = '',
												   IPP004_Tipo_Pagamento 			   = '',
												   IPP004_Data_Hora_Pagamento		   = '',
												   IPP004_Flag_Cancelado		   	   = 'S',
												   IPP004_Valor_Recebido               = '0',
												   IPP004_Data_Hora_Cancelamento	   = '{$canceled_at}',
												   IPP004_Valor_Cancelado			   = '{$canceled_amount}' 
											 WHERE IPP004_Id = '{$mIPP004['IPP004_Id']}'");
					$error = mysql_error();
					if(!empty($error)){
						echo 'aqui '.$error;
					}

					$registroLog .= '- Vai cancelar parcela com <b>ID '.$mIPP004['IPP004_Id'].'</b><br>';

					// Se o valor total cancelado está Ok, limpa o registro para poder enviar um novo pagamento
					$total = mysql_query("SELECT SUM(IPP004_Valor_Cancelado) as valorTotalCancelado
											FROM IPP004
										   WHERE IPP004_T007_Id = '{$mIPP005['IPP005_T007_Id']}'");

			  	 	$mTotal = mysql_fetch_array($total);

					// Verifica se existe valor recebido em dinheiro
					$dinheiro = mysql_query("SELECT IFNULL(IPP004_Valor_Recebido,0) as totalDinheiro
					                           FROM IPP004 
				                          LEFT JOIN D027 ON D027_Id = IPP004_D027_Id
					                          WHERE IPP004_T007_Id ='{$mIPP005['IPP005_T007_Id']}'
						                        AND D027_Tipo_Forma_Pagamento_NFCE = '01'");
                    $mdinheiro = mysql_fetch_array($dinheiro);

					echo "valor total cancelado cartão {$mTotal['valorTotalCancelado']} | dinheiro = {$mdinheiro['totalDinheiro']} | total da nota {$mIPP005['T007_Valor_Total']}";
					if(($mTotal['valorTotalCancelado'] + $mdinheiro['totalDinheiro']) >= $mIPP005['T007_Valor_Total']){
						// Limpa numero do pedido de pagamento
						mysql_query("UPDATE IPP005 SET IPP005_Id_Pedido_Pagamento = '' WHERE IPP005_Id = '{$mIPP005['IPP005_Id']}'");

						$registroLog .= '- Valor cancelado é igual ao valor total <b>R$'.gCorrigeNumero($mIPP005['T007_Valor_Total']).'</b>, vai limpar o número do pedido na nota fiscal <br>';
					}	

					registrarLog(array("T007", $mIPP005['IPP005_T007_Id'], $g['empresaAtual'], "Integração pagamento POS (cancelamento na POS)", "Parcela cancelada com sucesso: <b>".$numeroPedido."</b>", $registroLog, "N", "3"));
	
				}
				
			} else {
				//entra aqui se não encontrar um pedido direto ou lista de pedido
				//gravar um log de erro
				$registroLog .= '- Não encontrou pagamento, ID de retorno de pagamento <b>'.$idRetornoPagamento.'</b> para o pedido <b>'.$numeroPedido.'</b>';
				registrarLog(array("T007", '0', $g['empresaAtual'], "Integração pagamento POS (pedido não encontrado)", "Não foi encontrado pagamento", $registroLog, "S", "3"));

			}
		}
		http_response_code(200);					
	break;
	default:
		// $API001->retorno404();
}