<?php
namespace hardness;

function etc018Opcoes($array) {
    global $g;
    $tabela = $g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp'] ? 'D183' : 'C039';
    $retorno = <<<EOT
        <a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Visualizar Log', '/etc/etc018/content/etc018ContentLog/', '&tabela={$tabela}&acaoId=' + encodeURIComponent('{$array['todosCampos'][$tabela.'_Id']}'), [950,650]); return false;">Visualizar Log</a>
EOT;
    if ($tabela == 'D183') {
        $retorno .= <<<EOT
        &nbsp;|&nbsp;<a href="#" onclick="crontabExecutar('{$array['todosCampos'][$tabela.'_Id']}');">Executar Manualmente</a>
EOT;

    }
    return $retorno;
}

function etc018NomeApp($array) {
    global $g;
    $app = Apps::getApp($array['campoAtual']);
    return $app['nome'] ? $app['nome'] : 'IDE';
}
