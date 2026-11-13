<?php
namespace hardness;
require_once("old/lib/funcoes.php");
/**
 * Ações
 */
switch($g['r'][3]){
	case 'crm001InfoCliente':
		
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		$sqlD024 = "SELECT * FROM D024 LEFT JOIN D020 on D020_Id=D024_D020_Id 
									   LEFT JOIN D018 on D018_Id=D024_D018_Id 
									   LEFT JOIN C007 on C007_Id=D024_C007_Vendedor_Interno 
									   WHERE D024_Id='{$D024_Id}'";
		$resultD024 = mysql_query($sqlD024);
		$linhaD024= mysql_fetch_array($resultD024);
		if(!empty($linhaD024['D024_Cnpj'])){
			$cnpjCpf = (!empty($linhaD024['D024_Cnpj'])) ? "<span style=\"font-weight:normal; font-size:10px;\">CNPJ</span><br /><span style='font-weight:normal; font-size:11px;'>{$linhaD024['D024_Cnpj']}" : "<span style=\"font-weight:normal; font-size:11px;\">CNPJ</span><br /><span style='color: var(--BGVermelho);'>FALTA</span>";
		} else {
			$cnpjCpf = (!empty($linhaD024['D024_Cpf'])) ? "<span style=\"font-weight:normal; font-size:10px;\">CPF</span><br /><span style='font-weight:normal; font-size:11px;'>{$linhaD024['D024_Cpf']}" : "<span style=\"font-weight:normal; font-size:10px;\">CPF</span><br /><span style='color: var(--BGVermelho);'>FALTA</span>";
		}
		$linhaD024['D024_Nome_Contato'] = substr($linhaD024['D024_Nome_Contato'], 0, 15);

		$D024_IE 			= (!empty($linhaD024['D024_Inscricao_Estadual'])) ? $linhaD024['D024_Inscricao_Estadual'] : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$D024_Email 		= (!empty($linhaD024['D024_E_Mail'])) 			  ? substr($linhaD024['D024_E_Mail'], 0, 30)  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$D024_DDD			= (!empty($linhaD024['D024_DDD_Telefone_1'])) 	  ? $linhaD024['D024_DDD_Telefone_1'] 	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$D024_Telefone		= (!empty($linhaD024['D024_Telefone_1']))   	  ? $linhaD024['D024_Telefone_1']   	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$C007_Primeiro_Nome = (!empty($linhaD024['C007_Primeiro_Nome']))   	  ? $linhaD024['C007_Primeiro_Nome']   	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$Contato			= (!empty($linhaD024['D024_Nome_Contato']))   	  ? $linhaD024['D024_Nome_Contato']   	  : "<span style='color: var(--BGVermelho);'>FALTA</span>"; 
		$DDD_FAX			= (!empty($linhaD024['D024_DDD_Fax']))   	  	  ? $linhaD024['D024_DDD_Fax']   	  	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$FAX				= (!empty($linhaD024['D024_Fax']))   	  	  	  ? $linhaD024['D024_Fax']   	  	  	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$bairro				= (!empty($linhaD024['D024_Bairro']))   	  	  ? $linhaD024['D024_Bairro']   	  	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		
		$nomeEmpresa = substr($linhaD024['D024_Nome_Empresa'], 0, 17);
		$nomeFantasia = substr($linhaD024['D024_Nome_Fantasia'], 0, 18);
		$endereco = substr($linhaD024['D024_Endereco'], 0, 12);
		//$bairro = substr($bairro, 0, 12);

		$retorno = <<<EOT
		<style type="text/css">
		.containerTop{
			width: 99%;
			height: 102px;
			float:left;
			margin-left: 5px;
			padding:0px;
		}
		.containerTop table{
			width: 100%;
			height: 100px;
			float: left;
		}
		</style>
		<div class="containerTop ui-state-default ui-corner-all">
			<div>
				<table>
					<tr>
EOT;
						$retorno .= <<<EOT
						<td>
							<table>
								<tr class="dadosEmpresa">
									<td style="cursor: pointer;border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Razão Social</span><br/><span style="font-weight:bold; font-size:11px;">$nomeEmpresa</td>
								</tr>
								<tr>
									<td style="border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Endereço</span><br /><span style="font-weight:normal; font-size:11px;">$endereco - Nº{$linhaD024['D024_Numero']}</td>
								</tr>
								<tr>
									<td style="border-bottom:0px;"><span style="font-weight:normal; font-size:10px;">Bairro</span><br /><span style="font-weight:normal; font-size:11px;">{$bairro}</td>
								</tr>
							</table>
						</td>
						<td>
							<table>
								<tr>
									<td style="border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Nome Fantasia</span><br /><span style="font-weight:normal; font-size:11px;">$nomeFantasia</td>
								</tr>
								<tr>
									<td style="border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Cidade/UF</span><br /><span style="font-weight:normal; font-size:11px;">{$linhaD024['D020_Nome_Cidade']}/{$linhaD024['D018_UF']}</td>
								</tr>
								<tr>
									<td style="border-bottom:0px;"><span style="font-weight:normal; font-size:10px;">Vendedor</span><br /><span style="font-weight:normal; font-size:11px;">{$C007_Primeiro_Nome}</td>
								</tr>
							</table>
						</td>
						<td>
							<table>
								<tr>
									<td style="border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Telefone</span><br /><span style="font-weight:bold; font-size:11px;">{$D024_DDD}-{$D024_Telefone}</td>
								</tr>
								<tr>
									<td style="border-bottom:1px solid; border-color: #ACACAC;">{$cnpjCpf}</td>
								</tr>
								<tr>
									<td style="border-bottom:0px"><span style="font-weight:normal; font-size:10px;">IE</span><br /><span style="font-weight:normal; font-size:11px;">{$D024_IE}</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
EOT;
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('.dadosEmpresa').unbind('click').bind('click', function(data) {
				var id = '{$r_D024_Id}';
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cliente - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id), [960,583]);
			});
		} </script></div>
