<?php
namespace hardness;
global $g;
/*
# BLOCO E # INFORMAÇÕES PRINCIPAIS #
REG 	DESCRIÇÃO
E001	Abertura do Bloco
E100	Periodo de Apuracao do ICMS
E110 	Apuracao do ICMS
E200	Periodo de Apuracao do ICMS ST
E220	Apuracao do ICMS ST
E300	APURAÇÃO DO FUNDO DE COMBATE À POBREZA E DO ICMS DIFERENCIAL DE ALÍQUOTA – UF ORIGEM/DESTINO EC
E310	APURAÇÃO DO FUNDO DE COMBATE À POBREZA E DO ICMS DIFERENCIAL DE ALÍQUOTA – UF ORIGEM/DESTINO EC 87/15. (VÁLIDO A PARTIR DE 01/01/2017)
E316	OBRIGAÇÕES RECOLHIDAS OU A RECOLHER – FUNDO DE COMBATE À POBREZA E ICMS DIFERENCIAL DE ALÍQUOTA UF ORIGEM/DESTINO EC 87/15. (VÁLIDO A PARTIR DE 01/01/2017)
E500	Periodo de Apuracao do IPI
E510	Apuracao do IPI
E990	Encerramento do Bloco
*/

if (!function_exists('spedFiscalObterAjustesDetalhadosBlocoE')) {
	function spedFiscalObterAjustesDetalhadosBlocoE($dataInicial, $dataFinal, $dataInicialHora, $dataFinalHora, $dataNFRecebida) {
		global $g;

		$ajustes = array(
			'icms' => array(
				'totais' => array(
					'VL_TOT_AJ_DEBITOS' => 0,
					'VL_ESTORNOS_CRED' => 0,
					'VL_TOT_AJ_CREDITOS' => 0,
					'VL_ESTORNOS_DEB' => 0,
					'VL_TOT_DED' => 0,
					'DEB_ESP' => 0,
				),
				'registros' => array(),
			),
			'st' => array(),
			'difal_fcp' => array(),
		);

		$sqlAjustes = mysql_query("
			SELECT
				TRIM(D189.D189_Codigo_Ajuste) AS COD_AJ_APUR,
				TRIM(D189.D189_Descricao_Ajuste) AS DESCR_AJUSTE,
				TRIM(T298.T298_Descricao_Complementar_Ajuste) AS DESCR_COMPL_AJUSTE,
				IFNULL(T298.T298_Valor_ICMS, 0) AS VL_AJ_ITEM,
				D018.D018_UF AS UF_DOC,
				T007.T007_D024_Id AS COD_PART,
				CASE
					WHEN T007.T007_Flag_ACP = '6' THEN '55'
					WHEN T007.T007_Flag_ACP = '8' THEN '65'
					ELSE ''
				END AS COD_MOD,
				IFNULL(T007.T007_Serie, '') AS SER,
				'' AS SUB,
				IFNULL(T007.T007_Numero_Nota_Fiscal, '') AS NUM_DOC,
				T007.T007_Data_Emissao AS DT_DOC,
				IFNULL(D001.D001_Id, 0) AS D001_ID,
				IFNULL(D001.D001_Codigo_Produto, '') AS COD_ITEM,
				IFNULL(T007.T007_Chave_Acesso_Nfe, '') AS CHV_DOCE
			FROM T298
			INNER JOIN D189 ON D189.D189_Id = T298.T298_D189_Id
			INNER JOIN T297 ON T297.T297_Id = T298.T298_T297_Id
			INNER JOIN T007 ON T007.T007_Id = T297.T297_T007_Id
			LEFT JOIN D024 ON D024.D024_Id = T007.T007_D024_Id
			LEFT JOIN D018 ON D018.D018_Id = D024.D024_D018_Id
			LEFT JOIN T008 ON T008.T008_Id = T298.T298_T008_Id
			LEFT JOIN D009 ON D009.D009_Id = T008.T008_D009_Id
			LEFT JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
			LEFT JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
			WHERE T007.T007_Data_Emissao >= '{$dataInicial}'
			  AND T007.T007_Data_Emissao <= '{$dataFinal}'
			  AND T007.T007_C004_Id = '{$g['empresaAtual']}'
			  AND T007.T007_Flag_Cancelada != 'S'
			  AND T007.T007_Numero_Protocolo_Nfe > 0
			  AND T007.T007_Flag_ACP IN ('6', '8')

			UNION ALL

			SELECT
				TRIM(D189.D189_Codigo_Ajuste) AS COD_AJ_APUR,
				TRIM(D189.D189_Descricao_Ajuste) AS DESCR_AJUSTE,
				TRIM(T298.T298_Descricao_Complementar_Ajuste) AS DESCR_COMPL_AJUSTE,
				IFNULL(T298.T298_Valor_ICMS, 0) AS VL_AJ_ITEM,
				D018.D018_UF AS UF_DOC,
				T013.T013_D024_Id AS COD_PART,
				CASE
					WHEN T013.T013_Tipo = '1' THEN '55'
					WHEN T013.T013_Tipo = '5' THEN '01'
					WHEN T013.T013_Tipo = '6' THEN '06'
					WHEN T013.T013_Tipo = '7' THEN '21'
					WHEN T013.T013_Tipo = '8' THEN '22'
					WHEN T013.T013_Tipo = '11' THEN '66'
					WHEN T013.T013_Tipo = '12' THEN '62'
					ELSE ''
				END AS COD_MOD,
				IFNULL(T013.T013_Serie, '') AS SER,
				'' AS SUB,
				IFNULL(T013.T013_Numero_Nota_Fiscal, '') AS NUM_DOC,
				T013.T013_Data_Emissao AS DT_DOC,
				IFNULL(D001.D001_Id, 0) AS D001_ID,
				IFNULL(D001.D001_Codigo_Produto, '') AS COD_ITEM,
				IFNULL(T013.T013_Chave_Acesso_Nfe, '') AS CHV_DOCE
			FROM T298
			INNER JOIN D189 ON D189.D189_Id = T298.T298_D189_Id
			INNER JOIN T297 ON T297.T297_Id = T298.T298_T297_Id
			INNER JOIN T013 ON T013.T013_Id = T297.T297_T013_Id
			LEFT JOIN D024 ON D024.D024_Id = T013.T013_D024_Id
			LEFT JOIN D018 ON D018.D018_Id = D024.D024_D018_Id
			LEFT JOIN T014 ON T014.T014_Id = T298.T298_T014_Id
			LEFT JOIN D009 ON D009.D009_Id = T014.T014_D009_Id
			LEFT JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
			LEFT JOIN D001 ON D001.D001_Id = D049.D049_D001_Id
			WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
			  AND {$dataNFRecebida} <= '{$dataFinalHora}'
			  AND T013.T013_C004_Id = '{$g['empresaAtual']}'
			  AND T013.T013_Flag_Cancelada != 'S'
			  AND T013.T013_Flag_Estoque_Finalizado = 'S'
			  AND ((T013.T013_Tipo = '1' AND T013.T013_Numero_Protocolo_Nfe > 0) OR T013.T013_Tipo = '5')
		");

		while ($ajuste = mysql_fetch_array($sqlAjustes)) {
			$codigo = trim($ajuste['COD_AJ_APUR']);
			if (!preg_match('/^[A-Z]{2}[0-9]{6}$/', $codigo)) {
				continue;
			}

			$grupo = substr($codigo, 2, 1);
			$utilizacao = substr($codigo, 3, 1);
			if (!in_array($grupo, array('0', '1', '2', '3')) || !in_array($utilizacao, array('0', '1', '2', '3', '4', '5'))) {
				continue;
			}

			$valor = (float)$ajuste['VL_AJ_ITEM'];
			if ($valor <= 0) {
				continue;
			}

			$descricao = tirarEspacos(!empty($ajuste['DESCR_COMPL_AJUSTE']) ? $ajuste['DESCR_COMPL_AJUSTE'] : $ajuste['DESCR_AJUSTE']);
			$filho = array(
				'COD_PART' => trim($ajuste['COD_PART']),
				'COD_MOD' => trim($ajuste['COD_MOD']),
				'SER' => trim($ajuste['SER']),
				'SUB' => trim($ajuste['SUB']),
				'NUM_DOC' => trim($ajuste['NUM_DOC']),
				'DT_DOC' => formataData(substr($ajuste['DT_DOC'], 0, 10)),
				'D001_ID' => (int)$ajuste['D001_ID'],
				'COD_ITEM' => trim($ajuste['COD_ITEM']),
				'VL_AJ_ITEM' => $valor,
				'CHV_DOCE' => removeChars($ajuste['CHV_DOCE']),
			);

			if (!empty($filho['COD_PART'])) {
				verificaRegistro0150($filho['COD_PART']);
			}
			if (!empty($filho['D001_ID'])) {
				verificaRegistro0200($filho['D001_ID']);
			}

			if ($grupo == '0') {
				if (!isset($ajustes['icms']['registros'][$codigo])) {
					$ajustes['icms']['registros'][$codigo] = array(
						'descricao' => $descricao,
						'valor' => 0,
						'filhos' => array(),
					);
				}
				$ajustes['icms']['registros'][$codigo]['valor'] += $valor;
				$ajustes['icms']['registros'][$codigo]['filhos'][] = $filho;

				switch ($utilizacao) {
					case '0': $ajustes['icms']['totais']['VL_TOT_AJ_DEBITOS'] += $valor; break;
					case '1': $ajustes['icms']['totais']['VL_ESTORNOS_CRED'] += $valor; break;
					case '2': $ajustes['icms']['totais']['VL_TOT_AJ_CREDITOS'] += $valor; break;
					case '3': $ajustes['icms']['totais']['VL_ESTORNOS_DEB'] += $valor; break;
					case '4': $ajustes['icms']['totais']['VL_TOT_DED'] += $valor; break;
					case '5': $ajustes['icms']['totais']['DEB_ESP'] += $valor; break;
				}
				continue;
			}

			$uf = trim($ajuste['UF_DOC']);
			if (empty($uf) || $uf == 'EX') {
				continue;
			}

			if ($grupo == '1') {
				if (!isset($ajustes['st'][$uf])) {
					$ajustes['st'][$uf] = array(
						'totais' => array(
							'VL_OUT_CRED_ST' => 0,
							'VL_OUT_DEB_ST' => 0,
							'VL_DEDUCOES_ST' => 0,
							'DEB_ESP_ST' => 0,
						),
						'registros' => array(),
					);
				}
				if (!isset($ajustes['st'][$uf]['registros'][$codigo])) {
					$ajustes['st'][$uf]['registros'][$codigo] = array(
						'descricao' => $descricao,
						'valor' => 0,
						'filhos' => array(),
					);
				}
				$ajustes['st'][$uf]['registros'][$codigo]['valor'] += $valor;
				$ajustes['st'][$uf]['registros'][$codigo]['filhos'][] = $filho;

				switch ($utilizacao) {
					case '0':
					case '1':
						$ajustes['st'][$uf]['totais']['VL_OUT_DEB_ST'] += $valor;
					break;
					case '2':
					case '3':
						$ajustes['st'][$uf]['totais']['VL_OUT_CRED_ST'] += $valor;
					break;
					case '4':
						$ajustes['st'][$uf]['totais']['VL_DEDUCOES_ST'] += $valor;
					break;
					case '5':
						$ajustes['st'][$uf]['totais']['DEB_ESP_ST'] += $valor;
					break;
				}
				continue;
			}

			if (!isset($ajustes['difal_fcp'][$uf])) {
				$ajustes['difal_fcp'][$uf] = array(
					'difal' => array(
						'totais' => array(
							'VL_OUT_DEB_DIFAL' => 0,
							'VL_OUT_CRED_DIFAL' => 0,
							'VL_DEDUCOES_DIFAL' => 0,
							'DEB_ESP_DIFAL' => 0,
						),
						'registros' => array(),
					),
					'fcp' => array(
						'totais' => array(
							'VL_OUT_DEB_FCP' => 0,
							'VL_OUT_CRED_FCP' => 0,
							'VL_DEDUCOES_FCP' => 0,
							'DEB_ESP_FCP' => 0,
						),
						'registros' => array(),
					),
				);
			}

			$chaveGrupo = ($grupo == '2') ? 'difal' : 'fcp';
			if (!isset($ajustes['difal_fcp'][$uf][$chaveGrupo]['registros'][$codigo])) {
				$ajustes['difal_fcp'][$uf][$chaveGrupo]['registros'][$codigo] = array(
					'descricao' => $descricao,
					'valor' => 0,
					'filhos' => array(),
				);
			}
			$ajustes['difal_fcp'][$uf][$chaveGrupo]['registros'][$codigo]['valor'] += $valor;
			$ajustes['difal_fcp'][$uf][$chaveGrupo]['registros'][$codigo]['filhos'][] = $filho;

			if ($grupo == '2') {
				switch ($utilizacao) {
					case '0':
					case '1':
						$ajustes['difal_fcp'][$uf]['difal']['totais']['VL_OUT_DEB_DIFAL'] += $valor;
					break;
					case '2':
					case '3':
						$ajustes['difal_fcp'][$uf]['difal']['totais']['VL_OUT_CRED_DIFAL'] += $valor;
					break;
					case '4':
						$ajustes['difal_fcp'][$uf]['difal']['totais']['VL_DEDUCOES_DIFAL'] += $valor;
					break;
					case '5':
						$ajustes['difal_fcp'][$uf]['difal']['totais']['DEB_ESP_DIFAL'] += $valor;
					break;
				}
			} else {
				switch ($utilizacao) {
					case '0':
					case '1':
						$ajustes['difal_fcp'][$uf]['fcp']['totais']['VL_OUT_DEB_FCP'] += $valor;
					break;
					case '2':
					case '3':
						$ajustes['difal_fcp'][$uf]['fcp']['totais']['VL_OUT_CRED_FCP'] += $valor;
					break;
					case '4':
						$ajustes['difal_fcp'][$uf]['fcp']['totais']['VL_DEDUCOES_FCP'] += $valor;
					break;
					case '5':
						$ajustes['difal_fcp'][$uf]['fcp']['totais']['DEB_ESP_FCP'] += $valor;
					break;
				}
			}
		}

		if (!empty($ajustes['icms']['registros'])) {
			ksort($ajustes['icms']['registros']);
		}
		if (!empty($ajustes['st'])) {
			ksort($ajustes['st']);
			foreach ($ajustes['st'] as &$ajusteST) {
				if (!empty($ajusteST['registros'])) {
					ksort($ajusteST['registros']);
				}
			}
			unset($ajusteST);
		}
		if (!empty($ajustes['difal_fcp'])) {
			ksort($ajustes['difal_fcp']);
			foreach ($ajustes['difal_fcp'] as &$ajusteUF) {
				if (!empty($ajusteUF['difal']['registros'])) {
					ksort($ajusteUF['difal']['registros']);
				}
				if (!empty($ajusteUF['fcp']['registros'])) {
					ksort($ajusteUF['fcp']['registros']);
				}
			}
			unset($ajusteUF);
		}

		return $ajustes;
	}

	function spedFiscalRenderizarFilhosAjusteBlocoE(&$layout, &$contador_bloco_E, $registroPai, array $filhos) {
		foreach ($filhos as $filho) {
			switch ($registroPai) {
				case 'E111':
					$registroFilho = array(
						'E113',
						$filho['COD_PART'],
						$filho['COD_MOD'],
						$filho['SER'],
						$filho['SUB'],
						$filho['NUM_DOC'],
						$filho['DT_DOC'],
						$filho['COD_ITEM'],
						formataDecimal($filho['VL_AJ_ITEM']),
						$filho['CHV_DOCE'],
					);
				break;
				case 'E220':
					$registroFilho = array(
						'E240',
						$filho['COD_PART'],
						$filho['COD_MOD'],
						$filho['SER'],
						$filho['SUB'],
						$filho['NUM_DOC'],
						$filho['DT_DOC'],
						$filho['COD_ITEM'],
						formataDecimal($filho['VL_AJ_ITEM']),
						$filho['CHV_DOCE'],
					);
				break;
				case 'E311':
					$registroFilho = array(
						'E313',
						$filho['COD_PART'],
						$filho['COD_MOD'],
						$filho['SER'],
						$filho['SUB'],
						$filho['NUM_DOC'],
						$filho['CHV_DOCE'],
						$filho['DT_DOC'],
						$filho['COD_ITEM'],
						formataDecimal($filho['VL_AJ_ITEM']),
					);
				break;
				default:
					$registroFilho = array();
				break;
			}

			if (empty($registroFilho)) {
				continue;
			}

			$layout .= '|' . implode('|', $registroFilho) . '|' . _SALTO_LINHA;
			$contador_bloco_E++;
		}
	}
}

$contador_bloco_E = 1;
$ajustesDetalhadosBlocoE = ($gerarBlocoEDetalhado == 'S')
	? spedFiscalObterAjustesDetalhadosBlocoE($dataInicial, $dataFinal, $dataInicialHora, $dataFinalHora, $dataNFRecebida)
	: array(
		'icms' => array('totais' => array(), 'registros' => array()),
		'st' => array(),
		'difal_fcp' => array(),
	);

$IND_MOV = '0';
# REGISTRO E001
$E001 = array(													#TAM								
	$REG = 'E001',												#004* 
	$IND_MOV													#001*	Indicador de movimento (0 Bloco com dados informados/1 Bloco sem dados informados)
); 
$E001 = implode("|", $E001);
$layout .= '|' . $E001 . '|' . _SALTO_LINHA;

if($IND_MOV == '0'){
	# REGISTRO E100
	$E100 = array(												#TAM								
		$REG = 'E100',											#004* 
		$DT_INI = formataData($dataInicial),					#008*	Data inicial a que a apuração se refere
		$DT_FIN = formataData($dataFinal),						#008*	Data final a que a apuração se refere
	); 
	$E100 = implode("|", $E100);
	$layout .= '|' . $E100 . '|' . _SALTO_LINHA;
	$contador_bloco_E++;

	# REGISTRO E110
	$sqlT008 = mysql_query("SELECT SUM(T008_Valor_ICMS) AS TotalICMS 
							FROM T008
							LEFT JOIN T007 ON T007_Id = T008_T007_Id
							LEFT JOIN D006 ON D006_Id = T008_D006_Id
							WHERE (T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}')
							AND T007_C004_Id = '{$g['empresaAtual']}' 
							AND T007_Flag_Cancelada != 'S'
							AND T007_Numero_Protocolo_Nfe > 0
							AND T007_Flag_ACP IN('1','6')
							AND (D006_Codigo_CFOP LIKE '5%' OR D006_Codigo_CFOP LIKE '6%' OR D006_Codigo_CFOP LIKE '7%' OR (D006_Codigo_CFOP='1.605' OR D006_Codigo_CFOP='1605'));");

	$T008 = mysql_fetch_array($sqlT008);
	$VL_TOT_DEBITOS = ($T008['TotalICMS'] > 0) ? $T008['TotalICMS'] : '0';	
	
	// $sqlT008 = mysql_query("SELECT SUM(T008_Valor_ICMS) AS TotalICMS 
	// 						FROM T008
	// 						LEFT JOIN T007 ON T007_Id = T008_T007_Id
	// 						LEFT JOIN D006 ON D006_Id = T008_D006_Id
	// 						WHERE (T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}')
	// 						AND T007_C004_Id = '{$g['empresaAtual']}' 
	// 						AND T007_Flag_Cancelada != 'S'
	// 						AND T007_Numero_Protocolo_Nfe > 0
	// 						AND T007_Flag_ACP IN('1','6')
	// 						AND (D006_Codigo_CFOP LIKE '1.%' OR D006_Codigo_CFOP LIKE '2.%' OR D006_Codigo_CFOP LIKE '3.%' OR D006_Codigo_CFOP='5.605');"); // SEGUNDO O MANUAL, TEM QUE SER ESSAS CONDIÇÕES DA CLÁUSULA. NÃO USEI O FLAG_ENTRAD_SAIDA DA CFOP PORQUE ISSO PODE ESTAR ERRADO NO CADASTRO DA CFOP
	// $T008 = mysql_fetch_array($sqlT008);	
 	
	$sqlT014 = mysql_query("SELECT SUM(TotalICMS) as TotalICMS
  							FROM (									
									SELECT SUM(T008_Valor_ICMS) AS TotalICMS 
									  FROM T008
					   			 LEFT JOIN T007 ON T007_Id = T008_T007_Id
					   			 LEFT JOIN D006 ON D006_Id = T007_D006_Id
						   			 WHERE (T007_Data_Emissao >= '{$dataInicial}' 
						   	 		   AND T007_Data_Emissao <= '{$dataFinal}')
							 		   AND T007_C004_Id = '{$g['empresaAtual']}' 
							 		   AND T007_Flag_Cancelada != 'S'
							 		   AND T007_Numero_Protocolo_Nfe > 0
							 		   AND T007_Flag_ACP IN('1','6')
							 		   AND (D006_Codigo_CFOP LIKE '1%' OR D006_Codigo_CFOP LIKE '2%' OR D006_Codigo_CFOP LIKE '3%' OR (D006_Codigo_CFOP = '5.605' OR D006_Codigo_CFOP = '5605'))
								     UNION
									SELECT SUM(T014_Valor_ICMS) AS TotalICMS
									  FROM T014
								 LEFT JOIN T013 ON T013_Id = T014_T013_Id
								 LEFT JOIN D006 AS D006_Item ON D006_Item.D006_Id = T014_D006_Id
								 LEFT JOIN D006 AS D006_Cab ON D006_Cab.D006_Id = T013_D006_Id
								  	 WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
									   AND {$dataNFRecebida} <= '{$dataFinalHora}'
									   AND T013_C004_Id = '{$g['empresaAtual']}' 
									   AND T013_Flag_Cancelada != 'S' 
									   AND ((T013_Tipo = '1' AND T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo IN('6'))
									   AND T013_Flag_Estoque_Finalizado = 'S'
									   AND (D006_Item.D006_Codigo_CFOP LIKE '1%' OR D006_Item.D006_Codigo_CFOP LIKE '2%' OR D006_Item.D006_Codigo_CFOP LIKE '3%' OR (D006_Item.D006_Codigo_CFOP = '5.605' OR D006_Item.D006_Codigo_CFOP = '5605'))
									   AND D006_Item.D006_Flag_Deb_Crd_ICMS = 'S'
	  							     UNION
									SELECT SUM(T084_Valor_ICMS) AS TotalICMS
									  FROM T084
								 LEFT JOIN D006 ON D006_Id = T084_D006_Id
								  	 WHERE T084_Data_Lancamento >= '{$dataInicial}' 
									   AND T084_Data_Lancamento <= '{$dataFinal}'
									   AND T084_C004_Id = '{$g['empresaAtual']}' 
									   AND T084_Flag_Cancelado != 'S' 
									   AND T084_Numero_Protocolo_Cte > 0
									   AND T084_Modelo IN( '07', '08', '8B', '09', '10', '11', '26', '27', '57', '67', '63')
									   AND D006_Flag_Deb_Crd_ICMS = 'S'
								) AS documentos");

	$T014 = mysql_fetch_array($sqlT014);
	$VL_TOT_CREDITOS = ($T014['TotalICMS'] > 0) ? $T014['TotalICMS'] : '0';

	
		$VL_AJ_DEBITOS = '0';
		$VL_TOT_AJ_DEBITOS = isset($ajustesDetalhadosBlocoE['icms']['totais']['VL_TOT_AJ_DEBITOS']) ? $ajustesDetalhadosBlocoE['icms']['totais']['VL_TOT_AJ_DEBITOS'] : '0';
		$VL_ESTORNOS_CRED = isset($ajustesDetalhadosBlocoE['icms']['totais']['VL_ESTORNOS_CRED']) ? $ajustesDetalhadosBlocoE['icms']['totais']['VL_ESTORNOS_CRED'] : '0';
		$VL_AJ_CREDITOS = '0';
		$VL_TOT_AJ_CREDITOS = isset($ajustesDetalhadosBlocoE['icms']['totais']['VL_TOT_AJ_CREDITOS']) ? $ajustesDetalhadosBlocoE['icms']['totais']['VL_TOT_AJ_CREDITOS'] : '0';
		$VL_ESTORNOS_DEB = isset($ajustesDetalhadosBlocoE['icms']['totais']['VL_ESTORNOS_DEB']) ? $ajustesDetalhadosBlocoE['icms']['totais']['VL_ESTORNOS_DEB'] : '0';
		$VL_SLD_CREDOR_ANT = '0';
		$VL_TOT_DED = isset($ajustesDetalhadosBlocoE['icms']['totais']['VL_TOT_DED']) ? $ajustesDetalhadosBlocoE['icms']['totais']['VL_TOT_DED'] : '0';
		$VL_ICMS_RECOLHER = '0';
		$DEB_ESP = isset($ajustesDetalhadosBlocoE['icms']['totais']['DEB_ESP']) ? $ajustesDetalhadosBlocoE['icms']['totais']['DEB_ESP'] : '0';

	$VL_SLD_APURADO = ($VL_TOT_DEBITOS + ($VL_AJ_DEBITOS + $VL_TOT_AJ_DEBITOS) + $VL_ESTORNOS_CRED) - ($VL_TOT_CREDITOS + ($VL_AJ_CREDITOS + $VL_TOT_AJ_CREDITOS) + $VL_ESTORNOS_DEB + $VL_SLD_CREDOR_ANT);
	if($VL_SLD_APURADO >= 0){
		$VL_SLD_CREDOR_TRANSPORTAR = 0;
	} else {
		$VL_SLD_CREDOR_TRANSPORTAR = $VL_SLD_APURADO;
		$VL_SLD_APURADO = 0;
	} 
	$VL_ICMS_RECOLHER = $VL_SLD_APURADO - $VL_TOT_DED;
	$E110 = array(												#TAM								
		$REG = 'E110',											#004* 
		formataDecimal($VL_TOT_DEBITOS),						#	  	Valor total dos débitos por "Saídas e prestações com débito do imposto"
		formataDecimal($VL_AJ_DEBITOS),							#	  	Valor total dos ajustes a débito decorrentes do documento fiscal.
		formataDecimal($VL_TOT_AJ_DEBITOS),						#	  	Valor total de "Ajustes a débito"
		formataDecimal($VL_ESTORNOS_CRED), 						#	  	Valor total de Ajustes “Estornos de créditos”
		formataDecimal($VL_TOT_CREDITOS),						#	  	Valor total dos créditos por "Entradas e aquisições com crédito do imposto"
		formataDecimal($VL_AJ_CREDITOS),						#	  	Valor total dos ajustes a crédito decorrentes do documento fiscal.
		formataDecimal($VL_TOT_AJ_CREDITOS),					#	  	Valor total de "Ajustes a crédito"
		formataDecimal($VL_ESTORNOS_DEB),						#	  	Valor total de Ajustes “Estornos de Débitos”
		formataDecimal($VL_SLD_CREDOR_ANT),						#	  	Valor total de "Saldo credor do período anterior"
		formataDecimal($VL_SLD_APURADO),						#	  	Valor do saldo devedor apurado
		formataDecimal($VL_TOT_DED),							#	  	Valor total de "Deduções"
		formataDecimal($VL_ICMS_RECOLHER),						#	  	Valor total de "ICMS a recolher (11-12)
		formataDecimal(abs($VL_SLD_CREDOR_TRANSPORTAR)),		#	  	Valor total de "Saldo credor a transportar para o período seguinte”
			formataDecimal($DEB_ESP)								#	  	Valores recolhidos ou a recolher, extraapuração.
		); 
		$E110 = implode("|", $E110);
		$layout .= '|' . $E110 . '|' . _SALTO_LINHA;
		$contador_bloco_E++;

		if ($gerarBlocoEDetalhado == 'S' && !empty($ajustesDetalhadosBlocoE['icms']['registros'])) {
			foreach ($ajustesDetalhadosBlocoE['icms']['registros'] as $codigoAjuste => $ajusteICMS) {
				$E111 = array(
					'E111',
					$codigoAjuste,
					$ajusteICMS['descricao'],
					formataDecimal($ajusteICMS['valor'])
				);
				$layout .= '|' . implode('|', $E111) . '|' . _SALTO_LINHA;
				$contador_bloco_E++;
				spedFiscalRenderizarFilhosAjusteBlocoE($layout, $contador_bloco_E, 'E111', $ajusteICMS['filhos']);
			}
		}

	// EBS -      |E110|22853,21|0|2804,07|0|9734,49|0|8259,22|0|15929,67|0|0|0|8266,1|0|

	// HARDNESS - |E110|22853,21|0|0,00   |0|58,49  |0|0,00   |0|0,00    |22794,72|0,00|22794,72|0,00|0|

	# REGISTRO E116
	$sqlT008 = mysql_query("SELECT SUM(T008_Valor_ICMS) AS TotalICMS 
							  FROM T008
						 LEFT JOIN T007 ON T007_Id = T008_T007_Id
						 LEFT JOIN D006 ON D006_Id = T008_D006_Id
						 	 WHERE (T007_Data_Emissao >= '{$dataInicial}' 
							   AND T007_Data_Emissao <= '{$dataFinal}')
							   AND T007_C004_Id = '{$g['empresaAtual']}' 
							   AND T007_Flag_Cancelada != 'S'
							   AND T007_Numero_Protocolo_Nfe > 0
							   AND T007_Flag_ACP IN('1','6')
							   AND (D006_Codigo_CFOP LIKE '5%' OR D006_Codigo_CFOP LIKE '6%' OR D006_Codigo_CFOP LIKE '7%' OR (D006_Codigo_CFOP='1.605' OR D006_Codigo_CFOP='1605'));");

	$T008    = mysql_fetch_array($sqlT008);
	$DT_VCTO = date($dataInicial, strtotime("+1 month", strtotime($dataInicial)));
	$DT_VCTO = formataData($DT_VCTO);
	$MES_REF = $mes.$ano;

	// Valor da ST comente das devoluções.
	$sqlSTDV = <<<EOT
					SELECT SUM(T008_Valor_ICMS) as ValorICMS
				      FROM T008
	   	         LEFT JOIN T007 ON T007_Id = T008_T007_Id 
	   	         LEFT JOIN D006 ON D006_Id = T007_D006_Id 
	   	         LEFT JOIN D024 ON D024_Id = T007_D024_Id 
	   	         LEFT JOIN D018 ON D018_Id = D024_D018_Id 
	                 WHERE (T007_Data_Emissao >= '{$dataInicial}' 
					   AND T007_Data_Emissao <= '{$dataFinal}')
	                   AND T007_C004_Id = '{$g['empresaAtual']}' 
	                   AND T007_Flag_Cancelada != 'S'
	                   AND T007_Numero_Protocolo_Nfe > 0
	                   AND T007_Flag_ACP IN('1','6') 
   	                   AND (D006_Codigo_CFOP LIKE '1%' OR D006_Codigo_CFOP LIKE '2%' OR D006_Codigo_CFOP LIKE '3%' OR (D006_Codigo_CFOP='5.605' OR D006_Codigo_CFOP='5605'));
EOT;

	$mSqlSTDV          = mysql_fetch_array(mysql_query($sqlSTDV));
	$T008['TotalICMS'] = $T008['TotalICMS'] - $mSqlSTDV['ValorICMS'];	
	//$VL_OR			   = ($T008['TotalICMS'] > 0) ? $T008['TotalICMS'] : '0';	
	$VL_OR = $VL_ICMS_RECOLHER;
	$E116 = array(
		$REG = 'E116',
		$COD_OR = '000',									# Código da obrigação a recolher, conforme a Tabela 5.4
		formataDecimal($VL_OR),								# Valor da obrigação a recolher
		$DT_VCTO,											# Data de vencimento da obrigação
		$COD_REC = trim(empty($g['C031']['CodigoReceitaSPED']) ? '100099' : $g['C031']['CodigoReceitaSPED']),	# Código de receita referente à obrigação, próprio da unidade da federação, conforme legislação estadual
		$NUM_PROC = '',										# Número do processo ou auto de infração ao qual a obrigação está vinculada, se houver.
		$IND_PROC = '',										# Indicador da origem do processo: (0- SEFAZ / 1- Justiça Federal / 2- Justiça Estadual / 9- Outros)
		$PROC = '',											# Descrição resumida do processo que embasou o lançamento
		$TXT_COMPL = '',									# Descrição complementar das obrigações a recolher
		$MES_REF											#* Informe o mês de referência no formato mmaaaa
	); 
	$E116 = implode("|", $E116);
	$layout .= '|' . $E116 . '|' . _SALTO_LINHA;
	$contador_bloco_E++;

	# REGISTRO E200
	$sqlT008ST = mysql_query("SELECT D018_Id,
									 D018_UF, 
									 SUM(T008_Valor_ICMS_Substituicao_Retencao) as ValorICMSST 
							    FROM T008 
						   LEFT JOIN T007 ON T007_Id = T008_T007_Id
						   LEFT JOIN D006 ON D006_Id = T007_D006_Id
						   LEFT JOIN D024 ON D024_Id = T007_D024_Id
						   LEFT JOIN D018 ON D018_Id = D024_D018_Id
							   WHERE (T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}')
							     AND T007_C004_Id = '{$g['empresaAtual']}' 
							     AND T007_Flag_Cancelada != 'S'
							     AND T007_Numero_Protocolo_Nfe > 0
							     AND T007_Flag_ACP IN('1','6') 
							     AND (D006_Codigo_CFOP LIKE '5%' OR D006_Codigo_CFOP LIKE '6%' OR D006_Codigo_CFOP LIKE '7%' OR (D006_Codigo_CFOP='1.605' OR D006_Codigo_CFOP='1605'))
							GROUP BY D018_UF");

	$id_UF_ICMS_ST = array();
	while($T008ST = mysql_fetch_array($sqlT008ST)){

		// Array para ser utilizado no bloco 0 - Registro 0015
		if (!in_array($T008ST['D018_Id'], $id_UF_ICMS_ST)) {
			$id_UF_ICMS_ST[] = $T008ST['D018_Id'];
		}

		// Valor da ST comente das devoluções.
		$sqlSTDV = <<<EOT
						SELECT SUM(T008_Valor_ICMS_Substituicao_Retencao) as ValorICMSST
						  FROM T008
			         LEFT JOIN T007 ON T007_Id = T008_T007_Id 
			         LEFT JOIN D006 ON D006_Id = T007_D006_Id 
			         LEFT JOIN D024 ON D024_Id = T007_D024_Id 
			         LEFT JOIN D018 ON D018_Id = D024_D018_Id 
		                 WHERE (T007_Data_Emissao >= '{$dataInicial}' 
						   AND T007_Data_Emissao <= '{$dataFinal}')
						   AND T007_C004_Id = '{$g['empresaAtual']}' 
						   AND T007_Flag_Cancelada != 'S'
						   AND D018_UF='{$T008ST['D018_UF']}' 
						   AND T007_Numero_Protocolo_Nfe > 0
						   AND T007_Flag_ACP IN('1','6') 
						   AND D006_Flag_Venda_Compra_Outros='DV'
EOT;
		$mSqlSTDV = mysql_fetch_array(mysql_query($sqlSTDV));

		// Valor da ST de NF Recevida 
		$stNFNFEntrada = mysql_query("SELECT SUM(T014_Valor_ICMS_Substituicao) AS ValorICMSSTEntrada
  									   FROM T014
								  LEFT JOIN T013 ON T013_Id = T014_T013_Id
								  LEFT JOIN D006 ON D006_Id = T014_D006_Id
								  LEFT JOIN D024 ON D024_Id = T013_D024_Id 
								  LEFT JOIN D018 ON D018_Id = D024_D018_Id 
	                                  WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
	                                    AND {$dataNFRecebida} <= '{$dataFinalHora}'
	                                    AND T013_C004_Id = '{$g['empresaAtual']}'  
	                                    AND T013_Flag_Cancelada != 'S' 
										AND D018_UF='{$T008ST['D018_UF']}' 
	                                    AND T013_Numero_Protocolo_Nfe > 0 
										AND T013_Tipo = '1' AND T013_Flag_Estoque_Finalizado = 'S'
										AND (D006_Codigo_CFOP LIKE '1%' OR D006_Codigo_CFOP LIKE '2%')");

		$mStNFNFEntrada = mysql_fetch_array($stNFNFEntrada);

		$E200 = array(											#TAM								
			$REG = 'E200',										#004* 
			$UF = $T008ST['D018_UF'], 							#002*	Sigla da unidade da federação a que se refere a apuração do ICMS ST
			$DT_INI = formataData($dataInicial),				#008*	Data inicial a que a apuração se refere
			$DT_FIN = formataData($dataFinal),					#008*	Data final a que a apuração se refere
		); 
		$E200 = implode("|", $E200);
		$layout .= '|' . $E200 . '|' . _SALTO_LINHA;
		$contador_bloco_E++;

			$ajustesSTUF = isset($ajustesDetalhadosBlocoE['st'][$T008ST['D018_UF']]) ? $ajustesDetalhadosBlocoE['st'][$T008ST['D018_UF']] : array('totais' => array(), 'registros' => array());

			$VL_SLD_CRED_ANT_ST = '0';
			$VL_DEVOL_ST = $mSqlSTDV['ValorICMSST'];
			$VL_RESSARC_ST = '0';
			$VL_OUT_CRED_ST = $mStNFNFEntrada['ValorICMSSTEntrada'] + (isset($ajustesSTUF['totais']['VL_OUT_CRED_ST']) ? $ajustesSTUF['totais']['VL_OUT_CRED_ST'] : 0);
			$VL_AJ_CREDITOS_ST = '0';
			$VL_OUT_DEB_ST = isset($ajustesSTUF['totais']['VL_OUT_DEB_ST']) ? $ajustesSTUF['totais']['VL_OUT_DEB_ST'] : '0';
			$VL_AJ_DEBITOS_ST = '0';
			$VL_SLD_DEV_ANT_ST = '0';
			$VL_DEDUCOES_ST = isset($ajustesSTUF['totais']['VL_DEDUCOES_ST']) ? $ajustesSTUF['totais']['VL_DEDUCOES_ST'] : '0';
			$VL_ICMS_RECOL_ST = '0';
			$VL_SLD_CRED_ST_TRANSPORTAR = '0';
			$DEB_ESP_ST = isset($ajustesSTUF['totais']['DEB_ESP_ST']) ? $ajustesSTUF['totais']['DEB_ESP_ST'] : '0';

		$IND_MOV_ST = ($T008ST['ValorICMSST'] > 0) ? '1' : '0';
		$VL_RETENÇAO_ST = $T008ST['ValorICMSST'];
		$VL_SLD_DEV_ANT_ST = ($VL_RETENÇAO_ST + $VL_OUT_DEB_ST + $VL_AJ_DEBITOS_ST) - ($VL_SLD_CRED_ANT_ST + $VL_DEVOL_ST + $VL_RESSARC_ST + $VL_OUT_CRED_ST + $VL_AJ_CREDITOS_ST);
		if($VL_SLD_DEV_ANT_ST >= 0){
		# REGISTRO E210
			$VL_SLD_CRED_ST_TRANSPORTAR = 0;
		} else {
			$VL_SLD_CRED_ST_TRANSPORTAR = $VL_SLD_DEV_ANT_ST;
			$VL_SLD_DEV_ANT_ST = 0;
		}
		$VL_SLD_CRED_ST_TRANSPORTAR = ($VL_SLD_CRED_ST_TRANSPORTAR<=0) ? ($VL_SLD_CRED_ST_TRANSPORTAR*(-1)) : $VL_SLD_CRED_ST_TRANSPORTAR;

		$VL_ICMS_RECOL_ST = $VL_SLD_DEV_ANT_ST - $VL_DEDUCOES_ST;
		$E210 = array(											#TAM								
			$REG = 'E210',										#004* 
			$IND_MOV_ST,										# Indicador de movimento
			formataDecimal($VL_SLD_CRED_ANT_ST),				# Valor do "Saldo credor de período anterior – Substituição Tributária"
			formataDecimal($VL_DEVOL_ST),						# Valor total do ICMS ST de devolução de mercadorias
			formataDecimal($VL_RESSARC_ST),						# Valor total do ICMS ST de ressarcimentos
			formataDecimal($VL_OUT_CRED_ST),					# Valor total de Ajustes "Outros créditos ST" e “Estorno de débitos ST”
			formataDecimal($VL_AJ_CREDITOS_ST), 				# Valor total dos ajustes a crédito de ICMS ST, provenientes de ajustes do documento fiscal.
			formataDecimal($VL_RETENÇAO_ST),					# Valor Total do ICMS retido por Substituição Tributária
			formataDecimal($VL_OUT_DEB_ST),						# Valor Total dos ajustes "Outros débitos ST" " e “Estorno de créditos ST”
			formataDecimal($VL_AJ_DEBITOS_ST),					# Valor total dos ajustes a débito de ICMS ST, provenientes de ajustes do documento fiscal.
			formataDecimal($VL_SLD_DEV_ANT_ST),					# Valor total de Saldo devedor antes das deduções
			formataDecimal($VL_DEDUCOES_ST),					# Valor total dos ajustes "Deduções ST"
			formataDecimal($VL_ICMS_RECOL_ST),					# Imposto a recolher ST (11-12)
			formataDecimal($VL_SLD_CRED_ST_TRANSPORTAR),		# Saldo credor de ST a transportar para o período seguinte [(03+04+05+06+07)– (08+09+10)].
			formataDecimal($DEB_ESP_ST)							# Valores recolhidos ou a recolher, extraapuração.
		); 
			$E210 = implode("|", $E210);
			$layout .= '|' . $E210 . '|' . _SALTO_LINHA;
			$contador_bloco_E++;

			if ($gerarBlocoEDetalhado == 'S' && !empty($ajustesSTUF['registros'])) {
				foreach ($ajustesSTUF['registros'] as $codigoAjuste => $ajusteST) {
					$E220 = array(
						'E220',
						$codigoAjuste,
						$ajusteST['descricao'],
						formataDecimal($ajusteST['valor'])
					);
					$layout .= '|' . implode('|', $E220) . '|' . _SALTO_LINHA;
					$contador_bloco_E++;
					spedFiscalRenderizarFilhosAjusteBlocoE($layout, $contador_bloco_E, 'E220', $ajusteST['filhos']);
				}
			}

		/*
		|E210|1|0|0|0|0|0|875,35|0|0|875,35|0|875,35|0|0|

		|E210|1|0,00|0,00|0,00|0,00|0,00|30,05|0,00|0,00|30,05|0,00|30,05|0,00|0,00|

		|E210|1|0,00|0,00|0,00|0,00|0,00|157,00|0,00|0,00|157,00|0,00|157,00|0,00|0,00|
		|E250|999|157,52|01012016|1015|||||012016|
		*/
		# REGISTRO E250
		$sqlT008 = mysql_query("SELECT D006_Flag_Estado,
									   SUM(T008_Valor_ICMS_Substituicao_Retencao) as ValorICMSST 
								FROM T008
								LEFT JOIN T007 ON T007_Id = T008_T007_Id
								LEFT JOIN D024 ON D024_Id = T007_D024_Id
								LEFT JOIN D018 ON D018_Id = D024_D018_Id
								LEFT JOIN D006 ON D006_Id = T007_D006_Id
								WHERE T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}'
								AND T007_C004_Id = '{$g['empresaAtual']}' AND T007_Flag_Cancelada != 'S' 
								AND T007_Numero_Protocolo_Nfe > 0 AND D018_UF = '{$T008ST['D018_UF']}'
								AND (D006_Codigo_CFOP LIKE '5%' OR D006_Codigo_CFOP LIKE '6%' OR D006_Codigo_CFOP LIKE '7%' OR (D006_Codigo_CFOP='1.605' OR D006_Codigo_CFOP='1605'))
								GROUP BY D006_Flag_Estado");
		while($T008 = mysql_fetch_array($sqlT008)){

			// Valor da ST comente das devoluções.
			$sqlSTDV = <<<EOT
			SELECT 
				SUM(T008_Valor_ICMS_Substituicao_Retencao) as ValorICMSST
			FROM T008
				LEFT JOIN T007 ON T007_Id = T008_T007_Id 
				LEFT JOIN D006 ON D006_Id = T007_D006_Id 
				LEFT JOIN D024 ON D024_Id = T007_D024_Id 
				LEFT JOIN D018 ON D018_Id = D024_D018_Id 
			WHERE 
			(T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}')
			AND T007_C004_Id = '{$g['empresaAtual']}' 
			AND T007_Flag_Cancelada != 'S'
			AND T007_Numero_Protocolo_Nfe > 0
			AND T007_Flag_ACP IN('1','6') 
			AND D018_UF='{$T008ST['D018_UF']}' 
			AND D006_Flag_Venda_Compra_Outros='DV'
EOT;
			$mSqlSTDV = mysql_fetch_array(mysql_query($sqlSTDV));

		// Valor da ST de NF Recebida 
		$stNFNFEntrada = mysql_query("SELECT SUM(T014_Valor_ICMS_Substituicao) AS ValorICMSSTEntrada
										FROM T014
								   LEFT JOIN T013 ON T013_Id = T014_T013_Id
								   LEFT JOIN D006 ON D006_Id = T014_D006_Id
								   LEFT JOIN D024 ON D024_Id = T013_D024_Id 
								   LEFT JOIN D018 ON D018_Id = D024_D018_Id 
									   WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
									     AND {$dataNFRecebida} <= '{$dataFinalHora}'
								 	     AND T013_C004_Id = '{$g['empresaAtual']}'  
								 	     AND T013_Flag_Cancelada != 'S' 
								 	     AND D018_UF='{$T008ST['D018_UF']}' 
								 	     AND T013_Numero_Protocolo_Nfe > 0 
								 	     AND T013_Tipo = '1' AND T013_Flag_Estoque_Finalizado = 'S'
								 	     AND (D006_Codigo_CFOP LIKE '1%' OR D006_Codigo_CFOP LIKE '2%')");

			$mStNFNFEntrada = mysql_fetch_array($stNFNFEntrada);

			$COD_OR = ($T008['D006_Flag_Estado'] == 'D') ? '002' : '999';
			$DT_VCTO = date($dataInicial, strtotime("+1 month", strtotime($dataInicial)));
			$DT_VCTO = formataData($DT_VCTO);
			$MES_REF = $mes.$ano;
			$VL_OR = $T008['ValorICMSST'] - $mSqlSTDV['ValorICMSST'] - $mStNFNFEntrada['ValorICMSSTEntrada'];
			$VL_OR = ($VL_OR<=0) ? 0 : $VL_OR;
			
			$E250 = array(
				$REG = 'E250',
				$COD_OR,												# Código da obrigação a recolher, conforme a Tabela 5.4
				formataDecimal($VL_OR),									# Valor da obrigação ICMS ST a recolher
				$DT_VCTO,     											# Data de vencimento da obrigação
				$COD_REC,     											# Código de receita referente à obrigação, próprio da unidade da federação do contribuinte substituído.
				$NUM_PROC = '',											# Número do processo ou auto de infração ao qual a obrigação está vinculada, se houver
				$IND_PROC = '',											# Indicador da origem do processo: (0- SEFAZ / 1- Justiça Federal / 2- Justiça Estadual / 9- Outros)
				$PROC = '',												# Descrição resumida do processo que embasou o lançamento
				$TXT_COMPL = '',   										# Descrição complementar das obrigações a recolher
				$MES_REF,      											# Informe o mês de referência no formato “mmaaaa”
			); 
			$E250 = implode("|", $E250);
			$layout .= '|' . $E250 . '|' . _SALTO_LINHA;
			$contador_bloco_E++;
		}
	}

	# REGISTRO E300
	$T007Partilha = mysql_query("SELECT D018_UF, 
										SUM(CASE WHEN T007_Flag_Entrada_Saida = 'S' THEN T007_Valor_Total_FCP_Partilha ELSE 0 END) AS valorFCPPartilhaDestino,
										SUM(CASE WHEN T007_Flag_Entrada_Saida = 'S' THEN T007_Valor_Total_Partilha_ICMS_Destino ELSE 0 END) AS valorPartilhaDestino,
										SUM(CASE WHEN T007_Flag_Entrada_Saida = 'E' THEN T007_Valor_Total_FCP_Partilha ELSE 0 END) AS valorFCPPartilhaDestinoDev,
										SUM(CASE WHEN T007_Flag_Entrada_Saida = 'E' THEN T007_Valor_Total_Partilha_ICMS_Destino ELSE 0 END) AS valorPartilhaDestinoDev
							       FROM T007 
						      LEFT JOIN D006 ON D006_Id = T007_D006_Id
						      LEFT JOIN D024 ON D024_Id = T007_D024_Id
						      LEFT JOIN D018 ON D018_Id = D024_D018_Id
							      WHERE (T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}')
							        AND T007_C004_Id = '{$g['empresaAtual']}' 
							        AND T007_Flag_Cancelada != 'S'
							        AND T007_Numero_Protocolo_Nfe > 0
							        AND T007_Flag_ACP = '6'
									AND D018_UF != 'EX'
							   GROUP BY D018_UF");

	while($mT007Partilha = mysql_fetch_array($T007Partilha)){

		$E300 = array(											#TAM								
			$REG = 'E300',										#004* 
			$UF = $mT007Partilha['D018_UF'], 					#002*	Sigla da unidade da federação a que se refere a apuração do ICMS ST
			$DT_INI = formataData($dataInicial),				#008*	Data inicial a que a apuração se refere
			$DT_FIN = formataData($dataFinal),					#008*	Data final a que a apuração se refere
		); 

		$E300 = implode("|", $E300);
		$layout .= '|' . $E300 . '|' . _SALTO_LINHA;
		$contador_bloco_E++;

			$ajustesDifalFcpUF = isset($ajustesDetalhadosBlocoE['difal_fcp'][$mT007Partilha['D018_UF']]) ? $ajustesDetalhadosBlocoE['difal_fcp'][$mT007Partilha['D018_UF']] : array(
				'difal' => array('totais' => array(), 'registros' => array()),
				'fcp' => array('totais' => array(), 'registros' => array()),
			);

			$IND_MOV_FCP_DIFAL = ($mT007Partilha['valorFCPPartilhaDestino'] > 0
				|| $mT007Partilha['valorPartilhaDestino'] > 0
				|| !empty($ajustesDifalFcpUF['difal']['registros'])
				|| !empty($ajustesDifalFcpUF['fcp']['registros'])) ? '1' : '0';

		// ICMS PARTILHA
		$VL_SLD_CRED_ANT_DIFAL         = '0';
		$VL_TOT_DEBITOS_DIFAL          = $mT007Partilha['valorPartilhaDestino'];
			$VL_OUT_DEB_DIFAL 	           = isset($ajustesDifalFcpUF['difal']['totais']['VL_OUT_DEB_DIFAL']) ? $ajustesDifalFcpUF['difal']['totais']['VL_OUT_DEB_DIFAL'] : '0';
			$VL_TOT_CREDITOS_DIFAL 	       = $mT007Partilha['valorPartilhaDestinoDev'];
			$VL_OUT_CRED_DIFAL 	           = isset($ajustesDifalFcpUF['difal']['totais']['VL_OUT_CRED_DIFAL']) ? $ajustesDifalFcpUF['difal']['totais']['VL_OUT_CRED_DIFAL'] : '0';

		$VL_SLD_DEV_ANT_DIFAL 		   = ($VL_TOT_DEBITOS_DIFAL + $VL_OUT_DEB_DIFAL) - ($VL_SLD_CRED_ANT_DIFAL + $VL_TOT_CREDITOS_DIFAL + $VL_OUT_CRED_DIFAL);
		if($VL_SLD_DEV_ANT_DIFAL < 0){
			$VL_SLD_DEV_ANT_DIFAL 	   = '0';
		}

			$VL_DEDUCOES_DIFAL 			   = isset($ajustesDifalFcpUF['difal']['totais']['VL_DEDUCOES_DIFAL']) ? $ajustesDifalFcpUF['difal']['totais']['VL_DEDUCOES_DIFAL'] : '0'; 

		$VL_RECOL_DIFAL				   = ($VL_SLD_DEV_ANT_DIFAL - $VL_DEDUCOES_DIFAL);
		if($VL_RECOL_DIFAL < 0){
			$VL_RECOL_DIFAL			   = 0;
		}

			$VL_SLD_CRED_TRANSPORTAR_DIFAL = ($VL_SLD_CRED_ANT_DIFAL + $VL_TOT_CREDITOS_DIFAL + $VL_OUT_CRED_DIFAL + $VL_DEDUCOES_DIFAL) - ($VL_TOT_DEBITOS_DIFAL+ $VL_OUT_DEB_DIFAL);
		if($VL_SLD_CRED_TRANSPORTAR_DIFAL < 0){
			$VL_SLD_CRED_TRANSPORTAR_DIFAL = 0;
		}
		
			$DEB_ESP_DIFAL                 = isset($ajustesDifalFcpUF['difal']['totais']['DEB_ESP_DIFAL']) ? $ajustesDifalFcpUF['difal']['totais']['DEB_ESP_DIFAL'] : '0';
		// FIN ICMS PARTILHA

		// FCP PARTILHA
		$VL_SLD_CRED_ANT_FCP           = '0';
		$VL_TOT_DEB_FCP                = $mT007Partilha['valorFCPPartilhaDestino'];
			$VL_OUT_DEB_FCP                = isset($ajustesDifalFcpUF['fcp']['totais']['VL_OUT_DEB_FCP']) ? $ajustesDifalFcpUF['fcp']['totais']['VL_OUT_DEB_FCP'] : '0';
			$VL_TOT_CRED_FCP               = $mT007Partilha['valorFCPPartilhaDestinoDev'];
			$VL_OUT_CRED_FCP               = isset($ajustesDifalFcpUF['fcp']['totais']['VL_OUT_CRED_FCP']) ? $ajustesDifalFcpUF['fcp']['totais']['VL_OUT_CRED_FCP'] : '0';

		$VL_SLD_DEV_ANT_FCP = ($VL_TOT_DEB_FCP + $VL_OUT_DEB_FCP) - ($VL_SLD_CRED_ANT_FCP + $VL_TOT_CRED_FCP + $VL_OUT_CRED_FCP);
		if($VL_SLD_DEV_ANT_FCP < 0){
			$VL_SLD_DEV_ANT_FCP = 0;
		}

			$VL_DEDUCOES_FCP               = isset($ajustesDifalFcpUF['fcp']['totais']['VL_DEDUCOES_FCP']) ? $ajustesDifalFcpUF['fcp']['totais']['VL_DEDUCOES_FCP'] : '0';

		$VL_RECOL_FCP          		   = ($VL_SLD_DEV_ANT_FCP - $VL_DEDUCOES_FCP);
		if($VL_RECOL_FCP < 0 ){
			$VL_RECOL_FCP = 0;
		}

		$VL_SLD_CRED_TRANSPORTAR_FCP   = ($VL_SLD_CRED_ANT_FCP + $VL_TOT_CRED_FCP + $VL_OUT_CRED_FCP + $VL_DEDUCOES_FCP) - ($VL_TOT_DEB_FCP + $VL_OUT_DEB_FCP); 
		if($VL_SLD_CRED_TRANSPORTAR_FCP < 0 ){
			$VL_SLD_CRED_TRANSPORTAR_FCP = 0;
		}

			$DEB_ESP_FCP 				   = isset($ajustesDifalFcpUF['fcp']['totais']['DEB_ESP_FCP']) ? $ajustesDifalFcpUF['fcp']['totais']['DEB_ESP_FCP'] : '0';

		$E310 = array(											#TAM								
			$REG = 'E310',										#004* 
			$IND_MOV_FCP_DIFAL,									# Indicador de movimento - 1 Com Movimento | 0 - Sem Movimento
			formataDecimal($VL_SLD_CRED_ANT_DIFAL),				# Valor do "Saldo credor de período anterior – ICMS Diferencial de Alíquota da UF de Origem/Destino"
			formataDecimal($VL_TOT_DEBITOS_DIFAL),			    # Valor total dos débitos por "Saídas e prestações com débito do ICMS referente ao diferencial de alíquota devido à UF de Origem/Destino"
			formataDecimal($VL_OUT_DEB_DIFAL),					# Valor total dos ajustes "Outros débitos ICMS Diferencial de Alíquota da UF de Origem/Destino" e “Estorno de créditos ICMS Diferencial de Alíquota da UF de Origem/Destino”
			formataDecimal($VL_TOT_CREDITOS_DIFAL),				# Valor total dos créditos do ICMS referente ao diferencial de alíquota devido à UF de Origem/Destino
			formataDecimal($VL_OUT_CRED_DIFAL), 				# Valor total de Ajustes "Outros créditos ICMS Diferencial de Alíquota da UF de Origem/Destino" e “Estorno de débitos ICMS Diferencial de Alíquota da UF de Origem/Destino”
			formataDecimal($VL_SLD_DEV_ANT_DIFAL),				# Valor total de “Saldo devedor ICMS Diferencial de Alíquota da UF de Origem/Destino antes das deduções”
			formataDecimal($VL_DEDUCOES_DIFAL),					# Valor total dos ajustes "Deduções ICMS Diferencial de Alíquota da UF de Origem/Destino"			
			formataDecimal($VL_RECOL_DIFAL),					# Valor recolhido ou a recolher referente ao ICMS Diferencial de Alíquota da UF de Origem/Destino (08-09)
			formataDecimal($VL_SLD_CRED_TRANSPORTAR_DIFAL),	    # Saldo credor a transportar para o período seguinte referente ao ICMS Diferencial de Alíquota da UF de Origem/Destino
			formataDecimal($DEB_ESP_DIFAL),					    # Valores recolhidos ou a recolher, extra-apuração - ICMS Diferencial de Alíquota da UF de Origem/Destino
			formataDecimal($VL_SLD_CRED_ANT_FCP),			    # Valor do "Saldo credor de período anterior – FCP"
			formataDecimal($VL_TOT_DEB_FCP),					# Valor total dos débitos FCP por "Saídas e prestações”
			formataDecimal($VL_OUT_DEB_FCP),					# Valor total dos ajustes "Outros débitos FCP" e “Estorno de créditos FCP”
			formataDecimal($VL_TOT_CRED_FCP),					# Valores recolhidos ou a recolher, extraapuração.
			formataDecimal($VL_OUT_CRED_FCP),					# Valor total de Ajustes "Outros créditos FCP" e “Estorno de débitos FCP”			
			formataDecimal($VL_SLD_DEV_ANT_FCP),				# Valor total de Saldo devedor FCP antes das deduções
			formataDecimal($VL_DEDUCOES_FCP),					# Valor total das deduções "FCP"			
			formataDecimal($VL_RECOL_FCP),						# Valor recolhido ou a recolher referente ao FCP (18 - 19)
			formataDecimal($VL_SLD_CRED_TRANSPORTAR_FCP),	    # Saldo credor a transportar para o período seguinte referente ao FCP
			formataDecimal($DEB_ESP_FCP)						# Valores recolhidos ou a recolher, extra-apuração - FCP.
		); 

			$E310 = implode("|", $E310);
			$layout .= '|' . $E310 . '|' . _SALTO_LINHA;
			$contador_bloco_E++;

			if ($gerarBlocoEDetalhado == 'S') {
				if (!empty($ajustesDifalFcpUF['difal']['registros'])) {
					foreach ($ajustesDifalFcpUF['difal']['registros'] as $codigoAjuste => $ajusteDifal) {
						$E311 = array(
							'E311',
							$codigoAjuste,
							$ajusteDifal['descricao'],
							formataDecimal($ajusteDifal['valor'])
						);
						$layout .= '|' . implode('|', $E311) . '|' . _SALTO_LINHA;
						$contador_bloco_E++;
						spedFiscalRenderizarFilhosAjusteBlocoE($layout, $contador_bloco_E, 'E311', $ajusteDifal['filhos']);
					}
				}
				if (!empty($ajustesDifalFcpUF['fcp']['registros'])) {
					foreach ($ajustesDifalFcpUF['fcp']['registros'] as $codigoAjuste => $ajusteFcp) {
						$E311 = array(
							'E311',
							$codigoAjuste,
							$ajusteFcp['descricao'],
							formataDecimal($ajusteFcp['valor'])
						);
						$layout .= '|' . implode('|', $E311) . '|' . _SALTO_LINHA;
						$contador_bloco_E++;
						spedFiscalRenderizarFilhosAjusteBlocoE($layout, $contador_bloco_E, 'E311', $ajusteFcp['filhos']);
					}
				}
			}

		// REGISTRO E316
		$COD_OR = '000';
		$VL_OR  = $VL_RECOL_DIFAL + $DEB_ESP_DIFAL + $VL_RECOL_FCP + $DEB_ESP_FCP;
		$DT_VCTO = date($dataInicial, strtotime("+1 month", strtotime($dataInicial)));
		$DT_VCTO = formataData($DT_VCTO);
		$NUM_PROC = '';
		$IND_PROC = '';
		$PROC = '';
	    $TXT_COMPL = '';
		$MES_REF = $mes.$ano;

		$E316 = array(											#TAM								
			$REG = 'E316',										#004* 
			$COD_OR,											# Código da obrigação recolhida ou a recolher, conforme a Tabela 5.4
			formataDecimal($VL_OR),								# Valor da obrigação recolhida ou a recolher
			$DT_VCTO,											# Data de vencimento da obrigação
			$COD_REC = trim(empty($g['C031']['CodigoReceitaSPED']) ? '100099' : $g['C031']['CodigoReceitaSPED']),	# Código de receita referente à obrigação, próprio da unidade da federação da origem/destino, conforme legislação estadual.
			$NUM_PROC,											# Número do processo ou auto de infração ao qual a obrigação está vinculada, se houver
			$IND_PROC,											# Indicador da origem do processo: 1- Justiça Federal; 2- Justiça Estadual; 9- Outros
			$PROC,												# Descrição resumida do processo que embasou o lançamento
			$TXT_COMPL,											# Descrição complementar das obrigações recolhidas ou a recolher
			$MES_REF											# Informe o mês de referência no formato “mmaaaa

		); 

		$E316 = implode("|", $E316);
		$layout .= '|' . $E316 . '|' . _SALTO_LINHA;
		$contador_bloco_E++;
	}

	// Verifica se o tipo de atividade é industria para assim preencher o registro E500.
	if($IND_ATIV == 0){
		# REGISTRO E500
		$E500 = array(												#TAM								
			$REG = 'E500',											#004*
			$IND_APUR = '0',										#001* 	Indicador de período de apuração do IPI 								
			$DT_INI = formataData($dataInicial),					#008*	Data inicial a que a apuração se refere
			$DT_FIN = formataData($dataFinal),						#008*	Data final a que a apuração se refere
		); 
		$E500 = implode("|", $E500);
		$layout .= '|' . $E500 . '|' . _SALTO_LINHA;
		$contador_bloco_E++;
	
		# REGISTRO E510
/* 		$sqlT007 = mysql_query("SELECT T007_Id,
									   T008_Situacao_Tributaria_IPI,
									   SUM(T008_Valor_Base_IPI) AS BaseIPI, 
								       SUM(T008_Valor_IPI) AS TotalIPI, 
								       SUM(T008_Valor_Total_Preco) AS ValorContabil,
								       D006_Codigo_CFOP 
								  FROM T008
							 LEFT JOIN T007 ON T007_Id = T008_T007_Id
							 LEFT JOIN D006 ON D006_Id = T008_D006_Id
								 WHERE T007_Data_Emissao >= '{$dataInicial}' 
								   AND T007_Data_Emissao <= '{$dataFinal}' 
								   AND T007_C004_Id = '{$g['empresaAtual']}' 
								   AND T007_Flag_Cancelada != 'S' 
								   AND (D006_Codigo_CFOP LIKE '5.%' OR D006_Codigo_CFOP LIKE '6.%' OR D006_Codigo_CFOP LIKE '6.%' OR D006_Codigo_CFOP='1.605')
								   AND T007_Numero_Protocolo_Nfe > 0
							  GROUP BY D006_Codigo_CFOP, T008_Situacao_Tributaria_IPI"); */
							  
		$sqlT007 = mysql_query("SELECT T007_Id,
									   T008_Situacao_Tributaria_IPI,
							           SUM(BaseIPI) AS BaseIPI, 
							           SUM(TotalIPI) AS TotalIPI, 
							           SUM(ValorContabil) AS ValorContabil,
							           D006_Codigo_CFOP 
					             FROM (									
								        SELECT T007_Id,
											   COALESCE(LPAD(T008_Situacao_Tributaria_IPI, 2, '0'), '') AS T008_Situacao_Tributaria_IPI,
										       SUM(T008_Valor_Base_IPI) AS BaseIPI, 
										       (SUM(T008_Valor_IPI) + SUM(T008_Valor_IPI_Devolucao)) AS TotalIPI, 
										       SUM(T008_Valor_Total_Preco) AS ValorContabil,
											   replace(replace(replace(D006_Codigo_CFOP,'.',''),',',''),'-','') AS D006_Codigo_CFOP
								          FROM T008
							         LEFT JOIN T007 ON T007_Id = T008_T007_Id
							         LEFT JOIN D006 ON D006_Id = T008_D006_Id
								         WHERE T007_Data_Emissao >= '{$dataInicial}' 
								  		   AND T007_Data_Emissao <= '{$dataFinal}' 
								  		   AND T007_C004_Id = '{$g['empresaAtual']}' 
								  		   AND T007_Flag_Cancelada != 'S' 
								  		   AND (D006_Codigo_CFOP LIKE '5%' OR D006_Codigo_CFOP LIKE '6%' OR D006_Codigo_CFOP LIKE '7%' OR (D006_Codigo_CFOP='1.605' OR D006_Codigo_CFOP='1605'))
								  		   AND T007_Numero_Protocolo_Nfe > 0
							 		  GROUP BY D006_Codigo_CFOP, T008_Situacao_Tributaria_IPI
								         UNION
							             SELECT T007_Id,
										 		COALESCE(LPAD(T008_Situacao_Tributaria_IPI, 2, '0'), '') AS T008_Situacao_Tributaria_IPI,
										        SUM(IF(produto.D006_Flag_Credita_IPI != 'S',0,T008_Valor_Base_IPI)) AS BaseIPI, 
										        (SUM(IF(produto.D006_Flag_Credita_IPI != 'S',0,T008_Valor_IPI)) + SUM(IF(produto.D006_Flag_Credita_IPI != 'S',0,T008_Valor_IPI_Devolucao))) AS TotalIPI, 
										        SUM(T008_Valor_Total_Preco) AS ValorContabil,
										        replace(replace(replace(produto.D006_Codigo_CFOP,'.',''),',',''),'-','') AS D006_Codigo_CFOP
								           FROM T008
							          LEFT JOIN T007 ON T007_Id = T008_T007_Id
									  LEFT JOIN D006 AS produto ON produto.D006_Id = T008_D006_Id
									  LEFT JOIN D006 AS cabecalho ON cabecalho.D006_Id = T007_D006_Id
										  WHERE T007_Data_Emissao >= '{$dataInicial}' 
								  			AND T007_Data_Emissao <= '{$dataFinal}' 
								  			AND T007_C004_Id = '{$g['empresaAtual']}' 
								  			AND T007_Flag_Cancelada != 'S' 
								  			AND (produto.D006_Codigo_CFOP LIKE '1%' OR produto.D006_Codigo_CFOP LIKE '2%' OR produto.D006_Codigo_CFOP LIKE '3%' OR (produto.D006_Codigo_CFOP='1.605' OR produto.D006_Codigo_CFOP='1605'))
								  			AND T007_Numero_Protocolo_Nfe > 0
							 		   GROUP BY produto.D006_Codigo_CFOP, T008_Situacao_Tributaria_IPI
										  UNION
							   			 SELECT T013_Id AS T007_Id,
											    COALESCE(LPAD(T014_Situacao_Tributaria_IPI, 2, '0'), '') AS T008_Situacao_Tributaria_IPI,
									            SUM(IF(produto.D006_Flag_Credita_IPI != 'S',0,T014_Valor_Base_IPI)) AS BaseIPI, 
									            SUM(IF(produto.D006_Flag_Credita_IPI != 'S',0,T014_Valor_IPI)) AS TotalIPI, 
									            SUM(T014_Valor_Total_Custo) AS ValorContabil,
									            replace(replace(replace(produto.D006_Codigo_CFOP,'.',''),',',''),'-','') AS D006_Codigo_CFOP
								           FROM T014
							          LEFT JOIN T013 ON T013_Id = T014_T013_Id
							          LEFT JOIN D006 AS produto ON produto.D006_Id = T014_D006_Id
									  LEFT JOIN D006 AS cabecalho ON cabecalho.D006_Id = T013_D006_Id
								          WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
								            AND {$dataNFRecebida} <= '{$dataFinalHora}'
								            AND T013_C004_Id = '{$g['empresaAtual']}'  
								            AND T013_Flag_Cancelada != 'S' 
								            AND T013_Flag_Estoque_Finalizado = 'S'
								            AND ((T013_Tipo = '1' AND T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo = '5') 
								            AND (produto.D006_Codigo_CFOP LIKE '1%' OR produto.D006_Codigo_CFOP LIKE '2%' OR produto.D006_Codigo_CFOP LIKE '3%' OR (produto.D006_Codigo_CFOP='1.605' OR produto.D006_Codigo_CFOP='1605'))
							 		   GROUP BY produto.D006_Codigo_CFOP, T008_Situacao_Tributaria_IPI
								) AS documentos
								GROUP BY D006_Codigo_CFOP, T008_Situacao_Tributaria_IPI");
		

		while($T007 = mysql_fetch_array($sqlT007)){
			$VL_CONT_IPI = $T007['ValorContabil'];
			$VL_BC_IPI = $T007['BaseIPI'];
			$VL_IPI = $T007['TotalIPI'];
			$E510 = array(
				$REG = 'E510',
				$CFOP = removeChars($T007['D006_Codigo_CFOP']),			#Código Fiscal de Operação e Prestação do agrupamento de itens
				$CST_IPI = $T007['T008_Situacao_Tributaria_IPI'],		#Código da Situação Tributária referente ao IPI, conforme a Tabela indicada no item 4.3.2.
				formataDecimal($VL_CONT_IPI),							#Parcela correspondente ao "Valor Contábil" referente ao CFOP e ao Código de Tributação do IPI
				formataDecimal($VL_BC_IPI),								#Parcela correspondente ao "Valor da base de cálculo do IPI" referente ao CFOP e ao Código de Tributação do IPI, para operações tributadas
				formataDecimal($VL_IPI),								#Parcela correspondente ao "Valor do IPI" referente ao CFOP e ao Código de Tributação do IPI, para operações tributadas
			);
			$E510 = implode("|", $E510);
			$layout .= '|' . $E510 . '|' . _SALTO_LINHA;
			$contador_bloco_E++;
		}


		$sqlT008 = mysql_query("SELECT SUM(T008_Valor_IPI) AS TotalIPI 
								  FROM T008
							 LEFT JOIN T007 ON T007_Id = T008_T007_Id
							 LEFT JOIN D006 ON D006_Id = T008_D006_Id
								 WHERE T007_Data_Emissao >= '{$dataInicial}' 
								   AND T007_Data_Emissao <= '{$dataFinal}' 
								   AND T007_C004_Id = '{$g['empresaAtual']}' 
								   AND T007_Flag_Cancelada != 'S' 
								   AND T007_Numero_Protocolo_Nfe > 0 
								   AND (D006_Codigo_CFOP LIKE '5%' OR D006_Codigo_CFOP LIKE '6%' OR D006_Codigo_CFOP LIKE '7%' OR (D006_Codigo_CFOP='1.605' OR D006_Codigo_CFOP='1605'));"); 

		$T008 = mysql_fetch_array($sqlT008);

		$VL_DEB_IPI = ($T008['TotalIPI'] > 0) ? $T008['TotalIPI'] : '0';
		$VL_SD_IPI  = ($T008['TotalIPI'] > 0) ? $T008['TotalIPI'] : '0';

/* 		$sqlT008 = mysql_query("SELECT SUM(T008_Valor_IPI) AS TotalIPI 
								  FROM T008
							 LEFT JOIN T007 ON T007_Id = T008_T007_Id
							 LEFT JOIN D006 ON D006_Id = T008_D006_Id
								 WHERE T007_Data_Emissao >= '{$dataInicial}' 
								   AND T007_Data_Emissao <= '{$dataFinal}' 
								   AND T007_C004_Id = '{$g['empresaAtual']}' 
								   AND T007_Flag_Cancelada != 'S' 
								   AND T007_Numero_Protocolo_Nfe > 0 
								   AND (D006_Codigo_CFOP LIKE '1.%' OR D006_Codigo_CFOP LIKE '2.%' OR D006_Codigo_CFOP LIKE '3.%' OR D006_Codigo_CFOP='5.605')"); */

		// Essa query foi tratada com IF porque o SPED valida o total desse registro com a soma dos registros C190
		// E no C190 é tratado com a CFOP do cabeçalho para saber se credita ou não o IPI para destacar no SPED						   	
		$sqlT008 = mysql_query("SELECT SUM(TotalIPI) as TotalIPI
								  FROM (
									SELECT (SUM(IF(produto.D006_Flag_Credita_IPI != 'S', 0, T008_Valor_IPI)) + SUM(IF(produto.D006_Flag_Credita_IPI != 'S', 0 ,T008_Valor_IPI_Devolucao)))  AS TotalIPI 
										  FROM T008
									 LEFT JOIN T007 ON T007_Id = T008_T007_Id
									 LEFT JOIN D006 AS produto ON produto.D006_Id = T008_D006_Id
									 LEFT JOIN D006 AS cabecalho ON cabecalho.D006_Id = T007_D006_Id
										 WHERE T007_Data_Emissao >= '{$dataInicial}' 
										   AND T007_Data_Emissao <= '{$dataFinal}' 
										   AND T007_C004_Id = '{$g['empresaAtual']}' 
										   AND T007_Flag_Cancelada != 'S' 
										   AND T007_Numero_Protocolo_Nfe > 0 
										   AND T007_Flag_ACP IN('6', '8') 
										   AND (produto.D006_Codigo_CFOP LIKE '1%' OR produto.D006_Codigo_CFOP LIKE '2%' OR produto.D006_Codigo_CFOP LIKE '3%' OR (produto.D006_Codigo_CFOP='5.605' OR produto.D006_Codigo_CFOP='5605'))
									     UNION
										 SELECT SUM(IF(produto.D006_Flag_Credita_IPI != 'S',0, T014_Valor_IPI)) as TotalIPI 
					                      FROM T014
			                         LEFT JOIN T013 ON T013_Id = T014_T013_Id
			                         LEFT JOIN D006 as produto ON produto.D006_Id = T014_D006_Id
									 LEFT JOIN D006 as cabecalho ON cabecalho.D006_Id = T013_D006_Id
				  					     WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
					                       AND {$dataNFRecebida} <= '{$dataFinalHora}'
					                       AND T013_C004_Id = '{$g['empresaAtual']}'  
					                       AND T013_Flag_Cancelada != 'S' 
										   AND T013_Flag_Estoque_Finalizado = 'S'
					                       AND ((T013_Tipo = '1' AND T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo = '5') 
					                       AND (produto.D006_Codigo_CFOP LIKE '1%' OR produto.D006_Codigo_CFOP LIKE '2%' OR produto.D006_Codigo_CFOP LIKE '3%' OR (produto.D006_Codigo_CFOP='5.605' OR produto.D006_Codigo_CFOP='5605'))
								       ) AS valorTotalIPI");

		$T008 = mysql_fetch_array($sqlT008);
		$VL_CRED_IPI = ($T008['TotalIPI'] > 0) ? $T008['TotalIPI'] : '0';
		$VL_SC_IPI = ($T008['TotalIPI'] > 0) ? $T008['TotalIPI'] : '0';

		$VL_SC_IPI_ = ($VL_SC_IPI - $VL_SD_IPI);
		$VL_SD_IPI_ = ($VL_SD_IPI - $VL_SC_IPI);
		$VL_SC_IPI_ = ($VL_SC_IPI_<=0) ? 0 : $VL_SC_IPI_;
		$VL_SD_IPI_ = ($VL_SD_IPI_<=0) ? 0 : $VL_SD_IPI_;
	
		# REGISTRO E520
		$E520 = array(												#TAM								
			$REG = 'E520',											#004* 
			$VL_SD_ANT_IPI = '0',									# Saldo credor do IPI transferido do período anterior
			formataDecimal($VL_DEB_IPI),							# Valor total dos débitos por "Saídas com débito do imposto"
			formataDecimal($VL_CRED_IPI),							# Valor total dos créditos por "Entradas e aquisições com crédito do imposto"
			$VL_OD_IPI = '0',										# Valor de "Outros débitos" do IPI (inclusive estornos de crédito)
			$VL_OC_IPI = '0',										# Valor de "Outros créditos" do IPI (inclusive estornos de débitos)
			formataDecimal($VL_SC_IPI_),							# Valor do saldo credor do IPI a transportar para o período seguinte
			formataDecimal($VL_SD_IPI_)								# Valor do saldo devedor do IPI a recolher
		); 
		$E520 = implode("|", $E520);
		$layout .= '|' . $E520 . '|' . _SALTO_LINHA;
		$contador_bloco_E++;
	}
}

$linhas = substr_count($layout, CHR(13).CHR(10));
$linhas -= $linhas_bloco_C;

# REGISTRO E990
$E990 = array(																
	$REG = 'E990',
	$QTD_LIN_E = $contador_bloco_E+1
);
$E990 = implode("|", $E990);
$layout .= '|' . $E990 . '|' . _SALTO_LINHA;

//$layout .= '|G001|1|' . _SALTO_LINHA;
//$layout .= '|G990|2|' . _SALTO_LINHA;
//$layout .= '|H001|1|' . _SALTO_LINHA;
//$layout .= '|H990|2|' . _SALTO_LINHA;
	
$linhas_bloco_E = $QTD_LIN_E;
