<?php
namespace hardness;
$g['usarClickHouse'] = true;
ini_set('mysqli.reconnect', '1');
set_time_limit(0);
$debug = false;
$arrayJoins = array(
	'DIM001' => 'LEFT JOIN DIM001 					ON FAC001_DIM001_Id = DIM001_Id ',
	'DIM002' => 'LEFT JOIN DIM002 					ON FAC001_DIM002_Id = DIM002_Id ',
	'DIM003' => 'LEFT JOIN DIM003 					ON FAC001_DIM003_Id = DIM003_Id ',
	'DIM004' => 'LEFT JOIN DIM004 D4Vendedor 		ON FAC001_DIM004_Id_Vendedor = D4Vendedor.DIM004_Id     LEFT JOIN DIM004 D4Supervisor ON FAC001_DIM004_Id_Supervisor = D4Supervisor.DIM004_Id ',
	'DIM005' => 'LEFT JOIN DIM005 					ON FAC001_DIM005_Id = DIM005_Id ',
	'DIM006' => 'LEFT JOIN DIM006 					ON FAC001_DIM006_Id = DIM006_Id ',
	'DIM007' => 'LEFT JOIN DIM007 					ON FAC001_DIM007_Id = DIM007_Id ',
	'DIM008' => 'LEFT JOIN DIM008 					ON FAC001_DIM008_Id = DIM008_Id ',
	'DIM009' => 'LEFT JOIN DIM009 					ON FAC001_DIM009_Id = DIM009_Id ',
	'DIM010' => 'LEFT JOIN DIM010 					ON FAC001_DIM010_Id = DIM010_Id ',
	'DIM011' => 'LEFT JOIN DIM011 					ON FAC001_DIM011_Id = DIM011_Id ',
	'DIM012' => 'LEFT JOIN DIM012 					ON FAC001_DIM012_Id = DIM012_Id ',
	'DIM013' => 'LEFT JOIN DIM013 					ON FAC001_DIM013_Id = DIM013_Id ',
);

switch ($g['r'][3]) {
	case 'etc011GridPivot':

		$limit = false;
		if (!$r_generateCSV && !$r_generateHTML) {
			$limit = true;
		} else if ($r_generateCSV) {
			mysql_set_charset('latin1');
		}

		if ($debug) { $time_start = microtime(true); }
		ob_end_clean();
		ob_start();
		// Vem do GRID (Relatório Salvo)
		$primeiraLinhaGridHead = '<tr>';
		$filtroSalvo = false;
		$md5 = md5($g['r_']);
		$modificado = false;

		// preenchido pelo gridFiltroExtra(), caso a dimensão principal for igual ao filtro
        // nestes casos o filtro não entra no extra e o registro é colocado em negrito
		$filtroGlobal = false;

        /**
         * Função que define classes CSS para cada linha do grid
         */
        function gridLinha($array) {
            global $filtroGlobal, $dimensoes, $r_dimensao1;

            $dimensao = $dimensoes[$r_dimensao1][0];
            $valor = $array['todosCampos']['dim01'];
            if (preg_match('/Linhas.*Colunas/', $valor)) { return ''; }
            if (!$filtroGlobal) { return 'linhaCursorPointer'; }
            $valorFiltrado = $filtroGlobal;
            $valorFiltrado = preg_replace('/^=/', '', $valorFiltrado);

            if (preg_match('/_Dia_Da_Semana$/', $dimensao)) {
                $valor = formataDiaSemanaReverso($valor);
            }
            if (preg_match('/_Mes$/', $dimensao)) {
                $valor = formataMesReverso($valor);
            }

//            var_dump($valor);
//            var_dump($valorFiltrado);
//            var_dump($dimensao);
//            echo "\n";

            if ($valor == $valorFiltrado) {
                return 'linhaNegrito linhaCursorPointer';
            }

            return 'linhaCursorPointer';
        }

        $g['pularAdicionarColuna'] = true;

		if (isset($r_D132_Id) && !empty($r_D132_Id)) {
            $g['usarClickHouse'] = false;
            $r_D132_Id = mysql_real_escape_string($r_D132_Id);
			$sql = <<<EOT
SELECT D132_Descricao, D132_Salvo, D132_Filtro, D182_DivId, D182_Grid_Salvo
FROM D132
LEFT JOIN D182 ON D182_D132_Id = D132_Id AND D182_C007_Id = '{$g['usuarioAtual']}'
WHERE D132_Id = '{$r_D132_Id}'
EOT;
			$resultado = mysql_query($sql);
            $row = mysql_fetch_array($resultado);
            $g['usarClickHouse'] = true;

			//Carregando o grid do usuário
			$gridSalvoUsuario = "{$g['pathDados']}personalizacaoGrid/relatorioBI_{$r_D132_Id}-{$g['usuarioAtual']}";
			if(file_exists($gridSalvoUsuario)){
				$row['D182_Grid_Salvo'] = file_get_contents($gridSalvoUsuario);
			}
			$dadosSalvos = unserialize($row['D132_Salvo']);

			if(!isset($r_gridFiltrado) || (isset($r_gridFiltrado) && $r_bi == 'visu')){
				foreach ($dadosSalvos as $key => $value) {
					$var = 'r_' . $key;
					$$var = $value;
				}
				$r_mostrarCurvaABC = isset($dadosSalvos['mostrarCurvaABC']) ? $dadosSalvos['mostrarCurvaABC'] : 'true';
				$r_ocultarColunasDashboard = isset($dadosSalvos['ocultarColunasDashboard']) ? $dadosSalvos['ocultarColunasDashboard'] : 'true';
				$r_ocultarSempreColunas = isset($dadosSalvos['ocultarSempreColunas']) ? $dadosSalvos['ocultarSempreColunas'] : 'false';
				$r_ocultarPorcentagem = isset($dadosSalvos['ocultarPorcentagem']) ? $dadosSalvos['ocultarPorcentagem'] : 'false';
				$r_ocultarTotais = isset($dadosSalvos['ocultarTotais']) ? $dadosSalvos['ocultarTotais'] : 'false';
				$r_exibir = isset($dadosSalvos['exibir']) ? $dadosSalvos['exibir'] : 'true';
                $r_numMetricas = isset($dadosSalvos['numMetricas']) ? $dadosSalvos['numMetricas'] : 1;

			}
			$D132_Descricao = $row['D132_Descricao'];
			
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
			}
			if (!isset($r_gridFiltrado)) {
				$filtroSalvo = unserialize($row['D132_Filtro']);
				$dir = $g['pathDadosUsuario'] . 'gridFiltro/';
				$file = $dir . $md5;
				if (file_exists($file)) { unlink($file); }
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
				var mudarVariavel_Filtro_D132_Id = 'N';
				//$('#{$g['divId']} .grid').css('width', '1%');
				// Desabilita ordenação padrão do grid
				$('#{$g['divId']} th').removeAttr('onclick').css('cursor', 'default');
				$('#{$g['divId']} tr:not(.linhaCursorPointer)').css('cursor', 'auto');
				// Corrige as colunas COLSPAN="2"
				$('#{$g['divId']} th.extraTHGhost').remove();
			} </script></div>
EOT;
		
		// No modo 'visualização' não é permitido salvar
		if ($r_bi == 'visu') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#{$g['divId']} .gridFiltroBotoes:eq(0) button:eq(0)').click(function(data) {
						if(mudarVariavel_Filtro_D132_Id == 'S'){
							divSetarAcaoId('{$g['divId']}', '0', 'Filtro_D132_Id');
						} else {
							divSetarAcaoId('{$g['divId']}', '1', 'Filtro_D132_Id');
						}
						mudarVariavel_Filtro_D132_Id = 'N';
						return false;
					});
				} </script></div>
EOT;
			if($r_gridFiltrado && empty($r_loading)){
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('<!-- INICIO-BTNSALVAR --><input type="submit" value="Salvar"><!-- FIM-BTNSALVAR --->').click(function(data) {
							mudarVariavel_Filtro_D132_Id = 'S';
							divSetarAcaoId('{$g['divId']}', '1', 'FiltroUsuario');
							$('#{$g['divId']} .gridFiltroBotoes:eq(0) button:eq(0)').trigger('click');
							return false;
						}).appendTo('#{$g['divId']} .gridFiltroBotoes:eq(0)');
					} </script></div>
