<?  
namespace hardness;

include('conf.php');
include('inc.conf.php');

include('inc.header.php');
$etapa = 1;
include('inc.header.page.php');	 
?>
		<div id="menuBar" data-theme="a" class="menuOrcamento" data-grid="<?php echo $dataGrid; ?>">
			<ul data-role="listview">
				<li><a href="/hardness3/mobile/phone/orcamentoLista.php?statusOrcamento=X" id="orcamento-todos" data-transition="fade" data-icon="custom"><img class="ui-li-icon">Todos</a></li>
				<li><a href="/hardness3/mobile/phone/orcamentoLista.php?statusOrcamento=P" id="orcamento-pendente" data-transition="fade" data-icon="custom"><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/white-dot.png" class="ui-li-icon">Pendente</a></li>
				<li><a href="/hardness3/mobile/phone/orcamentoLista.php?statusOrcamento=L" id="orcamento-analise" data-transition="fade" data-icon="custom"><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/blue-dot.png" class="ui-li-icon">Em Análise</a></li>
				<li><a href="/hardness3/mobile/phone/orcamentoLista.php?statusOrcamento=N" id="orcamento-negado" data-transition="fade" data-icon="custom"><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/red-dot.png" class="ui-li-icon">Negado</a></li>
				<li><a href="/hardness3/mobile/phone/orcamentoLista.php?statusOrcamento=A" id="orcamento-aprovado" data-transition="fade" data-icon="custom"><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/green-dot.png" class="ui-li-icon">Aprovado</a></li>
				<li><a href="/hardness3/mobile/phone/orcamentoLista.php?statusOrcamento=F" id="orcamento-faturado" data-transition="fade" data-icon="custom"><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/gray-dot.png" class="ui-li-icon">Faturado</a></li>	
				<li><a href="/hardness3/mobile/phone/orcamentoLista.php?statusOrcamento=V" id="orcamento-vencido" data-transition="fade" data-icon="custom"><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/orange-dot.png" class="ui-li-icon">Vencido</a></li>
				<!-- <li><a href="./relatorios.php" id="menu-relatorio" data-transition="fade" data-icon="custom" data-ajax="false" <?php //if ($etapa == 6) { echo 'class="ui-btn-active"'; } ?>>Relatórios</a></li> -->
				
			</ul>
		</div>
	