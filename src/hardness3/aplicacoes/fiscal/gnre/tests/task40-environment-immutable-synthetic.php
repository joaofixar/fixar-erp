<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/EnvironmentResolver.php';
require_once __DIR__ . '/../src/ConfigUfSnapshot.php';

function t40(bool $ok, string $message): void { if (!$ok) throw new RuntimeException('Task 40: '.$message); }
foreach (['GuideDraftController.php','ConfigUfSnapshot.php','ConfigUfClient.php','OfficialPdfService.php','QueueWorker.php'] as $file) $source[$file]=(string)file_get_contents(__DIR__.'/../src/'.$file);
$index=(string)file_get_contents(__DIR__.'/../public/index.php');
$presentation=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
$modal=(string)file_get_contents(__DIR__.'/../public/task34-transmission-modal.js');
t40(strpos($source['GuideDraftController.php'],'GnreEnvironmentPreference') !== false,'preferência deve ser lida somente ao iniciar a nova guia.');
t40(strpos($source['GuideDraftController.php'],'GnreEnvironmentPreference())->read') !== false,'preferência deve definir o ambiente pretendido para novos snapshots.');
t40(strpos($source['GuideDraftController.php'],"'environment'=>\$environment") !== false,'ambiente pretendido deve ficar associado somente ao estado transitório da nova guia.');
t40(strpos($source['ConfigUfSnapshot.php'],'availableForUf(mysqli $db, array $context, string $uf, string $environment') !== false,'candidatos locais devem ser filtrados por ambiente.');
t40(strpos($source['ConfigUfSnapshot.php'],"GNRE007_Ambiente'] !== \$environment") === false,'snapshot não pode ser comparado implicitamente ao ambiente atual.');
t40(strpos($index,'GnreEnvironmentResolver::assertOperationalAllowed((string)$snapshot[\'GNRE007_Ambiente\'])') !== false,'transmissão deve usar o ambiente do snapshot vinculado.');
t40(strpos($source['OfficialPdfService.php'],"'environment'=>GnreEnvironmentResolver::assertOperationalAllowed") !== false,'PDF deve usar o ambiente do snapshot vinculado.');
t40(strpos($source['QueueWorker.php'],'snapshotEnvironment') !== false && strpos($source['QueueWorker.php'],"GnreResultClient())->consult((int)\$job['company_id'],(string)\$job['GNRE005_Recibo'],\$environment") !== false,'fila deve passar explicitamente o ambiente do snapshot ao cliente.');
t40(strpos($source['QueueWorker.php'],'$environment=GnreEnvironmentResolver::assertOperationalAllowed($environment)') !== false,'fila deve validar o ambiente do snapshot antes de callback ou cliente.');
t40(strpos($source['QueueWorker.php'],'s.GNRE007_UF=g.GNRE001_UF_Favorecida') !== false,'fila deve validar vínculo UF/snapshot da guia.');
t40(strpos($source['QueueWorker.php'],'$result=$this->call($job);$this->finish($job,$result);') !== false,'processamento deve atravessar o caminho protegido pelo lock.');
t40(strpos($source['QueueWorker.php'],'flock($f,LOCK_EX|LOCK_NB)') !== false && strpos($source['QueueWorker.php'],'finally { $wait=') !== false,'lock global deve ser liberado em finally.');
t40(strpos($source['QueueWorker.php'],'catch(Throwable $e) { return $this->retry($e->getMessage()); }') !== false,'falha de snapshot ou Produção deve retornar resultado técnico para finish limpar lease.');
t40(strpos($source['QueueWorker.php'],'call_user_func($this->consult,(int)$job[\'company_id\'],(string)$job[\'GNRE005_Recibo\'],$environment)') !== false,'callback injetado deve receber o ambiente imutável do snapshot.');
t40(strpos($source['QueueWorker.php'],'$this->finishOverride') !== false,'worker deve disponibilizar finalização controlada somente para teste comportamental isolado.');
t40(strpos($presentation,"use(\$guideEnvironmentLabel)") !== false && strpos($presentation,"'Transmitir em homologação'") !== false,'apresentação deve substituir o rótulo fixo pelo ambiente do snapshot.');
t40(strpos($modal,'transmissão em homologação') === false && strpos($modal,'data-gnre34-confirm-text') !== false,'modal não pode confirmar Homologação fixa.');
foreach (['1','2'] as $environment) t40(GnreEnvironmentResolver::validate($environment)===$environment,'ambiente permitido inválido.');
t40(GnreEnvironmentResolver::assertOperationalAllowed('1')==='1','Produção deve ser operacional quando vier do snapshot.');
echo "Task 40 immutable environment synthetic: OK\n";
