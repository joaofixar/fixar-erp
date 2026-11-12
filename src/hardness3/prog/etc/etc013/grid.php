<?php
namespace hardness;

require_once('prog/etc/etc013/grid_func-callback.php');

// Carrega funções callback
//require_once('prog/cad/cad016/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 04/01/2012
	*/                 
	case 'etc013GridAnalises':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('I001_Id', $g['divId'])),
			array('colunaValor' => 'I001_Id', 'colunaTitulo' => 'Número', 'chavePrimaria'=>true),
			array('colunaValor' => 'I001_Modulo', 'colunaTitulo' => 'Módulo', 'colunaEditar'=>true, 'style' => 'width: 1%;'),
			array('colunaValor' => 'I001_Topico', 'colunaTitulo' => 'Tópico', 'callback' => 'trimTitulo', 'style' => 'width: 1%;'),
			array('colunaValor' => 'I001_C007_Id', 'colunaTitulo' => 'Tópico', 'colunaExibir'=>false),
			//array('colunaValor' => 'I001_Descricao_Pre_Analise', 'colunaTitulo' => 'Pré Análise'),
			//array('colunaValor' => 'I001_Descricao_Analise', 'colunaTitulo' => 'Análise'),
			array('colunaValor' => 'I001_Prioridade', 'colunaTitulo' => 'Prioridade', 'colunaEditar'=>true),
			array('colunaValor' => 'I001_Previsao_Analise_Horas', 'colunaTitulo' => 'Horas Prev.', 'colunaEditar'=>true, 'style' => 'width: 1%;'),
			array('colunaValor' => 'DiasP', 'colunaTitulo' => 'Dias', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDias', 'style' => 'width: 1%;'),
			array('colunaValor' => 'DiasCH', 'colunaTitulo' => 'Horas Conc.', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDiasCH', 'style' => 'width: 1%;'),
			array('colunaValor' => 'DiasCD', 'colunaTitulo' => 'Dias', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDiasCD', 'style' => 'width: 1%;'),
			array('colunaValor'=>'I001_Status', 'colunaTitulo'=>'Status Analise','callback'=>'validaCampoStatus','colunaEditar' => true,
			'colunaEditarGeraSelect' => array(
										array('title' => 'Pendente', 'value' => 'P'), 
										array('title' => 'Execução', 'value' => 'E'),
										array('title' => 'Concluído', 'value' => 'C'),
										array('title' => 'Pré-Analise', 'value' => 'A'),
										array('title' => 'Pós-Análise', 'value' => 'N'))
			),
			array('colunaValor' => 'NumeroOSs', 'colunaTitulo' => 'OS\'s', 'style' => 'width: 1%;', 'colunaTipo'=>'livre', 'callback'=>'validaCampoOss'),
			array('colunaValor' => 'NumeroOSs', 'colunaTitulo' => 'H. OS\'s', 'style' => 'width: 1%;', 'colunaTipo'=>'livre', 'callback'=>'validaCampoHorasOss'),
			array('colunaValor' => 'Status', 'colunaTitulo' => 'Status OS', 'style' => 'width: 1%;', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGeral'),
			array('colunaValor'=>'I001_Tipo', 'colunaTitulo'=>'Tipo Analise','callback'=>'validaCampoTipo','colunaEditar' => true,
			'colunaEditarGeraSelect' => array(
										array('title' => 'Implementação', 'value' => 'I'), 
										array('title' => 'Correção', 'value' => 'C'))
			),
			//array('colunaValor' => 'I001_Previsao_Analise_Data', 'colunaTitulo' => 'Prév. Data', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
			array('colunaValor' => 'I001_Inicio_Analise_Data', 'colunaTitulo' => 'Data Início', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
			array('colunaValor' => 'I001_Inicio_Analise_Hora', 'colunaTitulo' => 'Hora Início', 'callback'=>'gCorrigeTempo', 'colunaEditar'=>true),
			array('colunaValor' => 'I001_Conclusao_Analise_Data', 'colunaTitulo' => 'Data Fim', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
			array('colunaValor' => 'I001_Conclusao_Analise_Hora', 'colunaTitulo' => 'Hora Fim', 'callback'=>'gCorrigeTempo', 'colunaEditar'=>true),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Analista', 'style' => 'width: 1%;'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'I001';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join C007 on C007_Id=I001_C007_Id
			order by I001_Prioridade
		
EOT;

			/**
			* Função que define classes CSS para cada linha do grid
			*/
			function gridLinha($array) {
 			    if ($array['todosCampos']['I001_Status']=='A' or $array['todosCampos']['I001_Previsao_Analise_Horas']<=0) 
 			    {
  			       return 'linhaVermelho';
 			    }
 			    else if ($array['todosCampos']['I001_Status']=='P') 
 			    {
  			       return 'linhaNegrito';
 			    }
 			    else if ($array['todosCampos']['I001_Status']=='C') 
 			    {
  			       return 'linhaCinza';
 			    }
 			    else if ($array['todosCampos']['I001_Status']=='E') 
 			    {
  			       return 'linhaAzul';
 			    }
			 	else if ($array['todosCampos']['I001_Status']=='N') 
 			    {
  			       return 'linhaVerde';
 			    } 			    
			}	
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc013FormControleInternoBtnIncluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc013FormControleInternoBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Análise', '/etc/etc013/content/etc013FormControleInternoIncluir/', '&tab=geral', [1000,700]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'I001_Id' =>array('titulo' => 'Número', 'tipo' => 'numero'),
			'I001_Topico' => array('titulo' => 'Tópico', 'tipo' => 'texto'),
			'C007_Primeiro_Nome' => array('titulo' => 'Analista', 'tipo' => 'texto'),
			'I001_Descricao_Pre_Analise' => array('titulo' => 'Pré Análise', 'tipo' => 'texto'),
			'I001_Status' => array('titulo' => 'Status(P/E/C)', 'tipo' => 'texto'),
			'I001_Tipo' => array('titulo' => 'Tipo(I/C)', 'tipo' => 'texto'),
			'I001_Inicio_Analise_Data' => array('titulo' => 'Inicio', 'tipo' => 'data'),
			'I001_Conclusao_Analise_Data' => array('titulo' => 'Conclusão', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		
		/**
		 * Totalizadores
		 */
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Registros<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'I001_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Análise - ID: ' + acaoId, '/etc/etc013/content/etc013FormControleInternoIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [1000,700]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
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
	
	/**
	* @author AndreySliwinski
	* @since 04/01/2012
	*/                 
	case 'etc013GridOs':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('I002_Id', $g['divId'])),
			array('colunaValor' => 'I002_Id', 'colunaTitulo' => 'Número', 'chavePrimaria'=>true),
			array('colunaValor' => 'I001_Modulo', 'colunaTitulo' => 'Módulo', 'style' => 'width: 1%;'),
			array('colunaValor'=>'PROGRAMADOR.C007_Primeiro_Nome', 'colunaExibir'=>false, 'colunaTitulo'=>'Programador', 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome','' ), 'colunaEditar' => true),
			array('colunaValor'=>'I002_C007_Id', 'colunaTitulo'=>'Programador', 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome','' ), 'colunaEditar' => true, 'callback' => 'mostrarNome', 'style' => 'width: 1%;'),
			array('colunaValor' => 'I002_I001_Id', 'colunaTitulo' => 'Tópico', 'colunaExibir'=>false),
			//array('colunaValor' => 'I001_Topico', 'colunaTitulo' => 'Análise'),
			array('colunaValor' => 'I001_C007_Id', 'colunaTitulo' => 'Tópico', 'colunaExibir'=>false),
			array('colunaValor' => 'I002_Topico', 'colunaTitulo' => 'Tópico', 'callback' => 'trimTitulo', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'I002_Descricao_OS', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'I002_Previsao_OS_Horas', 'colunaTitulo' => 'Prev. (H)', 'colunaEditar'=>true, 'style' => 'width: 1%;'),
			array('colunaValor' => 'DiasP', 'colunaTitulo' => '(D)', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDiasOs', 'style' => 'width: 1%;'),
			array('colunaValor' => 'DiasCH', 'colunaTitulo' => 'Conc. (H)', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDiasOsCH', 'style' => 'width: 1%;'),
			array('colunaValor' => 'DiasCD', 'colunaTitulo' => '(D)', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDiasOsCD', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'I002_Previsao_OS_Data', 'colunaTitulo' => 'Prév. Data', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
			array('colunaValor' => 'I002_Prioridade', 'colunaTitulo' => 'Prioridade', 'colunaEditar'=>true),
			//array('colunaValor' => 'PROGRAMADOR.C007_Primeiro_Nome', 'colunaTitulo' => 'Programador'),
			array('colunaValor'=>'I002_Status', 'colunaTitulo'=>'Status', 'callback'=>'validaCampoStatusOs','colunaEditar' => true,
			'colunaEditarGeraSelect' => array(
				array('title' => 'Pendente', 'value' => 'P'), 
				array('title' => 'Execução', 'value' => 'E'),
				array('title' => 'Concluído', 'value' => 'C'))
			),
			array('colunaValor' => 'I002_Inicio_OS_Data', 'colunaTitulo' => 'Data Início', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
			array('colunaValor' => 'I002_Inicio_OS_Hora', 'colunaTitulo' => 'Hora Início', 'callback'=>'gCorrigeTempo', 'colunaEditar'=>true),
			array('colunaValor' => 'I002_Conclusao_OS_Data', 'colunaTitulo' => 'Data Fim', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
			array('colunaValor' => 'I002_Conclusao_OS_Hora', 'colunaTitulo' => 'Hora Fim', 'callback'=>'gCorrigeTempo', 'colunaEditar'=>true),
			array('colunaValor' => 'I002_Email_Enviado', 'colunaTitulo' => 'Email', 'callback' => 'temEmail', 'style' => 'width: 1%;'),
			array('colunaValor' => 'Analista.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'I002';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join I001 on I001_Id=I002_I001_Id
			left join C007 as ANALISTA on ANALISTA.C007_Id=I001_C007_Id
			left join C007 as PROGRAMADOR on PROGRAMADOR.C007_Id=I002_C007_Id
			order by I002_Prioridade
		
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
 			if ($array['todosCampos']['I002_Previsao_OS_Horas']<=0) 
 			{
  			   return 'linhaVermelho';
 			}
 			else if ($array['todosCampos']['I002_Status']=='P') 
 			{
  			   return 'linhaNegrito';
 			}
 			else if ($array['todosCampos']['I002_Status']=='C') 
 			{
  			   return 'linhaCinza';
 			}
 			else if ($array['todosCampos']['I002_Status']=='E') 
 			{
  			   return 'linhaAzul';
 			}
		}
		
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc013FormControleInternoBtnIncluirOS">Incluir</button>
				<button type="submit" id="etc013OSEnviarEmail">Enviar Email</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc013FormControleInternoBtnIncluirOS').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro OS', '/etc/etc013/content/etc013FormControleInternoIncluirOS/', '&tab=geral', [1000,700]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'I002_Id' =>array('titulo' => 'Número', 'tipo' => 'numero'),
			'I002_Topico' => array('titulo' => 'Tópico', 'tipo' => 'texto'),
			'PROGRAMADOR.C007_Primeiro_Nome' => array('titulo' => 'Programador', 'tipo' => 'texto'),
			'ANALISTA.C007_Primeiro_Nome' => array('titulo' => 'Analista', 'tipo' => 'texto'),
			'I002_Status' => array('titulo' => 'Status(P/E/C)', 'tipo' => 'texto'),
			'I002_Inicio_OS_Data' => array('titulo' => 'Inicio', 'tipo' => 'data'),
			'I002_Conclusao_OS_Data' => array('titulo' => 'Conclusão', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		// Ação Enviar Email
		$ETC013 = urlencode(base64_encode($extra));
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc013OSEnviarEmail').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar E-mail', '/etc/etc006/content/etc006ContentEmailEnviar/', '&ETC013OS=' + encodeURIComponent("{$ETC013}"), [900,550]);
				});
			} </script></div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'I002_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Análise - ID: ' + acaoId, '/etc/etc013/content/etc013FormControleInternoIncluirOS/', '&acaoId=' + encodeURIComponent(acaoId), [1000,700]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Totalizadores
		 */
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Registros<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
				<div style="clear:both;"></div>
			</div>
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
}

?>
