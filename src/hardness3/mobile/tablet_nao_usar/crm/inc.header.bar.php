<!-- <div data-theme="a" data-role="header" style="font-size: 110%;">
    <h3 style="margin: 0.3em 40px 0.3em;">
        <div class="hleft"><?=$g['C031']['tabletHeaderTexto']?></div>
        <div class="hright"><span style="color: #F15928;">HARDNESS</span> SISTEMAS</div>
    </h3>
</div>  -->
<div style="float:left; position:relative; width:100%; margin-top:35px;">
</div>
<?php if (isset($etapa)) { 
		$gridData = 'c';
		if ($T005['T005_Flag_Esta_No_Caixa'] != 'S' && $T005['T005_Flag_Esta_No_Caixa'] != 'R') {
			$gridData = 'd';
		}
		
		$request = isset($_REQUEST['T003_Id']) ? "T003_Id={$_REQUEST['T003_Id']}" : false;
		$request = ($request == false && isset($_REQUEST['T005_Id'])) ? "T005_Id={$_REQUEST['T005_Id']}" : $request;
		//Determina Tamanhos dos Links do menu, variado por tipo de Usuário
		if($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S'){
			$dataGrid = "f";
		}else if($C007['C007_Flag_Vendedor']=='N'){
			$dataGrid = "a";
		}else{
			$dataGrid = "e";
		}
?>		
	<div data-role="collapsible" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-collapsed="true" data-theme="a" data-content-theme="a" style="position:absolute; z-index:1000; width:98%; margin-left:1%;  margin-top:3px;">
		<h3>
			<div style="float:left;" ><?=$g['C031']['tabletHeaderTexto']?></div>
	        <div style="float:right;" ><span style="color: #F15928;">HARDNESS</span> SISTEMAS</div>
        </h3>
		<div id="menuBar" data-role="navbar" data-theme="a" class="nav-glyphish"  data-grid="<?php echo $dataGrid; ?>">
			<ul class="menuPrincipal">
				<?php 
				if($C007['C007_Flag_Vendedor']!='N'){
				?>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/orcamentoLista.php" id="menu-orcamento" data-transition="fade" data-icon="custom" data-ajax="false" >Vendas</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/requisicoes.php" id="menu-requisicao" data-transition="fade" data-icon="custom" data-ajax="false" >Requisições</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/servicos.php" id="menu-servico" data-transition="fade" data-icon="custom" data-ajax="false" >Serviços</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/clientesLista.php" id="menu-cliente" data-transition="fade" data-icon="custom" data-ajax="false" >Clientes</a></li>
				<?php 	
				}
				?>
				<?php 
				if($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S' or $C007['C007_Flag_Vendedor']=='V'){
				?>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/produtos.php" id="menu-produto-precos" data-transition="fade" data-icon="custom" data-ajax="false" >Produtos</a></li>	
				<?php 
				}
				?>
				<?php 
				if($C007['C007_Flag_Vendedor']=='D' OR $C007['C007_Flag_Vendedor']=='S' or $C007['C007_Flag_Vendedor']=='N'){
				?>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/caixa/caixaLista.php" id="menu-caixa" data-transition="fade" data-icon="custom" data-ajax="false" >Caixa</a></li>
				<?php 	
				}
				echo $blink_ini;
				?>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/conversas.php" id="menu-conversas" data-transition="fade" data-icon="custom" data-ajax="false" <?php echo "style='$style_conversas'"; ?> >Conversas <?php echo $total_conversas;  ?></a></li>
				<?php 	
				echo $blink_fim;
				?>
				<!-- <li><a href="./relatorios.php" id="menu-relatorio" data-transition="fade" data-icon="custom" data-ajax="false" <?php //if ($etapa == 6) { echo 'class="ui-btn-active"'; } ?>>Relatórios</a></li> -->	
					</ul>
				</div>
		<div id="menuBar" data-role="navbar" data-theme="a" class="nav-glyphish"  data-grid="<?php echo $dataGrid; ?>" style="margin-top:2px;">
			<ul class="menuPrincipal">
				<?php 
				if($C007['C007_Flag_Vendedor']=='D'){
				?>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/nota_fiscal/nota_fiscalLista.php" id="menu-nota-fiscal" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 8) { echo 'class="ui-btn-active"'; } ?>>Notas Fiscais</a></li>	
				<li><a data-theme="c" href="/hardness3/mobile/tablet/analise_credito/analise_credito.php" id="menu-analise-credito" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 9) { echo 'class="ui-btn-active"'; } ?>>Análise de Crédito</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/financeiro/contasReceber.php" id="menu-contas-receber" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 10) { echo 'class="ui-btn-active"'; } ?>>Contas a Receber</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/financeiro/contasPagar.php" id="menu-contas-pagar" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 11) { echo 'class="ui-btn-active"'; } ?>>Contas a Pagar</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/crm/resumoClientes.php" id="menu-resumos-gerenciais" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 12) { echo 'class="ui-btn-active"'; } ?>>Resumos Gerênciais</a></li>
				<?php 
				}
				?>
			</ul>
		</div>
	</div>
<div data-role="navbar" data-theme="a" class="nav-glyphish" data-grid="e" style="clear:both; margin-top:45px;">
	<ul>
		<li style="width:16.66%;"><a href="./resumoClientes.php"  id="menu-cliente" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 1) { echo 'class="ui-btn-active"'; } ?>>Clientes</a></li>
		<li style="width:16.66%;"><a href="./resumoOrcamentos.php"  id="menu-orcamento" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 2) { echo 'class="ui-btn-active"'; } ?>>Orçamentos</a></li>
		<li style="width:16.66%;"><a href="./resumoPedidos.php"  id="menu-orcamento" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 3) { echo 'class="ui-btn-active"'; } ?>>Pedidos</a></li>
		<li style="width:16.66%;"><a href="./resumoNotasFiscais.php" id="menu-nota-fiscal" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 4) { echo 'class="ui-btn-active"'; } ?>>Notas Fiscais</a></li>
		<li style="width:16.66%;"><a href="./resumoDevolucoes.php" id="menu-devolucao" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 5) { echo 'class="ui-btn-active"'; } ?>>Devoluções</a></li>
		<li style="width:16.66%;"><a href="./resumoCompras.php" id="menu-compra" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 6) { echo 'class="ui-btn-active"'; } ?>>Compras</a></li>
	</ul>
</div>

<?php } ?>