EOT;
			}
		}

		// Definições das dimensões e fatos
		$dimensoes = array(
			//'Data', 
			'DDDMS' => array('DIM001_Data', 'Dia/Mes/Ano'),
			'DDMA' => array("CONCAT(DIM001_Mes, '/', DIM001_Ano)", 'Mes/Ano'),
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
			//'Empresa'
			'DIM012_Nome_Abreviado' => array('DIM012_Nome_Abreviado', 'Empresa'),
			//'Banco'
			'DIM013_Nome_Caixa' => array('DIM013_Nome_Caixa', 'Caixa'),
			'DIM013_Nome_Banco' => array('DIM013_Nome_Banco', 'Banco'),
		);
		$fatos2 = array(
			'ORCA' => array('ORCA', 'Orçamento'),
			'PEDI' => array('PEDI', 'Pedido'),
			'NOTAFISC' => array('NOTAFISC', 'NF Emitida'),
			'NOTAFISCR' => array('NOTAFISCR', 'NF Recebida'),
			'CONTRECE' => array('CONTRECE', 'A Receber'),
			'CONTPAGA' => array('CONTPAGA', 'A Pagar'),
			'LANCBANC' => array('LANCBANC', 'Banco'),
			//'LANCBANCCRED' => array('LANCBANCCRED', 'Recebido'),
			//'LANCBANCDEB' => array('LANCBANCDEB', 'Pago'),
			'COT' => array('COT', 'Cotações'),
			'ORDCOMP' => array('ORDCOMP', 'O.C.'),
			'PROJ' => array('PROJ', 'Projeção'),
			'HIST' => array('HIST', 'Estoque'),
		);
		$fatos = array(
			'FAC001_Orcamento_Valor_Total_Preco' => array('FAC001_Orcamento_Valor_Total_Preco', 'Valor Total'),
			'FAC001_Pedidos_Valor_Total_Preco' => array('FAC001_Pedidos_Valor_Total_Preco', 'Valor Total'),
			'FAC001_CR_Valor_Duplicata' => array('FAC001_CR_Valor_Duplicata', 'Valor Duplicata'),
			'FAC001_CP_Valor_Duplicata' => array('FAC001_CP_Valor_Duplicata', 'Valor Duplicata'),
			'FAC001_LB_Valor_Lancamento' => array('FAC001_LB_Valor_Lancamento', 'Valor Lancamento'),
			'FAC001_LB_Valor_Saldo_Inicial' => array('FAC001_LB_Valor_Saldo_Inicial', 'Valor Saldo Inicial'),
			'FAC001_LB_Valor_Saldo' => array('FAC001_LB_Valor_Saldo', 'Valor Saldo Final'),
			'FAC001_LB_Valor_Lancamento_Pago' => array('FAC001_LB_Valor_Lancamento_Pago', 'Valor Pago'),
			'FAC001_LB_Valor_Lancamento_Recebido' => array('FAC001_LB_Valor_Lancamento_Recebido', 'Valor Recebido'),
			// NF
			'FAC001_NF_Valor_Total_Preco' => array('FAC001_NF_Valor_Total_Preco', 'Valor Produtos'),
			'FAC001_NF_Produtos_Quantidade_Itens' => array('FAC001_NF_Produtos_Quantidade_Itens', 'Qtd Itens'),
			'FAC001_NF_Produtos_Quantidade' => array('FAC001_NF_Produtos_Quantidade', 'Qtd Produtos'),
			'FAC001_NF_Quantidade' => array('FAC001_NF_Quantidade', 'Qtd'),
			'FAC001_NF_ICMS' => array('FAC001_NF_ICMS', 'ICMS'),
			'FAC001_NF_ICMS_ST' => array('FAC001_NF_ICMS_ST', 'ICMS ST'),
			'FAC001_NF_IPI' => array('FAC001_NF_IPI', 'IPI'),
			'FAC001_NF_PIS' => array('FAC001_NF_PIS', 'PIS'),
			'FAC001_NF_COFINS' => array('FAC001_NF_COFINS', 'COFINS'),
			// NFR
			'FAC001_NFR_Valor_Total_Custo' => array('FAC001_NFR_Valor_Total_Custo', 'Valor Produtos'),
			'FAC001_NFR_Produtos_Quantidade_Itens' => array('FAC001_NFR_Produtos_Quantidade_Itens', 'Qtd Itens'),
			'FAC001_NFR_Produtos_Quantidade' => array('FAC001_NFR_Produtos_Quantidade', 'Qtd Produtos'),
			'FAC001_NFR_Quantidade' => array('FAC001_NFR_Quantidade', 'Qtd'),
			'FAC001_NFR_ICMS' => array('FAC001_NFR_ICMS', 'ICMS'),
			'FAC001_NFR_ICMS_ST' => array('FAC001_NFR_ICMS_ST', 'ICMS ST'),
			'FAC001_NFR_IPI' => array('FAC001_NFR_IPI', 'IPI'),
			'FAC001_NFR_PIS' => array('FAC001_NFR_PIS', 'PIS'),
			'FAC001_NFR_COFINS' => array('FAC001_NFR_COFINS', 'COFINS'),
			// COT
			'FAC001_COT_Valor_Total_Preco' => array('FAC001_COT_Valor_Total_Preco', 'Valor Total'),
			'FAC001_COT_Produtos_Quantidade_Itens' => array('FAC001_COT_Produtos_Quantidade_Itens', 'Qtd Itens'),
			'FAC001_COT_Produtos_Quantidade' => array('FAC001_COT_Produtos_Quantidade', 'Qtd Produtos'),
			'FAC001_COT_Quantidade' => array('FAC001_COT_Quantidade', 'Qtd'),
			// ORDCOMP
			'FAC001_ORDCOMP_Valor_Total_Preco' => array('FAC001_ORDCOMP_Valor_Total_Preco', 'Valor Total'),
			'FAC001_ORDCOMP_Produtos_Quantidade_Itens' => array('FAC001_ORDCOMP_Produtos_Quantidade_Itens', 'Qtd Itens'),
			'FAC001_ORDCOMP_Produtos_Quantidade' => array('FAC001_ORDCOMP_Produtos_Quantidade', 'Qtd Produtos'),
			'FAC001_ORDCOMP_Quantidade' => array('FAC001_ORDCOMP_Quantidade', 'Qtd'),
			// PROJ
			'FAC001_PROJ_Valor_Projetado' => array('FAC001_PROJ_Valor_Projetado', 'Valor Projetado'),
			'FAC001_PROJ_Valor_Saldo' => array('FAC001_PROJ_Valor_Saldo', 'Valor Saldo'),
			// HIST
			'FAC001_HIST_Quantidade_Entrada' => array('FAC001_HIST_Quantidade_Entrada', 'Qtd Entrada'),
			'FAC001_HIST_Quantidade_Saida' => array('FAC001_HIST_Quantidade_Saida', 'Qtd Saída'),
			'FAC001_HIST_Quantidade_Saldo_Inicial' => array('FAC001_HIST_Quantidade_Saldo_Inicial', 'Qtd Saldo Inicial'),
			'FAC001_HIST_Quantidade_Saldo' => array('FAC001_HIST_Quantidade_Saldo', 'Qtd Saldo Final'),
			'FAC001_HIST_Quantidade' => array('FAC001_HIST_Quantidade', 'Quantidade'),
			'FAC001_HIST_Valor_Entrada' => array('FAC001_HIST_Valor_Entrada', 'Valor Entrada'),
			'FAC001_HIST_Valor_Saida' => array('FAC001_HIST_Valor_Saida', 'Valor Saída'),
			'FAC001_HIST_Valor_Saldo_Inicial' => array('FAC001_HIST_Valor_Saldo_Inicial', 'Valor Saldo Inicial'),
			'FAC001_HIST_Valor_Saldo' => array('FAC001_HIST_Valor_Saldo', 'Valor Saldo Final'),
		);
		$medidas = array(
			'MS' => array('SUM', 'Soma'),
			'MM' => array('AVG', 'Média'),
			'SD' => array('', 'Saldo')
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
			//'Notas Fiscais Recebidas',
			'T013_Data_Emissao' => array('DIM001', 'Data Emissao'),
			'T013_Data_Entrada' => array('DIM005', 'Data Entrada'),
			//'Cotações'
			'T218_Data_Emissao' => array('DIM001', 'Data Emissao'),
			//'Ordens de Compras'
			'T224_Data_Emissao' => array('DIM001', 'Data Emissao'),
			//'Projeção'
			'T018_Data_Vencimento' => array('DIM005', 'Data Vencimento'),
			//'Historico'
			'T001_Data_Lancamento' => array('DIM001', 'Data Lançamento'),
		);
		
		$dimensoesFiltros = array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM005', 'DIM006', 'DIM007', 'DIM008', 'DIM009', 'DIM010', 'DIM011', 'DIM012', 'DIM013', 'FAC001_FLAG_CANCELADO');
		$fatos2Filtros = array(
			'ORCA' => array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM009', 'DIM010', 'DIM011', 'DIM012', 'FAC001_FLAG_CANCELADO'),
			'PEDI' => array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM005', 'DIM006', 'DIM009', 'DIM010', 'DIM011', 'DIM012', 'FAC001_FLAG_CANCELADO'),
			'NOTAFISC' => array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM005', 'DIM006', 'DIM008', 'DIM009', 'DIM010', 'DIM011', 'DIM012', 'FAC001_FLAG_CANCELADO'),
			'NOTAFISCR' => array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM005', 'DIM008', 'DIM009', 'DIM010', 'DIM011', 'DIM012', 'FAC001_FLAG_CANCELADO'),
			'CONTRECE' => array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM005', 'DIM006', 'DIM007', 'DIM011', 'DIM012', 'FAC001_FLAG_CANCELADO'),
			'CONTPAGA' => array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM005', 'DIM006', 'DIM007', 'DIM011', 'DIM012', 'FAC001_FLAG_CANCELADO'),
			'LANCBANC' => array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM007', 'DIM012', 'DIM013', 'FAC001_FLAG_CANCELADO'),
			'COT' => array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM008', 'DIM009', 'DIM010', 'DIM011', 'DIM012', 'FAC001_FLAG_CANCELADO'),
			'ORDCOMP' => array('DIM001', 'DIM002', 'DIM003', 'D4Vendedor', 'D4Supervisor', 'DIM008', 'DIM009', 'DIM010', 'DIM011', 'DIM012', 'FAC001_FLAG_CANCELADO'),
			'PROJ' => array('DIM002', 'DIM003', 'DIM005', 'DIM007', 'DIM012', 'FAC001_FLAG_CANCELADO'),
			'HIST' => array('DIM001', 'DIM002', 'DIM003', 'DIM009', 'DIM010', 'DIM012', 'FAC001_FLAG_CANCELADO'),
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
		if (isset($r_ocultarTotais)) {
			$ocultarTotais_ = $r_ocultarTotais != 'false' ? ' checked="checked"' : '';
		} else {
			$ocultarTotais_ = '';
		}

		// Ignorar dimensão: não filtra, mostra em negrito
        $ignorarDimensao = $dimensoes[$r_dimensao1][0];
        if ($ignorarDimensao == 'DIM002_Nome_Empresa' || $ignorarDimensao == 'DIM002_Nome_Fantasia') {
            $ignorarDimensao = 'DIM002_Nome_Empresa, DIM002_Nome_Fantasia';
        }
		
		//Loop para determinar quais filtros serão exibidos
		$gridFiltroExibir = array();
		for ($i = 0; $i < $r_numMetricas; $i++) {
			$id = ($i == 0) ? '' : 'a' . $i;
			$fato2 = 'fato2' . $id;
			$fato2_ = 'r_' . $fato2;
			$fato2_ = isset($$fato2_) ? $$fato2_ : false;
			
			$gridFiltroExibir = array_merge($gridFiltroExibir, $fatos2Filtros[$fato2_]);
		}

		if ($r_bi == 'conf') {
		/**
		* Opções extras de filtragem
		*/
		echo <<<EOT
			<style>
				.entidade {
					min-width: 560px;
					float: left;
				}
			</style>
			<div class="buttonsBottomTop gridFiltro ui-corner-all">
EOT;

		// Várias Métricas
		$divSetarMetricas = '';
		$triggersMetricas = '';
		$paramMetricas = '';
		$corrigeComboValor = '';
		$camposDataLeftJoin = array();
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

			$camposDataLeftJoin[] = $tipodata_;

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
						$('#{$fato} .opt[value="FAC001_NF_Quantidade"]').show();
						$('#{$fato} .opt[value="FAC001_NF_ICMS"]').show();
						$('#{$fato} .opt[value="FAC001_NF_ICMS_ST"]').show();
						$('#{$fato} .opt[value="FAC001_NF_IPI"]').show();
						$('#{$fato} .opt[value="FAC001_NF_PIS"]').show();
						$('#{$fato} .opt[value="FAC001_NF_COFINS"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_NF_Valor_Total_Preco'); }
					}
					if (value == 'NOTAFISCR') {
						$('#{$fato} .opt[value="FAC001_NFR_Valor_Total_Custo"]').show();
						$('#{$fato} .opt[value="FAC001_NFR_Produtos_Quantidade_Itens"]').show();
						$('#{$fato} .opt[value="FAC001_NFR_Produtos_Quantidade"]').show();
						$('#{$fato} .opt[value="FAC001_NFR_Quantidade"]').show();
						$('#{$fato} .opt[value="FAC001_NFR_ICMS"]').show();
						$('#{$fato} .opt[value="FAC001_NFR_ICMS_ST"]').show();
						$('#{$fato} .opt[value="FAC001_NFR_IPI"]').show();
						$('#{$fato} .opt[value="FAC001_NFR_PIS"]').show();
						$('#{$fato} .opt[value="FAC001_NFR_COFINS"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_NFR_Valor_Total_Custo'); }
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
						$('#{$fato} .opt[value="FAC001_LB_Valor_Lancamento_Recebido"]').show();
						$('#{$fato} .opt[value="FAC001_LB_Valor_Lancamento_Pago"]').show();
						$('#{$fato} .opt[value="FAC001_LB_Valor_Saldo_Inicial"]').show();
						$('#{$fato} .opt[value="FAC001_LB_Valor_Saldo"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_LB_Valor_Lancamento_Recebido'); }
					}
					if (value == 'COT') {
						$('#{$fato} .opt[value="FAC001_COT_Valor_Total_Preco"]').show();
						$('#{$fato} .opt[value="FAC001_COT_Produtos_Quantidade_Itens"]').show();
						$('#{$fato} .opt[value="FAC001_COT_Produtos_Quantidade"]').show();
						$('#{$fato} .opt[value="FAC001_COT_Quantidade"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_COT_Valor_Total_Preco'); }
					}
					if (value == 'ORDCOMP') {
						$('#{$fato} .opt[value="FAC001_ORDCOMP_Valor_Total_Preco"]').show();
						$('#{$fato} .opt[value="FAC001_ORDCOMP_Produtos_Quantidade_Itens"]').show();
						$('#{$fato} .opt[value="FAC001_ORDCOMP_Produtos_Quantidade"]').show();
						$('#{$fato} .opt[value="FAC001_ORDCOMP_Quantidade"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_ORDCOMP_Valor_Total_Preco'); }
					}
					if (value == 'PROJ') {
						$('#{$fato} .opt[value="FAC001_PROJ_Valor_Projetado"]').show();
						$('#{$fato} .opt[value="FAC001_PROJ_Valor_Saldo"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_PROJ_Valor_Projetado'); }
					}
					if (value == 'HIST') {
						$('#{$fato} .opt[value="FAC001_HIST_Quantidade_Entrada"]').show();
						$('#{$fato} .opt[value="FAC001_HIST_Quantidade_Saida"]').show();
						$('#{$fato} .opt[value="FAC001_HIST_Quantidade_Saldo"]').show();
						$('#{$fato} .opt[value="FAC001_HIST_Quantidade_Saldo_Inicial"]').show();
						$('#{$fato} .opt[value="FAC001_HIST_Quantidade"]').show();
						$('#{$fato} .opt[value="FAC001_HIST_Valor_Entrada"]').show();
						$('#{$fato} .opt[value="FAC001_HIST_Valor_Saida"]').show();
						$('#{$fato} .opt[value="FAC001_HIST_Valor_Saldo"]').show();
						$('#{$fato} .opt[value="FAC001_HIST_Valor_Saldo_Inicial"]').show();
						if (!primeiraVez{$fato2}) { $('#{$fato}').val('FAC001_HIST_Quantidade'); }
					}
					
					if (!primeiraVez{$fato2}) { $('#{$g['divId']} .gridFiltroBotoes:eq(0) button:eq(0)').trigger('click'); }

					$('#{$fato}').trigger('change');
					primeiraVez{$fato2} = false;
				});
				$('#{$fato}').change(function() {
					var value = $(this).val();
					$('#dimensao1 .opt').removeAttr('disabled');
					$('#dimensao2 .opt').removeAttr('disabled');
					$('#{$tipodata} .opt').hide();
					
					//divSetarAcaoId('{$g['divId']}', value, '{$fato}');
					$('#{$medida} .opt[value="SD"]').hide();
					if(value != 'FAC001_HIST_Quantidade_Saldo' && value != 'FAC001_HIST_Quantidade_Saldo_Inicial' && value != 'FAC001_LB_Valor_Saldo' && value != 'FAC001_LB_Valor_Saldo_Inicial' && value != 'FAC001_HIST_Valor_Saldo' && value != 'FAC001_HIST_Valor_Saldo_Inicial'){
						$('#{$medida}').val('MS');
					}

					if (value == 'FAC001_Orcamento_Valor_Total_Preco') {
						/*$('#dimensao1 .opt[value="DIM007_Id"]').attr('disabled', 'disabled');
						$('#dimensao2 .opt[value="DIM007_Id"]').attr('disabled', 'disabled');*/
						$('#{$tipodata} .opt[value="T003_Data_Emissao"]').show();
						$('#{$tipodata}').val('T003_Data_Emissao');
						var string = '{$tipodata_}';
						if('{$tipodata_}' != '' && string.substring(0, 4) == 'T003'){
							$('#{$tipodata}').val(string);
						}
					}
					if (value == 'FAC001_Pedidos_Valor_Total_Preco') {
						$('#{$tipodata} .opt[value="T005_Data_Emissao"]').show();
						$('#{$tipodata} .opt[value="T005_Data_Saida"]').show();
						$('#{$tipodata} .opt[value="T005_Data_Entrega"]').show();
						$('#{$tipodata}').val('T005_Data_Emissao');
						var string = '{$tipodata_}';
						if('{$tipodata_}' != '' && string.substring(0, 4) == 'T005'){
							$('#{$tipodata}').val(string);
						}
					}
					if (value == 'FAC001_NF_Quantidade' || value == 'FAC001_NF_Valor_Total_Preco' || value == 'FAC001_NF_Produtos_Quantidade_Itens' || value == 'FAC001_NF_Produtos_Quantidade' || value == 'FAC001_NF_ICMS') {
						$('#{$tipodata} .opt[value="T007_Data_Emissao"]').show();
						$('#{$tipodata} .opt[value="T007_Data_Saida"]').show();
						$('#{$tipodata} .opt[value="T007_Data_Vencimento"]').show();
						$('#{$tipodata}').val('T007_Data_Emissao');
						var string = '{$tipodata_}';
						if('{$tipodata_}' != '' && string.substring(0, 4) == 'T007'){
							$('#{$tipodata}').val(string);
						}
					}
					if (value == 'FAC001_NFR_Quantidade' || value == 'FAC001_NFR_Valor_Total_Custo' || value == 'FAC001_NFR_Produtos_Quantidade_Itens' || value == 'FAC001_NFR_Produtos_Quantidade' || value == 'FAC001_NFR_ICMS' || value == 'FAC001_NFR_ICMS_ST' || value == 'FAC001_NFR_IPI' || value == 'FAC001_NFR_PIS' || value == 'FAC001_NFR_COFINS') {
						$('#{$tipodata} .opt[value="T013_Data_Emissao"]').show();
						$('#{$tipodata} .opt[value="T013_Data_Entrada"]').show();
						$('#{$tipodata}').val('T013_Data_Emissao');
						var string = '{$tipodata_}';
						if('{$tipodata_}' != '' && string.substring(0, 4) == 'T013'){
							$('#{$tipodata}').val(string);
						}
					}
					if (value == 'FAC001_CR_Valor_Duplicata') {
						$('#{$tipodata} .opt[value="T002_Data_Vencimento"]').show();
						$('#{$tipodata} .opt[value="T002_Data_Emissao"]').show();
						$('#{$tipodata} .opt[value="T002_Data_Recebimento"]').show();
						$('#{$tipodata}').val('T002_Data_Vencimento');
						var string = '{$tipodata_}';
						if('{$tipodata_}' != '' && string.substring(0, 4) == 'T002'){
							$('#{$tipodata}').val(string);
						}
					}
					if (value == 'FAC001_CP_Valor_Duplicata') {
						$('#{$tipodata} .opt[value="T015_Data_Vencimento"]').show();
						$('#{$tipodata} .opt[value="T015_Data_Emissao"]').show();
						$('#{$tipodata} .opt[value="T015_Data_Pagamento"]').show();
						$('#{$tipodata}').val('T015_Data_Vencimento');
						var string = '{$tipodata_}';
						if('{$tipodata_}' != '' && string.substring(0, 4) == 'T015'){
							$('#{$tipodata}').val(string);
						}
					}
					if (value == 'FAC001_LB_Valor_Lancamento' || value == 'FAC001_LB_Valor_Lancamento_Pago' || value == 'FAC001_LB_Valor_Lancamento_Recebido' || value == 'FAC001_LB_Valor_Saldo' || value == 'FAC001_LB_Valor_Saldo_Inicial') {
						$('#{$tipodata} .opt[value="T019_Data_Lancamento"]').show();
						$('#{$tipodata}').val('T019_Data_Lancamento');
						if(value == 'FAC001_LB_Valor_Saldo' || value == 'FAC001_LB_Valor_Saldo_Inicial'){
							$('#{$medida} .opt[value="SD"]').show();
							$('#{$medida} .opt[value="MS"]').hide();
							$('#{$medida} .opt[value="MM"]').hide();
							$('#{$medida}').val('SD');
						}
					}
					if (value == 'FAC001_COT_Quantidade' || value == 'FAC001_COT_Valor_Total_Preco' || value == 'FAC001_COT_Produtos_Quantidade_Itens' || value == 'FAC001_COT_Produtos_Quantidade') {
						$('#{$tipodata} .opt[value="T218_Data_Emissao"]').show();
						$('#{$tipodata}').val('T218_Data_Emissao');
					}
					if (value == 'FAC001_ORDCOMP_Quantidade' || value == 'FAC001_ORDCOMP_Valor_Total_Preco' || value == 'FAC001_ORDCOMP_Produtos_Quantidade_Itens' || value == 'FAC001_ORDCOMP_Produtos_Quantidade') {
						$('#{$tipodata} .opt[value="T224_Data_Emissao"]').show();
						$('#{$tipodata}').val('T224_Data_Emissao');
					}
					if (value == 'FAC001_PROJ_Valor_Projetado' || value == 'FAC001_PROJ_Valor_Saldo') {
						$('#{$tipodata} .opt[value="T018_Data_Vencimento"]').show();
						$('#{$tipodata}').val('T018_Data_Vencimento');
					}
					if (value == 'FAC001_HIST_Valor_Saldo' || value == 'FAC001_HIST_Valor_Saldo_Inicial' || value == 'FAC001_HIST_Quantidade_Saldo_Inicial' || value == 'FAC001_HIST_Valor_Entrada' || value == 'FAC001_HIST_Valor_Saida' || value == 'FAC001_HIST_Quantidade_Entrada' || value == 'FAC001_HIST_Quantidade_Saida' || value == 'FAC001_HIST_Quantidade_Saldo' || value == 'FAC001_HIST_Quantidade') {
						$('#{$tipodata} .opt[value="T001_Data_Lancamento"]').show();
						$('#{$tipodata}').val('T001_Data_Lancamento');
						if(value == 'FAC001_HIST_Quantidade_Saldo' || value == 'FAC001_HIST_Quantidade_Saldo_Inicial' || value == 'FAC001_HIST_Valor_Saldo' || value == 'FAC001_HIST_Valor_Saldo_Inicial'){
							$('#{$medida} .opt[value="SD"]').show();
							$('#{$medida} .opt[value="MS"]').hide();
							$('#{$medida} .opt[value="MM"]').hide();
							$('#{$medida}').val('SD');
						}
					}
				});
				$('#{$fato2}').trigger('change');
