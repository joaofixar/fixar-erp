<?php
namespace hardness;

/**
 * Ações
 */
switch($g['r'][3]) {
	// Grid com SQL Manual
	case 'etc012Vendas':
		$data = $r_data;
	    $C007 = mysql_query("select C007_Id, C007_Primeiro_Nome from C007");
	    $geradoDados = array();
	    while ($mC007=mysql_fetch_array($C007))
	    {
		    $T003=mysql_query("select sum(T003_Valor_Total) total,
		                              count(*) itens
		                         from T003
		                        where T003_Data_Emissao = '$data'
		                          and T003_C007_Id_Vendedor_Interno='$mC007[C007_Id]'");
		    $mT003=mysql_fetch_array($T003);
		    
		    $T005=mysql_query("select sum(T005_Valor_Total) total,
		                              count(*) itens
		                         from T005
		                        where T005_Data_Emissao = '$data'
		                          and T005_C007_Id_Vendedor_Interno='$mC007[C007_Id]'");
		    $mT005=mysql_fetch_array($T005);

		    $T007=mysql_query("select sum(T007_Valor_Total) total,
		                              count(*) itens
		                         from T007
		                        where T007_Data_Emissao = '$data'
		                          and T007_C007_Id_Vendedor_Interno='$mC007[C007_Id]'");
		    $mT007=mysql_fetch_array($T007);
		    
		    if (($mT003['total']+$mT005['total']+$mT007['total'])>0)
		    {
		        $geradoDados[]= array($mC007['C007_Primeiro_Nome'], $mT003['total'], $mT003['itens'], $mT005['total'], $mT005['itens'], $mT007['total'], $mT007['itens']);
			}
		}
	                        
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "Vendedor", 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left'),
			array('colunaValor' => "Orcamentos", 'colunaTitulo' => 'Orcamentos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => "Qte", 'colunaTitulo' => 'Qte', 'colunaAlinhar' =>'center'),
			array('colunaValor' => "Pedidos", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => "Qte", 'colunaTitulo' => 'Qte', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "Notas", 'colunaTitulo' => 'Notas', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => "Qte", 'colunaTitulo' => 'Qte', 'colunaAlinhar' => 'center'),
		);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id), [600,500]);
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
        $g['smarty']->assign('gridOptNoShow', true); // ocultar
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;
	
	// Grid com SQL Manual
	case 'etc012Financeiro':
		$data = $r_data;

		$T019R=mysql_query("select sum(T019_Valor_Lancamento) total,
	                              count(*) itens
	                         from T019
	                        where T019_Data_Lancamento = '$data'
	                          and T019_Valor_Lancamento>0");
	    $mT019R=mysql_fetch_array($T019R);
		    
		$T019P=mysql_query("select sum(T019_Valor_Lancamento) total,
	                              count(*) itens
	                         from T019
	                        where T019_Data_Lancamento = '$data'
	                          and T019_Valor_Lancamento<0");
	    $mT019P=mysql_fetch_array($T019P);

        $geradoDados[]= array($mT019R['total'], $mT019R['itens'], $mT019P['total'], $mT019P['itens'], $mT019R['total']+$mT019P['total']);
	                        
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "Recebido", 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => "Titulos", 'colunaTitulo' => 'Titulos', 'colunaAlinhar' =>'center'),
			array('colunaValor' => "Pago", 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => "Titulos", 'colunaTitulo' => 'Qte', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "Resultado", 'colunaTitulo' => 'Diferenca', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
		);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id), [600,500]);
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
		$g['smarty']->assign('gridOptNoShow', true); // ocultar
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;
}
?>
