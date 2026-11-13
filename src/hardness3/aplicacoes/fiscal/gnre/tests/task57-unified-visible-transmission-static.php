<?php
declare(strict_types=1);

function t57(bool $ok,string $message): void { if(!$ok) throw new RuntimeException($message); }
$page=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
$modal=(string)file_get_contents(__DIR__.'/../public/task34-transmission-modal.js');
$index=(string)file_get_contents(__DIR__.'/../public/index.php');
$center=(string)file_get_contents(__DIR__.'/../src/GuideCenter.php');
$layout=(string)file_get_contents(__DIR__.'/../public/task10-layout.css');
$ui=(string)file_get_contents(__DIR__.'/../public/gnre-ui.js');

/* Processar keeps only the local XML conference; the legacy POST handlers
 * remain in index.php and are invoked by the guided modal, never removed. */
t57(strpos($page,'XML validado com sucesso.')!==false,'sucesso local do XML não foi simplificado.');
t57(strpos($page,'Para transmitir, use o botão <strong>Transmitir GNRE</strong> no topo da página.')!==false,'XML válido não orienta o fluxo guiado.');
t57(strpos($page,'preg_replace(\'~<article class="gnre-process-step" data-gnre-transmission>.*?</article>~s\'')!==false,'bloco manual de transmissão ainda é apresentado em Processar.');
t57(strpos($page,'gnre-guided-follow-up')!==false&&strpos($page,'Acompanhamento da transmissão')!==false,'pendência não preserva orientação pública de acompanhamento.');
t57(strpos($page,'Próxima consulta permitida em ')!==false,'horário público da fila deixou de ser calculado.');
t57(strpos($page,'data-gnre34-start-label="Obter PDF"')!==false,'PDF autorizado sem arquivo não foi concentrado no fluxo guiado.');
t57(strpos($page,'data-gnre34-start-disabled')!==false,'guia autorizada com PDF ou rejeitada ainda pode expor transmissão.');
t57(strpos($modal,"host.hasAttribute('data-gnre34-start-disabled')")!==false,'modal não respeita estados finais sem transmissão.');
t57(strpos($modal,"host.getAttribute('data-gnre34-start-label')||'Transmitir GNRE'")!==false,'ação de PDF não usa o caminho guiado.');
t57(strpos($page,'$noJsGuidedFallback')!==false&&strpos($page,'<noscript><form method="post"')!==false&&strpos($page,'Confirmar transmissão')!==false,'fallback sem JavaScript não preserva validação, prévia e confirmação no cabeçalho.');
t57(strpos($index,"'gnre57_fallback'")!==false&&strpos($index,'&gnre57_confirm=1')!==false,'fallback sem JavaScript não retorna da validação para confirmação explícita.');
t57(strpos($index,"elseif(\$action==='transmit_homologation')")!==false&&strpos($index,"elseif(\$action==='consult_result')")!==false&&strpos($index,"elseif(\$action==='obtain_official_pdf')")!==false,'endpoints existentes foram removidos.');
t57(strpos($index,"if(\$action==='generate_xml')")!==false&&strpos($index,'GNRE004')!==false,'validação local deixou de ser separada da tentativa de transmissão.');
t57(strpos($center,"'action'=>'Acompanhar'")!==false&&strpos($center,"'action'=>'Consultar resultado'")===false,'Central ainda oferece consulta manual concorrente.');
t57(strpos($index,"unset(\$_SESSION[GNRE_SESSION]['temporary']['xml_result'][(string)\$context['company_id']][\$id])")===false,'XML local validado não permanece disponível para conferência.');
t57(strpos($layout,'[data-gnre-process][data-gnre-manual-view="validation"]')===false,'CSS ainda oculta o cartão de validação/XML em Processar.');
t57(strpos($page,'data-view-generated-xml')!==false&&strpos($page,'data-generated-xml-dialog')!==false&&strpos($page,'gnre-xml-actions')!==false,'Visualizar/Copiar XML não foram preparados para o modal.');
t57(strpos($ui,"xmlDialog.showModal()")!==false&&strpos($layout,'.gnre-xml-dialog')!==false,'modal do XML não possui abertura ou estilo próprio.');
echo "Task 57 unified visible transmission static checks OK\n";
