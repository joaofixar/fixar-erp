<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/EnvironmentResolver.php';
require_once __DIR__ . '/../src/GuideDraftController.php';

function t42(bool $ok, string $message): void { if (!$ok) throw new RuntimeException('Task 42: '.$message); }
function t42rejects(callable $operation, string $message): void { try { $operation(); } catch (RuntimeException $e) { return; } throw new RuntimeException('Task 42: '.$message); }

$root=dirname(__DIR__);
$source=[];
foreach (['EnvironmentResolver.php','GuideDraftController.php','ConfigUfSnapshot.php','ConfigUfClient.php','ReceptionClient.php','ResultClient.php','QueueWorker.php','OfficialPdfService.php'] as $file) $source[$file]=(string)file_get_contents($root.'/src/'.$file);
$index=(string)file_get_contents($root.'/public/index.php');
$panel=(string)file_get_contents($root.'/public/task38-environment-panel.php');
$legacy=(string)file_get_contents($root.'/src/GuidedDraftController.php');
$task15=(string)file_get_contents($root.'/public/task15-consultation.php');

foreach (['1'=>'www.gnre.pe.gov.br','2'=>'www.testegnre.pe.gov.br'] as $environment=>$host) foreach (['config_uf','reception','result'] as $service) {
    $resolved=GnreEnvironmentResolver::service((string)$environment,$service);
    t42(parse_url($resolved['endpoint'],PHP_URL_HOST)===$host,"$service deve usar somente o endpoint allowlisted de $environment.");
}
t42(GnreEnvironmentResolver::assertOperationalAllowed('1')==='1','Produção não pode ser bloqueada somente pelo ambiente 1.');
t42(GnreEnvironmentResolver::assertOperationalAllowed('2')==='2','Homologação deve permanecer operacional.');
t42(GnreEnvironmentResolver::assertOperationalAllowed('1')==='1','Produção deve permanecer operacional na criação de rascunho.');
t42rejects(static function():void { GnreEnvironmentResolver::service('1','https://externo.invalid'); },'endpoint livre não pode ser resolvido.');

t42(strpos($source['GuideDraftController.php'],'GnreEnvironmentPreference())->read')!==false,'GNRE009 deve ser lida ao iniciar a nova guia.');
t42(strpos($source['GuideDraftController.php'],'availableForUf($db,$context,$uf,$environment)')!==false,'busca local deve restringir empresa, UF e ambiente pretendido.');
t42(strpos($source['GuideDraftController.php'],"GNRE007_Ambiente']??'')!==\$environment")!==false,'snapshot divergente deve bloquear antes da criação da guia.');
t42(strpos($source['GuideDraftController.php'],'INSERT INTO GNRE001')>strpos($source['GuideDraftController.php'],'Snapshot oficial inválido para a UF e ambiente selecionados.'),'guia só pode ser criada depois da validação do snapshot.');
t42(strpos($source['ConfigUfSnapshot.php'],'$environment !== $expectedEnvironment')!==false,'resposta oficial divergente deve ser recusada antes de persistir snapshot.');
t42(strpos($source['ConfigUfSnapshot.php'],'GNRE007_Ambiente=? AND GNRE007_UF=?')!==false,'snapshot deve continuar isolado por ambiente e UF.');
t42(strpos($source['ConfigUfSnapshot.php'],"GNRE007_Ambiente'] !== \$environment")===false,'guia existente não pode ser reinterpretada pela preferência corrente.');

/* Nenhum helper ou rota legada pode cair no default de Homologação. */
foreach (['ConfigUfClient.php','ReceptionClient.php','ResultClient.php','ConfigUfSnapshot.php'] as $file) t42(strpos($source[$file],'string $environment =')===false,'nenhum cliente ou seleção de snapshot pode ter ambiente padrão: '.$file.'.');
t42(strpos($source['ConfigUfSnapshot.php'],'string $expectedEnvironment =')===false,'validação de snapshot não pode assumir Homologação.');
t42(strpos($index,'function gnre_rules(array $context, ?string $uf, int $snapshotId, string $environment)')!==false,'gnre_rules exige ambiente explícito.');
t42(strpos($index,'availableForUf($db,$context,(string)$uf,$environment)')!==false,'gnre_rules filtra snapshots pelo ambiente explícito.');
t42(strpos($index,'function gnre_store_rules(array $context, array $result, string $environment)')!==false,'persistência de snapshot exige ambiente explícito.');
t42(strpos($index,"GnreConfigUfClient())->consult(\$context,\$uf,null,true,\$environment)")!==false,'start_new_guide deve consultar com o ambiente explícito.');
t42(strpos($index,"GnreConfigUfClient())->consult(\$context,\$uf,\$r===''?null:\$r,true,\$environment)")!==false,'consulta administrativa deve usar GNRE009 explicitamente.');
t42(strpos($index,'A guia não possui snapshot oficial vinculado; use o fluxo atual')!==false,'guia legada sem snapshot deve bloquear sem inferir a preferência.');
t42(strpos($index,'cadastro legado não cria guias sem snapshot')!==false,'create legado deve bloquear antes de persistir guia parcial.');
t42(strpos($index,'attach_guide_config')!==false && strpos($index,'fluxo atual para selecionar uma configuração oficial com ambiente explícito')!==false,'vínculo legado deve ser bloqueado sem ambiente esperado.');
t42(strpos($legacy,'Este fluxo legado não seleciona ambiente')!==false && strpos($legacy,'GnreConfigUfClient())->consult')===false,'task15_consult_config não pode consultar sem ambiente explícito.');
t42(strpos($task15,'não possui snapshot oficial vinculado')!==false,'interface legada deve orientar sem oferecer vínculo inferido.');

