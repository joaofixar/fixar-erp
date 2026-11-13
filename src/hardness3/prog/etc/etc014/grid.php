<?php
namespace hardness;

// (para fazer) para melhorar a performance, somente fazer joins de dimensões que estiverem sendo utilizadas
$joins = <<<EOT
	LEFT JOIN DIM001 					ON FAC001_DIM001_Id = DIM001_Id
	LEFT JOIN DIM002 					ON FAC001_DIM002_Id = DIM002_Id
	LEFT JOIN DIM003 					ON FAC001_DIM003_Id = DIM003_Id
	LEFT JOIN DIM004 D4Vendedor 		ON FAC001_DIM004_Id_Vendedor = D4Vendedor.DIM004_Id
	LEFT JOIN DIM004 D4Supervisor 		ON FAC001_DIM004_Id_Supervisor = D4Supervisor.DIM004_Id
	LEFT JOIN DIM005 					ON FAC001_DIM005_Id = DIM005_Id
	LEFT JOIN DIM006 					ON FAC001_DIM006_Id = DIM006_Id
	LEFT JOIN DIM007 					ON FAC001_DIM007_Id = DIM007_Id
	LEFT JOIN DIM008 					ON FAC001_DIM008_Id = DIM008_Id
	LEFT JOIN DIM009 					ON FAC001_DIM009_Id = DIM009_Id
	LEFT JOIN DIM010 					ON FAC001_DIM010_Id = DIM010_Id
	LEFT JOIN DIM011 					ON FAC001_DIM011_Id = DIM011_Id
EOT;

switch ($g['r'][3]) {
	case 'etc014GridPivot':
		// Vem do GRID (Relatório Salvo)
		$primeiraLinhaGridHead = '<tr>';
		$filtroSalvo = false;
		$md5 = md5($g['r_']);
		$modificado = false;
		if (isset($r_D132_Id) && !empty($r_D132_Id)) {
			$r_D132_Id = mysql_real_escape_string($r_D132_Id);
			$sql = "SELECT D132_Descricao, D132_Salvo, D132_Filtro FROM D132 WHERE D132_Id = '{$r_D132_Id}';";
			$resultado = mysql_query($sql);
			$row = mysql_fetch_array($resultado);
			$dadosSalvos = unserialize($row['D132_Salvo']);
			foreach ($dadosSalvos as $key => $value) {
				$var = 'r_' . $key;
				$$var = $value;
			}
			$D132_Descricao = $row['D132_Descricao'];
			$r_mostrarCurvaABC = isset($dadosSalvos['mostrarCurvaABC']) ? $dadosSalvos['mostrarCurvaABC'] : 'true';
			$r_ocultarColunasDashboard = isset($dadosSalvos['ocultarColunasDashboard']) ? $dadosSalvos['ocultarColunasDashboard'] : 'true';
			$r_ocultarSempreColunas = isset($dadosSalvos['ocultarSempreColunas']) ? $dadosSalvos['ocultarSempreColunas'] : 'false';
			$r_ocultarPorcentagem = isset($dadosSalvos['ocultarPorcentagem']) ? $dadosSalvos['ocultarPorcentagem'] : 'false';
			$r_exibir = isset($dadosSalvos['exibir']) ? $dadosSalvos['exibir'] : 'true';
			
			// Filtro
			if (isset($r_tab)) {
				// No 'Dashboard': utiliza o D132_Filtro somente se não existir um filtro para: este usuário, na tab X, no item Y
				//echo $g['r_'] . ' - ' . $r_tab . ' - ' .  $r_D132_Id . "<br />";
				$md5 = md5($g['r_'] . $r_tab . $r_D132_Id);
				$dir = $g['pathDadosUsuario'] . 'gridFiltro/';
				$file = $dir . $md5;
				if (!file_exists($file)) {
					file_put_contents($file, $row['D132_Filtro']);
				} else {
					$filtrado = file_get_contents($file);
					// (para fazer) verificar o array e não a string (pode ter campos no filtros novos não filtrados por exemplo)
					if ($row['D132_Filtro'] != $filtrado) {
						$filtrado = unserialize($filtrado);
						$modificado = true;
					}
				}
			} else {
				$r_tab = false;
				// Em 'Relatórios Salvos': utilizar sempre o D132_Filtro - se o usuário fizer um filtro, vale apenas para aquele momento
				if (!isset($r_Filtro_D132_Id)) {
					$filtroSalvo = unserialize($row['D132_Filtro']);
					$dir = $g['pathDadosUsuario'] . 'gridFiltro/';
					$file = $dir . $md5;
					if (file_exists($file)) { unlink($file); }
				}
			}
		} else {
			if ($r_bi == 'visu') {
				echo '<div style="padding: 13px 20px; font-size: 16px;">Selecione um relatório.</div>';
				die();
			}
		}
		$r_numMetricas = !isset($r_numMetricas) || empty($r_numMetricas) ? 1 : $r_numMetricas;
		
		// Modo de exibição padrão é 'grid'
		if (!isset($r_exibir) || $r_exibir == 'undefined' || empty($r_exibir)) { $r_exibir = 'grid'; }
		if (isset($r_grid) && !empty($r_grid)) { $r_exibir = 'grid'; }
		
		// Remove opções do grid (ordenação, etc)
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//$('#{$g['divId']} .grid').css('width', '1%');
				// Desabilita ordenação padrão do grid
				$('#{$g['divId']} th').removeAttr('onclick').css('cursor', 'default');
				$('#{$g['divId']} tr').css('cursor', 'auto');
				// Corrige as colunas COLSPAN="2"
				$('#{$g['divId']} th.extraTHGhost').remove();
			} </script></div>
EOT;
		
		// No modo 'visualização' não é permitido salvar
		if ($r_bi == 'visu') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#{$g['divId']} .gridFiltroBotoes:eq(0) input:eq(0)').click(function(data) {
						divSetarAcaoId('{$g['divId']}', '1', 'Filtro_D132_Id');
						return false;
					});
				} </script></div>
