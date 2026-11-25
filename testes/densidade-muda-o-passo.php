<?php
// O passo que o desktop abre: 12 para quem não escolheu, 13 e 14 quando já estão gravados.
if (PHP_SAPI !== 'cli') {
    exit(1);
}

require_once dirname(__DIR__) . '/src/fx/passo.php';

function falhar($mensagem)
{
    fwrite(STDERR, "FALHOU: {$mensagem}\n");
    exit(1);
}

function exigir($ok, $mensagem)
{
    if (!$ok) {
        falhar($mensagem);
    }
}

exigir(hardness\fxPasso(null) === 12, 'sem valor abre no passo 12');
exigir(hardness\fxPasso('') === 12, 'vazio abre no passo 12');
exigir(hardness\fxPasso(0) === 12, 'zero abre no passo 12');
exigir(hardness\fxPasso('12') === 12, 'Pequeno gravado abre em 12');
exigir(hardness\fxPasso('15') === 12, 'valor fora da escala abre em 12');
exigir(hardness\fxPasso('13') === 13, 'Médio gravado continua em 13');
exigir(hardness\fxPasso(13) === 13, '13 numérico continua em 13');
exigir(hardness\fxPasso('14') === 14, 'Grande gravado continua em 14');
exigir(hardness\fxPasso(14) === 14, '14 numérico continua em 14');

exigir(hardness\fxPassoDoAcesso(array()) === 12, 'sem usuário abre no passo 12');
exigir(
    hardness\fxPassoDoAcesso(array('C007' => array())) === 12,
    'usuário sem o campo abre no passo 12'
);
exigir(
    hardness\fxPassoDoAcesso(array('C007' => array('C007_Tamanho_Fonte_Sistema' => null))) === 12,
    'campo nulo abre no passo 12'
);
exigir(
    hardness\fxPassoDoAcesso(array('C007' => array('C007_Tamanho_Fonte_Sistema' => '13'))) === 13,
    'o acesso em 13 continua em 13'
);
exigir(
    hardness\fxPassoDoAcesso(array('c007' => array('C007_Tamanho_Fonte_Sistema' => '14'))) === 14,
    'o acesso em 14 continua em 14'
);

hardness\fxGarantirPadraoDoPasso();

echo "O passo abre em 12, e 13 ou 14 gravados continuam.\n";
