<?php
namespace hardness;

/**
* CAD - CAD067 - Periféricos
*/
include_once('prog/cad/cad067/grid_func-callback.php');

/**
* Ações
*/
	switch($g['r'][3]){
		/**
		* Grid Principal
		*/
		case 'cad067GridImpressora':
			/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
           	array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D175_Id', $g['divId'])),
            array('colunaValor' => 'D175_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-size:12px'),
            array('colunaValor' => 'D175_Tipo_Impressora', 'colunaTitulo' => 'Tipo Impressora', 'callback' => 'tipoImpressora',  'colunaAlinhar' => 'center','style' => 'width:10%;font-size:12px', 'uppercase' => false),
            array('colunaValor' => 'D175_Nome', 'colunaTitulo' => 'Nome da Impressora'),
            array('colunaValor' => 'D175_Observacao', 'colunaTitulo' => 'Observação'),
        );

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D175';

		$extra = <<<EOT

EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad010IncluirImpressora'),
			// array('titulo' => 'Download Fila Impressão - Versão 1.0.1', 'id' => 'cad067DownloadAplicacao'),
			array('titulo' => 'Download SMT Print', 'id' => 'cad067DownloadSMTPrint'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/

		$link = "<a href ='https://drive.google.com/file/d/1hNw7PkpHem-3AVC8PBVOOjA01LDu1xyX/view?usp=sharing' target='_blank'><font color='#3366ff'>";

		$SMTDialog  = "<h4>Siga os passos abaixo para instalar o aplicativo:</h4>";
		$SMTDialog .= "&nbsp;&nbsp;&nbsp;&nbsp;• Faça o download do programa pelo botão abaixo.</br>";
		$SMTDialog .= "&nbsp;&nbsp;&nbsp;&nbsp;• Siga os passos do instalador e finalize a instalação.</br>";
		$SMTDialog .= "&nbsp;&nbsp;&nbsp;&nbsp;• Execute o programa SMT Print pelo menu iniciar.</br>";
		$SMTDialog .= "&nbsp;&nbsp;&nbsp;&nbsp;• Após aberto, o ícone <span><img src='{$g['urlRaiz']}hardness3/static/img/smt-print.png' style='max-height: 12px'/></span> irá aparecer ao lado do relogio.</br>";
		$SMTDialog .= "&nbsp;&nbsp;&nbsp;&nbsp;• Para maiores informações acesse: <a href ='{$g['urlRaiz']}hardness3/static/download/smt-print-1.0.0.pdf' target='_blank'><font color='#3366ff'>Manual de Instalação.pdf</font></a>.</br>";
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad010IncluirImpressora">Incluir</button>
				<button type="button" id="cad067DownloadAplicacao">Download Fila Impressão - Versão 1.0.1</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript">
			if (typeof jQuery != 'undefined') {
				$('#cad010IncluirImpressora').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Impressora', '/cad/cad067/content/cad067ContentCadastroImpressora/', '', [500,400]);
				});
				$('#cad067DownloadAplicacao').unbind('click').bind('click', function(data) {
					 var \$buttons = {
					 	"Instalador hardness.rar": function() {
                            window.open('{$g['urlRaiz']}hardness3/outros/java/ImpressaoJava/hardness.rar');
                            window.close();
                            $(this).dialog("destroy").remove();
						},

						"Atualização impressao.exe - versão 1.0.1": function() {
                            window.open('{$g['urlRaiz']}hardness3/outros/java/ImpressaoJava/hardness/impressao.exe');
                            window.close();
                            $(this).dialog("destroy").remove();
						},

						"Cancelar": function() {
							$(this).dialog("destroy").remove();
						}
					 }
					dialogConfirm('Download Fila Impressão - Versão 1.0.1', "Siga os passos abaixo para instalar a Aplicação Java: <br/><br/><b>Para Download do Instalador (Primeira Instalação): </b><br/><br/>• Crie a pasta \'C:\\\hardness\' na pasta raiz do windowns (C:\\\)<br/>• Faça o Download do Instalador hardness.rar <br/>• Extraia os arquivos de hardness.rar na pasta criada <br/>• Modifique os parâmetros do arquivo impressao.bat<br/> • Execute o arquivo inicializarExe.bat <br/>• Para maiores informações acesse: <a href ='https://drive.google.com/open?id=1hNw7PkpHem-3AVC8PBVOOjA01LDu1xyX' target='_blank'><font color='#3366ff'><b>Manual Fila de Impressão.pdf</b></font></a><br/><br/> <b>Para Atualização do Executável (necessário já estar instalado):</b><br/><br/>• Utilize essa opção sempre que existir uma nova versão do executável<br/>• Remova o arquivo impressao.exe da pasta C:\\\hardness\\\impressao.exe <br/> • Salve o arquivo impressao.exe na pasta C:\\\hardness", \$buttons);
				});
				$('#cad067DownloadSMTPrint').unbind('click').bind('click', function(data) {
					 var \$buttons = {

						"SMT Print - v1.0.0": function() {
                            window.open('{$g['urlRaiz']}hardness3/static/download/smt-print-1.0.0.exe');
                            window.close();
                            $(this).dialog("destroy").remove();
						},

						"Cancelar": function() {
							$(this).dialog("destroy").remove();
						}
					 }
					dialogConfirm('Download SMT Print - v1.0.0', "{$SMTDialog}", \$buttons);
				});
			}
			</script></div>
EOT;


		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D175_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
			'D175_Id' => array('titulo' => 'Função', 'tipo' => 'Numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D175_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cadastro - ID: ' + id, '/cad/cad067/content/cad067ContentCadastroImpressora/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,400]);
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
?>
