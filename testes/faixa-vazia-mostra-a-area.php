<?php
// A área devolve o HTML que a faixa vazia mostra: avisos filtrados e notas.
if (PHP_SAPI !== 'cli') {
    exit(1);
}

require_once dirname(__DIR__) . '/src/fx/area.php';

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

$vazio = hardness\fxHtmlArea(array(), array());
exigir($vazio === '', 'sem nota e sem aviso não há bloco');
exigir(strpos($vazio, 'data-fx-nota') === false, 'o vazio não reserva nota');

$json = <<<'JSON'
[
  {"data":"2026-03-01","titulo":"Antiga","texto":"Primeira mudança."},
  {"data":"2026-11-02","titulo":"Nova","texto":"Segunda mudança."},
  {"data":"2026-12-01","titulo":"Vídeo da Hardness","texto":"Assista","video":"notificacao_00041.php"},
  {"titulo":"Últimas atualizações","texto":"lista de vídeos","video":"1"}
]
JSON;
$notas = hardness\fxLerNotas($json);
exigir(count($notas) === 2, 'a notificação de vídeo não vira nota');
exigir($notas[0]['titulo'] === 'Nova' && $notas[1]['titulo'] === 'Antiga', 'a mais nova vem primeiro');

$html = hardness\fxHtmlArea(
    array(
        array('texto' => '<p>Aviso do diretor</p>'),
        array('texto' => '<p>Olá Ana</p>'),
    ),
    $notas
);
$aviso = strpos($html, '<div data-fx-aviso><p>Aviso do diretor</p></div>');
$segunda = strpos($html, '<div data-fx-aviso><p>Olá Ana</p></div>');
$nova = strpos($html, '<article data-fx-nota><time datetime="2026-11-02">02/11/2026</time><h2>Nova</h2><p>Segunda mudança.</p></article>');
$antiga = strpos($html, '<article data-fx-nota><time datetime="2026-03-01">01/03/2026</time><h2>Antiga</h2><p>Primeira mudança.</p></article>');
exigir($aviso !== false && $segunda !== false && $nova !== false && $antiga !== false, 'aviso e nota saem com data, título e texto');
exigir($aviso < $segunda && $segunda < $nova && $nova < $antiga, 'avisos na ordem recebida e notas da mais nova à mais antiga');
exigir(strpos($html, 'Vídeo da Hardness') === false, 'o vídeo não entra na nota');
exigir(strpos($html, '<video') === false && strpos($html, '<iframe') === false, 'a nota não tem vídeo');
exigir(strpos($html, 'Últimas atualizações') === false, 'a lista de atualizações não entra');
exigir(strpos($html, 'versão') === false && strpos($html, 'boleto') === false, 'versão e cobrança ficam fora do html');

exigir(hardness\fxTextoAviso('Olá [NOME_USUARIO]', 'Ana') === 'Olá Ana', 'o aviso troca o nome de hoje');

$sql = hardness\fxSqlAvisos(4, 7, 9);
exigir(strpos($sql, "C036_C021_Id = 4") !== false, 'o aviso filtra o perfil');
exigir(strpos($sql, "C036_C004_Id = 7") !== false, 'o aviso filtra a empresa');
exigir(strpos($sql, "C036_C007_Id = 9") !== false, 'o aviso filtra o usuário');
exigir(strpos($sql, "C032_Flag_Ativo = 'S'") !== false, 'só aviso ativo');
exigir(strpos($sql, 'C036_Data_Inicial') !== false && strpos($sql, 'C036_Data_Final') !== false, 'o aviso respeita o prazo de hoje');
exigir(strpos($sql, 'BOL001') === false && strpos($sql, 'notificacao') === false, 'a consulta não traz cobrança nem vídeo');

exigir(hardness\fxAreaTrabalho(array('usuarioAtual' => '')) === '', 'sem sessão não há área');
exigir(
    hardness\fxAreaTrabalho(array('usuarioAtual' => 3, 'perfilUsuario' => 4, 'empresaAtual' => 7)) === '',
    'sem nota publicada o arquivo não reserva bloco'
);

$framework = file_get_contents(dirname(__DIR__) . '/src/hardness3/sistema/index-framework.php');
$chamada = "verificaBoleto(\$Cnpj_Empresa_Usuaria, 'S', 63, true);";
$parceiro = "verificaBoletoParceiro('', 'S', 63, true);";
exigir(substr_count($framework, "if (false) { {$chamada} }") === 1, 'o desktop não verifica boleto');
exigir(substr_count($framework, $chamada) === 1, 'não sobra chamada de boleto');
exigir(substr_count($framework, "if (false) { {$parceiro} }") === 1, 'o desktop não verifica boleto de parceiro');
exigir(substr_count($framework, $parceiro) === 1, 'não sobra chamada de boleto de parceiro');
exigir(strpos($framework, 'fxAreaTrabalho($g)') !== false, 'a página atribui a área ao buraco de avisos');

$abas = file_get_contents(dirname(__DIR__) . '/src/hardness3/prog/etc/etc011/tab.php');
exigir(strpos($abas, 'etc011Avisos') === false, 'a aba Início do B.I. sai do cockpit');

fwrite(STDOUT, "ok\n");
