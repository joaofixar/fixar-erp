<?php
namespace hardness;

require_once('prog/crm/crm002/grid_func-callback.php');
require_once('prog/crm/crm002/grid_func-js.php');

switch($g['r'][3]){
	case 'crm002GridAgenda':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D085_Id', $g['divId'])),
            array('colunaValor' => 'substr(C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
            array('colunaValor' => 'D085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => 'concat(CONVERT(substr(D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\'),\' \',ifnull(D013_DDD_Celular,\'\'),\'-\',ifnull(D013_Celular,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:180px;white-space:normal;min-width:180px;width:180px'),
            array('colunaValor' => 'CONCAT(D085_Data, " ", D085_Hora)', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
        	array('colunaValor' => 'IF(D085_Data_Previsao_Retorno = "0000-00-00", "", CONCAT(D085_Data_Previsao_Retorno, " ", D085_Hora_Previsao_Retorno))', 'colunaTitulo' => 'Retornar', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => 'Mensagem', 'style' => 'max-width:250px;white-space:normal;'),
            array('colunaValor' => 'D085_C007_Id', 'colunaTitulo' => 'IdUsuarioAtual', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Entidade', 'colunaTitulo' => 'Entidade', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Id_Entidade', 'colunaTitulo' => 'ID Entidade', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Flag_Manual', 'colunaTitulo' => 'Manual', 'colunaExibir' => false),
            array('colunaValor' => 'D085_T003_Id', 'colunaTitulo' => 'Orç.', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'D085_T003_Id', '1000,615')),
            array('colunaValor' => 'D085_T005_Id', 'colunaTitulo' => 'Ped.', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'D085_T005_Id', '1000,615')),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Nota', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', 'D085_T007_Id', '1000,615')),
            array('colunaValor' => 'D085_T139_Id', 'colunaTitulo' => 'Email', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Email', '/etc/etc006/content/etc006ContentEmail/', 'D085_T139_Id', '1000,615')),
            array('colunaValor' => 'D085_T002_Id', 'colunaTitulo' => 'Orç', 'colunaExibir' => false),
            array('colunaValor' => 'D085_T007_Id', 'colunaTitulo' => 'Nota', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'Duplicata', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Título a Receber', '/fin/fin001/content/fin001content02/', 'D085_T002_Id', '1050,583')),	
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'D085';

		if($g['C031']['exibirRegistroAgendaManual'] != 'S'){
            $extraFlagManual = " AND D085_Flag_Manual = 'N' ";
        }

		$extra = <<<EOT
			LEFT JOIN T002 ON T002_Id=D085_T002_Id
			LEFT JOIN T007 ON T007_Id=D085_T007_Id
			left join C007 on C007_Id=D085_C007_Id
			LEFT JOIN D024 on D024_Id=D085_D024_Id
			LEFT JOIN D021 ON D021_Id=D085_D021_Id
			LEFT JOIN D013 ON D013_Id=D085_D013_Id
			where D085_D024_Id = {$r_D024_Id}
			{$extraFlagManual}
			order by D085_Data desc,D085_Id desc
				
EOT;
		//Define ação do click de abertura (ao clicar em uma linha do grid)
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//$('.gridOptTop').remove();
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D085_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&D024_Id={$r_D024_Id}&acaoId=' + encodeURIComponent(id), [700,450]);
				});
			} </script></div>
EOT;

		/**
        * Botões Almir
        */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'crm002IncluirAgendaCliente'),
		);
		echo Botoes::processar($botoes);
			
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="crm002IncluirAgendaCliente">Incluir</button>	
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#crm002IncluirAgendaCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&D024_Id={$r_D024_Id}&T003_Id={$r_acaoId}&tab=geral', [700,450]);
				});
				} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D085_Id' => array('titulo' => 'ID Agenda', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D085_Data' => array('titulo' => 'Data Inclusão', 'tipo' => 'data'),
			'D085_Data_Previsao_Retorno' => array('titulo' => 'Data Previsão Retorno', 'tipo' => 'data'),
			'D085_Data_Retorno' => array('titulo' => 'Data Retorno', 'tipo' => 'data'),
			'D085_Mensagem' => array('titulo' => 'Mensagem', 'tipo' => 'texto'),
			'D085_Retorno' => array('titulo' => 'Retorno', 'tipo' => 'texto'),
			'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
		);
		
		 // Gera o filtro e acrescenta ao $extra
		 $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		 //$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
		 // Imprime o filtro do GRID (normalmente executado acima do GRID)
		 echo $g['ui']->gridFiltroPrint($gridFiltro);

		 //Totais personalizados clausulas where, extra

		 $orcamentos = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T003_Id > 0"));
		 $pedidos = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T005_Id > 0"));
		 $notas = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T007_Id > 0"));
		 $emails = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T139_Id > 0"));
		 $ligacoes = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_Flag_Manual = 'S'"));
		/**
         * Totais Almir
         */
       	$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Qte_Orcamentos','tipo'=>'totais','valor' => $orcamentos, 'titulo' => 'Orçamentos'),
			array('nome' => 'Qte_Pedidos','tipo'=>'totais','valor' => $pedidos, 'titulo' => 'Pedidos'),
			array('nome' => 'Qte_Notas','tipo'=>'totais','valor' => $notas, 'titulo' => 'Notas Fiscais'),
			array('nome' => 'Qte_Emails','tipo'=>'totais','valor' => $emails, 'titulo' => 'E-mails'),
			array('nome' => 'Qte_Ligacoes','tipo'=>'totais','valor' => $ligacoes, 'titulo' => 'Ligações'),
        );

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*if (!personalizacaoTotais()) {
			$total = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			$orcamentos = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T003_Id > 0"));
			$pedidos = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T005_Id > 0"));
			$notas = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T007_Id > 0"));
			$emails = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T139_Id > 0"));
			$ligacoes = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_Flag_Manual = 'S'"));

			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Registros<br><input type="text" value="{$total}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Orçamentos<br><input type="text" value="{$orcamentos}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Pedidos<br><input type="text" value="{$pedidos}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Notas Fiscais<br><input type="text" value="{$notas}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Emails<br><input type="text" value="{$emails}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Ligações<br><input type="text" value="{$ligacoes}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			$g['smarty']->assign('totais', $totais);
		}*/
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
		
	case 'crm002GridContatos':
		/**
		 * Definição do Grid (Tabela)
		 */		
			
		$grid = array(
			array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D013_Id', $g['divId'])),
			array('colunaValor'=>'D013_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
			array('colunaValor'=>'D013_Nome_Contato', 'colunaTitulo'=>'Nome'),
			array('colunaValor'=>'D013_E_Mail', 'colunaTitulo'=>'Email'),
			array('colunaValor'=>'D013_Telefone_1', 'colunaTitulo'=>'Telefone'),
			array('colunaValor'=>'D013_Data_Nascimento', 'colunaTitulo'=>'Nascimento', 'callback'=>'gCorrigeData'),
			array('colunaValor'=>'D013_Tipo_Contato', 'colunaTitulo'=>'Tipo'),
			
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D013';
			
		$extra = <<<EOT
			WHERE D013_D024_Id='{$r_D024_Id}'
EOT;
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//$('.gridOptTop').remove();
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D013_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Contato- ID: ' + id, '/crm/crm002/content/crm002contentEditarContato/', '&acaoId=' + encodeURIComponent(id), [700,400]);
				});
			} </script></div>