EOT;
		}
		
		// Definições das dimensões e fatos
		$dimensoes = array(
			//'Data', 
			'DDDMS' => array('DIM001_Data', 'Dia/Mes/Ano'),
			'DDA' => array('DIM001_Ano', 'Ano'),
			'DDM' => array('DIM001_Mes', 'Mes'),
			'DDDS' => array('DIM001_Dia_Da_Semana', 'Dia da Semana'),
			'DDD' => array('DIM001_Dia', 'Dia'),
			//'Clientes/Fornecedores',
			'DENE' => array('DIM002_Nome_Fantasia', 'Cliente/Fornecedor'),
			//'Local',
			'DLE' => array('DIM003_Estado', 'Estado'),
			'DLC' => array('DIM003_Cidade', 'Cidade'),
			'DLB' => array('DIM003_Bairro', 'Bairro'),
			//'DLEN' => array('DIM003_Endereco', 'Endereco'),
			//'Usuários',
			'DUV' => array('D4Vendedor.DIM004_Primeiro_Nome', 'Vendedor'),
			'DUS' => array('D4Supervisor.DIM004_Primeiro_Nome', 'Supervidor'),
			'DUR' => array('DIM011_Nome_Empresa', 'Representante'),
			//'Conta',
			'DIM007_Id' => array('DIM007_Conta', 'Conta'),
			//'Produtos',
			'DIM009_D001_Descricao_Produto' => array('DIM009_D001_Descricao_Produto', 'Produto'),
			'DIM009_D082_Marca' => array('DIM009_D082_Marca', 'Marca'),
			'DIM009_C008_Tipo' => array('DIM009_C008_Tipo', 'Tipo'),
			'DIM009_D003_Nome_Linha' => array('DIM009_D003_Nome_Linha', 'Linha'),
			//'CFOP',
			'DIM010_D006_Codigo_CFOP_e_Descricao' => array('DIM010_D006_Codigo_CFOP_e_Descricao', 'CFOP'),
		);
		$fatos2 = array(
			'ORCA' => array('ORCA', 'Orçamentos'),
			'PEDI' => array('PEDI', 'Pedidos'),
			'NOTAFISC' => array('NOTAFISC', 'Notas Fiscais Emitidas'),
			'CONTRECE' => array('CONTRECE', 'Contas a Receber'),
			'CONTPAGA' => array('CONTPAGA', 'Contas a Pagar'),
			'LANCBANC' => array('LANCBANC', 'Lançamentos Bancários')
		);
		$fatos = array(
			'FAC001_Orcamento_Valor_Total_Preco' => array('FAC001_Orcamento_Valor_Total_Preco', 'Valor Total'),
			'FAC001_Pedidos_Valor_Total_Preco' => array('FAC001_Pedidos_Valor_Total_Preco', 'Valor Total'),
			'FAC001_CR_Valor_Duplicata' => array('FAC001_CR_Valor_Duplicata', 'Valor Duplicata'),
			'FAC001_CP_Valor_Duplicata' => array('FAC001_CP_Valor_Duplicata', 'Valor Duplicata'),
			'FAC001_LB_Valor_Lancamento' => array('FAC001_LB_Valor_Lancamento', 'Valor Lancamento'),
			// NF
			'FAC001_NF_Valor_Total_Preco' => array('FAC001_NF_Valor_Total_Preco', 'Valor Total'),
			'FAC001_NF_Produtos_Quantidade_Itens' => array('FAC001_NF_Produtos_Quantidade_Itens', 'Qtd Itens'),
			'FAC001_NF_Produtos_Quantidade' => array('FAC001_NF_Produtos_Quantidade', 'Qtd Produtos'),
			//'FAC001_NF_ICMS' => array('FAC001_NF_ICMS', 'ICMS'),
			'FAC001_NF_Quantidade' => array('FAC001_NF_Quantidade', 'Qtd'),
		);
		$medidas = array(
			'MS' => array('SUM', 'Soma'),
			'MM' => array('AVG', 'Média')
			//'MC' => array('COUNT', 'Nr. de Registros')
		);
		$ordem = array(
			'ONA' => array('ASC', 'Nome ASC'),
			'OND' => array('DESC', 'Nome DESC'),
			'OTA' => array('ASC', 'Total ASC'),
			'OTD' => array('DESC', 'Total DESC'),
		);
		$tipodatas = array(
			//'Orçamentos',
			'T003_Data_Emissao' => array('DIM001', 'Data Emissao'),
			//'Pedidos',
			'T005_Data_Emissao' => array('DIM001', 'Data Emissao'),
			'T005_Data_Saida' => array('DIM005', 'Data Saida'),
			'T005_Data_Entrega' => array('DIM006', 'Data Entrega'),
			//'Notas Fiscais',
			'T007_Data_Emissao' => array('DIM001', 'Data Emissao'),
			'T007_Data_Saida' => array('DIM005', 'Data Saida'),
			'T007_Data_Vencimento' => array('DIM006', 'Data Vencimento'),
			//'Contas a Pagar',
			'T015_Data_Vencimento' => array('DIM001', 'Data Vencimento'),
			'T015_Data_Emissao' => array('DIM005', 'Data Emissao'),
			'T015_Data_Pagamento' => array('DIM006', 'Data Pagamento'),
			//'Contas a Receber',
			'T002_Data_Vencimento' => array('DIM001', 'Data Vencimento'),
			'T002_Data_Emissao' => array('DIM005', 'Data Emissao'),
			'T002_Data_Recebimento' => array('DIM006', 'Data Recebimento'),
			//'Lançamentos Bancários',
			'T019_Data_Lancamento' => array('DIM001', 'Data Lancamento'),
		);
		
		// Opções
		$incluirLinhaVazia_ = isset($r_incluirLinhaVazia) && $r_incluirLinhaVazia != 'false' ? ' checked="checked"' : '';
		$incluirColunaVazia_ = isset($r_incluirColunaVazia) && $r_incluirColunaVazia != 'false' ? ' checked="checked"' : '';
		$incluirGraficoLabel_ = !isset($r_incluirGraficoLabel) || $r_incluirGraficoLabel != 'false' ? ' checked="checked"' : '';
		$incluirGraficoLegend_ = !isset($r_incluirGraficoLegend) || $r_incluirGraficoLegend != 'false' ? ' checked="checked"' : '';
		if (isset($r_mostrarCurvaABC)) {
			$mostrarCurvaABC_ = $r_mostrarCurvaABC != 'false' ? ' checked="checked"' : '';
		} else {
			$mostrarCurvaABC_ = '';
		}
		if (isset($r_ocultarColunasDashboard)) {
			$ocultarColunasDashboard_ = $r_ocultarColunasDashboard != 'false' ? ' checked="checked"' : '';
		} else {
			$ocultarColunasDashboard_ = '';
		}
		if (isset($r_ocultarSempreColunas)) {
			$ocultarSempreColunas_ = $r_ocultarSempreColunas != 'false' ? ' checked="checked"' : '';
		} else {
			$ocultarSempreColunas_ = '';
		}
		if (isset($r_ocultarPorcentagem)) {
			$ocultarPorcentagem_ = $r_ocultarPorcentagem != 'false' ? ' checked="checked"' : '';
		} else {
			$ocultarPorcentagem_ = '';
		}
		
		if ($r_bi == 'conf') {

		/**
		* Opções extras de filtragem
		*/
		echo <<<EOT
			<div class="buttonsBottomTop gridFiltro ui-corner-all">
EOT;

		// Várias Métricas
		$divSetarMetricas = '';
		$triggersMetricas = '';
		$paramMetricas = '';
		for ($i = 0; $i < $r_numMetricas; $i++) {
			$id = ($i == 0) ? '' : 'a' . $i;
			$fato2 = 'fato2' . $id;
			$fato2_ = 'r_' . $fato2;
			$fato2_ = isset($$fato2_) ? $$fato2_ : false;
			$fato = 'fato' . $id;
			$fato_ = 'r_' . $fato;
			$fato_ = isset($$fato_) ? $$fato_ : false;
			$tipodata = 'tipodata' . $id;
			$tipodata_ = 'r_' . $tipodata;
			$tipodata_ = isset($$tipodata_) ? $$tipodata_ : false;
			$medida = 'medida' . $id;
			$medida_ = 'r_' . $medida;
			$medida_ = isset($$medida_) ? $$medida_ : false;
			
			$paramMetricas .= "+ '&{$fato}=' + encodeURIComponent(\$('#{$fato}').val()) + '&{$fato2}=' + encodeURIComponent(\$('#{$fato2}').val()) + '&{$tipodata}=' + encodeURIComponent(\$('#{$tipodata}').val()) + '&{$medida}=' + encodeURIComponent(\$('#{$medida}').val())";
			
			$divSetarMetricas .= <<<EOT
				divSetarAcaoId(divId, $('#{$fato}').val(), '{$fato}');
				divSetarAcaoId(divId, $('#{$fato2}').val(), '{$fato2}');
				divSetarAcaoId(divId, $('#{$tipodata}').val(), '{$tipodata}');
				divSetarAcaoId(divId, $('#{$medida}').val(), '{$medida}');
EOT;
			$triggersMetricas .= <<<EOT
				var primeiraVez{$fato2} = true;
				$('#{$fato2}').change(function() {
					var value = $(this).val();
					$('#{$fato} .opt').hide();
					if (value == 'ORCA') {
						$('#{$fato} .opt[value="FAC001_Orcamento_Valor_Total_Preco"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_Orcamento_Valor_Total_Preco'); }
					}
					if (value == 'PEDI') {
						$('#{$fato} .opt[value="FAC001_Pedidos_Valor_Total_Preco"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_Pedidos_Valor_Total_Preco'); }
					}
					if (value == 'NOTAFISC') {
						$('#{$fato} .opt[value="FAC001_NF_Valor_Total_Preco"]').show();
						$('#{$fato} .opt[value="FAC001_NF_Produtos_Quantidade_Itens"]').show();
						$('#{$fato} .opt[value="FAC001_NF_Produtos_Quantidade"]').show();
						$('#{$fato} .opt[value="FAC001_NF_ICMS"]').show();
						$('#{$fato} .opt[value="FAC001_NF_Quantidade"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_NF_Valor_Total_Preco'); }
					}
					if (value == 'CONTRECE') {
						$('#{$fato} .opt[value="FAC001_CR_Valor_Duplicata"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_CR_Valor_Duplicata'); }
					}
					if (value == 'CONTPAGA') {
						$('#{$fato} .opt[value="FAC001_CP_Valor_Duplicata"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_CP_Valor_Duplicata'); }
					}
					if (value == 'LANCBANC') {
						$('#{$fato} .opt[value="FAC001_LB_Valor_Lancamento"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_LB_Valor_Lancamento'); }
					}
					$('#{$fato}').trigger('change');
					primeiraVez{$fato2} = false;
				});
				$('#{$fato}').change(function() {
					var value = $(this).val();
					$('#dimensao1 .opt').removeAttr('disabled');
					$('#dimensao2 .opt').removeAttr('disabled');
					$('#{$tipodata} .opt').hide();
					if (value == 'FAC001_Orcamento_Valor_Total_Preco') {
						/*$('#dimensao1 .opt[value="DIM007_Id"]').attr('disabled', 'disabled');
						$('#dimensao2 .opt[value="DIM007_Id"]').attr('disabled', 'disabled');*/
						$('#{$tipodata} .opt[value="T003_Data_Emissao"]').show();
						$('#{$tipodata}').val('T003_Data_Emissao');
					}
					if (value == 'FAC001_Pedidos_Valor_Total_Preco') {
						$('#{$tipodata} .opt[value="T005_Data_Emissao"]').show();
						$('#{$tipodata} .opt[value="T005_Data_Saida"]').show();
						$('#{$tipodata} .opt[value="T005_Data_Entrega"]').show();
						$('#{$tipodata}').val('T005_Data_Emissao');
					}
					if (value == 'FAC001_NF_Quantidade' || value == 'FAC001_NF_Valor_Total_Preco' || value == 'FAC001_NF_Produtos_Quantidade_Itens' || value == 'FAC001_NF_Produtos_Quantidade' || value == 'FAC001_NF_ICMS') {
						$('#{$tipodata} .opt[value="T007_Data_Emissao"]').show();
						$('#{$tipodata} .opt[value="T007_Data_Saida"]').show();
						$('#{$tipodata} .opt[value="T007_Data_Vencimento"]').show();
						$('#{$tipodata}').val('T007_Data_Emissao');
					}
					if (value == 'FAC001_CR_Valor_Duplicata') {
						$('#{$tipodata} .opt[value="T002_Data_Vencimento"]').show();
						$('#{$tipodata} .opt[value="T002_Data_Emissao"]').show();
						$('#{$tipodata} .opt[value="T002_Data_Recebimento"]').show();
						$('#{$tipodata}').val('T002_Data_Vencimento');
					}
					if (value == 'FAC001_CP_Valor_Duplicata') {
						$('#{$tipodata} .opt[value="T015_Data_Vencimento"]').show();
						$('#{$tipodata} .opt[value="T015_Data_Emissao"]').show();
						$('#{$tipodata} .opt[value="T015_Data_Pagamento"]').show();
						$('#{$tipodata}').val('T015_Data_Vencimento');
					}
					if (value == 'FAC001_LB_Valor_Lancamento') {
						$('#{$tipodata} .opt[value="T019_Data_Lancamento"]').show();
						$('#{$tipodata}').val('T019_Data_Lancamento');
					}
				});
				$('#{$fato2}').trigger('change');
EOT;

			echo <<<EOT
					<span class="item">
						Entidade
						<br>
						<select id="{$fato2}" class="formInput ui-state-default ui-corner-all">
EOT;
			foreach ($fatos2 as $key => $value) {
				if (is_numeric($key)) {
					echo '<option disabled="disable">--- '.$value.' ---</option>';
				} else {
					$selected = '';
					if (isset($fato2_) && $fato2_ == $key) {
						$selected = ' selected="selected"';
					}
					echo '<option class="opt"'.$selected.' value="'.$key.'">'.$value[1].'</option>';
				}
			}
			echo <<<EOT
						</select>
					</span>
					<span class="item" style="display: none;">
						Valor
						<br>
						<select id="{$fato}" class="formInput ui-state-default ui-corner-all">
EOT;
			foreach ($fatos as $key => $value) {
				if (is_numeric($key)) {
					echo '<option disabled="disable">--- '.$value.' ---</option>';
				} else {
					$selected = '';
					if (isset($fato_) && $fato_ == $key) {
						$selected = ' selected="selected"';
					}
					echo '<option class="opt"'.$selected.' value="'.$key.'">'.$value[1].'</option>';
				}
			}
			echo <<<EOT
						</select>
					</span>
					<span class="item" style="display: none;">
						Data
						<br>
						<select id="{$tipodata}" class="formInput ui-state-default ui-corner-all">
