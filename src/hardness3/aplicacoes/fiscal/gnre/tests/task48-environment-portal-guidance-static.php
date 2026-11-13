<?php
declare(strict_types=1);

function t48(bool $condition, string $message): void { if (!$condition) throw new RuntimeException('Task 48: '.$message); }

$root=dirname(__DIR__);
$panel=(string)file_get_contents($root.'/public/task38-environment-panel.php');
$layout=(string)file_get_contents($root.'/public/task10-layout.css');
$links=[
    'Portal GNRE — Produção'=>'https://www.gnre.pe.gov.br:444/gnre/portal/automacao.jsp',
    'Portal GNRE — Homologação'=>'https://www.testegnre.pe.gov.br:444/gnre/portal/automacao.jsp',
];
foreach($links as $label=>$url) t48(strpos($panel,$label)!==false && strpos($panel,'href="'.$url.'"')!==false,'link exato ausente: '.$label.'.');
t48(substr_count($panel,'target="_blank"')===2,'os dois links devem abrir em nova aba.');
t48(substr_count($panel,'rel="noopener noreferrer"')===2,'os dois links devem proteger a nova aba.');
t48(strpos($panel,'Automação via Web Service')!==false && strpos($panel,'Esta configuração apenas define o ambiente das novas guias.')!==false,'a tela deve se apresentar como orientação de ambiente.');
t48(stripos($panel,'NF-e')===false,'o painel não pode relacionar GNRE à NF-e.');
t48(strpos($panel,'certificado digital e-CNPJ válido')!==false,'a orientação deve exigir certificado digital e-CNPJ válido.');
t48(strpos($panel,'solicite no Portal GNRE a habilitação para utilização do Web Service')!==false,'a orientação deve informar a habilitação direta no Portal GNRE.');
t48(strpos($panel,'não cadastra, armazena ou ativa certificados nesta tela')!==false,'o texto não pode sugerir cadastro, armazenamento ou ativação de certificado no ERP.');
t48(preg_match('/name="(?:certificate|certificado|password|senha|pfx|token|credential|credencial)"/i',$panel)===0,'o painel não pode receber certificado ou credencial.');
t48(strpos($panel,'INSERT ')===false && strpos($panel,'UPDATE ')===false && strpos($panel,'GnreEnvironmentPreference')===false,'o painel orientativo não pode persistir certificado ou alterar a regra de ambiente.');
t48(strpos($panel,'name="action" value="save_environment_preference"')!==false,'salvamento existente de ambiente deve permanecer.');
t48(strpos($panel,'<option value="2"<?=$environmentPreference===\'2\'?\' selected\':\'\'?>>Homologação</option>')!==false,'opção Homologação (2) deve permanecer.');
t48(strpos($panel,'<option value="1"<?=$environmentPreference===\'1\'?\' selected\':\'\'?>>Produção</option>')!==false,'opção Produção (1) deve permanecer.');
t48(preg_match('/<form method="post" class="form-grid">.*?<\/form>\s*<\/section>\s*<section class="gnre-panel gnre-environment-guidance"/s',$panel)===1,'orientação e links devem estar em bloco separado da ação Salvar.');
t48(strpos($layout,'.gnre-environment-guidance')!==false && strpos($layout,'.gnre-environment-portal-links{display:flex;flex-direction:column')!==false && strpos($layout,'width:min(250px,100%)')!==false,'orientação e links devem seguir estilo próprio do módulo, em linhas separadas e com largura uniforme.');
echo "Task 48 environment portal guidance static: OK\n";