EOT;
		 echo <<<EOT
		 <div class="buttonsBottomTop">
		 	<button type="button" id="crm002IncluirContato">Incluir</button>	
		 </div>
		 <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#crm002IncluirContato').unbind('click').bind('click', function(data) {	
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastrar Contato', '/crm/crm002/content/crm002contentEditarContato/', '&tab=geral&D024_Id=$r_D024_Id', [500,500]);
			});
		} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		
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
         
         /**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesMid = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// $novoIdTelaForm = uniqid();
		// echo <<<EOT
		// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			// $('#cad001Incluir').unbind('click').bind('click', function(data) {
				// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001content02/','', [900,550]);
			// });
		// } </script></div>
// EOT;
	break;

	case 'crm002GridPrincipalOrcamentosCliente':
		$filtroObrigatorio = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'campoCancelamentoOrcamento', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamentoOrcamento', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orc', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_D024_Id', 'colunaTitulo' => 'Cod.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,15)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'max-width:130px;min-width:130px;width:130px;white-space:normal;', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'concat(CONVERT(substr(orcamento.D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(orcamento.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(orcamento.D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:165px;white-space:normal;min-width:165px;width:165px'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_Valor_Total_Produtos_Sem_Desconto', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaGridOrcamento', 'style' => 'width:1%'),
            array('colunaValor' => '(T003_Valor_Desconto/T003_Valor_Total_Produtos_Sem_Desconto)*100', 'colunaTitulo' => 'Desc%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaMetrica' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Valor_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'T003_Percentual_Margem', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4'),
	        array('colunaValor' => 'T003_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T003_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Prazos(T003_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%'),
            array('colunaValor' => "(SELECT if(T139_Id>0,'Ok','<font style=\"color: var(--BGVermelho)\">--</font>') FROM T139 WHERE T139_T003_Id = T003_Id LIMIT 1) as Mail", 'colunaTitulo' => 'Mail', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => "(SELECT if(D085_Id>0,'Ok','<font style=\"color: var(--BGVermelho)\">--</font>') FROM D085 WHERE D085_T003_Id = T003_Id LIMIT 1) as Retorno", 'colunaTitulo' => 'Retorno', 'colunaExibir' => false, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'IFNULL(IF(T003_Status_Retorno_Email=\'RETORNAR\',CONCAT(\'<font style="color: var(--BGVermelho)">\',T003_Status_Retorno_Email,\'</font>\'),T003_Status_Retorno_Email), \'<font style="color: var(--BGVermelho)">--</font>\')', 'colunaTitulo' => 'Agenda', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'campoMostraTitle', 'callbackParameter' => 'D085_Mensagem'),
            array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")," - ",T005_Valor_Total) separator ", <br />") as Pedido', 'colunaTitulo' => 'Pedido', 'callback' => 'validaCampoPedido', 'style' => 'max-width:210px;white-space:normal;min-width:210px;width:210px'),
            array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'width:1%', 'callback' => 'validaCampoNFOrcamentos'),
            //array('colunaValor' => "(SELECT group_concat(distinct T007_Numero_Nota_Fiscal separator ', ') FROM T007 WHERE T007_T005_Id = T005_Id) as NF", 'colunaTitulo' => 'NF', 'style' => 'width:1%', 'callback' => 'validaCampoNFOrcamentos'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,12)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'colunaDimensao' => ''),
            array('colunaValor' => 'globalStatusOrcamento as Status', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Status', 'callback' => 'campoStatus', 'style' => 'max-width:200px;white-space:normal;min-width:200px;width:200px'),
            array('colunaValor' => 'T003_Flag_Alcada', 'colunaTitulo' => 'Alcada', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'globalStatusOrcamento', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Status', 'callback' => 'campoStatus', 'colunaExibir' => false),
            array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => 'T003_Flag_Perdido', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T003_Observacao_Orcamento_Perdido', 'colunaTitulo' => 'T003_Observacao_Orcamento_Perdido', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T003_Flag_ACP', 'colunaTitulo' => 'T003_Flag_ACP', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T003_Flag_Status_Orcamento', 'colunaExibir' => false, 'colunaTitulo' => 'T003_Flag_Status_Orcamento'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
        						FROM T004 
        						LEFT JOIN D009 ON D009_Id = T004_D009_Id
        						LEFT JOIN D049 ON D049_Id = D009_D049_Id
        						LEFT JOIN D082 ON D082_Id = D049_D082_Id
        						WHERE T004_T003_Id=T003_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
            array('colunaValor' => 'T003_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'T003_C007_Id_Vendedor_Interno', 'colunaExibir' => false),
            array('colunaValor' => 'T219_T218_Id', 'colunaTitulo' => 'Cotação', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => '(SELECT D085_Mensagem FROM D085 WHERE D085_T003_Id = T003_Id AND D085_T003_Id IS NOT NULL ORDER BY D085_Id DESC LIMIT 1) as D085_Mensagem', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T003';
		$extra = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D013 as Orcamento on Orcamento.D013_Id=T003_D013_Id
			left join D013 as Cliente on Cliente.D013_D024_Id=T003_D024_Id
			left join T004 on T004_T003_Id=T003_Id
			left join T006 on T006_T004_Id=T004_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
			left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			LEFT JOIN T005 ON T006_T005_Id=T005_Id AND T005_Flag_Status!='8' 
			left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id 
			left join T219 on T219_Id=T004_T219_Id
			left join D085 on D085_T003_Id=T003_Id
			where T003_C004_Id='{$g['empresaAtual']}' AND
			IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', T003_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}', '1=1')
			AND T003_D024_Id='{$r_D024_Id}'
			group by T003_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
			left join D006 on D006_Id=T003_D006_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
		    where (T003_C004_Id='{$g['empresaAtual']}') AND
			IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', T003_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}', '1=1')
			AND T003_D024_Id='{$r_D024_Id}'
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalAberto = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
			left join D006 on D006_Id=T003_D006_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
		    where (T003_C004_Id='{$g['empresaAtual']}' AND
			IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', T003_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}', '1=1')
		    and T003_Flag_Perdido!='S'
		    and (select T005_Id from T005 where T005_T003_Id=T003_Id limit 1) is null)
			AND T003_D024_Id='{$r_D024_Id}'
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalFechado = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
			left join T004 on T004_T003_Id=T003_Id
			left join T006 on T006_T004_Id=T004_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    LEFT JOIN T005 ON T006_T005_Id=T005_Id
		    left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
		    where (T003_C004_Id='{$g['empresaAtual']}' AND
			IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', T003_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}', '1=1')
		    and T006_Id > 0)
			AND T003_D024_Id='{$r_D024_Id}'
		    GROUP BY T003_Id
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalPerdido = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
			left join D006 on D006_Id=T003_D006_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
		    where (T003_C004_Id='{$g['empresaAtual']}' AND
			IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', T003_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}', '1=1')
		    and T003_Flag_Perdido = 'S')
			AND T003_D024_Id='{$r_D024_Id}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T005_Flag_Status']=='5'){
				return 'linhaCinza';
			} else if($array['todosCampos']['T003_Flag_Perdido']=='S'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T005_Id']>0){
			} else {
				return 'linhaAzul';
			}
		}
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir <span style="float: right; margin: 0 0 0 8px; color: rgb(68, 118, 164); font-size: 1em;">(F4)</span> ', 'id' => 'ven001GridPrincipalOrcamentosCliente'),
			array('titulo' => 'Incluir Consumidor', 'id' => 'ven001GridPrincipalOrcamentosConsumidorCliente'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*if (!personalizacaoBotoes()) {
			
				<div class="buttonsBottomTop">
					<button type="button" id="ven001GridPrincipalOrcamentosCliente">Incluir<span style="float: right; margin: 0 0 0 8px; color: rgb(68, 118, 164); font-size: 1em;">(F4)</span></button>
					<button type="button" id="ven001GridPrincipalOrcamentosConsumidorCliente">Incluir Consumidor</button>
				</div>*/
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
					//divId = {$g['divId']}
					setTimeout(function(){
						$('#Procura_Cliente_Pesquisa').focus();
					}, 200);
						
					$('#ven001GridPrincipalOrcamentosCliente').unbind('click').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_CRM={$r_D024_Id}', [850,550]);
					});
					$('#ven001GridPrincipalOrcamentosConsumidorCliente').unbind('click').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&consumidor=true', [850,550]);
					});
				} </script></div>
