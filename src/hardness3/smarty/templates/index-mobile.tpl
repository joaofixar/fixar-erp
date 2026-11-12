<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>{$titulo}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
		<script type="text/javascript">
			// monaco editor (VS Code)
			var require = { paths: { 'vs': '/hardness3/static/js/monaco-editor/0.25.2/min/vs' } };
		</script>
		{$cssJs}
		<style type="text/css">
			*:not(.icon) {
				font-size: 16px !important;
			}

			* {
				-ms-overflow-style: none !important;
				scrollbar-width: none !important;
			}
			*::-webkit-scrollbar {
				width: 0px !important;
				height: 0px !important;
				display: none !important;
			}

			html, body, table, td, th {
				-webkit-text-size-adjust: 100% !important;
				text-size-adjust: 100% !important;
			}

			ul.menubar {
				display: flex;
				flex-wrap: nowrap;
				overflow-y: hidden;
				overflow-x: auto;
			}

			ul.menubar .menuvertical:first-child {
				padding-left: 3px;
			}

			ul.menubar .menuvertical:last-child {
				padding-right: 10px;
			}

			#messageAlert .icone {
				top: 5px;
    			right: 30px;
				width: 24px;
				height: 24px;
			}

			#messageAlert .alerts {
				top: 9px;
				right: 5px;
			}

			#messageAlert .messages {
				width: 100%;
				height: calc(100% - 51px);
				top: 51px;
			}

			.ui-tabs .ui-tabs-nav li a {
				padding-top: 0.2em;
    			padding-bottom: 0;
			}

			.contentCabecalho {
				height: 48px !important;
			}

			.contentCabecalhoLayout {
				overflow-x: auto;
				white-space: nowrap;
			}

			.formEditor {
				display: block;
			}

			form.formFormulario .responsive-form-flex button.formInput, 
			form.formFormulario .responsive-form-flex input.formInput,
			form.formFormulario .responsive-form-flex input[type="file"].formInput,
			form.formFormulario .responsive-form-flex select.formInput,
			form.formFormulario .responsive-form-flex textarea.formInput,
			form.formFormulario .responsive-form-flex button.formInput[disabled],
			form.formFormulario .responsive-form-flex input.formInput[disabled],
			form.formFormulario .responsive-form-flex select.formInput[disabled],
			form.formFormulario .responsive-form-flex textarea.formInput[disabled] {
				font-family: inherit;
				font-feature-settings: inherit;
				font-variation-settings: inherit;
				font-size: 12px;
				font-weight: inherit;
				line-height: inherit;
				letter-spacing: inherit;
				color: inherit;
				margin: 0;
				padding: 0;
				height: auto;
			}

			form.formFormulario .responsive-form-flex .tw-form-button.itemForm.ui-state-default.ui-corner-all {
				background-image: none !important;
				border: unset !important;
				font-size: 16px !important;
			}

			.content {
				overflow: auto;
			}

			.gridFiltroAvancado {
				overflow: auto;
			}

			.campoFiltro {
				width: max-content;
			}

			{$cssExtra}
		</style>

		{* Fontes *}
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
		<script src="https://cdn.tailwindcss.com?plugins=container-queries"></script>
		<script>
			tailwind.config = {
				prefix: 'tw-',
				corePlugins: { preflight: false },
				important: true,
			}
		</script>
		<style type="text/tailwindcss">
			@layer utilities {
				.tw-form-field {
					@apply !tw-w-full !tw-px-3 !tw-py-2 !tw-border !tw-box-border !tw-border-gray-300 !tw-rounded-md 
					focus:!tw-outline focus:!tw-outline-2 focus:!tw-outline-blue-500 focus:!tw-ring-0 focus:!tw-shadow-none focus:!tw-border-transparent;
				}
				.tw-form-label {
					@apply !tw-w-fit !tw-block !tw-text-sm !tw-font-medium !tw-text-gray-700 !tw-mb-1;
				}
				.tw-form-button {
					@apply !tw-px-4 !tw-py-2 !tw-bg-blue-600 !tw-text-white !tw-rounded-md 
					hover:!tw-bg-blue-700 focus:!tw-outline-none focus:!tw-ring-2 focus:!tw-ring-blue-500 focus:!tw-ring-offset-2 
					!tw-w-full !tw-h-auto !tw-text-xs !tw-shadow-none !tw-transition-none;
				}
			}
		</style>
	</head>

	<body style="overflow: hidden;">
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
				{$certificado}
				{$senhaDesatualizada}
				{$versao}
				{$boleto}
				{$avisos}
			</div>

			<div id="footer">
				{if $usuarioAtual}
					<div style="width: calc(100% - 45px); margin-left: 10px; margin-top: 5px; float: left; display: flex; gap: 8px;">
						<div style="max-width: 50%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
							{if $selectEmpresa}
								<span id="campoEmpresa">{$empresaAtualNomeAbreviado}</span>
								<select id="selecionaEmpresa" style="width: 100%;" class="btnselect ui-corner-all" onchange="changeEmpresaAtual(this);" onblur="esconderEmpresa();">{$selectEmpresa}</select>
							{else}
								{$empresaAtualNome}
							{/if}
						</div>
						<div style="min-width: fit-content;">|</div>
						<div style="max-width: 30%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
							{if $selectUsuario}
								<span id="campoUsuario">{$usuarioAtualNome}</span>
								<select id="selecionaUsuario" style="width: 100%;" class="btnselect ui-corner-all" onchange="changeUsuarioAtual(this)" onblur="esconderUsuario();">{$selectUsuario}</select>
							{else}
								{$usuarioAtualNome}
							{/if}
						</div>
						<div style="min-width: fit-content;">|</div>
						<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
							{$usuarioAtualPerfil}
						</div>
					</div>
				{/if}
			</div>
			<div id="topo">
				<div id="topoMenu">
					{$menu}
				</div>
			</div>
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
		}

		var device = '{$device}';
		
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
