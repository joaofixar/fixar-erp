<?php
declare(strict_types=1);
$nfOrigins=[];
foreach ((array) $docs as $document) {
    $noteId=(int) ($document['GNRE002_T007_Id'] ?? 0);
    if ($noteId>0 && !isset($nfOrigins[$noteId])) $nfOrigins[$noteId]=$document;
}
$formatDate=static function ($value): string {
    $value=trim((string) $value);
    return preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $value, $match) ? $match[3].'/'.$match[2].'/'.$match[1] : '—';
};
$formatAmount=static function ($value): string {
    return is_numeric($value) ? 'R$ '.number_format((float) $value, 2, ',', '.') : '—';
};
?>
<?php if ($nfOrigins): ?><style>.nf-origin-card{padding:16px;border:1px solid #d6e1f5;border-radius:10px;background:#fbfdff}.nf-origin-card h3{margin:0 0 14px;color:#102e5c;font-size:16px}.nf-origin-card .guide-dl{margin:0}.nf-origin-tax-groups{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:16px}.nf-origin-tax-groups section{padding:12px;border:1px solid #d9e5f7;border-radius:9px;background:#f6f9fe}.nf-origin-tax-groups h4{margin:0 0 10px;color:#46628f;font-size:12px;text-transform:uppercase;letter-spacing:.03em}.nf-origin-tax-groups .nf-origin-taxes{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin:0}.nf-origin-tax-groups .nf-origin-taxes div{padding:9px 10px;border-radius:7px;background:#fff}.nf-origin-tax-groups small,.nf-origin-tax-groups strong{display:block}.nf-origin-tax-groups small{color:#506b95;font-weight:700;font-size:12px}.nf-origin-tax-groups strong{margin-top:3px;color:#102e5c;font-size:15px}@media(max-width:620px){.nf-origin-tax-groups{grid-template-columns:1fr}}</style><section class="gnre-panel"><h2>NF de origem</h2><div class="nf-origin-list"><?php foreach ($nfOrigins as $document): ?><article class="nf-origin-card"><h3>NF <?=gnre_h((string) ($document['NF_Numero'] ?: '—'))?><?=trim((string) ($document['NF_Serie'] ?? ''))!=='' ? ' / Série '.gnre_h((string) $document['NF_Serie']) : ''?></h3><dl class="guide-dl"><dt>Emissão</dt><dd><?=gnre_h($formatDate($document['NF_Data_Emissao'] ?? ''))?></dd><dt>UF do destinatário</dt><dd><?=gnre_h((string) ($document['NF_UF_Destinatario'] ?: '—'))?></dd><dt>Chave de acesso</dt><dd><?=gnre_h((string) ($document['NF_Chave_Acesso'] ?: $document['GNRE002_Identificacao']))?></dd></dl><div class="nf-origin-tax-groups"><section><h4>Substituição tributária</h4><div class="nf-origin-taxes"><div><small>ICMS-ST</small><strong><?=gnre_h($formatAmount($document['NF_Valor_ST'] ?? null))?></strong></div><div><small>FCP-ST</small><strong><?=gnre_h($formatAmount($document['NF_Valor_FCP_ST'] ?? null))?></strong></div></div></section><section><h4>DIFAL e partilha</h4><div class="nf-origin-taxes"><div><small>DIFAL</small><strong><?=gnre_h($formatAmount($document['NF_Valor_DIFAL'] ?? null))?></strong></div><div><small>FCP partilha</small><strong><?=gnre_h($formatAmount($document['NF_Valor_FCP_Partilha'] ?? null))?></strong></div></div></section></div></article><?php endforeach; ?></div><p class="optional-mark">Dados consultados da NF vinculada à guia.</p></section><?php endif; ?>
