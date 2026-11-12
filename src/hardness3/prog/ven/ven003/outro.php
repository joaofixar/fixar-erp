<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {

    case 'ven003ManualVarsLayout':
        $textoManual = '
        <div style="padding-left:10px;">
        <h2>Modelo de alteração de layout</h2>
        <h3>Observações:</h3>
        <p style="font-size:12px">-Abaixo está o modelo para realizar a alteração da DANFE!
        <br/>
        <br/>- Máximo de caracteres qeu a observação pode ocupar em uma linha.
        <br/>$maxChars = 53;
        <br/>
        <br/>- Quantidade total de linhas que os produtos podem ocupar na primeira página.
        <br/>$this->linhas_primeira_pagina = 40;
        <br/>
        <br/>- Quantidade total de linhas que o produto pode ocupar nas páginas diferentes de 1.
        <br/>$this->linhas_segunda_pagina = 79;
        <br/>
        <br/>- Define a quantidade total de linhas que deverão ser impressas no DANFE.
        <br/>$nTotal_Linhas = 0;
        <br/>
        <br/>- Define a quantidade de itens que serão impressos no DANFE.
        <br/>$nTotal_Itens_Contados = 0;
        <br/>
        <br/>- Define a altura do campo de informações adicionais do produto.
        <br/>$lineHeightInfo = 13;
        <br/>
        <br/>- Define a altura do campo.
        <br/>$lineHeight = 25;
        <br/>
        <br/>- Define a altura do campo que irá conter as informações dos produtos no DANFE.
        <br/>$lineHeightGeral = 11;
        <br/>
        <br/>- Somar altura linha quando descrição for mais que 2 linhas.
        <br/>$somaLinhaHeight = 13;
        <br/>
        <br/>- Define a quantidade total de páginas.
        <br/>$nTotal_Paginas = 1;
        <br/>
        <br/>- Vai definindo quantos itens estão sendo impressos.
        <br/>$nTotal_Itens_Impressos = 0;
        <br/>
        <br/>- Define a quantidade de itens que estão sendo Impressos por página.
        <br/>-$nTotal_Itens_Impressos_Tmp=0;
        <br/>$empresaP23 = "";
        <br/>
        <br/>- Define o texto das faturas e duplicatas.
        <br/>$dups = "";
        </p>
        </div>';

        echo $textoManual;

	break;
	
	case 'ven003OutroInutilizar':
		$T007_Numero_Nota_Fiscal_Inicial = urlencode($r_T007_Numero_Nota_Fiscal_Inicial);
		$T007_Numero_Nota_Fiscal_Final = urlencode($r_T007_Numero_Nota_Fiscal_Final);
		$T007_Justificativa_Inutilizacao = urlencode($r_T007_Justificativa_Inutilizacao);
		$T007_Serie = urlencode($r_T007_Serie);
		$T007_Flag_ACP = urlencode($r_T007_Flag_ACP);
		echo <<<EOT
			<STYLE TYPE=\"text/css\">
			.divMensagemCentro {
				width:220px;
				height:40px;
				position: absolute;
				top:50%;
				left:45%;
				margin-top:-50px;
				margin-left:-50px;
				z-index:auto;
				background-color: #00FF00;
				border: 1px solid #004000;
				clear:both;
				vertical-align:middle;
				color: #202020;
				padding:13px;
				font-family:arial, tahoma, verdana, helvetica, sans-serif;
				font-size:12px;
				font-weight:bold;
				text-align:center;
			}
			</style>
			<div id='Div_Transmitindo' class="divMensagemCentro" style='display:none;'></div>
			<iframe name='fme_inutilizar' id='fme_inutilizar' style='width: 100%; height: 100%; border: 0; margin: 0; padding: 0;' src="/hardness3/hardness/inutilizar_nfe/inutilizacao.php?T007_Numero_Nota_Fiscal_Inicial={$T007_Numero_Nota_Fiscal_Inicial}&T007_Numero_Nota_Fiscal_Final={$T007_Numero_Nota_Fiscal_Final}&T007_Justificativa_Inutilizacao={$T007_Justificativa_Inutilizacao}&T007_Serie={$T007_Serie}&T007_Flag_ACP={$T007_Flag_ACP}"></iframe>
EOT;
	break;

	case 'ven003OutroRecuperarInutilizacao':
		$t127Id = (int) $r_T127_Id;
		$empresaId = (int) $g['empresaAtual'];
		$sqlT127 = mysql_query("SELECT *
			FROM T127
			WHERE T127_Id = '{$t127Id}'
			  AND T127_C004_Id = '{$empresaId}'
			  AND T127_Codigo_Retorno = '000'
			  AND T127_Retorno_Sefaz LIKE 'INUTILIZACAO_PENDENTE%'");
		$t127 = mysql_fetch_assoc($sqlT127);

		if (!$t127) {
			echo '<div style="padding:15px;color:#b00">A pendência não foi encontrada, já foi tratada ou não pertence à empresa atual.</div>';
			break;
		}

		$xmlSolicitacao = trim($t127['T127_Xml_Retorno']);
		if ($xmlSolicitacao === '') {
			echo '<div style="padding:15px;color:#b00">Esta pendência foi criada antes do armazenamento do XML de solicitação e não pode ser recuperada automaticamente. Faça a inutilização novamente pelo formulário.</div>';
			break;
		}

		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($xmlSolicitacao, 'SimpleXMLElement', LIBXML_NONET);
		if ($xml === false || !isset($xml->infInut)) {
			echo '<div style="padding:15px;color:#b00">O XML gravado na pendência é inválido e a recuperação automática foi bloqueada.</div>';
			break;
		}

		$infInut = $xml->infInut;
		$modelo = (string) $infInut->mod;
		$serie = (string) $infInut->serie;
		$numeroInicial = (int) $infInut->nNFIni;
		$numeroFinal = (int) $infInut->nNFFin;
		$justificativa = (string) $infInut->xJust;
		$ano = (string) $infInut->ano;
		$cnpjXml = preg_replace('/\D/', '', (string) $infInut->CNPJ);
		$sqlEmpresa = mysql_query("SELECT C004_Cnpj FROM C004 WHERE C004_Id = '{$empresaId}'");
		$empresa = mysql_fetch_assoc($sqlEmpresa);
		$cnpjEmpresa = preg_replace('/\D/', '', $empresa['C004_Cnpj']);

		$dadosValidos = in_array($modelo, array('55', '65'))
			&& $modelo === $t127['T127_Modelo']
			&& $serie !== ''
			&& $numeroInicial > 0
			&& $numeroFinal >= $numeroInicial
			&& (int) $t127['T127_Numero_Nota_Fiscal'] >= $numeroInicial
			&& (int) $t127['T127_Numero_Nota_Fiscal'] <= $numeroFinal
			&& strlen($justificativa) >= 15
			&& preg_match('/^[0-9]{2}$/', $ano)
			&& $cnpjXml !== ''
			&& $cnpjXml === $cnpjEmpresa;

		if (!$dadosValidos) {
			echo '<div style="padding:15px;color:#b00">Os dados do XML não conferem com a pendência ou com a empresa atual. A recuperação automática foi bloqueada.</div>';
			break;
		}

		$flagAcp = $modelo === '55' ? '6' : '8';
		$destino = '/hardness3/hardness/inutilizar_nfe/inutilizacao.php?'
			. 'T007_Numero_Nota_Fiscal_Inicial=' . urlencode($numeroInicial)
			. '&T007_Numero_Nota_Fiscal_Final=' . urlencode($numeroFinal)
			. '&T007_Justificativa_Inutilizacao=' . urlencode($justificativa)
			. '&T007_Serie=' . urlencode($serie)
			. '&T007_Flag_ACP=' . $flagAcp
			. '&T127_Recuperacao_Id=' . $t127Id
			. '&ven003DivRefresh=' . urlencode($r_ven003DivRefresh);
		$destinoJson = json_encode($destino);
		$faixa = htmlspecialchars($numeroInicial . ' a ' . $numeroFinal, ENT_QUOTES, 'UTF-8');
		$justificativaHtml = htmlspecialchars($justificativa, ENT_QUOTES, 'UTF-8');

		echo <<<EOT
			<style>
				#ven003RecuperarInutilizacao { padding: 18px; font: 13px Arial, sans-serif; color: #25364a; }
				#ven003RecuperarInutilizacao .titulo { font-size: 17px; font-weight: bold; margin-bottom: 14px; }
				#ven003RecuperarInutilizacao .resumo { display: flex; gap: 10px; margin-bottom: 14px; }
				#ven003RecuperarInutilizacao .dado { flex: 1; padding: 10px; border: 1px solid #d5dfeb; border-radius: 4px; background: #f7f9fc; }
				#ven003RecuperarInutilizacao .dado small { display: block; color: #65758a; margin-bottom: 3px; }
				#ven003RecuperarInutilizacao .aviso { padding: 10px 12px; margin: 12px 0 16px; border-left: 4px solid #e9a900; background: #fff8e5; line-height: 1.45; }
				#ven003RecuperarInutilizacao .acoes { text-align: right; }
				#ven003RecuperarInutilizacao .botaoRecuperar { padding: 7px 14px; border: 0; border-radius: 4px; background: #2864ad; color: #fff; font-weight: bold; cursor: pointer; }
				#ven003RecuperarInutilizacao .botaoRecuperar:hover { background: #1e518f; }
				#ven003RecuperarInutilizacao .processando { display: none; padding: 18px 0; text-align: center; font-weight: bold; color: #2864ad; }
			</style>
			<div id="Div_Transmitindo" style="display:none"></div>
			<div id="ven003RecuperarInutilizacao">
				<div id="ven003RecuperarResumo">
					<div class="titulo">Confirmar recuperação da inutilização</div>
					<div class="resumo">
						<div class="dado"><small>Faixa</small><b>{$faixa}</b></div>
						<div class="dado"><small>Modelo</small><b>{$modelo}</b></div>
					</div>
					<div><b>Justificativa:</b> {$justificativaHtml}</div>
					<div class="aviso">Reenvie somente porque a resposta anterior não foi concluída localmente. Se a SEFAZ informar que a faixa já foi inutilizada, o sistema concluirá os registros locais.</div>
					<div class="acoes"><button type="button" class="botaoRecuperar" onclick="ven003IniciarRecuperacaoInutilizacao();">Recuperar inutilização</button></div>
				</div>
				<div id="ven003RecuperarProcessando" class="processando">Enviando solicitação para a SEFAZ...</div>
				<iframe id="fme_recuperar_inutilizacao" name="fme_recuperar_inutilizacao" style="display:none;width:100%;height:235px;border:0;margin-top:8px"></iframe>
			</div>
			<script>
				function ven003IniciarRecuperacaoInutilizacao() {
					document.getElementById('ven003RecuperarResumo').style.display = 'none';
					document.getElementById('ven003RecuperarProcessando').style.display = 'block';
					var frame = document.getElementById('fme_recuperar_inutilizacao');
					frame.style.display = 'block';
					frame.src = {$destinoJson};
				}
			</script>
EOT;
	break;
	
	case 'ven003CabecalhoNotaFiscal':
		// Select
		$sql = <<<EOT
		SELECT 
			*,
			if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
		FROM 
			T007
		left join D024 on T007_D024_Id=D024_Id
		left join D020 on D024_D020_Id=D020_Id
		left join D018 on D024_D018_Id=D018_Id
		left join C007 on C007_Id=D024_C007_Vendedor_Interno
		left join D030 on D024_D030_Id=D030_Id
		WHERE
			(T007_Id='$r_acaoId')
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

        $protocoloTransmissao = "NÃO TRANSMITIDA";
        $dataTransmissao = "NÃO TRANSMITIDA";
        if(!empty($row['T007_Numero_Protocolo_Nfe']))
        {
			$row['T007_Data_Hora_Protocolo_Nfe'] = gCorrigeData($row['T007_Data_Hora_Protocolo_Nfe']);
            $protocoloTransmissao = "{$row['T007_Numero_Protocolo_Nfe']}";
            $dataTransmissao = "{$row['T007_Data_Hora_Protocolo_Nfe']}";
		}
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">????</span>';
			}
		}

        $dataEmissao = gCorrigeData($row['T007_Data_Emissao']);
        $D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 57);
        $D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);
        $D024_Id = $row['T007_D024_Id'];
        $novoIdTelaForm = uniqid();
        $mDocumento[1]='NF';
        $mDocumento[2]='ROM';
        $mDocumento[3]='ACP';
        $mDocumento[4]='CF';
        $mDocumento[5]='D1';
        $mDocumento[6]='NF-e';
        $mDocumento[7]='NFS-e';
        $mDocumento[8]='NFC-e';
        if (empty($row['T007_Flag_ACP'])) { $row['T007_Flag_ACP'] = 1; }
        $flagTipo = $mDocumento[$row['T007_Flag_ACP']]; 

        
        if($D024_Id > 0){
            $link = <<<EOT
                <a onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + {$D024_Id}, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$D024_Id}') + '&tabela=D024', [930,583]);">