t42(strpos($index,"assertOperationalAllowed((string)\$snapshot['GNRE007_Ambiente'])")!==false,'transmissão deve obter o ambiente do GNRE007 da guia.');
t42(strpos($source['QueueWorker.php'],"call_user_func(\$this->consult,(int)\$job['company_id'],(string)\$job['GNRE005_Recibo'],\$environment)")!==false,'fila deve encaminhar o ambiente do snapshot ao cliente.');
t42(strpos($source['QueueWorker.php'],'s.GNRE007_UF=g.GNRE001_UF_Favorecida')!==false,'fila deve validar o vínculo UF/snapshot.');
t42(strpos($source['OfficialPdfService.php'],"'environment'=>GnreEnvironmentResolver::assertOperationalAllowed((string)\$row['GNRE007_Ambiente'])")!==false,'PDF deve usar o ambiente do snapshot da guia.');
t42(strpos($source['ReceptionClient.php'],'$service = GnreEnvironmentResolver::service($environment, \'reception\')')!==false,'transmissão deve obter serviço pela allowlist.');
t42(strpos($source['ResultClient.php'],'$service = GnreEnvironmentResolver::service($environment, \'result\')')!==false,'consulta e PDF devem obter serviço pela allowlist.');
t42(strpos($source['ConfigUfClient.php'],'$certificate = $this->companyCertificate') < strpos($source['ConfigUfClient.php'],'$this->send('),'falha de certificado deve impedir a chamada de configuração.');
t42(strpos($source['ReceptionClient.php'],'$cert = $this->companyCertificate') < strpos($source['ReceptionClient.php'],'$this->send('),'falha de certificado deve impedir a transmissão.');
t42(strpos($source['ResultClient.php'],'$cert = $this->companyCertificate') < strpos($source['ResultClient.php'],'$this->send('),'falha de certificado deve impedir a consulta.');
t42(strpos($source['OfficialPdfService.php'],"['code']!=='0'")!==false,'somente situacaoGuia=0 pode confirmar emissão/PDF.');
t42(strpos($panel,'A seleção é apenas preparatória')===false && strpos($panel,'novas guias')!==false,'configuração deve explicar o efeito somente para novas guias.');

$draft=$source['GuideDraftController.php'];$statePos=strpos($draft,'$draftState=(array)($_SESSION[GNRE_SESSION][\'temporary\'][\'guide_draft\'][$company]??[])');$environmentPos=strpos($draft,'$environment=GnreEnvironmentResolver::assertOperationalAllowed((string)($draftState[\'environment\']??\'\'))');$rulesPos=strpos($draft,'$loaded=gnre_rules($context,$uf,$snapshotId,$environment)');$insertPos=strpos($draft,'INSERT INTO GNRE001');
t42($statePos!==false&&$environmentPos!==false&&$rulesPos!==false&&$insertPos!==false&&$statePos<$environmentPos&&$environmentPos<$rulesPos&&$rulesPos<$insertPos,'criação deve inicializar e validar ambiente antes de snapshot e INSERT.');
t42(strpos($draft,"(\$loaded['snapshot']['GNRE007_UF']??'')!==\$uf")!==false&&strpos($draft,"(\$loaded['snapshot']['GNRE007_Ambiente']??'')!==\$environment")!==false,'UF e ambiente do snapshot devem ser conferidos antes do INSERT.');
t42(strpos(substr($draft,$statePos,$insertPos-$statePos),'GnreConfigUfClient())->consult')===false,'criação de rascunho não pode chamar o Portal.');
t42(gnre_guide_draft_public_error(new RuntimeException('technical php error'),0,0,'guide_draft_create')==='Não foi possível criar o rascunho agora. Revise a configuração e tente novamente.','falha de criação deve ter mensagem pública de rascunho.');
t42(gnre_guide_draft_public_error(new RuntimeException('technical php error'),1,1,'guide_draft_save_item')==='Não foi possível gravar o item agora. Seus dados foram mantidos para correção.','falha de item deve preservar mensagem específica.');

echo "Task 42 operational environment synthetic: OK\n";