EOT;
		echo $retorno;
	break;
	
	case 'crm001DadosFinanceiro':
		$xLast12 = date('Y-m-d');
		$xLast12 = new \DateTime($xLast12);
		$xLast12->modify('-12 months');
		$Data_Inicial = $xLast12->format('Y-m-d');
	
		$D024_Id = $r_D024_Id;
		$dadosCliente = mysql_query("SELECT D024_Data_Cadastro, D024_Valor_Limite, D024_Valor_Credito_Antecipado, D024_Valor_Credito_Troca, D024_Cnpj_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
		$dadosCliente = mysql_fetch_array($dadosCliente);

		$filtroSaldo = "D024_Id = '{$D024_Id}'";

		//calcula a soma dos limites das empresas vinculadas a um Cnpj Matriz
		if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($dadosCliente['D024_Cnpj_Matriz']))){
			$filtroSaldo = "D024_Cnpj_Matriz = '{$dadosCliente['D024_Cnpj_Matriz']}'";
			$dadosLimite = mysql_query("SELECT SUM(D024_Valor_Limite) AS D024_Valor_Limite, 
										SUM(D024_Valor_Credito_Antecipado) AS D024_Valor_Credito_Antecipado, 
										SUM(D024_Valor_Credito_Troca) AS D024_Valor_Credito_Troca 
										FROM D024 
										WHERE $filtroSaldo");
			$dadosLimite = mysql_fetch_array($dadosLimite);
			$dadosCliente['D024_Valor_Limite'] = $dadosLimite['D024_Valor_Limite'];
			$dadosCliente['D024_Valor_Credito_Antecipado'] = $dadosCreditoAntecipado['D024_Valor_Credito_Antecipado'];
			$dadosCliente['D024_Valor_Credito_Troca'] = $dadosCreditoTroca['D024_Valor_Credito_Troca'];
			
		}

		$valorLimite = $dadosCliente['D024_Valor_Limite'];
		//Maior Compra
		$maiorCompra = mysql_query("SELECT T007_Valor_Total, T007_Data_Emissao 
										FROM T007
										WHERE T007_D024_Id = '{$D024_Id}'
										AND T007_Data_Emissao >= '{$Data_Inicial}'
										AND T007_Flag_Cancelada='N'
										ORDER BY T007_Valor_Total DESC,
										T007_Data_Emissao DESC
										LIMIT 1");
		$maiorCompra = mysql_fetch_array($maiorCompra);
		//Ultima Compra
		$ultimaCompra = mysql_query("SELECT T007_Data_Emissao
										FROM T007
										WHERE T007_D024_Id = '{$D024_Id}'
										AND T007_Data_Emissao >= '{$Data_Inicial}'
										AND T007_Flag_Cancelada!='S'
										AND T007_Valor_Total>0
										ORDER BY T007_Data_Emissao DESC
										LIMIT 1");
		$ultimaCompra = mysql_fetch_array($ultimaCompra);
		
		$mesesComprados = mysql_query("SELECT T007_Id
										FROM T007
										LEFT JOIN D006 ON D006_Id=T007_D006_Id
										WHERE T007_D024_Id = '{$D024_Id}'
										AND T007_Flag_Cancelada!='S'
										AND D006_Flag_Entrada_Saida='S'
										AND D006_Flag_Venda_Compra_Outros='V'
										AND T007_Valor_Total>0
										GROUP BY MONTH(T007_Data_Emissao)
										LIMIT 12");
		$mesesComprados = mysql_num_rows($mesesComprados);
		
		$mediaVendasUlt12Meses = mysql_query("SELECT (SUM(T007_Valor_Total)/if({$mesesComprados}>12,12,{$mesesComprados})) as mediaVendas
										FROM T007
										LEFT JOIN D006 ON D006_Id=T007_D006_Id
										WHERE T007_D024_Id = '{$D024_Id}'
										AND T007_Data_Emissao >= '{$Data_Inicial}'
										AND T007_Flag_Cancelada!='S'
										AND D006_Flag_Entrada_Saida='S'
										AND D006_Flag_Venda_Compra_Outros='V'
										AND T007_Valor_Total>0");
		$mediaVendasUlt12Meses = mysql_fetch_array($mediaVendasUlt12Meses);
										
		//Maior Atraso
		$maiorAtraso = mysql_query("SELECT DATEDIFF(T002_Data_Recebimento,T002_Data_Vencimento)
										FROM T002
										WHERE T002_Data_Recebimento>T002_Data_Vencimento
										AND T002_D024_Id = '{$D024_Id}'
										AND T002_Data_Emissao >= '{$Data_Inicial}'
										AND T002_Flag_Cancelada='N'
										AND (T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL)
										ORDER BY DATEDIFF(T002_Data_Recebimento,T002_Data_Vencimento) DESC 
										LIMIT 1");
		$maiorAtraso = mysql_fetch_array($maiorAtraso);
		//Total a pagar
		$totalPagar = mysql_query("SELECT SUM(T002_Valor_Saldo)
									FROM T002
									WHERE T002_Data_Recebimento='0000-00-00'
									AND T002_Data_Emissao >= '{$Data_Inicial}'
									AND T002_D024_Id = '{$D024_Id}'
									AND (T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL)
									AND T002_Flag_Cancelada='N'");
		$totalPagar = mysql_fetch_array($totalPagar);
		//Total Atrasado
		$totalAtrasado = mysql_query("SELECT SUM(T002_Valor_Saldo)
										FROM T002
										WHERE CURRENT_DATE()>T002_Data_Vencimento
										AND T002_Data_Recebimento='0000-00-00'
										AND T002_Data_Emissao >= '{$Data_Inicial}'
										AND T002_D024_Id = '{$D024_Id}'
										AND (T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL)
										AND T002_Flag_Cancelada='N'");
		$totalAtrasado = mysql_fetch_array($totalAtrasado);
		
		$saldo = mysql_query("SELECT SUM(if(T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Vencer,
									 SUM(if(T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Vencido
									 FROM T002 
									 LEFT JOIN D024 ON D024_Id = T002_D024_Id
									 WHERE $filtroSaldo
									 AND T002_Data_Recebimento='0000-00-00'
									 AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)");
		$saldo = mysql_fetch_array($saldo);
		
		$pedidosAbertos = mysql_query("SELECT SUM(T005_Valor_Total) as Pedidos
											FROM T005
											LEFT JOIN T007 on T007_T005_Id=T005_Id
											LEFT JOIN D024 on D024_Id=T005_D024_Id
											WHERE $filtroSaldo
											AND T005_Flag_Status !=8
											AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
											AND T007_Id is null");
		$pedidosAbertos = mysql_fetch_array($pedidosAbertos);
		
		$vencido = $saldo['Vencido'];
		$vencer = $saldo['Vencer'];
		$pedidos = $pedidosAbertos['Pedidos'];
		$dataCadastro = gCorrigeData($dadosCliente['D024_Data_Cadastro']);
		$dataCadastro = substr($dataCadastro, 0, 10);
		
		$maiorCompra = gCorrigeNumero($maiorCompra[0]);
		
		if(empty($ultimaCompra)){
			$ultimaCompra = '-';
		} else {
			$ultimaCompra = gCorrigeData($ultimaCompra[0]);
		}
		$totalPagar = gCorrigeNumero($totalPagar[0]);
		$totalAtrasado = gCorrigeNumero($totalAtrasado[0]);
		$mediaVendasUlt12Meses = gCorrigeNumero($mediaVendasUlt12Meses['mediaVendas']);		
		$retorno = $valorLimite-($vencido+$vencer+$pedidos);
		log("retorno = ". $pedidos);
		
		if ($retorno<0) {
			$saldoLimite = '<span style="color: var(--BGVermelho);">'.number_format($retorno,2,',','.').'</span>';
		} else {
			$saldoLimite = number_format($retorno,2,',','.');
		}
		
		$valorLimite = gCorrigeNumero($valorLimite);

		$valorCreditoAntecipado = gCorrigeNumero($dadosCliente['D024_Valor_Credito_Antecipado']);
		$valorCreditoTroca = gCorrigeNumero($dadosCliente['D024_Valor_Credito_Troca']);

		$retorno = <<<EOT
		<style type="text/css">
		.containerFinanceiro {
			width: 99%;
			height: 102px;
			float:left;
			margin-left: 5px;
		}
		
		.containerFinanceiro table {
			align: center;
			width: 47.7%;
			height: 100%;
			float: left;
			margin-left: 5px;
			padding-right: 5px;
		}
		</style>
			<div class="containerFinanceiro ui-state-default ui-corner-all">
				<table width=50px>
					<tr>
						<td>Cadastro</td>
						<td><span style="font-weight:normal; font-size:11px;">{$dataCadastro}</td>
					</tr>
					<tr>
						<td>Maior compra</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ {$maiorCompra}</td>
					</tr>
					<tr>
						<td>Última compra</td>
						<td><span style="font-weight:normal; font-size:11px;">{$ultimaCompra}</td>
					</tr>
					<tr>
						<td>Maior atraso</td>
						<td><span style="font-weight:normal; font-size:11px;">{$maiorAtraso[0]} dias</td>
					</tr>
					<tr>
						<td style="border-bottom: none;">Freq.venda</td>
						<td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">{$mesesComprados}</td>
					</tr>
					<tr>
						<td style="border-bottom: none;">Média mês</td>
						<td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">R$ {$mediaVendasUlt12Meses}</td>
					</tr>
				</table>
				<table width=50px>
					<tr>
						<td>À Pagar</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ {$totalPagar}</td>
					</tr>
					<tr>
						<td>Atrasado</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ {$totalAtrasado}</td>
					</tr>
					<tr>
						<td>Limite</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ {$valorLimite}</td>
					</tr>
					<tr>
						<td>Saldo</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ $saldoLimite</td>
					</tr>
					<tr>
						<td style="border-bottom: none;">Crédito Antecipado</td>
						<td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">R$ {$valorCreditoAntecipado}</td>
					</tr>                    
					<tr>
						<td style="border-bottom: none;">Crédito Troca</td>
						<td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">R$ {$valorCreditoTroca}</td>
					</tr>
				</table>
			</div>		
EOT;
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('.containerFinanceiro').unbind('click').bind('click', function(data) {
					nova_janela('/hardness3/hardness/clientes/referencias_comerciais.php?D024_Id={$D024_Id}&Abre_Janela=Nao&Voltar=N',0,0,800,600,'no','no','no','no','yes','yes',0)});
							
		} </script></div>
EOT;
		echo $retorno;
	break;
	
	case 'crm001Observacoes':
		global $g;
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		$sqlD024 = "SELECT * FROM D024 LEFT JOIN D020 on D020_Id=D024_D020_Id 
									   LEFT JOIN D018 on D018_Id=D024_D018_Id 
									   LEFT JOIN C007 on C007_Id=D024_C007_Vendedor_Interno 
									   WHERE D024_Id='{$D024_Id}'";
		$resultD024 = mysql_query($sqlD024);
		$linhaD024= mysql_fetch_array($resultD024);
		
		$obsComercial = substr($linhaD024['D024_Observacao_Comercial'], 0, 120);
		$obsFinanceiro = substr($linhaD024['D024_Observacao_Financeiro'], 0, 120);
		$D024_Cnpj_Matriz = $linhaD024['D024_Cnpj_Matriz'];
		log("cnpj" . $D024_Cnpj_Matriz);

		$height = "49%";

		if(!(isset($g['c029Ids'][217])) && (isset($g['c029Ids'][218]))){
			$height="100%";
		}

		echo <<<EOT
		<style type="text/css">
		.observacoes{
			width: 99%;
			height: 102px;
			float:left;
			margin-left:8px;
		}
		.observacoes .comercial{
			height:{$height};
			padding:1px;
		}
		.observacoes .financeiro{
			height: {$height};
			padding:1px;
			border-top:none;
		}
		</style>

EOT;

	if((isset($g['c029Ids'][217])) && (isset($g['c029Ids'][218]))){
	    echo<<<EOT
        	<div class="observacoes  ui-state-default ui-corner-all">
        	&nbsp;
        	</div>
EOT;
	}
	if(!(isset($g['c029Ids'][217])) || !(isset($g['c029Ids'][218]))){
	    echo<<<EOT
        	<div class="observacoes  ui-state-default ui-corner-all">
EOT;
	}


  	if(!(isset($g['c029Ids'][217]))){
        echo<<<EOT
			<div class="comercial">
				<b>Observação comercial:</b><br />
				<span style="font-weight:normal;color: var(--BGVermelho); font-size:11px;">$obsComercial</span>
			</div>
EOT;
	}

 	if(!(isset($g['c029Ids'][218])) && (empty($linhaD024['D024_Cnpj_Matriz']))){
        echo<<<EOT
			<div class="financeiro">
				<b>Observação financeiro:</b><br />
				<span style="color: var(--BGVermelho);"><span style="font-weight:normal; font-size:11px;" title='{$linhaD024['D024_Observacao_Financeiro']}'>$obsFinanceiro</span>
			</div>
EOT;
	}

	if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($linhaD024['D024_Cnpj_Matriz']))){
        echo<<<EOT
			<div class="financeiro">
				<b>Observação financeiro:</b><br/>
				<span style="color: blue;"><span style="font-weight:normal; font-size:11px;">Possui CNPJ Matriz: $D024_Cnpj_Matriz</br></span>
				<span style="color: var(--BGVermelho);"><span style="font-weight:normal; font-size:11px;"title='{$linhaD024['D024_Observacao_Financeiro']}'>$obsFinanceiro</span>
			</div>
EOT;
	}
  	echo<<<EOT
		</div>
EOT;

	break;

	case 'crm001ImagemOriginal':
        $urlImagem = (isset($r_urlImagem)) ? $r_urlImagem : false;
        $urlImagem = urldecode($urlImagem);

        $D001_Id = (isset($r_D001_Id)) ? $r_D001_Id : false;
        $D001_Id = urldecode($D001_Id);

        $T172 = mysql_query("SELECT T172_Id, T172_D001_Id, T172_Nome_Arquivo,T172_Link_Video FROM T172 WHERE T172_D001_Id='{$D001_Id}' order by T172_Nome_Arquivo DESC");

        if (mysql_num_rows($T172) < 2) {
            $mT172 = mysql_fetch_array($T172);
            if (!empty($mT172['T172_Link_Video'])) {
                echo <<<EOT
                <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                    <iframe width='832' height='450' src='{$mT172['T172_Link_Video']}' frameborder='0' allowfullscreen></iframe>
                </div>                  
EOT;
            } else {
                echo <<<EOT
                <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                    <img src='{$urlImagem}'style='max-width: 100%; max-height: 100%;'/>
                </div>                  
EOT;
            }

            return;
        }

        $imagens = '';
        while ($mT172 = mysql_fetch_array($T172)) {
            $idFoto    = $mT172['T172_Id'];
            $idProduto = $mT172['T172_D001_Id'];
            $pathinfo  = pathinfo($mT172['T172_Nome_Arquivo']);

            $dir = $g['pathRaiz'] . "dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
            $dirFull = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";

            if (!file_exists($dir)) {
                $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
            }

            // Verifica se é um vídeo
            if (!empty($mT172['T172_Link_Video'])) {
                $imagens .= "<i class='material-icons video-icon' onclick=\"document.getElementById('display-image').innerHTML = '<iframe width=\'832\' height=\'450\' src=\'{$mT172['T172_Link_Video']}\' frameborder=\'0\' allowfullscreen></iframe>';\">smart_display</i>";
            } else {
                $imagens .= "<img src='{$dirFull}' class='miniatura' onclick='changeImage(this)' />";
            }
        }

        echo <<<EOT
		<style>
		.container {
			margin-top: 10px;
			display: flex;
			flex-direction: row;
			overflow: hidden;
		}
	
		#display-image img {
			max-width: 90%;
			max-height: 90%;
			position: absolute; /* Define posição absoluta para a imagem grande */
		}
	
		.col1 {
			flex: 80%;
			margin-left: 10px;
		}
	
		.col2 {
			flex: 20%;
			display: flex;
			flex-wrap: wrap;
			justify-content: flex-start;
			align-items: center;
			margin-bottom: 5px;
		}
	
		.miniatura {
			cursor: pointer;
			max-width: 150px;
			max-height: 150px;
			margin: 8px; /* Margem uniforme */
			transition: transform 0.2s ease-in-out;
		}
	
		.miniatura:hover {
			transform: scale(1.1);
		}
		
		.video-icon {
			font-size: 180px;
			cursor: pointer;
			color: red;
			margin: -22px -10px -10px -10px; 
			margin-bottom: 5px; /* Adiciona margem inferior apenas para o ícone de vídeo */

		}
		.video-icon:hover {
			transform: scale(1.1);
		}
	</style>
	
	<div class="container">
		<div class="col1">
			<div id="display-image">
				<img src='{$urlImagem}' />
			</div>
		</div>
		<div class="col2">
			{$imagens}
		</div>
	</div>
	
	<script>
		function changeImage(img) {
			var display = document.getElementById("display-image").getElementsByTagName("img")[0];
			if (display) {
				display.src = img.src;
				resetImageSize(display); // Redefinir o tamanho da imagem após alterar
			} else {
				document.getElementById("display-image").innerHTML = "<img src='" + img.src + "' style='max-width: 100%; max-height: 100%;'/>";
				resetImageSize(document.getElementById("display-image").getElementsByTagName("img")[0]); // Redefinir o tamanho da imagem após inserir
			}
		}

		function resetImageSize(img) {
			img.style.maxWidth = "90%";
			img.style.maxHeight = "90%";
		}


	</script>
EOT;
	break;
	
	case 'xxxcrm001ImagemOriginal':
		$urlImagem = (isset($r_urlImagem)) ? $r_urlImagem : false;
		$urlImagem = urldecode($urlImagem);

		$D001_Id = (isset($r_D001_Id)) ? $r_D001_Id : false;
		$D001_Id = urldecode($D001_Id);		

		$T172 = mysql_query("SELECT T172_Id, T172_D001_Id, T172_Nome_Arquivo FROM T172 WHERE T172_D001_Id='{$D001_Id}'");		

		if(mysql_num_rows($T172) < 2) {
			echo <<<EOT
				<div style="display: flex; align-items: center; justify-content: center; height: 100%;">
					<img src='{$urlImagem}'/>
				</div>				  
			EOT;
			return;
		}

		$imagens = '';
		while($mT172 = mysql_fetch_array($T172)) {
			$idFoto = $mT172['T172_Id'];
            $idProduto = $mT172['T172_D001_Id'];
            $pathinfo = pathinfo($mT172['T172_Nome_Arquivo']);            
            
            $dir = $g['pathRaiz'] . "dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
            $dirFull = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";

            if (!file_exists($dir)){                
                $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
            }

			$imagens .= "<img src='{$dirFull}'  onclick='changeImage(this)'/>";
		}
		

		echo <<<EOT
			<style>
				.col1 {        
					flex: 80%;	
					margin-left: 10px;	
					justi			
				}      
			
				.col2 {                
					flex:20%;					
					margin-bottom: 10px;     
					display: flex;           
					flex-direction: column;        					
					align-items: flex-end;
				}      
				
				.col2 > img {
					max-width: 200px;
					max-height: 200px;    
					margin:0 10px 10px 0;					
				}
			
				.container  {					
					margin-top: 10px;
					display: flex;
					flex-direction: row;        
				}
			</style>
			<div class="container"> 				
				<div class="col1">
					<div id="display-image">		
						<img src='{$urlImagem}'/>			
					</div>
				</div>
				<div class="col2">
					{$imagens}
				</div>
			</div>
			<script>
				function changeImage(img) {
					var display = document.getElementById("display-image");
					display.innerHTML = "<img src='" + img.src + "'/>";
				}
			</script>
		EOT;
	break;

	case 'cabecalhoPedido':
		// Select
		$sql = <<<EOT
		SELECT 
			D001_Codigo_Produto,
			D001_Descricao_Produto,
			D009_Quantidade_Estoque_Liquido,
			D009_Quantidade_Estoque_Loja,
			D009_Quantidade_Estoque_Fora,
			D001_Id
		FROM D009
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			where D009_Id='{$r_acaoId}'
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		//onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Cliente/Fornecedor - ID: ' + '{$row['D024_Id']}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', [930,583]);"
		$urlCadastroCliente = <<<EOT
			onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$row['D001_Id']}') + '&tabela=D001', [1000,583]);"
EOT;

		$row['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
		$row['D009_Quantidade_Estoque_Loja'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Loja']);
		$row['D001_Descricao_Produto'] = gTruncar($row['D001_Descricao_Produto'], 45);

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc" style="cursor:pointer;" {$urlCadastroCliente}>
						<td width='15%'>Código</td>
						<td width='48%'>Descrição</td>
						<td width='13%'>Estoque Líquido</td>
						<td width='13%'>Estoque Loja</td>
						<td width='13%'>Estoque Fora</td>
					</tr>
					<tr class="linhaVal" style="cursor:pointer;" {$urlCadastroCliente}>
						<td align='center'>{$row['D001_Codigo_Produto']}</td>
						<td>{$row['D001_Descricao_Produto']}</td>
						<td>{$row['D009_Quantidade_Estoque_Liquido']}</td>
						<td>{$row['D009_Quantidade_Estoque_Loja']}</td>
						<td>{$row['D009_Quantidade_Estoque_Fora']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'crm001Resumo':
		// Mostra informações gerênciais do CRM

		// recarregar a tela automaticamente      
		echo <<<EOT
			<div>
				<script type="text/javascript">
					setTimeout(function(){
						divRefresh('{$g['divId']}');
					}, 30000);
				</script>
EOT;

		// Total de pedidos ativos (em aberto)
		$qT005Ativos = mysql_query("select count(*) quantidade,
										   sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where (T005_Flag_Status=0
									 or T005_Flag_Status=14
									 or T005_Flag_Status=15
									 or T005_Flag_Status=13
									 or T005_Flag_Status=6
									 or T005_Flag_Status=1
									 or T005_Flag_Status=2
									 or T005_Flag_Status=3
									 or T005_Flag_Status=10)
									 and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			 and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005Ativos = mysql_fetch_array($qT005Ativos);
		$T005AtivosQuantidade = $mT005Ativos['quantidade'];
		$T005AtivosValor = gCorrigeNumero($mT005Ativos['valor']);

		// Total de pedidos pendentes
		$qT005Pendente = mysql_query("select count(*) quantidade,
											 sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=0
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005Pendente = mysql_fetch_array($qT005Pendente);
		$T005PendenteQuantidade = $mT005Pendente['quantidade'];
		$T005PendenteValor = gCorrigeNumero($mT005Pendente['valor']);

		// Total de pedidos aprovados
		$qT005Aprovado = mysql_query("select count(*) quantidade,
											 sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=14
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005Aprovado = mysql_fetch_array($qT005Aprovado);
		$T005AprovadoQuantidade = $mT005Aprovado['quantidade'];
		$T005AprovadoValor = gCorrigeNumero($mT005Aprovado['valor']);

		// Total de pedidos negados
		$qT005Negado = mysql_query("select count(*) quantidade,
										   sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=15
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005Negado = mysql_fetch_array($qT005Negado);
		$T005NegadoQuantidade = $mT005Negado['quantidade'];
		$T005NegadoValor = gCorrigeNumero($mT005Negado['valor']);

		// Total de pedidos em análise comercial
		$qT005AnaliseComercial = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=13
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005AnaliseComercial = mysql_fetch_array($qT005AnaliseComercial);
		$T005AnaliseComercialQuantidade = $mT005AnaliseComercial['quantidade'];
		$T005AnaliseComercialValor = gCorrigeNumero($mT005AnaliseComercial['valor']);

		// Total de pedidos em análise comercial
		$qT005AnaliseFinanceiro = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=6
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005AnaliseFinanceiro = mysql_fetch_array($qT005AnaliseFinanceiro);
		$T005AnaliseFinanceiroQuantidade = $mT005AnaliseFinanceiro['quantidade'];
		$T005AnaliseFinanceiroValor = gCorrigeNumero($mT005AnaliseFinanceiro['valor']);

		// Total de pedidos para impressão
		$qT005Imprimir = mysql_query("select count(*) quantidade,
											 sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=1
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005Imprimir = mysql_fetch_array($qT005Imprimir);
		$T005ImprimirQuantidade = $mT005Imprimir['quantidade'];
		$T005ImprimirValor = gCorrigeNumero($mT005Imprimir['valor']);

		// Total de pedidos para separação
		$qT005Separando = mysql_query("select count(*) quantidade,
											  sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=2
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005Separando = mysql_fetch_array($qT005Separando);
		$T005SeparandoQuantidade = $mT005Separando['quantidade'];
		$T005SeparandoValor = gCorrigeNumero($mT005Separando['valor']);

		// Total de pedidos para conferência
		$qT005Conferindo = mysql_query("select count(*) quantidade,
											   sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=10
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005Conferindo = mysql_fetch_array($qT005Conferindo);
		$T005ConferindoQuantidade = $mT005Conferindo['quantidade'];
		$T005ConferindoValor = gCorrigeNumero($mT005Conferindo['valor']);

		// Total de pedidos para gerar NF
		$qT005GerarNF = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=3
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005GerarNF = mysql_fetch_array($qT005GerarNF);
		$T005GerarNFQuantidade = $mT005GerarNF['quantidade'];
		$T005GerarNFValor = gCorrigeNumero($mT005GerarNF['valor']);

		// Total de pedidos para conferência
		$qT005AguardandoColeta = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=4
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005AguardandoColeta = mysql_fetch_array($qT005AguardandoColeta);
		$T005AguardandoColetaQuantidade = $mT005AguardandoColeta['quantidade'];
		$T005AguardandoColetaValor = gCorrigeNumero($mT005AguardandoColeta['valor']);

		// Total de pedidos vencidos
		$qT005Vencidos = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T005_Flag_Status=26
								    and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT005Vencidos = mysql_fetch_array($qT005Vencidos);
		$T005VencidosQuantidade = $mT005Vencidos['quantidade'];
		$T005VencidosValor = gCorrigeNumero($mT005Vencidos['valor']);

		// Total clientes cadastrados
		$qD024Clientes = mysql_query("select count(*) quantidade
								   from D024 
							  left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where D024_Flag_Ativo='S'
									and D024_Flag_Cliente='S'
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mD024Clientes = mysql_fetch_array($qD024Clientes);
		$D024ClientesQuantidade = $mD024Clientes['quantidade'];

		// Total clientes cadastrados
		$qD024Ativos = mysql_query("select count(*) quantidade
								   from D024 
							  left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where D024_Flag_Ativo='S'
									and D024_Flag_Comercial='A'
									and D024_Flag_Cliente='S'
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mD024Ativos = mysql_fetch_array($qD024Ativos);
		$D024AtivosQuantidade = $mD024Ativos['quantidade'];

		// Total clientes cadastrados
		$qD024Inativos = mysql_query("select count(*) quantidade
								   from D024 
							  left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where D024_Flag_Ativo='S'
									and D024_Flag_Comercial!='A'
									and D024_Flag_Cliente='S'
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mD024Inativos = mysql_fetch_array($qD024Inativos);
		$D024InativosQuantidade = $mD024Inativos['quantidade'];

		// Total clientes cadastrados
		$cMes=date("m",time());
		$cAno=date("Y",time());
		$Data_Inicio=date("Y-m-01", mktime(0, 0, 0, $cMes, 1, $cAno));        
		$Data_Fim=date("Y-m-t", mktime(0, 0, 0, $cMes, 1, $cAno));        
		$qT007Vendas = mysql_query("select T007_Data_Emissao,
										   count(*) quantidade,
										   sum(T007_Valor_Total) valor 
								   from T007
							  left join D006 on D006_Id=T007_D006_Id
							  left join D024 on D024_Id=T007_D024_Id
							  left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T007_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T007_Data_Emissao>='$Data_Inicio'
									and T007_Data_Emissao<='$Data_Fim'
									and T007_Flag_Cancelada!='S'
									and D006_Flag_Entrada_Saida='S'
									and D006_Flag_Venda_Compra_Outros='V'
									and D006_Flag_Devolucao!='S'
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
						  			group by T007_Data_Emissao
						  			order by T007_Data_Emissao");

		// Total clientes cadastrados
		$qT003Orcamentos = mysql_query("select count(*) quantidade,
											   sum(T003_Valor_Total) valor
								   from T003
							  left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T003_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T003_Data_Emissao>='$Data_Inicio'
									and T003_Data_Emissao<='$Data_Fim'
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))");
		$mT003Orcamentos = mysql_fetch_array($qT003Orcamentos);

		// Total clientes cadastrados
		$qT003OrcamentosCancelados = mysql_query("select count(*) quantidade,
											   sum(T003_Valor_Total) valor,
											   D047_Descricao_Motivo
								   from T003
							  left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T003_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D047 on D047_Id=T003_D047_Id
								  where T003_Data_Emissao>='$Data_Inicio'
									and T003_Data_Emissao<='$Data_Fim'
									and T003_Flag_Perdido='S'
						  			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
						  			group by D047_Id");
		$mT003OrcamentosCancelados = mysql_fetch_array($qT003OrcamentosCancelados);

		// Total de pedidos ativos por cliente
		$qT005AtivosClientes = mysql_query("select count(distinct T005_Id) pedidos,
											sum(T006_Valor_Total_Preco) totalProdutos,
											sum(T006_Valor_Preco_Unitario*if(T006_Quantidade_Estoque>0,if(T006_Quantidade_Estoque>T006_Quantidade,T006_Quantidade,T006_Quantidade_Estoque),0)) totalDisponivel,
											D024_Id,
											substr(D024_Nome_Empresa,1,25) D024_Nome_Empresa,
											D024_Cnpj
								   from T005 
							  left join T006 on T006_T005_Id=T005_Id
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where (T005_Flag_Status=0
									 or T005_Flag_Status=14
									 or T005_Flag_Status=15)
									and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}' or externo.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
									and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
									group by D024_Id
									order by sum(T006_Valor_Total_Preco) desc");

		echo mysql_error();

		$retorno = <<<EOT
		<style type="text/css">
		.td{
			font-weight: normal;
			font-size: 12px;
			font-family: arial, verdana;
			padding: 3px 3px 3px 3px;
		}
		</style>
		<table valign=top>
			<tr>
				<td valign=top>
					<table valign=top>
						<tr>
							<td valign=top>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											PEDIDOS ATIVOS
										</td>
									</tr>
									<tr style='background:lightblue'>
										<td class='td'>
											Status
										</td>
										<td class='td' align='right'>
											Qtde
										</td>
										<td class='td' align='right'>
											Valor R$
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Pendentes
										</td>
										<td class='td' align='right'>
											$T005PendenteQuantidade
										</td>
										<td class='td' align='right'>
											$T005PendenteValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Aprovados
										</td>
										<td class='td' align='right'>
											$T005AprovadoQuantidade
										</td>
										<td class='td' align='right'>
											$T005AprovadoValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Negados
										</td>
										<td class='td' align='right'>
											$T005NegadoQuantidade
										</td>
										<td class='td' align='right'>
											$T005NegadoValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Análise comercial
										</td>
										<td class='td' align='right'>
											$T005AnaliseComercialQuantidade
										</td>
										<td class='td' align='right'>
											$T005AnaliseComercialValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Análise financeiro
										</td>
										<td class='td' align='right'>
											$T005AnaliseFinanceiroQuantidade
										</td>
										<td class='td' align='right'>
											$T005AnaliseFinanceiroValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Imprimir
										</td>
										<td class='td' align='right'>
											$T005ImprimirQuantidade
										</td>
										<td class='td' align='right'>
											$T005ImprimirValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Separando
										</td>
										<td class='td' align='right'>
											$T005SeparandoQuantidade
										</td>
										<td class='td' align='right'>
											$T005SeparandoValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Conferindo
										</td>
										<td class='td' align='right'>
											$T005ConferindoQuantidade
										</td>
										<td class='td' align='right'>
											$T005ConferindoValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Gerar NF
										</td>
										<td class='td' align='right'>
											$T005GerarNFQuantidade
										</td>
										<td class='td' align='right'>
											$T005GerarNFValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td' style='font-weight:bold'>
											&nbsp;
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T005AtivosQuantidade
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T005AtivosValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Aguardando coleta
										</td>
										<td class='td' align='right'>
											$T005AguardandoColetaQuantidade
										</td>
										<td class='td' align='right'>
											$T005AguardandoColetaValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Vencidos
										</td>
										<td class='td' align='right'>
											$T005VencidosQuantidade
										</td>
										<td class='td' align='right'>
											$T005VencidosValor
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td valign=top height=100%>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											CLIENTES
										</td>
									</tr>
									<tr style='background:lightblue'>
										<td class='td'>
											Descrição
										</td>
										<td class='td' align='right'>
											Qtde
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Ativos
										</td>
										<td class='td' align='right'>
											$D024AtivosQuantidade
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Inativos
										</td>
										<td class='td' align='right'>
											$D024InativosQuantidade
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td' style='font-weight:bold'>
											&nbsp;
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$D024ClientesQuantidade
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
				<td valign=top>
					<table valign=top>
						<tr>
							<td valign=top>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=5 align='center' style='font-weight:bold'>
											VENDAS $cMes/$cAno
										</td>
									</tr>
									<tr style='background:lightblue'>
										<td class='td'>
											Dia
										</td>
										<td class='td' align='right'>
											Qtde
										</td>
										<td class='td' align='right'>
										   	Valor R$
										</td>
										<td class='td' align='right'>
										   	Médio R$
										</td>
									</tr>
EOT;
								   	$T007VendasQuantidadeTotal = 0;
								   	$T007VendasValorTotal = 0;

									while ($mT007Vendas = mysql_fetch_array($qT007Vendas)) {

											$T007VendasQuantidadeTotal += $mT007Vendas['quantidade'];
											$T007VendasValorTotal += $mT007Vendas['valor'];

											$T007VendasTicketMedio = gCorrigeNumero($mT007Vendas['valor'] / $mT007Vendas['quantidade']);
											$T007VendasData = gCorrigeData($mT007Vendas['T007_Data_Emissao']);
											$T007VendasQuantidade = $mT007Vendas['quantidade'];
											$T007VendasValor = gCorrigeNumero($mT007Vendas['valor']);

											$retorno .= <<<EOT
													<tr style='background:white'>
														<td class='td'>
															$T007VendasData
														</td>
														<td class='td' align='right'>
															$T007VendasQuantidade
														</td>
														<td class='td' align='right'>
															$T007VendasValor
														</td>
														<td class='td' align='right'>
															$T007VendasTicketMedio
														</td>
													</tr>
EOT;
									
									}

								   	$Aproveitamento = gCorrigeNumero(($T007VendasValorTotal/$mT003Orcamentos['valor'])*100);
									$T007VendasTicketMedioTotal = gCorrigeNumero($T007VendasValorTotal/$T007VendasQuantidadeTotal);
									$T007VendasValorTotal = gCorrigeNumero($T007VendasValorTotal);
								   	$T003OrcamentosTicketMedio = gCorrigeNumero($mT003Orcamentos['valor']/$mT003Orcamentos['quantidade']);
								   	$T003OrcamentosQuantidade = $mT003Orcamentos['quantidade'];
								   	$T003OrcamentosValor = gCorrigeNumero($mT003Orcamentos['valor']);

									$retorno .= <<<EOT
									<tr style='background:white;'>
										<td class='td' style='font-weight:bold'>
											Total vendas
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T007VendasQuantidadeTotal
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T007VendasValorTotal
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T007VendasTicketMedioTotal
										</td>
									</tr>
									<tr style='background:white;'>
										<td class='td' style='font-weight:bold'>
											Total orçamentos
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosQuantidade
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosValor
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosTicketMedio
										</td>
									</tr>
									<tr style='background:white;'>
										<td class='td' style='font-weight:bold'>
											Aproveitamento
										</td>
										<td class='td' align='right' style='font-weight:bold' colspan=2>
											$Aproveitamento %
										</td>
										<td class='td' align='right' style='font-weight:bold' colspan=2>
											&nbsp;
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											ORÇAMENTOS CANCELADOS $cMes/$cAno
										</td>
									</tr>
									<tr style='background:lightblue'>
										<td class='td'>
											Motivo
										</td>
										<td class='td' align='right'>
											Qtde
										</td>
										<td class='td' align='right'>
										   	Valor R$
										</td>
									</tr>
EOT;
								   	$T003OrcamentosCanceladosQuantidadeTotal = 0;
								   	$T003OrcamentosCanceladosValorTotal = 0;

									while ($mT003OrcamentosCancelados = mysql_fetch_array($qT003OrcamentosCancelados)) {

											$T003OrcamentosCanceladosQuantidadeTotal += $mT003OrcamentosCancelados['quantidade'];
											$T003OrcamentosCanceladosValorTotal += $mT003OrcamentosCancelados['valor'];

											$T003OrcamentosCanceladosMotivo = $mT003OrcamentosCancelados['D047_Descricao_Motivo'];
											$T003OrcamentosCanceladosQuantidade = $mT003OrcamentosCancelados['quantidade'];
											$T003OrcamentosCanceladosValor = gCorrigeNumero($mT003OrcamentosCancelados['valor']);

											$retorno .= <<<EOT
													<tr style='background:white'>
														<td class='td'>
															$T003OrcamentosCanceladosMotivo
														</td>
														<td class='td' align='right'>
															$T003OrcamentosCanceladosQuantidade
														</td>
														<td class='td' align='right'>
															$T003OrcamentosCanceladosValor
														</td>
													</tr>
EOT;
		
									}

									$T003OrcamentosCanceladosValorTotal = gCorrigeNumero($T003OrcamentosCanceladosValorTotal);

									$retorno .= <<<EOT
									<tr style='background:white;'>
										<td class='td' style='font-weight:bold'>
											&nbsp;
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosCanceladosQuantidadeTotal
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosCanceladosValorTotal
										</td>
									</tr>
								</table>
							</tr>
						</td>
					</table>
				</td>
				<td valign=top>
					<table valign=top>
						<tr>
							<td valign=top>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=6 align='center' style='font-weight:bold'>
											PEDIDOS PENDENTES POR CLIENTE
										</td>
									</tr>
									<tr style='background:lightblue'>
										<td class='td'>
											Código
										</td>
										<td class='td'>
											Cliente
										</td>
										<td class='td'>
											Cnpj
										</td>
										<td class='td' align='right'>
											Qtde
										</td>
										<td class='td' align='right'>
										   	Produtos R$
										</td>
										<td class='td' align='right'>
										   	Estoque R$
										</td>
									</tr>
EOT;
								   	$T005AtivosClientesQuantidadeTotal = 0;
								   	$T005AtivosClientesTotalProdutosTotal = 0;
								   	$T005AtivosClientesTotalDisponivelTotal =0;

									while($mT005AtivosClientes = mysql_fetch_array($qT005AtivosClientes)){
				
										$T005AtivosClientesQuantidadeTotal += $mT005AtivosClientes['pedidos'];
										$T005AtivosClientesTotalProdutosTotal += $mT005AtivosClientes['totalProdutos'];
										$T005AtivosClientesTotalDisponivelTotal += $mT005AtivosClientes['totalDisponivel'];

										$T005AtivosClientesQuantidade = $mT005AtivosClientes['pedidos'];
										$T005AtivosClientesTotalProdutos = gCorrigeNumero($mT005AtivosClientes['totalProdutos']);
										$T005AtivosClientesTotalDisponivel = gCorrigeNumero($mT005AtivosClientes['totalDisponivel']);

										$retorno .= <<<EOT
											<tr style='background:white'>
												<td class='td' align='center'>
													$mT005AtivosClientes[D024_Id]
												</td>
												<td class='td'>
													$mT005AtivosClientes[D024_Nome_Empresa]
												</td>
												<td class='td'>
													$mT005AtivosClientes[D024_Cnpj]
												</td>
												<td class='td' align='right'>
													$T005AtivosClientesQuantidade
												</td>
												<td class='td' align='right'>
													$T005AtivosClientesTotalProdutos
												</td>
												<td class='td' align='right'>
													$T005AtivosClientesTotalDisponivel
												</td>
											</tr>
EOT;
									}

									$T005AtivosClientesTotalProdutosTotal = gCorrigeNumero($T005AtivosClientesTotalProdutosTotal);
									$T005AtivosClientesTotalDisponivelTotal = gCorrigeNumero($T005AtivosClientesTotalDisponivelTotal);

									$retorno .= <<<EOT
										<tr style='background:white'>
											<td class='td'>
												&nbsp;
											</td>
											<td class='td'>
												&nbsp;
											</td>
											<td class='td'>
												&nbsp;
											</td>
											<td class='td' align='right' style='font-weight:bold'>
												$T005AtivosClientesQuantidadeTotal
											</td>
											<td class='td' align='right' style='font-weight:bold'>
												$T005AtivosClientesTotalProdutosTotal
											</td>
											<td class='td' align='right' style='font-weight:bold'>
												$T005AtivosClientesTotalDisponivelTotal
											</td>
										</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
EOT;

		echo $retorno;
	break;

	case 'crm001ResumoCliente':
		// Mostra informações gerênciais do Cliente no CRM

		// recarregar a tela automaticamente      
		echo <<<EOT
			<div>
				<script type="text/javascript">
					setTimeout(function(){
						divRefresh('{$g['divId']}');
					}, 30000);
				</script>
EOT;

		// Total de pedidos ativos (em aberto)
		$qT005Ativos = mysql_query("select count(*) quantidade,
										   sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where (T005_Flag_Status=0
									 or T005_Flag_Status=14
									 or T005_Flag_Status=15
									 or T005_Flag_Status=13
									 or T005_Flag_Status=6
									 or T005_Flag_Status=1
									 or T005_Flag_Status=2
									 or T005_Flag_Status=3
									 or T005_Flag_Status=10)
						  			 and D024_Id='$r_D024_Id')");
		$mT005Ativos = mysql_fetch_array($qT005Ativos);
		$T005AtivosQuantidade = $mT005Ativos['quantidade'];
		$T005AtivosValor = gCorrigeNumero($mT005Ativos['valor']);

		// Total de pedidos pendentes
		$qT005Pendente = mysql_query("select count(*) quantidade,
											 sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=0
						  			and D024_Id='$r_D024_Id'");
		$mT005Pendente = mysql_fetch_array($qT005Pendente);
		$T005PendenteQuantidade = $mT005Pendente['quantidade'];
		$T005PendenteValor = gCorrigeNumero($mT005Pendente['valor']);

		// Total de pedidos aprovados
		$qT005Aprovado = mysql_query("select count(*) quantidade,
											 sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=14
						  			and D024_Id='$r_D024_Id'");
		$mT005Aprovado = mysql_fetch_array($qT005Aprovado);
		$T005AprovadoQuantidade = $mT005Aprovado['quantidade'];
		$T005AprovadoValor = gCorrigeNumero($mT005Aprovado['valor']);

		// Total de pedidos negados
		$qT005Negado = mysql_query("select count(*) quantidade,
										   sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=15
						  			and D024_Id='$r_D024_Id'");
		$mT005Negado = mysql_fetch_array($qT005Negado);
		$T005NegadoQuantidade = $mT005Negado['quantidade'];
		$T005NegadoValor = gCorrigeNumero($mT005Negado['valor']);

		// Total de pedidos em análise comercial
		$qT005AnaliseComercial = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=13
						  			and D024_Id='$r_D024_Id'");
		$mT005AnaliseComercial = mysql_fetch_array($qT005AnaliseComercial);
		$T005AnaliseComercialQuantidade = $mT005AnaliseComercial['quantidade'];
		$T005AnaliseComercialValor = gCorrigeNumero($mT005AnaliseComercial['valor']);

		// Total de pedidos em análise comercial
		$qT005AnaliseFinanceiro = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=6
						  			and D024_Id='$r_D024_Id'");
		$mT005AnaliseFinanceiro = mysql_fetch_array($qT005AnaliseFinanceiro);
		$T005AnaliseFinanceiroQuantidade = $mT005AnaliseFinanceiro['quantidade'];
		$T005AnaliseFinanceiroValor = gCorrigeNumero($mT005AnaliseFinanceiro['valor']);

		// Total de pedidos para impressão
		$qT005Imprimir = mysql_query("select count(*) quantidade,
											 sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=1
						  			and D024_Id='$r_D024_Id'");
		$mT005Imprimir = mysql_fetch_array($qT005Imprimir);
		$T005ImprimirQuantidade = $mT005Imprimir['quantidade'];
		$T005ImprimirValor = gCorrigeNumero($mT005Imprimir['valor']);

		// Total de pedidos para separação
		$qT005Separando = mysql_query("select count(*) quantidade,
											  sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=2
						  			and D024_Id='$r_D024_Id'");
		$mT005Separando = mysql_fetch_array($qT005Separando);
		$T005SeparandoQuantidade = $mT005Separando['quantidade'];
		$T005SeparandoValor = gCorrigeNumero($mT005Separando['valor']);

		// Total de pedidos para conferência
		$qT005Conferindo = mysql_query("select count(*) quantidade,
											   sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=10
						  			and D024_Id='$r_D024_Id'");
		$mT005Conferindo = mysql_fetch_array($qT005Conferindo);
		$T005ConferindoQuantidade = $mT005Conferindo['quantidade'];
		$T005ConferindoValor = gCorrigeNumero($mT005Conferindo['valor']);

		// Total de pedidos para gerar NF
		$qT005GerarNF = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=3
						  			and D024_Id='$r_D024_Id'");
		$mT005GerarNF = mysql_fetch_array($qT005GerarNF);
		$T005GerarNFQuantidade = $mT005GerarNF['quantidade'];
		$T005GerarNFValor = gCorrigeNumero($mT005GerarNF['valor']);

		// Total de pedidos para conferência
		$qT005AguardandoColeta = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=4
						  			and D024_Id='$r_D024_Id'");
		$mT005AguardandoColeta = mysql_fetch_array($qT005AguardandoColeta);
		$T005AguardandoColetaQuantidade = $mT005AguardandoColeta['quantidade'];
		$T005AguardandoColetaValor = gCorrigeNumero($mT005AguardandoColeta['valor']);

		// Total de pedidos vencidos
		$qT005Vencidos = mysql_query("select count(*) quantidade,
											sum(T005_Valor_Total) valor
								   from T005 
							  left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T005_D024_Id
								  where T005_Flag_Status=26
						  			and D024_Id='$r_D024_Id'");
		$mT005Vencidos = mysql_fetch_array($qT005Vencidos);
		$T005VencidosQuantidade = $mT005Vencidos['quantidade'];
		$T005VencidosValor = gCorrigeNumero($mT005Vencidos['valor']);


		// Total vendas por dia
		$cMes=date("m",time());
		$cAno=date("Y",time());
		$Data_Inicio=date("Y-01-01", mktime(0, 0, 0, $cMes, 1, $cAno));        
		$Data_Fim=date("Y-m-t", mktime(0, 0, 0, $cMes, 1, $cAno));        
		$qT007Vendas = mysql_query("select month(T007_Data_Emissao) mes,
										   year(T007_Data_Emissao) ano,
										   count(*) quantidade,
										   sum(T007_Valor_Total) valor 
								   from T007
							  left join D006 on D006_Id=T007_D006_Id
							  left join D024 on D024_Id=T007_D024_Id
							  left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
								  where T007_Data_Emissao>='$Data_Inicio'
									and T007_Data_Emissao<='$Data_Fim'
									and T007_Flag_Cancelada!='S'
									and D006_Flag_Entrada_Saida='S'
									and D006_Flag_Venda_Compra_Outros='V'
									and D006_Flag_Devolucao!='S'
						  			and D024_Id='$r_D024_Id'
						  			group by month(T007_Data_Emissao)
						  			order by month(T007_Data_Emissao)");

		echo mysql_error();

		// Total orcamentos
		$qT003Orcamentos = mysql_query("select count(*) quantidade,
											   sum(T003_Valor_Total) valor
								   from T003
							  left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T003_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D024 on D024_Id=T003_D024_Id
								  where T003_Data_Emissao>='$Data_Inicio'
									and T003_Data_Emissao<='$Data_Fim'
						  			and D024_Id='$r_D024_Id'");
		$mT003Orcamentos = mysql_fetch_array($qT003Orcamentos);

		echo mysql_error();

		// Total orcamentos cancelados por motivo
		$qT003OrcamentosCancelados = mysql_query("select count(*) quantidade,
											   sum(T003_Valor_Total) valor,
											   D047_Descricao_Motivo
								   from T003
							  left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
							  left join C007 as Externo on Externo.C007_Id=T003_C007_Id_Vendedor_Externo
							  left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
							  left join D047 on D047_Id=T003_D047_Id
							  left join D024 on D024_Id=T003_D024_Id
								  where T003_Data_Emissao>='$Data_Inicio'
									and T003_Data_Emissao<='$Data_Fim'
									and T003_Flag_Perdido='S'
						  			and D024_Id='$r_D024_Id'
						  			group by D047_Id");
		$mT003OrcamentosCancelados = mysql_fetch_array($qT003OrcamentosCancelados);

	   $T002=mysql_query("select count(*)
							from T002
						   where T002_Data_Emissao>='$Data_Inicio'
							 and T002_Data_Emissao<='$Data_Fim'
 							 and T002_D024_Id='$r_D024_Id'
							 and T002_Flag_Cancelada='N'");
	   $mT002_Total=mysql_fetch_array($T002);

	   $T002_Media_Atraso=mysql_query("select count(*),
											  sum(datediff(T002_Data_Recebimento,T002_Data_Vencimento))
							from T002
						   where T002_Data_Recebimento>T002_Data_Vencimento
							 and T002_Data_Emissao>='$Data_Inicio'
							 and T002_Data_Emissao<='$Data_Fim'
							 and T002_D024_Id='$r_D024_Id'
							 and T002_Flag_Cancelada='N'");
	   $mT002_Media_Atraso=mysql_fetch_array($T002_Media_Atraso);

	   echo mysql_error();

	   $T002_Media_EmDia=mysql_query("select count(*)
							from T002
						   where T002_Data_Recebimento<=T002_Data_Vencimento
							 and T002_Data_Emissao>='$Data_Inicio'
							 and T002_Data_Emissao<='$Data_Fim'
							 and T002_D024_Id='$r_D024_Id'
							 and T002_Flag_Cancelada='N'");
	   $mT002_Media_EmDia=mysql_fetch_array($T002_Media_EmDia);

	   $T002=mysql_query("select D027_Portador,
								 count(*)
							from T002
					   left join D027 on D027_Id=T002_D027_Id
						   where T002_D024_Id='$r_D024_Id'
							 and T002_Data_Emissao>='$Data_Inicio'
							 and T002_Data_Emissao<='$Data_Fim'
							 and T002_Flag_Cancelada='N'
						group by T002_D027_Id
						order by count(*) desc");
						
	   $T002_Prazo=mysql_query("select DateDiff(T002_Data_Vencimento,T002_Data_Emissao),
								 count(*)
							from T002
						   where T002_D024_Id='$r_D024_Id'
							 and T002_Data_Emissao>='$Data_Inicio'
							 and T002_Data_Emissao<='$Data_Fim'
							 and T002_Flag_Cancelada='N'
						group by DateDiff(T002_Data_Vencimento,T002_Data_Emissao)
						order by count(*) desc");

		echo mysql_error();

		//Maior Compra
		$maiorCompra = mysql_query("SELECT T007_Data_Emissao,T007_Valor_total,T007_Id 
										FROM T007
										WHERE T007_D024_Id = '{$r_D024_Id}'
										AND T007_Data_Emissao >= '{$Data_Inicio}'
										AND T007_Flag_Cancelada='N'
										ORDER BY T007_Valor_Total DESC,
										T007_Data_Emissao DESC
										LIMIT 1");
		$maiorCompra = mysql_fetch_array($maiorCompra);

		//Ultima Compra
		$ultimaCompra = mysql_query("SELECT T007_Data_Emissao,T007_Valor_total,T007_Id
										FROM T007
										WHERE T007_D024_Id = '{$r_D024_Id}'
										AND T007_Data_Emissao >= '{$Data_Inicio}'
										AND T007_Flag_Cancelada!='S'
										AND T007_Valor_Total>0
										ORDER BY T007_Data_Emissao DESC
										LIMIT 1");
		$ultimaCompra = mysql_fetch_array($ultimaCompra);

		$dataUltimaCompra = gCorrigeData($ultimaCompra['T007_Data_Emissao']);
		$valorUltimaCompra = gCorrigeNumero($ultimaCompra['T007_Valor_total']);
		$numeroUltimaCompra = $ultimaCompra['T007_Id'];

		$dataMaiorCompra = gCorrigeData($maiorCompra['T007_Data_Emissao']);
		$valorMaiorCompra = gCorrigeNumero($maiorCompra['T007_Valor_total']);
		$numeroMaiorCompra = $maiorCompra['T007_Id'];

		$retorno = <<<EOT
		<style type="text/css">
		.td{
			font-weight: normal;
			font-size: 12px;
			font-family: arial, verdana;
			padding: 3px 3px 3px 3px;
		}
		</style>
		<table valign=top>
			<tr>
				<td valign=top>
					<table valign=top>
						<tr>
							<td valign=top>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											PEDIDOS ATIVOS
										</td>
									</tr>
									<tr style='background:lightblue'>
										<td class='td'>
											Status
										</td>
										<td class='td' align='right'>
											Qtde
										</td>
										<td class='td' align='right'>
											Valor R$
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Pendentes
										</td>
										<td class='td' align='right'>
											$T005PendenteQuantidade
										</td>
										<td class='td' align='right'>
											$T005PendenteValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Aprovados
										</td>
										<td class='td' align='right'>
											$T005AprovadoQuantidade
										</td>
										<td class='td' align='right'>
											$T005AprovadoValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Negados
										</td>
										<td class='td' align='right'>
											$T005NegadoQuantidade
										</td>
										<td class='td' align='right'>
											$T005NegadoValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Análise comercial
										</td>
										<td class='td' align='right'>
											$T005AnaliseComercialQuantidade
										</td>
										<td class='td' align='right'>
											$T005AnaliseComercialValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Análise financeiro
										</td>
										<td class='td' align='right'>
											$T005AnaliseFinanceiroQuantidade
										</td>
										<td class='td' align='right'>
											$T005AnaliseFinanceiroValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Imprimir
										</td>
										<td class='td' align='right'>
											$T005ImprimirQuantidade
										</td>
										<td class='td' align='right'>
											$T005ImprimirValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Separando
										</td>
										<td class='td' align='right'>
											$T005SeparandoQuantidade
										</td>
										<td class='td' align='right'>
											$T005SeparandoValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Conferindo
										</td>
										<td class='td' align='right'>
											$T005ConferindoQuantidade
										</td>
										<td class='td' align='right'>
											$T005ConferindoValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Gerar NF
										</td>
										<td class='td' align='right'>
											$T005GerarNFQuantidade
										</td>
										<td class='td' align='right'>
											$T005GerarNFValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td' style='font-weight:bold'>
											&nbsp;
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T005AtivosQuantidade
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T005AtivosValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Aguardando coleta
										</td>
										<td class='td' align='right'>
											$T005AguardandoColetaQuantidade
										</td>
										<td class='td' align='right'>
											$T005AguardandoColetaValor
										</td>
									</tr>
									<tr style='background:white'>
										<td class='td'>
											Vencidos
										</td>
										<td class='td' align='right'>
											$T005VencidosQuantidade
										</td>
										<td class='td' align='right'>
											$T005VencidosValor
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											FORMA PAGAMENTO
								   		</td>
								   	</tr>
EOT;
									  while ($mT002=mysql_fetch_array($T002)) {
											$retorno .= "<tr style='background:white'><td class='td' align=right width='25px'>".number_format(($mT002[1]/$mT002_Total[0])*100,2,',','.')."% </td><td> $mT002[0]</td></tr>";
									  }

									$retorno .= <<<EOT
								</table>
							</td>
						</tr>
						<tr align=left>
							<td>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											PRAZO PAGAMENTO
								   		</td>
								   	</tr>
EOT;
									while ($mT002_Prazo=mysql_fetch_array($T002_Prazo)) {
										$retorno .= "<tr style='background:white'><td class='td' align=right width='25px'>".number_format(($mT002_Prazo[1]/$mT002_Total[0])*100,2,',','.')."% </td><td class='td'>$mT002_Prazo[0] dias</td></tr>";
									}

									$retorno .= <<<EOT
							  </table>
						   </td>
						 </tr>
						 <tr align=left>
						   <td>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											PONTUALIDADE $cAno
								   		</td>
								   	</tr>
EOT;
									if ($mT002_Media_Atraso[0]>0 and $mT002_Total[0]>0)
									{
										$retorno .= "<tr style='background:white'><td class='td' width='25px' align=right>".number_format(($mT002_Media_Atraso[0]/$mT002_Total[0])*100,2,',','.')."% </td><td> Pago Atrasado (média dias ".number_format($mT002_Media_Atraso[1]/$mT002_Media_Atraso[0],0).")</td></tr>";
									}
									if ($mT002_Media_EmDia[0]>0 and $mT002_Total[0]>0)
								 	{
									 	$retorno .= "<tr style='background:white'><td class='td' width='25px' align=right>".number_format(($mT002_Media_EmDia[0]/$mT002_Total[0])*100,2,',','.')."% </td><td> Pago em dia</td></tr>";
								 	}

								$retorno .= <<<EOT
							  	</table>
						   	</td>
					 	</tr>
			   		</table>
				</td>
				<td valign=top>
					<table valign=top>
						<tr>
							<td valign=top>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=5 align='center' style='font-weight:bold'>
											VENDAS $cAno
										</td>
									</tr>
									<tr style='background:lightblue'>
										<td class='td'>
											Mês
										</td>
										<td class='td' align='right'>
											Qtde
										</td>
										<td class='td' align='right'>
										   	Valor R$
										</td>
										<td class='td' align='right'>
										   	Médio R$
										</td>
									</tr>
EOT;
								   	$T007VendasQuantidadeTotal = 0;
								   	$T007VendasValorTotal = 0;

									while ($mT007Vendas = mysql_fetch_array($qT007Vendas)) {

											$T007VendasQuantidadeTotal += $mT007Vendas['quantidade'];
											$T007VendasValorTotal += $mT007Vendas['valor'];

											$T007VendasTicketMedio = gCorrigeNumero($mT007Vendas['valor'] / $mT007Vendas['quantidade']);
											$T007VendasMes = $mT007Vendas['mes'].'/'.$mT007Vendas['ano'];
											$T007VendasQuantidade = $mT007Vendas['quantidade'];
											$T007VendasValor = gCorrigeNumero($mT007Vendas['valor']);

											$retorno .= <<<EOT
													<tr style='background:white'>
														<td class='td'>
															$T007VendasMes
														</td>
														<td class='td' align='right'>
															$T007VendasQuantidade
														</td>
														<td class='td' align='right'>
															$T007VendasValor
														</td>
														<td class='td' align='right'>
															$T007VendasTicketMedio
														</td>
													</tr>
EOT;
									
									}

								   	$Aproveitamento = gCorrigeNumero(($T007VendasValorTotal/$mT003Orcamentos['valor'])*100);
									$T007VendasTicketMedioTotal = gCorrigeNumero($T007VendasValorTotal/$T007VendasQuantidadeTotal);
									$T007VendasValorTotal = gCorrigeNumero($T007VendasValorTotal);
								   	$T003OrcamentosTicketMedio = gCorrigeNumero($mT003Orcamentos['valor']/$mT003Orcamentos['quantidade']);
								   	$T003OrcamentosQuantidade = $mT003Orcamentos['quantidade'];
								   	$T003OrcamentosValor = gCorrigeNumero($mT003Orcamentos['valor']);

									$retorno .= <<<EOT
									<tr style='background:white;'>
										<td class='td' style='font-weight:bold'>
											Total vendas
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T007VendasQuantidadeTotal
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T007VendasValorTotal
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T007VendasTicketMedioTotal
										</td>
									</tr>
									<tr style='background:white;'>
										<td class='td' style='font-weight:bold'>
											Total orçamentos
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosQuantidade
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosValor
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosTicketMedio
										</td>
									</tr>
									<tr style='background:white;'>
										<td class='td' style='font-weight:bold'>
											Aproveitamento
										</td>
										<td class='td' align='right' style='font-weight:bold' colspan=2>
											$Aproveitamento %
										</td>
										<td class='td' align='right' style='font-weight:bold' colspan=2>
											&nbsp;
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table width=100% valign=top>
									<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											ORÇAMENTOS CANCELADOS $cAno
										</td>
									</tr>
									<tr style='background:lightblue'>
										<td class='td'>
											Motivo
										</td>
										<td class='td' align='right'>
											Qtde
										</td>
										<td class='td' align='right'>
										   	Valor R$
										</td>
									</tr>
EOT;
								   	$T003OrcamentosCanceladosQuantidadeTotal = 0;
								   	$T003OrcamentosCanceladosValorTotal = 0;

									while ($mT003OrcamentosCancelados = mysql_fetch_array($qT003OrcamentosCancelados)) {

											$T003OrcamentosCanceladosQuantidadeTotal += $mT003OrcamentosCancelados['quantidade'];
											$T003OrcamentosCanceladosValorTotal += $mT003OrcamentosCancelados['valor'];

											$T003OrcamentosCanceladosMotivo = $mT003OrcamentosCancelados['D047_Descricao_Motivo'];
											$T003OrcamentosCanceladosQuantidade = $mT003OrcamentosCancelados['quantidade'];
											$T003OrcamentosCanceladosValor = gCorrigeNumero($mT003OrcamentosCancelados['valor']);

											$retorno .= <<<EOT
													<tr style='background:white'>
														<td class='td'>
															$T003OrcamentosCanceladosMotivo
														</td>
														<td class='td' align='right'>
															$T003OrcamentosCanceladosQuantidade
														</td>
														<td class='td' align='right'>
															$T003OrcamentosCanceladosValor
														</td>
													</tr>
EOT;
		
									}

									$T003OrcamentosCanceladosValorTotal = gCorrigeNumero($T003OrcamentosCanceladosValorTotal);

									$retorno .= <<<EOT
									<tr style='background:white;'>
										<td class='td' style='font-weight:bold'>
											&nbsp;
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosCanceladosQuantidadeTotal
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											$T003OrcamentosCanceladosValorTotal
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<table width=100% valign=top>
								   	<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											MAIOR COMPRA $cAno
										</td>
								   	</tr>
								   	<tr style='background:white;'>
										<td class='td' align='right' style='font-weight:bold'>
											NF - {$numeroMaiorCompra}
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											{$dataMaiorCompra}
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											R$ {$valorMaiorCompra}
										</td>
								   	</tr>
								   	<tr style='background:lightblue'>
										<td class='td' colspan=3 align='center' style='font-weight:bold'>
											ÚLTIMA COMPRA $cAno
										</td>
								   	</tr>
								   	<tr style='background:white;'>
										<td class='td' align='right' style='font-weight:bold'>
											NF - {$numeroUltimaCompra}
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											{$dataUltimaCompra}
										</td>
										<td class='td' align='right' style='font-weight:bold'>
											R$ {$valorUltimaCompra}
										</td>
								   	</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
				<td valign=top>
					<table valign=top>
						<tr>
							<td valign=top>
EOT;
							  $usaGrupo=mysql_query("select count(*)
												   from T007
											  left join D006 on D006_Id=T007_D006_Id
											  left join T008 on T008_T007_Id=T007_Id
											  left join D009 on D009_Id=T008_D009_Id
											  left join D049 on D049_Id=D009_D049_Id
											  left join D001 on D001_Id=D049_D001_Id
											  left join D003 on D003_Id=D001_D003_Id
												  where T007_Data_Emissao>='$Data_Inicio'
													and T007_Data_Emissao<='$Data_Fim'
													and T007_D024_Id='$r_D024_Id'
													and D006_Flag_Venda_Compra_Outros='V'
													and D006_Flag_Devolucao='N'
													and T007_Flag_Cancelada!='S'
													and D003_Id is not null");

							  $corSetor='white';
							  $fonteSetor='normal';
							  $usaGrupo=mysql_fetch_array($usaGrupo);
							  if ($usaGrupo[0]>0){
							  	  $corSetor='#f4f4f4';
							  	  $fonteSetor='bold';
							  }

							  $totalProdutos=mysql_query("select sum(T008_Valor_Total_Preco) Valor_Total
												   from T007
											  left join D006 on D006_Id=T007_D006_Id
											  left join T008 on T008_T007_Id=T007_Id
											  left join D009 on D009_Id=T008_D009_Id
											  left join D049 on D049_Id=D009_D049_Id
											  left join D001 on D001_Id=D049_D001_Id
											  left join D003 on D003_Id=D001_D003_Id
												  where T007_Data_Emissao>='$Data_Inicio'
													and T007_Data_Emissao<='$Data_Fim'
													and T007_D024_Id='$r_D024_Id'
													and D006_Flag_Venda_Compra_Outros='V'
													and D006_Flag_Devolucao='N'
													and T007_Flag_Cancelada!='S'");
		   					  $totalProdutos=mysql_fetch_array($totalProdutos);

							  $C008=mysql_query("select sum(T008_Valor_Total_Preco) Valor_Total,
														C008_Id,
														C008_Tipo
												   from T007
											  left join D006 on D006_Id=T007_D006_Id
											  left join T008 on T008_T007_Id=T007_Id
											  left join D009 on D009_Id=T008_D009_Id
											  left join D049 on D049_Id=D009_D049_Id
											  left join D001 on D001_Id=D049_D001_Id
											  left join C008 on C008_Id=D001_C008_Id
												  where T007_Data_Emissao>='$Data_Inicio'
													and T007_Data_Emissao<='$Data_Fim'
													and T007_D024_Id='$r_D024_Id'
													and D006_Flag_Venda_Compra_Outros='V'
													and D006_Flag_Devolucao='N'
													and T007_Flag_Cancelada!='S'
											   group by C008_Id
											   order by count(*) desc");

							   echo mysql_error();

							   $retorno .= "<table cellpadding=2 width=100% valign=top>";
								 $retorno .= "<tr bgcolor='lightblue'>";
								   $retorno .= "<td align=center colspan=11>";
									 $retorno .= "<font class=td style='font-weight:bold'>VENDAS POR DEPARTAMENTO $cAno</font>";
								   $retorno .= "</td>";
								 $retorno .= "</tr>";
								 $retorno .= "<tr bgcolor='lightblue'>";
								   $retorno .= "<td align=center>";
									 $retorno .= "<font class=td>Departamento</font>";
								   $retorno .= "</td>";
								   $retorno .= "<td align=center>";
									 $retorno .= "<font class=td>Setor</font>";
								   $retorno .= "</td>";
								   if ($usaGrupo[0]>0){
									   $retorno .= "<td align=center>";
										 $retorno .= "<font class=td>Grupo</font>";
									   $retorno .= "</td>";
									   $retorno .= "<td align=center>";
										 $retorno .= "<font class=td>Sub-Grupo</font>";
									   $retorno .= "</td>";
								   }
								   $retorno .= "<td align=right>";
									 $retorno .= "<font class=td>Total R$</font>";
								   $retorno .= "</td>";
								   $retorno .= "<td align=right>";
									 $retorno .= "<font class=td>&nbsp;</font>";
								   $retorno .= "</td>";
								 $retorno .= "</tr>";

								 echo mysql_error();
								 unset($mTotal);

								 while ($mC008=mysql_fetch_array($C008)) {

									 $retorno .= "<tr bgcolor='#f4f4f4'>";
									   $retorno .= "<td align=left>";
										 $retorno .= "<font class=td style='font-size:12px;'><b>{$mC008[C008_Tipo]}</b></font>";
									   $retorno .= "</td>";
									   $retorno .= "<td align=center>";
										 $retorno .= "<font class=td style='font-size:12px;'><b>&nbsp;</b></font>";
									   $retorno .= "</td>";
									   if ($usaGrupo[0]>0){
										   $retorno .= "<td align=center>";
											 $retorno .= "<font class=td style='font-size:12px;'><b>&nbsp;</b></font>";
										   $retorno .= "</td>";
										   $retorno .= "<td align=center>";
											 $retorno .= "<font class=td>&nbsp;</font>";
										   $retorno .= "</td>";
									   }
									   $retorno .= "<td align=right>";
										 $retorno .= "<font class=td style='font-size:12px;'><b>".number_format($mC008[Valor_Total],2,',','.')."</b></font>";
									   $retorno .= "</td>";
									   $retorno .= "<td align=right>";
										 $retorno .= "<font class=td style='font-size:12px;'><b>".number_format(($mC008[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</b></font>";
									   $retorno .= "</td>";
									 $retorno .= "</tr>";


									$T007=mysql_query("select sum(T008_Valor_Total_Preco) Valor_Total,
															  D060_Id,
															  D060_Descricao_Grupo
														 from T007
													left join D006 on D006_Id=T007_D006_Id
													left join T008 on T008_T007_Id=T007_Id
													left join D009 on D009_Id=T008_D009_Id
													left join D049 on D049_Id=D009_D049_Id
													left join D001 as D1 on D001_Id=D049_D001_Id
													left join D060 on D060_Id=D001_D060_Id
														where T007_Data_Emissao>='$Data_Inicio'
														  and T007_Data_Emissao<='$Data_Fim'
														  and T007_D024_Id='$r_D024_Id'
														  and D006_Flag_Venda_Compra_Outros='V'
														  and D006_Flag_Devolucao='N'
														  and T007_Flag_Cancelada!='S'
														  and D001_C008_Id='$mC008[C008_Id]'
													 group by D060_Id
													 order by sum(T008_Valor_Total_Preco) desc");

									 echo mysql_error();


									   while ($mT007=mysql_fetch_array($T007)) {

											 $retorno .= "<tr bgcolor='$corSetor' style='font-weight:$fonteSetor'>";
											   $retorno .= "<td align=center>";
												 $retorno .= "<font class=td style='font-size:12px;'>&nbsp;</font>";
											   $retorno .= "</td>";
											   $retorno .= "<td align=left>";
												 $retorno .= "<font class=td style='font-size:12px;font-weight:$fonteSetor'>{$mT007[D060_Descricao_Grupo]}</font>";
											   $retorno .= "</td>";
											   if ($usaGrupo[0]>0){
												   $retorno .= "<td align=center>";
													 $retorno .= "<font class=td style='font-size:12px;'>&nbsp;</font>";
												   $retorno .= "</td>";
												   $retorno .= "<td align=center>";
													 $retorno .= "<font class=td>&nbsp;</font>";
												   $retorno .= "</td>";
											   }
											   $retorno .= "<td align=right>";
												 $retorno .= "<font class=td style='font-size:12px;font-weight:$fonteSetor'>".number_format($mT007[Valor_Total],2,',','.')."</font>";
											   $retorno .= "</td>";
											   $retorno .= "<td align=right>";
												 $retorno .= "<font class=td style='font-size:12px;font-weight:$fonteSetor'>".number_format(($mT007[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</font>";
											   $retorno .= "</td>";
											 $retorno .= "</tr>";

											 $mTotal[Notas]      += $mT007[Notas];
											 $mTotal[Clientes]   += $mT007[Clientes];
											 $mTotal[Itens]      += $mT007[Itens];
											 $mTotal[Valor_Total]+= $mT007[Valor_Total];

											 $T0071=mysql_query("select D003_Nome_Linha,
																	  D003_Id,
																	  sum(T008_Valor_Total_Preco) Valor_Total
																 from T007
															left join D006 on D006_Id=T007_D006_Id
															left join T008 on T008_T007_Id=T007_Id
															left join D009 on D009_Id=T008_D009_Id
															left join D049 on D049_Id=D009_D049_Id
															left join D001 as D1 on D001_Id=D049_D001_Id
															left join D003 on D003_Id=D001_D003_Id
																where T007_Data_Emissao>='$Data_Inicio'
																  and T007_Data_Emissao<='$Data_Fim'
																  and T007_D024_Id='$r_D024_Id'
																  and D006_Flag_Venda_Compra_Outros='V'
																  and D006_Flag_Devolucao='N'
																  and T007_Flag_Cancelada!='S'
																  and D003_Id is not null
																  and D001_C008_Id='$mC008[C008_Id]'
																  and D001_D060_Id='$mT007[D060_Id]'
															 group by D003_Id
															 order by sum(T008_Valor_Total_Preco) desc");

											   echo mysql_error();


											   while ($mT0071=mysql_fetch_array($T0071)) {

													 $retorno .= "<tr bgcolor='white'>";
													   $retorno .= "<td align=center>";
														 $retorno .= "<font class=td>&nbsp;</font>";
													   $retorno .= "</td>";
													   $retorno .= "<td align=center>";
														 $retorno .= "<font class=td>&nbsp;</font>";
													   $retorno .= "</td>";
													   if ($usaGrupo[0]>0){
														   $retorno .= "<td align=left>";
															 $retorno .= "<font class=td><b>{$mT0071[D003_Nome_Linha]}</b></font>";
														   $retorno .= "</td>";
														   $retorno .= "<td align=left>";
															 $retorno .= "<font class=td>&nbsp;</font>";
														   $retorno .= "</td>";
													   }
													   $retorno .= "<td align=right>";
														 $retorno .= "<font class=td><b>".number_format($mT0071[Valor_Total],2,',','.')."</b></font>";
													   $retorno .= "</td>";
													   $retorno .= "<td align=right>";
														 $retorno .= "<font class=td><b>".number_format(($mT0071[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</b></font>";
													   $retorno .= "</td>";
													 $retorno .= "</tr>";

														 $T0072=mysql_query("select D002_Id,
																				  D002_Descricao_Produto,
																				  sum(T008_Valor_Total_Preco) Valor_Total
																			 from T007
																		left join D006 on D006_Id=T007_D006_Id
																		left join T008 on T008_T007_Id=T007_Id
																		left join D009 on D009_Id=T008_D009_Id
																		left join D049 on D049_Id=D009_D049_Id
																		left join D001 as D1 on D001_Id=D049_D001_Id
																		left join D002 on D002_Id=D001_D002_Id
																			where T007_Data_Emissao>='$Data_Inicio'
																			  and T007_Data_Emissao<='$Data_Fim'
																			  and T007_D024_Id='$r_D024_Id'
																			  and D006_Flag_Venda_Compra_Outros='V'
																			  and D006_Flag_Devolucao='N'
																			  and T007_Flag_Cancelada!='S'
																			  and D002_Id is not null
																			  and D001_C008_Id='$mC008[C008_Id]'
																			  and D001_D060_Id='$mT007[D060_Id]'
																			  and D001_D003_Id='$mT0071[D003_Id]'
																		 group by D002_Id
																		 order by sum(T008_Valor_Total_Preco) desc");

														 echo mysql_error();

														   while ($mT0072=mysql_fetch_array($T0072)) {

																 $retorno .= "<tr bgcolor='white'>";
																   $retorno .= "<td align=center>";
																	 $retorno .= "<font class=td>&nbsp;</font>";
																   $retorno .= "</td>";
																   $retorno .= "<td align=center>";
																	 $retorno .= "<font class=td>&nbsp;</font>";
																   $retorno .= "</td>";
																   if ($usaGrupo[0]>0){
																	   $retorno .= "<td align=center>";
																		 $retorno .= "<font class=td>&nbsp;</font>";
																	   $retorno .= "</td>";
																	   $retorno .= "<td alig=left>";
																		 $retorno .= "<font class=td>$mT0072[D002_Descricao_Produto]</font>";
																	   $retorno .= "</td>";
																   }
																   $retorno .= "<td align=right>";
																	 $retorno .= "<font class=td>".number_format($mT0072[Valor_Total],2,',','.')."</font>";
																   $retorno .= "</td>";
																   $retorno .= "<td align=right>";
																	 $retorno .= "<font class=td>".number_format(($mT0072[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</font>";
																   $retorno .= "</td>";
																 $retorno .= "</tr>";

														   }


													}

											 }
									  } 
					 			   $retorno .= "<tr bgcolor='#f4f4f4'>";
								   if ($usaGrupo[0]>0){
									   $retorno .= "<td align=center colspan=4>";
										 $retorno .= "<font class=td><b>Total</font>";
									   $retorno .= "</td>";
								   } else {
									   $retorno .= "<td align=center colspan=2>";
										 $retorno .= "<font class=td><b>Total</font>";
									   $retorno .= "</td>";
								   }
								   $retorno .= "<td align=right>";
									 $retorno .= "<font class=td><b>".number_format($mTotal[Valor_Total],2,',','.')."</b></font>";
								   $retorno .= "</td>";
								   $retorno .= "<td align=center>";
									 $retorno .= "<font class=td><b></b></font>";
								   $retorno .= "</td>";
								 $retorno .= "</tr>";

							 	$retorno .= "</table>";
							$retorno .= <<<EOT
							</td>
						</tr>
						<tr>
							<td valign=top>
EOT;
							  $usaGrupo=mysql_query("select count(*)
												   from T003
											  left join D006 on D006_Id=T003_D006_Id
											  left join T004 on T004_T003_Id=T003_Id
											  left join T006 on T006_T004_Id=T004_Id
											  left join T008 on T008_T006_Id=T006_Id 
											  left join D009 on D009_Id=T004_D009_Id
											  left join D049 on D049_Id=D009_D049_Id
											  left join D001 on D001_Id=D049_D001_Id
											  left join D003 on D003_Id=D001_D003_Id
												  where T003_Data_Emissao>='$Data_Inicio'
													and T003_Data_Emissao<='$Data_Fim'
													and T003_D024_Id='$r_D024_Id'
													and T008_Id is null
													and D006_Flag_Venda_Compra_Outros='V'
													and D006_Flag_Devolucao='N'
													and D003_Id is not null");

							  $corSetor='white';
							  $fonteSetor='normal';
							  $usaGrupo=mysql_fetch_array($usaGrupo);
							  if ($usaGrupo[0]>0){
							  	  $corSetor='#fce3e6';
							  	  $fonteSetor='bold';
							  }

							  $totalProdutos=mysql_query("select sum(T004_Valor_Total_Preco) Valor_Total
												   from T003
											  left join D006 on D006_Id=T003_D006_Id
											  left join T004 on T004_T003_Id=T003_Id
											  left join T006 on T006_T004_Id=T004_Id
											  left join T008 on T008_T006_Id=T006_Id 
											  left join D009 on D009_Id=T004_D009_Id
											  left join D049 on D049_Id=D009_D049_Id
											  left join D001 on D001_Id=D049_D001_Id
												  where T003_Data_Emissao>='$Data_Inicio'
													and T003_Data_Emissao<='$Data_Fim'
													and T003_D024_Id='$r_D024_Id'
													and T008_Id is null
													and D006_Flag_Venda_Compra_Outros='V'
													and D006_Flag_Devolucao='N'");
		   					  $totalProdutos=mysql_fetch_array($totalProdutos);

							  $C008=mysql_query("select sum(T004_Valor_Total_Preco) Valor_Total,
														C008_Id,
														C008_Tipo
												   from T003
											  left join D006 on D006_Id=T003_D006_Id
											  left join T004 on T004_T003_Id=T003_Id
											  left join T006 on T006_T004_Id=T004_Id
											  left join T008 on T008_T006_Id=T006_Id 
											  left join D009 on D009_Id=T004_D009_Id
											  left join D049 on D049_Id=D009_D049_Id
											  left join D001 on D001_Id=D049_D001_Id
											  left join C008 on C008_Id=D001_C008_Id
												  where T003_Data_Emissao>='$Data_Inicio'
													and T003_Data_Emissao<='$Data_Fim'
													and T003_D024_Id='$r_D024_Id'
													and T008_Id is null
													and D006_Flag_Venda_Compra_Outros='V'
													and D006_Flag_Devolucao='N'
											   group by C008_Id
											   order by count(*) desc");

							   echo mysql_error();

							   $retorno .= "<table cellpadding=2 width=100% valign=top>";
								 $retorno .= "<tr bgcolor='#fc8a98'>";
								   $retorno .= "<td align=center colspan=11>";
									 $retorno .= "<font class=td style='font-weight:bold'>ORÇAMENTOS NÃO FECHADOS OU PERDIDOS $cAno</font>";
								   $retorno .= "</td>";
								 $retorno .= "</tr>";
								 $retorno .= "<tr bgcolor='#fc8a98'>";
								   $retorno .= "<td align=center>";
									 $retorno .= "<font class=td>Departamento</font>";
								   $retorno .= "</td>";
								   $retorno .= "<td align=center>";
									 $retorno .= "<font class=td>Setor</font>";
								   $retorno .= "</td>";
								   if ($usaGrupo[0]>0){
									   $retorno .= "<td align=center>";
										 $retorno .= "<font class=td>Grupo</font>";
									   $retorno .= "</td>";
									   $retorno .= "<td align=center>";
										 $retorno .= "<font class=td>Sub-Grupo</font>";
									   $retorno .= "</td>";
								   }
								   $retorno .= "<td align=right>";
									 $retorno .= "<font class=td>Total R$</font>";
								   $retorno .= "</td>";
								   $retorno .= "<td align=right>";
									 $retorno .= "<font class=td>&nbsp;</font>";
								   $retorno .= "</td>";
								 $retorno .= "</tr>";

								 echo mysql_error();
								 unset($mTotal);

								 while ($mC008=mysql_fetch_array($C008)) {

									 $retorno .= "<tr bgcolor='#f9c2c9'>";
									   $retorno .= "<td align=left>";
										 $retorno .= "<font class=td style='font-size:12px;'><b>{$mC008[C008_Tipo]}</b></font>";
									   $retorno .= "</td>";
									   $retorno .= "<td align=center>";
										 $retorno .= "<font class=td style='font-size:12px;'><b>&nbsp;</b></font>";
									   $retorno .= "</td>";
									   if ($usaGrupo[0]>0){
										   $retorno .= "<td align=center>";
											 $retorno .= "<font class=td style='font-size:12px;'><b>&nbsp;</b></font>";
										   $retorno .= "</td>";
										   $retorno .= "<td align=center>";
											 $retorno .= "<font class=td>&nbsp;</font>";
										   $retorno .= "</td>";
									   }
									   $retorno .= "<td align=right>";
										 $retorno .= "<font class=td style='font-size:12px;'><b>".number_format($mC008[Valor_Total],2,',','.')."</b></font>";
									   $retorno .= "</td>";
									   $retorno .= "<td align=right>";
										 $retorno .= "<font class=td style='font-size:12px;'><b>".number_format(($mC008[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</b></font>";
									   $retorno .= "</td>";
									 $retorno .= "</tr>";


									$T007=mysql_query("select sum(T004_Valor_Total_Preco) Valor_Total,
															  D060_Id,
															  D060_Descricao_Grupo
														 from T003
													left join D006 on D006_Id=T003_D006_Id
													left join T004 on T004_T003_Id=T003_Id
													left join T006 on T006_T004_Id=T004_Id
													left join T008 on T008_T006_Id=T006_Id 
													left join D009 on D009_Id=T004_D009_Id
													left join D049 on D049_Id=D009_D049_Id
													left join D001 on D001_Id=D049_D001_Id
													left join D060 on D060_Id=D001_D060_Id
														where T003_Data_Emissao>='$Data_Inicio'
														  and T003_Data_Emissao<='$Data_Fim'
														  and T003_D024_Id='$r_D024_Id'
														  and T008_Id is null
														  and D006_Flag_Venda_Compra_Outros='V'
														  and D006_Flag_Devolucao='N'
														  and D001_C008_Id='$mC008[C008_Id]'
													 group by D060_Id
													 order by sum(T004_Valor_Total_Preco) desc");

									 echo mysql_error();


									   while ($mT007=mysql_fetch_array($T007)) {

											 $retorno .= "<tr bgcolor='$corSetor' style='font-weight:$fonteSetor'>";
											   $retorno .= "<td align=center>";
												 $retorno .= "<font class=td style='font-size:12px;'>&nbsp;</font>";
											   $retorno .= "</td>";
											   $retorno .= "<td align=left>";
												 $retorno .= "<font class=td style='font-size:12px;font-weight:$fonteSetor'>{$mT007[D060_Descricao_Grupo]}</font>";
											   $retorno .= "</td>";
											   if ($usaGrupo[0]>0){
												   $retorno .= "<td align=center>";
													 $retorno .= "<font class=td style='font-size:12px;'>&nbsp;</font>";
												   $retorno .= "</td>";
												   $retorno .= "<td align=center>";
													 $retorno .= "<font class=td>&nbsp;</font>";
												   $retorno .= "</td>";
											   }
											   $retorno .= "<td align=right>";
												 $retorno .= "<font class=td style='font-size:12px;font-weight:$fonteSetor'>".number_format($mT007[Valor_Total],2,',','.')."</font>";
											   $retorno .= "</td>";
											   $retorno .= "<td align=right>";
												 $retorno .= "<font class=td style='font-size:12px;font-weight:$fonteSetor'>".number_format(($mT007[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</font>";
											   $retorno .= "</td>";
											 $retorno .= "</tr>";

											 $mTotal[Notas]      += $mT007[Notas];
											 $mTotal[Clientes]   += $mT007[Clientes];
											 $mTotal[Itens]      += $mT007[Itens];
											 $mTotal[Valor_Total]+= $mT007[Valor_Total];

											 $T0071=mysql_query("select D003_Nome_Linha,
																	  D003_Id,
																	  sum(T004_Valor_Total_Preco) Valor_Total
																 from T003
															left join D006 on D006_Id=T003_D006_Id
															left join T004 on T004_T003_Id=T003_Id
															left join T006 on T006_T004_Id=T004_Id
															left join T008 on T008_T006_Id=T006_Id 
															left join D009 on D009_Id=T004_D009_Id
															left join D049 on D049_Id=D009_D049_Id
															left join D001 as D1 on D001_Id=D049_D001_Id
															left join D003 on D003_Id=D001_D003_Id
																where T003_Data_Emissao>='$Data_Inicio'
																  and T003_Data_Emissao<='$Data_Fim'
																  and T003_D024_Id='$r_D024_Id'
																  and T008_Id is null
																  and D006_Flag_Venda_Compra_Outros='V'
																  and D006_Flag_Devolucao='N'
																  and D003_Id is not null
																  and D001_C008_Id='$mC008[C008_Id]'
																  and D001_D060_Id='$mT007[D060_Id]'
															 group by D003_Id
															 order by sum(T004_Valor_Total_Preco) desc");

											   echo mysql_error();

											   while ($mT0071=mysql_fetch_array($T0071)) {

													 $retorno .= "<tr bgcolor='white'>";
													   $retorno .= "<td align=center>";
														 $retorno .= "<font class=td>&nbsp;</font>";
													   $retorno .= "</td>";
													   $retorno .= "<td align=center>";
														 $retorno .= "<font class=td>&nbsp;</font>";
													   $retorno .= "</td>";
													   if ($usaGrupo[0]>0){
														   $retorno .= "<td align=left>";
															 $retorno .= "<font class=td><b>{$mT0071[D003_Nome_Linha]}</b></font>";
														   $retorno .= "</td>";
														   $retorno .= "<td align=left>";
															 $retorno .= "<font class=td>&nbsp;</font>";
														   $retorno .= "</td>";
													   }
													   $retorno .= "<td align=right>";
														 $retorno .= "<font class=td><b>".number_format($mT0071[Valor_Total],2,',','.')."</b></font>";
													   $retorno .= "</td>";
													   $retorno .= "<td align=right>";
														 $retorno .= "<font class=td><b>".number_format(($mT0071[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</b></font>";
													   $retorno .= "</td>";
													 $retorno .= "</tr>";

														 $T0072=mysql_query("select D002_Id,
																				  D002_Descricao_Produto,
																				  sum(T004_Valor_Total_Preco) Valor_Total
																			 from T003
																		left join D006 on D006_Id=T003_D006_Id
																		left join T004 on T004_T003_Id=T003_Id
																		left join T006 on T006_T004_Id=T004_Id
																		left join T008 on T008_T006_Id=T006_Id 
																		left join D009 on D009_Id=T004_D009_Id
																		left join D049 on D049_Id=D009_D049_Id
																		left join D001 as D1 on D001_Id=D049_D001_Id
																		left join D002 on D002_Id=D001_D002_Id
																			where T003_Data_Emissao>='$Data_Inicio'
																			  and T003_Data_Emissao<='$Data_Fim'
																			  and T003_D024_Id='$r_D024_Id'
																			  and T008_Id is null
																			  and D006_Flag_Venda_Compra_Outros='V'
																			  and D006_Flag_Devolucao='N'
																			  and D002_Id is not null
																			  and D001_C008_Id='$mC008[C008_Id]'
																			  and D001_D060_Id='$mT007[D060_Id]'
																			  and D001_D003_Id='$mT0071[D003_Id]'
																		 group by D002_Id
																		 order by sum(T004_Valor_Total_Preco) desc");

														 echo mysql_error();

														   while ($mT0072=mysql_fetch_array($T0072)) {

																 $retorno .= "<tr bgcolor='white'>";
																   $retorno .= "<td align=center>";
																	 $retorno .= "<font class=td>&nbsp;</font>";
																   $retorno .= "</td>";
																   $retorno .= "<td align=center>";
																	 $retorno .= "<font class=td>&nbsp;</font>";
																   $retorno .= "</td>";
																   if ($usaGrupo[0]>0){
																	   $retorno .= "<td align=center>";
																		 $retorno .= "<font class=td>&nbsp;</font>";
																	   $retorno .= "</td>";
																	   $retorno .= "<td alig=left>";
																		 $retorno .= "<font class=td>$mT0072[D002_Descricao_Produto]</font>";
																	   $retorno .= "</td>";
																   }
																   $retorno .= "<td align=right>";
																	 $retorno .= "<font class=td>".number_format($mT0072[Valor_Total],2,',','.')."</font>";
																   $retorno .= "</td>";
																   $retorno .= "<td align=right>";
																	 $retorno .= "<font class=td>".number_format(($mT0072[Valor_Total]/$totalProdutos[Valor_Total])*100,2,',','.')."%</font>";
																   $retorno .= "</td>";
																 $retorno .= "</tr>";

														   }


													}

											 }
									  } 
								   $retorno .= "<tr bgcolor='#f9c2c9'>";
								   if ($usaGrupo[0]>0){
									   $retorno .= "<td align=center colspan=4>";
										 $retorno .= "<font class=td><b>Total</font>";
									   $retorno .= "</td>";
								   } else {
									   $retorno .= "<td align=center colspan=2>";
										 $retorno .= "<font class=td><b>Total</font>";
									   $retorno .= "</td>";
								   }
								   $retorno .= "<td align=right>";
									 $retorno .= "<font class=td><b>".number_format($mTotal[Valor_Total],2,',','.')."</b></font>";
								   $retorno .= "</td>";
								   $retorno .= "<td align=center>";
									 $retorno .= "<font class=td><b></b></font>";
								   $retorno .= "</td>";
								 $retorno .= "</tr>";

							 	$retorno .= "</table>";
				$retorno .= <<<EOT
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
EOT;

		echo $retorno;
	break;


	case 'crm001ResumoClienteTeste':
		// Mostra informações gerênciais do CRM

		//-- Obtem o primeiro dia do mes se o mesmo nao estiver preenchido
		$cMes='01';
		$cAno=date("Y",time());
		$xData_Inicial=date("Y-m-01", mktime(0, 0, 0, $cMes, 1, $cAno));
		//-- Obtem o ultimo dia do mes se o mesmo nao estiver preenchido
		$cMes=date("m",time());
		$cAno=date("Y",time());
		$xData_Final=date("Y-m-t", mktime(0, 0, 0, $cMes, 1, $cAno));

		$Data_Inicio=gCorrigeData($xData_Inicial);
		$Data_Fim=gCorrigeData($xData_Final);
		$D024_Id=$r_D024_Id;

	   $D024=mysql_query("select D024_Nome_Empresa,D024_Data_Cadastro from D024 where D024_Id='$D024_Id'");
	   $mD024=mysql_fetch_array($D024);
	   
	   $T007=mysql_query("select T007_Valor_Total,
								 T007_Data_Emissao
							from T007
						   where T007_D024_Id='$D024_Id'
							 and T007_Data_Emissao>='$xData_Inicial'
							 and T007_Data_Emissao<='$xData_Final'
							 and T007_Flag_Cancelada='N'
						order by T007_Valor_Total desc, T007_Data_Emissao desc
						   limit 1");
	   $mT007_Maior_Compra=mysql_fetch_array($T007);

	   $T005=mysql_query("select T005_Valor_Total,
								 T005_Data_Emissao
							from T005
						   where T005_D024_Id='$D024_Id'
							 and T005_Data_Emissao>='$xData_Inicial'
							 and T005_Data_Emissao<='$xData_Final'
							 and (T005_Flag_Status='7' or T005_Flag_Status='11')
							 and T005_Flag_Situacao='1'
						order by T005_Valor_Total desc
						   limit 1");
	   $mT005_Maior_Compra=mysql_fetch_array($T005);
	   
/*	   $T008=mysql_query("(select T008_Codigo_Produto,
								 T008_Descricao_Produto,
								 sum(T008_Quantidade) as T008_Quantidade,
								 sum(T008_Valor_Total_Preco) as T008_Faturado,
								 T008_Unidade
							from T007
					   left join T008 on T008_T007_Id=T007_Id
						   where T007_D024_Id='$D024_Id'
							 and T007_Data_Emissao>='$xData_Inicial'
							 and T007_Data_Emissao<='$xData_Final'
							 and T007_Flag_Cancelada='N'
						group by T008_Codigo_Produto,T008_Unidade
						order by T008_Descricao_Produto)

						union

						(select T006_Codigo_Produto as T008_Codigo_Produto,
								 T006_Descricao_Produto as T008_Descricao_Produto,
								 sum(T006_Quantidade) as T008_Quantidade,
								 sum(T006_Valor_Total_Preco) as T008_Faturado,
								 D037_Unidade as T008_Unidade
							from T005
					   left join T006 on T006_T005_Id=T005_Id
					   left join D037 on T006_D037_Id=D037_Id
						   where T005_D024_Id='$D024_Id'
							 and T005_Data_Emissao>='$xData_Inicial'
							 and T005_Data_Emissao<='$xData_Final'
							 and (T005_Flag_Status='7' or T005_Flag_Status='11')
							 and T005_Flag_Situacao='1'
						group by T006_Codigo_Produto,D037_Unidade
						order by T006_Descricao_Produto)
						order by T008_Descricao_Produto"); */


	   $T007_Mes=mysql_query("select Concat(Month(T007_Data_Emissao),'/',Year(T007_Data_Emissao)),
								 sum(T007_Valor_Total) as Total_Mensal
							from T007
						   where T007_D024_Id='$D024_Id'
							 and T007_Data_Emissao>='$xData_Inicial'
							 and T007_Data_Emissao<='$xData_Final'
							 and T007_Flag_Cancelada='N'
						group by Year(T007_Data_Emissao),Month(T007_Data_Emissao)
						order by Year(T007_Data_Emissao),Month(T007_Data_Emissao)");
						
	   echo mysql_error();

	   $T005_Mes=mysql_query("select Concat(Month(T005_Data_Emissao),'/',Year(T005_Data_Emissao)),
									 sum(T005_Valor_Total) Total_Mensal
									from T005
								   where T005_D024_Id='$D024_Id'
									 and T005_Data_Emissao>='$xData_Inicial'
									 and T005_Data_Emissao<='$xData_Final'
									 and (T005_Flag_Status='7' or T005_Flag_Status='11')
									 and T005_Flag_Situacao='1'
									 group by Year(T005_Data_Emissao),Month(T005_Data_Emissao)");

	   echo mysql_error();

	   $T007=mysql_query("select T007_Valor_Total,
								 T007_Data_Emissao
							from T007
						   where T007_D024_Id='$D024_Id'
							 and T007_Data_Emissao>='$xData_Inicial'
							 and T007_Data_Emissao<='$xData_Final'
							 and T007_Flag_Cancelada!='S'
							 and T007_Valor_Total>0
						order by T007_Data_Emissao desc
						   limit 1");
	   $mT007_Ultima_Compra=mysql_fetch_array($T007);

	   $T005=mysql_query("select T005_Valor_Total,
								 T005_Data_Emissao
							from T005
						   where T005_D024_Id='$D024_Id'
							 and T005_Data_Emissao>='$xData_Inicial'
							 and T005_Data_Emissao<='$xData_Final'
							 and (T005_Flag_Status='7' or T005_Flag_Status='11')
							 and T005_Flag_Situacao='1'
						order by T005_Data_Emissao desc
						   limit 1");
	   $mT005_Ultima_Compra=mysql_fetch_array($T005);

	   $T002=mysql_query("select count(*)
							from T002
						   where T002_D024_Id='$D024_Id'
							 and T002_Data_Emissao>='$xData_Inicial'
							 and T002_Data_Emissao<='$xData_Final'
							 and T002_Flag_Cancelada='N'");
	   $mT002_Total=mysql_fetch_array($T002);

	   $T005=mysql_query("select count(*)
							from T005
						   where T005_D024_Id='$D024_Id'
							 and T005_Data_Emissao>='$xData_Inicial'
							 and T005_Data_Emissao<='$xData_Final'
							 and (T005_Flag_Status='7' or T005_Flag_Status='11')
							 and T005_Flag_Situacao='1'");
	   $mT005_Total=mysql_fetch_array($T005);

	   $T002_Total_Aberto=mysql_query("select sum(T002_Valor_Total)
							from T002
						   where T002_Data_Recebimento='0000-00-00'
							 and T002_Data_Emissao>='$xData_Inicial'
							 and T002_Data_Emissao<='$xData_Final'
							 and T002_D024_Id='$D024_Id'
							 and T002_Flag_Cancelada='N'");
	   $mT002_Total_Aberto=mysql_fetch_array($T002_Total_Aberto);

	   $T005_Total_Romaneio=mysql_query("select sum(T005_Valor_Total)
							from T005
						   where T005_Data_Emissao>='$xData_Inicial'
							 and T005_Data_Emissao<='$xData_Final'
							 and T005_D024_Id='$D024_Id'
							 and (T005_Flag_Status='7' or T005_Flag_Status='11')
							 and T005_Flag_Situacao='1'");
	   echo mysql_error();
	   $mT005_Total_Romaneio=mysql_fetch_array($T005_Total_Romaneio);
	   
	   $T002_Total_Atrasado=mysql_query("select sum(T002_Valor_Total) as t1
							from T002
						   where current_date()>T002_Data_Vencimento
							 and T002_Data_Recebimento='0000-00-00'
							 and T002_Data_Emissao>='$xData_Inicial'
							 and T002_Data_Emissao<='$xData_Final'
							 and T002_D024_Id='$D024_Id'
							 and T002_Flag_Cancelada='N'");
	   echo mysql_error();
	   $mT002_Total_Atrasado=mysql_fetch_array($T002_Total_Atrasado);
	   


	   $T002_Media_Atraso=mysql_query("select count(*),
											  sum(datediff(T002_Data_Recebimento,T002_Data_Vencimento))
							from T002
						   where T002_Data_Recebimento>T002_Data_Vencimento
							 and T002_Data_Emissao>='$xData_Inicial'
							 and T002_Data_Emissao<='$xData_Final'
							 and T002_D024_Id='$D024_Id'
							 and T002_Flag_Cancelada='N'");
	   $mT002_Media_Atraso=mysql_fetch_array($T002_Media_Atraso);
	   
	   $T002_Maior_Atraso=mysql_query("select datediff(T002_Data_Recebimento,T002_Data_Vencimento)
							from T002
						   where T002_Data_Recebimento>T002_Data_Vencimento
							 and T002_D024_Id='$D024_Id'
							 and T002_Data_Emissao>='$xData_Inicial'
							 and T002_Data_Emissao<='$xData_Final'
							 and T002_Flag_Cancelada='N'
						order by datediff(T002_Data_Recebimento,T002_Data_Vencimento) desc");
	   $mT002_Maior_Atraso=mysql_fetch_array($T002_Maior_Atraso);

	   echo mysql_error();

	   $T002_Media_EmDia=mysql_query("select count(*)
							from T002
						   where T002_Data_Recebimento<=T002_Data_Vencimento
							 and T002_Data_Emissao>='$xData_Inicial'
							 and T002_Data_Emissao<='$xData_Final'
							 and T002_D024_Id='$D024_Id'
							 and T002_Flag_Cancelada='N'");
	   $mT002_Media_EmDia=mysql_fetch_array($T002_Media_EmDia);

	   $T002=mysql_query("select D027_Portador,
								 count(*)
							from T002
					   left join D027 on D027_Id=T002_D027_Id
						   where T002_D024_Id='$D024_Id'
							 and T002_Data_Emissao>='$xData_Inicial'
							 and T002_Data_Emissao<='$xData_Final'
							 and T002_Flag_Cancelada='N'
						group by T002_D027_Id");
						
	   $T002_Prazo=mysql_query("select DateDiff(T002_Data_Vencimento,T002_Data_Emissao),
								 count(*)
							from T002
						   where T002_D024_Id='$D024_Id'
							 and T002_Data_Emissao>='$xData_Inicial'
							 and T002_Data_Emissao<='$xData_Final'
							 and T002_Flag_Cancelada='N'
						group by DateDiff(T002_Data_Vencimento,T002_Data_Emissao)");

		echo  <<<EOT
		<style type="text/css">
		.table{
			font-weight: normal;
			font-size: 12px;
			font-family: arial, verdana;
			padding: 3px 3px 3px 3px;
		}
		</style>
EOT;
		echo "<table cellspacing=0px cellpadding=0px>";
			/* echo "<tr align=left>";
			   echo "<td bgcolor='lightblue' colspan=5 style='font-weight:bold;padding:3px 3px 3px 3px'>";
					echo  "PERÍODO DE $Data_Inicio a $Data_Fim";
			   echo "</td>";
			 echo "</tr>"; */
			echo "<tr>";
				echo "<td valign=top>";
				   echo "<table class=table valign=top cellpadding=3px height=100%>";
/*				     echo "<tr align=left>";
					   echo "<td bgcolor='lightblue' width=145>";
						  echo 'Data cadastro';
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo gCorrigeData($mD024[D024_Data_Cadastro])."&nbsp;";
					   echo "</td>";
					 echo "</tr>";
					 echo "<tr align=left>";
					   echo "<td  bgcolor='lightblue'>";
						  echo "Maior Compra";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "R$ ".number_format($mT007_Maior_Compra[0],2,',','.')." em ".gCorrigeData($mT007_Maior_Compra[1])."";
						  if ($mT005_Maior_Compra[0]>0)
						  {
							 echo "&nbsp;&nbsp;&nbsp;";
							 echo "  (Romaneio R$ ".number_format($mT005_Maior_Compra[0],2,',','.')." em ".gCorrigeData($mT005_Maior_Compra[1]).")";
						  }
					   echo "</td>";
					 echo "</tr>";
					 echo "<tr align=left bgcolor='white'>";
					   echo "<td  bgcolor='lightblue'>";
						  echo "Última Compra";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "R$ ".number_format($mT007_Ultima_Compra[0],2,',','.')." em ".gCorrigeData($mT007_Ultima_Compra[1])."";
						  if ($mT005_Ultima_Compra[0]>0)
						  {
							 echo "&nbsp;&nbsp;&nbsp;";
							 echo "  (Romaneio R$ ".number_format($mT005_Ultima_Compra[0],2,',','.')." em ".gCorrigeData($mT005_Ultima_Compra[1]).")";
						  }
					   echo "</td>";
					 echo "</tr>";
					 echo "<tr align=left>";
					   echo "<td bgcolor='lightblue'>";
						  echo "Maior atraso";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "$mT002_Maior_Atraso[0] dias";
					   echo "</td>";
					 echo "</tr>";
					 echo "<tr align=left>";
					   echo "<td bgcolor='lightblue'>";
						  echo "Total a pagar";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "R$ ".number_format($mT002_Total_Aberto[0],2,',','.');
						  if ($mT005_Total_Romaneio[0]>0)
						  {
							 echo " + (Romaneios: R$ ".number_format($mT005_Total_Romaneio[0],2,',','.').") ";
						  }
						  echo "";
					   echo "</td>";
					 echo "</tr>";
					 echo "<tr align=left>";
					   echo "<td bgcolor='lightblue'>";
						  echo "Total atrasado";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "R$ ".number_format($mT002_Total_Atrasado[0],2,',','.')."";
					   echo "</td>";
					 echo "</tr>"; */
					 echo "<tr align=left  bgcolor='white'>";
					   echo "<td bgcolor='lightblue'>";
						  echo "Compras por mês (NF)";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "<table bgcolor='white' cellspacing=1 cellpadding=2>";
						  echo "<tr bgcolor=white>
								   <td>Mês</td>
								   <td align=right width=80>NF</td>
								</tr>" ;
						  while ($mT007_Mes=mysql_fetch_array($T007_Mes))
						  {
								 echo "<tr bgcolor=white>
										  <td>$mT007_Mes[0]</td>
										  <td align=right width=80> ".number_format($mT007_Mes[Total_Mensal],2,',','.')."  </td>
									   </tr>" ;
						  }
						  echo "</table>";
					   echo "</td>";
					 echo "</tr>";
					 if (mysql_num_rows($T005_Mes)>0)
					 {
						 echo "<tr align=left  bgcolor='white'>";
						   echo "<td bgcolor='lightblue'>";
							  echo "Compras por mês (Romaneio)";
						   echo "</td>";
						   echo "<td bgcolor='white'>";
							  echo "<table bgcolor='white' cellspacing=1 cellpadding=2>";
							  echo "<tr bgcolor=white>
									   <td>Mês</td>
									   <td align=right width=80>Romaneios</td>
									</tr>" ;
							  while ($mT005_Mes=mysql_fetch_array($T005_Mes))
							  {
									 echo "<tr bgcolor=white>
											  <td>$mT005_Mes[0]</td>
											  <td align=right width=80> ".number_format($mT005_Mes[Total_Mensal],2,',','.')."  </td>
										   </tr>" ;
							  }
							  echo "</table>";
						   echo "</td>";
						 echo "</tr>";
					 }
				   echo "</table>";
				echo "</td>";
				echo "<td valign=top>";
				   echo "<table class=table valign=top cellpadding=3px>";
					 echo "<tr align=left bgcolor='white'>";
					   echo "<td bgcolor='lightblue'>";
						  echo "Forma de pagamento";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "<table>";
						  while ($mT002=mysql_fetch_array($T002)) {
								 echo "<tr><td align=right>".number_format(($mT002[1]/$mT002_Total[0])*100,2,',','.')."% </td><td> $mT002[0]</td></tr>";
						  }
						  echo "</table>";
					   echo "</td>";
					 echo "</tr>";
					 echo "<tr align=left>";
					   echo "<td bgcolor='lightblue'>";
						  echo "Prazo de pagamento";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "<table>";
						  while ($mT002_Prazo=mysql_fetch_array($T002_Prazo)) {
								 echo "<tr><td align=right>".number_format(($mT002_Prazo[1]/$mT002_Total[0])*100,2,',','.')."% </td><td>$mT002_Prazo[0] dias</td></tr>";
						  }
						  echo "</table>";
					   echo "</td>";
					 echo "</tr>";
					 echo "<tr align=left>";
					   echo "<td bgcolor='lightblue'>";
						  echo "Pontualidade";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "<table>";
							 if ($mT002_Media_Atraso[0]>0 and $mT002_Total[0]>0)
							 {
								 echo "<tr><td align=right>".number_format(($mT002_Media_Atraso[0]/$mT002_Total[0])*100,2,',','.')."% </td><td> Pago Atrasado (média dias ".number_format($mT002_Media_Atraso[1]/$mT002_Media_Atraso[0],0).")</td></tr>";
							 }
							 if ($mT002_Media_EmDia[0]>0 and $mT002_Total[0]>0)
							 {
								 echo "<tr><td align=right>".number_format(($mT002_Media_EmDia[0]/$mT002_Total[0])*100,2,',','.')."% </td><td> Pago em dia</td></tr>";
							 }
						  echo "</table>";
					   echo "</td>";
					 echo "</tr>";
			/*	     echo "<tr align=left>";
					   echo "<td bgcolor='lightblue'>";
						  echo "Principais Produtos";
					   echo "</td>";
					   echo "<td bgcolor='white'>";
						  echo "<table bgcolor='white' cellspacing=1>";
						  $count=0;
						  echo "<tr bgcolor=white><td align=center>Código</td><td width=300>Descrição</td><td align=right>Quantidade</td><td align=center>Und</td><td align=right>Faturado</td></tr>" ;
						  while ($mT008=mysql_fetch_array($T008)) {
								 echo "<tr bgcolor=white><td align=left>$mT008[0]</td><td width=300>".substr($mT008[1],0,40)."</td><td align=right> ".number_format($mT008[T008_Quantidade],2,',','.')."  </td><td align=center>$mT008[T008_Unidade]</td><td align=right> ".number_format($mT008[T008_Faturado],2,',','.')."  </td></tr>" ;
								 $nTotal_Geral+=$mT008[T008_Faturado];
						  }
						  echo "<tr bgcolor=white><td colspan=2><font class=td_local  style='font-weight:bold'>".mysql_num_rows($T008)." produtos fornecidos</td><td align=right width=80></td><td align=right width=80></td><td align=right width=80><font class=td_local style='font-weight:bold'>".number_format($nTotal_Geral,2,',','.')."</td></tr>" ;
						  echo "</table>";
					   echo "</td>";
					 echo "</tr>"; */
				   echo "</table>";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
	echo "</div>";
	break;

	case 'crm001CabecalhoTabelaPrecoCliente':
		global $g;
		$resultado = mysql_query("SELECT * FROM D024
									  LEFT JOIN D161 ON D161_D024_Id=D024_Id
									  LEFT JOIN D020 ON D020_Id=D024_D020_Id
									  LEFT JOIN D018 ON D018_Id=D024_D018_Id 
										  WHERE D161_Id = '{$r_acaoId}'");
		$row = mysql_fetch_assoc($resultado);
		foreach ($row as &$row_) {
			if (trim($row_)=="") {
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}

	  	$Contribuinte = "<font color='red'><b> - </b></font>";
		if($row['D024_Flag_Contribuinte']=="0"){
			$Contribuinte = "<font color='blue'><b>NÃO CONTRIB.</b></font>";
		} else if($row['D024_Flag_Contribuinte']=="1"){
			$Contribuinte = "<font color='blue'><b>CONSUM.FINAL</b></font>";
		} else if($row['D024_Flag_Contribuinte']=="2"){
			$Contribuinte = "<font color='blue'><b>REVENDA</b></font>";
		} else if($row['D024_Flag_Contribuinte']=="3"){
			$Contribuinte = "<font color='blue'><b>PROD.RURAL</b></font>";
		} else if($row['D024_Flag_Contribuinte']=="4"){
			$Contribuinte = "<font color='blue'><b>SIMPLES</b></font>";
		} else if($row['D024_Flag_Contribuinte']=="5"){
			$Contribuinte = "<font color='blue'><b>INDUSTRIA</b></font>";
		} else if ($row['D024_Flag_Contribuinte']==""){
			$Contribuinte="<font color='red'>FALTA</font>";
		}

		switch($row['D024_Flag_Lista_Preco']){
			case '1':
				$listaPreco="Preço 1";
				break;
			case '2':
				$listaPreco="Preço 2";
				break;
			case '3':
				$listaPreco = "Preço 3";
				break;
			case '4':
				$listaPreco = "Preço 4";
				break;
			case '5':
				$listaPreco = "Preço 5";
				break;
			default:
				$listaPreco="<font color='red'><b>FALTA</b></font>";
		}


		$dataEmissao = gCorrigeData($row['T003_Data_Emissao']);
		if($g['C031']['campoNomeEmpresa'] == 'fantasia'){
			$D024_Nome_Empresa = gTruncar($row['D024_Nome_Fantasia'], 24, ' ');
		} else {
			$D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 24, ' ');
		}
		$D024_Nome_Empresa = $D024_Nome_Empresa.' '.$row['D024_Cnpj'];
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 15);
		$row['D024_Telefone_1'] = str_replace(array(' ', '.', '-'), '', $row['D024_Telefone_1']);
		$D024_Telefone = strpos($row['D024_Telefone_1'], "FALTA") ? '<span style="color: var(--BGVermelho);">FALTA</span>' : gTruncar($row['D024_DDD_Telefone_1'].' '.$row['D024_Telefone_1'], 20);


		$urlCadastroCliente = <<<EOT
			onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cliente', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', 'auto');"
EOT;
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc" style="cursor:pointer;" {$urlCadastroCliente}>
						<td min-width='10%' align="center">Código</td>
						<td min-width='35%'>Cliente</td>
						<td min-width='15%'>Cidade</td>
						<td min-width='5%'>UF</td>
						<td min-width='10%'>Tipo</td>
						<td min-width='5%'>Lista</td>
					</tr>
					<tr style="font-size:1.1em; font-weight:bold; cursor:pointer;" {$urlCadastroCliente}>
						<td style='padding:1px 10px 4px 2px;' align="center">{$row['D024_Id']}</td>
						<td style='padding:1px 10px 4px 2px;'>{$D024_Nome_Empresa}</td>
						<td style='padding:1px 10px 4px 2px;'>{$D020_Nome_Cidade}</td>
						<td style='padding:1px 10px 4px 2px;'>{$row['D018_UF']}</td>
						<td style='padding:1px 10px 4px 2px;'>{$Contribuinte}</td>
						<td style='padding:1px 10px 4px 2px;'>{$listaPreco}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'Magento1GetOrdersOauth':
	  	global $g;

	  	//ini_set('display_errors',1);
		//ini_set('display_startup_erros',1);
		//error_reporting(E_ALL);
	  
	 	$atualizacao=$g['C031']['IntervaloDeAtualizacao']*60;
		
		echo '<meta HTTP-EQUIV="refresh" CONTENT="'.$atualizacao.'";URL="http://'.$_SERVER['HTTP_HOST'].'/crm/crm001/outro/Magento1GetOrders?ajax=true">';

		echo "<h2>Importação Magento</h2><br>";
		$date = date('y-m-d H:i');

		echo "<b>Inicio - $date </b><br>";
	
		require_once("bibliotecas/classes/VEN014.php");
		$VEN014 = new VEN014();

		// $callbackUrl is a path to your file with OAuth authentication example for the Admin user
		$callbackUrl = "http://".$_SERVER['HTTP_HOST']."/crm/crm001/outro/Magento1GetOrders?ajax=true";
		// //oAuth Initiate URL
		$temporaryCredentialsRequestUrl=$g['C031']['MagentoUrlCredencial'].urlencode($callbackUrl);
		// //oAuth Authorize
		$adminAuthorizationUrl = $g['C031']['MagentoUrlAutorizacao'];
		//oAuth Token URL
		$accessTokenRequestUrl = $g['C031']['MagentoUrlRequisicao'];
		//API Url
		$apiUrl = $g['C031']['MagentoUrlApi'];
		// Consumer Key hardness
		$consumerKey = $g['C031']['MagentoConsumerKEy'];
		//Consumer Secret
		$consumerSecret = $g['C031']['MagentoConsumerSecret'];
		//Status dos pedidos que serão trazidos para o sistema
		$status_pedido = $g['C031']['MagentoStatusPedido'];

		if (!isset($_GET['oauth_token']) && isset($_SESSION['state']) && $_SESSION['state'] == 1) 
		{
			$_SESSION['state'] = 0;
		}
		try 
		{
			$authType = ($_SESSION['state'] == 2) ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
			$oauthClient = new \OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, $authType);
			$oauthClient->enableDebug();

			if (!isset($_GET['oauth_token']) && !$_SESSION['state']) 
			{
				$requestToken = $oauthClient->getRequestToken("$temporaryCredentialsRequestUrl");
				$_SESSION['secret'] = $requestToken['oauth_token_secret'];
				$_SESSION['state'] = 1;
				header('Location: ' . $adminAuthorizationUrl . '?oauth_token=' . $requestToken['oauth_token']);
				exit;
			} 
			else if ($_SESSION['state'] == 1) 
			{
				$oauthClient->setToken($_GET['oauth_token'], $_SESSION['secret']);
				$accessToken = $oauthClient->getAccessToken("$accessTokenRequestUrl");
				$_SESSION['state'] = 2;
				$_SESSION['token'] = $accessToken['oauth_token'];
				$_SESSION['secret'] = $accessToken['oauth_token_secret'];
				header('Location: ' . $callbackUrl);
				exit;
			} 
			else 
			{
			

				$oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);

				$resourceUrl = "$apiUrl/orders?filter[1][attribute]=status&filter[1][in]=".$status_pedido ;

				$oauthClient->fetch("$resourceUrl", array(), 'GET', array('Accept' => '*/*'));

				$listOrders = $oauthClient->getLastResponse();
				
			}
		} 
		catch (OAuthException $e) 
		{
			print_r($e->getMessage());
			echo "<br/>";
			print_r($e->lastResponse);
		}

		$listOrders = json_decode($listOrders, true);

		$count = 0;		
		$fail=0;
		$sucess=0;
		foreach ($listOrders as $key => $order) {

			$count++;
			$items = (array) $order;
			$address = (array) $order['addresses'];
			$address_billing = (array) $address[0];
			$address_shipping = (array) $address[1];
			$itens = (array) $items['order_items'];

			$D024 = array();	
			$T005 = array();
			$D148 = array();
			$T006 = array();


			//buscar no magento os dados do cliente do pedido
			$resourceUrl = "$apiUrl/customers/".$order['customer_id'];
			$oauthClient->fetch($resourceUrl, array(), 'GET', array('Accept' => '*/*'));
			$customer = $oauthClient->getLastResponse();
			$customer = json_decode($customer, true);

			//tirar o número  da string endereço
			$D024nEndereco = preg_replace("/[^0-9]/", "",$address_billing['street']);		
			
	
			//retirando dados do cliente		
			if(strlen($customer['taxvat'])<=14){
				$D024["D024_Cpf"]=str_replace(array('.', '/', '-'), '', trim($customer['taxvat']));
				$D024["D024_Cnpj"]='';
			}else{
				$D024["D024_Cpf"]='';
				$D024["D024_Cnpj"]=str_replace(array('.', '/', '-'), '', trim($customer['taxvat']));
			}
			$D024["D024_Nome_Fantasia"]=$address_billing['firstname'].' '.$address_billing['lastname'];
			$D024["D024_Nome_Empresa"]=$address_billing['firstname'].' '.$address_billing['lastname'];
			$D024["Razao_Social"]=$address_billing['firstname'].' '.$address_billing['lastname'];
			$D024["D024_Endereco"]=$address_billing['street'];
			$D024["D024_Numero"]=$D024nEndereco;
			$D024["D024_Cep"]=$address_billing['postcode'];
			$D024["D024_Complemento"]='';
			$D024["D024_Bairro"]='';
			$D024["Cidade"]=$address_billing['city'];
			$D024["UF"]=$address_billing['region'];
			$D024['Pais']=$address_billing['country_id'];
			$D024["D024_DDD_Telefone_1"]=$address_billing['telephone'];
			$D024["D024_Telefone_1"]='';
			$D024["D024_DDD_Telefone_2"]='';
			$D024["D024_Telefone_2"]='';
			$D024["D024_E_Mail"]=$address_billing['email'];
			$D024["Referencia_Endereco"]='';
			$D024["Informacao_adicional"]='';
	
			//endereço de entrega	
			$D148nEndereco = preg_replace("/[^0-9]/", "", $address_billing['street']);
					
			$D148["UF"]=$address_shipping['region'];
			$D148["Cidade"]=$address_shipping['city'];
			$D148["Pais"]=$address_shipping['country_id'];
			$D148["D148_Nome"]=$address_shipping['firstname'].' '.$address_shipping['lastname'];
			$D148["D148_Cep"]=$address_shipping['postcode'];
			$D148["D148_Rua"]=$address_shipping['street'];
			$D148["D148_Numero"]=$D148nEndereco;
			$D148["D148_Complemento"]='';
			$D148["D148_Bairro"]='';
			$D148["D148_DDD_Telefone"]='';
			$D148["D148_Telefone"]=$address_shipping['telephone'];
			$D148["D148_DDD_Fax"]='';
			$D148["D148_Fax"]='';
			$D148["D148_Email"]=$address_shipping['email'];

			$D024['D148'] = $D148;

			//cabeçalho do pedido
			$T005["T005_Valor_Desconto"]=$order['discount_amount'];
			$T005["T005_Valor_Total_Produtos"]=$order['subtotal'];
			$T005["T005_Valor_Total_Produtos_Sem_Desconto"]=$order['subtotal'];
			$T005["T005_Valor_Total"]=$order["grand_total"];
			$T005["T005_Valor_Frete"]=$order['shipping_amount'];
			$T005["T005_Observacao_Nota_Fiscal"]=$order['store_name']." ".$order['entity_id'];		
			$T005["T005_Pedido_Ecommerce"]=$order['entity_id'];
			$T005["T005_Ordem_Compra"]=$order['entity_id'];
			$T005["T005_Id_Pedido_Externo"]=$order['entity_id'];


			$produtos = array();

			$countp=0;
			foreach ($itens as $key => $item) {
				$countp++;
				$product = (array) $item;				
				//itens do pedido
				$T006["Peso_Unitario"]=$product['row_weight'];
				$T006["T006_Numero_Pedido_Compra"]=$order['entity_id'];
				$T006["T006_Codigo_Produto"]=$product['sku'];
				$T006["T006_Descricao_Produto"]=$product['name'];
				$T006["T006_Quantidade"]=$product['qty_ordered'];
				$T006["T006_Valor_Preco_Sem_Desconto_Unitario"]=$product['price'];
				$T006["Codigo_Barras"]='';
			
				$produtos[] = $T006;

			}

			$T005['D024'] = $D024;
			$T005['T006'] = $produtos;		
				
			$retorno = $VEN014->cadastrarPedido($T005);  
					
			if($retorno === false){
				$fracasso .= "Pedido  ". $T005["T005_Id_Pedido_Externo"] ."  não importado, verifique o log de erros.<br>";
				$fail++;
			}else{
				$sucesso .= "Pedido ".$T005["T005_Id_Pedido_Externo"] ." importado com sucesso!<br>";
				$sucess++;			
			}       
			
		}

			echo "<br><br>";

		$date = date('y-m-d H:i');
		echo $fracasso."<br>".$sucesso."<br>";
		echo "<b>Fim - $date </b><br><br>";
		echo "<b>Total de pedidos encontrados: $count</b><br>";
		echo "<b>Total de pedidos importados: $sucess</b><br>";
		echo "<b>Total de pedidos não importados: $fail</b><br>";
		

	break;

	case 'Magento1PutInventory':
	 	global $g, $confUsuario;
	 		
			$atualizacao=$g['C031']['IntervaloDeAtualizacaoEstoque']*60;
			
			echo '<meta HTTP-EQUIV="refresh" CONTENT="'.$atualizacao.'";URL="http://'.$_SERVER['HTTP_HOST'].'/crm/crm001/outro/Magento1PutInventory?ajax=true">';

			echo "<h2>Atualização estoque do magento</h2><br>";
			$date = date('y-m-d H:i');

			echo "<b>Inicio - $date </b><br>";
		
			require_once("bibliotecas/classes/VEN014.php");
			$VEN014 = new VEN014();

			// $callbackUrl is a path to your file with OAuth authentication example for the Admin user
			$callbackUrl = "http://".$_SERVER['HTTP_HOST']."/crm/crm001/outro/Magento1PutInventory?ajax=true";
			// //oAuth Initiate URL
			$temporaryCredentialsRequestUrl=$g['C031']['MagentoUrlCredencial'].urlencode($callbackUrl);
			// //oAuth Authorize
			$adminAuthorizationUrl = $g['C031']['MagentoUrlAutorizacao'];
			//oAuth Token URL
			$accessTokenRequestUrl = $g['C031']['MagentoUrlRequisicao'];
			//API Url
			$apiUrl = $g['C031']['MagentoUrlApi'];
			// Consumer Key hardness
			$consumerKey = $g['C031']['MagentoConsumerKEy'];
			//Consumer Secret
			$consumerSecret = $g['C031']['MagentoConsumerSecret'];
			//Status dos pedidos que serão trazidos para o sistema
			$status_pedido = $g['C031']['MagentoStatusPedido'];

			if (!isset($_GET['oauth_token']) && isset($_SESSION['state']) && $_SESSION['state'] == 1) 
			{
				$_SESSION['state'] = 0;
			}
			try 
			{
				$authType = ($_SESSION['state'] == 2) ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
				$oauthClient = new \OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, $authType);
				$oauthClient->enableDebug();

				if (!isset($_GET['oauth_token']) && !$_SESSION['state']) 
				{
					$requestToken = $oauthClient->getRequestToken($temporaryCredentialsRequestUrl);
					$_SESSION['secret'] = $requestToken['oauth_token_secret'];
					$_SESSION['state'] = 1;
					header('Location: ' . $adminAuthorizationUrl . '?oauth_token=' . $requestToken['oauth_token']);
					exit;
				} 
				else if ($_SESSION['state'] == 1) 
				{
					$oauthClient->setToken($_GET['oauth_token'], $_SESSION['secret']);
					$accessToken = $oauthClient->getAccessToken($accessTokenRequestUrl);
					$_SESSION['state'] = 2;
					$_SESSION['token'] = $accessToken['oauth_token'];
					$_SESSION['secret'] = $accessToken['oauth_token_secret'];
					header('Location: ' . $callbackUrl);
					exit;
				} 
				else 
				{

					// $sqlD001 = mysql_query("SELECT D001_Id FROM D001");	
					// $sqlProduto = mysql_query("SELECT * FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D001_Id = '{$rowD001['D001_Id']}' AND D009_C004_Id = '{$g['empresaAtual']}'");
					// $produto   = mysql_fetch_array($sqlProduto);	

					// foreach ($produto as $key => $updateproduto) {

					// 	$rowD009 = mysql_fetch_assoc($sqlD009);
					// 	$dados['item_id']=$rowD009['D009_Id'];
					// 	$dados['qty']=$rowD009['D009_Quantidade_Estoque'];
					// 	$arrayProduto = (object) $dados;
					// 	$arrayProduto = json_encode($arrayProduto, JSON_UNESCAPED_UNICODE); //Funciona no PHP >= 5.4
			
					// 	$oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);

					// 	$resourceUrl = "$apiUrl/stockitems/$rowD009['D009_Id'];

					// 	$oauthClient->fetch($resourceUrl, array(), 'PUT', array('Accept' => '*/*'));

					// 	$listOrders = $oauthClient->setLastResponse();
					// }

				}
			} 
			catch (OAuthException $e) 
			{
				print_r($e->getMessage());
				echo "<br/>";
				print_r($e->lastResponse);
			}
			

		echo "<h1> Melhoria em fase de construção!<br> Por favor, aguarde.</h1>";
		echo "<br><br>";

		$date = date('y-m-d H:i');
		echo $fracasso."<br>".$sucesso."<br>";
		echo "<b>Fim - $date </b><br><br>";
		echo "<b>Total de produtos atualizados: </b><br>";
		

	break;

	case 'crm001ManualCSVTabelaPrecos':
		
		echo <<<EOT
        
        <style>
            .tabelaManual{
                border:1px solid black;
                border-colapse: colapse;    
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabelaManual{               
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }   
        </style>

        <div style="padding-left:10px;">
        <h2>Modelo de documento CSV</h2>
        <h3>Observações:</h3>
        <p style="font-size:12px">-Abaixo está o formato do modelo do documento para a importação!
        <br/>-O documento deve ter <strong>3 colunas</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
        <br/>-Os nomes das colunas devem estar necessáriamente na primeira linha(1) do documento e ter a mesma grafia do documento modelo.
        <br/>-A extensão do documento deve ser CSV (Delimitado por vírgulas)(*.csv)
        <br/>-Ao final do documento não deve haver nenhuma linha em branco. 
        <br/>-Os campos obrigatórios estão identificados pelo (*).
        <br/>-Nos campos numéricos utilizar apenas vírgula como separador decimal e não utilizar ponto. Ex: 2,50; 2500,36
        <br/>-<a href="{$confUsuario['urlRaiz']}hardness3/prog/crm/crm001/modeloCSVTabelaPreco.csv?ajax=true" style="color:green" target=_blank>BAIXAR MODELO CSV</a>
        <!--<br/>-<a href="{$confUsuario['urlRaiz']}crm/crm001/grid_func-ajax/baixaCSVTabelaPreco/?ajax=true" style="color:green" target=_blank>BAIXAR MODELO CSV</a>-->
        </p>
        </div>

        <table cellspacing=1px bgcolor="black" >     
            <tr>        
                <td class="linhaTabelaManual">*Codigo Cliente</td>
                <td class="linhaTabelaManual" >*Codigo Produto</td>
                <td class="linhaTabelaManual" >*Preco</td>
            </tr>
            <tr>            
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            </tr>
            <tr>            
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            </tr>
            <tr>            
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            </tr>
            <tr>        
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
            </tr>   
        </table>
EOT;

	break;

	case 'crm001CabecalhoOrcamentoCancelado':
		global $g;
		$filtro = '';
        if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
              $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
        }else{
              $filtro = '';
        }
		$resultado = mysql_query("SELECT D001_Codigo_Produto,
										 D001_Descricao_Produto,
										 (SELECT SUM(T004_Quantidade) 
                                           		FROM T004 
                                      	   LEFT JOIN T003 ON T003_Id=T004_T003_Id 
                                           LEFT JOIN D047 ON D047_Id = T003_D047_Id
                                               WHERE T004_D009_Id=D009_Id
                                                 AND T003_Flag_Status_Orcamento='C' 
                                                 AND D047_Flag_Compra = 'S'
                                                AND T003_C004_Id=D009_C004_Id) AS vendaPerdida
								    FROM D009
									LEFT JOIN T187 on T187_D009_Id=D009_Id
									LEFT JOIN D049 on D049_Id=D009_D049_Id
									LEFT JOIN D001 on D001_Id=D049_D001_Id
									LEFT JOIN D082 on D082_Id=D049_D082_Id
									LEFT JOIN D083 on D083_D049_Id=D049_Id
									LEFT JOIN D037 on D037_Id=D001_D037_Id
									LEFT JOIN C008 on C008_Id=D001_C008_Id
									LEFT JOIN D003 on D003_Id=D001_D003_Id
									LEFT JOIN D060 on D001_D060_Id=D060_Id
									LEFT JOIN D002 on D001_D002_Id=D002_Id
									LEFT JOIN D015 on D001_D015_Id=D015_Id
									LEFT JOIN T001 on T001_Id=D009_T001_Id
									LEFT JOIN D024 as ultimaCompra on ultimaCompra.D024_Id = T001_D024_Id
									LEFT JOIN C007 on (C007_Id=D009_C007_Id_Comprar and D009_Flag_Cotar='S') or C007_Id=D009_C007_Id_Comprador
									LEFT JOIN T066 on T066_D009_Id=D009_Id
									LEFT JOIN D004 on D004_Id=T066_D004_Id
									LEFT JOIN T241 ON T241_D001_Id=D001_Id
									LEFT JOIN D024 as fornecedor on fornecedor.D024_Id=T241_D024_Id
									WHERE D009_Id = '{$r_acaoId}' 
									  AND D009_C004_Id='{$g['empresaAtual']}'
									  AND D001_Flag_Ativo='S'
						                {$filtro}
								");
		$row = mysql_fetch_assoc($resultado);
		
		$row['D001_Descricao_Produto'] = gTruncar($row['D001_Descricao_Produto'], 45);
		$row['vendaPerdida'] = gCorrigeNumero($row['vendaPerdida']);
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='13%'>Código</td>
						<td width='90%'>Descrição</td>
						<td width='13%'>Quantidade</td>
					</tr>
					<tr class="linhaVal">
						<td align='left'>{$row['D001_Codigo_Produto']}</td>
						<td>{$row['D001_Descricao_Produto']}</td>
						<td>{$row['vendaPerdida']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;


}

