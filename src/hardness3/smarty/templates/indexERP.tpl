<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>{$titulo}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		{$cssJs}
	</head>
	{if $usuarioAtual}
	<body style='overflow: hidden;' onload="setTimeout(function () { abrirJanela(event, 'erp001', '', menuId('erp001'), 'E.R.P', '.: E.R.P :.', '/erp/erp001/content/erp001ContentPrincipal/', '', 'full', false, 'topleft', false); }, 200);"> 
	{else}
	<body style='overflow: hidden;'> 
	{/if}
		<div class="main">
			<div id="topo">
				<div id="topoMenu">
					{$menu}
					<div style="margin: 27px 17px; font-size: 12px; float: left; position: absolute; color: #FFFFFF;">HARDNESS SISTEMAS - EMISSOR NF-E</div>
					<!--<ul id="barra" class="menubar ui-widget-header ui-corner-bottom">
						<div style="margin: 27px 17px; font-size: 12px;">GRUPO BOTICÁRIO - EMISSOR NF-E</div>
					</ul>-->
				</div>
				<div id="logo"></div>
				<!--<div id="header-info">
					<p>...</p>
				</div>-->
			</div>
			<div id="desktop"></div>
			<div id="logo" style="cursor: pointer;" onclick="abrirJanela(event, divIdRootAberto(), '', menuId('sobre'), 'Sobre o Sistema', 'Sobre o Sistema', '/sistema/outros/sobre/content/', '', [530,370], false, 'center'); return false;"></div>
			<div id="footer">
				{if $usuarioAtual}
					<div style="margin: 1px 0 0 5px; float: left;">
						{if $selectEmpresa}
							Empresa:&nbsp;&nbsp;<font id="campoEmpresa"><span style="margin-right: 12px;">{$empresaAtualNome}</span></font>
							<select id="selecionaEmpresa" style="margin-left: -3px;" class="btnselect ui-corner-all" onchange="changeEmpresaAtual(this);" onblur="esconderEmpresa();">{$selectEmpresa}</select>
						{else}
							Empresa:&nbsp;&nbsp;{$empresaAtualNome}&nbsp;&nbsp;&nbsp;&nbsp;
						{/if}
						{if $selectUsuario}
							Usuário:&nbsp;<font id="campoUsuario"><span style="margin-right: 12px;">{$usuarioAtualNome}</span></font>
							<select id="selecionaUsuario" style="margin-left: -3px;" class="btnselect ui-corner-all" onchange="changeUsuarioAtual(this)" onblur="esconderUsuario();">{$selectUsuario}</select>
						{else}
							Usuário:&nbsp;&nbsp;{$usuarioAtualNome}&nbsp;&nbsp;&nbsp;&nbsp;
						{/if}
						Perfil:&nbsp;{$usuarioAtualPerfil}&nbsp;&nbsp;&nbsp;&nbsp;
						Base:&nbsp;{$dbDatabase}&nbsp;&nbsp;&nbsp;&nbsp;
						<!--<span onclick="window.location = '/logout';" style="margin-top: 2px; cursor: pointer; font-weight: bold;">Sair</span>-->
					</div>
					<div style="margin: 0px 230px 0 0;  float: right;">
						<!--<span id="alertFooterIcon" style="margin-right: 5px;"></span> -->
						<!--<span id="fontSizeIcon" style="margin-right: 5px;"></span> -->
						<!--<button class="btn ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="width: 35px;" onclick="window.location = '/logout';">Sair</button>-->
					</div>
					<br><br>
				{/if}
			</div>
		</div>

	</body>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#selecionaEmpresa').hide();
		$('#selecionaUsuario').hide();

		
		
		$('#campoEmpresa').unbind('click').bind('click', function() {
			$('#campoEmpresa').hide();
			$('#selecionaEmpresa').show();
		});	
		
		$('#campoUsuario').unbind('click').bind('click', function() {
			$('#campoUsuario').hide();
			$('#selecionaUsuario').show();
		});

		function esconderEmpresa() {
			$('#selecionaEmpresa').hide();
			$('#campoEmpresa').show();
		}

		function esconderUsuario() {
			$('#selecionaUsuario').hide();
			$('#campoUsuario').show();
		}

		function fixMenuExtraHtml() {
			var menu1Size = $('#footer > div').outerWidth() + 14
			var menu2Size = 0;
			$('#topoMenu li').each(function() {
				menu2Size += $(this).outerWidth();
			});
			var size = (menu2Size > menu1Size) ? menu2Size : menu1Size;
			$('#menuextrahtml')
				.css({
					'left': size - 10
				})
				.show();
		}

		// Globais
		var g = {
			'empresaAtual': '{$empresaAtual}',
			'personalizacaoConf': {$personalizacaoConf},
			'alertaEmailRecebido' : {$alertaEmailRecebido},
			'menuExtraEmpresas': {$menuExtraEmpresas}
		};
		$(document).ready(function() {
			{$teclasAtalho}
			fixMenuExtraHtml();
		});
	}</script></div>
</html>