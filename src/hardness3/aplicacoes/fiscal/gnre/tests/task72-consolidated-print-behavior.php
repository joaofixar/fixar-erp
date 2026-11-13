<?php
declare(strict_types=1);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
define('GNRE_ROOT',dirname(__DIR__,4));
require_once __DIR__.'/../src/AuthorizedGuidesPrint.php';
function task72_assert(bool $value,string $message):void {if(!$value)throw new RuntimeException($message);}
$root=dirname(__DIR__);$ui=(string)file_get_contents($root.'/public/task63-batch.php');$js=(string)file_get_contents($root.'/public/task63-batch.js');$individual=(string)file_get_contents($root.'/public/task34-transmission-modal.js');$index=(string)file_get_contents($root.'/public/index.php');$worker=(string)file_get_contents($root.'/src/QueueWorker.php');
task72_assert(strpos($ui,'Selecione uma ou mais guias para validar e transmitir. Cada guia será processada individualmente, com seu próprio recibo e resultado.')!==false,'texto inicial da Fila deve explicar o processamento individual');
task72_assert(substr_count($ui,'data-task63-start')===1&&strpos($ui,'task63-queue-actions')!==false,'Fila deve ter uma única linha de ações e um único botão de revisão');
task72_assert(strpos($ui,'task63-select-all')!==false&&strpos($ui,'task63-start')!==false,'seleção e ação principal devem ficar na linha de ações');
task72_assert(strpos($ui,'task63-primary')!==false&&strpos($ui,'task63-secondary')!==false,'confirmação deve ter ação principal verde e retorno contornado');
task72_assert(strpos($ui,'Cada guia terá validação, recibo e resultado próprios. Você poderá acompanhar o resultado ao final do processamento.')!==false,'modal deve explicar o processamento individual');
task72_assert(strpos($js,"'Serão transmitidas '+count")!==false&&strpos($js,"', uma por vez, no ambiente '")!==false,'modal deve informar quantidade, processamento individual e ambiente');
task72_assert(strpos($js,"storedPdfs.length===1?'Imprimir guia autorizada':'Imprimir guias autorizadas'")!==false,'uma ou várias guias persistidas devem ter um único botão consolidado');
task72_assert(strpos($js,"groups[0].items.filter(function(x){return Number(x.pdfId)>0;})")!==false,'autorizada sem PDF persistido não pode entrar na impressão');
task72_assert(strpos($js,'print_authorized_guides')!==false&&strpos($index,"action'] ?? '') === 'print_authorized_guides'")!==false,'botão deve abrir somente a rota consolidada');
$routeStart=strpos($index,"action'] ?? '') === 'print_authorized_guides'");$routeEnd=strpos($index,'/* Troca somente',$routeStart);$printRoute=substr($index,$routeStart,$routeEnd-$routeStart);
task72_assert(strpos($index,'GnreAuthorizedGuidesPrint')!==false&&strpos($printRoute,'obtain(')===false&&strpos($printRoute,'ResultClient')===false,'rota consolidada não pode consultar ou obter PDF no Portal');
task72_assert(strpos($js,'item.environment===')===false&&strpos($js,'item.next=nextTime(state.queue&&state.queue.next)')!==false&&strpos($js,'return time<=Date.now()?Date.now()+2000:time;')!==false,'Homologação e Produção devem respeitar o próximo horário persistido; dois segundos só após ele vencer');
task72_assert(strpos($js,'state.queue&&state.queue.next')!==false&&strpos($js,'waitForPersistedTime')!==false&&strpos($js,'Math.min.apply(null,pending.map(function(x){return x.next;}))')!==false,'pendência deve voltar ao menor horário persistido e retomar o ciclo visível');
task72_assert(strpos($worker,'GnrePendingResultPolicy::nextDelaySeconds')!==false,'fluxo individual deve preservar o reagendamento pendente persistido');
task72_assert(strpos($individual,'state.queue.next')!==false&&strpos($individual,'Date.now()+2000')===false,'modal individual deve continuar seguindo somente o horário persistido');
task72_assert(strpos($js,'task63-summary-close')!==false&&strpos($ui,'task63-summary-close')!==false,'resumo deve oferecer Fechar como botão vermelho padronizado');
$printer=new GnreAuthorizedGuidesPrint(static function(): ?array {return null;});
try {$printer->render(1,[['guide_id'=>98,'document_id'=>7]]);throw new RuntimeException('falha de leitura não interrompeu a impressão');}
catch(RuntimeException $e){task72_assert($e->getMessage()==='Não foi possível preparar a impressão da guia #98.','falha deve identificar a guia e não gerar impressão parcial');}
require_once GNRE_ROOT.'/bibliotecas/PDFMerger/fpdf/fpdf.php';
$source=new FPDF();$source->AddPage();$source->SetFont('Arial','',10);$source->Cell(30,8,'GNRE');$sourcePdf=$source->Output('source.pdf','S');
$merged=(new GnreAuthorizedGuidesPrint(static function(int $companyId,int $guideId,int $documentId) use($sourcePdf): array {return ['GNRE008_Conteudo'=>$sourcePdf];}))->render(1,[['guide_id'=>98,'document_id'=>7]]);
task72_assert(substr($merged,0,5)==='%PDF-','uma guia com PDF persistido deve gerar impressão consolidada');
$merged=(new GnreAuthorizedGuidesPrint(static function(int $companyId,int $guideId,int $documentId) use($sourcePdf): array {return ['GNRE008_Conteudo'=>$sourcePdf];}))->render(1,[['guide_id'=>98,'document_id'=>7],['guide_id'=>99,'document_id'=>8]]);
task72_assert(substr($merged,0,5)==='%PDF-','múltiplas guias com PDFs persistidos devem gerar uma única impressão consolidada');
echo "OK Task 72 consolidated print behavioral checks\n";