EOT;
			foreach ($tipodatas as $key => $value) {
				if (is_numeric($key)) {
					echo '<option disabled="disable">--- '.$value.' ---</option>';
				} else {
					$selected = '';
					if (isset($tipodata_) && $tipodata_ == $key) {
						$selected = ' selected="selected"';
					}
					echo '<option class="opt"'.$selected.' value="'.$key.'">'.$value[1].'</option>';
				}
			}
			echo <<<EOT
						</select>
					</span>
					<span class="item" style="display: none;">
						Operação
						<br>
						<select id="{$medida}" class="formInput ui-state-default ui-corner-all">
EOT;
			foreach ($medidas as $key => $value) {
				if (is_numeric($key)) {
					echo '<option disabled="disable">--- '.$value.' ---</option>';
				} else {
					$selected = '';
					if (isset($medida_) && $medida_ == $key) {
						$selected = ' selected="selected"';
					}
					echo '<option class="opt"'.$selected.' value="'.$key.'">'.$value[1].'</option>';
				}
			}
			echo <<<EOT
						</select>
					</span>
EOT;
			
			if ($i == 0) {
				echo <<<EOT
					<input id="maisNumMetricas" type="submit" title="Adicionar mais Métricas" style="background-position: -17px -130px; margin: 16px 0 0 2px; cursor: pointer; display: none;" class="ui-icon ui-icon-help ui-state-default ui-corner-all" value="">
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						//$(this).closest('.buttonsBottomTop').find('.clearMetrica');
						$('#maisNumMetricas').click(function() {
							divSetarAcaoId('{$g['divId']}', {$r_numMetricas} + 1, 'numMetricas');
							divRefresh('{$g['divId']}');
						});
					} </script></div>
EOT;
			} else if (($r_numMetricas -1) == $i) {
				echo <<<EOT
					<input id="menosNumMetricas" type="submit" title="Remover Métricas" style="background-position: -49px -130px; margin: 16px 0 0 2px; cursor: pointer;" class="ui-icon ui-icon-help ui-state-default ui-corner-all" value="">
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						//$(this).closest('.buttonsBottomTop').find('.clearMetrica');
						$('#menosNumMetricas').click(function() {
							divSetarAcaoId('{$g['divId']}', {$r_numMetricas} - 1, 'numMetricas');
							divRefresh('{$g['divId']}');
						});
					} </script></div>
EOT;
			}
			
			if ($i == 0 || $i == ($r_numMetricas - 2) || $i == ($r_numMetricas - 1)) {
				echo <<<EOT
					
EOT;
			} else {
				echo '<span class="item" style="width: 2px; height: 24px; margin-top: 6px; background-color: silver;"></span>';
			}
		}
		
		// Javascript dos combo-box
		if ($r_bi == 'conf') {
		/**
		* Dimensões e Fatos
		*/
		$gridID = md5($g['r_']);
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .gridFiltroBotoes:eq(0) input:eq(0)').val('Gerar Relatório/Filtrar').click(function(data) {
					var divId = '{$g['divId']}';
					divSetarAcaoId(divId, '', 'D132_Id');
					{$divSetarMetricas}
					divSetarAcaoId(divId, $('#dimensao1').val(), 'dimensao1');
					divSetarAcaoId(divId, $('#dimensao1ord').val(), 'dimensao1ord');
					divSetarAcaoId(divId, $('#dimensao2').val(), 'dimensao2');
					divSetarAcaoId(divId, $('#dimensao2ord').val(), 'dimensao2ord');
					divSetarAcaoId(divId, $('#incluirLinhaVazia').is(':checked'), 'incluirLinhaVazia');
					divSetarAcaoId(divId, $('#incluirColunaVazia').is(':checked'), 'incluirColunaVazia');
					divSetarAcaoId(divId, $('#mostrarCurvaABC').is(':checked'), 'mostrarCurvaABC');
					divSetarAcaoId(divId, $('#ocultarColunasDashboard').is(':checked'), 'ocultarColunasDashboard');
					divSetarAcaoId(divId, $('#ocultarSempreColunas').is(':checked'), 'ocultarSempreColunas');
					divSetarAcaoId(divId, $('#ocultarPorcentagem').is(':checked'), 'ocultarPorcentagem');
					divSetarAcaoId(divId, $('input[name=exibir]:checked').val(), 'exibir');
					divSetarAcaoId(divId, $('#incluirGraficoLabel').is(':checked'), 'incluirGraficoLabel');
					divSetarAcaoId(divId, $('#incluirGraficoLegend').is(':checked'), 'incluirGraficoLegend');
					//divRefresh(divId);
					return false;
				});
				$('#{$g['divId']} .gridFiltroBotoes:eq(0) input:eq(1)').hide();
				$('<input type="submit" value="Salvar">').click(function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Salvar Relatório', '/etc/etc014/content/etc014ContentSalvar/', '&acaoId=' + encodeURIComponent('') + '&tabela=D132' + '&dimensao1=' + encodeURIComponent($('#dimensao1').val()) + '&dimensao1ord=' + encodeURIComponent($('#dimensao1ord').val()) + '&dimensao2=' + encodeURIComponent($('#dimensao2').val()) + '&mostrarCurvaABC=' + encodeURIComponent($('#mostrarCurvaABC').is(':checked')) + '&ocultarColunasDashboard=' + encodeURIComponent($('#ocultarColunasDashboard').is(':checked')) + '&ocultarSempreColunas=' + encodeURIComponent($('#ocultarSempreColunas').is(':checked')) + '&ocultarPorcentagem=' + encodeURIComponent($('#ocultarPorcentagem').is(':checked')) + '&exibir=' + encodeURIComponent($('input[name=exibir]:checked').val()) + '&incluirLinhaVazia=' + encodeURIComponent($('#incluirLinhaVazia').is(':checked')) + '&incluirColunaVazia=' + encodeURIComponent($('#incluirColunaVazia').is(':checked')) + '&dimensao2ord=' + encodeURIComponent($('#dimensao2ord').val()) + '&md5=' + encodeURIComponent('{$gridID}') + '&incluirGraficoLabel=' + encodeURIComponent($('#incluirGraficoLabel').is(':checked')) + '&incluirGraficoLegend=' + encodeURIComponent($('#incluirGraficoLegend').is(':checked')) + '&numMetricas={$r_numMetricas}' {$paramMetricas}, [350,130]);
					return false;
				}).appendTo('#{$g['divId']} .gridFiltroBotoes:eq(0)');
				// Disabilita dimensões e datas dependendo do fato escolhido
				$('#dimensao1').change(function() {
					var nome = $("#dimensao1 option[value='" + $(this).val() + "']").text();
					$('#dimensao1ord .opt:eq(0)').text(nome + ' ASC');
					$('#dimensao1ord .opt:eq(1)').text(nome + ' DESC');
				});
				$('#dimensao2').change(function() {
					var nome = $("#dimensao2 option[value='" + $(this).val() + "']").text();
					$('#dimensao2ord .opt:eq(0)').text(nome + ' ASC');
					$('#dimensao2ord .opt:eq(1)').text(nome + ' DESC');
				});
				{$triggersMetricas}
				$('#dimensao1').trigger('change');
				$('#dimensao2').trigger('change');
			} </script></div>
EOT;
		}
		
		// Caso seja um gráfico, não precisa curva abc
		if (isset($r_exibir) && $r_exibir != 'grid') {
			$r_mostrarCurvaABC = 'false';
		}
		echo <<<EOT
				<span class="item">
					Linha
					<br>
					<select id="dimensao1" class="formInput ui-state-default ui-corner-all">
EOT;
		foreach ($dimensoes as $key => $value) {
			if (is_numeric($key)) {
				echo '<option disabled="disable">--- '.$value.' ---</option>';
			} else {
				$selected = '';
				if (isset($r_dimensao1) && $r_dimensao1 == $key) {
					$selected = ' selected="selected"';
				}
				echo '<option class="opt"'.$selected.' value="'.$key.'">'.$value[1].'</option>';
			}
		}
		echo <<<EOT
					</select>
				</span>
				<span class="item" style="display: none;">
					Ordenação
					<br>
					<select id="dimensao1ord" class="formInput ui-state-default ui-corner-all">
EOT;
		foreach ($ordem as $key => $value) {
			if (is_numeric($key)) {
				echo '<option disabled="disable">--- '.$value.' ---</option>';
			} else {
				$selected = '';
				if (isset($r_dimensao1ord) && $r_dimensao1ord == $key) {
					$selected = ' selected="selected"';
				}
				echo '<option class="opt"'.$selected.' value="'.$key.'">'.$value[1].'</option>';
			}
		}
		echo <<<EOT
					</select>
				</span>
				<span class="item">
					Coluna
					<br>
					<select id="dimensao2" class="formInput ui-state-default ui-corner-all">
EOT;
		foreach ($dimensoes as $key => $value) {
			if (is_numeric($key)) {
				echo '<option disabled="disable">--- '.$value.' ---</option>';
			} else {
				$selected = '';
				if (isset($r_dimensao2) && $r_dimensao2 == $key) {
					$selected = ' selected="selected"';
				}
				echo '<option class="opt"'.$selected.' value="'.$key.'">'.$value[1].'</option>';
			}
		}
		echo <<<EOT
					</select>
				</span>
				<span class="item" style="display: none;">
					Ordenação
					<br>
					<select id="dimensao2ord" class="formInput ui-state-default ui-corner-all">
EOT;
		foreach ($ordem as $key => $value) {
			if (is_numeric($key)) {
				echo '<option disabled="disable">--- '.$value.' ---</option>';
			} else {
				$selected = '';
				if (isset($r_dimensao2ord) && $r_dimensao2ord == $key) {
					$selected = ' selected="selected"';
				}
				echo '<option class="opt"'.$selected.' value="'.$key.'">'.$value[1].'</option>';
			}
		}
		echo <<<EOT
					</select>
				</span>
				<div style="clear:both;"></div>