EOT;
		//}
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
            'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'orcamento.D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'T003_Status_Orcamento(T003_Flag_Status_Orcamento)' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'ifnull(T003_Flag_Perdido[[virgula]]\'N\')' => array('titulo' => 'Cancelado', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ),
            'naoGerarExtra' => ''),
            'T003_Valor_Total' => array('titulo' => 'Valor total', 'tipo' => 'moeda'),
            'T003_Status_Retorno_Email' => array('titulo' => 'Retorno', 'tipo' => 'texto'),
            'T004_Descricao_Produto,T004_Codigo_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
            'T003_Percentual_Margem' => array('titulo' => '% Margem', 'tipo' => 'moeda'),
            'T003_IPV' => array('titulo' => 'IPV', 'tipo' => 'moeda'),           
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Monta fórmula total produtos sem impostos como base do cálculo da Margem
		$baseMargem  = "({Valor_Produtos}";
		$baseMargem .= ($g['C031']['somarICMSPrecoVenda']   == 'S') ? "-{Valor_ICMS_}"   : "" ;
		$baseMargem .= ($g['C031']['somarPISPrecoVenda']    == 'S') ? "-{Valor_PIS_}"    : "" ;
		$baseMargem .= ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? "-{Valor_COFINS_}" : "" ;
		$baseMargem .= ($g['C031']['somarIRPJPrecoVenda']   == 'S') ? "-{Valor_IRPJ_}"   : "" ;
		$baseMargem .= ($g['C031']['somarCSLLPrecoVenda']   == 'S') ? "-{Valor_CSLL_}"   : "" ;
		$baseMargem .= ")";

		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_Tabela', 'valor' => 'T003_Valor_Total_Produtos_Sem_Desconto', 'titulo' => 'Tabela R$'),
            array('nome' => 'Valor_Desconto', 'valor' => 'T003_Valor_Desconto', 'titulo' => 'Desconto R$', 'exibir'=>false),
            array('nome' => 'Percentual_Desconto', 'tipo' => 'totais', 'valor' => '({Valor_Desconto} / {Valor_Tabela}) * 100', 'titulo' => '% Desconto'),
            array('nome' => 'Valor_ICMS_', 'valor' => 'T003_Valor_Total_ICMS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_PIS_', 'valor' => 'T003_Valor_Total_PIS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_COFINS_', 'valor' => 'T003_Valor_Total_COFINS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_IRPJ_', 'valor' => 'T003_Valor_Total_IRPJ', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_CSLL_', 'valor' => 'T003_Valor_Total_CSLL', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_Produtos', 'valor' => 'T003_Valor_Total_Produtos', 'titulo' => 'Produtos R$'),
            array('nome' => 'Valor_Custo', 'valor' => 'T003_Valor_Total_Custo', 'titulo' => 'Custo R$'),
            array('nome' => 'Percentual_Margem', 'tipo' => 'totais', 'valor' => '(('.$baseMargem.' - {Valor_Custo}) / '.$baseMargem.') * 100', 'titulo' => '% Margem'),
			array('nome' => 'Valor_ST', 'valor' => 'T003_Valor_ST', 'titulo' => 'Valor ST'),
            array('nome' => 'Valor_Total', 'valor' => 'T003_Valor_Total', 'titulo' => 'Total R$'),
			array('nome' => 'Valor_ICMS_', 'valor' => 'T003_Valor_Total_ICMS', 'titulo' => 'ICMS R$', 'exibir' => true),
            array('nome' => 'Valor_Comissao', 'valor' => 'T003_Valor_Total_Comissao', 'titulo' => 'Comissão R$'),
            array('nome' => 'Percentual_Comissao', 'tipo' => 'totais', 'valor' => '({Valor_Comissao}/{Valor_Produtos})*100', 'titulo' => '% Comissão'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/*$campos = "COUNT(*), SUM(T003_Valor_Total_Produtos_Sem_Desconto), (sum(T003_Valor_Desconto)/sum(T003_Valor_Total_Produtos_Sem_Desconto))*100, sum(T003_Valor_Total_Produtos), sum(T003_Valor_Total_Custo), ((sum(T003_Valor_Total_Produtos)-sum(T003_Valor_Total_Custo))/sum(T003_Valor_Total_Produtos))*100, sum(T003_Valor_Total_IPI), sum(T003_Valor_ST), sum(T003_Valor_Total), sum(T003_Valor_Total_ICMS), sum(T003_Valor_Total_Comissao), (sum(T003_Valor_Total_Comissao)/sum(T003_Valor_Total_Produtos))*100";
		list($numeroRegistros, $valorTotalSemDesconto, $valorTotalDesconto, $valorTotalProdutos, $valorTotalCusto, $valorTotalMargem, $valorTotalIPI, $valorTotalST, $valorTotal, $valorTotalICMS, $valorTotalComissao, $valorTotalPercentualComissao) = $g['sqlAuto']->pegarSqlCampos($campos, $from, $extraTotal);
		$valorTotalSemDesconto = gCorrigeNumero($valorTotalSemDesconto);
		$valorTotalDesconto = gCorrigeNumero($valorTotalDesconto);
		$valorTotalProdutos = gCorrigeNumero($valorTotalProdutos);
		$valorTotalCusto = gCorrigeNumero($valorTotalCusto);
		$valorTotalMargem = gCorrigeNumero($valorTotalMargem);
		$valorTotalIPI = gCorrigeNumero($valorTotalIPI);
		$valorTotalST = gCorrigeNumero($valorTotalST);
		$valorTotal = gCorrigeNumero($valorTotal);
		$valorTotalICMS = gCorrigeNumero($valorTotalICMS);
		$valorTotalComissao = gCorrigeNumero($valorTotalComissao);
		$valorTotalPercentualComissao = gCorrigeNumero($valorTotalPercentualComissao);*/
		/**
		* Totais(Versão Acima do GRID)
		*/
		/*if (!personalizacaoTotais(false, true)) {
			if($g['C007']['C007_Flag_Vendedor'] == 'V'){
				echo <<<EOT
					<div class="buttonsBottomTop">
						<span class="item" style="text-align:right">Orçamentos<br><input type="text" class="whiteBg" size="5" value="{$numeroRegistros}" readonly></span>
						<span class="item" style="text-align:right">Valor produtos<br><input type="text" class="whiteBg"  size="11" value="{$valorTotalSemDesconto}" readonly></span>
						<span class="item" style="text-align:right">%Desconto<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalDesconto}%" readonly></span>
						<span class="item" style="text-align:right">Valor produtos<br><input type="text" class="whiteBg" size="10" value="{$valorTotalProdutos}" readonly></span>
						<span class="item" style="text-align:right">Valor total<br><input type="text" class="whiteBg"  size="10" value="{$valorTotal}" readonly></span>
						<span class="item" style="text-align:right">%Comissão<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalPercentualComissao}" readonly></span>
					<span class="item" style="text-align:right">Valor comissão<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalComissao}" readonly></span>
					    <div style="clear:both;"></div>
					</div>
EOT;
			} else {
				echo <<<EOT
					<div class="buttonsBottomTop">
						<span class="item" style="text-align:right">Orçamentos<br><input type="text" class="whiteBg" size="5" value="{$numeroRegistros}" readonly></span>
						<span class="item" style="text-align:right">Valor produtos<br><input type="text" class="whiteBg"  size="11" value="{$valorTotalSemDesconto}" readonly></span>
						<span class="item" style="text-align:right">%Desconto<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalDesconto}%" readonly></span>
						<span class="item" style="text-align:right">Valor produtos<br><input type="text" class="whiteBg" size="10" value="{$valorTotalProdutos}" readonly></span>
						<span class="item" style="text-align:right">Valor custo<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalCusto}" readonly></span>
					    <span class="item" style="text-align:right">%Margem<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalMargem}%" readonly></span>
					    <!-- <span class="item" style="text-align:right">Valor IPI<br><input type="text" class="whiteBg"  size="9" value="{$valorTotalIPI}" readonly></span> -->
					    <span class="item" style="text-align:right">Valor ST<br><input type="text" class="whiteBg"  size="9" value="{$valorTotalST}" readonly></span>
						<span class="item" style="text-align:right">Valor total<br><input type="text" class="whiteBg"  size="10" value="{$valorTotal}" readonly></span>
					    <span class="item" style="text-align:right">Valor ICM<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalICMS}" readonly></span>
						<span class="item" style="text-align:right">%Comissão<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalPercentualComissao}" readonly></span>
						<span class="item" style="text-align:right">Valor comissão<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalComissao}" readonly></span>
					    <div style="clear:both;"></div>
					</div>
EOT;
			}
		}
		fimTotais();*/
			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T003_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Orçamento - ID: ' + id, '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
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

	case 'crm002GridPrincipalPedidosCliente':
		$filtroObrigatorio = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'campoCancelamento', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamento', 'style' => 'width:1%'),
            array('colunaValor' => 'DividirPedidoCliente', 'colunaTitulo' => '', 'callback' => 'dividirPedidoCliente', 'colunaTipo' => 'livre', 'style' => 'width:1%;'),
            array('colunaValor' => 'T005_T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Num', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'T005_Ordem_Compra', 'colunaTitulo' => 'OC.  Cli', 'colunaAlinhar' => 'center', 'style' => 'width:1%;', 'colunaEditar' => true),
            array('colunaValor' => 'T005_Flag_Dividido', 'colunaTitulo' => 'Div', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T005_Data_Entrega', 'colunaTitulo' => 'Entrega', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaEditar' => true),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'Cod.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,15)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'max-width:130px;min-width:130px;width:130px;white-space:normal;', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'concat(CONVERT(substr(orcamento.D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(orcamento.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(orcamento.D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:170px;white-space:normal;min-width:170px;width:170px'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T005_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false, 'style' => 'width:1%', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T005_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'T005_Percentual_Margem', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T005_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T005_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Prazos(T005_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%'),
            array('colunaValor' => 'group_concat(distinct "<span style=\\"\\">", T005_Status_Pedido(T005_Flag_Status,1), "</span> ", ifnull((SELECT CONCAT(date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>"), if(T005_Flag_Status=8, concat("<br />", T178_Observacao), "")) FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1), ""), "<br />", if(T005_Flag_Status=9, T005_Motivo_Nao_Liberado,"")) as Status2', 'colunaTitulo' => 'Status', 'style' => 'max-width:200px;white-space:normal;min-width:200px;width:200px'),
            array('colunaValor' => 'T005_Flag_Alcada', 'colunaTitulo' => 'Alcada', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'width:1%', 'callback' => 'validaCampoNFOrcamentos'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'colunaDimensao' => ''),
            array('colunaValor' => 'supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Flag Status', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_ACP', 'colunaTitulo' => 'T005_Flag_ACP', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            /*array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
        						FROM T006 
        						LEFT JOIN D009 ON D009_Id = T006_D009_Id
        						LEFT JOIN D049 ON D049_Id = D009_D049_Id
        						LEFT JOIN D082 ON D082_Id = D049_D082_Id
        						WHERE T006_T005_Id=T005_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),*/
            array('colunaValor' => 'T005_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'T005_C007_Id_Vendedor_Interno', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'T005_Flag_Reenviado_Almoxarifado', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'T005';
		$extra = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D013 as orcamento on orcamento.D013_Id=T005_D013_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C007 as Liberou on Liberou.C007_Id=T005_Liberou_Alcada_C007_Id
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T005_D006_Id
			left join T006 on T006_T005_Id=T005_Id
			where (T005_C004_Id='{$g['empresaAtual']}' 
			and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null))
			AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', T005_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}', '1=1')
			AND T005_D024_Id='{$r_D024_Id}'
			group by T005_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
    		left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
     		left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    		left join D021 on D021_Id = T005_D021_Id
    		left join D006 on D006_Id=T005_D006_Id
    		where (T005_C004_Id={$g['empresaAtual']} 
    		AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null))
 			AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', T005_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}', '1=1')
 			AND T005_D024_Id='{$r_D024_Id}'
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
		    if($array['todosCampos']['T005_Flag_Status']=='5'){
		        return 'linhaCinza';
		    } else if($array['todosCampos']['T005_Flag_Status']=='8'){
		        return 'linhaVermelho';
		    } else if($array['todosCampos']['T005_Flag_Status']=='26'){
		        return 'linhaLaranja';
		    } else if($array['todosCampos']['T005_Flag_Status']=='9'){
		    } else if($array['todosCampos']['T005_Flag_Status']=='14'){
		       return 'linhaVerde';
		  		// } else if ($array['todosCampos']['T005_Flag_Reenviado_Almoxarifado'] == 'S') {
		    } else if($array['todosCampos']['T005_Flag_Status']<=0){
		       return 'linhaAzul';
		    }
		}

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'ven002IncluirPedido',  'exibirCallback' => 'return $g["C031"]["usarBotaoIncluirPedido"] == "S";'),
			array('titulo' => 'Análise compra emergência', 'id' => 'crm001AnaliseComprasCliente',  'exibirCallback' => 'return $g["nivelAlcada"] == 4;'),
		);
		echo Botoes::processar($botoes);

      /*  echo <<<EOT
        	<div class="buttonsBottomTop">
EOT;
		if($g['C031']['usarBotaoIncluirPedido'] == "S"){
			echo <<<EOT
        		<button type="button" id="ven002IncluirPedido">Incluir</button>
EOT;
		}
		if($g['nivelAlcada'] == 4){
			echo <<<EOT
	            <button type="button" id="crm001AnaliseComprasCliente">Análise compra emergência</button>
EOT;
		
		echo <<<EOT
	            <!--<button type="button" id="ven002ProcessarPedidosVencidos" onclick="processarPedidosVencidos('{$g['divId']}');">Processar Vencidos</button>-->
	            <!--<button type="button" id="ven002DuplicarPedidos">Duplicar pedidos</button>-->
	            <!--<button type="button" id="ven002AgruparPedidos">Agrupar pedidos</button>-->
	            <!--<button type="button" id="ven002MudarEmpresaPedidos">Mudar empresa</button>-->
	            <!--<button type="button" id="ven002SelecionarTodos">Selecionar Todos</button>-->
	            <!--<button type="button" id="ven002DesmarcarTodos">Desmarcar Todos</button>-->
	        </div>
EOT;}*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		//$msg = gGeraAlertMsg('Erro!', 'Não existem itens para serem duplicados!');
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T005_Id = id[0].replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [1000,615]);            
			});
		} </script></div>
EOT;

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#ven002DuplicarPedidos').unbind('click').bind('click', function(data) {
				$.getJSON('/crm/crm001/grid_func-ajax/crm001DuplicarPedido/?ajax=true&callback=?', function(request) {
					if (request.code) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Duplicar Pedidos', '/crm/crm001/content/crm001ContentSelecionarDiasDuplicar/', '&T003_Id=' + encodeURIComponent('$r_acaoId'), [300,150]);
					}else{
						dialogConfirm('Erro', 'Não existem pedidos selecionados para serem duplicados!');
					}
				});
			});
			$('#ven002AgruparPedidos').unbind('click').bind('click', function(data) {
				$.getJSON('/crm/crm001/grid_func-ajax/crm001VerificaAgruparPedidos/?ajax=true&callback=?', function(request) {
					if (request.code) {
						var \$buttons = {
							"Sim": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/crm/crm001/grid_func-ajax/crm001AgruparPedidos/?ajax=true&callback=?', function(request) {
									if (request.code) {
										dialogConfirm('Operação Ok!', 'Processo concluído com sucesso');
										divRefresh('{$g['divId']}');
										$('#' + '{$g['divId']}').hideLoading();
									}else{
										dialogConfirm('Erro', request.data);
										$('#' + '{$g['divId']}').hideLoading();
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Você tem certeza?', 'Atenção, este processo irá gerar um novo pedido com todos os produtos dos pedidos selecionados.<br /><br />Você tem certeza?', \$buttons);
					}else{
						dialogConfirm('Erro', 'Por favor, selecione pedidos do mesmo cliente');
					}
				});
			});
			$('#ven002IncluirPedido').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Pedido', '/ven/ven002/content/ven002contentIncluirPedido/', '', [1000,615]);            
			});
			$('#ven002MudarEmpresaPedidos').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Mudar Pedidos de Empresa', '/ven/ven002/content/ven002ContentMudarPedidoEmpresa/', '&T005_Id=' + encodeURIComponent(acaoId) + '&extra=' + encodeURIComponent('{$extra_}'), [300,200]);            
			});
			$('#crm001AnaliseComprasCliente').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Análise de Compras', '/crm/crm001/content/crm001ContentAnaliseCompras/', '', 'full');            
			});
		} </script></div>
EOT;
        }

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'orcamento.D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'ifnull(T005_Flag_Status[[virgula]]\'0\')' => array('titulo' => 'Cancelado', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => '8'),
                array('title' => 'Não', 'value' => '!8'),
            ), 'naoGerarExtra' => ''),
            'T005_Valor_Total' => array('titulo' => 'Valor total', 'tipo' => 'moeda'),
            'T005_Status_Pedido(T005_Flag_Status[[virgula]]1)' => array('titulo' => 'Status Pedido', 'tipo' => 'texto'),
            'T005_Ordem_Compra' => array('titulo' => 'OC. Cliente', 'tipo' => 'texto'),
            'T006_Descricao_Produto,T006_Codigo_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
            'T005_Percentual_Margem' => array('titulo' => '% Margem', 'tipo' => 'moeda'),
            'T005_IPV' => array('titulo' => 'IPV', 'tipo' => 'moeda'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Monta fórmula total produtos sem impostos como base do cálculo da Margem
		$baseMargem  = "({Valor_Produtos}";
		$baseMargem .= ($g['C031']['somarICMSPrecoVenda']   == 'S') ? "-{Valor_ICMS_}"   : "" ;
		$baseMargem .= ($g['C031']['somarPISPrecoVenda']    == 'S') ? "-{Valor_PIS_}"    : "" ;
		$baseMargem .= ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? "-{Valor_COFINS_}" : "" ;
		$baseMargem .= ($g['C031']['somarIRPJPrecoVenda']   == 'S') ? "-{Valor_IRPJ_}"   : "" ;
		$baseMargem .= ($g['C031']['somarCSLLPrecoVenda']   == 'S') ? "-{Valor_CSLL_}"   : "" ;
		$baseMargem .= ")";

		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_Tabela', 'valor' => 'T005_Valor_Total_Produtos_Sem_Desconto', 'titulo' => 'Tabela R$'),
            array('nome' => 'Valor_Desconto', 'valor' => 'T005_Valor_Desconto', 'titulo' => 'Desconto R$','exibir'=>false),
            array('nome' => 'Percentual_Desconto', 'tipo' => 'totais', 'valor' => '({Valor_Desconto} / {Valor_Tabela}) * 100', 'titulo' => '% Desconto'),
            array('nome' => 'Valor_ICMS_', 'valor' => 'T005_Valor_Total_ICMS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_PIS_', 'valor' => 'T005_Valor_Total_PIS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_COFINS_', 'valor' => 'T005_Valor_Total_COFINS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_IRPJ_', 'valor' => 'T005_Valor_Total_IRPJ', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_CSLL_', 'valor' => 'T005_Valor_Total_CSLL', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_Produtos', 'valor' => 'T005_Valor_Total_Produtos', 'titulo' => 'Produtos R$'),
            array('nome' => 'Valor_Custo', 'valor' => 'T005_Valor_Total_Custo', 'titulo' => 'Custo R$'),
            array('nome' => 'Percentual_Margem', 'tipo' => 'totais', 'valor' => '(('.$baseMargem.' - {Valor_Custo}) / '.$baseMargem.') * 100', 'titulo' => '% Margem'),
			array('nome' => 'Valor_IPI_', 'valor' => 'T005_Valor_Total_IPI', 'titulo' => 'IPI R$'),
			array('nome' => 'Valor_Total', 'valor' => 'T005_Valor_ICMS_Substituicao_Retencao', 'titulo' => 'ST R$'),
            array('nome' => 'Valor_Total', 'valor' => 'T005_Valor_Total', 'titulo' => 'Total R$'),
			array('nome' => 'Valor_ICMS_', 'valor' => 'T005_Valor_Total_ICMS', 'titulo' => 'ICMS R$'),
            array('nome' => 'Valor_Comissao', 'valor' => 'T005_Valor_Total_Comissao', 'titulo' => 'Comissão R$'),
            array('nome' => 'Percentual_Comissao', 'tipo' => 'totais', 'valor' => '({Valor_Comissao}/{Valor_Produtos})*100', 'titulo' => '% Comissão'),
        );
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$sqlGridFiltro = base64_encode($geradoSql);
		$extra_ = base64_encode(gRetirarOrderBy($extra));

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven002SelecionarTodos').unbind('click').bind('click', function(data) {
					$.getJSON('/crm/crm001/grid_func-ajax/crm001SelecionarTodos/?ajax=true&extra=' + encodeURIComponent('{$sqlGridFiltro}') + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						}
					});
				});
				
				$('#ven002DesmarcarTodos').unbind('click').bind('click', function(data) {
					$.getJSON('/crm/crm001/grid_func-ajax/crm001DesmarcarTodos/?ajax=true&extra=' + encodeURIComponent('{$sqlGridFiltro}') + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						}
					});
				});
			} </script></div>
EOT;
        }

		/**
		 * Totalizadores
		 */
		/*if (!personalizacaoTotais()) { 
			$campos = "COUNT(*), SUM(T005_Valor_Total_Produtos_Sem_Desconto), (sum(T005_Valor_Desconto)/sum(T005_Valor_Total_Produtos_Sem_Desconto))*100, sum(T005_Valor_Total_Produtos), sum(T005_Valor_Total_Custo), ((sum(T005_Valor_Total_Produtos)-sum(T005_Valor_Total_Custo))/sum(T005_Valor_Total_Produtos))*100, sum(T005_Valor_Total_IPI), sum(T005_Valor_ICMS_Substituicao_Retencao), sum(T005_Valor_Total), sum(T005_Valor_Total_ICMS), sum(T005_Valor_Total_Comissao), (sum(T005_Valor_Total_Comissao)/sum(T005_Valor_Total_Produtos))*100";
			list($numeroRegistros, $valorTotalSemDesconto, $valorTotalDesconto, $valorTotalProdutos, $valorTotalCusto, $valorTotalMargem, $valorTotalIPI, $valorTotalST, $valorTotal, $valorTotalICMS, $valorTotalComissao, $valorTotalPercentualComissao) = $g['sqlAuto']->pegarSqlCampos($campos, $from, $extraTotal);
			$valorTotalSemDesconto = gCorrigeNumero($valorTotalSemDesconto);
			$valorTotalDesconto = gCorrigeNumero($valorTotalDesconto);
			$valorTotalProdutos = gCorrigeNumero($valorTotalProdutos);
			$valorTotalCusto = gCorrigeNumero($valorTotalCusto);
			$valorTotalMargem = gCorrigeNumero($valorTotalMargem);
			$valorTotalIPI = gCorrigeNumero($valorTotalIPI);
			$valorTotalST = gCorrigeNumero($valorTotalST);
			$valorTotal = gCorrigeNumero($valorTotal);
			$valorTotalICMS = gCorrigeNumero($valorTotalICMS);
			$valorTotalComissao = gCorrigeNumero($valorTotalComissao);
			$valorTotalPercentualComissao = gCorrigeNumero($valorTotalPercentualComissao);

			$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extraTotal);			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" size="5" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor produtos<br><input type="text" class="whiteBg"  size="11" value="{$valorTotalSemDesconto}" readonly></span>
					<span class="item" style="text-align:right">%desconto<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalDesconto}%" readonly></span>
					<span class="item" style="text-align:right">Valor produtos<br><input type="text" class="whiteBg" size="11" value="{$valorTotalProdutos}" readonly></span>
					<span class="item" style="text-align:right">Valor custo<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalCusto}" readonly></span>
					<span class="item" style="text-align:right">%Margem<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalMargem}%" readonly></span>
					<span class="item" style="text-align:right">Valor IPI<br><input type="text" class="whiteBg"  size="9" value="{$valorTotalIPI}" readonly></span>
					<span class="item" style="text-align:right">Valor ST<br><input type="text" class="whiteBg"  size="9" value="{$valorTotalST}" readonly></span>
					<span class="item" style="text-align:right">Valor total<br><input type="text" class="whiteBg"  size="10" value="{$valorTotal}" readonly></span>
					<span class="item" style="text-align:right">Valor ICM<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalICMS}" readonly></span>
			        <span class="item" style="text-align:right">%Comissão<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalPercentualComissao}" readonly></span>
			        <span class="item" style="text-align:right">Valor comissão<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalComissao}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}*/

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
	
	case 'crm002gridProdutosCliente':
		$filtroObrigatorio = true;
		$grid = array(
	            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T008_Id', $g['divId'])),
	            array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'T008_T007_Id', 'colunaTitulo' => 'T008_T007_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
	            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Razão Social'),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'style' => 'width:1%;', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Produto', 'callback' => 'abrirJanela', 'style' => '', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
	            array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width:1%;'),
	            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
	            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T008_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background:var(--celula-amarelo-claro)'),
	            array('colunaValor' => 'T008_Valor_Preco_Original', 'colunaTitulo' => 'Original', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T008_Percentual_Desconto', 'colunaTitulo' => '%Desc', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background:var(--celula-amarelo-claro)'),
	            array('colunaValor' => 'T008_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T008_Percentual_Comissao', 'colunaTitulo' => '%Comiss', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
	            array('colunaValor' => 'T008_Valor_Comissao', 'colunaTitulo' => 'Comissão R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
	            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
	            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Flag_Situacao_Tributaria', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Aliquota_ICMS', 'colunaTitulo' => '%ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T008";
		$extra = <<<EOT
			left join D009 on D009_Id=T008_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D006 on D006_Id=T008_D006_Id
            left join D037 on D037_Id=T008_D037_Id
            left join T007 on T007_Id=T008_T007_Id
            left join D024 on D024_Id=T007_D024_Id
            left join C008 on C008_Id=D001_C008_Id
            left join D060 on D060_Id=D001_D060_Id
            left join D021 on D021_Id=T007_D021_Id
            left join T006 on T006_Id=T008_T006_Id
			left join C007 as vendedor on vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
			left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
			where T007_C004_Id='{$g['empresaAtual']}'
			and T007_D024_Id='{$r_D024_Id}'
			and D006_Flag_Venda_Compra_Outros='V'
			and D006_Flag_Entrada_Saida='S'
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var T006_T005_Id = id[3].replace(/T006_T005_Id-/, '');
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + T006_T005_Id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T006_T005_Id) + '&tabela=T005', [950,615]);            
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'exibir' => false),
            'T008_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'T008_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C008_Id,C008_Tipo' => array('titulo' => 'Depto.', 'tipo' => 'texto'),
            'D060_Descricao_Grupo' => array('titulo' => 'Setor', 'tipo' => 'texto'),
            'T007_Flag_Cancelada' => array('titulo' => 'Cancelada?(S/N)', 'tipo' => 'texto'),
            'T008_Percentual_Desconto' => array('titulo' => '% Desconto', 'tipo' => 'numero'),
        );

		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		function gridLinha($array) {
			if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			}
		}
		
		if (!personalizacaoTotais()) { 
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			$valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra));
			$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T008_Quantidade)", $from, $extra));
			$valorTotal3 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T008_Valor_Total_Preco)", $from, $extra));
			$valorTotal4 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T008_Valor_Comissao)", $from, $extra));
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Qte vendas<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Qte produto<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor total<br><input type="text" value="{$valorTotal3}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor comissões<br><input type="text" value="{$valorTotal4}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}

		$novoIdTelaForm = uniqid();

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
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
	
	case 'crm002GridNotasCliente':
		$filtroObrigatorio = true;
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirNFe'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número'),
            array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'callback' => 'validaTipo'),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T007_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T007_Prazos(T007_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T007_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T007_Valor_Total_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T007_Percentual_Margem', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'T007_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T007_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'Vendedor', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', '', true), 'callback' => 'mostrarVendedorInternoNF', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'Danfe', 'colunaTitulo' => 'Danfe', 'colunaTipo' => 'livre', 'callback' => 'ValidaCampoDanfe'),
            array('colunaValor' => 'NF', 'colunaTitulo' => 'NF', 'colunaTipo' => 'livre', 'callback' => 'validaCampoNF'),
            array('colunaValor' => 'ACP', 'colunaTitulo' => 'ACP', 'colunaTipo' => 'livre', 'callback' => 'validaCampoACP'),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar'),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'T007_Data_Envio_XML', 'colunaTitulo' => 'eMail', 'colunaTipo' => 'Livre', 'callback' => 'validaCampoEmail'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T008_T007_Id=T007_Id) as Tipos', 'colunaTitulo' => 'Tipos', 'colunaExibir' => false),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) as Marca
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  WHERE T008_T007_Id=T007_Id) as Marcas', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
            array('colunaValor' => 'Retorno Sefaz', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Retorno SEFAZ', 'callback' => 'chamaCampo'),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe as Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocolo NFe'),
            array('colunaValor' => 'T007_Numero_Protocolo_Cancelamento_Nfe', 'colunaTitulo' => 'Prot. Cancel'),
            array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso', 'callback' => 'ValidaChaveAcesso'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Impresso', 'colunaTitulo' => 'Impressão', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status', 'colunaTitulo' => 'Status Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Hora_Inutilizacao', 'colunaTitulo' => 'Hora Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Inutilizado', 'colunaTitulo' => 'Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Envio_XML as Data_Envio_XML', 'colunaTitulo' => 'XML', 'colunaExibir' => false),
            array('colunaValor' => '(select ifnull(T127_Id,0) from T127 where T127_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T127_C004_Id=T007_C004_Id limit 1) as T127_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => '(select T008_Codigo_Produto from T008 where T008_T007_Id=T007_Id limit 1) as T008_Codigo_Produto', 'colunaTitulo' => '', 'colunaExibir' => false),
        );
		//retira a coluna de exclusão da NF-e caso o usuário não seja o 63(root)
		if($g['usuarioAtual'] != 63){
			unset($grid[0]);
		}
		
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			}
		}
		
		/**
		 * Definição da SQL
		 */
		$from = "T007";
		
		$extra = <<<EOT
			left join T005 on T005_Id=T007_T005_Id
		    left join D024 on D024_Id=T007_D024_Id
		    left join D018 on D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T007_D006_Id
		    left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
		    left join D021 on D021_Id=D024_D021_Id
		    where
		    (T007_C004_Id='{$g['empresaAtual']}'
		    and T007_Flag_Emitida_Recebida!='R')
   			AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', T007_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}', '1=1')
   			AND T007_D024_Id='{$r_D024_Id}'
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'ven003IncluirNotaFiscalCliente'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="ven003IncluirNotaFiscalCliente">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#ven003IncluirNotaFiscalCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','', [900,550]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'Nota Fiscal', 'tipo' => 'texto'),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'Pedido.D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'T007_Flag_Cancelada' => array('titulo' => 'Cancelada', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ),
            'naoGerarExtra' => ''),
            'T007_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Origem', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
              ),
              'naoGerarExtra' => ''),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Tipo produto', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) as Marca
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Marca produto', 'tipo' => 'texto'),
            'T007_Numero_Protocolo_Nfe' => array('titulo' => 'Protocolo', 'tipo' => 'text', 'exibir' => false),
            'T007_Flag_ACP' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'NF-e', 'value' => '6'),
                array('title' => 'CF', 'value' => '4'),
              ),
            ),
            'T007_Prazos(T007_Id)' => array('titulo' => 'Prazo', 'tipo' => 'texto'),
            'T007_Percentual_Margem' => array('titulo' => '% Margem', 'tipo' => 'moeda'),
            'T007_IPV' => array('titulo' => 'IPV', 'tipo' => 'moeda'),
        );
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Monta fórmula total produtos sem impostos como base do cálculo da Margem
		$baseMargem  = "({Valor_Produtos}";
		$baseMargem .= ($g['C031']['somarICMSPrecoVenda']   == 'S') ? "-{Valor_ICMS_}"   : "" ;
		$baseMargem .= ($g['C031']['somarPISPrecoVenda']    == 'S') ? "-{Valor_PIS_}"    : "" ;
		$baseMargem .= ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? "-{Valor_COFINS_}" : "" ;
		$baseMargem .= ($g['C031']['somarIRPJPrecoVenda']   == 'S') ? "-{Valor_IRPJ_}"   : "" ;
		$baseMargem .= ($g['C031']['somarCSLLPrecoVenda']   == 'S') ? "-{Valor_CSLL_}"   : "" ;
		$baseMargem .= ")";

        /**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_ICMS_', 'valor' => 'T007_Valor_Total_ICMS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_PIS_', 'valor' => 'T007_Valor_Total_PIS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_COFINS_', 'valor' => 'T007_Valor_Total_COFINS', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_IRPJ_', 'valor' => 'T007_Valor_Total_IRPJ', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_CSLL_', 'valor' => 'T007_Valor_Total_CSLL', 'titulo' => 'ICMS R$', 'exibir' => false),
            array('nome' => 'Valor_Produtos', 'valor' => 'T007_Valor_Total_Produtos', 'titulo' => 'Produtos R$', 'exibir' => true),
            array('nome' => 'Valor_IPI', 'valor' => 'T007_Valor_Total_IPI', 'titulo' => 'IPI R$', 'exibir' => false),
            array('nome' => 'Valor_ST', 'valor' => 'T007_Valor_ICMS_Substituicao_Retencao', 'titulo' => 'ST R$', 'exibir' => false),
            array('nome' => 'Valor_Desconto', 'valor' => 'T007_Valor_Desconto', 'titulo' => 'Desconto R$', 'exibir' => false),
            array('nome' => 'Valor_Despesas', 'valor' => 'T007_Valor_Despesas_Acessorias', 'titulo' => 'Desp.Assec. R$', 'exibir' => false),
            array('nome' => 'Valor_Frete', 'valor' => 'T007_Valor_Frete', 'titulo' => 'Frete R$', 'exibir' => false),
            array('nome' => 'Valor_Total', 'valor' => 'T007_Valor_Total', 'titulo' => 'Total R$'),
			array('nome' => 'Valor_Comissao', 'valor' => 'T007_Valor_Total_Comissao', 'titulo' => 'Comissão R$'),
            array('nome' => 'Percentual_Comissao', 'tipo' => 'totais', 'valor' => '({Valor_Comissao}/{Valor_Produtos})*100', 'titulo' => '% Comissão'),
            array('nome' => 'Valor_Custo', 'valor' => 'T007_Valor_Total_Custo', 'titulo' => 'Custo R$', 'exibir' => false),
            array('nome' => 'Percentual_Margem', 'tipo' => 'totais', 'valor' => '(('.$baseMargem.' - {Valor_Custo}) / '.$baseMargem.') * 100', 'titulo' => '% Margem'),
            array('nome' => 'IPV', 'tipo' => 'totais', 'valor' => '('.$baseMargem.'/{Valor_Custo})', '1,' => 4, 'titulo' => 'IPV', 'exibir' => false),
            
        );
        
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*if (!personalizacaoTotais()) { 
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			$valorTotal1 = $g['sqlAuto']->pegarSqlCampo("sum(T007_Valor_Total_Produtos)", $from, $extra);
			$valorTotal2 = $g['sqlAuto']->pegarSqlCampo("sum(T007_Valor_Total) Total", $from, $extra);
			$valorTotal3 = $g['sqlAuto']->pegarSqlCampo("sum(T007_Valor_Total_Comissao) Comissao", $from, $extra);
			$valorTotal4 = $g['sqlAuto']->pegarSqlCampo("(sum(T007_Valor_Total_Custo)/sum(T007_Valor_Total_Produtos))*100", $from, $extra);
			$percentualComissao = ($valorTotal3 / $valorTotal1)*100;

			$valorTotal1 = gCorrigeNumero($valorTotal1);
			$valorTotal2 = gCorrigeNumero($valorTotal2);
			$valorTotal3 = gCorrigeNumero($valorTotal3);
			$valorTotal4 = gCorrigeNumero($valorTotal4);
			$percentualComissao = gCorrigeNumero($percentualComissao);
			
			if($g['C007']['C007_Flag_Vendedor'] == 'D'){
				echo <<<EOT
					<div class="buttonsBottomTop">
						<span class="item" style="text-align:right">Notas Emitidas..<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">Valor comissões<br><input type="text" value="{$valorTotal3}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">% comissões<br><input type="text" value="{$percentualComissao}%" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">% Margem<br><input type="text" value="{$valorTotal4}%" class="whiteBg" id="" size="11" readonly></span>
						<div style="clear:both;"></div>
					</div>
EOT;
			} else {
				echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Notas Emitidas..<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor comissões<br><input type="text" value="{$valorTotal3}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">% comissões<br><input type="text" value="{$percentualComissao}%" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			}
 		}*/
		$novoIdTelaForm = uniqid();
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [900,550]);
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
	
	/**
	* Definição do Grid (Tabela)
	*/
	case 'crm002GridRequisicao':
		// $meses = $g['c020']['C020_Numero_Meses_Relatorio_Estoque'];
		// $periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T189_Id', $g['divId'])),
			array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaExibir'=>false, 'chavePrimaria'=>true),
			array('colunaValor' => 'T189_Data_Criado', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'concat(T189_Descricao_Produto, " ", if(D001_Flag_Pre_Cadastro="S","(PRÉ)",""))', 'colunaTitulo' => 'Descrição Produto'),
			array('colunaValor' => 'T189_Quantidade', 'colunaTitulo' => 'Qnt', 'style' => 'width: 1%', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true),
			array('colunaValor' => 'Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'validaCampoTipo', 'colunaTipo'=>'livre'),
			array('colunaValor' => 'T189_Flag_Cotar', 'colunaTitulo' => 'Cotar', 'callback' => 'validaCampoCotar', 'style'=>'width:1%;'),
			array('colunaValor' => 'T189_Flag_Comprar', 'colunaTitulo' => 'Comprar', 'callback'=>'validaCampoComprar', 'style'=>'width:1%;'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T189_Observacao', 'colunaTitulo' => 'Observação'),
			array('colunaValor' => 'T189_C007_Id', 'colunaTitulo' => 'Usuário', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_Detalhes', 'colunaTitulo' => 'Detalhes'),
			array('colunaValor' => 'T189_T004_Id', 'colunaTitulo' => 'T189_T004_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_T006_Id', 'colunaTitulo' => 'T189_T006_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_D009_Id', 'colunaTitulo' => 'T189_D009_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_Flag_Tipo', 'colunaTitulo' => 'T189_Flag_Tipo', 'colunaExibir'=>false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T189';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		LEFT JOIN C007 ON C007_Id=T189_C007_Id
		LEFT JOIN T012 on T012_T189_Id=T189_Id
		LEFT JOIN T059 on T059_T189_Id=T189_Id
		LEFT JOIN D009 on D009_Id=T189_D009_Id
		LEFT JOIN D049 on D049_Id=D009_D049_Id
		LEFT JOIN D001 on D001_Id=D049_D001_Id
		WHERE (T012_T189_Id IS NULL OR T012_T189_Id='' OR T012_T189_Id=0) 
		AND 
		(T059_T189_Id IS NULL OR T059_T189_Id='' OR T059_T189_Id=0) and D024_Id={$r_D024_Id}
		ORDER BY T189_Data_Criado ASC
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T189_Descricao_Produto' => array('titulo' => 'Descrição Produto', 'tipo' => 'texto'),
			'T189_Data_Criado' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T189_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
			'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T189_Detalhes' => array('titulo' => 'Detalhes', 'tipo' => 'texto'),
			'T189_Flag_Tipo' => array('titulo' => 'Tipo (C/O/P/E)', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

	echo <<<EOT
	    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    setTimeout(function() {
		         divRefresh('{$g['divId']}');
		    }, 180000);
	    } </script></div>
EOT;
		
		$sql_ = urlencode(base64_encode($geradoSql));
		// <button type="button" id="crm002Incluir">Incluir</button>
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="crm002FlagCotar">Marcar Cotar</button>
				<button type="button" id="crm002FlagDesmarcarCotar">Desmarcar Cotar</button>
				<button type="button" id="crm002FlagMarcarComprar">Marcar Comprar</button>
				<button type="button" id="crm002FlagDesmarcarComprar">Desmarcar Comprar</button>
				<button type="button" id="crm002GerarOC">Gerar OC</button>
				<button type="button" id="crm002GerarCotacao">Gerar Cotação</button>
				
			</div>
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$extra_ = urlencode(base64_encode($extra));
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#crm002FlagCotar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/crm002/grid_func-ajax/crm002FlagMarcar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#crm002FlagDesmarcarCotar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/crm002/grid_func-ajax/crm002FlagDesmarcar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#crm002FlagMarcarComprar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/crm002/grid_func-ajax/crm002FlagMarcar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#crm002FlagDesmarcarComprar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/crm002/grid_func-ajax/crm002FlagDesmarcar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				
				$('#crm002GerarOC').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/crm002/grid_func-ajax/crm002GerarOC/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = {
											"Sim": function() {
												$('#{$g['divId']}').showLoading();
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar OC', '/est/est006/content/est006ContentIncluirOrdemCompra/', '&tab=geral&GerarOCEstoqueRequisicao=true', [500,300]);
												$('#{$g['divId']}').hideLoading();
												$(this).dialog("destroy").remove();
											},
											"Não": function() { $(this).dialog("destroy").remove(); } 
										};
										dialogConfirm('Confirmação', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gerar Ordem de Compra', 'Gostaria de gerar Ordem de Compra? ', \$buttons);
				});
				
				$('#crm002GerarCotacao').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/crm002/grid_func-ajax/crm002GerarCotacao/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = {
											"Sim": function() {
												$('#{$g['divId']}').showLoading();
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est008/content/est008contentCadastroCotacaoCompraNew/', '&tab=geral&GerarCotacaoEstoqueRequsicao=true', [500,300]);
												$('#{$g['divId']}').hideLoading();
												$(this).dialog("destroy").remove();
											},
											"Não": function() { $(this).dialog("destroy").remove(); } 
										};
										dialogConfirm('Confirmação', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gerar Cotação', 'Gostaria de gerar Cotação? ', \$buttons);
				});
				$('#crm002Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/crm002/content/crm002ContentRequisicaoCompraEditarNovo/', '&tab=geral&', [500,300]);
				});
			} </script></div>
