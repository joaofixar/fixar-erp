<section class="gnre-panel" data-gnre-environment-config>
<h2>Configuração de ambiente GNRE</h2>
<p><strong>Empresa selecionada:</strong> <?=gnre_h($context['company_name'])?></p>
<?php if(is_array($environmentPreferenceFlash)): ?><dialog class="gnre-environment-feedback" data-gnre-environment-feedback><section class="gnre-panel"><h2><?=$environmentPreferenceFlash['error']?'Não foi possível salvar':'Ambiente salvo'?></h2><p class="<?=$environmentPreferenceFlash['error']?'error':'success'?>"><?=gnre_h((string)$environmentPreferenceFlash['message'])?></p><form method="dialog" class="form-actions"><button>Entendi</button></form></section></dialog><?php endif; ?>
<p class="optional-mark">O ambiente escolhido será usado somente nas novas guias. Guias existentes mantêm sempre o ambiente do snapshot vinculado.</p>
<form method="post" class="form-grid">
<input type="hidden" name="csrf" value="<?=gnre_h($csrf)?>">
<input type="hidden" name="action" value="save_environment_preference">
<label>Ambiente<select name="environment" required><option value="2"<?=$environmentPreference==='2'?' selected':''?>>Homologação</option><option value="1"<?=$environmentPreference==='1'?' selected':''?>>Produção</option></select></label>
<div class="form-actions"><button>Salvar</button><a class="button environment-back" href="<?=gnre_h(gnre_url('?view=list'))?>">Voltar para guias</a></div>
</form>
</section>
<section class="gnre-panel gnre-environment-guidance" aria-label="Orientações sobre automação via Web Service">
<h2>Automação via Web Service</h2>
<p>Para transmitir e consultar GNRE por Web Service, a empresa deve utilizar certificado digital e-CNPJ válido. Antes do primeiro uso, solicite no Portal GNRE a habilitação para utilização do Web Service.</p>
<p>Esta configuração apenas define o ambiente das novas guias. O ERP ainda não cadastra, armazena ou ativa certificados nesta tela.</p>
<div class="gnre-environment-portal-links" aria-label="Acessos externos ao Portal GNRE"><a href="https://www.gnre.pe.gov.br:444/gnre/portal/automacao.jsp" target="_blank" rel="noopener noreferrer">Portal GNRE — Produção</a><a href="https://www.testegnre.pe.gov.br:444/gnre/portal/automacao.jsp" target="_blank" rel="noopener noreferrer">Portal GNRE — Homologação</a></div>
</section>