EOT;
			echo <<<EOT
				<div class="entidade">
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
					<span class="item">
						Valor
						<br>
						<select id="{$fato}" class="formInput ui-state-default ui-corner-all" style="width: 160px;">
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
					<span class="item">
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
					<span class="item">
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
					<input id="maisNumMetricas" type="submit" title="Adicionar mais Métricas" style="background-position: -17px -130px; margin: 16px 0 0 2px; cursor: pointer;" class="item ui-icon ui-icon-help ui-state-default ui-corner-all" value="">
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						//$(this).closest('.buttonsBottomTop').find('.clearMetrica');
						$('#maisNumMetricas').click(function() {
							divSetarAcaoId('{$g['divId']}', {$r_numMetricas} + 1, 'numMetricas');
							$('#{$g['divId']} .gridFiltroBotoes:eq(0) button:eq(0)').trigger('click');
						});
					} </script></div>
EOT;
			} else if (($r_numMetricas -1) == $i) {
				echo <<<EOT
					<input id="menosNumMetricas" type="submit" title="Remover Métricas" style="background-position: -49px -130px; margin: 16px 0 0 2px; cursor: pointer;" class="item ui-icon ui-icon-help ui-state-default ui-corner-all" value="">
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						//$(this).closest('.buttonsBottomTop').find('.clearMetrica');
						$('#menosNumMetricas').click(function() {
							divSetarAcaoId('{$g['divId']}', {$r_numMetricas} - 1, 'numMetricas');
							$('#{$g['divId']} .gridFiltroBotoes:eq(0) button:eq(0)').trigger('click');
							divSetarAcaoId('{$g['divId']}', '', '{$fato}');
							divSetarAcaoId('{$g['divId']}', '', '{$fato2}');
							divSetarAcaoId('{$g['divId']}', '', '{$tipodata}');
							divSetarAcaoId('{$g['divId']}', '', '{$medida}');
						});
					} </script></div>
EOT;
			}
			echo "</div>";
			if (($r_numMetricas -1) == $i || ($i == 0 && $r_numMetricas == 1)) {
				echo <<<EOT
					<div class="clearMetrica" style="clear:both;"></div>
EOT;
			} else {
				echo '<span class="item" style="width: 2px; height: 24px; margin-top: 8px; background-color: silver;"></span>';
			}
			$corrigeComboValor .= <<<EOT
				setTimeout(function(){
					$('#{$fato} .opt').each(function(){
						if($(this).css('display') === 'none'){
							$(this).remove();
						}
					});
				}, 500);
