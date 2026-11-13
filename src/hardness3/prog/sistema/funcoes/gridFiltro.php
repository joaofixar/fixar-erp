<?php
namespace hardness;
/**
* Atualiza o arquivo de filtros com base na busca
*/

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'filtrar':
		$callback = (isset($r_callback)) ? $r_callback : false;
		$resposta = array('code' => true, 'data' => array());
		
		$filtroUID = (isset($r_filtroUID)) ? $r_filtroUID : false;
		$filtro = (isset($r_filtro)) ? $r_filtro : '';
		$grid = (isset($r_grid)) ? $r_grid : '';
		if (empty($grid)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Por favor, contate o suporte técnico.';
		}
		
		// Gravar filtro no arquivo
		$file = $g['pathDadosUsuario'] . 'gridFiltro/' . $grid;
		if (!file_exists($file)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Arquivo para guardar o filtro não encontrado.<br />Verifique se foi executado: $g[\'ui\']->gridFiltroExtra($grid)';
		} else {
			$gridFiltroOriginal = unserialize(file_get_contents($file));
			if(isset($gridFiltroOriginal['campos'])){
				$gridFiltro = $gridFiltroOriginal;
				if ($filtroUID) {
					// Filtro avançado
					foreach ($gridFiltro['campos'] as $campo_ => &$filtro_) {
						//$campo_ = str_replace('.', '_', $campo_);
						unset($d1);
						unset($variavel);
						$count = $filtro_['count'];

						if($filtro_['count'] <= 1){
							$redutor = 0.1;
						} else if($filtro_['count'] <= 2) {
							$redutor = 0.5;
						} else {
							$redutor = 1;
						}

						!isset($_REQUEST["{$filtroUID}-".base64_encode($campo_)]) or ($variavel = $_REQUEST["{$filtroUID}-".base64_encode($campo_)]);
						//echo $campo_ . ' - ' . base64_encode($campo_) . ' - ' . $variavel . "<br />";
						!isset($_REQUEST["{$filtroUID}-".base64_encode($campo_)."-d1"]) or ($d1 = $_REQUEST["{$filtroUID}-".base64_encode($campo_)."-d1"]);
						!isset($_REQUEST["{$filtroUID}-".base64_encode($campo_)."-d2"]) or ($d2 = $_REQUEST["{$filtroUID}-".base64_encode($campo_)."-d2"]);
						if (isset($d1)) {
							$filtro_['busca'] = array($d1, $d2);
							if(!empty($d1)) {
								$count = $filtro_['count']+1;
							} else {
								$count = $filtro_['count']-$redutor;
							}
						} else if (isset($variavel)) {
							$filtro_['busca'] = trim($variavel);
							if(!empty($variavel)) {
								$count = $filtro_['count']+1;
							} else {
								$count = $filtro_['count']-$redutor;
							}
						} else {
							//unset($filtro_['busca']);
							$filtro_['busca'] = '';
						}
						$count = ($count > 15) ? 15 : (($count < 0) ? 0 : $count);
						$filtro_['count'] = $count;
						$gridFiltro['busca'] = '!--advanced--!';
						// remover modo antigo (para filtros antigos) (pode ser retirado no futuro) 07/03/2012
						unset($filtro_['colunaTitulo']);
						unset($filtro_['colunaOrdem']);
						unset($filtro_['colunaTipo']);
					}
					unset($filtro_);
				} else {
					// Filtro básico
					foreach ($gridFiltro['campos'] as $campo_ => &$filtro_) {
						unset($filtro_['busca']);
					}
					unset($filtro_);
					$gridFiltro['busca'] = array();
					$filtro_ = explode(' ', $filtro);
					foreach ($filtro_ as $filtro__) {
						$gridFiltro['busca'][] = $filtro__;
					}
				}
				// Grava dados
				$h = fopen($file, 'w');
				fwrite($h, serialize($gridFiltro));
				fclose($h);
			} else {
				foreach($gridFiltroOriginal as $idFiltro => $gridFiltro){
					if ($filtroUID) {
						// Filtro avançado
						$indexCampo = 0;
						foreach ($gridFiltro['campos'] as $campo_ => &$filtro_) {
							if($idFiltro != 0){
								$tmpArray = array_keys($gridFiltroOriginal[0]['campos']);
								$campo_ = $tmpArray[$indexCampo];
							}
							//$campo_ = str_replace('.', '_', $campo_);
							unset($d1);
							unset($variavel);
							$count = $filtro_['count'];
							if($filtro_['count'] <= 1){
								$redutor = 0.1;
							} else if($filtro_['count'] <= 2) {
								$redutor = 0.5;
							} else {
								$redutor = 1;
							}
							!isset($_REQUEST["{$filtroUID}-".base64_encode($campo_)]) or ($variavel = $_REQUEST["{$filtroUID}-".base64_encode($campo_)]);
							//echo $campo_ . ' - ' . base64_encode($campo_) . ' - ' . $variavel . "<br />";
							!isset($_REQUEST["{$filtroUID}-".base64_encode($campo_)."-d1"]) or ($d1 = $_REQUEST["{$filtroUID}-".base64_encode($campo_)."-d1"]);
							!isset($_REQUEST["{$filtroUID}-".base64_encode($campo_)."-d2"]) or ($d2 = $_REQUEST["{$filtroUID}-".base64_encode($campo_)."-d2"]);
							if (isset($d1)) {
								$filtro_['busca'] = array($d1, $d2);
								if(!empty($d1)) { 
									$count = $filtro_['count']+1;
								} else {
									$count = $filtro_['count']-$redutor;
								}
							} else if (isset($variavel)) {
								$filtro_['busca'] = trim($variavel);
								if(!empty($variavel)) {
									$count = $filtro_['count']+1;
								} else {
									$count = $filtro_['count']-$redutor;
								}
							} else {
								//unset($filtro_['busca']);
								$filtro_['busca'] = '';
							}
							$count = ($count > 15) ? 15 : (($count < 0) ? 0 : $count);
							$filtro_['count'] = $count;
							$gridFiltro['busca'] = '!--advanced--!';
							// remover modo antigo (para filtros antigos) (pode ser retirado no futuro) 07/03/2012
							unset($filtro_['colunaTitulo']);
							unset($filtro_['colunaOrdem']);
							unset($filtro_['colunaTipo']);
							$indexCampo++;
						}
						unset($filtro_);
					} else {
						// Filtro básico
						foreach ($gridFiltro['campos'] as $campo_ => &$filtro_) {
							unset($filtro_['busca']);
						}
						unset($filtro_);
						$gridFiltro['busca'] = array();
						$filtro_ = explode(' ', $filtro);
						foreach ($filtro_ as $filtro__) {
							$gridFiltro['busca'][] = $filtro__;
						}
					}
					// Grava dados
					$h = fopen($file, 'w');
					$gridFiltroOriginal[$idFiltro] = $gridFiltro;
					$gridFiltro = $gridFiltroOriginal;
					fwrite($h, serialize($gridFiltro));
					fclose($h);
				}
			}
		}

		// Histórico dos filtros
		if ($r_historicoFiltros == 'true') {
			$histFiltro = '';
			$histFiltroData = '';
			foreach ($gridFiltro['campos'] as $key => $value) {
				if (!empty($value['busca'])) {
					$valor = $value['busca'];
					if (is_array($valor)) {
						if (empty(implode('', $valor))) continue;
						$valor = implode(',', $valor);
					}
					$histFiltro .= $histFiltro ? ';;; ' : '';
					$histFiltro .= $_REQUEST["{$filtroUID}-".base64_encode($key)."-titulo"] . ': ' . $valor;
					$histFiltroData .= $histFiltroData ? '||||' : '';
					$histFiltroData .= "{$key}|||{$valor}";
				}
			}
			if ($histFiltro) {
            $histHash = sha1($histFiltroData);
            $sql = <<<EOT
                SELECT C034_Id, C034_Count FROM C034 WHERE C034_Grid_Id = '{$grid}' AND C034_C007_Id = '{$g['C007']['C007_Id']}' AND C034_Hash = '{$histHash}';
EOT;
            $sqlReturn = mysql_query($sql);
            $resultado = mysql_fetch_assoc($sqlReturn);
            $histFiltro = mysql_real_escape_string($histFiltro);
            $histFiltroData = mysql_real_escape_string($histFiltroData);
            if (count($resultado) == 0) {
                $sql = <<<EOT
                INSERT INTO C034 (
                    C034_Grid_Id,
                    C034_C007_Id,
                    C034_Hash,
                    C034_Data,
                    C034_Filtro,
                    C034_Filtro_Data,
                    C034_Count
                ) VALUES (
                    '{$grid}',
                    '{$g['C007']['C007_Id']}',
                    '{$histHash}',
                    now(),
                    '{$histFiltro}',
                    '{$histFiltroData}',
                    1
                );
EOT;
                mysql_query($sql);
            } else {
                $sum = $resultado['C034_Count'] > 999 ? 0 : 1; // parar de contar em 1.000
                $sql = <<<EOT
                UPDATE C034 SET C034_Count = C034_Count + {$sum}, C034_Data = now() WHERE C034_Id = '{$resultado['C034_Id']}';
EOT;
                mysql_query($sql);
            }
			}
		}
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
	
	case 'ordenar':
		$callback = (isset($r_callback)) ? $r_callback : false;
		$resposta = array('code' => true, 'data' => array());
		
		$ordem = (isset($r_ordem)) ? base64_decode($r_ordem) : '';
		$grid = (isset($r_grid)) ? $r_grid : '';
		$ascdesc = isset($r_ascdesc) && ($r_ascdesc == 'ASC' || $r_ascdesc == 'DESC') ? $r_ascdesc : 'ASC';
		if (empty($grid)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Por favor, contate o suporte técnico.';
		}
		
		// Gravar filtro no arquivo
		$file = $g['pathDadosUsuario'] . 'gridFiltro/' . $grid;
		if (!file_exists($file)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Arquivo para guardar o filtro não encontrado.<br />Verifique se foi executado: $g[\'ui\']->gridFiltroExtra($grid)';
		} else {
			$gridFiltro = unserialize(file_get_contents($file));
			if ($r_ctrlKey) {
				if (empty($gridFiltro['ordem'])) {
					//$gridFiltro['ordem'] = "{$ordem} ASC";
					$gridFiltro['ordem'] = "{$ordem} {$ascdesc}";
				} else {
					preg_match('/' . preg_quote($ordem) . ' (ASC|DESC)/', $gridFiltro['ordem'], $matches);
					if (empty($matches)) {
						//$gridFiltro['ordem'] .= ", {$ordem} ASC";
						$gridFiltro['ordem'] .= ", {$ordem} {$ascdesc}";
					} else {
						if ($matches[1] == 'ASC') {
							//$gridFiltro['ordem'] = str_replace($matches[0], "{$ordem} DESC", $gridFiltro['ordem']);
							$gridFiltro['ordem'] = str_replace($matches[0], "{$ordem} {$ascdesc}", $gridFiltro['ordem']);
						} else {
							//$gridFiltro['ordem'] = str_replace($matches[0], "{$ordem} ASC", $gridFiltro['ordem']);
							$gridFiltro['ordem'] = str_replace($matches[0], "{$ordem} {$ascdesc}", $gridFiltro['ordem']);
						}
					}
				}
			} else {
				if (empty($gridFiltro['ordem']) || strpos($gridFiltro['ordem'], $ordem.' ASC') === false) {
					//$gridFiltro['ordem'] = "{$ordem} ASC";
					$gridFiltro['ordem'] = "{$ordem} {$ascdesc}";
				} else {
					//$gridFiltro['ordem'] = "{$ordem} DESC";
					$gridFiltro['ordem'] = "{$ordem} {$ascdesc}";
				}
			}
			// Grava dados
			$h = fopen($file, 'w');
			fwrite($h, serialize($gridFiltro));
			fclose($h);
		}
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'contentFiltroGrid':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/gridFiltro/tabFiltroGrid/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'tabFiltroGrid':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), $r_origemFiltroUrl, 'Filtrar'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