EOT;
        }
		
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			  var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T189_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Requisicao de compra - Nº: ' + acaoId, '/est/crm002/content/crm002ContentRequisicaoCompraEditarNovo/', '&acaoId=' + encodeURIComponent(acaoId), [600,400]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
	case 'crm002GridCotacao':

		 $usarAlcada = $g['conf']->ini('usarAlcadaCompra');
		
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T010_Id', $g['divId'])),
 			array('colunaValor' => 'T010_Data', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style'=>'width:1%;'),
 			array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			//array('colunaValor' => 'T173_T010_Id', 'colunaTitulo' => 'T173_Id','colunaExibir' => false),
			//array('colunaValor' => 'T173_Id', 'colunaTitulo' => 'T173_Id'),
			//array('colunaValor' => 'concat(D024_Nome_Empresa," - ",(SELECT D024_Nome_Empresa FROM T173 LEFT JOIN D024 ON T173_D024_Id=D024_Id WHERE T173_T010_Id=T010_Id))', 'colunaTitulo' => 'Fornecedor1' ),
			array('colunaValor' => 'T173_D024_Id', 'colunaTipo' => 'livre','colunaTitulo' => 'Fornecedor / Alçada','callback' => 'campoFornecedor'),
			array('colunaValor' => 'ValorCotacao', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor Cotação','callback' => 'campoValorCotacao', 'colunaAlinhar'=>'right', 'style'=>'width:1%;'),
			array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => '','callback' => 'validaCampoEmailEnviadoForn', 'colunaAlinhar'=>'right', 'style'=>'width:1%;'),
			array('colunaValor' => 'ValorConfirmado', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor Confirmado','callback' => 'campoValorConfirmado', 'colunaAlinhar'=>'right', 'style'=>'width:1%;'),
			array('colunaValor' => '(select count(*) from T059 where T059_T010_Id=T010_Id) as tudoT059', 'colunaTitulo' => 'Itens', 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T010_Flag_Tipo', 'colunaTitulo' => 'Tipo'),
			array('colunaValor' => 'T010_Flag_Alcada_Status', 'colunaTitulo' => 'Alçada', 'colunaExibir'=>$usarAlcada, 'callback'=>'retornaStatusAlcada', 'colunaTipo'=>'livre'),
			//array('colunaValor' => "if((select count(*) from T059 left join T012 on T012_T059_Id=T059_Id where T059_T010_Id=T010_Id and T012_Id is null)>0,'PENDENTE',if(T010_Flag_Cotacao_Finalizada='S','Finalizado','Concluido')) as Status", 'colunaTitulo' => 'Status','callback' => 'colunaStatus'),
			array('colunaValor' => "T010_Flag_Cotacao_Finalizada", 'colunaTitulo' => 'Status','callback' => 'colunaStatusNovo'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'T010_Motivo_Nao_Liberado', 'colunaTitulo' => 'T010_Motivo_Nao_Liberado', 'colunaExibir'=>false),
			
			
			
			//$cClausula .= " (select count(*) from T059 where T059_T010_Id=T010_Id) Itens, ";
			
     );
     
		
		/**
		 * Definição da SQL
		 */
		$from = "T010";
		
		$extra = <<<EOT
			left join C007 on C007_Id=T010_C007_Id
			left join D024 on D024_Id=T010_D024_Id
			where T010_C004_Id='{$g['empresaAtual']}'
EOT;
			/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est008Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est008Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Cotação de Compra', '/est/est008/content/est008contentCadastroCotacaoCompra/','', [500,350]);
				});
			} </script></div>
