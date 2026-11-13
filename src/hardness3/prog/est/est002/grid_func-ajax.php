<?php
namespace hardness;
require_once('bibliotecas/classes/VEN012.php');
require_once('bibliotecas/classes/VEN003.php');
require_once('bibliotecas/classes/EST002.php');
$VEN012 = new VEN012();
$EST002 = new EST002();

$resposta = array('code' => true, 'data' => array());

	switch($g['r'][3]) {

		case 'abreXML':
			$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;

			$sql = 'select T007_Numero_Protocolo_Nfe, T007_Data_Emissao from T007 WHERE T007_Id ='.$id;
			$result = mysql_query($sql);
			$array = mysql_fetch_assoc($result);


			$cAno_Mes_NFe=substr($array['T007_Data_Emissao'],0,7);
			$Diretorio_NFe=$g['pathDados'] . 'nfe';
			//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
			$cArquivo="$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/".$array['T007_Numero_Protocolo_Nfe'].".xml";

			if (file_exists($cArquivo)){
				// We'll be outputting a PDF
				header('Content-type: text/xml');

				// It will be called downloaded.pdf
				header('Content-Disposition: attachment; filename="'.$array['T007_Numero_Protocolo_Nfe'].'"');

				// The PDF source is in original.pdf
				readfile($cArquivo);
			}
			else{
				echo "XML Inexistente";
			}
			die();
		break;

		case 'retornarPedido':
			$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
			$T005_Flag_Status = isset($_REQUEST['T005_Flag_Status']) ? $_REQUEST['T005_Flag_Status'] : 0;
			$verifica = $VEN012->retornarPedido($T005_Id, $T005_Flag_Status);

			if ($verifica !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $verifica;
			}
		break;

		case 'retornarPedidoExpedicao':
			// Encaminha o retorno de etapa para a regra centralizada da classe EST002.
			$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
			$T005_Flag_Status = isset($_REQUEST['T005_Flag_Status']) ? $_REQUEST['T005_Flag_Status'] : 0;
			$verifica = $EST002->retornarPedidoExpedicao($T005_Id, $T005_Flag_Status);

			if ($verifica !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $verifica;
			}
		break;

		case 'retornarPedidoSeparacao':
			$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
			$verifica = $VEN012->retornarPedidoSeparacao($T005_Id);

			if ($verifica !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $verifica;
			}
		break;
			
		case 'confirmarEntrega':
			global $g;

			/* Utilizada principalmente para o Magento 2. Mas é possível
			chamar qualquer API ao coletar*/
			require_once('bibliotecas/classes/API001.php');
			$API001 = new API001();

			$mensagem = "";
			
			$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;

			$sql = <<<EOT
			SELECT T007_Flag_Cancelada,
				   T007_Flag_ACP,
				   T007_Id,
				   T007_Numero_Nota_Fiscal,
				   T007_Numero_Protocolo_Nfe,
				   T007_Protocolo_Inutilizacao,
				   T005_Flag_Expedicao,
				   T005_Id,
				   T005_Flag_Pdv_Importado
		      FROM T005
		 LEFT JOIN T007 on T007_T005_Id=T005_Id
			 WHERE T005_Id = '{$T005_Id}'
		  ORDER BY T007_Id DESC
EOT;
			$res = mysql_query($sql);
			$row = mysql_fetch_assoc($res);

			$verifica='';
			include("validaColeta.php");

			if(empty($verifica)){
				$verifica = $VEN012->confirmarEntrega($T005_Id);

				// Integrações externas só podem ser chamadas após o COMMIT da coleta.
				if ($verifica === true) {
					$mensagem = "Pedido coletado!";

					if(($g['C031']['EnviarMagentoColetar']=="S") && ($g['C031']['plataformaECommerce']=="magentoSoap" || $g['C031']['plataformaECommerce2']=="magentoSoap")){
						require_once('bibliotecas/webservices/magento/magentoSoap.php');
						$MagentoSoap = new MagentoSoap();
						$retornoMagento = $MagentoSoap->atualizarPedidoMagento($T005_Id);

						if($retornoMagento !== true){
							$mensagem.="</br>Magento - Erro ao enviar dados de faturamento:".$retornoMagento;
						}else{
							$mensagem.="</br>Magento - Dados do faturamento enviados com sucesso.";
						}
					}

					/* Se o sistema estiver configurado para utilizar o Magento2, o Intelipost é
					feito junto com a criação da entrega na aba "Coletados"*/
					if(($g['C031']['EnviarIntelipostColetar']=="S") && (!empty($g['C031']['IntelipostUrlApi']))){
						require_once('bibliotecas/webservices/intelipost/apiIntelipost.php');
						$Intelipost = new Intelipost();
						$retornoIntelipost = $Intelipost->criarPedido($T005_Id);			

						if($retornoIntelipost[0]=="SUCCESS"){
							$mensagem.="</br>Intelipost - Dados do faturamento enviados com sucesso.";
						}else{
							$mensagem.="</br>Intelipost - Erro ao enviar dados de faturamento:<br>".$retornoIntelipost[1];
						}
					}

					//Se a plataforma for magento2
					if($g['C031']['plataformaECommerce'] == "magento2" || $g['C031']['plataformaECommerce2'] == "magento2") {
						/*Chama processo que retorna array com algumas
						opções da API*/
						$mg2ColetaCriaEntrega = $API001->executaProcesso(25)['COLETAR_CRIAR_ENTREGA'];

						//Se a opção de criar entrega ao coletar estiver ativa
						if($mg2ColetaCriaEntrega) {
							//Cria a entrega com o processo 31 e usa opção pularIntelipost pois, o Intelipost foi enviado acima
							$mg2EntregaRetorno = $API001->executaProcesso(31, array('T005_Id' => $T005_Id, 'silencioAjax' => true, 'silencioEcho' => true, 'pularIntelipost' => true));
						
							// Se for erro da mensagem de erro, se der certo da mensagem de sucesso
							if(!$mg2EntregaRetorno) {
								$mensagem.="</br></br><b>Magento 2:</b> Erro ao criar a entrega";
							} else {
								$mensagem.="</br></br><b>Magento 2:</b> Sucesso ao criar a entrega";
							}
						}
					}
				}
			}

			if ($verifica !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $verifica;
			}else{
				$resposta['code'] = true;
				$resposta['data'] = $mensagem;
			}
		break;


		case 'correioPostarEtiqueta':
			global $g;
			$mensagem = "";
			
			$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;

			$sql = <<<EOT
			SELECT
				T005_Id,
				T007_Id,
				T268_Codigo_Rastreio,
				T268_Id,
				D036_Flag_Correios,
				T007_Numero_Nota_Fiscal
			FROM T005
			left join T007 on T007_T005_Id=T005_Id
			left join D022 on D022_Id=T005_D022_Id
			left join D036 on D036_Id=IF(T005_D036_Id > 0,T005_D036_Id, D022_D036_Id)
			left join T268 on T268_T007_Id=T007_Id
			WHERE 
				T007_Id = '{$T007_Id}'
EOT;
			$res = mysql_query($sql);
			$T005 = mysql_fetch_assoc($res);

			//Se a flag do tipo transportadora for Correio e tiver um T268 vinculado ao t005
			if($T005['D036_Flag_Correios']!="S"){
				$resposta['code'] = false;
				$resposta['data'] = "Pedido ".$T005['T005_Id']." não é da transportadora Correio.";
			} else if (empty($T005['T268_Id'])){
				$resposta['code'] = false;
				$resposta['data'] = "A nota fiscal ".$T005['T007_Numero_Nota_Fiscal']." não possui registro na aba CORREIOS, favor verificar e incluir.";
			} else {

				//Se o codigo de rastreio ja estiver preenchido, ou seja, já foi feito uma postagem
				//Imprimir etiqueta
				if(!empty($T005['T268_Codigo_Rastreio'])){ 					
					$retorno = $EST002->imprimirEtiquetaCorreio($T005['T268_Id'],$T005['T268_Codigo_Rastreio']);
					$resposta['code'] = $retorno[0];

					if($retorno[0]===true){
						$resposta['data'] = "Pedido ".$T005['T268_Id']." - Etiqueta impressa".$retorno[1];
					}else{
						$resposta['data'] = "Pedido ".$T005['T268_Id']." - ".$retorno[1];
					}
				}else{
					//Se o codigo de rastreio não estiver preenchido, realizar a postagem
					$retorno = $EST002->prePostagemPacote($T005['T268_Id']);

					if(($retorno[0]===true) && (!empty($retorno[2]))){
						//Se a postagem retornar true, ou seja, foi postado e retornou um codigo de rastreio chama a função que imprime etiqueta
						$retornoEtiqueta = $EST002->imprimirEtiquetaCorreio($T005['T268_Id'],$retorno[2]);

						$resposta['code'] = $retorno[0];
						$resposta['data'] = "Pedido ".$T005['T268_Id']." - Pedido ".$retorno[1].$retornoEtiqueta[1];

					}else{
						$resposta['code'] = false;
						$resposta['data'] = "Pedido ".$T005['T005_Id']." - Erro ao postar: ".$retorno[1];
					}
				}
			}
		break;

		case 'confirmarEntregaVariosPedidos':
			global $g;

			/* Utilizada principalmente para o Magento 2. Mas é possível
			chamar qualquer API ao coletar*/
			include('bibliotecas/classes/API001.php');
			$API001 = new API001();

			if ($r_inicio==1){

				$extra = base64_decode($r_extra);
	      		$extra = gInsertExtraWhere($extra, "T007_Flag_Imprimir_Romaneio = 'S'");
				$extraSemGroupBy = substr($extra,0,-17); //retira o group by T005_Id

		        $sqlCache = mysql_query("SELECT T007_Flag_Cancelada,
												T007_Flag_ACP,
												T007_Id,
												T007_Numero_Nota_Fiscal,
												T007_Numero_Protocolo_Nfe,
												T007_Protocolo_Inutilizacao,
												T005_Flag_Expedicao,
												T005_Pedido_Ecommerce,
												T005_Data_Hora_Envio_Ecommerce,
												T005_Id
										   FROM T005 
											    {$extra}
										GROUP BY T005_Id
										ORDER BY T007_Id DESC");
				
		        $erro = mysql_error();

		        if (!empty($erro)) {
		        	$resposta['code'] = false;
		        	$resposta['erros'] = 'Erro consultando itens dos pedidos: $erro';
		        }

		    	$count=0;
		    	$numero = mysql_num_rows($sqlCache);
		    	log("numero de linhas:".$numero);

		    	while($rowCache = mysql_fetch_array($sqlCache)){
		    		$cache[$count]['T007_Flag_ACP'] = $rowCache['T007_Flag_ACP'];
		    		$cache[$count]['T007_Flag_Cancelada'] = $rowCache['T007_Flag_Cancelada'];
		    		$cache[$count]['T007_Id'] = $rowCache['T007_Id'];
		    		$cache[$count]['T007_Numero_Nota_Fiscal'] = $rowCache['T007_Numero_Nota_Fiscal'];
		    		$cache[$count]['T007_Numero_Protocolo_Nfe'] = $rowCache['T007_Numero_Protocolo_Nfe'];
		    		$cache[$count]['T007_Protocolo_Inutilizacao'] = $rowCache['T007_Protocolo_Inutilizacao'];
		    		$cache[$count]['T005_Flag_Expedicao'] = $rowCache['T005_Flag_Expedicao'];
		    		$cache[$count]['T005_Id'] = $rowCache['T005_Id'];
					$cache[$count]['T005_Pedido_Ecommerce'] = $rowCache['T005_Pedido_Ecommerce'];
					$cache[$count]['T005_Data_Hora_Envio_Ecommerce'] = $rowCache['T005_Data_Hora_Envio_Ecommerce'];
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
				apc_delete("cacheT005Coleta-{$g['usuarioAtual']}");
				apc_delete("cacheT005Coleta-resposta-{$g['usuarioAtual']}");
		        $retorno = apc_store("cacheT005Coleta-{$g['usuarioAtual']}", serialize($cache));
			        if(!$retorno){
			        	$resposta['code'] = false;
			        	$resposta['data'] = 'Erro ao criar variável no cache';
			        }
	        } else {
				$cache = unserialize(apc_fetch("cacheT005Coleta-{$g['usuarioAtual']}"));
				$resposta = unserialize(apc_fetch("cacheT005Coleta-resposta-{$g['usuarioAtual']}"));
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
			$row = array();
	    	$erroIntegracao = "";
	    	$erroSistema = "";
			
		    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) {

				
				$row['T007_Flag_ACP'] = $cache[$i]['T007_Flag_ACP'];
	    		$row['T007_Flag_Cancelada'] = $cache[$i]['T007_Flag_Cancelada'];
	    		$row['T007_Id'] = $cache[$i]['T007_Id'];
	    		$row['T007_Numero_Nota_Fiscal'] = $cache[$i]['T007_Numero_Nota_Fiscal'];
	    		$row['T007_Numero_Protocolo_Nfe'] = $cache[$i]['T007_Numero_Protocolo_Nfe'];
	    		$row['T007_Protocolo_Inutilizacao'] = $cache[$i]['T007_Protocolo_Inutilizacao'];
	    		$row['T005_Flag_Expedicao'] = $cache[$i]['T005_Flag_Expedicao'];
	    		$row['T005_Id'] = $cache[$i]['T005_Id'];
				$row['T005_Pedido_Ecommerce'] = $cache[$i]['T005_Pedido_Ecommerce'];
				$row['T005_Data_Hora_Envio_Ecommerce'] = $cache[$i]['T005_Data_Hora_Envio_Ecommerce'];

	    		$verifica='';
				include("validaColeta.php");

				if(empty($verifica)){
					$verifica = $VEN012->confirmarEntrega($row['T005_Id']);

					// O romaneio e as integrações dependem da coleta ter sido efetivada.
					if ($verifica === true) {
						$sqlUpdtT007 = <<<SQL
							UPDATE T007
							SET T007_Flag_Imprimir_Romaneio = 'N'
							WHERE T007_T005_Id = '{$row['T005_Id']}'
SQL;
						if (!mysql_query($sqlUpdtT007)) {
							$erroSistema .= "Pedido ".$cache[$i]['T005_Id']." - Coleta concluída, mas não foi possível desmarcar o romaneio: ".mysql_error()."</br>";
						}
						$ok++;

					if(($g['C031']['EnviarMagentoColetar']=="S") && ($g['C031']['plataformaECommerce']=="magentoSoap" || $g['C031']['plataformaECommerce2']=="magentoSoap")){
						log("Entrou no magento");
						require_once('bibliotecas/webservices/magento/magentoSoap.php');
						$MagentoSoap = new MagentoSoap();
						$retornoMagento = $MagentoSoap->atualizarPedidoMagento($row['T005_Id']);

		           		if($retornoMagento !== true){
		           			log("Erro no magento");
		           			$erroIntegracao .= "Pedido <b>".$cache[$i]['T005_Id']." </b> -<b> Magento</b> - Erro ao enviar dados, verifique o histórico do pedido.<br/>";
		           		}
					}

					if(($g['C031']['EnviarIntelipostColetar']=="S") && (!empty($g['C031']['IntelipostUrlApi']))){
						log("Entrou no intelipost");
						require_once('bibliotecas/webservices/intelipost/apiIntelipost.php');
	        			$Intelipost = new Intelipost();
	        			$retornoIntelipost = $Intelipost->criarPedido($row['T005_Id']);			

		           		if($retornoIntelipost[0]!="SUCCESS"){
		           			log("Erro no intelipost");
		           			$erroIntegracao .= "Pedido <b>".$cache[$i]['T005_Id']." </b> -<b> Intelipost </b>- Erro ao enviar dados, verifique o histórico do pedido.<br/>";
		           		}
					}

					//Se for a plataforma Mg2
						if($g['C031']['plataformaECommerce'] == "magento2" || $g['C031']['plataformaECommerce2'] == "magento2")
						{
						// Chama processo de opções para verificar se a criação da entrega na coleta é verdadeira
						$mg2ColetaCriaEntrega = $API001->executaProcesso(25)['COLETAR_CRIAR_ENTREGA'];
						if($mg2ColetaCriaEntrega)
						{
							//Cria a entrega no mg2
							$mg2EntregaRetorno = $API001->executaProcesso(31, array('T005_Id' => $row['T005_Id'], 'silencioAjax' => true, 'silencioEcho' => true, 'pularIntelipost' => true));
						
							//Se for erro avisa na tela JS
							if(!$mg2EntregaRetorno)
							{
								$erroIntegracao.= "Pedido <b>".$cache[$i]['T005_Id'] . " - Magento 2</b> - Erro ao criar a entrega no Magento 2<br>";
							}
						}
						}
					} else {
						$erroSistema .= "Pedido ".$cache[$i]['T005_Id']." - Erro ao coletar pedido: ".$verifica."</br>";
						$erro++;
					}
				}else{
					$erroSistema .= "Pedido ".$cache[$i]['T005_Id']." - Erro ao coletar pedido:".$verifica."</br>";
					$erro++;
				}    	
	        }

			$resposta['data'] = '';        
		    $resposta['ok'] += $ok;
		    $resposta['erro'] += $erro;
		    $resposta['erros'] .= $erros;
		    $resposta['erroIntegracao'].=$erroIntegracao;
		    $resposta['erroSistema'].=$erroSistema;

			if($resposta['etapa'] >= $resposta['etapas']){
				apc_delete("cacheT005Coleta-{$g['usuarioAtual']}");
				apc_delete("cacheT005Coleta-resposta-{$g['usuarioAtual']}");
				$resposta['concluido'] = 1;
			} else {
		        apc_delete("cacheT005Coleta-resposta-{$g['usuarioAtual']}");
		        $resposta['etapa'] = $resposta['etapa'] + 1;
		        apc_store("cacheT005Coleta-resposta-{$g['usuarioAtual']}", serialize($resposta));
			}
		break;

		case 'postarEtiquetasCorreiosPedidosMarcados':
			global $g;
			
			if ($r_inicio==1){

				$extra = base64_decode($r_extra);
				$extra = gInsertExtraWhere($extra, "T007_Flag_Imprimir_Romaneio = 'S'");
				$extraSemGroupBy = substr($extra,0,-17); //retira o group by T005_Id

		        $sqlCache = mysql_query("SELECT
												T005_Id,
												T007_Id,
												T268_Codigo_Rastreio,
												T268_Id,
												D036_Flag_Correios
											FROM T005
											{$extra}");
		        $erro = mysql_error();

		        log("SELECT
												T005_Id,
												T007_Id,
												T268_Codigo_Rastreio,
												T268_Id,
												D036_Flag_Correios
											FROM T005
											{$extra}");

		        if (!empty($erro)) {
		        	$resposta['code'] = false;
		        	$resposta['erros'] = 'Erro consultando itens dos pedidos:'.$erro;
		        }

		    	$count=0;

		    	while($rowCache = mysql_fetch_array($sqlCache)){
		    		$cache[$count]['T005_Id'] = $rowCache['T005_Id'];
			        $cache[$count]['T007_Id']= $rowCache['T007_Id'];
			        $cache[$count]['T268_Codigo_Rastreio']= $rowCache['T268_Codigo_Rastreio'];
			        $cache[$count]['T268_Id']= $rowCache['T268_Id'];
			        $cache[$count]['D036_Flag_Correios']= $rowCache['D036_Flag_Correios'];

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
				apc_delete("cacheT005Correio-{$g['usuarioAtual']}");
				apc_delete("cacheT005Correio-resposta-{$g['usuarioAtual']}");
		        $retorno = apc_store("cacheT005Correio-{$g['usuarioAtual']}", serialize($cache));
		        if(!$retorno){
		        	$resposta['code'] = false;
		        	$resposta['erros'] = 'Erro ao criar variável no cache';
		        }else{
		        	$resposta['code'] = true;
		        }		    
				$resposta['T005_Id'] = 0;
		    }else {
				$cache = unserialize(apc_fetch("cacheT005Correio-{$g['usuarioAtual']}"));
				$resposta = unserialize(apc_fetch("cacheT005Correio-resposta-{$g['usuarioAtual']}"));
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
			$erros = "";
			$transportadoraErrada = 0;
			$codigosRastreio = "";
			$possuiCodigoRastreio = 0;
			$update = false;
			
		    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) {
		    	//Verificando se O tipo transporte é correio e se tem um T268 vinculado ao T005
		    	if(($cache[$i]['D036_Flag_Correios']=="S") && !empty($cache[$i]['T268_Id'])){
		    		//Verifica se tem código de rastreio, se sim concatena o código na variavel se não posta o pedido.
		    		if(!empty($cache[$i]['T268_Codigo_Rastreio'])){
		    			if(empty($codigosRastreio)){
		    				$codigosRastreio = $cache[$i]['T268_Codigo_Rastreio'].",";
		    			}else{
		    				$codigosRastreio.=$cache[$i]['T268_Codigo_Rastreio'].",";
		    			}
		    			$possuiCodigoRastreio++;
		    			$update = true;
		    		}else{
		    			//Se o codigo de rastreio não estiver preenchido, realizar a postagem
						$retorno = $EST002->prePostagemPacote($cache[$i]['T268_Id']);

						if(($retorno[0]===true) && (!empty($retorno[2]))){
							$ok++;
							$codigosRastreio.=$retorno[2].",";
			    			$update = true;
						}else{
							$erro++;
							$erros .= "<br>Pedido ".$cache[$i]['T005_Id']." - Erro ao postar: ".$retorno[1];
						}
		    		}
				}else{
					$transportadoraErrada++;
				}

				if($update===true){
	    			mysql_query("UPDATE T007 SET T007_Flag_Imprimir_Romaneio='N' WHERE T007_Id='".$cache[$i]['T007_Id']."'");
				}			
	        }

			$resposta['data'] = '';        
		    $resposta['ok'] += $ok;
		    $resposta['erro'] += $erro;
		    $resposta['erros'] .= $erros;
		    $resposta['transportadoraErrada'] += $transportadoraErrada;
		    $resposta['codigosRastreio'] .= $codigosRastreio;
		    $resposta['possuiCodigoRastreio'] += $possuiCodigoRastreio;

			if($etapa > $etapas){
				apc_delete("cacheT005Correio-{$g['usuarioAtual']}");
				apc_delete("cacheT005Correio-resposta-{$g['usuarioAtual']}");
			} else {
		        apc_delete("cacheT005Correio-resposta-{$g['usuarioAtual']}");
		        apc_store("cacheT005Correio-resposta-{$g['usuarioAtual']}", serialize($resposta));
			}
		break;

		case 'gerarEtiquetasCorreio':	

			$codigosRastreio = isset($r_codigosRastreio) ? $r_codigosRastreio : false;
			$codigosRastreio = substr($codigosRastreio,0,-1);

      		if(empty($codigosRastreio)){
      			$resposta['code'] = false;
				$resposta['retornoEtiquetas'] = "<b><font color='red'>Erro na impressão das etiquetas<br></font></b><br>Nenhum código de rastreio foi retornado para impressão.";
      		}else{
      			$retorno = $EST002->imprimirEtiquetaCorreio("",$codigosRastreio);

				$resposta['code'] = $retorno[0];
				$resposta['retornoEtiquetas'] = $retorno[1];
      		}					

		break;

		case 'gerarNumeroRomaneio':	

			$extra = base64_decode($r_extra);
      		$extra = gInsertExtraWhere($extra, "T007_Flag_Imprimir_Romaneio = 'S'");

      		$sqlT005 = mysql_query("SELECT T005_Id FROM T005 {$extra} AND T005_T275_Id>0");
      		$numT005 = mysql_num_rows($sqlT005);

      		if($numT005>0){
      			$resposta['code'] = false;
				$resposta['data'] = "Há pedidos selecionados ja vinculados a um romaneio.";
      		}else{
      			$retorno = $EST002->gerarNumeroRomaneio($extra);

				$resposta['code'] = $retorno[0];
				$resposta['data'] = $retorno[1];
      		}					

		break;
		
		case 'confirmarEntregaInterna':			
			$verifica = $EST002->confirmarEntregaInterna($r_D109_Id);
			if ($verifica !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $verifica;
			}else{
				$resposta['code'] = true;
				$resposta['data'] = 'Operação realizada com sucesso!';				
			}

		break;		

	case 'verificarQteEntrega':
			$T006_Id 		= isset($r_T006_Id) ? $r_T006_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;						
			
			$retorno = $EST002->verificarQteEntrega($T006_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	/**
	* Esta função é antiga, utilizada somente antes do REFACTORY do sistema de ETIQUETAS
	* A partir de agora, todas as etiquetas são gerencidas/criadas pelo editor de ediquetas
	* Somente foi mantido este código, porque a CRV utiliza
	* Na CRV as etiquetas possuem várias particularidades, que com o tempo devem ser passadas para o editor de etiquetas
	*/
	case 'impTag':
		// inicio
		$D138_Id = (isset($r_D138_Id)) ? $r_D138_Id : false;
		$D138_Id = 7;
		// pacote ou caixa
		if ($D138_Id == 7 && isset($_REQUEST['TipoEtiqueta']) && $_REQUEST['TipoEtiqueta'] == 'P') {
			  $D138_Id = 8;
		}
		
		$D00X_Id = isset($_REQUEST['D00X_Id']) ? $_REQUEST['D00X_Id'] : false;
			$sql = <<<EOT
				SELECT 
					D049_Id as Codigo_Produto, 
					D001_Descricao_Produto, 
					D082_Marca, 
					D037_Unidade,
					D003_Nome_Linha,
					D060_Descricao_Grupo,
					D002_Descricao_Produto,
					D015_Nome_Grupo
				FROM D049 
					LEFT JOIN D001 ON D001_Id=D049_D001_Id 
					LEFT JOIN D037 ON D037_Id=D001_D037_Id 
					LEFT JOIN D003 ON D003_Id=D001_D003_Id 
					LEFT JOIN D060 ON D060_Id=D001_D060_Id 
					LEFT JOIN D002 ON D002_Id=D001_D002_Id 
					LEFT JOIN D015 ON D015_Id=D001_D015_Id 
					LEFT JOIN D082 ON D082_Id=D049_D082_Id 
				WHERE 
					D049_Id = '{$D00X_Id}';
EOT;
			$res = mysql_query($sql);
			$row = mysql_fetch_assoc($res);
			$row['D001_Descricao_Produto'] = preg_replace('/ +/', ' ', $row['D001_Descricao_Produto']);
			if ($D138_Id == 7 || $D138_Id == 8) {
				/**
				* Linha 1: D001_D003_Id + D001_D060_Id + D001_D002_Id
				* Linha 2: D001_D015_Id
				* Linha 3: D001_Descricao_Produto (menos todos os outros)
				* Linha 4: D082_Marca
				*/
				$row['D003_Nome_Linha'] = ($row['D003_Nome_Linha'] == 'EM BRANCO') ? '' : $row['D003_Nome_Linha'];
				$row['D060_Descricao_Grupo'] = ($row['D060_Descricao_Grupo'] == 'EM BRANCO') ? '' : $row['D060_Descricao_Grupo'];
				$row['D002_Descricao_Produto'] = ($row['D002_Descricao_Produto'] == 'EM BRANCO') ? '' : $row['D002_Descricao_Produto'];
				$row['D015_Nome_Grupo'] = ($row['D015_Nome_Grupo'] == 'EM BRANCO') ? '' : $row['D015_Nome_Grupo'];
				$d1 = trim($row['D003_Nome_Linha'] . ' ' . $row['D060_Descricao_Grupo'] . ' ' . $row['D002_Descricao_Produto']);
				$d2 = trim($row['D015_Nome_Grupo']);
				$d3 = trim($row['D001_Descricao_Produto']);
				$d3 = str_replace($row['D003_Nome_Linha'], '', $d3);
				$d3 = str_replace($row['D060_Descricao_Grupo'], '', $d3);
				$d3 = str_replace($row['D002_Descricao_Produto'], '', $d3);
				$d3 = str_replace($row['D015_Nome_Grupo'], '', $d3);
				$d3 = trim(preg_replace('/ +/', ' ', $d3));
				$d1 = substr($d1, 0, 25);
				$d2 = substr($d2, 0, 25);
				$d3 = substr($d3, 0, 25);
				$Codigo_Produto = (int) $row['Codigo_Produto'];
				$marca = substr($row['D082_Marca'], 0, 13);
				$unidade = $row['D037_Unidade'];
			} else {
				$d1 = substr($row['D001_Descricao_Produto'], 0, 20);
				$d2 = (strlen($row['D001_Descricao_Produto']) > 20) ? substr($row['D001_Descricao_Produto'], 20, 20) : '';
				$d3 = $d4 = '';
				$Codigo_Produto = (int) $row['Codigo_Produto'];
				$marca = substr($row['D082_Marca'], 0, 13);
				$unidade = $row['D037_Unidade'];
			}
			
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		
		//if ($D138_Id == 3 || $D138_Id == 4 || $D138_Id == 5 || $D138_Id == 7 || $D138_Id == 8) {
			// D138_Id = 3
			$sqlModelo = "SELECT * FROM D138 WHERE D138_Id = '{$D138_Id}';";
			$resModelo = mysql_query($sqlModelo);
			$rowModelo = mysql_fetch_assoc($resModelo);
			// print
			$print = $rowModelo['D138_Codigo'];
			$print = str_replace('[[stx]]', chr(2), $print);
			$print = str_replace('[[d1]]', $d1, $print);
			$print = str_replace('[[d2]]', $d2, $print);
			$print = str_replace('[[d3]]', $d3, $print);
			$print = str_replace('[[d4]]', $d4, $print);
			$print = str_replace('[[Codigo_Produto]]', $Codigo_Produto, $print);
			$print = str_replace('[[EMPRESA]]', $marca, $print);
			$print = str_replace('[[QTDPRODUTO]]', $r_QtdProdutos, $print);
			$print = str_replace('[[UNIDADE]]', $unidade, $print);
		//}
		
		// Várias etiquetas do mesmo produto
		if (!empty($r_QtdEtiquetas) && $r_QtdEtiquetas > 1) {
			$tmp = $print;
			$print = '';
			for ($i = 1; $i <= $r_QtdEtiquetas; $i++) {
				$print .= $tmp . "\n";
			}
		}
		
		// send to printer
		$file = '/tmp/est002ImpTag'.$confUsuario['dbDatabase'].'.txt';
		file_put_contents($file, $print);
		$resposta['code'] = false;
		eval("echo `lpr -Pestoque01 {$file}`;");
	break;
	
	case 'checkboxFlagColeta':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;						
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		if($selected == 'N'){
			mysql_query("UPDATE T005 SET T005_Flag_Coleta = 'N' WHERE T005_Id = '{$T005_Id}'");
		} else {
			mysql_query("UPDATE T005 SET T005_Flag_Coleta = 'S' WHERE T005_Id = '{$T005_Id}'");
		}
		
		$resposta['code'] = true;
	break;

	case 'est002validaCampoCheckboxRomaneio':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		$T007=mysql_query("UPDATE T007 SET T007_Flag_Imprimir_Romaneio='{$selected}' where T007_Id='{$T007_Id}'");
		if($T007_Id){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
		}
	break;

	case 'est002validaCampoCheckboxEnvioemail':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		$T007=mysql_query("UPDATE T007 SET T007_Flag_Enviar_XML_Transportadora='{$selected}' where T007_Id='{$T007_Id}'");
		if($T007_Id){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
		}
	break;

	case 'est002PreencherSeparados':
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		$res = mysqli_query("UPDATE T006 SET T006_Quantidade_Separacao = T006_Quantidade 
									   WHERE T006_T005_Id='{$T005_Id}' 
									     AND T006_Flag_Cancelado!='S'");


		// Vai fazer o lançamento na tabela T238
		$T006 = mysql_query("SELECT T006_Id,
									T006_T005_Id,
									T006_T066_Id,
									D004_Id,
									T238_Id,
									T006_Quantidade_Separacao
							   FROM T006
						  LEFT JOIN D009 ON D009_Id = T006_D009_Id
						  LEFT JOIN T066 ON T066_Id = T006_T066_ID
						  LEFT JOIN D004 ON D004_Id = T066_D004_ID
						  LEFT JOIN T238 ON T006_Id = T238_T006_Id
						      WHERE T006_T005_Id = '{$T005_Id}'");

		while($mT006 = mysql_fetch_array($T006)){

			if($mT006['T238_Id'] > 0){
				mysql_query("UPDATE T238 SET T238_T006_Id 			   = '{$mT006['T006_Id']}',
											 T238_T005_Id 			   = '{$mT006['T006_T005_Id']}',
											 T238_T066_Id 			   = '{$mT006['T006_T066_Id']}',
											 T238_D004_Id 			   = '{$mT006['D004_Id']}',
											 T238_Quantidade_Separacao = '{$mT006['T006_Quantidade_Separacao']}'	
									   WHERE T238_Id = '{$mT006['T238_Id']}'");
			} else {
				mysql_query("INSERT INTO T238 (T238_T006_Id,
											   T238_T005_Id,
											   T238_T066_Id,
											   T238_D004_Id,
											   T238_Quantidade_Separacao
									 ) VALUES ('{$mT006['T006_Id']}',
											   '{$mT006['T006_T005_Id']}',
											   '{$mT006['T006_T066_Id']}',
											   '{$mT006['D004_Id']}',
											   '{$mT006['T006_Quantidade_Separacao']}')");
			}
		}


		// habilitar somente onde precisar fazer o log (alumifix usa)
		/*
		$sql = "SELECT T006_Id, T006_Quantidade_Separacao FROM T006 where T006_T005_Id='{$T005_Id}'";
		$res2 = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res2)) {
			$sql = <<<EOT
INSERT INTO P001 
(P001_C007_Id, P001_C004_Id, P001_T006_Id, P001_Local, P001_Data_Hora, P001_T006_Quantidade_Separacao)
values
('{$g['usuarioAtual']}', '{$g['empresaAtual']}', '{$row['T006_Id']}', '{$g['r_']}', NOW(), '{$row['T006_Quantidade_Separacao']}')
EOT;
			mysql_query($sql);
		}
		*/

		if ($res === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Houve algum erro ao atualizar a quantidade. Por favor, contato o suporte técnico.";
		}
	break;

	case 'est002LimparSeparados':
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;

		mysql_query("UPDATE T238 SET T238_Quantidade_Separacao = 0 
							   WHERE T238_T005_Id='{$T005_Id}'");

		$res = mysql_query("UPDATE T006 SET T006_Quantidade_Separacao = 0 
									  WHERE T006_T005_Id='{$T005_Id}'");



		// habilitar somente onde precisar fazer o log (alumifix usa)
		/*
		$sql = "SELECT T006_Id, T006_Quantidade_Separacao FROM T006 where T006_T005_Id='{$T005_Id}'";
		$res2 = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res2)) {
			$sql = <<<EOT
INSERT INTO P001 
(P001_C007_Id, P001_C004_Id, P001_T006_Id, P001_Local, P001_Data_Hora, P001_T006_Quantidade_Separacao)
values
('{$g['usuarioAtual']}', '{$g['empresaAtual']}', '{$row['T006_Id']}', '{$g['r_']}', NOW(), '{$row['T006_Quantidade_Separacao']}')
EOT;
			mysql_query($sql);
		}
		*/

		if ($res === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Houve algum erro ao atualizar a quantidade. Por favor, contato o suporte técnico.";
		}
	break;

	case 'est002ImprimirFicha':
	break;

	case 'est002LogAlteracoesT006QuantidadeSeparacao':

		$T006_Id    = $r_T006_Id;
		$quantidade = str_replace(',', '.', $r_novoValor);
		
		$T006 = mysql_query("SELECT T006_Id,
									T006_T005_Id,
									T006_T066_Id,
									D004_Id,
									T238_Id
							   FROM T006
						  LEFT JOIN D009 ON D009_Id = T006_D009_Id
						  LEFT JOIN T066 ON T066_Id = T006_T066_ID
						  LEFT JOIN D004 ON D004_Id = T066_D004_ID
						  LEFT JOIN T238 ON T006_Id = T238_T006_Id
						      WHERE T006_Id = '{$T006_Id}'");

		$mT006 = mysql_fetch_array($T006);

		if($mT006['T238_Id'] > 0){
			mysql_query("UPDATE T238 SET T238_T006_Id 			   = '{$mT006['T006_Id']}',
										 T238_T005_Id 			   = '{$mT006['T006_T005_Id']}',
										 T238_T066_Id 			   = '{$mT006['T006_T066_Id']}',
										 T238_D004_Id 			   = '{$mT006['D004_Id']}',
										 T238_Quantidade_Separacao = '{$quantidade}'

								   WHERE T238_Id = '{$mT006['T238_Id']}'");
		} else {
			mysql_query("INSERT INTO T238 (T238_T006_Id,
										   T238_T005_Id,
										   T238_T066_Id,
										   T238_D004_Id,
										   T238_Quantidade_Separacao
								 ) VALUES ('{$mT006['T006_Id']}',
								 		   '{$mT006['T006_T005_Id']}',
										   '{$mT006['T006_T066_Id']}',
										   '{$mT006['D004_Id']}',
										   '{$quantidade}')");
		}

		// Felipe Carrano 05/09/2023
		// Alumifix não usa, verificado direto no servidor deles e personalização e está comentado 
		// Levantamento para trazer alumifix novo sistema
		
		// habilitar somente onde precisar fazer o log (alumifix usa)
		/*
		$r_T006_Id = mysql_real_escape_string($r_T006_Id);
		$r_local = mysql_real_escape_string($r_local);
		$r_valorAntigo = mysql_real_escape_string($r_valorAntigo);
		$r_novoValor = mysql_real_escape_string($r_novoValor);
		$r_novoValor = gCorrigeNumeroInverte($r_novoValor);
		$sql = <<<EOT
INSERT INTO P001
(P001_C007_Id, P001_C004_Id, P001_T006_Id, P001_Local, P001_Data_Hora, P001_T006_Quantidade_Separacao)
values
('{$g['usuarioAtual']}', '{$g['empresaAtual']}', '{$r_T006_Id}', '{$r_local}', NOW(), '{$r_novoValor}')
EOT;
		mysql_query($sql);
		*/
	break;

	case 'conferenciaCegaFim':
		$flag = base64_decode($r_flag);
		$sql_codvar = base64_decode($r_sql_codvar);
		$codvar = $r_codvar;
		$divId = $r_divId;

		$sql_codvar = str_replace('[[CODVAR]]', "{$codvar}' AND {$flag} = 'N", $sql_codvar);
		$res = mysql_query($sql_codvar);
		if (!$res || mysql_num_rows($res) == 0) {
			echo <<<EOT
				divSetarAcaoId('{$divId}', '', 'sql');
				divSetarAcaoId('{$divId}', encodeURIComponent('{$r_sql_codvar}'), 'sql_codvar');
				divRefresh('{$divId}');
				abrirJanela(false, divIdRootAberto(), '{$r_divId}', menuId('est002Conf'), '', 'Faturamento', '/est/est002/content/est002FaturamentoPedido/','&acaoId={$codvar}&T005_Id={$codvar}', [990,605]);
EOT;
		} else {
			$prods = mysql_num_rows($res) == 1 ? 'Existe 1 item que não confere.' : 'Existem ' . mysql_num_rows($res) . ' itens que não conferem.';
			$linhaVermelho = '';
			while($row = mysql_fetch_assoc($res)) {
				$linhaVermelho .= "$(\"tr[cod='{$row['D001_Codigo_Barras']}']\").css('background-color', '#FF6A6A'); ";
			}
			echo <<<EOT
				$('tr').css('background-color', '');
				{$linhaVermelho}
				var cabecalho = $('#{$divId} .cabecalho');
				dialogConfirm('Erro de Conferência', 'Não foi possível finalizar a Conferência.<br></EOT>{$prods}');
EOT;
		}
		die();
	break;

	case 'verificarPacotesPrePostagem':
		$extra = base64_decode($r_extra);
		$extra = gInsertExtraWhere($extra, "T268_Flag_Postagem = 'S' AND T268_Flag_Status = '0'");
		$sql = mysql_query("SELECT DISTINCT T268_Id FROM T268 {$extra}");
		$rows = mysql_num_rows($sql);
		if($rows > 0){
			$resposta['code'] = true;
			$resposta['data'] = ($rows == 1) ? "Deseja realizar a pré postagem do pacote selecionado?" : "Deseja realizar a pré postagem dos {$rows} pacotes selecionados?";
		} else {
			$resposta['data'] = "Nenhum pacote foi selecionado para postagem";
			$resposta['code'] = false;
		}
	break;

	case 'prePostagemPacotes':
		$extra = base64_decode($r_extra);
		$extra = gInsertExtraWhere($extra, "T268_Flag_Postagem = 'S' AND T268_Flag_Status = '0'");
		$sql = mysql_query("SELECT DISTINCT T268_Id FROM T268 {$extra}");
		$rows = mysql_num_rows($sql);
		if($rows > 0){
			$resposta['data'] = "Resultado da pré postagem:<br><br>";
			while($row = mysql_fetch_array($sql)){
				$retorno = $EST002->prePostagemPacote($row['T268_Id']);
				$resposta['data'] .= "Pacote {$row['T268_Id']}: {$retorno[1]}<br>";
			}
		} else {
			$resposta['data'] = "Nenhum pacote foi selecionado para postagem";
			$resposta['code'] = false;
		}
	break;

	case 'validacaoEnvioXMLTransportadoras':
		global $g;
		$semTransportadora = 0;
    	$transportadoraSemEmail = 0;
    	$T007Erros = "";
		$extra = base64_decode($r_extra);
  		$extra = gInsertExtraWhere($extra, "T007_Flag_Enviar_XML_Transportadora = 'S'");
		$extra = gRetirarGroupBy($extra);

        $sql = mysql_query("SELECT T007_Id,
        								T007_C007_Id,
        								T007_D022_Id,
        								D022_Id,
        								D022_Email,
        								D022_Fax,
        								T007_T005_Id,
        								T007_D024_Id
								FROM 
								T005 {$extra}");
        $rows = mysql_num_rows($sql);
		if($rows > 0){
			while($T007 = mysql_fetch_array($sql)){
				if(empty($T007['T007_D022_Id']) || ($T007['T007_D022_Id']=="0")){
	    			if (!empty($T007Erros)){
                        $T007Erros .= ', ';
                    }
	    			$T007Erros .= $T007['T007_T005_Id'];
	    			$semTransportadora++;
	    		}else if (empty($T007['D022_Fax'])){
	    			if (!empty($T007Erros)){
                        $T007Erros .= ', ';
                    }
	    			$T007Erros .= $T007['T007_T005_Id'];
	    			$transportadoraSemEmail++;
    			}
			}

			if (($semTransportadora>0) || ($transportadoraSemEmail>0)) {
	        	$resposta['code'] = false;
	        	$resposta['data'] = '<font color="red"><b>Existem pedidos selecionados sem transportadora vinculada ou com a transportadora sem e-mail preenchido.<br><br>Pedidos com erros: '.$T007Erros."</b></font>";
        	}else{
        		$resposta['code'] = true;
        	}
		} else {
			$resposta['data'] = "Nenhum pedido foi selecionado para envio.";
			$resposta['code'] = false;
		}
	break;

	case 'envioXMLEmailTransportadoraPedidosMarcados':
		global $g;		
		if ($r_inicio==1){

			$extra = base64_decode($r_extra);
      		$extra = gInsertExtraWhere($extra, "T007_Flag_Enviar_XML_Transportadora = 'S'");
			$extra = gRetirarGroupBy($extra);

	        $sqlCache = mysql_query("SELECT T007_Id,
	        								T007_C007_Id,
	        								T007_D022_Id,
	        								D022_Id,
	        								D022_Email,
	        								D022_Fax,
	        								T007_T005_Id,
	        								T007_D024_Id
									FROM 
									T005 {$extra}");
	        $erro = mysql_error();

	        if (!empty($erro)) {
	        	$resposta['code'] = false;
	        	$resposta['erros'] = 'Erro consultando itens dos pedidos: $erro';
	        }

	    	$count=0;
	    	while($rowCache = mysql_fetch_array($sqlCache)){
    			$cache[$count]['T007_Id'] = $rowCache['T007_Id'];
    			$cache[$count]['C007_Id'] = $g['usuarioAtual'];
    			$cache[$count]['T007_T005_Id'] = $rowCache['T007_T005_Id'];
    			$cache[$count]['T007_D024_Id'] = $rowCache['T007_D024_Id'];
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
			apc_delete("cacheT007EmailXML-{$g['usuarioAtual']}");
			apc_delete("cacheT007EmailXML-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT007EmailXML-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			$cache = unserialize(apc_fetch("cacheT007EmailXML-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT007EmailXML-resposta-{$g['usuarioAtual']}"));
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

    		$row['T007_Id'] = $cache[$i]['T007_Id'];
    		$row['C007_Id'] = $cache[$i]['C007_Id'];
    		$row['T007_T005_Id'] = $cache[$i]['T007_T005_Id'];
    		$row['T007_D024_Id'] = $cache[$i]['T007_D024_Id'];

    		$retorno = $EST002->enviarXMLEmailTransportadora($row['T007_Id'],$row['C007_Id'],$row['T007_D024_Id']);
    		// $retorno = true;

			if($retorno===true){
				$ok++;
			}else{
				$erros .= "Pedido - {$row['T007_T005_Id']} - Erro : {$retorno}</br>";
				$erro++;
			}    	
        }

		$resposta['data'] = '';        
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){
			apc_delete("cacheT007EmailXML-{$g['usuarioAtual']}");
			apc_delete("cacheT007EmailXML-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT007EmailXML-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT007EmailXML-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}
	break;

	case 'retornarPedidoColetado':
			$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
			$T005_Flag_Status = isset($_REQUEST['T005_Flag_Status']) ? $_REQUEST['T005_Flag_Status'] : 0;
			$verifica = $VEN012->retornarPedidoColetado($T005_Id, $T005_Flag_Status);

			if ($verifica !== true) {
				$resposta['code'] = false;
				$resposta['data'] = $verifica;
			}
		break;

	// Checa quantos pedidos estão selecionados
	case 'checaPedidosSelecionados':

		// Da um decode nas duas informações de query
		$extra = base64_decode($r_ajaxExtra);

		// Query que seleciona os pedidos com os filtros do grid
		$wrap = "SELECT T005_Id, T005_Flag_Marcado FROM T005 $extra";

		//Pega a contagem de pedidos selecionados na tela do grid
		$contagem = mysql_query("SELECT COUNT(*) as 'CONTAGEM' FROM ($wrap) src WHERE T005_Flag_Marcado = 'S'");
		$contagem = mysql_fetch_assoc($contagem)['CONTAGEM'];
		$resposta['data']['contagem'] = $contagem;

		$erro = mysql_error();
		if($erro){log($erro);};
		
		if($contagem > 0)
		{	
			// Pega os Id's dos pedidos
			$pedidos = mysql_query("SELECT T005_Id FROM ($wrap) src WHERE T005_Flag_Marcado = 'S'");
			$pedidos = mysqli_fetch_all($pedidos);

			$erro = mysql_error();
			if($erro){log($erro);};
	
			// Transforma numa string e encoda em 64
			$pedidos = base64_encode(implode(",", array_column($pedidos, 0)));

			// Salva para retornar no JSON
			$resposta['data']['pedidos'] = $pedidos;
		}
		break;

	// Checa quantos pedidos estão selecionados
	case 'enviarXMLEmailTransportadora':

		$T007_Id = $r_T007_Id;

		$retProc = $EST002->enviarXMLEmailTransportadoraUsrRoot($T007_Id);

		if ($retProc === true) {
			$resposta['sucesso'] = true;
			$resposta['mensagem'] = 'Xml enviado';
		} else {
			$resposta['sucesso'] = true;
			$resposta['mensagem'] = $retProc;
		}

		break;

        case 'gravarT313':
            $dados = array('T313_Id'     	     => isset($r_T313_Id)             ? $r_T313_Id     : '',
                           'T005_Id'     	     => isset($r_T005_Id)             ? $r_T005_Id     : '',
                           'T006_Id'     	     => isset($r_T006_Id)             ? $r_T006_Id     : '',
                           'D009_Id'     	     => isset($r_D009_Id)             ? $r_D009_Id     : '',
                           'ValorAntigo' 	     => isset($r_valorAntigo)         ? $r_valorAntigo : '',
                           'Quantidade'          => isset($r_novoValor)           ? $r_novoValor   : '',
						   'ValorAntigoLote'     => isset($r_valorAntigoLote)     ? $r_valorAntigoLote : '',
                           'T313_Lote'  	     => isset($r_novoValorLote)       ? $r_novoValorLote  : '',
						   'novoValorNrVolume'   => isset($r_novoValorNrVolume)   ? $r_novoValorNrVolume : '',
                           'valorAntigoNrVolume' => isset($r_valorAntigoNrVolume) ? $r_valorAntigoNrVolume  : '');
			   
			// $qtde     = floatval(isset($r_novoValor) ? $r_novoValor   : '');
			// $nrVolume = isset($r_novoValorNrVolume)  ? $r_novoValorNrVolume : '';
			// if($qtde%$nrVolume != 0){
			// 	return "<div class='msgError'>Erro: Não é possível criar os volumes com essa quantidade.</div>";
			// }
            $retorno = $EST002->gravarQuantidadeConferida($dados);
            if($retorno === true){
                $resposta['code'] = true;
            } else {
                $resposta['code'] = false;
                $resposta['data'] = $retorno;
            }
        break;

        case 'est002VolumesConferenciaGerarVolume':
            $T005_Id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;

            $retorno = $EST002->volumesConferenciaGerar($T005_Id);
            if($retorno === true){
                $resposta['code'] = true;
            } else {
                $resposta['code'] = false;
                $resposta['data'] = $retorno;
            }
        break;

		case 'est002VolumesParcialConferenciaGerarVolume':
				
			$T005_Id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;

			$retorno = $EST002->volumesParcialConferenciaGerar($T005_Id);
				
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
            
        break;

        case 'est002VolumesConferenciaValidar':
            $T005_Id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;

            $resposta = $EST002->est002VolumesConferenciaValidar($T005_Id);
        break;

        case 'est002VolumesConferenciaFinalizar':
            $T005_Id = isset($_REQUEST['acaoId'])  ? $_REQUEST['acaoId']  : '0';
            $D047_Id = isset($_REQUEST['D047_Id']) ? $_REQUEST['D047_Id'] : '0';

            $retorno = $EST002->volumesConferenciaFinalizar($T005_Id, $D047_Id);
            if($retorno === true){
                $resposta['code'] = true;
            } else {
                $resposta['code'] = false;
                $resposta['data'] = $retorno;
            }
        break;

        case 'est002VolumesConferenciaReiniciarConferencia':
            $T005_Id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;

            $retorno = $EST002->volumesConferenciaReiniciarConferencia($T005_Id);
            if($retorno === true){
                $resposta['code'] = true;
            } else {
                $resposta['code'] = false;
                $resposta['data'] = $retorno;
            }
        break;

        case 'est002VolumesConferenciaReiniciarSeparacao':
            $T005_Id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;

            $retorno = $EST002->volumesConferenciaReiniciarSeparacao($T005_Id);
            if($retorno === true){
                $resposta['code'] = true;
            } else {
                $resposta['code'] = false;
                $resposta['data'] = $retorno;
            }
        break;

        case 'est002ExcluirVolume':
            $T314_Id = isset($_REQUEST['T314_Id']) ? $_REQUEST['T314_Id'] : '';

            $retorno = $EST002->volumesConferenciaCancelar($T314_Id);
            if($retorno === true){
                $resposta['code'] = true;
            } else {
                $resposta['code'] = false;
                $resposta['data'] = $retorno;
            }
        break;

		case 'atualizaT313_D109_Id':
			global $g;

			$D109_Id = isset($_REQUEST['D109_Id']) ? $_REQUEST['D109_Id'] : false;
			$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
			$T006_Id = isset($_REQUEST['T006_Id']) ? $_REQUEST['T006_Id'] : false;
			$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
			$Campo   = isset($_REQUEST['Campo'])   ? $_REQUEST['Campo']   : false;
			$C007_Id = $g['usuarioAtual'];

			$sql  = " SELECT T313_Id ";
			$sql .=   " FROM T313 ";
			$sql .=  " WHERE T313_T005_Id = '{$T005_Id}' ";
			$sql .=    " AND T313_T006_Id = '{$T006_Id}' ";
			$sql .=    " AND T313_D009_Id = '{$D009_Id}' ";
			$query = mysql_query($sql);
			$dados = mysql_fetch_array($query);

			if ($dados['T313_Id'] > 0) {
				mysql_query("UPDATE T313 SET {$Campo} = '{$D109_Id}' WHERE T313_Id = '{$dados['T313_Id']}'");
			} else {
				mysql_query("INSERT INTO T313 (T313_T005_Id, T313_T006_Id, T313_D009_Id, T313_C007_Id, {$Campo}) VALUES ('{$T005_Id}', '{$T006_Id}', '{$D009_Id}', '{$C007_Id}', '{$D109_Id}')");
			}

			$resposta['data'] = true;
		break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
