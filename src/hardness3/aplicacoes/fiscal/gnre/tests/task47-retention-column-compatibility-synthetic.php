<?php
declare(strict_types=1);

require_once __DIR__.'/../src/ResultInterpreter.php';
require_once __DIR__.'/../src/GuideRetention.php';
require_once __DIR__.'/../src/GuideCenter.php';

function t47(bool $condition,string $message):void { if(!$condition) throw new RuntimeException($message); }
function f47(array $attempts=[],array $queues=[],array $pdfs=[]):array { return GnreGuideRetention::facts($attempts,$queues,$pdfs); }

$draft=['GNRE001_Status'=>'R','GNRE001_Arquivada'=>'N'];
$rejected=['GNRE001_Status'=>'F','GNRE001_Arquivada'=>'N'];
$attempt=[['GNRE004_Id'=>1,'GNRE004_Sequencia'=>1,'GNRE004_Estado'=>'REJEITADA','GNRE004_Recibo'=>'']];

t47(GnreGuideRetention::action($draft,f47(),true)==='delete','com coluna, rascunho sem histórico pode excluir.');
t47(GnreGuideRetention::action($rejected,f47($attempt),true)==='archive','com coluna, rejeição final pode arquivar.');
t47(GnreGuideRetention::action(['GNRE001_Status'=>'R','GNRE001_Arquivada'=>'S'],f47($attempt),true)==='unarchive','com coluna, guia arquivada pode desarquivar conforme a política.');
t47(GnreGuideRetention::action(['GNRE001_Status'=>'P','GNRE001_Arquivada'=>'N'],f47(),true)==='none','com coluna, estado pendente não pode reter por exclusão ou arquivamento.');

t47(GnreGuideRetention::action($draft,f47(),false)==='delete','sem coluna, rascunho sem histórico preserva exclusão segura.');
t47(GnreGuideRetention::action($draft,f47($attempt),false)==='none','sem coluna, GNRE004 bloqueia a exclusão.');
t47(GnreGuideRetention::action($draft,f47([], [['GNRE005_GNRE004_Id'=>1,'GNRE005_Situacao'=>'PENDENTE']]),false)==='none','sem coluna, GNRE005 bloqueia a exclusão.');
t47(GnreGuideRetention::action($draft,f47([],[],[['GNRE008_Id'=>1]]),false)==='none','sem coluna, GNRE008 bloqueia a exclusão.');
t47(GnreGuideRetention::action($rejected,f47($attempt),false)==='none','sem coluna, arquivamento não é exposto como alternativa.');

function gnre_h($value):string{return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}
function gnre_url(string $path=''):string{return '/gnre/'.$path;}
function gnre_guide_url(int $id,string $tab='dados'):string{return '/gnre/?edit='.$id.'&tab='.$tab;}
require_once __DIR__.'/../public/task28-guide-center.php';
$filters=['status'=>'','uf'=>'','revenue'=>'','due_from'=>'','due_to'=>'','guide'=>0,'receipt'=>'','environment'=>'','include_archived'=>true];
$html=task28_render_guide_center($filters,[],[],[],true);
t47(strpos($html,'Incluir arquivadas')!==false,'com coluna, Central apresenta filtro de arquivadas.');
$html=task28_render_guide_center($filters,[],[],[],false);
t47(strpos($html,'Incluir arquivadas')===false,'sem coluna, Central não apresenta filtro de arquivadas.');

$center=(string)file_get_contents(__DIR__.'/../src/GuideCenter.php');
$retention=(string)file_get_contents(__DIR__.'/../src/GuideRetention.php');
$index=(string)file_get_contents(__DIR__.'/../public/index.php');
$flow=(string)file_get_contents(__DIR__.'/../public/guide-draft-flow.php');
t47(strpos($center,'array $filters, bool $archiveColumnAvailable')!==false&&strpos($center,"if (\$archiveColumnAvailable && empty(\$filters['include_archived']))")!==false,'SQL da Central só usa GNRE001_Arquivada quando a disponibilidade recebida permite.');
t47(strpos($center,'GnreGuideRetention::available')===false,'a Central não repete SHOW COLUMNS na interface.');
t47(strpos($retention,'deleteWithoutArchiveColumn')!==false&&strpos($retention,'FOR UPDATE')!==false&&strpos($retention,'mysqli_commit')!==false,'exclusão sem coluna permanece transacional, bloqueada e revalidada.');
t47(strpos($index,"O SQL manual da Task 45 ainda não foi aplicado; arquivamento está indisponível.")!==false,'POST forjado de arquivamento sem coluna recebe mensagem pública segura.');
t47(strpos($index,'GnreGuideCenter::load($listDb,(int)$context[\'company_id\'],[],$retentionAvailable)')!==false,'listagem recebe disponibilidade única e mantém isolamento por empresa.');
t47(strpos($flow,'gnre_retention_available')===false,'página da guia usa o estado já derivado, sem nova consulta de coluna.');
t47(strpos($index,'gnre_require_csrf();$guideId=')!==false,'POSTs de retenção continuam protegidos por CSRF.');
foreach (['GnreReceptionClient','GnreResultClient','GnreOfficialPdfService','generate_xml','transmit_homologation'] as $forbidden) t47(strpos($retention,$forbidden)===false,'retenção não aciona integração externa: '.$forbidden);

echo "Task 47 retention-column compatibility synthetic: OK\n";
