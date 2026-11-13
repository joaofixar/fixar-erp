<?php
declare(strict_types=1);
function t60d(bool $condition, string $message): void { if (!$condition) throw new RuntimeException('Task 60 Escopo 3: '.$message); }
$root=dirname(__DIR__);
$creator=(string) file_get_contents($root.'/src/NfGnreDraftCreator.php');
$schema=(string) file_get_contents(dirname($root, 3).'/scripts/atualizacaoBase.php');
$reader=(string) file_get_contents($root.'/src/NfGnreEligibilityReader.php');
$index=(string) file_get_contents($root.'/public/index.php');
$view=(string) file_get_contents($root.'/public/note-source.php');
$modal=(string) file_get_contents($root.'/public/task60-batch-modal.js');
$taskDoc=(string) file_get_contents($root.'/docs/tasks/60-inclusao-assistida-gnre-por-nf.md');
foreach (['gnre_require_csrf()', 'task60_prepare_batch', 'task60_create_batch', 'GnreNfDraftCreator::createComponent', "'R'", 'mysqli_begin_transaction', 'FOR UPDATE', 'GnreNfEligibilityReader::loadById', 'GNRE002_T007_Id', 'GNRE002_Tributo_Origem', 'mysqli_rollback', "'outcome'=>'existing'"] as $needle) t60d(strpos($creator.$index, $needle)!==false, 'Contrato de inclusão ausente: '.$needle);
t60d(strpos($schema, 'UNIQUE KEY UQ_GNRE002_NF_TRIBUTO (GNRE002_T007_Id,GNRE002_Tributo_Origem)')!==false, 'Schema distribuído deve conter a chave única NF + tributo.');
t60d(strpos($schema, "GNRE001_Codigo_Receita varchar(20) NOT NULL DEFAULT ''")!==false && strpos($schema, "GNRE001_Valor decimal(15,2) NOT NULL DEFAULT '0.00'")!==false && strpos($schema, 'GNRE001_Data_Vencimento date DEFAULT NULL')!==false, 'Schema deve suportar rascunho incompleto explicitamente.');
t60d(strpos($creator, "'receipt'=>'100099'")!==false && strpos($creator, 'GNRE003')!==false && strpos($creator, 'count($available)!==1')!==false, 'Lote deve usar somente o estado canônico de rascunho e bloquear snapshot ambíguo.');
foreach (["IFNULL(n.T007_Numero_Protocolo_Nfe, '') <> ''", "IFNULL(n.T007_Flag_Cancelada, 'N') <> 'S'", "IFNULL(n.T007_Flag_Inutilizado, 'N') <> 'S'", "IFNULL(n.T007_Protocolo_Inutilizacao, '') = ''", "IFNULL(n.T007_Flag_Denegado, 'N') <> 'S'", 'D006_Flag_Gera_GNRE_ST', 'D006_Flag_Gera_GNRE_DIFAL', 'D006_Flag_Gera_GNRE_FCP'] as $needle) t60d(strpos($reader, $needle)!==false, 'Releitura não reaplica elegibilidade: '.$needle);
foreach (['GNRE004', 'GNRE005', 'GNRE008', 'Xml', 'Portal', 'Financeiro', 'VEN012', 'VEN003'] as $forbidden) t60d(strpos($creator, $forbidden)===false, 'Criador de rascunho não pode antecipar fluxo fora do escopo: '.$forbidden);
t60d(strpos($view, 'method="post"')!==false && strpos($view, 'name="csrf"')!==false && strpos($view, 'Preparar guias selecionadas')!==false && strpos($view, 'Criar rascunhos conferidos')!==false, 'Lote deve usar POST com CSRF nas duas etapas.');
t60d(strpos($view, '<dialog class="guide-item-modal task60-batch-modal"')!==false && strpos($view, 'Conferir rascunhos')!==false && strpos($view, 'task60_cancel_batch')!==false, 'A conferência deve abrir no modal interno com fechamento seguro.');
t60d(strpos($view, 'Revise os demais dados exigidos pela configuração oficial antes de transmitir.')!==false, 'Modal deve manter o aviso fiscal explícito.');
t60d(strpos($modal, 'dialog.showModal()')!==false && strpos($modal, "dialog.addEventListener('cancel'")!==false && strpos($modal, 'event.preventDefault()')!==false, 'Modal deve abrir acima da lista e não fechar por Esc ou backdrop.');
t60d(strpos($creator, 'self::positive($amount)')!==false && strpos($creator, 'Componente sem valor final aplicável na NF.')===false, 'Componente sem valor não pode gerar linha bloqueada no lote.');
foreach (['UQ_GNRE002_T007_Id', 'Incluir guia', '## Escopo 3 — Rascunho individual'] as $obsolete) t60d(stripos($taskDoc, $obsolete)===false, 'Documentação não pode reintroduzir contrato superado: '.$obsolete);
t60d(strpos($taskDoc, 'UQ_GNRE002_NF_TRIBUTO')!==false && strpos($taskDoc, 'uma NF pode gerar guias diferentes por tributo')!==false, 'Documentação deve registrar lote por NF + tributo.');
echo "Task 60 NF draft static: OK\n";