EOT;
			
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'T010_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
			'T010_Data' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			//'T010_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
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
			* Botões (Versão Acima do GRID)
			*/
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Cotação de Compra<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			/**
			 * Define ação do click de abertura (ao clicar em uma linha do grid)
			 */
			$novoIdTelaForm = uniqid();
			echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T010_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cotação de Compra - Número: ' + id, '/est/est008/content/est008contentCadastroCotacaoCompra/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [900,500]);
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
  	case 'crm002GridOrdem':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T011_Id', $g['divId'])),
 			array('colunaValor' => 'T011_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style'=>'width:1%;'),
 			array('colunaValor' => 'T011_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'colunaAlinhar'=>'center', 'style'=>'width:1%;'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'CódigoFornecedor','colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Fornecedor' ),
			array('colunaValor' => 'T011_Valor_Total_Custo', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T011_Valor_Total_Pendente', 'colunaTitulo' => 'Valor Pendente', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'T011_Flag_Tipo', 'colunaTitulo' => 'Tipo' ),
			array('colunaValor' => '(select count(*) from T012 where T012_T011_Id=T011_Id) as Itens', 'colunaTitulo' => 'Itens', 'colunaAlinhar'=>'center', 'style'=>'width:1%;'), 
			//array('colunaValor' => '(select group_concat(distinct T059_T010_Id) from T059 left join T012 on T012_T059_Id=T059_Id where T012_T011_Id=T011_Id) as Cotacao','colunaTitulo' => 'Cotação'),
			array('colunaValor' => 'T011_T010_Id','colunaTitulo' => 'Cotação', 'style'=>'width:1%;'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'T011_Status_Aprovacao', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
			array('colunaValor' => 'T011_C007_Id_Aprovador', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
      );
		
		/**
		 * Definição da SQL
		 */
		$from = "T011";
		
		$extra = <<<EOT
		    
		    left join D024 on D024_Id=T011_D024_Id
		    left join C007 on C007_Id=T011_C007_Id
		    where T011_C004_Id={$g['empresaAtual']} and D024_Id={$r_D024_Id}
EOT;

/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est006IncluirNotaFiscal_Btn">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est006IncluirNotaFiscal_Btn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Ordem de Compra', '/est/est006/content/est006ContentIncluirOrdemCompra/', '&tab=geral', [450,330]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
			'T011_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
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
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		
		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T011_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra: ' + id, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1000,600]);
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
  	case 'crm002GridNFRecebida':
		
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
 			array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'IdNFRecebida', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T013_Numero_Controle', 'colunaTitulo' => 'Controle', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'N.F.', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T013_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T013_Data_Entrada', 'colunaTitulo' => 'Entrada', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'callback'=>'coloreLinha'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => "if(T013_Tipo='1','NF',if(T013_Tipo='2','ACP',if(T013_Tipo='3','NF Consumidor',if(T013_Tipo='4','NFe','CF'))) ) as Tipo_Documento", 'colunaTitulo' => 'Doc' ),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'), 
			array('colunaValor' => 'sum((T014_Quantidade*T014_Valor_Custo_Unitario)-T014_Valor_Total_Desconto) as Produtos','colunaTitulo' => 'Produtos', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T013_Valor_Mao_Obra', 'colunaTitulo' => 'Serviços', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'Almoxarifado', 'colunaTitulo' => 'Estoque', 'callback' => 'validaCampoAlmoxarifado', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Cancelar', 'colunaTitulo' => 'Cancelar', 'callback'=>'validaCampoCancelarNFRec', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'DANFE', 'colunaTitulo' => 'DANFE', 'callback'=>'validaCampoDanfeNFRec', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback'=>'validaCampoXMLNFRec', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T013_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso'),
			array('colunaValor' => 'T013_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocoloo NFe'),
			array('colunaValor' => 'T013_Retorno_Nfe', 'colunaTitulo' => 'Retorno SEFAZ'),
			array('colunaValor' => 'T013_Flag_Cancelada', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'T013_Flag_Travar_Nota', 'colunaTitulo' => '', 'colunaExibir' => false),
      );
		
		/**
		 * Definição da SQL
		 */
		$from = "T013";
		
		$extra = <<<EOT
		    left join T014 on T014_T013_Id=T013_Id
		    left join T012 on T012_Id=T014_T012_Id
		    left join D009 on D009_Id=T014_D009_Id
		    left join D049 on D049_Id=D009_D049_Id
		    left join D001 on D001_Id=D049_D001_Id
		    left join D024 on D024_Id=T013_D024_Id
		    left join D018 on D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T014_D006_Id OR D006_Id=T013_D006_Id
		    where T013_C004_Id='{$g['empresaAtual']}' and D024_Id={$r_D024_Id}
		    GROUP BY T013_Id,D006_Codigo_CFOP
EOT;
/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est004IncluirNotaFiscal_Btn">Incluir</button>
				<!-- <button type="button" onclick="nova_janela('/hardness/nota_recebida_lancamento/atualizar_pedidos_venda.php',0,0,820,600,'no','no','no','no','yes','yes',0);">Atualizar Pedidos Venda</button> -->
				<!-- <button type="button" id="est004AtualizarPedidosVenda_Btn">Atualizar Pedidos Venda</button> -->
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est004IncluirNotaFiscal_Btn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir nota Fiscal', '/est/est004/content/est004ContentCadastroNotaFiscal/', '&tab=geral', [990,550]);
				});
				$('#est004AtualizarPedidosVenda_Btn').unbind('click').bind('click', function(data) {
					$('#' + '{$g['divId']}').showLoading();
					$.getJSON('/est/est004/grid_func-ajax/atualizarPedidosVenda/?ajax=true&callback=?', function(request) {
					if (request.code) {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    		dialogConfirm("Sucesso!", request.data, buttons)
			    		divRefresh('{$g['divId']}'); 
					} else {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    		dialogConfirm("Erro!", request.data, buttons)
					}
				});
			});
			} </script></div>
