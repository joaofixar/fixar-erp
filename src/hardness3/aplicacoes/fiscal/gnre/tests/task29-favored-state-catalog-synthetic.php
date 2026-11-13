<?php
declare(strict_types=1);

require_once __DIR__.'/../src/FavoredStateCatalog.php';

function t29(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); }

$states=GnreFavoredStateCatalog::all();
t29($states===['AC','AL','AM','AP','BA','CE','DF','GO','MA','MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','TO'],'catálogo deve conter, em ordem alfabética, todas e somente as UFs do Portal Nacional.');
t29($states===array_values(array_unique($states)),'catálogo de UFs não pode duplicar siglas.');

$page=(string)file_get_contents(__DIR__.'/../public/index.php');
$flow=(string)file_get_contents(__DIR__.'/../public/guide-draft-flow.php');
$controller=(string)file_get_contents(__DIR__.'/../src/GuideDraftController.php');
t29(strpos($page,'$states = GnreFavoredStateCatalog::all();')!==false,'Nova guia deve obter UFs de catálogo independente.');
t29(strpos($flow,'foreach($states as $s)')!==false,'combo deve renderizar o catálogo de UFs.');
t29(strpos($flow,'GnreConfigUfClient')===false,'carregamento da tela e abertura do combo não podem chamar o Portal.');
$chooseParts=explode("if(\$action==='guide_draft_choose_uf')",$controller,2);
$consultParts=explode("if(\$action==='guide_draft_consult')",$controller,2);
t29(count($chooseParts)===2&&strpos($chooseParts[1],"if(\$action==='guide_draft_consult')")!==false&&strpos(strstr($chooseParts[1],"if(\$action==='guide_draft_consult')",true),'GnreConfigUfSnapshot::availableForUf')!==false&&strpos(strstr($chooseParts[1],"if(\$action==='guide_draft_consult')",true),'GnreConfigUfClient')===false,'selecionar UF deve apenas verificar snapshot local, sem Portal.');
t29(count($consultParts)===2&&strpos($consultParts[1],'GnreConfigUfClient')!==false,'Portal só pode ser consultado pela ação explícita após Continuar.');
t29(strpos($controller,"if(count(\$available)===1)\$state['snapshot_id']")!==false,'um único snapshot local deve ser selecionado automaticamente.');
t29(strpos($flow,"if(!\$snapshotId&&count(\$snapshots)===1)")!==false,'uma seleção de UF já aberta deve aproveitar automaticamente seu único snapshot local.');
t29(strpos($flow,'Configurações para <?=gnre_h($uf)?> carregadas')!==false&&strpos($flow,'Buscar configurações no Portal')!==false,'configuração carregada deve ser comunicada sem expor detalhes técnicos e permitir busca explícita.');
t29(strpos($flow,'Carregar configurações da UF')!==false&&strpos($flow,'Esta ação não cria nem transmite uma guia.')!==false,'UF sem configuração deve explicar a ação necessária sem parecer erro.');
t29(strpos($controller,'$available?\'Há mais de uma configuração oficial local. Selecione a versão.\':\'\',false')!==false,'ausência de snapshot não deve gerar alerta de erro no fluxo guiado.');
echo "task29 favored-state catalog: OK\n";