EOT;
		}
		/*
		$filtroObrigatorio = true;
		if($r_bi == 'visu' && (isset($row['D182_Grid_Salvo']) && $row['D182_Grid_Salvo'] != '') && !$r_gridFiltrado){
			$filtroObrigatorio = false;
		} else if($r_bi == 'conf' && isset($filtroSalvo)){
			$filtroObrigatorio = false;
		}*/
		//$filtroObrigatorio = true;
		
		// Javascript dos combo-box
		if ($r_bi == 'conf') {
			/**
			* Dimensões e Fatos
			*/
			if(isset($r_D132_Id)){
				$gridID = $md5;
			} else {
				$gridID = md5($g['r_']);
			}
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					{$corrigeComboValor}
					$('#{$g['divId']} .gridFiltroBotoes:eq(0) button:eq(0)').val('Gerar Relatório/Filtrar').unbind('click').click(function(data) {
						var divId = '{$g['divId']}';
						divSetarAcaoId(divId, '{$r_D132_Id}', 'D132_Id');
						{$divSetarMetricas}
						console.log($('#dimensao1ord').val());
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
						divSetarAcaoId(divId, $('#ocultarTotais').is(':checked'), 'ocultarTotais');
						divSetarAcaoId(divId, $('input[name=exibir]:checked').val(), 'exibir');
						divSetarAcaoId(divId, $('#incluirGraficoLabel').is(':checked'), 'incluirGraficoLabel');
						divSetarAcaoId(divId, $('#incluirGraficoLegend').is(':checked'), 'incluirGraficoLegend');
						//divRefresh(divId);
						return false;
					});
					$('#{$g['divId']} .gridFiltroBotoes:eq(0) button:eq(1)').hide();



					$('<button type="submit" value="" class="ui-state-default limparFiltro" title="Salvar" style="width: 24px; height: 22px; margin-left: 4px;"><span class="ui-icon  ui-icon-disk" title="Salvar" style="margin-left: -10px; margin-top: -2px;"></span></button>').click(function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Salvar Relatório', '/etc/etc011/content/etc011ContentSalvar/', '&acaoId=' + encodeURIComponent('{$r_D132_Id}') + '&tabela=D132' + '&dimensao1=' + encodeURIComponent($('#dimensao1').val()) + '&dimensao1ord=' + encodeURIComponent($('#dimensao1ord').val()) + '&dimensao2=' + encodeURIComponent($('#dimensao2').val()) + '&mostrarCurvaABC=' + encodeURIComponent($('#mostrarCurvaABC').is(':checked')) + '&ocultarColunasDashboard=' + encodeURIComponent($('#ocultarColunasDashboard').is(':checked')) + '&ocultarSempreColunas=' + encodeURIComponent($('#ocultarSempreColunas').is(':checked')) + '&ocultarPorcentagem=' + encodeURIComponent($('#ocultarPorcentagem').is(':checked')) +'&ocultarTotais=' + encodeURIComponent($('#ocultarTotais').is(':checked')) + '&exibir=' + encodeURIComponent($('input[name=exibir]:checked').val()) + '&incluirLinhaVazia=' + encodeURIComponent($('#incluirLinhaVazia').is(':checked')) + '&incluirColunaVazia=' + encodeURIComponent($('#incluirColunaVazia').is(':checked')) + '&dimensao2ord=' + encodeURIComponent($('#dimensao2ord').val()) + '&md5=' + encodeURIComponent('{$gridID}') + '&incluirGraficoLabel=' + encodeURIComponent($('#incluirGraficoLabel').is(':checked')) + '&incluirGraficoLegend=' + encodeURIComponent($('#incluirGraficoLegend').is(':checked')) + '&numMetricas={$r_numMetricas}' {$paramMetricas}, [350,130]);
						return false;
					}).appendTo('#{$g['divId']} .gridFiltroBotoes:eq(0)');

					if('{$r_D132_Id}' != ''){
						$('<button type="submit" value="" class="ui-state-default limparFiltro" title="Cancelar edição" style="width: 24px; height: 22px; margin-left: 4px;"><span class="ui-icon  ui-icon-closethick" title="Cancelar edição" style="margin-left: -10px; margin-top: -2px;"></span></button>').click(function(data) {
							divSetarAcaoId('{$g['divId']}', '', 'D132_Id');
							divRefresh('{$g['divId']}');
						}).appendTo('#{$g['divId']} .gridFiltroBotoes:eq(0)');

						//$('<span style="margin: 5px 0 0 -350px; position: absolute; font-weight: bold;">Editando: {$D132_Descricao}</span>').appendTo('#{$g['divId']} .gridFiltroBotoes:eq(0)');
					}
					// Desabilita dimensões e datas dependendo do fato escolhido
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
				<span class="item">
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
		echo '<option class="opt" value="">...</option>';

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
				<span class="item">
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
		echo 'Tabela: ';
		$checked = isset($r_exibir) && $r_exibir == 'grid' ? ' checked="checked"' : '';
		echo '<input type="radio" name="exibir" value="grid"'.$checked.'> Grid';
		echo <<<EOT
				
				<input type="checkbox" id="mostrarCurvaABC" value="1"{$mostrarCurvaABC_} />&nbsp;Curva ABC&nbsp;
				<input type="checkbox" id="ocultarColunasDashboard" value="1"{$ocultarColunasDashboard_} />&nbsp;Ocultar colunas no dashboard&nbsp;
				<input type="checkbox" id="ocultarSempreColunas" value="1"{$ocultarSempreColunas_} />&nbsp;Ocultar colunas sempre&nbsp;
				<input type="checkbox" id="ocultarPorcentagem" value="1"{$ocultarPorcentagem_} />&nbsp;Ocultar porcentagem&nbsp;
				<input type="checkbox" id="ocultarTotais" value="1"{$ocultarTotais_} />&nbsp;Ocultar totais&nbsp;
				<input type="checkbox" id="incluirLinhaVazia" value="1"{$incluirLinhaVazia_} />&nbsp;Incluir linhas vazias&nbsp;
				<input type="checkbox" id="incluirColunaVazia" value="1"{$incluirColunaVazia_} />&nbsp;Incluir colunas vazias
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
		echo '</div>';
		} else {
		    // Visualização, precisamos dessa variável para o inject no $extra
            $fato_ = 'r_fato';
            $fato_ = isset($$fato_) ? $$fato_ : false;
        }
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'DIM002_Nome_Empresa, DIM002_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
			'DIM001_Data' => array('titulo' => 'Data Emissão', 'tipo' => 'data'),
            'DIM001_Dia' => array('titulo' => 'Data Emissão (dia)', 'tipo' => 'texto'),
            'DIM001_Mes' => array('titulo' => 'Data Emissão (mês)', 'tipo' => 'texto'),
            'DIM001_Ano' => array('titulo' => 'Data Emissão (ano)', 'tipo' => 'texto'),
            'CONCAT(DIM001_Mes, '/', DIM001_Ano)' => array('titulo' => 'Data Emissão (mês/ano)', 'tipo' => 'texto'),
            'DIM001_Dia_Da_Semana' => array('titulo' => 'Data Emissão (dia da semana)', 'tipo' => 'texto'),
			'DIM003_Estado' => array('titulo' => 'Estado', 'tipo' => 'texto'),
			'DIM003_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'DIM003_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			//'DIM003_Endereco' => array('titulo' => 'Endereço', 'tipo' => 'texto'),
			'D4Vendedor.DIM004_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D4Vendedor.DIM004_C007_Id' => array('titulo' => 'Vendedor (id)', 'tipo' => 'texto'),
			'D4Supervisor.DIM004_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'D4Supervisor.DIM004_C007_Id' => array('titulo' => 'Supervisor (id)', 'tipo' => 'texto'),
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
			'DIM005_Data' => array('titulo' => 'Entrada/Saída/Vencimento', 'tipo' => 'data'),
			'FAC001_FLAG_CANCELADO' => array('titulo' => 'Cancelado', 'tipo' => 'texto'),
			'DIM012_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'DIM013_Nome_Caixa' => array('titulo' => 'Caixa', 'tipo' => 'texto'),
			'DIM013_Nome_Banco' => array('titulo' => 'Banco', 'tipo' => 'texto'),
		);
		$gridFiltroExibir = array_unique($gridFiltroExibir);
		$gridFiltroExibido = array();
		foreach ($gridFiltro as $chaveFiltro => $detalhesFiltro) {
			foreach ($gridFiltroExibir as $chaveExibir) {
				if(strpos($chaveFiltro, $chaveExibir) !== false){
					$gridFiltroExibido[$chaveFiltro] = $detalhesFiltro;
				}
			}
		}
		if(!empty($gridFiltroExibido)){
			$gridFiltro = $gridFiltroExibido;			
		}
		
		// Descrição (Se for relatório salvo)
		if ($r_bi == 'visu' && isset($D132_Descricao)) {
			// Mudou, não é mais salvo por usuário (ficar gravado no disco)
			if(!empty($filtroSalvo)){
				$filtrado = $filtroSalvo;
			}
			$modificado = true;
			//
			if ($modificado) {
				$filtradoTxt = '';
				foreach ($filtrado['campos'] as $filtroKey => $filtro) {
					if($filtroKey != 'DIM001_Data' && $filtroKey != 'DIM012_Nome_Abreviado') { continue; }
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
				if ($filtradoTxt) {
					$modificado = !isset($r_novavisao) ? '' : 'Filtros: ';
					$modificado .= '<span style="font-weight: normal; font-size: 12px;">(' . $filtradoTxt . ')</style>';
				} else {
					$modificado = '';
				}
			}
			if (isset($r_novavisao) && $r_novavisao == 'semfiltro') {
				if ($modificado) {
					echo '<div style="padding: 4px 10px; font-size: 14px;"><span style="font-weight: bold;">'.$modificado.'</span></div>';
				}
			} else if (isset($r_novavisao)) {
				//
			} else {
				echo '<div style="padding: 4px 10px; font-size: 14px;"><span style="font-weight: bold;">'.$D132_Descricao. ' ' .$modificado.'</span></div>';
				//echo '<div style="padding: 4px 10px; font-size: 14px;"><span style="font-weight: bold;">'..'</span></div>';
			}
		}
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(false, $gridFiltro, '', $md5, $filtroSalvo, false, true, false, false, $r_tab ? md5("bi-{$r_tab}") : false, $ignorarDimensao, $filtroGlobal);
        $extra = popularExtra($extra);
		preg_match_all("#DIM[0-9]{3}+#", $extra, $leftJoin);
		$joinsIncluir = array_unique($leftJoin[0]);

		$camposDataLeftJoin = array_unique($camposDataLeftJoin);
		foreach ($tipodatas as $campoData => $arrayCampoData) {
			if(in_array($campoData, $camposDataLeftJoin)){
				$joinsIncluir[] = $arrayCampoData[0];
			}
		}

		$d1 = explode('.', $dimensoes[$r_dimensao1][0]);
		$d1 = count($d1) == 1 ? $d1[0] : $d1[1];
		$d2 = explode('.', $dimensoes[$r_dimensao2][0]);
		$d2 = count($d2) == 1 ? $d2[0] : $d2[1];
		$joinsIncluir[] = substr($d1, 0, 6);
		$joinsIncluir[] = substr($d2, 0, 6);
		$joinsIncluir = array_unique($joinsIncluir);

		$joins = '';
		foreach ($arrayJoins as $join => $leftJoin) {
			if(in_array($join, $joinsIncluir)){
				$joins .= $leftJoin;
			}
		}

		if (isset($r_novavisao) && $r_novavisao == 'semfiltro') {
			//echo "<button style='margin: 2px 6px; padding: 0px; float: left;' onclick=\"divSetarAcaoId('{$g['divId']}', 'comfiltro', 'novavisao'); divRefresh('{$g['divId']}');\">Modificar Filtros</button><div style=\"clear: both;\"></div>";
		} else if ($r_bi == 'conf' || (isset($r_grid) && $r_Filtro_D132_Id == 1)) {
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro, $md5);
		} else {
			//$g['smarty']->assign('gridOptNoShow', true);
			// Esconde o filtro
			echo '<!-- INICIO-FILTRO --><div class="dashboardFiltro">';
				echo $g['ui']->gridFiltroPrint($gridFiltro, $md5);
			echo '</div><!-- FIM-FILTRO --->';
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
						.addClass('icone titleRelatorioBI')
						.css({
							'margin-right': '5px',
							'position': 'absolute',
							'top': '7px',
							'right': 0
						})
						.click(function() {
							abrirJanela(false, 'B.I.', '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc011/content/etc011ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent('{$r_D132_Id}') + '&bi=visu&exibirFiltro=true&grid=true&tab=' + encodeURIComponent('{$r_tab}'), [1200,600]);
						})
						.appendTo($('#{$g['divId']}').closest('.formEditor'));
					$('<span>').addClass('ui-icon ui-icon-zoomin').attr('title', 'Abrir Grid / Filtrar').css({'background-image': 'url("/hardness3/static/css/temas/padrao/images/ui-icons_bbbbbb_256x240.png")', 'display': 'none'}).appendTo(divGrid);
					// Refresh
					
					var divRecarregar = $('<div></div>')
						.attr({
							'title': 'Recarregar'
						})
						.addClass('icone titleRelatorioBI')
						.css({
							'margin-right': '20px',
							'position': 'absolute',
							'top': '7px',
							'right': 0
						})
						.click(function() {
							divSetarAcaoId('{$g['divId']}', 'true', 'carregarDashboard');
							divRefresh('{$g['divId']}', 'true');
							setTimeout(function(){
								$('#{$g['divId']}').find('.grid:first').css('font-size', '11px');
								$('#{$g['divId']}').find('.theadnormal th').css('font-size', '11px');
								corrigeGrid('{$g['divId']}');
							}, 300);
						})
						.appendTo($('#{$g['divId']}').closest('.formEditor'));
					$('<span>').addClass('ui-icon ui-icon-refresh').attr('title', 'Recarregar').css({'background-image': 'url("/hardness3/static/css/temas/padrao/images/ui-icons_bbbbbb_256x240.png")', 'display': 'none'}).appendTo(divRecarregar);
					$('#{$g['divId']} .gridOptTop').remove();
				} </script></div>
EOT;
			if(isset($r_FiltroUsuario)){
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var divRemoverFiltroSalvo = $('<div></div>')
							.attr({
								'title': 'Limpar filtro salvo'
							})
							.addClass('icone titleRelatorioBI')
							.css({
								'margin-right': '35px',
								'position': 'absolute',
								'top': '7px',
								'right': 0
							})
							.click(function() {
								var \$buttons = {
									"Sim": function() {
										$(this).dialog("destroy").remove();
										$.getJSON('/etc/etc011/ajax/limparFiltroSalvo/?ajax=true&D132_Id=' + encodeURIComponent('{$r_D132_Id}') + '&callback=?', function(request) {
											divSetarAcaoId('{$g['divId']}', 'true', 'carregarDashboard');
											divRefresh('{$g['divId']}', 'true');
											setTimeout(function(){
												$('#{$g['divId']}').find('.grid:first').css('font-size', '11px');
												$('#{$g['divId']}').find('.theadnormal th').css('font-size', '11px');
												corrigeGrid('{$g['divId']}');
											}, 300);
										});
										
									},
									"Não": function() { $(this).dialog("destroy").remove(); }
								};
					    		dialogConfirm('Atenção', 'Deseja remover o filtro salvo para este relatório, retornando ao modelo padrão?', \$buttons);
							})
							.appendTo($('#{$g['divId']}').closest('.formEditor'));
						$('<span>').addClass('ui-icon ui-icon-trash').attr('title', 'Limpar filtro salvo').css({'background-image': 'url("/hardness3/static/css/temas/padrao/images/ui-icons_bbbbbb_256x240.png")'}).appendTo(divRemoverFiltroSalvo);
					} </script></div>
EOT;
			}
		}

		$array = array(
			'fato' => $r_fato,
			'fato2' => $r_fato2,
			'dimensao1' => $r_dimensao1,
			'dimensao1ord' => $r_dimensao1ord,
			'dimensao2' => $r_dimensao2,
			'dimensao2ord' => $r_dimensao2ord,
			'medida' => $r_medida,
			'incluirLinhaVazia' => $r_incluirLinhaVazia,
			'incluirColunaVazia' => $r_incluirColunaVazia,
			'mostrarCurvaABC' => $r_mostrarCurvaABC,
			'ocultarColunasDashboard' => $r_ocultarColunasDashboard,
			'tipodata' => $r_tipodata,
			'exibir' => $r_exibir,
			'incluirGraficoLabel' => $r_incluirGraficoLabel,
			'incluirGraficoLegend' => $r_incluirGraficoLegend,
			'numMetricas' => $r_numMetricas,
			'ocultarSempreColunas' => $r_ocultarSempreColunas,
			'ocultarPorcentagem' => $r_ocultarPorcentagem,
			'ocultarTotais' => $r_ocultarTotais,
		);
		for ($i = 1; $i < 10; $i++) {
			$var = 'r_fatoa' . $i;
			if (isset($$var)) {
				$array['fatoa' . $i] = $$var;
				$var = 'r_fato2a' . $i;
				$array['fato2a' . $i] = $$var;
				$var = 'r_tipodataa' . $i;
				$array['tipodataa' . $i] = $$var;
				$var = 'r_medidaa' . $i;
				$array['medidaa' . $i] = $$var;
			}
		}
		$arrayCamposBI = serialize($array);
		$dir = $g['pathDadosUsuario'] . 'gridFiltro/';
		$file = $dir . $md5;
		$filtroAtual = file_get_contents($file);
		//$filtroObrigatorio = true;
		if($r_bi == 'visu' && (isset($row['D182_Grid_Salvo']) && $row['D182_Grid_Salvo'] != '') && !$r_gridFiltrado){
			ob_end_clean();
			//$filtroObrigatorio = false;
			if($r_exibirFiltro){
				//$row['D182_Grid_Salvo'] = str_replace('<div style="display: none; position: fixed; z-index: 9999" class="dashboardFiltro">', '<div class="dashboardFiltro">', $row['D182_Grid_Salvo']);
				$row['D182_Grid_Salvo'] = str_replace('$(\'#' . $row['D182_DivId'] . ' .gridOptTop\').remove();', '', $row['D182_Grid_Salvo']);
				$row['D182_Grid_Salvo'] = preg_replace('#INICIO-BTNSALVAR.*FIM-BTNSALVAR#imsU', ' ', $row['D182_Grid_Salvo']);
			} else {
				$row['D182_Grid_Salvo'] = preg_replace('#INICIO-FILTRO.*FIM-FILTRO#imsU', ' ', $row['D182_Grid_Salvo']);
			}
			if($r_carregarDashboard == 'true'){
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var colunasRemovidas = [];
						$('#{$g['divId']} thead tr').each(function(indexTR,elemTR){
							$(elemTR).find("th").each(function(indexTH,elemTH){							
								if($(elemTH).hasClass('hideDashBoard')){
									$('#{$g['divId']} div [valor="'+$(elemTH).attr('valor')+'"]').remove();
									$(elemTH).remove();
									if(indexTR == 1) {
										colunasRemovidas.push(indexTH);
									}
								}
							});
						});

						$('#{$g['divId']} tbody tr').each(function(index,elem){
							$(elem).find('td').each(function(i,e){
								if($.inArray(i, colunasRemovidas) != '-1'){
									$(e).remove();
								}
							});
						});
					} </script></div>
EOT;
		    }
			// Comentar para sempre não fazer cache
			echo str_replace(array($row['D182_DivId'],'#DIV_ID'), $g['divId'], $row['D182_Grid_Salvo']);
			die();
		}

		if (!isset($r_dimensao1) || empty($r_dimensao1)) { 
			die();
		}

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
		$dimensoes['DDMA'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDMA'][0]);
		$dimensoes['DDA'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDA'][0]);
		$dimensoes['DDM'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDM'][0]);
		$dimensoes['DDDS'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDDS'][0]);
		$dimensoes['DDD'][0] = str_replace('DIM001', $tipodatas[$r_tipodata][0], $dimensoes['DDD'][0]);
		
		// Variaveis
		$extraVazioLinha = ($r_incluirLinhaVazia == 'false') ? 'HAVING (Valor != 0)' : '';
		$extraVazioColuna = ($r_incluirColunaVazia == 'false') ? 'HAVING (Valor != 0)' : '';

		if ($debug) { log('(time) Conf: ' . round(microtime(true) - $time_start, 4)); }

		$thStyle = '';
		$thClass = 'ui-widget-header ui-corner-all';
		if (isset($r_generateHTML)) {
			$thStyle = 'background-color: silver; cursor: auto; font-size:11px; white-space: nowrap; padding: 5px 5px;';
			$thClass = '';
		}

		/**
		* Grid - Let's Go!
		*/
		// Colunas iniciais: Count/Totais + Curva ABC (se for Total DESC)
		$grid = array();
        //$primeiraLinhaGridHead .= '<th colspan="1" class="'.$thClass.'" style="'.$thStyle.' text-align: center;"></th>';
		if ($r_mostrarCurvaABC != 'false') {
			$primeiraLinhaGridHead .= '<th colspan="1" class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;"></th>';
			$grid[] = array('colunaValor' => "ABC", 'colunaTitulo' => 'ABC', 'colunaAlinhar' => 'center');
		}

        // count automático retirado
		//$grid[] = array('colunaValor' => "count", 'colunaTitulo' => '-', 'colunaAlinhar' => 'center', 'style' => 'width: 1%');
		$grid[] = array('colunaValor' => "dim01", 'colunaTitulo' => $dimensoes[$r_dimensao1][1], 'colunaAlinhar' => 'left', 'chavePrimaria' => true, 'chavePrimariaBase64' => true, 'extra' => "dim={$dimensoes[$r_dimensao1][0]}");
		$primeiraLinhaGridHead .= '<th colspan="1" class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;"></th>';
		$primeiraLinhaGridHead .= '<th colspan="1" class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;"></th>';
		
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
					if (empty($ocultarPorcentagem_)  && empty($ocultarTotais_)) {
						$primeiraLinhaGridHead .= '<th colspan="' . (($count - 1) * 2) . '" class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;">' . $fatos2[$last][1] . '</th>';
					} else if (empty($ocultarPorcentagem_) || empty($ocultarTotais_)) {
						$primeiraLinhaGridHead .= '<th colspan="' . ($count - 1) . '" class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;">' . $fatos2[$last][1] . '</th>';
					}
					$count = 1;
				}
				$last = $fato2_;
			}
			if ($count > 0) {
				if (empty($ocultarPorcentagem_) && empty($ocultarTotais_)) {
					$primeiraLinhaGridHead .= '<th colspan="' . (($count) * 2) . '" class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;">' . $fatos2[$last][1] . '</th>';
				} else if (empty($ocultarPorcentagem_) || empty($ocultarTotais_)) {
					$primeiraLinhaGridHead .= '<th colspan="' . ($count) . '" class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;">' . $fatos2[$last][1] . '</th>';
				}
			}
		} else {
			if (empty($ocultarPorcentagem_) && empty($ocultarTotais_)) {
				$primeiraLinhaGridHead .= '<th colspan="2" class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;">' . $fatos2[$r_fato2][1] . '</th>';
			} else if (empty($ocultarPorcentagem_) || empty($ocultarTotais_)) {
				$primeiraLinhaGridHead .= '<th class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;">' . $fatos2[$r_fato2][1] . '</th>';
			}
		}
		
		for ($i = 0; $i < $r_numMetricas; $i++) {
			$id = ($i == 0) ? '' : 'a' . $i;
			$fato2 = 'fato2' . $id;
			$fato2_ = 'r_' . $fato2;
			$fato2_ = isset($$fato2_) ? $$fato2_ : false;
			$fato = 'fato' . $id;
			$fato_ = 'r_' . $fato;
			$fato_ = isset($$fato_) ? $$fato_ : false;
			if(empty($ocultarTotais_)){
				$grid[] = array('colunaValor' => "dim02-{$i}", 'colunaTitulo' => $fatos[$fato_][1], 'colunaAlinhar' => 'right', 'style' => 'width: 1%; font-weight: bold;', 'extraTH' => 'colspan="1"');
			}
			if (empty($ocultarPorcentagem_)) {
				$grid[] = array('colunaValor' => "percentLinha-{$i}", 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;', 'extraTH' => 'colspan="1"');
			}
		}
		//$grid[] = array('colunaValor' => "dim02", 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'classTH' => 'extraTHGhost');

		if ($debug) { log('(time) Thead: ' . round(microtime(true) - $time_start, 4)); }

		// Curva ABC
		if ($r_mostrarCurvaABC != 'false') {
			// Consulta a primeira dimensão (CURVA ABC)
			$sql = <<<EOT
                /* ClickHouse */
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
			if (!$result) { 
				//die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); 
			}
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

		if ($debug) { log('(time) Curva ABC: ' . round(microtime(true) - $time_start, 4)); }

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

			$gridFiltroExibir = $fatos2Filtros[$fato2_];
			$gridFiltroExibido = array();
			foreach ($gridFiltro as $chaveFiltro => $detalhesFiltro) {
				foreach ($gridFiltroExibir as $chaveExibir) {
					if(strpos($chaveFiltro, $chaveExibir) !== false){
						$gridFiltroExibido[$chaveFiltro] = $detalhesFiltro;
					}
				}
			}
			$extraModificado = $g['ui']->gridFiltroExtra(false, $gridFiltroExibido, '', $md5, $filtroSalvo, false, false, false, false, $r_tab ? md5("bi-{$r_tab}") : false, $ignorarDimensao, $filtroGlobal);
            $extraModificado = popularExtra($extraModificado);
			// Consulta a primeira dimensão (TOTAL)
			$sql = <<<EOT
                /* ClickHouse */
				SELECT
				    SUM({$fatos[$fato_][0]}) AS Valor
				FROM FAC001
				{$joins}
				{$extraModificado}
EOT;
			if(strpos($fato_, "Saldo") !== false){
				$orderBy = (strpos($fato_, "Saldo_Inicial") !== false) ? 'ASC' : 'DESC';
				$sql = <<<EOT
                    /* ClickHouse */
					SELECT {$dimensoes[$r_dimensao1][0]},
						SUM(Valor) AS ValorB FROM (
							SELECT * FROM (
								SELECT
									{$dimensoes[$r_dimensao1][0]},
									{$fatos[$fato_][0]} AS Valor,
									{$dimensoes[$r_dimensao2][0]},
									FAC001_DIM012_Id
								FROM FAC001
									{$joins}
									{$extraModificado}
									AND FAC001_FLAG_SALDO = 'S'
								ORDER BY DIM001_Data {$orderBy}, FAC001_Id {$orderBy}
							) AS T3
						GROUP BY
							FAC001_DIM012_Id, {$dimensoes[$r_dimensao2][0]}, {$dimensoes[$r_dimensao1][0]}
							{$extraVazioLinha}
						) AS T2
EOT;
			}
			$result = mysql_query($sql);
			if (!$result) { 
				//die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); 
			}
			$row = mysql_fetch_row($result);
			$dim1Total[$i] = isset($row[0]) ? $row[0] : 0;
			// Consulta a primeira dimensão (NUMERO LINHAS)
			$sql = <<<EOT
                /* ClickHouse */
				SELECT
					{$dimensoes[$r_dimensao1][0]},
					{$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor
				FROM FAC001
				{$joins}
				{$extraModificado}
				GROUP BY
					{$dimensoes[$r_dimensao1][0]}
				{$extraVazioLinha}
EOT;
			if(strpos($fato_, "Saldo") !== false){
				$orderBy = (strpos($fato_, "Saldo_Inicial") !== false) ? 'ASC' : 'DESC';
				$sql = <<<EOT
                    /* ClickHouse */
					SELECT * FROM (
							SELECT
								{$dimensoes[$r_dimensao1][0]},
								{$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor,
								FAC001_DIM012_Id
							FROM FAC001
								{$joins}
								{$extraModificado}
								AND FAC001_FLAG_SALDO = 'S'
								{$extraVazioLinha}
							ORDER BY DIM001_Data {$orderBy}, FAC001_Id {$orderBy}
						) AS T2
						GROUP BY
							FAC001_DIM012_Id, {$dimensoes[$r_dimensao1][0]}
						{$extraVazioLinha}
EOT;
			}
			$result = mysql_query($sql);
			if (!$result) { 
				//die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); 
			}
			$dim1NumLinhas[$i] = mysql_numrows($result);
			
			// Consulta a segunda dimensão (NUMERO DE LINHAS)
			if ($r_dimensao2ord == 'ONA' || $r_dimensao2ord == 'OND') {
				$ordemD2 = "{$dimensoes[$r_dimensao2][0]} {$ordem[$r_dimensao2ord][0]}";
			} else {
				$ordemD2 = "Valor {$ordem[$r_dimensao2ord][0]}";
			}
			$sql = <<<EOT
                /* ClickHouse */
				SELECT
					{$dimensoes[$r_dimensao2][0]},
					{$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor
				FROM FAC001
				{$joins}
				{$extraModificado}
				GROUP BY
					{$dimensoes[$r_dimensao2][0]}
				{$extraVazioColuna}
EOT;
			if(strpos($fato_, "Saldo") !== false){
				$orderBy = (strpos($fato_, "Saldo_Inicial") !== false) ? 'ASC' : 'DESC';
				$sql = <<<EOT
                    /* ClickHouse */
					SELECT * FROM (
							SELECT
								{$dimensoes[$r_dimensao2][0]},
								{$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor,
								FAC001_DIM012_Id
							FROM FAC001
								{$joins}
								{$extraModificado}
								AND FAC001_FLAG_SALDO = 'S'
								{$extraVazioColuna}
							ORDER BY DIM001_Data {$orderBy}, FAC001_Id {$orderBy}
						) AS T2
						GROUP BY
							FAC001_DIM012_Id, {$dimensoes[$r_dimensao2][0]}
						{$extraVazioColuna}
EOT;
			}
			$result = mysql_query($sql);
			if (!$result) { 
				//die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); 
			}
			$dim2NumLinhas[$i] = mysql_numrows($result);
		}

		if ($debug) { log('(time) Totais: ' . round(microtime(true) - $time_start, 4)); }
		
		// Passamos a primeira dimensão para o gerado (elemento grid) e um array extra para identificar a posição da dimensão neste array
		$geradoDados = array();
		$geradoDadosItem = array();
		if ($r_mostrarCurvaABC != 'false') {
			$tmp = array('', '');
		} else {
			$tmp = array('');
		}
		// TODO: merge da primeira dimensão
		$qtdLinhas = $dim1NumLinhas[0];
		$qtdColunas = $dim2NumLinhas[0];
		//$tmp[] = '<span style="font-weight: bold;">Linhas: ' . number_format($dim1NumLinhas[0], 0, '.', '') . ' / Colunas: ' . number_format($dim2NumLinhas[0], 0, '.', '') . '</span>';
		foreach ($dim1Total as $value) {
			if(empty($ocultarTotais_)){
				$tmp[] = formataNumero($value, true);
			}
			if (empty($ocultarPorcentagem_)) {
				$tmp[] = '100%';
			}
		}
		$geradoDados[] = $tmp;

		if ($debug) { log('(time) 1 DIM .1: ' . round(microtime(true) - $time_start, 4)); }
		
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

			$gridFiltroExibir = $fatos2Filtros[$fato2_];
			$gridFiltroExibido = array();
			foreach ($gridFiltro as $chaveFiltro => $detalhesFiltro) {
				foreach ($gridFiltroExibir as $chaveExibir) {
					if(strpos($chaveFiltro, $chaveExibir) !== false){
						$gridFiltroExibido[$chaveFiltro] = $detalhesFiltro;
					}
				}
			}
			
			$extraModificado = $g['ui']->gridFiltroExtra(false, $gridFiltroExibido, '', $md5, $filtroSalvo, false, false, false, false, $r_tab ? md5("bi-{$r_tab}") : false, $ignorarDimensao, $filtroGlobal);
            $extraModificado = popularExtra($extraModificado);
			
			// Consulta a primeira dimensão (OS ELEMENTOS AGRUPADOS)
			if ($r_dimensao1ord == 'ONA' || $r_dimensao1ord == 'OND') {
				$ordemD1 = "{$dimensoes[$r_dimensao1][0]} {$ordem[$r_dimensao1ord][0]}";
			} else {
				$ordemD1 = "Valor {$ordem[$r_dimensao1ord][0]}";
			}

			$limit_ = $limit ? 'LIMIT 1000' : '';
			$sql = <<<EOT
                /* ClickHouse */
				SELECT
					{$dimensoes[$r_dimensao1][0]},
				    {$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor
				FROM FAC001
				{$joins}
				{$extraModificado}
				GROUP BY
					{$dimensoes[$r_dimensao1][0]}
					{$extraVazioLinha}
				ORDER BY
					{$ordemD1}
				{$limit_}
EOT;
			if(strpos($fato_, "Saldo") !== false){
				$orderBy = (strpos($fato_, "Saldo_Inicial") !== false) ? 'ASC' : 'DESC';
				$limit_ = $limit ? 'LIMIT 1000' : '';
				$sql = <<<EOT
                    /* ClickHouse */
					SELECT {$dimensoes[$r_dimensao1][0]},
						SUM(Valor) AS ValorB FROM (
							SELECT * FROM (
								SELECT
									{$dimensoes[$r_dimensao1][0]},
									{$fatos[$fato_][0]} AS Valor,
									{$dimensoes[$r_dimensao2][0]},
									FAC001_DIM012_Id
								FROM FAC001
									{$joins}
									{$extraModificado}
									AND FAC001_FLAG_SALDO = 'S'
								ORDER BY DIM001_Data {$orderBy}, FAC001_Id {$orderBy}
							) AS T3
						GROUP BY
							FAC001_DIM012_Id, {$dimensoes[$r_dimensao2][0]}, {$dimensoes[$r_dimensao1][0]}
							{$extraVazioLinha}
						) AS T2
					GROUP BY {$dimensoes[$r_dimensao1][0]}
					ORDER BY
						{$ordemD1}
					{$limit_}
EOT;
			}
			$geradoSql = addslashes(str_replace("\"", "'", str_replace("\r", ' ', str_replace("\n", ' ', $sql))));
			$result = mysql_query($sql);
			$qtdLinhas = (mysql_num_rows($result) > $qtdLinhas) ? mysql_num_rows($result) : $qtdLinhas;
			$pos = ($r_mostrarCurvaABC != 'false') ? 1 : 0;
			$geradoDados[0][$pos] = '<span style="font-weight: bold;">Linhas: ' . number_format($qtdLinhas, 0, '.', '') . ' / Colunas: ' . number_format($qtdColunas, 0, '.', '') . '</span>';
			if (!$result) { 
				//die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); 
			}

			if ($debug) { log('(time) 1 DIM .2: ' . round(microtime(true) - $time_start, 4)); }
			
			/*
			* Merge das Dimensões
			* Os passos são:
			* 		1. Fazer o loop, adicionar o ITEM e o TOTAL
			* 		2. Percorrer todas as linhas para ver se alguma não foi adiciona o TOTAL, e adiciona em branco (para fazer o merge de várias métricas)
			*/
			$keysAdicionados = array();
			while ($row = mysql_fetch_row($result)) {
				// Total por linha
				$totalPorLinha_ = $row[1];
				//$totalPorLinha = ($fato_ == 'FAC001_NF_Quantidade') ? number_format($row[1], 0, '.', ',') : formataNumero($row[1]);
				$totalPorLinha = formataNumero($row[1]);
				if($debug == true) {
					$totalPorLinha = '<div style="width: 50px; background-color: var(--BGVermelho)">'.$totalPorLinha.'</span>';
				}
				// Porcentagem total por linha
				$porcentagem  = round(($totalPorLinha_ * 100) / $dim1Total[$i], 2);
				if ($porcentagem != 100) {
					$porcentagem = number_format($porcentagem, 2, ',', '');
				}
				if($debug == true){
					$porcentagem = '<div style="width: 50px; background-color: green">'.$porcentagem.'</span>';
				}
				// Linha Nova
				$nova = array();
				// Utilizado para guardar o valor original
				$nova[-1] = array($row[0], (!$i ? $row[1] : 0));
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
					$keysAdicionados[$row[0]] = true;

					if (isset($geradoDadosItem[$row[0]])) {
						$key = $geradoDadosItem[$row[0]];
						//echo count($geradoDados[$key]) . " - {$totalPorLinha} - {$i}<br />";
						// métricas nulas ou vazias
						$x = (empty($ocultarPorcentagem_) && empty($ocultarTotais_)) ? 2 : 0;
						$numCols = 3 + ($i * $x);
						// totais de métricas anteriores
						for ($tmp = count($geradoDados[$key]); $tmp < $numCols; $tmp++) {
							if($debug == true){
								$geradoDados[$key][] = '<div style="width: 50px; background-color: pink;">0,00</div>';
							} else {
								$geradoDados[$key][] = '<span style="color: gray;">0,00</span>';
							}
						}
						if(empty($ocultarTotais_)){
							$geradoDados[$key][] = $totalPorLinha;
						}
						if (empty($ocultarPorcentagem_)) {
							$geradoDados[$key][] = $porcentagem;
						}
					} else {
						// 0: número da linha
						//$nova[] = ++$countLinha;
						// 1: nome
						if($r_dimensao1 == 'DDDMS'){
							$row[0] = gCorrigeData($row[0]);
						}
						$nova[] = $row[0];
						// totais de métricas anteriores
						if(!empty($ocultarPorcentagem_) && !empty($ocultarTotais_)){
							$multiplicador = 0;
						} else if(empty($ocultarPorcentagem_) && empty($ocultarTotais_)) {
							$multiplicador = ($i * 2);
						} else {
							$multiplicador = $i;
						}

						for ($tmp = 0; $tmp < $multiplicador; $tmp++) {
							if($debug == true){
								$nova[] = '<div style="width: 50px; background-color: pink;">0,00</div>';
							} else {
								$nova[] = '<span style="color: gray;">0,00</span>';
							}
						}
						// 2: total
						if(empty($ocultarTotais_)){
							$nova[] = $totalPorLinha;
						}
						// 3: porcentagem
						if (empty($ocultarPorcentagem_)) {
							$nova[] = $porcentagem;
						}
						// Formata dia do mês / dia da semana
						if (strpos($dimensoes[$r_dimensao1][0], '_Dia_Da_Semana') !== false) {
							$nova[0] = formataDiaSemana($nova[0]);
						} else if (strpos($dimensoes[$r_dimensao1][0], 'CONCAT') !== false) {
							$tmp = explode('/', $nova[0]);
							$nova[0] = formataDiaMes($tmp[0]) . '/' . $tmp[1];
						} else if (strpos($dimensoes[$r_dimensao1][0], '_Mes') !== false) {
							$nova[0] = formataDiaMes($nova[0]);
						}
						// adicionar
						$geradoDados[] = $nova;
						end($geradoDados);
						$geradoDadosItem[$nova[-1][0]] = key($geradoDados);
					}
				} else {
					$keysAdicionados[$row[0]] = true;
					// 0: número da linha
					//$nova[] = ++$countLinha;
					// 1: nome
					if($r_dimensao1 == 'DDDMS'){
						$row[0] = gCorrigeData($row[0]);	
					}
					$nova[] = $row[0];
					// 2: total
					if(empty($ocultarTotais_)){
						$nova[] = $totalPorLinha;
					}
					// 3: porcentagem
					if (empty($ocultarPorcentagem_)) {
						$nova[] = $porcentagem;
					}
					// Formata dia do mês / dia da semana
					if($r_mostrarCurvaABC != 'false'){
						if (strpos($dimensoes[$r_dimensao1][0], '_Dia_Da_Semana') != false) {
							$nova[1] = formataDiaSemana($nova[1]);
						} else if (strpos($dimensoes[$r_dimensao1][0], 'CONCAT') !== false) {
							$tmp = explode('/', $nova[1]);
							$nova[1] = formataDiaMes($tmp[0]) . '/' . $tmp[1];
						} else if (strpos($dimensoes[$r_dimensao1][0], '_Mes') != false) {
							$nova[1] = formataDiaMes($nova[1]);
						}
					} else {
						if (strpos($dimensoes[$r_dimensao1][0], '_Dia_Da_Semana') != false) {
							$nova[0] = formataDiaSemana($nova[0]);
						} else if (strpos($dimensoes[$r_dimensao1][0], 'CONCAT') !== false) {
							$tmp = explode('/', $nova[0]);
							$nova[0] = formataDiaMes($tmp[0]) . '/' . $tmp[1];
						} else if (strpos($dimensoes[$r_dimensao1][0], '_Mes') != false) {
							$nova[0] = formataDiaMes($nova[0]);
						}
					}

					// adicionar
					$geradoDados[] = $nova;
					end($geradoDados);
					$geradoDadosItem[$nova[-1][0]] = key($geradoDados);
				}
			}

			if ($debug) { log('(time) 1 DIM .3: ' . round(microtime(true) - $time_start, 4)); }

			//Caso a segunda coluna total/percentual em diante nao existir, inclui zero para empurrar o resto
			if ($i != 0) {
				foreach ($geradoDados as $key => $value) {
					if($key == 0) { continue; }
					if (!isset($value[-1])) {
						continue;
					}
					//
					if (count($keysAdicionados) > 0 && !isset($keysAdicionados[$value[-1][0]])) {
						//echo count($geradoDados[$key]) . " - {$totalPorLinha} - {$i}<br />";
						// métricas nulas ou vazias
						$x = (empty($ocultarPorcentagem_) && empty($ocultarTotais_)) ? 2 : 0;
						$numCols = 3 + ($i * $x);
						// totais de métricas anteriores
						for ($tmp = count($geradoDados[$key]); $tmp < $numCols; $tmp++) {
							if($debug == true){
								$geradoDados[$key][] = '<div style="width: 50px; background-color: yellow;">0,00</div>';
							} else {
								$geradoDados[$key][] = '<span style="color: gray;">0,00</span>';
							}
						}
						if(empty($ocultarTotais_)){
							if($debug == true){
								$geradoDados[$key][] = '<div style="width: 50px; background-color: brown;">0,00</div>';
							} else {
								$geradoDados[$key][] = '-';
							}
						}
						if (empty($ocultarPorcentagem_)) {
							if($debug == true){
								$geradoDados[$key][] = '<div style="width: 50px; background-color: brown;">0,00</div>';
							} else {
								$geradoDados[$key][] = '-';
							}
						}
					}
				}
			}

			if ($debug) { log('(time) 1 DIM .4: ' . round(microtime(true) - $time_start, 4)); }
			
			
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
						if (!$resultD1Registros) { 
						//die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); 
						}
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

		if ($debug) { log('(time) 1 DIM: ' . round(microtime(true) - $time_start, 4)); }

		$gridFiltroExibir = $fatos2Filtros[$r_fato2];
		$gridFiltroExibido = array();
		foreach ($gridFiltro as $chaveFiltro => $detalhesFiltro) {
			foreach ($gridFiltroExibir as $chaveExibir) {
				if(strpos($chaveFiltro, $chaveExibir) !== false){
					$gridFiltroExibido[$chaveFiltro] = $detalhesFiltro;
				}
			}
		}
		
		$extraModificado = $g['ui']->gridFiltroExtra(false, $gridFiltroExibido, '', $md5, $filtroSalvo, false, false, false, false, $r_tab ? md5("bi-{$r_tab}") : false, $ignorarDimensao, $filtroGlobal);
        $extraModificado = popularExtra($extraModificado);

		// Adiciona tabela Pivot da SEGUNDA DIMENSÃO
		if ($r_dimensao2ord == 'ONA' || $r_dimensao2ord == 'OND') {
			$ordemD2 = "{$dimensoes[$r_dimensao2][0]} {$ordem[$r_dimensao2ord][0]}";
		} else {
			$ordemD2 = "Valor {$ordem[$r_dimensao2ord][0]}";
		}
		$limit_ = $limit ? 'LIMIT 50' : 'LIMIT 200';
		$sql = <<<EOT
            /* ClickHouse */
			SELECT
				{$dimensoes[$r_dimensao2][0]},
				{$medidas[$r_medida][0]}({$fatos[$r_fato][0]}) AS Valor
			FROM FAC001
			{$joins}
			{$extraModificado}
			GROUP BY
				{$dimensoes[$r_dimensao2][0]}
			{$extraVazioColuna}
			ORDER BY
				{$ordemD2}
			{$limit_}
EOT;
		if(strpos($r_fato, "Saldo") !== false){
			$orderBy = (strpos($r_fato, "Saldo_Inicial") !== false) ? 'ASC' : 'DESC';
			$limit_ = $limit ? 'LIMIT 50' : 'LIMIT 200';
			$sql = <<<EOT
                /* ClickHouse */
				SELECT 
				    {$dimensoes[$r_dimensao2][0]},
				    SUM(Valor) AS ValorB 
				FROM
					(SELECT * FROM (
						SELECT
							{$dimensoes[$r_dimensao2][0]},
							({$fatos[$r_fato][0]}) AS Valor,
							FAC001_DIM012_Id
						FROM FAC001
							{$joins}
							{$extraModificado}
							AND FAC001_FLAG_SALDO = 'S'
							{$extraVazioColuna}
						ORDER BY DIM001_Data {$orderBy}, FAC001_Id {$orderBy}
					) AS T2
					GROUP BY
						FAC001_DIM012_Id, {$dimensoes[$r_dimensao2][0]}
						{$extraVazioColuna}
					ORDER BY
						{$ordemD2}
					{$limit_}) AS T3
				GROUP BY {$dimensoes[$r_dimensao2][0]}
EOT;
		}
		log($sql);
		$resultDim2 = mysql_query($sql);
		if (!$resultDim2) { 
			//die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); 
		}
		$pivotD2Count = mysql_numrows($resultDim2);
		if ($debug) { log('(time) 2 DIM - 1: ' . round(microtime(true) - $time_start, 4)); }
		while ($rowDim2 = mysql_fetch_row($resultDim2)) {
			$colunaTitulo = $rowDim2[0];
			if (strpos($dimensoes[$r_dimensao2][0], '_Dia_Da_Semana') != false) {
				$colunaTitulo = formataDiaSemana($colunaTitulo);
			} else if (strpos($dimensoes[$r_dimensao2][0], 'CONCAT') !== false) {
				$tmp = explode('/', $colunaTitulo);
				$colunaTitulo = formataDiaMes($tmp[0]) . '/' . $tmp[1];
			} else if (strpos($dimensoes[$r_dimensao2][0], '_Mes') != false) {
				$colunaTitulo = formataDiaMes($colunaTitulo);
			} else if ($r_dimensao2 == 'DDDMS'){
				$colunaTitulo = gCorrigeData($colunaTitulo);
			}
			if (!empty($ocultarSempreColunas_) || (!empty($ocultarColunasDashboard_) && $r_bi == 'visu' && (!isset($r_grid) || empty($r_grid)))) {
				$primeiraLinhaGridHead .= '<th colspan="' . ($r_numMetricas) . '" class="'.$thClass.' hideDashBoard" style="'.$thStyle.' text-align: center; z-index: 2;">' . $colunaTitulo . '</th>';
			} else {
				$primeiraLinhaGridHead .= '<th colspan="' . ($r_numMetricas) . '" class="'.$thClass.'" style="'.$thStyle.' text-align: center; z-index: 2;">' . $colunaTitulo . '</th>';
			}
			if ($debug) { log('(time) 2 DIM - 2.1: ' . round(microtime(true) - $time_start, 4)); }
			
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
				$grid[] = array('colunaValor' => uniqid(), 'colunaTitulo' => '<center>'.$fatos2[$fato2_][1].'<br />'.$fatos[$fato_][1].'</center>', 'colunaTituloLegenda' => $colunaTitulo ,'colunaAlinhar' => 'right', 'colunaAlinhar' => 'right', 'extraTH' => 'colspan="1"');
			
				$key = mysql_real_escape_string($rowDim2[0]);

				$gridFiltroExibir = $fatos2Filtros[$fato2_];
				$gridFiltroExibido = array();
				foreach ($gridFiltro as $chaveFiltro => $detalhesFiltro) {
					foreach ($gridFiltroExibir as $chaveExibir) {
						if(strpos($chaveFiltro, $chaveExibir) !== false){
							$gridFiltroExibido[$chaveFiltro] = $detalhesFiltro;
						}
					}
				}
				
				$extraModificado = $g['ui']->gridFiltroExtra(false, $gridFiltroExibido, '', $md5, $filtroSalvo, false, false, false, false, $r_tab ? md5("bi-{$r_tab}") : false, $ignorarDimensao, $filtroGlobal);
                $extraModificado = popularExtra($extraModificado);

				if ($debug) { log('(time) 2 DIM - 2.2: ' . round(microtime(true) - $time_start, 4)); }

				// Total
				// /*
				$sql = <<<EOT
                    /* ClickHouse */
					SELECT
						{$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor
					FROM FAC001
					{$joins}
					{$extraModificado} AND ({$dimensoes[$r_dimensao2][0]} = '{$key}')
EOT;
				if(strpos($fato_, "Saldo") !== false){
					$orderBy = (strpos($fato_, "Saldo_Inicial") !== false) ? 'ASC' : 'DESC';
					$limit_ = $limit ? 'LIMIT 1000' : '';
					$sql = <<<EOT
                        /* ClickHouse */
						SELECT {$dimensoes[$r_dimensao1][0]},
							SUM(Valor) AS ValorB FROM (
								SELECT * FROM (
									SELECT
										{$dimensoes[$r_dimensao1][0]},
										{$fatos[$fato_][0]} AS Valor,
										{$dimensoes[$r_dimensao2][0]},
										FAC001_DIM012_Id
									FROM FAC001
										{$joins}
										{$extraModificado}
										AND ({$dimensoes[$r_dimensao2][0]} = '{$key}')
										AND FAC001_FLAG_SALDO = 'S'
									ORDER BY DIM001_Data {$orderBy}, FAC001_Id {$orderBy}
								) AS T3
							GROUP BY
								FAC001_DIM012_Id, {$dimensoes[$r_dimensao2][0]}, {$dimensoes[$r_dimensao1][0]}
								{$extraVazioLinha}
							) AS T2
						ORDER BY
							{$ordemD1}
						{$limit_}
EOT;
				}
				log($sql);
				$resultDim2Ind = mysql_query($sql);
				if (!$resultDim2Ind) { 
					//die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); 
				}
				$row = mysql_fetch_row($resultDim2Ind);
				$total = isset($row[0]) ? $row[0] : 0;
				$geradoDados[0][count($geradoDados[0])] = formataNumero($total, true);

				if ($debug) { log('(time) 2 DIM - 2.3: ' . round(microtime(true) - $time_start, 4)); }

				// */
				// Para cada item do pivot, faz uma nova consulta
				$sql = <<<EOT
                    /* ClickHouse */
					SELECT
						{$dimensoes[$r_dimensao1][0]},
					    {$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor
					FROM FAC001
					{$joins}
					{$extraModificado} AND ({$dimensoes[$r_dimensao2][0]} = '{$key}')
					GROUP BY
					    {$dimensoes[$r_dimensao1][0]}
					ORDER BY
						{$dimensoes[$r_dimensao1][0]} ASC
EOT;
				if(strpos($fato_, "Saldo") !== false){
					$orderBy = (strpos($fato_, "Saldo_Inicial") !== false) ? 'ASC' : 'DESC';
					$sql = <<<EOT
                        /* ClickHouse */
						SELECT 
							{$dimensoes[$r_dimensao1][0]},
							SUM(Valor) as ValorB
						FROM
							(SELECT * FROM (
								SELECT
									{$dimensoes[$r_dimensao1][0]},
								    {$medidas[$medida_][0]}({$fatos[$fato_][0]}) AS Valor,
								    FAC001_DIM012_Id
								FROM FAC001
								{$joins}
								{$extraModificado} AND ({$dimensoes[$r_dimensao2][0]} = '{$key}') 
								AND FAC001_FLAG_SALDO = 'S'
								ORDER BY DIM001_Data {$orderBy}, FAC001_Id {$orderBy}
							) AS T3
								GROUP BY FAC001_DIM012_Id, {$dimensoes[$r_dimensao1][0]}
						) AS T2
							GROUP BY
							    {$dimensoes[$r_dimensao1][0]}
							ORDER BY
								{$dimensoes[$r_dimensao1][0]} ASC
EOT;
				}
				$resultDim2Ind = mysql_query($sql);
				if (!$resultDim2Ind) { 
					//die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); 
				}
				if ($debug) { log('(time) 2 DIM - 2.4: ' . round(microtime(true) - $time_start, 4)); }
				if (mysql_num_rows($resultDim2Ind) == 0) {
					foreach ($geradoDados as $key_ => $geradoDadosItem) {
						if ($key_ == 0) { continue; }
						if($debug == true){
							$geradoDados[$key_][] = '<div style="width: 50px; background-color: blue">'.formataNumero(0).'</span>';
						} else {
							$geradoDados[$key_][] = formataNumero(0);
						}
					}
					if ($debug) { log('(time) 2 DIM - 2.5.1: ' . round(microtime(true) - $time_start, 4)); }
				} else {
					/*
					// Loop dos itens já existentes (1 DIM) para cada item encontrado da nova DIM
					foreach ($geradoDados as $key => $geradoDadosItem) {
						if ($key == 0) { continue; }
						@mysql_data_seek($resultDim2Ind, 0);
						$find = false;
						while ($rowDim2Ind = mysql_fetch_row($resultDim2Ind)) {
							if ($geradoDadosItem[-1] == $rowDim2Ind[0]) {
								// Total por linha
								if($debug == true){
									$geradoDados[$key][] = '<div style="width: 50px; background-color: blue">'.formataNumero($rowDim2Ind[1]).'</span>';
								} else {
									$geradoDados[$key][] = formataNumero($rowDim2Ind[1]);
								}
								$find = true;
							}
						}
						if (!$find) {
							if($debug == true){
								$geradoDados[$key][] = '<div style="width: 50px; background-color: blue">'.formataNumero(0).'</span>';
							} else {
								$geradoDados[$key][] = formataNumero(0);
							}
						}
					}
					*/
					// Otimização do código acima (em vez de fazer 2 loops, é feito cache em um array acessado via key)
					$cache = array();
					while ($rowDim2Ind = mysql_fetch_row($resultDim2Ind)) {
						$cache[$rowDim2Ind[0]] = $rowDim2Ind[1];
					}
					foreach ($geradoDados as $key => $geradoDadosItem) {
						if ($key == 0) { continue; }
						if (isset($cache[$geradoDadosItem[-1][0]])) {
							if($debug == true){
								$geradoDados[$key][] = '<div style="width: 50px; background-color: blue">'.formataNumero($cache[$geradoDadosItem[-1][0]]).'</span>';
							} else {
								$geradoDados[$key][] = formataNumero($cache[$geradoDadosItem[-1][0]]);
							}
						} else {
							if($debug == true){
								$geradoDados[$key][] = '<div style="width: 50px; background-color: blue">'.formataNumero(0).'</span>';
							} else {
								$geradoDados[$key][] = formataNumero(0);
							}
						}
					}
					unset($cache);

					if ($debug) { log('(time) 2 DIM - 2.5.2: ' . round(microtime(true) - $time_start, 4)); }
				}
			}
			if ($debug) { log('(time) 2 DIM - 2: ' . round(microtime(true) - $time_start, 4)); }
		}

		if ($debug) { log('(time) 2 DIM: ' . round(microtime(true) - $time_start, 4)); }
		
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


		// Ordenação
		/*
		ONA - Item ASC
		OND - Item DESC
		OTA - Total ASC
		OTD - Total DESC
		*/
		if ($r_numMetricas > 1) {
			usort($geradoDados, function($a, $b) {
				global $r_dimensao1ord, $r_dimensao1;
				if (empty($b[0]) ) {
					return 1;
				}
				if (empty($a[0]) ) {
					return -1;
				}
				if ($r_dimensao1ord == 'ONA') {
					if($r_dimensao1 == 'DDDMS'){
						return ($a[-1][0] > $b[-1][0]) ? 1 : -1;
					} else {
						return strcmp($a[0], $b[0]);
					}
				} else if ($r_dimensao1ord == 'OND') {
					if($r_dimensao1 == 'DDDMS'){
						return ($a[-1][0] > $b[-1][0]) ? -1 : 1;
					} else {
						return strcmp($b[0], $a[0]);
					}
				} else if ($r_dimensao1ord == 'OTA') {
					return ( $a[-1][1] > $b[-1][1] ) ? 1 : -1;
				} else if ($r_dimensao1ord == 'OTD') {
					return ( $a[-1][1] > $b[-1][1] ) ? -1 : 1;
				}
			});
		}

		if ($debug) { log('(time) Ordenação: ' . round(microtime(true) - $time_start, 4)); }

//		$count = 0;
//		foreach ($geradoDados as $key => $value) {
//			if (!$count) { $count++; continue; }
//			if ($r_mostrarCurvaABC != 'false') {
//				$geradoDados[$key][1] = $count;
//			} else {
//				$geradoDados[$key][0] = $count;
//			}
//			$count++;
//		}

		if ($r_mostrarCurvaABC != 'false') {
			$curvaABCTotal['A'][0] = number_format($curvaABCTotal['A'][0], 0, '', '.');
			$curvaABCTotal['A'][1] = number_format($curvaABCTotal['A'][1], 2, ',', '.');
			$curvaABCTotal['B'][0] = number_format($curvaABCTotal['B'][0], 0, '', '.');
			$curvaABCTotal['B'][1] = number_format($curvaABCTotal['B'][1], 2, ',', '.');
			$curvaABCTotal['C'][0] = number_format($curvaABCTotal['C'][0], 0, '', '.');
			$curvaABCTotal['C'][1] = number_format($curvaABCTotal['C'][1], 2, ',', '.');
			$stringCurvaABC = <<<EOT
				<div style="margin: 5px;"><span style="color: green; font-weight: bold;">A (80%)</span> : {$curvaABCTotal['A'][0]} - {$curvaABCTotal['A'][1]} &nbsp;&nbsp;|&nbsp;&nbsp; <span style="color: blue; font-weight: bold;">B (10%)</span> : {$curvaABCTotal['B'][0]} - {$curvaABCTotal['B'][1]} &nbsp;&nbsp;|&nbsp;&nbsp; <span style="color: var(--BGVermelho); font-weight: bold;">C (10%)</span> : {$curvaABCTotal['C'][0]} - {$curvaABCTotal['C'][1]}</div>
EOT;
			echo $stringCurvaABC;
		}
		
		/**
		* Gráfico - Organiza os valores para geração do gráfico no javascript
		*/
		$grafico = array('nrColunas' => 0, 'colunas' => array(), 'colunaTotal' => array(), 'colunaColunas' => array());
        $tmpKey = 0;
		foreach ($geradoDados as $key => $value) {
			$tmpKey = str_replace("'", '', strip_tags($value[0]));
			if ($key == 0) { continue; }
			// Números de colunas que existe neste grid
			if ($grafico['nrColunas'] == 0) {
				$grafico['nrColunas'] = count($value)-4;
			}
			// Coluna Total
			$colunaTotal = gCorrigeNumeroInverte(strip_tags($value[1]));
			$grafico['colunaTotal'][$tmpKey] = empty($colunaTotal) ? 0 : str_replace("'", '', $colunaTotal);
			// Todas as outras colunas
			foreach ($value as $colunaKey => $colunaValue) {
				if ($colunaKey < 3) { continue; }
				$coluna = gCorrigeNumeroInverte(strip_tags($colunaValue));
				$grafico['colunaColunas']["$tmpKey "][] = array(
				    empty($coluna) ? 0 : str_replace("'", '', $coluna),
                    str_replace("'", '', strip_tags($value[0])) . '/' . @$grid[$colunaKey]['colunaTituloLegenda']
                );
			}
            $tmpKey++;
		}
		foreach ($grid as $key => $value) {
			if ($key < 3) { continue; }
			$grafico['colunas'][] = $value['colunaTituloLegenda'];
		}
		// Caso não exista um coluna selecionada, permite exibir somente a linha+valorTotal no gráfico de barra
        if ($grafico['nrColunas'] <= 0 && in_array($r_exibir, array('barra'))) {
            $grafico['nrColunas'] = count($grafico['colunaTotal']);
            $grafico['colunaColunas']['-'] = array();
            foreach ($grafico['colunaTotal'] as $key => $value) {
                $grafico['colunas'][] = $key;
                $grafico['colunaColunas']['-'][] = array($value, $key);
            }
        }
		$grafico = json_encode($grafico, true);

		/* Mostra o JSON do Gráfico
		$jsonPretty=json_encode(json_decode($grafico), JSON_PRETTY_PRINT);
		echo "<pre>";
		echo $jsonPretty;
		echo "</pre>";
		*/

		// acrescenta o gráfico para uso no javascript
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//$('#{$g['divId']} .gridOptTop').remove();
				$('#{$g['divId']}').data('grafico', '{$grafico}');
			} </script></div>
EOT;

		// não precisa total se tiver apenas uma coluna
		/*if ($pivotD2Count == 1) {
			unset($grid[2]);
		}*/
		if (!empty($ocultarSempreColunas_) || (!empty($ocultarColunasDashboard_) && $r_bi == 'visu' && (!isset($r_grid) || empty($r_grid)))) {
			$countGrid = count($grid);
			$i = 1;
			if ($r_mostrarCurvaABC != 'false') {
				$i = 2;
			}
			//$i = $i + (1 * $r_numMetricas);

			if (empty($ocultarPorcentagem_) && empty($ocultarTotais_)) {
				$i = $i + (2 * $r_numMetricas);
			} else if(empty($ocultarPorcentagem_) || empty($ocultarTotais_)){
				$i = $i + (1 * $r_numMetricas);
			} else {
				$i = $i + (0 * $r_numMetricas);
			}
			//Remove 1 pra contagem bater com os indexes do array
			$countGrid = $countGrid-1;
			for ($i; $i <= $countGrid; $i++) {
				//unset($grid[$i]);
				$grid[$i]['classTH'] = "hideDashBoard";
			}
		}

		//$g['smarty']->assign('gridOptNoShow', true);
		if (isset($r_novavisao)) {
			echo <<<EOT
<div class="gridOptTop {$class}" style="margin-top: -25px; position: absolute; right: 4px;">
    <div class="btn-group">
        <button class="btn btn-mini" onclick="gridRefresh(this); return false;">Atualizar</button>
        <button data-toggle="dropdown" class="btn btn-mini dropdown-toggle" onmouseover="if (!$(this).closest('div').hasClass('open')) { $(this).trigger('click'); } "><span class="caret"></span></button>
        <ul class="dropdown-menu pull-left" style="margin-left: -80px; margin-top: -5px;">
EOT;
			if ($r_novavisao == 'semfiltro') {
	            echo <<<EOT
	           		<li><a href="#" onclick="divSetarAcaoId('{$g['divId']}', 'comfiltro', 'novavisao'); divRefresh('{$g['divId']}');">Exibir Filtros</a></li>
	           		<li><a href="#" onclick="abrirJanela(false, divIdRootAberto(), '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc011/content/etc011ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent('{$r_D132_Id}') + '&bi=conf&grid=true&tab=' + encodeURIComponent('{$r_tab}'), [1200,600]);">Configurar</a></li>
EOT;
			} else {
				echo <<<EOT
	           		<li><a href="#" onclick="divSetarAcaoId('{$g['divId']}', 'semfiltro', 'novavisao'); divRefresh('{$g['divId']}');">Ocultar Filtros</a></li>
	           		<li><a href="#" onclick="abrirJanela(false, divIdRootAberto(), '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc011/content/etc011ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent('{$r_D132_Id}') + '&bi=conf&grid=true&tab=' + encodeURIComponent('{$r_tab}'), [1200,600]);">Configurar</a></li>
EOT;
			}
			if ($r_exibir != 'grid') {
				echo <<<EOT
	           		<li><a href="#" onclick="abrirJanela(false, divIdRootAberto(), '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc011/content/etc011ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent('{$r_D132_Id}') + '&bi=visu&grid=true&tab=' + encodeURIComponent('{$r_tab}'), [1200,600]);">Visualizar como Grid</a></li>
EOT;
			} else {
				echo <<<EOT
            		<li><a href="#" onclick="acoesGrid(this, false, true, false, false); return false;" class="acao-grid-csv">Gerar CSV (Excel)</a></li>
EOT;
			}
			echo <<<EOT
        </ul>
    </div>
</div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	$('#{$g['divId']} .dropdown-toggle').dropdown();
} </script></div>
EOT;
		}

		if ($debug) { log('(time) Ajustes: ' . round(microtime(true) - $time_start, 4)); }
		
		if ($r_exibir == 'grid') {
            if ($r_bi == 'visu' && isset($r_tab)) {
                echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						// Click na linha
						$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                            if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                            var \$tr = $(this).closest('tr');
                            var valor = \$tr.attr('id');
                            if (window.atob(valor).match(/Linhas.*Colunas/)) { return true; }
                            var dim = $(this).find('td[dim]').attr('dim');
                            var formEditor = $(this).closest('.formEditor').attr('id');
                            if (\$tr.hasClass('linhaNegrito')) {
                                \$tr.removeClass('linhaNegrito');
                            } else {
                                \$tr.parent().find('tr').removeClass('linhaNegrito');
                                \$tr.addClass('linhaNegrito');
                            }
                            $('#' + '{$g['divId']}').showLoading();
                            $.getJSON('/etc/etc011/grid_func-ajax/etc011AdicionarFiltroGlobal/?ajax=true&tab=' + encodeURIComponent('$r_tab') + '&valor=' + encodeURIComponent(valor) + '&dim=' + encodeURIComponent(dim) + '&callback=?', function(request) {
                                $.getJSON('/etc/etc011/grid_func-ajax/etc011ReprocessarCache/?ajax=true&callback=?', function(request) {
                                    $('#' + '{$g['divId']}').hideLoading();
//                                    divRefresh($('#' + '{$g['divId']}').closest('.ui-tabs-panel').attr('id'), true);
                                    // Refresh de todos os outros grids
                                    $('#' + '{$g['divId']}').closest('.formFormulario').find('.formEditor').each(function() {
                                        if (\$(this).attr('id') != formEditor) {
                                            divRefresh($(this).attr('id'), false, false, 'manual');
                                        }
                                    });
                                    // $('span[title="Recarregar"]').each(function() {
                                    //     var \$this = $(this);
                                    //     if (\$this.closest('.formEditor').attr('id') != formEditor) {
                                    //         \$this.trigger('click');
                                    //     }
                                    // });
                                    // Recarregar cards
                                    $('#' + '{$g['divId']}').closest('.formFormulario').find('.bi-indicador').each(function() {
                                        divRefresh($(this).parent().attr('id'), false, false, 'manual');
                                    });
                                });
                            });
                        });
					} </script></div>
EOT;
            }

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
				$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
			} else {
				$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
			}
			if (isset($r_generateHTML)) { 
				ob_end_clean();
				ob_start();
				$g['smarty']->display('gridHTML.tpl');
				return true;
			} else {
				$g['smarty']->display('grid.tpl');
			}
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
						            explode: 8,
						            shadowSize: 2,
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
						            show: true
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
									//xAxisTexto1[i + (variacao * colunas)] = value[colunas][1];
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
						            autoscaleMargin: 1,
						            tickFormatter: function(x) {
										if (x>=1000000){
											x = x / 1000000;
											return "<font style='font-size:12px'>" + number_format(x, 2, '.', '.') + "MI </font>";
										} else if (x>1000){
											x = x / 1000;
											return "<font style='font-size:12px'>" + number_format(x, 0, '.', '.') + "K </font>";
										} else {
											return "<font style='font-size:12px'>" + number_format(x, 0, ',', '.') + "</font>";
										}
						            }
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
						            shadowSize: 1,
						        },
						        points: {
						            show: true
						        },
						        legend: {
						        	show: {$incluirGraficoLegend_},
						            position: "ne",
						            backgroundColor: "#D2E8FF"
						        },
						        label: {
						        	show: true,
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
			if(isset($r_D132_Id) && $r_D132_Id > 0 && $r_Filtro_D132_Id != 1 && $r_bi == 'visu'){
				$dir = $g['pathDadosUsuario'] . 'gridFiltro/';
				$file = $dir . $md5;
				$filtroAtual =  mysql_real_escape_string(file_get_contents($file));

				if(isset($r_FiltroUsuario)){
					$dir = "{$g['pathDados']}personalizacaoGrid/";
					is_dir($dir) or mkdir($dir, 0777, true);
					$gridFiltroUsuario = $dir . "relatorioBI_{$r_D132_Id}-{$g['usuarioAtual']}";
					file_put_contents($gridFiltroUsuario, $grafico);
				} else {
                    $graficoSerialize = mysql_real_escape_string(serialize($grafico));
                    $g['usarClickHouse'] = false;
                    mysql_query("INSERT INTO D182 (D182_D132_Id, D182_C007_Id, D182_Grid_Salvo) VALUES('{$r_D132_Id}', '{$g['usuarioAtual']}', '{$graficoSerialize}') ON DUPLICATE KEY UPDATE D182_Grid_Salvo='{$graficoSerialize}'");
                    $g['usarClickHouse'] = true;
					//mysql_query("UPDATE D132 SET D132_Grid_Tabela = '{$graficoSerialize}' WHERE D132_Id = '{$r_D132_Id}'");
				}
				
			}
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

		$output = ob_get_contents();
		ob_end_clean();
		if(isset($r_D132_Id) && $r_D132_Id > 0 && $r_Filtro_D132_Id != 1 && $r_bi == 'visu'){
			$dir = $g['pathDadosUsuario'] . 'gridFiltro/';
			$file = $dir . $md5;
			$filtroAtual =  mysql_real_escape_string(file_get_contents($file));

			if(isset($r_FiltroUsuario)){
				$dir = "{$g['pathDados']}personalizacaoGrid/";
				is_dir($dir) or mkdir($dir, 0777, true);
				$gridFiltroUsuario = $dir . "relatorioBI_{$r_D132_Id}-{$g['usuarioAtual']}";
				file_put_contents($gridFiltroUsuario, str_replace("{$g['divId']}", "#DIV_ID", $output));
			} else {
                $gridTabela = mysql_real_escape_string($output);
                $g['usarClickHouse'] = false;
                mysql_query("INSERT INTO D182 (D182_D132_Id, D182_C007_Id, D182_Grid_Salvo, D182_DivId) VALUES('{$r_D132_Id}', '{$g['usuarioAtual']}', '{$gridTabela}', '{$g['divId']}') ON DUPLICATE KEY UPDATE D182_Grid_Salvo='{$gridTabela}', D182_DivId = '{$g['divId']}'");
                $g['usarClickHouse'] = true;
			}

			//log($g['pathDadosUsuario']);
			$output = preg_replace('#INICIO-BTNSALVAR.*FIM-BTNSALVAR#ims', ' ', $output);
			$output = preg_replace('#INICIO-FILTRO.*FIM-FILTRO#ims', ' ', $output);
		}

		echo $output;

		if ($debug) { log('(time) Final: ' . round(microtime(true) - $time_start, 4)); }

	break;
	
	case 'etc011GridSave':
        $g['usarClickHouse'] = false;

        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D132_Id', $g['divId']), 'style' => 'width: 1%;'),
            array('colunaValor' => 'D132_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'D132_Tipo', 'colunaTitulo' => 'Tipo', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D132_Flag_Exibir_Dashboard', 'colunaTitulo' => 'Exibir', 'style' => 'font-size: 1.0em;', 'callback' => 'campoMostraValorBI', 'callbackParameter' => 'D132_Flag_Exibir_Dashboard', 'colunaEditar' => true, 'style' => 'width: 1%;', 'colunaEditarGeraSelect' =>
                array(
                    array('title' => 'SIM', 'value' => 'S'),
                    array('title' => 'NÃO', 'value' => 'N'),
                )
            ),
            array('colunaValor' => 'D132_Descricao', 'colunaTitulo' => 'Descrição', 'style' => 'font-size: 1.0em;', 'colunaEditar' => true),
            array('colunaValor' => 'D132_Grupo', 'colunaTitulo' => 'Dashboard', 'colunaEditar' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D132_Flag_Exibir_Alcada', 'colunaTitulo' => 'Alçada', 'style' => 'font-size: 1.0em;', 'callback' => 'campoMostraValorBI', 'callbackParameter' => 'D132_Flag_Exibir_Alcada', 'colunaEditar' => true, 'colunaEditarGeraSelect' =>
                array(
                    array('title' => 'VENDEDOR/COMPRADOR', 'value' => '1'),
                    array('title' => 'SUPERVISOR', 'value' => '2'),
                    array('title' => 'DIRETOR', 'value' => '4'),
                )
            ),
            array('colunaValor' => 'configurar', 'colunaTitulo' => 'Configurar', 'style' => 'font-size: 1.0em;', 'callback' => 'configurarRelatorioBI', 'style' => 'width: 1%;', 'colunaTipo' => 'livre', 'callbackParameter' => $r_tabBI),
        );

        /**
         * Definição da SQL
         */
        $from = 'D132';
        $extra = <<<EOT
			ORDER BY D132_Descricao
EOT;

        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc011ReprocessarCache">Reprocessar Relatórios</button>
				<button type="button" id="etc011ReprocessarDataWarehouse">Reprocessar Data Warehouse</button>
				<button type="button" id="etc011IncluirIndicador">Incluir Indicador</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
				$('#etc011ReprocessarCache').unbind('click').bind('click', function(data) {
					abrirJanela(data, divIdRootAberto(), '', menuId('atualizarBI'), 'Atualizar', '.: Atualizar B.I. :.', '/etc/etc011/grid_func-ajax/etc011ReprocessarCache/?todosUsuarios=true', '', [900,500], false, 'center');

//					$('#' + '{$g['divId']}').showLoading();
//					$.get('/etc/etc011/grid_func-callback/etc011ReprocessarCache/?ajax=true&todosUsuarios=true&callback=?', function(request) {
//						$('#' + '{$g['divId']}').hideLoading();
//						if (request.code) {
//							dialogConfirm('Ok', 'Relatórios reprocessados.');							
//						}
//					});
				});
				$('#etc011ReprocessarDataWarehouse').unbind('click').bind('click', function(data) {
				    abrirJanela(data, divIdRootAberto(), '', menuId('atualizarBI'), 'Atualizar', '.: Atualizar B.I. :.', '/etc/etc011/grid_func-ajax/etc011ReprocessarDataWarehouse/', '', [900,500], false, 'center');
//					$('#' + '{$g['divId']}').showLoading();
//					$.getJSON('/etc/etc011/grid_func-ajax/etc011ReprocessarDataWarehouse/?ajax=true&callback=?', function(request) {
//						$('#' + '{$g['divId']}').hideLoading();
//						if (request.code) {
//							dialogConfirm('Ok', 'Data Warehouse reprocessado.');							
//						}
//					});
				});
				$('#etc011IncluirIndicador').unbind('click').bind('click', function(data) {
				    abrirJanela(data, divIdRootAberto(), '', menuId('incluirIndicadorBI'), 'Incluir Indicador', '.: Incluir Indicador :.', '/etc/etc011/content/etc011IncluirIndicador/', '', [900,600], false, 'center');
				});
			} </script></div>
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
        echo $g['ui']->gridFiltroPrint($gridFiltro);

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
				var id = $(this).closest('tr').attr('id').split('|');
				var tipo = id[1].replace(/D132_Tipo-/, '');
				var id = id[0].replace(/D132_Id-/, '');
				if (tipo == 1) {
				    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc011/content/etc011ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent(id) + '&bi=visu&tab=true&exibirFiltro=true&tab=' + encodeURIComponent('{$r_tab}'), [1024,600]);
                } else {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', '.: Editar Indicador :.', '/etc/etc011/content/etc011IncluirIndicador/', '&acaoId=' + encodeURIComponent(id), [900,600], false, 'center');                
                }
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
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
        break;

	case 'etc011GridNovaVisao':
        $g['usarClickHouse'] = false;

        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            //array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D132_Id', $g['divId']), 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'D132_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'D132_Descricao', 'colunaTitulo' => 'Descrição')
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
				var ids = $(this).closest('tr').attr('id').split('|');
				var D132_Id = ids[0].replace(/D132_Id-/, '');
				divSetarAcaoId('{$r_divRefresh}', D132_Id, 'D132_Id');
				divRefresh('{$r_divRefresh}');
			});
			//$('#{$g['divId']} .grid').css('font-size', '9px');
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

    case 'etc011FiltroGlobal':
        $globalMostrarTodosFiltros = true;

        $gridFiltro = array(
            'DIM002_Nome_Empresa, DIM002_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
            'DIM001_Data' => array('titulo' => 'Data Emissão', 'tipo' => 'data'),
            'DIM001_Dia' => array('titulo' => 'Data Emissão (dia)', 'tipo' => 'texto'),
            'DIM001_Mes' => array('titulo' => 'Data Emissão (mês)', 'tipo' => 'texto'),
            'DIM001_Ano' => array('titulo' => 'Data Emissão (ano)', 'tipo' => 'texto'),
            'CONCAT(DIM001_Mes, '/', DIM001_Ano)' => array('titulo' => 'Data Emissão (mês/ano)', 'tipo' => 'texto'),
            'DIM001_Dia_Da_Semana' => array('titulo' => 'Data Emissão (dia da semana)', 'tipo' => 'texto'),
            'DIM003_Estado' => array('titulo' => 'Estado', 'tipo' => 'texto'),
            'DIM003_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
            'DIM003_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
            //'DIM003_Endereco' => array('titulo' => 'Endereço', 'tipo' => 'texto'),
            'D4Vendedor.DIM004_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D4Vendedor.DIM004_C007_Id' => array('titulo' => 'Vendedor (id)', 'tipo' => 'texto'),
            'D4Supervisor.DIM004_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'D4Supervisor.DIM004_C007_Id' => array('titulo' => 'Supervisor (id)', 'tipo' => 'texto'),
            'FAC001_DIM004_Id_Supervisor' => array('titulo' => 'Supervisor (id)', 'tipo' => 'texto'),
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
            'DIM005_Data' => array('titulo' => 'Entrada/Saída/Vencimento', 'tipo' => 'data'),
            'FAC001_FLAG_CANCELADO' => array('titulo' => 'Cancelado', 'tipo' => 'texto'),
            'DIM012_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
            'DIM013_Nome_Caixa' => array('titulo' => 'Caixa', 'tipo' => 'texto'),
            'DIM013_Nome_Banco' => array('titulo' => 'Banco', 'tipo' => 'texto'),
        );

        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, md5("bi-{$r_tab}"));
        echo $g['ui']->gridFiltroPrint($gridFiltro, md5("bi-{$r_tab}"));

        if ($r_gridFiltrado) {
            echo <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    $('#' + '{$g['divId']}').showLoading();
                    $.getJSON('/etc/etc011/grid_func-ajax/etc011ReprocessarCache/?ajax=true&callback=?', function(request) {
						$('#' + '{$g['divId']}').hideLoading();
						divRefresh('{$g['divIdParent']}', true);
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
					});
                } </script></div>