EOT;


		/**
		* Filtro do modo correto, porém está dando um looping, verificar com o tiago
		*/
		$gridFiltro = array(
			'T013_Numero_Controle' => array('titulo' => 'Controle', 'tipo' => 'texto'),
			'T013_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'T013_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T013_Data_Entrada' => array('titulo' => 'Entrada', 'tipo' => 'data'),
			
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		//*/
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		
	
		/**
		* Botões (Versão Acima do GRID)
		*/
		// O extra utilizados para o total é diferente do utilizado para montar o grid
		$extraTotais1 = <<<EOT
		    left join D024 on D024_Id=T013_D024_Id
		    left join D018 on D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T013_D006_Id
		    where T013_C004_Id='{$g['empresaAtual']}' AND T013_Flag_Cancelada!='S' and D024_Id={$r_D024_Id}
EOT;
		$extraTotais1 = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotais1);
		$extraTotais = <<<EOT
		    left join T014 on T014_T013_Id=T013_Id
		    left join T012 on T012_Id=T014_T012_Id
		    left join D009 on D009_Id=T014_D009_Id
		    left join D049 on D049_Id=D009_D049_Id
		    left join D001 on D001_Id=D049_D001_Id
		    left join D024 on D024_Id=T013_D024_Id
		    left join D018 on D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T014_D006_Id
		    where T013_C004_Id='{$g['empresaAtual']}' AND T013_Flag_Cancelada!='S' and D024_Id={$r_D024_Id}
