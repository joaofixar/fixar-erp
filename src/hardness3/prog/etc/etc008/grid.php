<?php
namespace hardness;

require_once('prog/etc/etc008/grid_func-callback.php');

// Carrega funções callback
//require_once('prog/cad/cad016/grid_func-callback.php');

switch ($g['r'][3]) {
	case 'etc008GridBancoDadosClientes':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D131_Id', $g['divId'])),
			//array('colunaValor' => 'D131_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			//array('colunaValor' => 'D116_Id', 'colunaTitulo' => 'Id Veículo', 'colunaExibir'=>false),
			//array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículos'),
			array('colunaValor' => 'C024_Banco_Dados', 'colunaTitulo' => 'C024_Banco_Dados', 'colunaExibir'=>false, 'chavePrimaria'=>true),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			array('colunaValor' => 'C024_Banco_Dados', 'colunaTitulo' => 'BD', 'style' => 'width:1%'),
			array('colunaValor' => 'C025_Dominio', 'colunaTitulo' => 'Domínio'),
			array('colunaValor' => 'C026_Razao_Social', 'colunaTitulo' => 'Contabilidade'),
			// array('colunaValor' => '(select A001_Data_Acesso from A001 where A001_D024_Id=D024_Id order by A001_Data_Acesso desc limit 1) as Ultimo_Acesso', 'colunaTitulo' => 'Último Acesso', 'callback'=>'gCorrigeData'),
			// array('colunaValor' => '(select datediff(curdate(),A001_Data_Acesso) from A001 where A001_D024_Id=D024_Id order by A001_Data_Acesso desc limit 1) as Dias_Ultimo_Acesso', 'colunaTitulo' => 'Dias'),
			// array('colunaValor' => '(select count(*) from A001 where A001_D024_Id=D024_Id and A001_Data_Acesso>=(curdate() - interval 30 day)) as Frequencia_Acesso', 'colunaTitulo' => 'Frq'),
			array('colunaValor' => '(select datediff(curdate(),T117_Data_Vencimento) from T117 left join T002 on T002_T117_Id=T117_Id where T117_D024_Id=D024_Id and T002_Data_Recebimento="0000-00-00" order by T117_Data_Vencimento limit 1) as Atraso', 'colunaTitulo' => 'Ats', 'callback'=>'validaCampoAts'),
			array('colunaValor' => 'Acessar Local', 'colunaTitulo' => 'Local', 'callback'=>'validaCampoAcessoLocal', 'colunaTipo'=>'livre', 'colunaExibir' => false),
			array('colunaValor' => 'Acessar Remoto', 'colunaTitulo' => 'Remoto', 'callback'=>'validaCampoAcessoRemoto', 'colunaTipo'=>'livre', 'colunaExibir' => false),
			array('colunaValor' => '(select T117_Data_Vencimento from T117 left join T002 on T002_T117_Id=T117_Id where T117_D024_Id=D024_Id and T002_Data_Recebimento="0000-00-00" order by T117_Data_Vencimento limit 1) as T117_Data_Vencimento', 'colunaTitulo' => 'Data Vencimento', 'colunaExibir'=>false),
			array('colunaValor' => '(select T117_Id from T117 where T117_D024_Id=D024_Id limit 1) as Tem_Contas', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => 'C025_Banco_Dados', 'colunaTitulo' => 'C025_Banco_Dados', 'colunaExibir'=>false),
			array('colunaValor' => 'C025_Senha', 'colunaTitulo' => 'C025_Senha', 'colunaExibir'=>false),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'colunaExibir'=>false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'C024';

		//left join D116 on D116_Id=D131_D116_Id
		$extra = <<<EOT
					left join C025 on C025_D024_Id=C024_D024_Id
				    left join C026 on C026_Id=C025_C026_Id
				    left join D024 on D024_Id=C024_D024_Id
				    group by C024_Banco_Dados
EOT;
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc008FormBancosDadosClienteBtnIncluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc008FormBancosDadosClienteBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Nova Empresa', '/etc/etc008/content/etc008ContentFormCadastroDB/', '&tab=geral', [1000,800]);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			'C024_Banco_Dados' => array('titulo' => 'Banco de Dados', 'tipo' => 'texto'),
			'C026_Razao_Social' => array('titulo' => 'Contabilidade', 'tipo' => 'texto'),
			//'Ultimo_Acesso' => array('titulo' => 'Último Acesso', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
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
					var chavePrimaria = 'C024_Banco_Dados';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var C024_Banco_Dados = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Especificações de Veículos - ID: ' + C024_Banco_Dados, '/etc/etc008/content/etc008ContentFormBancoDadosClientesIncluir/', '&C024_Banco_Dados=' + encodeURIComponent(C024_Banco_Dados), [400,300]);
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
}

?>
