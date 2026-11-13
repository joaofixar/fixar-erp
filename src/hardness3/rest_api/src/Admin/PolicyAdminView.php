<?php

declare(strict_types=1);

namespace RestApi\Admin;

class PolicyAdminView
{
    /**
     * @param array<string, string> $context
     */
    public static function render(array $context): string
    {
        $containerId = $context['container_id'] ?? uniqid('api001PolicyAdmin_');
        $accessStatusUrl = $context['access_status_url'] ?? '';
        $accessUnlockUrl = $context['access_unlock_url'] ?? '';
        $statusUrl = $context['status_url'] ?? '';
        $loadTableUrl = $context['load_table_url'] ?? '';
        $previewUrl = $context['preview_url'] ?? '';
        $saveUrl = $context['save_url'] ?? '';
        $removeUrl = $context['remove_url'] ?? '';
        $tokenStatusUrl = $context['token_status_url'] ?? '';
        $tokenCreateUrl = $context['token_create_url'] ?? '';
        $tokenSetStatusUrl = $context['token_set_status_url'] ?? '';
        $docsUrl = $context['docs_url'] ?? '/api/docs';
        $isLocalhostAccess = ($context['is_localhost_access'] ?? '') === '1';
        $lockMessageHtml = $isLocalhostAccess
            ? '<div class="api-rest-lock-localhost-message">Senha não necessária no localhost.</div>'
            : '<input type="text" class="js-api-rest-password api-rest-password-input" name="api_rest_unlock_key" placeholder="Senha master" autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" readonly="readonly" data-lpignore="true" data-1p-ignore="true" />';
        $localhostJsFlag = $isLocalhostAccess ? 'true' : 'false';

        return <<<HTML
		<style>
			#{$containerId} { padding: 12px; font-family: Arial, Helvetica, sans-serif; }
			#{$containerId} .api-rest-subtabs { display: flex; gap: 8px; margin-bottom: 12px; }
			#{$containerId} .api-rest-subtab { border: 1px solid #b9c5d3; background: #eef3f8; color: #16324f; }
			#{$containerId} .api-rest-subtab.active { background: #2d74b8; color: #fff; border-color: #1f5c99; }
			#{$containerId} .api-rest-lock { display: none; background: #fff; border: 1px solid #d9dde3; border-radius: 4px; padding: 16px; margin-bottom: 14px; }
			#{$containerId} .api-rest-lock h3 { margin: 0 0 8px; color: #16324f; }
			#{$containerId} .api-rest-lock p { margin: 0 0 12px; color: #4b5563; font-size: 13px; }
			#{$containerId} .api-rest-lock-meta { margin: 8px 0 0; color: #6b7280; font-size: 12px; }
			#{$containerId} .api-rest-lock-controls { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
			#{$containerId} .api-rest-lock-controls input[type=text] { border: 1px solid #c7ced6; border-radius: 4px; padding: 6px 8px; min-width: 240px; }
			#{$containerId} .api-rest-lock-localhost-message { display: inline-block; color: #166534; background: #dcfce7; font-size: 13px; padding: 6px 10px; min-width: 240px; border-radius: 4px; text-align: center; }
			#{$containerId} .api-rest-docs-link { display: inline-flex; align-items: center; }
			#{$containerId} .api-rest-docs-link button {
				border: 1px solid #d97706;
				background: #f59e0b;
				color: #fff;
				font-weight: bold;
				padding: 6px 12px;
				box-shadow: 0 2px 6px rgba(217, 119, 6, 0.18);
			}
			#{$containerId} .api-rest-docs-link button:hover {
				background: #d97706;
				border-color: #b45309;
			}
			#{$containerId} .api-rest-password-input { -webkit-text-security: disc; }
			#{$containerId} .api-rest-decoy { position: absolute; left: -10000px; top: -10000px; width: 1px; height: 1px; opacity: 0; pointer-events: none; }
			#{$containerId} .api-rest-section { display: none; }
			#{$containerId} .api-rest-section.api-rest-section-active { display: block; }
			#{$containerId} .api-policy-toolbar,
			#{$containerId} .api-policy-summary,
			#{$containerId} .api-policy-editor,
			#{$containerId} .api-token-toolbar,
			#{$containerId} .api-token-summary { background: #fff; border: 1px solid #d9dde3; border-radius: 4px; padding: 14px; margin-bottom: 14px; }
			#{$containerId} .api-policy-toolbar h2,
			#{$containerId} .api-policy-summary h3,
			#{$containerId} .api-policy-editor h3,
			#{$containerId} .api-token-toolbar h2,
			#{$containerId} .api-token-summary h3 { margin: 0 0 10px; color: #16324f; }
			#{$containerId} .api-policy-meta { display: grid; grid-template-columns: repeat(2, minmax(260px, 1fr)); gap: 10px 20px; margin-bottom: 12px; }
			#{$containerId} .api-policy-meta div { font-size: 12px; color: #374151; }
			#{$containerId} .api-policy-meta strong { display: block; font-size: 12px; color: #111827; margin-bottom: 2px; }
			#{$containerId} .api-policy-controls { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
			#{$containerId} .api-policy-controls input[type=text],
			#{$containerId} .api-policy-controls select,
			#{$containerId} .api-policy-search input[type=text] { border: 1px solid #c7ced6; border-radius: 4px; padding: 6px 8px; min-width: 180px; }
			#{$containerId} button { border: 1px solid #1f5c99; background: #2d74b8; color: #fff; border-radius: 4px; padding: 6px 10px; cursor: pointer; }
			#{$containerId} button.secondary { background: #fff; color: #1f5c99; }
			#{$containerId} button.danger { background: #a73737; border-color: #8a2d2d; }
			#{$containerId} .api-policy-summary-list { margin-top: 12px; }
			#{$containerId} .api-policy-summary-row { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #edf0f3; padding: 10px 0; gap: 12px; }
			#{$containerId} .api-policy-summary-row:first-child { border-top: none; padding-top: 0; }
			#{$containerId} .api-policy-summary-row .meta { color: #4b5563; font-size: 13px; }
			#{$containerId} .api-policy-summary-row .actions { display: flex; gap: 8px; }
			#{$containerId} .api-policy-editor { display: none; }
			#{$containerId} .api-policy-modes label { display: block; margin: 6px 0; }
			#{$containerId} .api-policy-search { display: flex; gap: 8px; align-items: center; margin: 10px 0; }
			#{$containerId} .api-policy-bulk { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px; }
			#{$containerId} .api-policy-fields { max-height: 340px; overflow: auto; border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px; background: #fafafa; }
			#{$containerId} .api-policy-field { display: block; padding: 4px 0; font-size: 13px; }
			#{$containerId} .api-policy-table-summary { display: flex; gap: 18px; margin: 12px 0; color: #374151; font-size: 13px; }
			#{$containerId} .api-policy-feedback { display: none; margin-bottom: 10px; padding: 10px 12px; border-radius: 4px; }
			#{$containerId} .api-policy-feedback.success { display: block; background: #ecfdf3; color: #166534; border: 1px solid #a7f3d0; }
			#{$containerId} .api-policy-feedback.error { display: block; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
			#{$containerId} .api-policy-empty { color: #6b7280; font-size: 13px; padding: 12px 0; }
			#{$containerId} .api-policy-preview-content { white-space: pre-wrap; font-family: Consolas, monospace; font-size: 12px; background: #0f172a; color: #e5eef9; padding: 12px; border-radius: 4px; max-height: 360px; overflow: auto; }
			#{$containerId} .api-policy-hint { margin-top: 8px; color: #6b7280; font-size: 12px; }
			#{$containerId} .api-token-meta { display: grid; grid-template-columns: repeat(2, minmax(260px, 1fr)); gap: 10px 20px; margin-bottom: 12px; }
			#{$containerId} .api-token-meta div { font-size: 12px; color: #374151; }
			#{$containerId} .api-token-meta strong { display: block; font-size: 12px; color: #111827; margin-bottom: 2px; }
			#{$containerId} .api-token-controls { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
			#{$containerId} .api-token-controls input[type=text] { border: 1px solid #c7ced6; border-radius: 4px; padding: 6px 8px; min-width: 180px; }
			#{$containerId} .api-token-list { margin-top: 12px; }
			#{$containerId} .api-token-row { border-top: 1px solid #edf0f3; padding: 10px 0; }
			#{$containerId} .api-token-row:first-child { border-top: none; padding-top: 0; }
			#{$containerId} .api-token-row .meta { color: #4b5563; font-size: 13px; line-height: 1.5; }
			#{$containerId} .api-token-row .actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
			#{$containerId} .api-token-secret-panel { display: none; background: #fff8e8; border: 1px solid #f2d18b; border-radius: 4px; padding: 14px; margin: 14px 0; }
			#{$containerId} .api-token-secret-panel.api-token-secret-panel-visible { display: block; }
			#{$containerId} .api-token-secret-content { white-space: pre-wrap; font-family: Consolas, monospace; font-size: 12px; background: #0f172a; color: #e5eef9; padding: 12px; border-radius: 4px; max-height: 360px; overflow: auto; }
			#{$containerId} .api-token-secret-actions { margin-top: 10px; display: flex; gap: 8px; }
		</style>

		<div id="{$containerId}">
			<div class="api-policy-feedback"></div>
			<div class="api-rest-lock js-api-rest-lock" autocomplete="off">
				<h3>Confirmação adicional</h3>
				<p>Informe a senha master para liberar esta área administrativa da API.</p>
				<div class="api-rest-decoy" aria-hidden="true">
					<input type="text" tabindex="-1" autocomplete="username" />
					<input type="password" tabindex="-1" autocomplete="current-password" />
				</div>
				<div class="api-rest-lock-controls">
					{$lockMessageHtml}
					<button type="button" class="js-api-rest-unlock">Liberar acesso</button>
					<div class="api-rest-docs-link">
						<button type="button" class="js-api-rest-open-docs">Abrir documentação</button>
					</div>
				</div>
				<div class="api-rest-lock-meta js-api-rest-lock-meta">A liberação expira automaticamente após alguns minutos.</div>
			</div>
			<div class="api-rest-subtabs">
				<button type="button" class="secondary api-rest-subtab active js-api-rest-subtab" data-section="policy">AccessPolicy</button>
				<button type="button" class="secondary api-rest-subtab js-api-rest-subtab" data-section="tokens">Tokens</button>
			</div>

			<div class="js-api-rest-section api-rest-section api-rest-section-active" data-section="policy">
			<div class="api-policy-toolbar">
				<h2>Administração da AccessPolicy</h2>
				<div class="api-policy-meta">
					<div><strong>Status da policy</strong><span class="js-policy-status">Carregando...</span></div>
					<div><strong>Arquivo</strong><span class="js-policy-path">-</span></div>
					<div><strong>Última alteração</strong><span class="js-policy-last-modified">-</span></div>
					<div><strong>Tabelas configuradas</strong><span class="js-policy-total-tables">0</span></div>
				</div>
				<div class="api-policy-controls">
					<button type="button" class="secondary js-policy-reload">Recarregar Policy</button>
					<button type="button" class="secondary js-api-rest-open-docs">Abrir documentação</button>
					<input type="text" class="js-policy-table-input" placeholder="Tabela, ex.: T005" />
					<button type="button" class="js-policy-load-table">Carregar tabela</button>
				</div>
			</div>

			<div class="api-policy-summary">
				<h3>Resumo geral</h3>
				<div class="api-policy-controls">
					<select class="js-policy-filter-mode">
						<option value="all">Todas</option>
						<option value="all_mode">Tabela inteira</option>
						<option value="block">Block</option>
						<option value="allow">Allow</option>
					</select>
				</div>
				<div class="api-policy-summary-list js-policy-summary-list">
					<div class="api-policy-empty">Nenhuma tabela configurada.</div>
				</div>
			</div>

			<div class="api-policy-editor">
				<h3 class="js-editor-title">Configurar tabela</h3>
				<div class="api-policy-modes">
					<label><input type="radio" name="{$containerId}_mode" value="all" /> Liberar tabela inteira</label>
					<label><input type="radio" name="{$containerId}_mode" value="block" /> Liberar tudo e bloquear campos</label>
					<label><input type="radio" name="{$containerId}_mode" value="allow" /> Liberar somente campos selecionados</label>
				</div>
				<div class="api-policy-hint js-policy-mode-hint">Digite uma tabela e clique em "Carregar tabela" para buscar o schema real e começar a configuração.</div>

				<div class="api-policy-search">
					<input type="text" class="js-policy-field-search" placeholder="Pesquisar campo" />
					<select class="js-policy-field-filter">
						<option value="all">Todos os campos</option>
						<option value="selected">Somente marcados</option>
						<option value="unselected">Somente não marcados</option>
					</select>
				</div>

				<div class="api-policy-bulk">
					<button type="button" class="secondary js-policy-select-all">Selecionar todos</button>
					<button type="button" class="secondary js-policy-clear-all">Limpar seleção</button>
					<button type="button" class="secondary js-policy-preview">Ver PHP gerado</button>
				</div>

				<div class="api-policy-table-summary">
					<div>Campos totais: <strong class="js-policy-total-fields">0</strong></div>
					<div>Liberados: <strong class="js-policy-allowed-fields">0</strong></div>
					<div>Bloqueados: <strong class="js-policy-blocked-fields">0</strong></div>
				</div>

				<div class="api-policy-fields js-policy-fields"></div>

				<div class="api-policy-controls" style="margin-top: 12px;">
					<button type="button" class="js-policy-save">Salvar</button>
					<button type="button" class="secondary js-policy-close-editor">Fechar</button>
					<button type="button" class="danger js-policy-remove-current">Remover tabela</button>
				</div>
			</div>

			<div class="js-policy-preview-modal" title="PHP gerado" style="display:none;">
				<div class="api-policy-preview-content js-policy-preview-content"></div>
			</div>
			</div>

			<div class="js-api-rest-section api-rest-section" data-section="tokens">
				<div class="api-token-toolbar">
					<h2>Administração de Tokens</h2>
					<div class="api-token-meta">
						<div><strong>Total de tokens</strong><span class="js-token-total">0</span></div>
						<div><strong>Tokens ativos</strong><span class="js-token-active-total">0</span></div>
					</div>
					<div class="api-token-controls">
						<button type="button" class="secondary js-token-reload">Recarregar Tokens</button>
						<input type="text" class="js-token-name" placeholder="Nome do token" />
						<input type="text" class="js-token-observation" placeholder="Observação" />
						<button type="button" class="js-token-create">Gerar novo token</button>
					</div>
				</div>

				<div class="api-token-secret-panel js-token-secret-panel">
					<h3>Token gerado</h3>
					<div class="api-token-secret-actions">
						<button type="button" class="secondary js-token-secret-copy">Copiar token</button>
						<button type="button" class="secondary js-token-secret-close">Fechar</button>
					</div>
					<div class="api-token-secret-content js-token-secret-content"></div>
				</div>

				<div class="api-token-summary">
					<h3>Tokens cadastrados</h3>
					<div class="api-token-list js-token-list">
						<div class="api-policy-empty">Nenhum token cadastrado.</div>
					</div>
				</div>
			</div>
		</div>

		<div class="divHidden"><script type="text/javascript">
		if (typeof jQuery !== 'undefined') {
			(function($) {
				var summaryRows = [];
				var tokenRows = [];
				var isLocalhostAccess = {$localhostJsFlag};
				var state = {
					currentTable: '',
					currentMode: 'all',
					fields: [],
					selectedFields: [],
					fieldSearchTerm: '',
					fieldSelectionFilter: 'all',
					currentTokenSecret: '',
					apiUnlocked: false
				};

				function getRoot() {
					return $('#{$containerId}');
				}

				function showFeedback(type, message) {
					var root = getRoot();
					var box = root.find('.api-policy-feedback');
					box.removeClass('success error').addClass(type).html(message).show();
				}

				function clearFeedback() {
					var root = getRoot();
					root.find('.api-policy-feedback').removeClass('success error').hide().html('');
				}

				function switchSection(section) {
					var root = getRoot();
					if (!state.apiUnlocked) {
						return;
					}
					root.find('.js-api-rest-subtab').removeClass('active');
					root.find('.js-api-rest-subtab[data-section="' + section + '"]').addClass('active');
					root.find('.js-api-rest-section').removeClass('api-rest-section-active').hide();
					root.find('.js-api-rest-section[data-section="' + section + '"]').addClass('api-rest-section-active').show();
				}

				function setRestApiAccess(unlocked, expiresAt, ttlMinutes) {
					var root = getRoot();
					state.apiUnlocked = unlocked === true;
					if (state.apiUnlocked) {
						root.find('.js-api-rest-lock').hide();
						root.find('.js-api-rest-password').val('');
						root.find('.js-api-rest-section').hide();
						switchSection('policy');
						refreshStatus();
						refreshTokens();
						return;
					}

					root.find('.js-api-rest-section').removeClass('api-rest-section-active').hide();
					root.find('.js-api-rest-subtab').removeClass('active');
					root.find('.js-api-rest-lock').show();
					var message = 'A liberação expira automaticamente após alguns minutos.';
					if (ttlMinutes) {
						message = 'A liberação expira automaticamente após ' + ttlMinutes + ' minutos.';
					}
					if (expiresAt) {
						message = 'Acesso liberado até ' + formatDate(expiresAt) + '.';
					}
					root.find('.js-api-rest-lock-meta').text(message);
				}

				function formatDate(value) {
					if (!value) { return '-'; }
					var date = new Date(value * 1000);
					if (isNaN(date.getTime())) { return '-'; }
					var pad = function(number) { return number < 10 ? '0' + number : number; };
					return pad(date.getDate()) + '/' + pad(date.getMonth() + 1) + '/' + date.getFullYear() + ' ' + pad(date.getHours()) + ':' + pad(date.getMinutes());
				}

				function postJson(url, data, onSuccess) {
					clearFeedback();
					$.ajax({
						url: url,
						type: 'POST',
						dataType: 'json',
						data: data || {},
						success: function(response) {
							if (!response || response.success !== true) {
								showFeedback('error', response && response.message ? response.message : 'Falha ao processar a requisição.');
								return;
							}
							onSuccess(response.data || {});
						},
						error: function(xhr) {
							var message = 'Falha ao comunicar com o backend.';
							var responseJson = xhr.responseJSON || null;
							if (!responseJson && xhr.responseText) {
								try {
									responseJson = $.parseJSON(xhr.responseText);
								} catch (parseError) {
									responseJson = null;
								}
							}
							if (responseJson && responseJson.message) {
								message = responseJson.message;
							} else if (xhr.responseJSON && xhr.responseJSON.message) {
								message = xhr.responseJSON.message;
							} else if (xhr.responseText) {
								message = String(xhr.responseText).replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').substr(0, 220);
							}
							if (message.indexOf('senha master') !== -1 || message.indexOf('liberar esta área') !== -1) {
								setRestApiAccess(false, 0, 15);
							}
							showFeedback('error', message);
						}
					});
				}

				function refreshAccessStatus() {
					postJson('{$accessStatusUrl}', {}, function(data) {
						setRestApiAccess(data.unlocked === true, data.expires_at || 0, data.ttl_minutes || 0);
					});
				}

				function unlockRestApi() {
					var root = getRoot();
					var password = $.trim(root.find('.js-api-rest-password').val());
					if (!isLocalhostAccess && !password) {
						showFeedback('error', 'Informe a senha master para continuar.');
						return;
					}

					postJson('{$accessUnlockUrl}', { password: password }, function(data) {
						showFeedback('success', 'Área liberada com sucesso.');
						setRestApiAccess(true, data.expires_at || 0, data.ttl_minutes || 0);
					});
				}

				function openDocs() {
					window.open('{$docsUrl}', '_blank');
				}

				function renderSummaryRows() {
					var root = getRoot();
					var filter = root.find('.js-policy-filter-mode').val();
					var list = root.find('.js-policy-summary-list');
					list.empty();

					var filtered = $.grep(summaryRows, function(item) {
						if (filter === 'all') { return true; }
						if (filter === 'all_mode') { return item.mode === 'all'; }
						return item.mode === filter;
					});

					if (filtered.length === 0) {
						list.html('<div class="api-policy-empty">Nenhuma tabela encontrada para o filtro selecionado.</div>');
						return;
					}

					$.each(filtered, function(_, item) {
						var row = $('<div class="api-policy-summary-row"></div>');
						row.append('<div><strong>' + item.table + '</strong><div class="meta">' + item.label + '</div></div>');
						var actions = $('<div class="actions"></div>');
						actions.append($('<button type="button" class="secondary">Editar</button>').bind('click', function() {
							loadTable(item.table);
						}));
						actions.append($('<button type="button" class="danger">Remover</button>').bind('click', function() {
							removeTable(item.table);
						}));
						row.append(actions);
						list.append(row);
					});
				}

				function renderTokenRows() {
					var root = getRoot();
					var list = root.find('.js-token-list');
					list.empty();

					if (tokenRows.length === 0) {
						list.html('<div class="api-policy-empty">Nenhum token cadastrado.</div>');
						return;
					}

					$.each(tokenRows, function(_, item) {
						var statusLabel = item.status === 'active' ? 'Ativo' : 'Inativo';
						var row = $('<div class="api-token-row"></div>');
						var meta = $('<div><strong>' + item.name + '</strong><div class="meta"></div></div>');
						var metaLines = [];
						metaLines.push('ID: ' + item.id);
						metaLines.push('Prefixo: ' + (item.prefix || '-'));
						metaLines.push('Status: ' + statusLabel);
						metaLines.push('Criado em: ' + (item.created_at || '-'));
						metaLines.push('Último uso: ' + (item.last_used_at || '-'));
						metaLines.push('Último IP: ' + (item.last_used_ip || '-'));
						if (item.observation) {
							metaLines.push('Observação: ' + item.observation);
						}
						meta.find('.meta').html(metaLines.join('<br />'));
						row.append(meta);

						var actions = $('<div class="actions"></div>');
						if (item.status === 'active') {
							actions.append($('<button type="button" class="danger">Inativar</button>').bind('click', function() {
								updateTokenStatus(item.id, 'inactive');
							}));
						}
						if (actions.children().length > 0) {
							row.append(actions);
						}
						list.append(row);
					});
				}

				function showTokenSecret(data, title) {
					var root = getRoot();
					var panel = root.find('.js-token-secret-panel');
					var toolbar = root.find('.api-token-toolbar');
					var lines = [];
					state.currentTokenSecret = data.token || '';
					lines.push(title || 'Token gerado com sucesso.');
					lines.push('');
					lines.push('Mostre o token completo apenas uma vez.');
					lines.push('');
					lines.push('Token: ' + (data.token || ''));
					lines.push('Prefixo: ' + (data.prefix || ''));
					lines.push('Hash HMAC: ' + (data.hash || ''));
					lines.push('Status: ' + (data.status || 'active'));
					root.find('.js-token-secret-content').text(lines.join("\\n"));
					root.find('.js-token-secret-copy').text('Copiar token');
					if (panel.length > 0 && toolbar.length > 0) {
						panel.insertAfter(toolbar);
					}
					panel.addClass('api-token-secret-panel-visible').show();
					if (panel.length > 0 && panel.get(0) && typeof panel.get(0).scrollIntoView === 'function') {
						panel.get(0).scrollIntoView(true);
					}
				}

				function closeTokenSecret() {
					var root = getRoot();
					state.currentTokenSecret = '';
					root.find('.js-token-secret-content').text('');
					root.find('.js-token-secret-copy').text('Copiar token');
					root.find('.js-token-secret-panel').removeClass('api-token-secret-panel-visible').hide();
				}

				function copyTokenSecret() {
					var root = getRoot();
					if (!state.currentTokenSecret) {
						showFeedback('error', 'Nenhum token disponível para copiar.');
						return;
					}

					var temp = $('<textarea style="position:absolute;left:-9999px;top:-9999px;"></textarea>');
					$('body').append(temp);
					temp.val(state.currentTokenSecret).select();

					var copied = false;
					try {
						copied = document.execCommand('copy');
					} catch (error) {
						copied = false;
					}

					temp.remove();

					if (copied) {
						root.find('.js-token-secret-copy').text('Token copiado');
						return;
					}

					showFeedback('error', 'Não foi possível copiar o token automaticamente.');
				}

				function refreshTokens() {
					var root = getRoot();
					postJson('{$tokenStatusUrl}', {}, function(data) {
						root.find('.js-token-total').text(data.total_tokens || 0);
						root.find('.js-token-active-total').text(data.active_tokens || 0);
						tokenRows = data.tokens || [];
						renderTokenRows();
					});
				}

				function refreshStatus() {
					var root = getRoot();
					postJson('{$statusUrl}', {}, function(data) {
						root.find('.js-policy-status').text(data.status_label || '-');
						root.find('.js-policy-path').text(data.path || '-');
						root.find('.js-policy-last-modified').text(formatDate(data.last_modified));
						root.find('.js-policy-total-tables').text(data.total_tables || 0);
						summaryRows = data.tables || [];
						renderSummaryRows();
					});
				}

				function createToken() {
					var root = getRoot();
					var name = $.trim(root.find('.js-token-name').val());
					var observation = $.trim(root.find('.js-token-observation').val());
					if (!name) {
						name = 'Token ' + new Date().toLocaleString();
					}

					postJson('{$tokenCreateUrl}', {
						name: name,
						observation: observation
					}, function(data) {
						root.find('.js-token-name').val('');
						root.find('.js-token-observation').val('');
						showFeedback('success', 'Token gerado com sucesso.');
						refreshTokens();
						showTokenSecret(data, 'Token gerado com sucesso.');
					});
				}

				function updateTokenStatus(tokenId, status) {
					var actionLabel = status === 'active' ? 'ativar' : 'inativar';
					if (!confirm('Deseja ' + actionLabel + ' este token?')) {
						return;
					}

					postJson('{$tokenSetStatusUrl}', {
						token_id: tokenId,
						status: status
					}, function() {
						showFeedback('success', 'Status do token atualizado.');
						refreshTokens();
					});
				}

				function renderTableSummary() {
					var root = getRoot();
					var total = state.fields.length;
					var selected = state.selectedFields.length;
					var allowed = state.currentMode === 'block' ? Math.max(0, total - selected) : (state.currentMode === 'allow' ? selected : total);
					var blocked = state.currentMode === 'block' ? selected : (state.currentMode === 'allow' ? Math.max(0, total - selected) : 0);
					root.find('.js-policy-total-fields').text(total);
					root.find('.js-policy-allowed-fields').text(allowed);
					root.find('.js-policy-blocked-fields').text(blocked);
				}

				function closeEditor() {
					var root = getRoot();
					state.currentTable = '';
					state.fields = [];
					state.selectedFields = [];
					state.fieldSearchTerm = '';
					state.fieldSelectionFilter = 'all';
					state.currentMode = 'all';
					root.find('.js-policy-table-input').val('');
					root.find('.js-policy-field-search').val('');
					root.find('.js-policy-field-filter').val('all');
					root.find('.js-editor-title').text('Configurar tabela');
					root.find('.api-policy-editor').hide();
					root.find('.api-policy-summary').show();
				}

				function updateFieldVisibility() {
					var root = getRoot();
					state.fieldSearchTerm = $.trim(root.find('.js-policy-field-search').val()).toUpperCase();
					state.fieldSelectionFilter = root.find('.js-policy-field-filter').val() || 'all';
					renderFields();
				}

				function updateFieldFilterLabels() {
					var root = getRoot();
					var filter = root.find('.js-policy-field-filter');
					var selectedLabel = 'Somente marcados';
					var unselectedLabel = 'Somente não marcados';

					if (state.currentMode === 'block') {
						selectedLabel = 'Somente bloqueados';
						unselectedLabel = 'Somente liberados';
					} else if (state.currentMode === 'allow') {
						selectedLabel = 'Somente liberados';
						unselectedLabel = 'Somente bloqueados';
					}

					filter.find('option[value="selected"]').text(selectedLabel);
					filter.find('option[value="unselected"]').text(unselectedLabel);
				}

				function getSortedFields() {
					return state.fields.slice(0).sort(function(left, right) {
						return String(left).localeCompare(String(right));
					});
				}

				function getVisibleFields() {
					return $.grep(getSortedFields(), function(field) {
						var matchesSearch = state.fieldSearchTerm === '' || String(field).toUpperCase().indexOf(state.fieldSearchTerm) !== -1;
						if (!matchesSearch) {
							return false;
						}

						var isSelected = state.selectedFields.indexOf(field) !== -1;
						if (state.fieldSelectionFilter === 'selected') {
							return isSelected;
						}
						if (state.fieldSelectionFilter === 'unselected') {
							return !isSelected;
						}

						return true;
					});
				}

				function renderFields() {
					var root = getRoot();
					var container = root.find('.js-policy-fields');
					var sortedFields = getSortedFields();
					var filteredFields = getVisibleFields();
					container.empty();

					if (sortedFields.length === 0) {
						container.html('<div class="api-policy-empty">Nenhum campo carregado. Informe uma tabela e clique em "Carregar tabela".</div>');
						renderTableSummary();
						return;
					}

					if (state.currentMode === 'all') {
						container.html('<div class="api-policy-empty">Tabela carregada com ' + sortedFields.length + ' campos reais. Se quiser bloquear ou liberar campos específicos, troque para Block ou Allow.</div>');
						renderTableSummary();
						return;
					}

					if (filteredFields.length === 0) {
						container.html('<div class="api-policy-empty">Nenhum campo encontrado para o filtro informado.</div>');
						renderTableSummary();
						return;
					}

					$.each(filteredFields, function(_, field) {
						var checked = state.selectedFields.indexOf(field) !== -1 ? ' checked="checked"' : '';
						var row = $('<label class="api-policy-field" data-field="' + field + '"><input type="checkbox" value="' + field + '"' + checked + ' /> ' + field + '</label>');
						row.find('input').bind('change', function() {
							var value = $(this).val();
							if ($(this).is(':checked')) {
								if (state.selectedFields.indexOf(value) === -1) {
									state.selectedFields.push(value);
								}
							} else {
								state.selectedFields = $.grep(state.selectedFields, function(item) { return item !== value; });
							}
							renderTableSummary();
						});
						container.append(row);
					});
					renderTableSummary();
				}

				function applyMode(mode) {
					var root = getRoot();
					state.currentMode = mode;
					root.find('input[name="{$containerId}_mode"][value="' + mode + '"]').prop('checked', true);
					var hint = 'Digite uma tabela e clique em "Carregar tabela" para buscar o schema real e começar a configuração.';
					if (mode === 'block') {
						hint = 'Modo Block: todos os campos reais da tabela são exibidos abaixo, e os campos marcados ficarão bloqueados.';
					} else if (mode === 'allow') {
						hint = 'Modo Allow: todos os campos reais da tabela são exibidos abaixo, e apenas os campos marcados ficarão liberados.';
					} else if (mode === 'all') {
						hint = 'Modo Tabela inteira: a tabela será liberada inteira. Se quiser bloquear ou liberar campos específicos, troque para Block ou Allow.';
					}
					root.find('.js-policy-mode-hint').text(hint);
					updateFieldFilterLabels();
					renderFields();
				}

				function loadTable(table) {
					postJson('{$loadTableUrl}', { table: table }, function(data) {
						var root = getRoot();
						state.currentTable = data.table || table;
						state.fields = data.fields || [];
						state.selectedFields = data.selected_fields || [];
						state.fieldSearchTerm = '';
						state.fieldSelectionFilter = 'all';
						root.find('.js-policy-table-input').val(state.currentTable);
						root.find('.js-editor-title').text('Configurar tabela ' + state.currentTable);
						root.find('.api-policy-summary').hide();
						root.find('.api-policy-editor').show();
						root.find('.js-policy-field-search').val('');
						root.find('.js-policy-field-filter').val('all');
						var nextMode = data.mode || 'all';
						if (!data.has_rule && nextMode === 'all') {
							nextMode = 'allow';
						}
						applyMode(nextMode);
						showFeedback('success', 'Tabela ' + state.currentTable + ' carregada com ' + state.fields.length + ' campos.');
					});
				}

				function collectPayload() {
					return {
						table: state.currentTable,
						mode: state.currentMode,
						fields: state.selectedFields
					};
				}

				function previewTable() {
					if (!state.currentTable) {
						showFeedback('error', 'Informe uma tabela antes de gerar o preview.');
						return;
					}

					postJson('{$previewUrl}', collectPayload(), function(data) {
						var root = getRoot();
						root.find('.js-policy-preview-content').text(data.preview || '');
						root.find('.js-policy-preview-modal').dialog({
							modal: true,
							width: 760,
							height: 520
						});
					});
				}

				function saveTable() {
					if (!state.currentTable) {
						showFeedback('error', 'Informe uma tabela antes de salvar.');
						return;
					}

					postJson('{$saveUrl}', collectPayload(), function() {
						var root = getRoot();
						showFeedback('success', 'Policy salva com sucesso.');
						root.find('.js-policy-filter-mode').val('all');
						closeEditor();
						refreshStatus();
					});
				}

				function removeTable(table) {
					var tableName = table || state.currentTable;
					if (!tableName) {
						showFeedback('error', 'Selecione uma tabela para remover.');
						return;
					}

					if (!confirm('Remover a configuração da tabela ' + tableName + '?')) {
						return;
					}

					postJson('{$removeUrl}', { table: tableName }, function() {
						showFeedback('success', 'Tabela removida da policy.');
						if (state.currentTable === tableName) {
							closeEditor();
						}
						refreshStatus();
					});
				}

				function bindEvents() {
					var root = getRoot();
					if (root.data('api-policy-bound')) {
						return;
					}

					root.data('api-policy-bound', true);
					root.find('.js-api-rest-subtab').unbind('click').bind('click', function() {
						switchSection($(this).data('section'));
					});
					root.find('.js-api-rest-unlock').unbind('click').bind('click', unlockRestApi);
					root.find('.js-api-rest-open-docs').unbind('click').bind('click', openDocs);
					root.find('.js-api-rest-password').unbind('focus').bind('focus', function() {
						$(this).removeAttr('readonly');
					});
					root.find('.js-api-rest-password').unbind('keyup').bind('keyup', function(event) {
						if ((event.which || event.keyCode) === 13) {
							unlockRestApi();
						}
					});
					root.find('.js-policy-reload').unbind('click').bind('click', refreshStatus);
					root.find('.js-policy-load-table').unbind('click').bind('click', function() {
						var table = $.trim(root.find('.js-policy-table-input').val());
						if (!table) {
							showFeedback('error', 'Informe uma tabela antes de carregar.');
							return;
						}
						loadTable(table);
					});
					root.find('.js-policy-filter-mode').unbind('change').bind('change', renderSummaryRows);
					root.find('.js-policy-field-search').unbind('keyup').bind('keyup', updateFieldVisibility);
					root.find('.js-policy-field-search').unbind('change').bind('change', updateFieldVisibility);
					root.find('.js-policy-field-filter').unbind('change').bind('change', updateFieldVisibility);
					root.find('input[name="{$containerId}_mode"]').unbind('change').bind('change', function() {
						applyMode($(this).val());
					});
					root.find('.js-policy-select-all').unbind('click').bind('click', function() {
						if (state.currentMode === 'all') {
							showFeedback('error', 'Troque para Block ou Allow para selecionar campos.');
							return;
						}
						var visibleFields = getVisibleFields();
						if (visibleFields.length === 0) {
							showFeedback('error', 'Nenhum campo visível para selecionar com o filtro atual.');
							return;
						}
						$.each(visibleFields, function(_, field) {
							if (state.selectedFields.indexOf(field) === -1) {
								state.selectedFields.push(field);
							}
						});
						renderFields();
					});
					root.find('.js-policy-clear-all').unbind('click').bind('click', function() {
						if (state.currentMode === 'all') {
							showFeedback('error', 'Troque para Block ou Allow para limpar a seleção de campos.');
							return;
						}
						var visibleFields = getVisibleFields();
						if (visibleFields.length === 0) {
							showFeedback('error', 'Nenhum campo visível para limpar com o filtro atual.');
							return;
						}
						state.selectedFields = $.grep(state.selectedFields, function(field) {
							return visibleFields.indexOf(field) === -1;
						});
						renderFields();
					});
					root.find('.js-policy-preview').unbind('click').bind('click', previewTable);
					root.find('.js-policy-save').unbind('click').bind('click', saveTable);
					root.find('.js-policy-close-editor').unbind('click').bind('click', function() {
						clearFeedback();
						closeEditor();
					});
					root.find('.js-policy-remove-current').unbind('click').bind('click', function() {
						removeTable();
					});
					root.find('.js-token-reload').unbind('click').bind('click', refreshTokens);
					root.find('.js-token-create').unbind('click').bind('click', createToken);
					root.find('.js-token-secret-copy').unbind('click').bind('click', copyTokenSecret);
					root.find('.js-token-secret-close').unbind('click').bind('click', closeTokenSecret);
				}

				function initialize(attempt) {
					var root = getRoot();
					if (root.length === 0) {
						if (attempt < 20) {
							window.setTimeout(function() { initialize(attempt + 1); }, 100);
						}
						return;
					}

					bindEvents();
					refreshAccessStatus();
				}

				window.setTimeout(function() {
					initialize(0);
				}, 50);
			})(jQuery);
		}
		</script></div>
HTML;
    }
}
