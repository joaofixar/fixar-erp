<?php
namespace hardness;
?>

<style>
	#menuBar .ui-icon-custom {
		padding: 6px 15px !important;
		left: 6px;
	}
	#menuBar .ui-btn-inner {
	    padding-top: 15px !important;
	    padding-bottom: 15px !important;
	}
</style>

<div class="menu" data-role="collapsible" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-collapsed="true" data-theme="a" data-content-theme="a" style="z-index:1000; width:95%; margin: auto; margin-top: 3px; margin-bottom: 3px;">
	<h3>
		<div style="text-align: center;" ><?=$g['C004']['C004_Nome_Abreviado']?></div>
	</h3>
	<div id="menuBar" data-role="navbar" data-theme="a" class="nav-glyphish">
		<ul class="menuPrincipal">
			<li><a style="font-size: 16px;" data-theme="c" href="/hardness3/mobile/phone/orcamentoLista.php" id="menu-orcamento" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 1) { echo 'class="ui-btn-active "'; } ?>>Vendas</a></li>
		</ul>
		<ul class="menuPrincipal">
			<li><a style="font-size: 16px;" data-theme="c" href="/hardness3/mobile/phone/clientesLista.php" id="menu-cliente" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 5) { echo 'class="ui-btn-active "'; } ?>>Clientes</a></li>
		</ul>
		<ul class="menuPrincipal">
			<li><a style="font-size: 16px;" data-theme="c" href="/hardness3/mobile/phone/produtos.php" id="menu-produto-precos" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 6) { echo 'class="ui-btn-active "'; } ?>>Produtos</a></li>
		</ul>
		<ul class="menuPrincipal">
			<li><a style="font-size: 16px;" data-theme="c" href="/hardness3/mobile/phone/nota_fiscal/nota_fiscalLista.php" id="menu-nota-fiscal" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 7) { echo 'class="ui-btn-active "'; } ?>>Notas Fiscais</a></li>
		</ul>
		<? if(isset($g['c029Ids'][110])){ ?> <ul class="menuPrincipal">
			<li><a style="font-size: 16px;" data-theme="c" href="/hardness3/mobile/phone/inventario.php" id="menu-estoque" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 8) { echo 'class="ui-btn-active "'; } ?>>Inventário</a></li>
		</ul> <? } ?>
		<ul class="menuPrincipal">
			<li><a style="font-size: 16px;" data-theme="c" href="/hardness3/mobile/phone/mudarEmpresa.php" id="menu-empresa" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 99) { echo 'class="ui-btn-active "'; } ?>>Mudar empresa</a></li>
		</ul>
		<ul class="menuPrincipal">
			<li><a style="font-size: 16px;" data-theme="c" href="/hardness3/mobile/phone/login.php" id="menu-sair" data-transition="fade" data-icon="custom" data-ajax="false">Sair</a></li>
		</ul>
	</div>
</div>

