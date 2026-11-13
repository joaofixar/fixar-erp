<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<title>{$titulo}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript">
			// monaco editor (VS Code)
			var require = { paths: { 'vs': '/hardness3/static/js/monaco-editor/0.25.2/min/vs' } };
		</script>
		{$cssJs}
		<style type="text/css">
		{$cssExtra}
		</style>

		{* Fontes *}
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
	</head>

	<body style="overflow: hidden;{if $oneWindow} background: url('/hardness3/static/css/temas/padrao/images/ui-bg_dots-small_100_ede7de_2x2.png') repeat scroll 50% 50% #EDE7DE;{/if}">
		{if !$oneWindow}
			<div id="messageAlert">
				<div class="click">
					<div class="icone"></div>
					<div class="alerts"></div>
				</div>
				<div class="messages">
					<div class="header">
						<div>
							{if $permissaoSuporte===true}
								<button class="enviarSuporte ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="position: absolute; left: 5px; top: 4px; cursor: pointer; padding: 2px 13px; font-size: 13px;">Suporte
									<div class="alertsSuporte">0</div>
								</button>
							{/if}
							<span>Notificações</span>
							<img class="exibirOcultarNotificacoes" src="/hardness3/static/img/check.png" style="-webkit-filter: opacity(70%); filter: opacity(70%); position: absolute; right: 65px; top: 5px; cursor: pointer; width: 20px; height: 20px; " title="Exibir notificações desabilitadas">
							<img class="refreshNotifications" src="/hardness3/static/img/icone_refresh_preto.png" style="-webkit-filter: opacity(70%); filter: opacity(70%); position: absolute; right: 38px; top: 5px; cursor: pointer; width: 20px; height: 20px; " title="Atualizar notificações">
							<img class="enviarMensagem" src="/hardness3/static/img/icon-send-message-black.png" style="-webkit-filter: opacity(70%); filter: opacity(70%); position: absolute; right: 6px; top: 3px; width: 24px; height: 24px; cursor: pointer" title="Enviar mensagem">
							<!--<img class="enviarMensagem" src="/hardness3/static/img/icon-send-message.png" style="position: absolute; right: 6px; top: 5px; cursor: pointer" title="Enviar mensagem">
							<img class="agendarMensagem" src="/hardness3/static/img/icone-compromisso.png" style="position: absolute; right: 36px; top: 3px; cursor: pointer;" title="Agendar uma mensagem">-->
						</div>
					</div>
					<div class="body">
						<ul>
							<li class="boxenviarmensagem" style="text-align: left;">
								<div>
									Para: <select style="height: 21px;">{$selectUsuarioMensagem}</select>{if $usuarioAtual == 63}&nbsp;&nbsp;<input type="checkbox" name="msgTravar" value="1"> Travar{/if}<br/>
									<textarea placeholder="Digite a mensagem" style="width: 98%; height: 61px; margin-top: 4px;"></textarea><br/>
									<div style="margin: 2px 0px; font-weight: bold;">Agende esta mensagem</div>
									Data: <input class="campoData" type="text" style="width: 67px" />&nbsp;&nbsp;Hora: <input class="campoTempo" type="text" onkeyup="return(Formatar_Tempo(this));" style="width: 67px;" /><br/>
									<button class="btn" style="float: right; margin: -20px 1px -2px 0;">Enviar</button>
									<span style="float: left; margin: 3px 1px -2px 0; color: red; font-weight: bold; margin: 8px 0 0 2px; display: none;">* Preencha todos os campos.</span>
									<div style="clear: both;"></div>
								</div>
							</li>
							<li class="mensagemenviada" style="text-align: center; font-weight: bold; background-color: #DFDFDF;">
								<div>Mensagem enviada!</div>
							</li>
							<li class="vazio">
								<div>Você não possui nenhuma mensagem ou alerta.</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		{/if}
		<div class="main">
			<div id="desktop">
				{if !$oneWindow}
					{$certificado}
					{$senhaDesatualizada}
					{$versao}
					{$boleto}
					{$avisos}
				{/if}
			</div>
			{if !$oneWindow}
				<!--<div id="toolbar" class="ui-widget-header ui-corner-top"></div>-->
			{/if}
			{if !$oneWindow}
				<div id="footer">
					{if $usuarioAtual}
						<div style="margin-left: 17px; margin-top: 5px; float: left;">
							{if $selectEmpresa}
								<font id="campoEmpresa"><span style="margin-right: 12px;">{$empresaAtualNomeAbreviado}</span></font>
								<select id="selecionaEmpresa" style="margin-left: -3px;" class="btnselect ui-corner-all" onchange="changeEmpresaAtual(this);" onblur="esconderEmpresa();">{$selectEmpresa}</select>
							{else}
								{$empresaAtualNome}&nbsp;&nbsp;
							{/if}
							{if $selectUsuario}
								|&nbsp;&nbsp;<font id="campoUsuario"><span style="margin-right: 12px;">{$usuarioAtualNome}</span></font>
								{* font-size: 14px; *}
								<select id="selecionaUsuario" style="margin-left: -3px; font-family: monospace;" class="btnselect ui-corner-all" onchange="changeUsuarioAtual(this)" onblur="esconderUsuario();">{$selectUsuario}</select>
							{else}
								|&nbsp;&nbsp;{$usuarioAtualNome}&nbsp;&nbsp;
							{/if}
							|&nbsp;&nbsp;{$usuarioAtualPerfil}&nbsp;
							|&nbsp;&nbsp;<font title='{$dbUsuario}'>{$dbDatabase}</font>&nbsp;&nbsp;
							{if $modoDesenvolvedor}
								<span style="font-weight: bold; margin-right: 16px;background: red;color: #ffffff; padding: 4px; border-radius: 10px;"> &nbsp; *** Modo IDE *** &nbsp; </span>
							{/if}
							{if $modoDesenvolvedorApp}
								<span id="modoDesenvolvedorApp" style="font-weight: bold; margin-right: 16px;background: red;color: #ffffff; padding: 4px; border-radius: 10px;"> &nbsp; *** Editando App: {$modoDesenvolvedorApp} *** &nbsp; </span>
							{/if}
						</div>
						<div style="margin: 25px 100px 0 0;  float: left;">
							<!--<span id="alertFooterIcon" style="margin-right: 5px;"></span> -->
							<!--<span id="fontSizeIcon" style="margin-right: 5px;"></span> -->
							<!--<button class="btn ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="width: 35px;" onclick="window.location = '/logout';">Sair</button>-->
						</div>
						{if !$isMobile}
							<div id="menuextrahtml" class="menuvertical" style="margin: 0; padding: 0; position: absolute; display: none; top: 3px;" title="Empresa Atual">{$menuextra}</div>
						{/if}
					{else}
						<div style="margin-left: 5px; float: left;">
							Hardness Sistemas - www.hardness.com.br
						</div>
					{/if}
				</div>
			{/if}
			{if !$oneWindow}
				<div id="topo">
					<div id="topoMenu">
						{$menu}
					</div>
					{if !$isMobile}
						{if $permissaoLojaCliente}
							<div class="lojaApp" onclick="abrirJanela(event, 'Loja Apps', '', menuId('lojaAppsCliente'), 'Loja Apps', '.: Loja Apps :.', '/sistema/personalizacaoLowCode/content/appsListarCliente/', '', 'full', false, 'topleft', false);">
								<span class="material-icons">
									cloud_download
								</span>
							</div>
						{/if}
						<div class="zoomButton">
							<i class="zoom material-icons">text_format</i>
						</div>
						<div id="logo" style="margin-top: 3px; cursor: pointer; background-image: url('{$logoMenu}'); background-size: contain; background-repeat: no-repeat; background-position: center;" onclick="abrirJanela(event, divIdRootAberto(), '', menuId('sobre'), 'Sobre o Sistema', 'Sobre o Sistema', '/sistema/outros/sobre/content/', '', [530,370], false, 'center'); return false;"></div>
					{/if}
				</div>
			{/if}
		</div>

	</body>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#selecionaEmpresa').hide();
		$('#selecionaUsuario').hide();
		
		$('#campoEmpresa').unbind('click').bind('click', function() {
			$('#campoEmpresa').hide();
			$('#selecionaEmpresa').show();
			fixMenuExtraHtml();
		});	
		
		$('#campoUsuario').unbind('click').bind('click', function() {
			$('#campoUsuario').hide();
			$('#selecionaUsuario').show();
			fixMenuExtraHtml();
		});

		function esconderEmpresa() {
			$('#selecionaEmpresa').hide();
			$('#campoEmpresa').show();
			fixMenuExtraHtml();
		}

		function esconderUsuario() {
			$('#selecionaUsuario').hide();
			$('#campoUsuario').show();
			fixMenuExtraHtml();
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
			// Abrir menu ao entrar no sistema
			//$('#topoMenu #barra .menuvertical:eq(0) a:first').trigger('mouseover');
		}

		var device = 'desktop';
		
		// Globais
		var g = {
			'empresaAtual': '{$empresaAtual}',
			'usuarioAtual': '{$usuarioAtual}',
			'personalizacaoConf': {$personalizacaoConf},
			'alertaEmailRecebido' : {$alertaEmailRecebido},
			'menuExtraEmpresas': {$menuExtraEmpresas},
			'tamanhosJanelas': {$tamanhosJanelas},
			'janelasIgnorarESC': {$janelasIgnorarESC},
			'oneWindow': '{$oneWindow}',
			'nomeEmpresaAbreviado' : '{$empresaAtualNomeAbreviado}',
			'D024_Id_Hardness' : '{$D024_Id_Hardness}'
		};
		$(document).ready(function() {
			{$teclasAtalho}
			fixMenuExtraHtml();
		});
	}</script></div>
</html>
