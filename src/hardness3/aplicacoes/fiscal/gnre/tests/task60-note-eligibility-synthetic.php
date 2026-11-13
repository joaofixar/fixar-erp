<?php
declare(strict_types=1);
require_once __DIR__.'/../src/NfGnreEligibilityReader.php';

function t60n(bool $condition, string $message): void { if (!$condition) throw new RuntimeException('Task 60 Escopo 2: '.$message); }
function fact60n(array $changes=[]): array { return array_merge(['protocol'=>'123','cancelled'=>'N','invalidated'=>'N','invalidation_protocol'=>'','denied'=>'N','st'=>true,'difal'=>false,'fcp'=>false], $changes); }

t60n(GnreNfEligibilityReader::eligible(fact60n()), 'NF autorizada com CFOP ST marcada deve ser elegível.');
t60n(GnreNfEligibilityReader::eligible(fact60n(['st'=>false,'difal'=>true])), 'DIFAL marcada deve ser elegível.');
t60n(GnreNfEligibilityReader::eligible(fact60n(['st'=>false,'fcp'=>true])), 'FCP marcada deve ser elegível.');
foreach (['cancelled'=>'S','invalidated'=>'S','invalidation_protocol'=>'999','denied'=>'S','protocol'=>'','st'=>false] as $field=>$value) {
    t60n(!GnreNfEligibilityReader::eligible(fact60n([$field=>$value])), 'Fato inválido deve bloquear: '.$field);
}
$filters=GnreNfEligibilityReader::filters(['issued_from'=>'2026-11-01','issued_to'=>'2026-11-04']);
t60n($filters['issued_from']==='2026-11-01' && $filters['issued_to']==='2026-11-04', 'Período válido deve ser aceito.');
t60n(GnreNfEligibilityReader::filters(['issued_from'=>'2026-02-31','issued_to'=>'x'])===['issued_from'=>'','issued_to'=>'','note_number'=>'','recipient_uf'=>'','recipient_name'=>''], 'Período inválido deve ser recusado.');
$filters=GnreNfEligibilityReader::filters(['issued_from'=>'2026-11-01','issued_to'=>'2026-11-04','note_number'=>'123','recipient_uf'=>'sp','recipient_name'=>'Cliente teste']);
t60n($filters['note_number']==='123' && $filters['recipient_uf']==='SP' && $filters['recipient_name']==='Cliente teste', 'Filtros por NF, UF e cliente devem ser normalizados.');
$source=(string) file_get_contents(__DIR__.'/../src/NfGnreEligibilityReader.php');
foreach (['n.T007_C004_Id=?', "IFNULL(n.T007_Numero_Protocolo_Nfe, '') <> ''", "IFNULL(n.T007_Flag_Cancelada, 'N') <> 'S'", "IFNULL(n.T007_Flag_Inutilizado, 'N') <> 'S'", "IFNULL(n.T007_Protocolo_Inutilizacao, '') = ''", 'n.T007_Numero_Nota_Fiscal = ?', 'n.T007_Cliente_Estado', 'n.T007_Cliente_Razao LIKE', 'D006_Flag_Gera_GNRE_ST', 'D006_Flag_Gera_GNRE_DIFAL', 'D006_Flag_Gera_GNRE_FCP', 'n.T007_Valor_ICMS_Substituicao_Retencao', 'n.T007_Valor_Total_Partilha_ICMS_Destino', 'n.T007_Valor_Total_FCP_Partilha'] as $needle) t60n(strpos($source,$needle)!==false, 'Consulta deve conter o contrato persistido: '.$needle);
foreach (['INSERT INTO','UPDATE ','DELETE '] as $forbidden) t60n(strpos($source,$forbidden)===false, 'Adaptador de leitura não pode conter: '.$forbidden);
t60n(strpos($source,'n.T007_Valor_FCP_ST')!==false,'FCP-ST deve vir do total autorizado persistido no cabeçalho da NF.');t60n(strpos($source,'T008_Valor_FCP_ST')===false&&strpos($source,'T007_Valor_FCP_ST_Calculado')===false,'Leitor não pode calcular ou expor FCP-ST monetário a partir de T008.');
$index=(string) file_get_contents(__DIR__.'/../public/index.php');
$view=(string) file_get_contents(__DIR__.'/../public/note-source.php');
t60n(strpos($index, '$nav===\'source_notes\'')!==false && strpos($index, "(int)\$context['company_id']")!==false, 'Rota deve carregar a consulta somente na empresa GNRE ativa.');
t60n(strpos($index, "new DateTimeImmutable('today', new DateTimeZone('America/Sao_Paulo'))")!==false && strpos($index, "'issued_from'=>\$today, 'issued_to'=>\$today")!==false, 'Tela deve iniciar o período na data atual.');
t60n(strpos($view, 'name="issued_from" required')!==false && strpos($view, 'name="issued_to" required')!==false, 'Tela deve exigir período de emissão.');
t60n(strpos($view, '<th>DIFAL (R$)</th><th>FCP partilha (R$)</th><th>ST (R$)</th><th>FCP-ST (R$)</th>')!==false, 'Tela deve agrupar DIFAL/FCP-partilha antes de ST/FCP-ST.');
t60n(strpos($view, '<th>Destinatário</th><th>UF</th>')!==false && strpos($view, 'mb_strimwidth($value, 0, 36')!==false, 'Tela deve separar UF e limitar a apresentação do cliente.');
t60n(strpos($view, 'name="note_number"')!==false && strpos($view, 'name="recipient_uf"')!==false && strpos($view, 'name="recipient_name"')!==false, 'Tela deve oferecer filtros por NF, UF e cliente.');
t60n(strpos($view, 'toggle-all-notes')!==false && strpos($view, 'note-selection')!==false && strpos($view, 'task60_prepare_batch')!==false, 'Escopo 3 deve permitir seleção e preparação em lote.');
t60n(strpos($view, '<th>UF favorecida</th>')!==false && strpos($view, "\$entry['favored_uf']")!==false && strpos((string) file_get_contents(__DIR__.'/../src/NfGnreDraftCreator.php'), "'favored_uf'=>strtoupper(trim((string)\$note['T007_Cliente_Estado']))")!==false, 'Conferência deve mostrar a UF favorecida que será gravada na guia.');
t60n(strpos($view, '<th>Indicadores</th>')===false && strpos($view, 'implode(')===false, 'Tela não pode concatenar indicadores.');
foreach (['T007_Valor_ICMS_Substituicao_Retencao', 'T007_Valor_Total_Partilha_ICMS_Destino', 'T007_Valor_Total_FCP_Partilha', "number_format((float) \$value, 2, ',', '.')", "'—'"] as $needle) t60n(strpos($view, $needle)!==false, 'Tela deve mostrar valores separados em formato brasileiro: '.$needle);
t60n(strpos($view,"\$note['T007_Valor_FCP_ST']")!==false&&strpos($view,'T007_Valor_FCP_ST_Calculado')===false,'Grade deve exibir FCP-ST do total T007 autorizado.');
t60n(strpos($view, "'R$ '.number_format")===false, 'Símbolo monetário deve ficar somente no cabeçalho da coluna.');
t60n(strpos($view, "taxValue((bool) \$note['indicator_st']")!==false && strpos($view, "taxValue((bool) \$note['indicator_difal']")!==false && strpos($view, "taxValue((bool) \$note['indicator_fcp']")!==false, 'Grid deve condicionar cada valor ao respectivo flag da CFOP.');
t60n(strpos($view, 'Selecione NFs elegíveis')!==false && strpos($view, 'Revise os demais dados exigidos pela configuração oficial antes de transmitir.')!==false, 'Tela deve preservar a conferência fiscal antes da transmissão.');
foreach (['GNRE001', 'GNRE002', 'Portal', 'XML'] as $forbidden) t60n(strpos($view,$forbidden)===false, 'Tela de busca não pode antecipar operação fora do escopo: '.$forbidden);
t60n(strpos($view, 'method="post"')!==false && strpos($view, 'name="csrf"')!==false && strpos($view, 'task60_prepare_batch')!==false && strpos($view, 'task60_create_batch')!==false, 'Escopo 3 deve ter lote protegido por POST e CSRF.');
echo "Task 60 note eligibility synthetic: OK\n";