EOT;
		$extraTotais = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotais);
		$valorTotal = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T013_Valor_Total)", $from, $extraTotais1)));
		$valorTotal1 = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Quantidade*T014_Valor_Custo_Unitario)", $from, $extraTotais)));
		$valorTotal2 = $g['sqlAuto']->pegarSqlCampo("count(distinct T013_Id)", $from, $extraTotais);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Notas Recebidas<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
			
		
		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'FONT') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T013_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal: ' + $(this).closest('tr').find('td:eq(1)').text(), '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,550]);
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
  	
  	case 'crm002GridConhecimentos':
		
		global $g;
		
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
 			array('colunaValor' => 'T084_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T084_Numero_Conhecimento', 'colunaTitulo' => 'Conhecimento'),
			array('colunaValor' => 'T084_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T084_Data_Lancamento', 'colunaTitulo' => 'Lançamento', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
			array('colunaValor' => 'T084_Valor_Conhecimento', 'colunaTitulo' => 'Valor','colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'T084_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T084_Flag_Fechado', 'colunaTitulo' => 'Notas Lançadas', 'callback'=>'validaNotasLancadas'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'Cancelar', 'colunaTitulo' => 'Cancelar', 'colunaTipo'=>'livre', 'callback'=>'validaCampoCancelarNF'),
			array('colunaValor' => 'Excluir', 'colunaTitulo' => 'Excluir', 'colunaTipo'=>'livre', 'callback'=>'validaCampoExcluir'),
			array('colunaValor' => 'T084_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T084_C007_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T084_D022_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T084_D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T084_D006_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false)
      );
		
		/**
		 * Definição da SQL
		 */
		$from = "T084";
		
		$extra = <<<EOT
				left join C007 on C007_Id=T084_C007_Id
			    left join D022 on D022_Id=T084_D022_Id
			    left join D024 on D024_Id=T084_D024_Id
			    left join D020 on D020_Id=D024_D020_Id
			    left join D018 on D018_Id=D020_D018_Id
			    left join T015 on T015_T084_Id=T084_Id and T015_Flag_Cancelada!='S'
			    left join D006 on T084_D006_Id=D006_Id
			    where (T084_C004_Id='{$g['empresaAtual']}'  and D024_Id={$r_D024_Id} and D024_Id={$r_D024_Id})
			    
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est005IncluirConhecimentoBtn">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est005IncluirConhecimentoBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir conhecimento', '/est/est005/content/est005ContentCadastroConhecimento/', '&tab=geral', [650,400]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T084_Numero_Conhecimento' => array('titulo' => 'Conhecimento', 'tipo' => 'numero'),
			//'T013_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'numero'),
			'T084_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			//'T084_Data_Lancamento' => array('titulo' => 'Lançamento', 'tipo' => 'data'),
			//'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			//'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			//'T084_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
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
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T084_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Conhecimento ' + $(this).closest('tr').find('td:eq(1)').text(), '/est/est005/content/est005ContentEditarConhecimento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,700]);
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
	
	case 'crm002GridEmail':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			// Chave Primária
			array('colunaValor' => 'T139_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T139_Pasta', 'colunaTitulo' => 'P'),
			array('colunaValor' => 'T139_De', 'colunaTitulo' => 'De'),
			array('colunaValor' => 'T139_Para', 'colunaTitulo' => 'Para'),
			array('colunaValor' => 'T139_Titulo', 'colunaTitulo' => 'Assunto'),
			array('colunaValor' => 'T139_Data_Hora', 'colunaTitulo' => 'Data/Hora', 'callback'=>'gCorrigeData'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T139';
		$extra = <<<EOT
			left join D024 on D024_Id=T139_D024_Id
			where (T139_D024_Id='{$r_D024_Id}')
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc006EnviarEmail2">Novo Email</button>
				<button type="button" id="etc006VerificarEmailsEnviadosCad001">Verificar Novos Emails</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006EnviarEmail2').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&D024_Id=' + encodeURIComponent('$r_acaoId'), [900,550]);
				});
				$('#etc006VerificarEmailsEnviadosCad001').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/etc/etc006/grid_func-ajax/atualizarEmails/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						var buttons = {
							"Ok": function() {
								$(this).dialog("destroy").remove(); 
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								}
							}
						}
						dialogConfirm('Atualizar Emails', request.data, buttons);
					});
				});
			} </script></div>
