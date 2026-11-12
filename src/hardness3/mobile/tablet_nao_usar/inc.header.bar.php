<?php
namespace hardness;
?>
<div data-role="collapsible" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-collapsed="true" data-theme="a" data-content-theme="a" style="z-index:1000; width:98%; margin: auto; margin-top: 3px; margin-bottom: 3px;">
	<h3>
		<div style="float:left;" ><?=$g['C004']['C004_Nome_Abreviado']?></div>
        <div style="float:right;" ><span style="color: #F15928;">HARDNESS</span> SISTEMAS</div>
    </h3>
	<div id="menuBar" data-role="navbar" data-theme="a" class="nav-glyphish" data-grid="f">
		<ul class="menuPrincipal">
			<li><a data-theme="c" href="/hardness3/mobile/tablet/orcamentoLista.php" id="menu-orcamento" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 1) { echo 'class="ui-btn-active"'; } ?>>Vendas</a></li>
			<li><a data-theme="c" href="/hardness3/mobile/tablet/clientesLista.php" id="menu-cliente" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 5) { echo 'class="ui-btn-active"'; } ?>>Clientes</a></li>
			<li><a data-theme="c" href="/hardness3/mobile/tablet/produtos.php" id="menu-produto-precos" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 6) { echo 'class="ui-btn-active"'; } ?>>Produtos</a></li>	
			<li><a data-theme="c" href="/hardness3/mobile/tablet/nota_fiscal/nota_fiscalLista.php" id="menu-nota-fiscal" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 7) { echo 'class="ui-btn-active"'; } ?>>Notas Fiscais</a></li>
			<? if(isset($g['c029Ids'][110])){ ?>
			<li><a data-theme="c" href="/hardness3/mobile/tablet/inventario.php" id="menu-estoque" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 8) { echo 'class="ui-btn-active"'; } ?>>Inventário</a></li>
			<? } ?>
			<li><a data-theme="c" href="/hardness3/mobile/tablet/mudarEmpresa.php" id="menu-empresa" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 99) { echo 'class="ui-btn-active"'; } ?>>Empresa</a></li>
			<li><a data-theme="c" href="/hardness3/mobile/tablet/login.php" id="menu-sair" data-transition="fade" data-icon="custom" data-ajax="false">Sair</a></li>
		</ul>
	</div>
</div>