EOT;
		
		// Exibir & Opções
		/*echo 'Tabela: ';
		$checked = isset($r_exibir) && $r_exibir == 'grid' ? ' checked="checked"' : '';
		echo '<input type="radio" name="exibir" value="grid"'.$checked.'> Grid';
		echo <<<EOT
				<input type="checkbox" id="mostrarCurvaABC" value="1"{$mostrarCurvaABC_} />&nbsp;Curva ABC&nbsp;<input type="checkbox" id="ocultarColunasDashboard" value="1"{$ocultarColunasDashboard_} />&nbsp;Ocultar colunas no dashboard&nbsp;<input type="checkbox" id="ocultarSempreColunas" value="1"{$ocultarSempreColunas_} />&nbsp;Ocultar colunas sempre&nbsp;<input type="checkbox" id="ocultarPorcentagem" value="1"{$ocultarPorcentagem_} />&nbsp;Ocultar porcentagem&nbsp;<input type="checkbox" id="incluirLinhaVazia" value="1"{$incluirLinhaVazia_} />&nbsp;Incluir linhas vazias&nbsp;<input type="checkbox" id="incluirColunaVazia" value="1"{$incluirColunaVazia_} />&nbsp;Incluir colunas vazias
				<br />
EOT;
		echo 'Gráfico: ';
		$checked = isset($r_exibir) && $r_exibir == 'barra' ? ' checked="checked"' : '';
		echo '<input type="radio" name="exibir" value="barra"'.$checked.'> Barra';
		$checked = isset($r_exibir) && $r_exibir == 'pizza' ? ' checked="checked"' : '';
		echo '<input type="radio" name="exibir" value="pizza"'.$checked.'> Pizza';
		$checked = isset($r_exibir) && $r_exibir == 'linha' ? ' checked="checked"' : '';
		echo '<input type="radio" name="exibir" value="linha"'.$checked.'> Linha';
		$checked = isset($r_exibir) && $r_exibir == 'area' ? ' checked="checked"' : '';
		echo '<input type="radio" name="exibir" value="area"'.$checked.'> Area';
		$checked = isset($r_exibir) && $r_exibir == 'radar' ? ' checked="checked"' : '';
		echo '<input type="radio" name="exibir" value="radar"'.$checked.'> Radar';
		echo "&nbsp;<input type=\"checkbox\" id=\"incluirGraficoLabel\" value=\"1\"{$incluirGraficoLabel_} />&nbsp;Incluir Rótulos";
		echo "&nbsp;<input type=\"checkbox\" id=\"incluirGraficoLegend\" value=\"1\"{$incluirGraficoLegend_} />&nbsp;Incluir Legenda";
		*/
		echo '</div>';
		}
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'DIM002_Nome_Empresa, DIM002_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
			'DIM001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'DIM003_Estado' => array('titulo' => 'Estado', 'tipo' => 'texto'),
			'DIM003_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'DIM003_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			//'DIM003_Endereco' => array('titulo' => 'Endereço', 'tipo' => 'texto'),
			'D4Vendedor.DIM004_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'D4Supervisor.DIM004_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'DIM011_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
			'DIM007_Conta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'DIM007_Flag_Deb_Crd' => array('titulo' => 'Débido/Crédito', 'tipo' => 'texto'),
			'DIM008_D006_Flag_Entrada_Saida' => array('titulo' => 'NF Tipo (S/E)', 'tipo' => 'texto'),
			'DIM010_D006_Codigo_CFOP_e_Descricao' => array('titulo' => 'NF CFOP', 'tipo' => 'texto'),
			'DIM009_D001_Descricao_Produto' => array('titulo' => 'Produto Descrição', 'tipo' => 'texto'),
			'DIM009_D082_Marca' => array('titulo' => 'Produto Marca', 'tipo' => 'texto'),
			'DIM009_C008_Tipo' => array('titulo' => 'Produto Tipo', 'tipo' => 'texto'),
			'DIM009_D003_Nome_Linha' => array('titulo' => 'Produto Linha', 'tipo' => 'texto'),
			'DIM006_Data' => array('titulo' => 'Recebimento/Pagamento', 'tipo' => 'data'),
		);
		
		// Descrição (Se for relatório salvo)
		if ($r_bi == 'visu' && isset($D132_Descricao)) {
			if ($modificado) {
				$filtradoTxt = '';
				foreach ($filtrado['campos'] as $filtroKey => $filtro) {
					if (is_array($filtro['busca'])) {
						if (!empty($filtro['busca'][0]) || !empty($filtro['busca'][1])) {
							$filtradoTxt .= $gridFiltro[$filtroKey]['titulo'] . ': ' . $filtro['busca'][0] . ' - ' . $filtro['busca'][1] . ' ';
						}
					} else {
						if (!empty($filtro['busca'])) {
							$filtradoTxt .= $gridFiltro[$filtroKey]['titulo'] . ': ' . $filtro['busca'] . ' ';
						}
					}
				}
				$modificado = '<br /><span style="font-weight: normal; font-size: 10px;">(' . $filtradoTxt . ')</style>';
			}
			echo '<div style="padding: 4px 10px; font-size: 14px;"><span style="font-weight: bold;">'.$D132_Descricao.$modificado.'</span></div>';
		}
		
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(false, $gridFiltro, '', $md5, $filtroSalvo);
		if ($r_bi == 'conf' || isset($r_grid)) {
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro, $md5);
		} else {
			// Esconde o filtro
			echo '<div style="display: none; position: fixed; z-index: 9999" class="dashboardFiltro">';
				echo $g['ui']->gridFiltroPrint($gridFiltro, $md5);
			echo '</div>';
			// Botão para abertura do filtro
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					// Abrir Filtros
					/*
					// botão para mostrar só o filtro, substituido pelo grid c/ filtro
					var divFiltro = $('<div></div>')
						.attr({
							'title': 'Filtros'
						})
						.addClass('icone')
						.css({
							'margin-right': '5px',
							'position': 'absolute',
							'top': '7px',
							'right': 0
						})
						.click(function() {
							var parent = $(this).parent();
							var filtro = parent.find('.dashboardFiltro');
							if (filtro.is(':hidden')) {
								parent
									.data('overflow', parent.css('overflow'))
									.css({
										'overflow': 'hidden',
									});
								filtro
									.css({
										'width': '1000px',
										'top': ($('#desktop').height() - 100)/2 + 'px',
										'left': ($('#desktop').width() - 1000)/2 + 'px',
									})
									.show()
									.find('.gridFiltro').css({
										'box-shadow': '0 0 3px 5px #A6A6A6',
									});
								// Icone de fechar filtro
								var divIcone = $('<div></div>')
									.attr({
										'title': 'Fechar'
									})
									.addClass('icone')
									.css({
										'margin-right': '8px',
										'position': 'absolute',
										'top': '7px',
										'right': 0
									})
									.click(function() {
										$(this).closest('.formEditor').find('.icone[title="Filtros"]').trigger('click');
									})
									.appendTo(filtro);
								$('<span>').addClass('ui-icon ui-icon-circle-close').attr('title', 'Fechar').appendTo(divIcone);
								reloadUI($('#{$g['divId']}').attr('id'));
							} else {
								parent
									.css({
										'overflow': parent.data('overflow')
									});
								filtro.find('.icone').remove();
								filtro.hide();
							}
						})
						.appendTo($('#{$g['divId']}').closest('.formEditor'));
					$('<span>').addClass('ui-icon ui-icon-search').attr('title', 'Filtrar').css({'background-image': 'url("/hardness3/static/css/temas/padrao/images/ui-icons_bbbbbb_256x240.png")'}).appendTo(divFiltro);
					*/
					// Abrir Grid
					var divGrid = $('<div></div>')
						.attr({
							'title': 'Abrir Grid'
						})
						.addClass('icone')
						.css({
							'margin-right': '5px',
							'position': 'absolute',
							'top': '7px',
							'right': 0
						})
						.click(function() {
							abrirJanela(false, 'B.I.', '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc014/content/etc014ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent('{$r_D132_Id}') + '&bi=visu&grid=true&tab=' + encodeURIComponent('{$r_tab}'), [1200,600]);
						})
						.appendTo($('#{$g['divId']}').closest('.formEditor'));
					$('<span>').addClass('ui-icon ui-icon-zoomin').attr('title', 'Abrir Grid / Filtrar').css({'background-image': 'url("/hardness3/static/css/temas/padrao/images/ui-icons_bbbbbb_256x240.png")'}).appendTo(divGrid);
					// Refresh
					var divRecarregar = $('<div></div>')
						.attr({
							'title': 'Recarregar'
						})
						.addClass('icone')
						.css({
							'margin-right': '20px',
							'position': 'absolute',
							'top': '7px',
							'right': 0
						})
						.click(function() {
							divRefresh('{$g['divId']}');
						})
						.appendTo($('#{$g['divId']}').closest('.formEditor'));
					$('<span>').addClass('ui-icon ui-icon-refresh').attr('title', 'Recarregar').css({'background-image': 'url("/hardness3/static/css/temas/padrao/images/ui-icons_bbbbbb_256x240.png")'}).appendTo(divRecarregar);
				} </script></div>
EOT;
		}

		if (!isset($r_dimensao1) || empty($r_dimensao1)) { die(); }
		/*
		$dimensoes[$r_dimensao1]
		$dimensoes[$r_dimensao2]
		$fatos[$r_fato]
		$medida[$r_medida]
		$ordem[$r_dimensao1ord]
		$ordem[$r_dimensao2ord]
		$r_mostrarCurvaABC
		$r_incluirLinhaVazia
		$r_incluirColunaVazia
		$tipodata[$r_tipodata]
		$r_exibir
		$r_incluirGraficoLabel
		$r_incluirGraficoLegend
		*/
		
		// Modifica o array dimensões para a data correta
		$dimensoes['DDDMS'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDDMS'][0]);
		$dimensoes['DDA'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDA'][0]);
		$dimensoes['DDM'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDM'][0]);
		$dimensoes['DDDS'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDDS'][0]);
		$dimensoes['DDD'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDD'][0]);
		
		// Variaveis
		$extraVazioLinha = ($r_incluirLinhaVazia == 'false') ? 'HAVING (Valor != 0)' : '';
		$extraVazioColuna = ($r_incluirColunaVazia == 'false') ? 'HAVING (Valor != 0)' : '';

		/**
		* Grid - Let's Go!
		*/
		// Colunas iniciais: Count/Totais + Curva ABC (se for Total DESC)
		$grid = array();
		$primeiraLinhaGridHead .= '<th colspan="1" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>';
		if ($r_mostrarCurvaABC != 'false') {
			$primeiraLinhaGridHead .= '<th colspan="1" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>';
			$grid[] = array('colunaValor' => "ABC", 'colunaTitulo' => 'ABC', 'colunaAlinhar' => 'center');
		}
		
		$grid[] = array('colunaValor' => "count", 'colunaTitulo' => '-', 'colunaAlinhar' => 'center', 'style' => 'width: 1%');
		$grid[] = array('colunaValor' => "dim01", 'colunaTitulo' => $dimensoes[$r_dimensao1][1], 'colunaAlinhar' => 'left');
		$primeiraLinhaGridHead .= '<th colspan="1" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>';
		$primeiraLinhaGridHead .= '<th colspan="1" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>';
		
		// Agrupamento das métricas (th do thead com colspan)
		if ($r_numMetricas > 1) {
			$last = '';
			$count = 0;
			for ($i = 0; $i < $r_numMetricas; $i++) {
				$count++;
				$id = ($i == 0) ? '' : 'a' . $i;
				$fato2 = 'fato2' . $id;
				$fato2_ = 'r_' . $fato2;
				$fato2_ = isset($$fato2_) ? $$fato2_ : false;
				if ($i != 0 && $fato2_ != $last) {
					if (empty($ocultarPorcentagem_)) {
						$primeiraLinhaGridHead .= '<th colspan="' . (($count - 1) * 2) . '" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">' . $fatos2[$last][1] . '</th>';
					} else {
						$primeiraLinhaGridHead .= '<th colspan="' . ($count - 1) . '" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">' . $fatos2[$last][1] . '</th>';
					}
					$count = 1;
				}
				$last = $fato2_;
			}
			
			if ($count > 0) {
				if (empty($ocultarPorcentagem_)) {
					$primeiraLinhaGridHead .= '<th colspan="' . (($count + 1) * 2) . '" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">' . $fatos2[$last][1] . '</th>';
				} else {
					$primeiraLinhaGridHead .= '<th colspan="' . ($count + 1) . '" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">' . $fatos2[$last][1] . '</th>';
				}
			}
		} else {
			$colspan = empty($ocultarPorcentagem_) ? 2 : 1;
			$primeiraLinhaGridHead .= '<th colspan="' . $colspan . '" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">' . $fatos2[$r_fato2][1] . '</th>';
		}
		
		for ($i = 0; $i < $r_numMetricas; $i++) {
			$id = ($i == 0) ? '' : 'a' . $i;
			$fato2 = 'fato2' . $id;
			$fato2_ = 'r_' . $fato2;
			$fato2_ = isset($$fato2_) ? $$fato2_ : false;
			$fato = 'fato' . $id;
			$fato_ = 'r_' . $fato;
			$fato_ = isset($$fato_) ? $$fato_ : false;
			$grid[] = array('colunaValor' => "dim02-{$i}", 'colunaTitulo' => $fatos[$fato_][1], 'colunaAlinhar' => 'right', 'style' => 'width: 1%; font-weight: bold;', 'extraTH' => 'colspan="1"');
			if (empty($ocultarPorcentagem_)) {
				$grid[] = array('colunaValor' => "percentLinha-{$i}", 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;', 'extraTH' => 'colspan="1"');
			}
		}
		// count - $grid[] = array('colunaValor' => "dim02", 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'classTH' => 'extraTHGhost');
		
		// Curva ABC
		if ($r_mostrarCurvaABC != 'false') {
			// Consulta a primeira dimensão (CURVA ABC)
			$sql = <<<EOT
				SELECT
					{$dimensoes[$r_dimensao1][0]},
					{$medidas[$r_medida][0]}({$fatos[$r_fato][0]}) AS Valor
				FROM FAC001
				{$joins}
				{$extra}
				GROUP BY
					{$dimensoes[$r_dimensao1][0]}
				{$extraVazioLinha}
				ORDER BY
					Valor DESC
EOT;
			$result = mysql_query($sql);
			if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
			// Calcula curva ABC
			$totalRegistros = mysql_numrows($result);
			$totalValor = 0;
			while ($row = mysql_fetch_array($result)) {
				$totalRegistros++;
				$totalValor += $row[1];
			}
			$curvaABCTotal = array('A' => array(0, 0), 'B' => array(0, 0), 'C' => array(0, 0));
			$curvaABC = array();
			$curvaA = round($totalValor * 0.8, 2);
			$curvaB = round($curvaA + ($totalValor * 0.1), 2);
			$totalTmp = 0;
			@mysql_data_seek($result, 0);
			while ($row = mysql_fetch_array($result)) {
				if ($totalTmp <= $curvaA) {
					$curvaABC[$row[0]] = 'A';
				} else if ($totalTmp <= $curvaB) {
					$curvaABC[$row[0]] = 'B';
				} else {
					$curvaABC[$row[0]] = 'C';
				}
				$curvaABCTotal[$curvaABC[$row[0]]][0] += 1;
				$curvaABCTotal[$curvaABC[$row[0]]][1] += $row[1];
				$totalTmp += $row[1];
			}
		}
		
		/**
		* Pega os totais das métricas
		*/
		$dim1Total = $dim1NumLinhas = $dim2NumLinhas = array();
		for ($i = 0; $i < $r_numMetricas; $i++) {
			$id = ($i == 0) ? '' : 'a' . $i;
			$fato2 = 'fato2' . $id;
			$fato2_ = 'r_' . $fato2;
			$fato2_ = isset($$fato2_) ? $$fato2_ : false;
			$fato = 'fato' . $id;
			$fato_ = 'r_' . $fato;
			$fato_ = isset($$fato_) ? $$fato_ : false;
			$tipodata = 'tipodata' . $id;
			$tipodata_ = 'r_' . $tipodata;
			$tipodata_ = isset($$tipodata_) ? $$tipodata_ : false;
			$medida = 'medida' . $id;
			$medida_ = 'r_' . $medida;
			$medida_ = isset($$medida_) ? $$medida_ : false;
			
			// Consulta a primeira dimensão (TOTAL)
			$sql = <<<EOT
				SELECT
					{$dimensoes[$r_dimensao1][0]},
				    {$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor
				FROM FAC001
				{$joins}
				{$extra}
EOT;
			$result = mysql_query($sql);
			if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
			$row = mysql_fetch_row($result);
			$dim1Total[$i] = isset($row[1]) ? $row[1] : 0;
			// Consulta a primeira dimensão (NUMERO LINHAS)
			$sql = <<<EOT
				SELECT
					{$dimensoes[$r_dimensao1][0]},
					{$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor
				FROM FAC001
				{$joins}
				{$extra}
				GROUP BY
					{$dimensoes[$r_dimensao1][0]}
				{$extraVazioLinha}
EOT;
			$result = mysql_query($sql);
			if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
			$dim1NumLinhas[$i] = mysql_numrows($result);
			
			// Consulta a segunda dimensão (NUMERO DE LINHAS)
			if ($r_dimensao2ord == 'ONA' || $r_dimensao2ord == 'OND') {
				$ordemD2 = "{$dimensoes[$r_dimensao2][0]} {$ordem[$r_dimensao2ord][0]}";
			} else {
				$ordemD2 = "Valor {$ordem[$r_dimensao2ord][0]}";
			}
			$sql = <<<EOT
				SELECT
					{$dimensoes[$r_dimensao2][0]},
					{$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor
				FROM FAC001
				{$joins}
				{$extra}
				GROUP BY
					{$dimensoes[$r_dimensao2][0]}
				{$extraVazioColuna}
EOT;
			$result = mysql_query($sql);
			if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
			$dim2NumLinhas[$i] = mysql_numrows($result);
		}
		
		// Passamos a primeira dimensão para o gerado (elemento grid) e um array extra para identificar a posição da dimensão neste array
		$geradoDados = array();
		if ($r_mostrarCurvaABC != 'false') {
			$tmp = array('', '');
		} else {
			$tmp = array('');
		}
		// TODO: merge da primeira dimensão
		$tmp[] = '<span style="font-weight: bold;">Total (Linhas: ' . number_format($dim1NumLinhas[0], 0, '.', '') . ' / Colunas: ' . number_format($dim2NumLinhas[0], 0, '.', '') . ')</span>';
		foreach ($dim1Total as $value) {
			$tmp[] = formataNumero($value, true);
			if (empty($ocultarPorcentagem_)) {
				$tmp[] = '100%';
			}
		}
		$geradoDados[] = $tmp;
		
		$countLinha = 0;
		for ($i = 0; $i < $r_numMetricas; $i++) {
			$id = ($i == 0) ? '' : 'a' . $i;
			$fato2 = 'fato2' . $id;
			$fato2_ = 'r_' . $fato2;
			$fato2_ = isset($$fato2_) ? $$fato2_ : false;
			$fato = 'fato' . $id;
			$fato_ = 'r_' . $fato;
			$fato_ = isset($$fato_) ? $$fato_ : false;
			$tipodata = 'tipodata' . $id;
			$tipodata_ = 'r_' . $tipodata;
			$tipodata_ = isset($$tipodata_) ? $$tipodata_ : false;
			$medida = 'medida' . $id;
			$medida_ = 'r_' . $medida;
			$medida_ = isset($$medida_) ? $$medida_ : false;
			
			// Consulta a primeira dimensão (OS ELEMENTOS AGRUPADOS)
			if ($r_dimensao1ord == 'ONA' || $r_dimensao1ord == 'OND') {
				$ordemD1 = "{$dimensoes[$r_dimensao1][0]} {$ordem[$r_dimensao1ord][0]}";
			} else {
				$ordemD1 = "Valor {$ordem[$r_dimensao1ord][0]}";
			}
			$sql = <<<EOT
				SELECT
					{$dimensoes[$r_dimensao1][0]},
				    {$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor
				FROM FAC001
				{$joins}
				{$extra}
				GROUP BY
					{$dimensoes[$r_dimensao1][0]}
				{$extraVazioLinha}
				ORDER BY
					{$ordemD1}
				LIMIT 1000
EOT;
			$geradoSql = addslashes(str_replace("\"", "'", str_replace("\r", ' ', str_replace("\n", ' ', $sql))));
			$result = mysql_query($sql);
			if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
			
			/*
			* Merge das Dimensões
			* Os passos são:
			* 		1. Fazer o loop, adicionar o ITEM e o TOTAL
			* 		2. Percorrer todas as linhas para ver se alguma não foi adiciona o TOTAL, e adiciona em branco (para fazer o merge de várias métricas)
			*/
			while ($row = mysql_fetch_row($result)) {
				// Total por linha
				$totalPorLinha_ = $row[1];
				//$totalPorLinha = ($fato_ == 'FAC001_NF_Quantidade') ? number_format($row[1], 0, '.', ',') : formataNumero($row[1]);
				$totalPorLinha = formataNumero($row[1]);
				// Porcentagem total por linha
				$porcentagem  = round(($totalPorLinha_ * 100) / $dim1Total[$i], 2);
				if ($porcentagem != 100) {
					$porcentagem = number_format($porcentagem, 2, ',', '');
				}
				// Linha Nova
				$nova = array();
				// Utilizado para guardar o valor original
				$nova[-1] = $row[0];
				// Curva ABC
				if ($i == 0 && $r_mostrarCurvaABC != 'false') {
					switch ($curvaABC[$row[0]]) {
						case 'A':
							$cor = 'green';
							break;
						case 'B':
							$cor = 'blue';
							break;
						case 'C':
							$cor = 'red';
							break;
					}
					$nova[] = "<span style=\"color: {$cor}; font-weight: bold;\">{$curvaABC[$row[0]]}</span>";
				}
				
				if ($i != 0) {
					$found = false;
					foreach ($geradoDados as $key => $value) {
						if (!isset($value[-1])) {
							continue;
						}
						if ($row[0] == $value[-1]) {
							$found = true;
							
							//echo count($geradoDados[$key]) . " - {$totalPorLinha} - {$i}<br />";
							// métricas nulas ou vazias
							$x = empty($ocultarPorcentagem_) ? 2 : 1;
							$numCols = 3 + ($i * $x);
							// totais de métricas anteriores
							for ($tmp = count($geradoDados[$key]); $tmp < $numCols; $tmp++) {
								$geradoDados[$key][] = '<span style="color: gray;">0,00</span>';
							}
							
							$geradoDados[$key][] = $totalPorLinha;
							if (empty($ocultarPorcentagem_)) {
								$geradoDados[$key][] = $porcentagem;
							}
						}
					}
					if (!$found) {
						// 0: número da linha
						$nova[] = ++$countLinha;
						// 1: nome
						$nova[] = $row[0];
						// totais de métricas anteriores
						for ($tmp = 0; $tmp < (empty($ocultarPorcentagem_) ? ($i * 2) : $i); $tmp++) {
							$nova[] = '<span style="color: gray;">0,00</span>';
						}
						// 2: total
						$nova[] = $totalPorLinha;
						// 3: porcentagem
						if (empty($ocultarPorcentagem_)) {
							$nova[] = $porcentagem;
						}
						// Formata dia do mês / dia da semana
						if (strpos($dimensoes[$r_dimensao1][0], '_Dia_Da_Semana') != false) {
							$nova[1] = formataDiaSemana($nova[1]);
						} else if (strpos($dimensoes[$r_dimensao1][0], '_Mes') != false) {
							$nova[1] = formataDiaMes($nova[1]);
						}
						// adicionar
						$geradoDados[] = $nova;
					}
				} else {
					// 0: número da linha
					$nova[] = ++$countLinha;
					// 1: nome
					$nova[] = $row[0];
					// 2: total
					$nova[] = $totalPorLinha;
					// 3: porcentagem
					if (empty($ocultarPorcentagem_)) {
						$nova[] = $porcentagem;
					}
					// Formata dia do mês / dia da semana
					if (strpos($dimensoes[$r_dimensao1][0], '_Dia_Da_Semana') != false) {
						$nova[1] = formataDiaSemana($nova[1]);
					} else if (strpos($dimensoes[$r_dimensao1][0], '_Mes') != false) {
						$nova[1] = formataDiaMes($nova[1]);
					}
					// adicionar
					$geradoDados[] = $nova;
				}
			}
			
			
			/*$dimensao1 = array();
			$dim1TotalRegistros = 0;
			while ($row = mysql_fetch_row($result)) {
				
				// Total por linha
				$dim1TotalLinha = $row[1];
				$row[1] = formataNumero($row[1]);
				// Porcentagem total por linha
				$porcentagem  = round(($dim1TotalLinha[$i] * 100) / $dim1Total[$i], 2);
				if ($porcentagem != 100) {
					$porcentagem = number_format($porcentagem, 2, ',', '');
				}
				$row[2] = $porcentagem . '%';
				// Utilizado para guardar o valor original
				$row_[-1] = $row[0];
				if (strpos($dimensoes[$r_dimensao1][0], '_Dia_Da_Semana') != false) {
					$row[0] = formataDiaSemana($row[0]);
				} else if (strpos($dimensoes[$r_dimensao1][0], '_Mes') != false) {
					$row[0] = formataDiaMes($row[0]);
				}
				$row_[] = ++$count;
				foreach ($row as $key => $value) {
					$row_[] = $value;
					/* count -
					if ($key == 1) {
						// adiciona depois do total
						$extraD1Registros = gInsertExtraWhere($extra, "{$dimensoes[$r_dimensao1][0]} = '".mysql_real_escape_string($row[0])."'");
						$sql = <<<EOT
							SELECT
								count({$dimensoes[$r_dimensao1][0]}) as Registros
							FROM FAC001
							{$joins}
							{$extraD1Registros}
EOT;
						$resultD1Registros = mysql_query($sql);
						if (!$resultD1Registros) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
						$rowD1Registros = mysql_fetch_row($resultD1Registros);
						$D1Registros = isset($rowD1Registros[0]) ? $rowD1Registros[0] : 0;
						$row_[] = $D1Registros;
						$dim1TotalRegistros += $D1Registros;
					}
					****
				}
				if ($i == 0) {
					$geradoDados[] = $row_;
				} else {
					$found = false;
					foreach ($geradoDados as $key => $value) {
						if (isset($value[-1]) && $value[-1] == $row_[-1]) {
							$found = true;
							$geradoDados[$key][] = $row_[2];
							$geradoDados[$key][] = $row_[3];
						}
					}
					if (!$found) {
						for ($tmp = 0; $tmp < ($i * 2); $tmp++) {
							$row_[] = '';
						}
						$geradoDados[] = $row_;
					}
				}
			}*/
		}
		/* count -
		if ($r_mostrarCurvaABC != 'false') {
			$geradoDados[0][4] = number_format($dim1TotalRegistros, 0, '.', '');
		} else {
			$geradoDados[0][3] = number_format($dim1TotalRegistros, 0, '.', '');
		}
		*/
		// Adiciona tabela Pivot da SEGUNDA DIMENSÃO
		if ($r_dimensao2ord == 'ONA' || $r_dimensao2ord == 'OND') {
			$ordemD2 = "{$dimensoes[$r_dimensao2][0]} {$ordem[$r_dimensao2ord][0]}";
		} else {
			$ordemD2 = "Valor {$ordem[$r_dimensao2ord][0]}";
		}
		$sql = <<<EOT
			SELECT
				{$dimensoes[$r_dimensao2][0]},
				{$medidas[$r_medida][0]}({$fatos[$r_fato][0]}) AS Valor
			FROM FAC001
			{$joins}
			{$extra}
			GROUP BY
				{$dimensoes[$r_dimensao2][0]}
			{$extraVazioColuna}
			ORDER BY
				{$ordemD2}
			LIMIT 50
EOT;
		$resultDim2 = mysql_query($sql);
		if (!$resultDim2) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
		$pivotD2Count = mysql_numrows($resultDim2);
		while ($rowDim2 = mysql_fetch_row($resultDim2)) {
			$colunaTitulo = $rowDim2[0];
			if (strpos($dimensoes[$r_dimensao2][0], '_Dia_Da_Semana') != false) {
				$colunaTitulo = formataDiaSemana($colunaTitulo);
			} else if (strpos($dimensoes[$r_dimensao2][0], '_Mes') != false) {
				$colunaTitulo = formataDiaMes($colunaTitulo);
			}
			if (!empty($ocultarSempreColunas_) || (!empty($ocultarColunasDashboard_) && $r_bi == 'visu' && (!isset($r_grid) || empty($r_grid)))) {
				//
			} else {
				$primeiraLinhaGridHead .= '<th colspan="' . ($r_numMetricas) . '" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">' . $colunaTitulo . '</th>';
			}
			for ($i = 0; $i < $r_numMetricas; $i++) {
				$id = ($i == 0) ? '' : 'a' . $i;
				$fato2 = 'fato2' . $id;
				$fato2_ = 'r_' . $fato2;
				$fato2_ = isset($$fato2_) ? $$fato2_ : false;
				$fato = 'fato' . $id;
				$fato_ = 'r_' . $fato;
				$fato_ = isset($$fato_) ? $$fato_ : false;
				$grid[] = array('colunaValor' => uniqid(), 'colunaTitulo' => $fatos2[$fato2_][1], 'colunaAlinhar' => 'right', 'colunaAlinhar' => 'center', 'extraTH' => 'colspan="1"');
			
				$key = mysql_real_escape_string($rowDim2[0]);
				// Total
				// /*
				$sql = <<<EOT
					SELECT
						{$dimensoes[$r_dimensao1][0]},
						{$medidas[$r_medida][0]}({$fatos[$fato_][0]}) AS Valor
					FROM FAC001
					{$joins}
					{$extra} AND ({$dimensoes[$r_dimensao2][0]} = '{$key}')
EOT;
				$resultDim2Ind = mysql_query($sql);
				if (!$resultDim2Ind) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
				$row = mysql_fetch_row($resultDim2Ind);
				$total = isset($row[1]) ? $row[1] : 0;
				$geradoDados[0][count($geradoDados[0])] = formataNumero($total, true);
				// */
				// Para cada item do pivot, faz uma nova consulta
				$sql = <<<EOT
					SELECT
						{$dimensoes[$r_dimensao1][0]},
					    {$medidas[$r_medida][0]}({$fatos[$fato_][0]}) AS Valor
					FROM FAC001
					{$joins}
					{$extra} AND ({$dimensoes[$r_dimensao2][0]} = '{$key}')
					GROUP BY
					    {$dimensoes[$r_dimensao1][0]}
					ORDER BY
						{$dimensoes[$r_dimensao1][0]} ASC
EOT;
				$resultDim2Ind = mysql_query($sql);
				if (!$resultDim2Ind) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
				if (mysql_num_rows($resultDim2Ind) == 0) {
					foreach ($geradoDados as $key_ => $geradoDadosItem) {
						if ($key_ == 0) { continue; }
						$geradoDados[$key_][] = formataNumero(0);
					}
				} else {
					foreach ($geradoDados as $key => $geradoDadosItem) {
						if ($key == 0) { continue; }
						@mysql_data_seek($resultDim2Ind, 0);
						$find = false;
						while ($rowDim2Ind = mysql_fetch_row($resultDim2Ind)) {
							if ($geradoDadosItem[-1] == $rowDim2Ind[0]) {
								// Total por linha
								$geradoDados[$key][] = formataNumero($rowDim2Ind[1]);
								$find = true;
							}
						}
						if (!$find) {
							$geradoDados[$key][] = formataNumero(0);
						}
					}
				}
			}
		}
		
		// Transforma os 0 os valores nulos (-)
		$count = 0;
		$countI = count($geradoDados[0]);
		foreach ($geradoDados as &$valor) {
			$count++;
			if ($count == 1) { continue; }
			for ($i = 0; $i < $countI; $i++) {
				if (!isset($valor[$i])) {
					//$valor[$i] = formataNumero(0);
					$valor[$i] = '-';
				}
			}
		}
		
		// Se for ordenação por Nome e tiver mais de uma métrica, precisamos ordenar manualmente aqui
		if ($r_numMetricas > 1 && ($r_dimensao1ord == 'ONA' || $r_dimensao1ord == 'OND')) {
			usort($geradoDados, function($a, $b) {
				global $r_dimensao1ord;
				if (empty($b[0]) ) {
					return 1;
				}
				if (empty($a[0]) ) {
					return -1;
				}
				if ($r_dimensao1ord == 'ONA') {
					return strcmp($a[1], $b[1]);
				} else {
					return strcmp($a[1], $b[1]) == 0 ? 0 : (strcmp($a[1], $b[1]) * -1);
				}
			});
		}
		$count = 0;
		foreach ($geradoDados as $key => $value) {
			if (!$count) { $count++; continue; }
			if ($r_mostrarCurvaABC != 'false') {
				$geradoDados[$key][1] = $count;
			} else {
				$geradoDados[$key][0] = $count;
			}
			$count++;
		}
		
		if ($r_mostrarCurvaABC != 'false') {
			$curvaABCTotal['A'][0] = number_format($curvaABCTotal['A'][0], 0, '', '.');
			$curvaABCTotal['A'][1] = number_format($curvaABCTotal['A'][1], 2, ',', '.');
			$curvaABCTotal['B'][0] = number_format($curvaABCTotal['B'][0], 0, '', '.');
			$curvaABCTotal['B'][1] = number_format($curvaABCTotal['B'][1], 2, ',', '.');
			$curvaABCTotal['C'][0] = number_format($curvaABCTotal['C'][0], 0, '', '.');
			$curvaABCTotal['C'][1] = number_format($curvaABCTotal['C'][1], 2, ',', '.');
			echo <<<EOT
				<div style="margin: 5px;"><span style="color: green; font-weight: bold;">A (80%)</span> : {$curvaABCTotal['A'][0]} - {$curvaABCTotal['A'][1]} &nbsp;&nbsp;|&nbsp;&nbsp; <span style="color: blue; font-weight: bold;">B (10%)</span> : {$curvaABCTotal['B'][0]} - {$curvaABCTotal['B'][1]} &nbsp;&nbsp;|&nbsp;&nbsp; <span style="color: var(--BGVermelho); font-weight: bold;">C (10%)</span> : {$curvaABCTotal['C'][0]} - {$curvaABCTotal['C'][1]}</div>
EOT;
		}
		
		/**
		* Gráfico - Organiza os valores para geração do gráfico no javascript
		*/
		$grafico = array('nrColunas' => 0, 'colunas' => array(), 'colunaTotal' => array(), 'colunaColunas' => array());
		foreach ($geradoDados as $key => $value) {
			$tmpKey = str_replace("'", '', strip_tags($value[1]));
			if ($key == 0) { continue; }
			// Números de colunas que existe neste grid
			if ($grafico['nrColunas'] == 0) {
				$grafico['nrColunas'] = count($value)-5;
			}
			// Coluna Total
			$colunaTotal = gCorrigeNumeroInverte(strip_tags($value[2]));
			$grafico['colunaTotal'][$tmpKey] = empty($colunaTotal) ? 0 : str_replace("'", '', $colunaTotal);
			// Todas as outras colunas
			foreach ($value as $colunaKey => $colunaValue) {
				if ($colunaKey <= 3) { continue; }
				$coluna = gCorrigeNumeroInverte(strip_tags($colunaValue));
				$grafico['colunaColunas'][$tmpKey][] = array(empty($coluna) ? 0 : str_replace("'", '', $coluna), str_replace("'", '', strip_tags($value[1])) . '/' . @$grid[$colunaKey]['colunaTitulo']);
			}
		}
		foreach ($grid as $key => $value) {
			if ($key <= 3) { continue; }
			$grafico['colunas'][] = $value['colunaTitulo'];
		}
		$grafico = json_encode($grafico, true);
		// acrescenta o gráfico para uso no javascript
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .gridOptTop').remove();
				$('#{$g['divId']}').data('grafico', '{$grafico}');
			} </script></div>
EOT;

		// não precisa total se tiver apenas uma coluna
		/*if ($pivotD2Count == 1) {
			unset($grid[2]);
		}*/
		if (!empty($ocultarSempreColunas_) || (!empty($ocultarColunasDashboard_) && $r_bi == 'visu' && (!isset($r_grid) || empty($r_grid)))) {
			$countGrid = count($grid);
			$i = 2;
			if ($r_mostrarCurvaABC != 'false') {
				$i = 3;
			}
			if (empty($ocultarPorcentagem_)) {
				$i = $i + (2 * $r_numMetricas);
			} else {
				$i = $i + (1 * $r_numMetricas);
			}
			for ($i; $i < $countGrid; $i++) {
				unset($grid[$i]);
			}
		}

		if ($r_exibir == 'grid') {
			/**
			* Geração: Grid
			*/
			$gridTabela = $g['ui']->grid($grid, $geradoDados);
			$g['smarty']->assign('thead', $gridTabela['thead']);
			$g['smarty']->assign('tbody', $gridTabela['tbody']);
			$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			$g['smarty']->assign('gridId', md5($g['r_']));
			$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
			$primeiraLinhaGridHead .= '</tr>';
			if ($r_numMetricas == 1 && (!empty($ocultarSempreColunas_) || (!empty($ocultarColunasDashboard_) && $r_bi == 'visu' && (!isset($r_grid) || empty($r_grid))))) {
				//
			} else {
				$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
			}
			if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		} else {
			/**
			* Geração dos Gráficos
			*/
			$incluirGraficoLabel_ = (empty($incluirGraficoLabel_)) ? 'false' : 'true';
			$incluirGraficoLegend_ = (empty($incluirGraficoLegend_)) ? 'false' : 'true';
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					/**
					* Monta o gráfico de barra
					*/
					function g{$g['divId']}graficobarra() {
						$('#grafico{$g['divId']}').html('');
						(function basic_bars(container, horizontal) {
						    var horizontal = horizontal ? true : false, dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
							grafico = JSON.parse($('#{$g['divId']}').data('grafico'));
							// tamanho de cada coluna
							variacao = 0.9 / grafico['nrColunas'];
							// Calcula xAxisTexto1 (para fazer o tickFormatter do xaxis)
							i = 0;
							$.each(grafico['colunaColunas'], function(index, value) {
								xAxisTexto1[i] = index;
								i++;
							});
						    // Adiciona os dados de cada coluna
						    for (colunas; colunas < grafico['nrColunas']; colunas++) {
					    		dim = [];
					    		i = 0;
							    $.each(grafico['colunaColunas'], function(index, value) {
				    				i++;
									point = [i + (variacao * colunas), parseFloat(value[colunas][0])];
									dim.push(point);
									//xAxisTexto1[i + (variacao * colunas)] = index;
									xAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
								});
								dims.push({data: dim, label: grafico['colunas'][colunas]});
							}
						    Flotr.draw(container, dims, {
						    	grid: {
						            outlineWidth: 0,
						        },
						        bars: {
						            show: true,
						            horizontal: horizontal,
						            shadowSize: 0,
						            barWidth: variacao
						        },
						        mouse: {
						            track: true,
						            relative: true,
						            trackFormatter: function(x) {
					            		return xAxisTexto2[x.nearest.x] + ' - ' + number_format(x.y, 2, ',', '.');
						            }
						        },
						        yaxis: {
						            min: 0,
						            autoscaleMargin: 1
						        },
						        xaxis: {
						        	showLabels: {$incluirGraficoLabel_},
						            noTicks: xAxisTexto1.length,
						            tickFormatter: function(x) {
						                var x = x.split('.');
						                if (parseInt(x[1]) == 0 ){ 
						                	return xAxisTexto1[parseInt(x[0])-1];
						                } else {
						                	return '';
						                }
						            }
						        },
						        legend: {
						        	show: {$incluirGraficoLegend_},
						            position: "ne",
						            backgroundColor: "#D2E8FF"
						        }
						    });
						})(document.getElementById("grafico{$g['divId']}"));
					}
					function g{$g['divId']}graficopizza() {
						$('#grafico{$g['divId']}').html('');
						(function basic_pie(container) {
							var data = [], dim, grafico, graph, i = 0, xAxisTexto = [];
							grafico = JSON.parse($('#{$g['divId']}').data('grafico'));
							$.each(grafico['colunaTotal'], function(index, value) {
								i++;
								dim = {
									data: [[i, parseFloat(value)]],
									label: index
								}
								data.push(dim);
								xAxisTexto.push(index);
							});
						    graph = Flotr.draw(container, data, {
						        HtmlText: false,
						        grid: {
						            verticalLines: false,
						            horizontalLines: false,
						            outlineWidth: 0,
						        },
						        xaxis: {
						            showLabels: false
						        },
						        yaxis: {
						            showLabels: false
						        },
						        pie: {
						        	showLabels: {$incluirGraficoLabel_},
						            show: true,
						            explode: 6,
						            shadowSize: 0,
						            labelFormatter : function (total, value) {
						            	if ('{$incluirGraficoLabel_}' == 'false') {
						            		return '';
						            	}
						            	var x = parseFloat((value*100)/total);
						            	if (x < 4) {
						            		return '';
						            	} else {
        									return x.toFixed(2) + '%';
        								}
      								}
						        },
						        mouse: {
						            track: true,
						            relative: true,
						            trackFormatter: function(x) {
					            		return xAxisTexto[(x.x - 1)] + ' - ' + number_format(x.y, 2, ',', '.');
						            }
						        },
						        legend: {
						        	show: {$incluirGraficoLegend_},
						            position: "ne",
						            backgroundColor: "#D2E8FF"
						        }
						    });
						})(document.getElementById("grafico{$g['divId']}"));
					}
					function g{$g['divId']}graficolinha() {
						$('#grafico{$g['divId']}').html('');
						(function basic_line(container) {
						    var dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
							grafico = JSON.parse($('#{$g['divId']}').data('grafico'));
							// variacao da linha (para poder identificar cada linha)
							variacao = 0.001;
							// Calcula xAxisTexto1 (para fazer o tickFormatter do xaxis)
							i = 0;
							$.each(grafico['colunaColunas'], function(index, value) {
								xAxisTexto1[i] = index;
								i++;
							});
						    // Adiciona os dados de cada coluna
						    for (colunas; colunas < grafico['nrColunas']; colunas++) {
					    		dim = [];
					    		i = 0;
							    $.each(grafico['colunaColunas'], function(index, value) {
				    				i++;
									point = [i + (variacao * colunas), parseFloat(value[colunas][0])];
									dim.push(point);
									//xAxisTexto1[i + (variacao * colunas)] = index;
									xAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
								});
								dims.push({data: dim, label: grafico['colunas'][colunas]});
							}
						    Flotr.draw(container, dims, {
						    	grid: {
						            outlineWidth: 0,
						        },
						        mouse: {
						            track: true,
						            relative: true,
						            trackFormatter: function(x) {
					            		return xAxisTexto2[x.nearest.x] + ' - ' + number_format(x.y, 2, ',', '.');
						            }
						        },
						        yaxis: {
						            min: 0,
						            autoscaleMargin: 1
						        },
						        xaxis: {
						        	showLabels: {$incluirGraficoLabel_},
						            noTicks: xAxisTexto1.length,
						            tickFormatter: function(x) {
						                var x = x.split('.');
						                if (parseInt(x[1]) == 0 ){ 
						                	return xAxisTexto1[parseInt(x[0])-1];
						                } else {
						                	return '';
						                }
						            }
						        },
						        lines: {
						            show: true,
						            shadowSize: 0,
						        },
						        points: {
						            show: false
						        },
						        legend: {
						        	show: {$incluirGraficoLegend_},
						            position: "ne",
						            backgroundColor: "#D2E8FF"
						        },
						    });
						})(document.getElementById("grafico{$g['divId']}"));
					}
					function g{$g['divId']}graficoarea() {
						$('#grafico{$g['divId']}').html('');
						(function basic_area(container) {
							var dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
							grafico = JSON.parse($('#{$g['divId']}').data('grafico'));
							// variacao da linha (para poder identificar cada linha)
							variacao = 0.001;
							// Calcula xAxisTexto1 (para fazer o tickFormatter do xaxis)
							i = 0;
							$.each(grafico['colunaColunas'], function(index, value) {
								xAxisTexto1[i] = index;
								i++;
							});
						    // Adiciona os dados de cada coluna
						    for (colunas; colunas < grafico['nrColunas']; colunas++) {
					    		dim = [];
					    		i = 0;
							    $.each(grafico['colunaColunas'], function(index, value) {
				    				i++;
									point = [i + (variacao * colunas), parseFloat(value[colunas][0])];
									dim.push(point);
									//xAxisTexto1[i + (variacao * colunas)] = index;
									xAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
								});
								dims.push({data: dim, label: grafico['colunas'][colunas]});
							}
						    Flotr.draw(container, dims, {
						    	grid: {
						            outlineWidth: 0,
						        },
						        mouse: {
						            track: true,
						            relative: true,
						            trackFormatter: function(x) {
					            		return xAxisTexto2[x.nearest.x] + ' - ' + number_format(x.y, 2, ',', '.');
						            }
						        },
						        yaxis: {
						            min: 0,
						            autoscaleMargin: 1
						        },
						        xaxis: {
						        	showLabels: {$incluirGraficoLabel_},
						            noTicks: xAxisTexto1.length,
						            tickFormatter: function(x) {
						                var x = x.split('.');
						                if (parseInt(x[1]) == 0 ){ 
						                	return xAxisTexto1[parseInt(x[0])-1];
						                } else {
						                	return '';
						                }
						            }
						        },
						        lines: {
						            fill: true,
						            show: true,
						            shadowSize: 0,
						        },
						        points: {
						            show: false
						        },
						        legend: {
						        	show: {$incluirGraficoLegend_},
						            position: "ne",
						            backgroundColor: "#D2E8FF"
						        }
						    });
						})(document.getElementById("grafico{$g['divId']}"));
					}
					function g{$g['divId']}graficoradar() {
						$('#grafico{$g['divId']}').html('');
						(function basic_area(container) {
							var dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
							grafico = JSON.parse($('#{$g['divId']}').data('grafico'));
							// variacao da linha (para poder identificar cada linha)
							variacao = 0.001;
							// Calcula xAxisTexto1 (para fazer o tickFormatter do xaxis)
							i = 0;
							$.each(grafico['colunaColunas'], function(index, value) {
								xAxisTexto1[i] = index;
								i++;
							});
						    // Adiciona os dados de cada coluna
						    for (colunas; colunas < grafico['nrColunas']; colunas++) {
					    		dim = [];
					    		i = 0;
							    $.each(grafico['colunaColunas'], function(index, value) {
				    				i++;
									point = [i + (variacao * colunas), parseFloat(value[colunas][0])];
									dim.push(point);
									//xAxisTexto1[i + (variacao * colunas)] = index;
									xAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
								});
								dims.push({data: dim, label: grafico['colunas'][colunas]});
							}
						    Flotr.draw(container, dims, {
						    	grid: {
						            outlineWidth: 0,
						        },
					    		radar: {
						            show: true,
						            shadowSize: 0,
						        },
						        grid: {
						            circular: true,
						            minorHorizontalLines: true
						        },
						        mouse: {
						            track: true,
						            relative: true,
						            trackFormatter: function(x) {
					            		return xAxisTexto2[x.nearest.x] + ' - ' + number_format(x.y, 2, ',', '.');
						            }
						        },
						        yaxis: {
									min: 0,
						            autoscaleMargin: 1
						        },
						        xaxis: {
						        	showLabels: {$incluirGraficoLabel_},
						            noTicks: xAxisTexto1.length,
						            tickFormatter: function(x) {
						                var x = x.split('.');
						                if (parseInt(x[1]) == 0 ){ 
						                	return xAxisTexto1[parseInt(x[0])-1];
						                } else {
						                	return '';
						                }
						            }
						        },
						        legend: {
						        	show: {$incluirGraficoLegend_},
					    			position: "ne",
					        		backgroundColor: "#D2E8FF"
						        }
						    });
						})(document.getElementById("grafico{$g['divId']}"));
					}
				} </script></div>
EOT;
			$funcao = 'grafico' . $r_exibir;
			if ($r_bi == 'visu') {
				echo <<<EOT
					<div id="grafico{$g['divId']}" style="margin: 5px; width: 100%; height: 100%;"></div>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						// corrige o tamanho do DIV para ocupar tudo
						$('#grafico{$g['divId']}')
							.width($('#{$g['divId']}').width() - 20)
							.height($('#{$g['divId']}').height() - 40);
						// chama o gráfico
						setTimeout(function() {
							g{$g['divId']}{$funcao}();
						}, 100);
					} </script></div>
EOT;
			} else {
				echo <<<EOT
					<div id="grafico{$g['divId']}" style="margin: 5px; width: 90%; height: 50%;"></div>
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						// chama o gráfico
						setTimeout(function() {
							g{$g['divId']}{$funcao}();
						}, 100);
					} </script></div>
EOT;
			}
		}

	break;
	
	case 'etc014GridSave':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D132_Id', $g['divId']), 'style' => 'width: 1%;', 'colunaExibir' => false),
			array('colunaValor' => 'D132_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'colunaExibir' => false),
			array('colunaValor' => 'D132_Descricao', 'colunaTitulo' => 'Descrição', 'style' => 'font-size: 1.5em;')
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D132';
		$extra = <<<EOT
			ORDER BY D132_Descricao
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D132_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(data.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D132_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc014/content/etc014ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent(id) + '&bi=visu', [1024,600]);
			});
		} </script></div>
EOT;
			
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->assign('gridOptNoShow', true);
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
}

function formataNumero($valor, $bold = false) {
	if ($bold == true) {
		$bold = ' font-weight: bold;';
	}
	if ($valor < 0) {
		return '<span style="color: var(--BGVermelho);'.$bold.'">' . number_format($valor, 2, ',', '.') . '</span>';
	} else if ($valor > 0) {
		return '<span style="color: blue;'.$bold.'">' . number_format($valor, 2, ',', '.') . '</span>';
	} else {
		return '<span style="color: gray;'.$bold.'">' . number_format($valor, 2, ',', '.') . '</span>';
	}
}

function formataDiaSemana($valor) {
	switch($valor) {
		case 1:
			$valor = 'Segunda-feira';
		break;
		case 2:
			$valor = 'Terça-feira';
		break;
		case 3:
			$valor = 'Quarta-feira';
		break;
		case 4:
			$valor = 'Quinta-feira';
		break;
		case 5:
			$valor = 'Sexta-feira';
		break;
		case 6:
			$valor = 'Sábado';
		break;
		case 7:
			$valor = 'Domingo';
		break;
	}
	return $valor;
}

function formataDiaMes($valor) {
	switch($valor) {
		case 1:
			$valor = 'Janeiro';
		break;
		case 2:
			$valor = 'Fevereiro';
		break;
		case 3:
			$valor = 'Março';
		break;
		case 4:
			$valor = 'Abril';
		break;
		case 5:
			$valor = 'Maio';
		break;
		case 6:
			$valor = 'Junho';
		break;
		case 7:
			$valor = 'Julho';
		break;
		case 8:
			$valor = 'Agosto';
		break;
		case 9:
			$valor = 'Setembro';
		break;
		case 10:
			$valor = 'Outubro';
		break;
		case 11:
			$valor = 'Novembro';
		break;
		case 12:
			$valor = 'Dezembro';
		break;
	}
	return $valor;
}
?>