EOT;
	
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$gridFiltro = array(
			'T139_De' => array ('titulo' => 'De', 'tipo' => 'texto'),
			'T139_Data_Hora' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			
		);
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
	
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		echo <<<EOT
			<script type="text/javascript">
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace('T139_Id-', '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Email', '/etc/etc006/content/etc006ContentEmail/', '&pasta=S&acaoId=' + encodeURIComponent(id), [900,550]);
				});
			</script>
			
EOT;
	
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	
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

	case 'crm002GridTabelaPrecosCliente':

		$sqlD161 = mysql_query("SELECT * FROM D161 WHERE D161_D024_Id = '{$r_D024_Id}'");
		if(mysql_num_rows($sqlD161) <= 0){
			mysql_query("INSERT INTO D161 (D161_D024_Id) VALUES ('{$r_D024_Id}')");
			$D161_Id = $g['mysqlLastId'];
		} else {
			$rowD161 = mysql_fetch_assoc($sqlD161);
			$D161_Id = $rowD161['D161_Id'];
		}
		$importacaoDados=array('D161_Id '=>$D161_Id);
		/*
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D160_Id', $g['divId'])),
            array('colunaValor' => 'D160_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Cód. Produto', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D160_Valor_Preco', 'colunaTitulo' => 'Preço', 'style' => 'width:1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%;'),
            array('colunaValor' => 'D160_Data_Hora', 'colunaTitulo' => 'Atualização', 'style' => 'width:1%;', 'callback' => 'gCorrigeData'),
        );

		$from = 'D160';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D024 on D024_Id=D160_D024_Id
			LEFT JOIN C007 on C007_Id=D160_C007_Id
			LEFT JOIN D009 on D009_Id=D160_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			WHERE D009_C004_Id = '{$g['empresaAtual']}'
			AND D160_D161_Id = '{$D161_Id}'
			GROUP BY D160_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir produtos', 'id' => 'crm002IncluirProdutosTabelaPreco'),

		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D160_Data_Hora' => array('titulo' => 'Atualização', 'tipo' => 'data'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		
		    /*<div class="buttonsBottomTop">
		        <button type="button" id="crm002IncluirProdutosTabelaPreco">Incluir produtos</button>
		    </div>*/
			echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		        $('#crm002IncluirProdutosTabelaPreco').unbind('click').bind('click', function(data) {
		            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir produtos', '/crm/crm001/content/crm001ContentIncluirProdutosTabelaPreco/', '&D161_Id={$D161_Id}', [990,600]);
		        });
		    } </script></div>
EOT;

		/**
        * Totais Almir
        */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

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
}