EOT;
        }
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
			$valor = '1 - Segunda-feira';
		break;
		case 2:
			$valor = '2 - Terça-feira';
		break;
		case 3:
			$valor = '3 - Quarta-feira';
		break;
		case 4:
			$valor = '4 - Quinta-feira';
		break;
		case 5:
			$valor = '5 - Sexta-feira';
		break;
		case 6:
			$valor = '6 - Sábado';
		break;
		case 7:
			$valor = '7 - Domingo';
		break;
	}
	return $valor;
}

function formataDiaMes($valor) {
	switch($valor) {
		case 1:
			$valor = '01 - Janeiro';
		break;
		case 2:
			$valor = '02 - Fevereiro';
		break;
		case 3:
			$valor = '03 - Março';
		break;
		case 4:
			$valor = '04 - Abril';
		break;
		case 5:
			$valor = '05 - Maio';
		break;
		case 6:
			$valor = '06 - Junho';
		break;
		case 7:
			$valor = '07 - Julho';
		break;
		case 8:
			$valor = '08 - Agosto';
		break;
		case 9:
			$valor = '09 - Setembro';
		break;
		case 10:
			$valor = '10 - Outubro';
		break;
		case 11:
			$valor = '11 - Novembro';
		break;
		case 12:
			$valor = '12 - Dezembro';
		break;
	}
	return $valor;
}