EOT;
        } else {
            $link = <<<EOT
                <a href="#">
EOT;
            $D024_Nome_Empresa = "<span style='color: var(--BGVermelho);'>CLIENTE NÃO INFORMADO</span>";
        }
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Tipo</td>
                        <td width='70px'>Número</td>
						<td width='380px' style="cursor:pointer;">
							{$link}
                                Cliente
                            </a>
						</td>
						<td width='150px'>Cidade</td>
						<td width='50px'>Estado</td>
						<td width='70px'>Emissão</td>
						<td width='150px'>Protocolo Transmissão</td>
                        <td>Data Transmissão</td>
					</tr>
					<tr class="linhaVal">
                        <td>{$flagTipo}</td>
						<td>{$row['T007_Numero_Nota_Fiscal']}</td>
						<td style="cursor:pointer;">
							{$link}
		                        {$D024_Nome_Empresa}
							</a>
						</td>
						<td>{$D020_Nome_Cidade}</td>
						<td>{$row['D018_UF']}</td>
						<td>{$dataEmissao}</td>
						<td>{$protocoloTransmissao}</td>
                        <td>{$dataTransmissao}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'impressao':
		global $g;
		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();
		$retorno = $VEN003->imprimirNFeMarcada();
		if($retorno === true){
			require_once("bibliotecas/PDFMerger/PDFMerger.php");
			$pdf = new \PDFMerger;
			$sql = mysql_query("SELECT * FROM T007 WHERE T007_Flag_Impresso = 'M' AND T007_C004_Id = '{$g['empresaAtual']}'");
			while($res = mysql_fetch_array($sql)){
				if(!empty($res['T007_Numero_Protocolo_Nfe'])) {
					$AnoMesNFe=substr($res['T007_Data_Emissao'],0,7);
					$arquivoXML = '../hardness/'.$g['pathDados'].'nfe/xml/autorizado/'.$AnoMesNFe.'/'.$res['T007_Numero_Protocolo_Nfe'].'.xml';
					$pdf->addPDF('../hardness/'.$g['pathDados'].'nfe/danfe/danfe_'.$res['T007_Numero_Protocolo_Nfe'].'.pdf', 'all');
				}
			}
			$pdf->merge('../hardness/'.$g['pathDados'].'nfe/danfe', 'danfe.pdf');
			die();
		}
	break;

	case 'ven003observacoes':
		global $g;
        $T007_Id = isset($r_acaoId) ? $r_acaoId : false;
        $divAjuste = isset($r_divAjuste) ? $r_divAjuste : false;

        $cSql_T008=mysql_query("select T008_Id from T008 where T008_T007_Id='$T007_Id'");
        while ($mT008=mysql_fetch_array($cSql_T008)) {
              mysql_query("call T008_Gravar_Totalizacao_4('{$mT008['T008_Id']}')");
        }

        mysql_query("call T007_Gravar_Totalizacao_4($T007_Id);");
        $T007 = "SELECT *, T007_Prazos(T007_Id) as Prazo, 
                           SUM(T008_Valor_Lucro) as Lucro,
                           sum(T008_Quantidade) as quantidadeTotal,
                           sum(D001_Cubagem_Unitaria*T008_Quantidade) as cubagemTotal
                      FROM T007
                 LEFT JOIN D024 ON D024_Id = T007_D024_Id
                 LEFT JOIN T008 ON T008_T007_Id = T007_Id
                 LEFT JOIN D009 ON D009_Id = T008_D009_Id
                 LEFT JOIN D049 ON D049_Id = D009_D049_Id
                 LEFT JOIN D001 ON D001_Id = D049_D001_Id
                     WHERE T007_Id='{$T007_Id}'
                     GROUP BY T007_Id";
        $T007 = mysql_query($T007);
        $mT007= mysql_fetch_array($T007);

        $mT007['T007_Valor_Frete']+=$mT007['T007_Valor_Despesas_Acessorias'];
        $mT007['T007_Valor_Frete']=gCorrigeNumero($mT007['T007_Valor_Frete']);
        $obsComercial    = substr($mT007['D024_Observacao_Comercial'], 0, 140);
        $obsFinanceiro   = substr($mT007['D024_Observacao_Financeiro'], 0, 180);
        $valorLimite     = gCorrigeNumero($mT007['D024_Valor_Limite']);
        
        $lucro                                          = gCorrigeNumero(($mT007['Lucro']/$mT007['T007_Valor_Total_Produtos'])*100);
        $mT007['Lucro']                                 = gCorrigeNumero($mT007['Lucro']);
        $margem                                         = gCorrigeNumero((($mT007['T007_Valor_Total_Produtos']-$mT007['T007_Valor_Total_Custo'])/$mT007['T007_Valor_Total_Produtos'])*100);
        $valorDescontoTabela                            = $mT007['T007_Valor_Total_Produtos_Sem_Desconto'] - $mT007['T007_Valor_Total_Produtos'];
        $percentualDesconto                             = gCorrigeNumero(($valorDescontoTabela/$mT007['T007_Valor_Total_Produtos_Sem_Desconto'])*100);
        require_once("bibliotecas/classes/VEN003.php");
        $VEN003 = new VEN003();
        $percentualDesconto = $VEN003->ven003ModObservacaoPercentualDesconto($percentualDesconto, $mT007);
        $ipv                                            = gCorrigeNumero($mT007['T007_Valor_Total_Produtos']/$mT007['T007_Valor_Total_Custo'],4);
        $mT007['T007_Valor_Total_Custo']                = gCorrigeNumero($mT007['T007_Valor_Total_Custo']);
        $mT007['T007_Valor_Desconto']                   = gCorrigeNumero($mT007['T007_Valor_Desconto']);
        //$mT007['T007_Valor_Total_Produtos_Sem_Desconto'] = gCorrigeNumero($mT007['T007_Valor_Total_Produtos_Sem_Desconto']);
        $mT007['T007_Valor_Total_Produtos']             = gCorrigeNumero($mT007['T007_Valor_Total_Produtos']);
        $somaIcmsFCP                                    = gCorrigeNumero($mT007['T007_Valor_Total_ICMS'] + $mT007['T007_Valor_Total_FCP']);
        $mT007['T007_Valor_Total_ICMS']                 = gCorrigeNumero($mT007['T007_Valor_Total_ICMS']);
        $mT007['T007_Valor_Total_FCP']                  = gCorrigeNumero($mT007['T007_Valor_Total_FCP']);
        $mT007['T007_Valor_Total_ICMS_Credito']         = gCorrigeNumero($mT007['T007_Valor_Total_ICMS_Credito']);
        $mT007['T007_Valor_Total_IPI']                  = gCorrigeNumero($mT007['T007_Valor_Total_IPI']);
        //$mT007['T007_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mT007['T007_Valor_ICMS_Substituicao_Retencao']);
        $mT007['T007_Valor_Total']                      = gCorrigeNumero($mT007['T007_Valor_Total']);
        $mT007['T007_IPV']                              = gCorrigeNumero($mT007['T007_IPV'],4);
        $mT007['T007_Percentual_Margem']                = gCorrigeNumero($mT007['T007_Percentual_Margem'],2);
        $valorDescontoTabela                            = gCorrigeNumero($valorDescontoTabela);
        $mT007['quantidadeTotal']                       = gCorrigeNumero($mT007['quantidadeTotal']);
        $mT007['cubagemTotal']                          = gCorrigeNumero($mT007['cubagemTotal']);
        $mT007['T007_Valor_Frete_Calculado']            = gCorrigeNumero($mT007['T007_Valor_Frete_Calculado']);
        $mT007['pesoLiquido']                           = gCorrigeNumero($mT007['T007_Peso_Liquido']);
        $mT007['pesoBruto']                             = gCorrigeNumero($mT007['T007_Peso_Bruto']);
        $totalST                                        = gCorrigeNumero($mT007['T007_Valor_ICMS_Substituicao_Retencao'] + $mT007['T007_Valor_FCP_ST']);        
        $mT007['T007_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mT007['T007_Valor_ICMS_Substituicao_Retencao']);
        $mT007['T007_Valor_FCP_ST']                     = gCorrigeNumero($mT007['T007_Valor_FCP_ST']);
        
        require_once('prog/crm/crm001/grid_func-callback.php');
        if($mT007['T007_Flag_ACP'] == '6' || $mT007['T007_Flag_ACP'] == '8') {
            $doc = ($mT007['T007_Flag_ACP'] == '6') ? 'NF-e' : 'NFC-e';
            if($mT007['T007_Flag_Cancelada'] == 'S'){
                $status = "$doc cancelada";
            } else if($mT007['T007_Flag_Almoxarifado'] == 'N' || empty($mT007['T007_Numero_Nota_Fiscal']) || $mT007['T007_Flag_Travar_Nota'] == 'N'){
                $status = "Aguard. finalizar $doc";
            } else if($mT007['T007_Flag_Almoxarifado'] == 'S' && $mT007['T007_TX_Flag_Finalizada'] != '1' && empty($mT007['T007_Numero_Protocolo_Nfe'])){
                $status = "Aguard. transmitir $doc";
            } else {
                $status = "$doc transmitida";
            }
        }
        $status = "<div class='blue' style='font-weight:bold;border-radius:5px;background:blue;color:white; padding: 1px;margin-top: 14px;height: 16px;font-size: 13px;' align=center>{$status}</div>";

        $mT007['T007_Valor_Total_Produtos_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_Total_Produtos_Confirmado']);
        $mT007['T007_Valor_Total_ICMS_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_Total_ICMS_Confirmado']);
        $mT007['T007_Valor_Total_IPI_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_Total_IPI_Confirmado']);
        $mT007['T007_Valor_ST_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_ST_Confirmado']);
        $mT007['T007_Valor_Total_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_Total_Confirmado']);


        $mT007['T007_Percentual_Comissao'] = gCorrigeNumero($mT007['T007_Percentual_Comissao']);
        $mT007['T007_Valor_Total_Comissao'] = gCorrigeNumero($mT007['T007_Valor_Total_Comissao']);

        //Calcula saldo do cliente
        $sqlSaldo = "SELECT ifnull(sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)),0) as Vencido, 
                            ifnull(sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)),0) as Vencer
                        FROM T002
                        WHERE T002_D024_Id='{$linhaD024['D024_Id']}'
                        and T002_Data_Recebimento='0000-00-00' 
                        and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
                        ";
        $resultSaldo = mysql_query($sqlSaldo);
        $linhaSaldo= mysql_fetch_array($resultSaldo);
        
        $sqlPedidos="select ifnull(sum(T005_Valor_Total),0) as Pedidos
                                from T005
                           left join T007 on T007_T005_Id=T005_Id
                               where T005_D024_Id='{$linhaD024['D024_Id']}'
                                 and T005_Flag_Almoxarifado='S'
                                 and T007_Id is null";
        $retornoPedidos = mysql_query($sqlPedidos);
        $linhaPedidos = mysql_fetch_array($retornoPedidos);
        $linhaD024['D024_Valor_Limite'] = (!empty($linhaD024['D024_Valor_Limite']) ? $linhaD024['D024_Valor_Limite'] : '0.00');
        $saldoAtual=gCorrigeNumero($linhaD024['D024_Valor_Limite']-($linhaSaldo['Vencido']+$linhaSaldo['Vencer']+$linhaPedidos['Pedidos']));
        
        $valorCreditoAntecipado = gCorrigeNumero($mT007['D024_Valor_Credito_Antecipado']);
        $valorCreditoTroca = gCorrigeNumero($mT007['D024_Valor_Credito_Troca']);
        //$valorOrcamento = $VEN001->calculaTotalGeral($T007_Id);
        $exibirLimites = $g['C031']['exibirLimitesOrcamentoPedido'];

        $valorPrecoTabela = gCorrigeNumero($mT007['T007_Valor_Total_Produtos_Sem_Desconto']);
        
        //$onclick = ($g['nivelAlcada'] == 4) ? "abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Montagem IPV', '/ven/ven001/content/contentMontagemIPV/', '&acaoId={$T007_Id}', [370,580]);" : "";

        $heightComercial=60;
        $heightFinanceiro=70;

        if((isset($g['c029Ids'][217])) && !(isset($g['c029Ids'][218]))){
            $heightComercial=130;
        }
        if(!(isset($g['c029Ids'][217])) && (isset($g['c029Ids'][218]))){
            $heightFinanceiro=145;
        }


        $C004 = mysql_query("SELECT C004_Codigo_Regime_Tributario FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);
        if($mC004['C004_Codigo_Regime_Tributario'] == 1 ){
            $ICMS = 'ICMS Crédito';
            $aliquotaICMS = "(".$mT007['T007_Aliquota_ICMS_Credito']."%) ";
            $valorICMS = $mT007['T007_Valor_Total_ICMS_Credito'];
        }else{
            $ICMS = 'ICM';
            $aliquotaICMS='';
            $valorICMS = $somaIcmsFCP;
        }

        $somaTributos = $mT007['T007_Valor_Total_PIS']+$mT007['T007_Valor_Total_COFINS']+$mT007['T007_Valor_Total_IRPJ']+$mT007['T007_Valor_Total_CSLL']+$mT007['T007_Valor_Total_Partilha_ICMS_Destino']+$mT007['T007_Valor_Total_Partilha_ICMS_Origem'];
        $somaTributos = gCorrigeNumero($somaTributos);

        $mT007['T007_Valor_Total_PIS'] = gCorrigeNumero($mT007['T007_Valor_Total_PIS']);
        $mT007['T007_Valor_Total_COFINS'] = gCorrigeNumero($mT007['T007_Valor_Total_COFINS']);
        $mT007['T007_Valor_Total_IRPJ'] = gCorrigeNumero($mT007['T007_Valor_Total_IRPJ']);
        $mT007['T007_Valor_Total_CSLL'] = gCorrigeNumero($mT007['T007_Valor_Total_CSLL']);
        $mT007['T007_Valor_Total_Partilha_ICMS_Destino'] = gCorrigeNumero($mT007['T007_Valor_Total_Partilha_ICMS_Destino']);
        $mT007['T007_Valor_Total_Partilha_ICMS_Origem'] = gCorrigeNumero($mT007['T007_Valor_Total_Partilha_ICMS_Origem']);
        $title = "PIS: &nbsp; {$mT007['T007_Valor_Total_PIS']}\nCOFINS: &nbsp; {$mT007['T007_Valor_Total_COFINS']}\nIRPJ: &nbsp; {$mT007['T007_Valor_Total_IRPJ']}\nCSLL: &nbsp; {$mT007['T007_Valor_Total_CSLL']} \nDIFAL DEST: &nbsp; {$mT007['T007_Valor_Total_Partilha_ICMS_Destino']} \nDIFAL ORIG: &nbsp; {$mT007['T007_Valor_Total_Partilha_ICMS_Origem']}";


        echo <<<EOT
            <style type="text/css">
                .observacoesLimites{
                    width: 285px;
                    height: 186px;
                    float: left;
                    margin: 7px 0px 10px 8px;
                    border-radius:5px;
                    border-color:silver;
                }
                .observacoesLimites .comercial{
                    height: {$heightComercial}px;
                    padding:1px;
                    border-bottom:none;
                }
                .observacoesLimites .financeiro{
                    height: {$heightFinanceiro}px;
                    padding:1px;
                }
                .observacoesLimites .limite{
                    height: 15%;
                    padding:1px;
                    border-top:none;
                }
                .observacoesLimites .liberado{
                    height: 7%;
                    padding:0px;
                    border-bottom:1px solid silver;
                }
                .observacoesLimites .saldo{
                    height: 9%;
                    padding:1px;
                    border-top:none;
                }
            </style>
            <table style='font-size:13px;border-left:5px;float:left; margin: 3px 5px 0 5px; height: 211px; width: 195px; cursor: pointer;' cellspacing="0" cellpadding="0" onclick="{$onclick}">
              <tr >
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;;font-size:11px'>
                    Tabela
                </td>
                <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                    R$ {$valorPrecoTabela}
                </td>
              </tr>
EOT;
        if($valorDescontoTabela<0 && $g['C031']['mostrarDescontoProdutosAcrescimo']=="S"){     
            $percentualDesconto="0,00";
            $valorDescontoTabela = "0,00";
        }
            echo <<<EOT
              <tr>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                    Desconto
                </td>
                <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                    ($percentualDesconto%) {$valorDescontoTabela}
                </td>
              </tr>
              <tr>
                <td class='observacaoTotaisTd' style='font-weight:bold;border-bottom:1px solid #F4F4F4'>
                    Produtos
                </td>
                <td class='observacaoTotaisTd' style='font-weight:bold;font-size:14px;border-bottom:1px solid #F4F4F4' align='right'>
                    {$mT007['T007_Valor_Total_Produtos']}
                </td>
              </tr>
              <tr>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                    IPI
                </td>
                <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                    {$mT007['T007_Valor_Total_IPI']}
                </td>
              </tr>
              <tr title="ST: &nbsp; {$mT007['T007_Valor_ICMS_Substituicao_Retencao']}\nFCP ST: &nbsp; {$mT007['T007_Valor_FCP_ST']}">
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                    <u>ST+FCP ST</u>
                </td>
                <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                    {$totalST}
                </td>
              </tr>
              <tr>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                    Desconto
                </td>
                <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                    {$mT007['T007_Valor_Desconto']}
                </td>
              </tr>
               <tr title="Quantidade: &nbsp; {$mT007['quantidadeTotal']}\nCubagem: &nbsp; {$mT007['cubagemTotal']}m³\nPeso liquido: &nbsp; {$mT007['pesoLiquido']} kg\nPeso bruto:  &nbsp; {$mT007['pesoBruto']} kg \nFrete calculado: &nbsp; {$mT007['T007_Valor_Frete_Calculado']}">
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        <u>Frete+Desp</u>
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        ({$mT007['quantidadeTotal']} - {$mT007['cubagemTotal']}m³) {$mT007['T007_Valor_Frete']}
                    </td>
                </tr>
              <tr>
                <td class='observacaoTotaisTd' style='font-weight:bold;border-bottom:1px solid #F4F4F4'>
                    Total
                </td>
                <td class='observacaoTotaisTd' style='font-weight:bold;font-size:18px;border-bottom:1px solid #F4F4F4' align='right'>
                    {$mT007['T007_Valor_Total']}
                </td>
              </tr>
              <tr>
              <tr title="ICMS: &nbsp; {$mT007['T007_Valor_Total_ICMS']}\nFCP: &nbsp; {$mT007['T007_Valor_Total_FCP']}">
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                    <u>{$ICMS}</u>
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                    {$aliquotaICMS}{$valorICMS}
                </td>
             </tr>
             <tr title="{$title}">
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                    <u>Outros</u>
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                    {$somaTributos}
                </td>
             </tr>    
              <tr>
                <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px'>
                    Comissão
                </td>
                <td class='observacaoTotaisTd' style='font-size:13px;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                    ({$mT007['T007_Percentual_Comissao']}%) {$mT007['T007_Valor_Total_Comissao']}
                </td>
              </tr>              
            </table>
EOT;

    if(!(isset($g['c029Ids'][217])) && !(isset($g['c029Ids'][218]))){
        echo<<<EOT
            <div class="observacoes observacoesLimites" style='border:1px solid silver; float:left;'>
                <div style="height:150px;"></div>
EOT;
    }
    if((isset($g['c029Ids'][217])) || (isset($g['c029Ids'][218]))){
        echo<<<EOT
            <div class="observacoes observacoesLimites" style='border:1px solid silver; float:left;'>
EOT;
    }
    if(isset($g['c029Ids'][217])){
        echo<<<EOT
            <div class="comercial" title='{$mT003['D024_Observacao_Comercial']}'>
                <b>Observação comercial:</b><br />
                <span style="color: var(--BGVermelho);">{$obsComercial}</span>
            </div>
            <div class="liberado">
                <span style="">Liberado Por: <span style="color: var(--BGVermelho);">{$mT003['Liberou']}</span></span>
            </div>
EOT;
    }
    if(isset($g['c029Ids'][218])){
        echo<<<EOT
            <div class="financeiro" title='{$mT003['D024_Observacao_Financeiro']}'>
                <b>Observação financeiro:</b><br />
                <span style="color: var(--BGVermelho);">{$obsFinanceiro}</span>
            </div>
EOT;

    }
        echo<<<EOT
            <div class="limite">
                Prazo Pagamento: <span style="color: var(--BGVermelho);">{$mT003['Prazo']}</span><br>
                Limite: <span style="color: var(--BGVermelho);">{$valorLimite}</span> Saldo: <span style="color: var(--BGVermelho);">{$saldoAtual}</span><br>
                Crédito Antecipado: <span style="color: var(--BGVermelho);">{$valorCreditoAntecipado}</span> Crédito Troca: <span style="color: var(--BGVermelho);">{$valorCreditoTroca}</span>
            </div>
            $status
        </div> 
EOT;
    break;

    case 'ven003TrocoPagamentoPDV':
        $sqlT007 = mysql_query("SELECT T007_Valor_Total FROM T007 WHERE T007_Id = '{$r_acaoId}'");
        $rowT007 = mysql_fetch_assoc($sqlT007);

        $sqlT118 = mysql_query("SELECT SUM(T118_Valor_Parcela) as Valor_Recebido FROM T118 WHERE T118_T007_Id = '{$r_acaoId}'");
        $rowT118 = mysql_fetch_assoc($sqlT118);

        if ($rowT007['T007_Valor_Total']>$rowT118['Valor_Recebido']) {
            $troco = $rowT007['T007_Valor_Total']-$rowT118['Valor_Recebido'];
            $labelDiferenca = 'Falta';
            $labelCor = 'red';
        } else {
            $troco = $rowT118['Valor_Recebido']-$rowT007['T007_Valor_Total'];
            $labelDiferenca = 'Troco';
            $labelCor = 'blue';
        }

        $troco = gCorrigeNumero($troco);
        $rowT118['Valor_Recebido'] = gCorrigeNumero($rowT118['Valor_Recebido']);
        $rowT007['T007_Valor_Total'] = gCorrigeNumero($rowT007['T007_Valor_Total']);
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right; font-size: 14px;">Total R$     <input type="text" style="font-size: 16px; height: 28px; font-weight:bold; margin-right: 5px;" value="{$rowT007['T007_Valor_Total']}" class="whiteBg" id="" size="6" readonly></span>
                <span class="item" style="text-align:right; font-size: 14px;">Recebido  R$ <input type="text" style="font-size: 16px; height: 28px; font-weight:bold; margin-right: 5px;" value="{$rowT118['Valor_Recebido']}"   class="whiteBg" id="" size="6" readonly></span>
                <span class="item" style="text-align:right; font-size: 14px;">$labelDiferenca R$     <input type="text" style="font-size: 16px; height: 28px; font-weight:bold; color:$labelCor;" value="{$troco}"     class="whiteBg" id="" size="6" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;
    break;

    case 'imprimirACP':
        require_once("bibliotecas/classes/VEN003.php");
        $VEN003 = new VEN003();
        $T007_Id = $r_T007_Id;
        $retorno = $VEN003->imprimirACP($T007_Id);

        echo $retorno;
        
    break;

    case 'excluirNfeComErro':
        // Excluir arquivo do disco
        $T007 = mysql_query("select T007_Numero_Nota_Fiscal from T007 where T007_Id='$r_acaoId'");
        $mT007 = mysql_fetch_array($T007);
        $arquivoNome = "/var/www/html/hardness/dados_usuarios/e229/nfe/xml/assinado/".$mT007['T007_Numero_Nota_Fiscal']."_1.xml";
        @unlink($arquivoNome);
        mysql_query("update T007 set T007_Chave_Acesso_Nfe='',T007_TX_Resposta_Codigo='0' where T007_Id='$r_acaoId'");
        echo $arquivoNome." excluído";   
           
    break;

    case 'finalizarEnvioEmail':

    
    break;
}