function formataDiaSemanaReverso($valor) {
    switch($valor) {
        case '1 - Segunda-feira':
            $valor = 1;
            break;
        case '2 - Terça-feira':
            $valor = 2;
            break;
        case '3 - Quarta-feira':
            $valor = 3;
            break;
        case '4 - Quinta-feira':
            $valor = 4;
            break;
        case '5 - Sexta-feira':
            $valor = 5;
            break;
        case '6 - Sábado':
            $valor = 6;
            break;
        case '7 - Domingo':
            $valor = 7;
            break;
    }
    return $valor;
}

function formataMesReverso($valor) {
    switch($valor) {
        case'01 - Janeiro':
            $valor = '1';
            break;
        case'02 - Fevereiro':
            $valor = '2';
            break;
        case'03 - Março':
            $valor = '3';
            break;
        case'04 - Abril':
            $valor = '4';
            break;
        case'05 - Maio':
            $valor = '5';
            break;
        case'06 - Junho':
            $valor = '6';
            break;
        case'07 - Julho':
            $valor = '7';
            break;
        case'08 - Agosto':
            $valor = '8';
            break;
        case'09 - Setembro':
            $valor = '9';
            break;
        case '10 - Outubro':
            $valor = '10';
            break;
        case '11 - Novembro':
            $valor = '11';
            break;
        case '12 - Dezembro':
            $valor = '12';
            break;
    }
    return $valor;
}

function popularExtra($extra) {
    global $fatos, $fato_, $g;
    $extra = gInsertExtraWhere($extra, " {$fatos[$fato_][0]} != 0 ");
    $extra = gPopularExtraBI($extra);
    return $extra;
}