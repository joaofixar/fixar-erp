<?php
namespace hardness;

/**
 * Prepara a empresa da NF antes de declarar qualquer classe personalizável.
 * O processo da queueNF atende somente um registro de Q001 por execução.
 */
function carregarContextoQueueNF($Q001, &$notaConfirmada) {
    global $g, $confUsuario;
    static $iniciado = false;

    if ($iniciado) {
        throw new \RuntimeException('O contexto da queueNF já foi inicializado neste processo.');
    }
    $iniciado = true;

    if (class_exists('hardness\VEN003', false)
        || class_exists('hardness\VEN012', false)
        || class_exists('hardness\EST004', false)) {
        throw new \RuntimeException('Uma classe fiscal já foi carregada antes do contexto da NF.');
    }

    if (empty($Q001['Q001_Database']) || !preg_match('/^[A-Za-z0-9_]+$/', $Q001['Q001_Database'])) {
        throw new \RuntimeException('Q001_Database inválido.');
    }
    if (!ctype_digit((string) $Q001['Q001_C004_Id']) || (int) $Q001['Q001_C004_Id'] <= 0
        || !ctype_digit((string) $Q001['Q001_T007_Id']) || (int) $Q001['Q001_T007_Id'] <= 0) {
        throw new \RuntimeException('Empresa ou nota fiscal inválida em Q001.');
    }

    $C004_Id = (int) $Q001['Q001_C004_Id'];
    $T007_Id = (int) $Q001['Q001_T007_Id'];

    if (!mysqli_query("USE `{$Q001['Q001_Database']}`")) {
        throw new \RuntimeException('Erro ao selecionar o banco da NF: ' . mysql_error());
    }

    $sqlT007 = <<<SQL
        SELECT T007_Id, T007_C004_Id FROM T007 WHERE T007_Id = '{$T007_Id}'
    SQL;
    $resT007 = mysqli_query($sqlT007);
    if (!$resT007) {
        throw new \RuntimeException('Erro ao consultar T007: ' . mysql_error());
    }
    $T007 = mysql_fetch_assoc($resT007);
    if (empty($T007['T007_Id']) || (int) $T007['T007_C004_Id'] !== $C004_Id) {
        throw new \RuntimeException("T007 {$T007_Id} não pertence à empresa {$C004_Id} do Q001.");
    }
    $notaConfirmada = true;

    $pathOrigem = !empty($Q001['Q001_Path']) ? realpath($Q001['Q001_Path']) : false;
    if ($pathOrigem === false || !is_dir($pathOrigem)) {
        throw new \RuntimeException('Q001_Path não aponta para uma instalação acessível.');
    }
    $pathOrigem = rtrim($pathOrigem, '/') . '/';
    $pathRaiz = realpath(getcwd());
    if ($pathRaiz === false || !is_dir($pathRaiz)) {
        throw new \RuntimeException('A instalação executora da queueNF não está acessível.');
    }
    $pathRaiz = rtrim($pathRaiz, '/') . '/';
    $pathDados = $pathRaiz . 'dados_usuarios/' . $Q001['Q001_Database'] . '/';
    $pathDadosReal = realpath($pathDados);
    if ($pathDadosReal === false || !is_dir($pathDados) || !is_readable($pathDados)) {
        throw new \RuntimeException("Pasta de personalizações indisponível: {$pathDados}");
    }
    // Interfaces em instalações distintas podem compartilhar os dados do cliente.
    // A fila só aceita essa origem quando ambas apontam para a mesma pasta física
    // do banco da NF; pastas separadas com o mesmo nome não são equivalentes.
    if ($pathOrigem !== $pathRaiz) {
        $pathDadosOrigem = realpath($pathOrigem . 'dados_usuarios/' . $Q001['Q001_Database'] . '/');
        if ($pathDadosOrigem === false || $pathDadosOrigem !== $pathDadosReal) {
            throw new \RuntimeException("Q001_Path aponta para outra instalação sem compartilhar os dados do banco {$Q001['Q001_Database']}.");
        }
    }
    if (empty($Q001['Q001_Url'])) {
        throw new \RuntimeException('Q001_Url não foi informado.');
    }

    $g['empresaAtual'] = $C004_Id;
    $g['usuarioAtual'] = !empty($Q001['Q001_C007_Id']) ? $Q001['Q001_C007_Id'] : 63;
    if (!ctype_digit((string) $g['usuarioAtual']) || (int) $g['usuarioAtual'] <= 0) {
        throw new \RuntimeException('Q001_C007_Id inválido.');
    }
    $g['usuarioAtual'] = (int) $g['usuarioAtual'];
    // Classes, IDE e Apps continuam na instalação que executa a fila. Manter os
    // caminhos nessa raiz também preserva os caminhos legados derivados abaixo.
    // Q001_Path permanece com a origem para os logs e eventuais reenvios da NF.
    $g['pathRaiz'] = $confUsuario['pathRaiz'] = $pathRaiz;
    $g['pathDados'] = $pathDados;
    $g['pathDadosUsuario'] = $pathDados . 'usuarios/u_'
        . str_pad((string) $g['usuarioAtual'], 6, '0', STR_PAD_LEFT) . '/';
    $g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $g['pathDados']);
    $g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $g['pathDadosUsuario']);
    $g['urlRaiz'] = $confUsuario['urlRaiz'] = $Q001['Q001_Url'];
    $g['dbDatabase'] = $confUsuario['dbDatabase'] = $Q001['Q001_Database'];
    $g['mysqlLastId'] = false;
    $g['modoDesenvolvedor'] = false;
    $g['modoDesenvolvedorApp'] = false;
    $g['isRoot'] = $g['usuarioAtual'] === 63;

    $sqlC004 = <<<SQL
        SELECT * FROM C004
            LEFT JOIN D020 ON D020_Id = C004_D020_Id
            LEFT JOIN D018 ON D018_Id = C004_D018_Id
        WHERE C004_Id = '{$C004_Id}'
    SQL;
    $resC004 = mysqli_query($sqlC004);
    if (!$resC004) {
        throw new \RuntimeException('Erro ao consultar C004: ' . mysql_error());
    }
    $C004 = mysql_fetch_assoc($resC004);
    if (empty($C004['C004_Id'])) {
        throw new \RuntimeException("Empresa C004 {$C004_Id} não encontrada.");
    }
    $g['c004'] = $g['C004'] = $C004;

    $sqlC020 = <<<SQL
        SELECT * FROM C020 WHERE C020_C004_Id = '{$C004_Id}'
    SQL;
    $resC020 = mysqli_query($sqlC020);
    if (!$resC020) {
        throw new \RuntimeException('Erro ao consultar C020: ' . mysql_error());
    }
    $C020 = mysql_fetch_assoc($resC020);
    if (empty($C020['C020_Id'])) {
        throw new \RuntimeException("Configuração C020 da empresa {$C004_Id} não encontrada.");
    }
    $g['c020'] = $g['C020'] = $C020;

    $sqlC031 = <<<SQL
        SELECT C031_Campo, C031_Valor FROM C031 WHERE C031_C004_Id = '{$C004_Id}'
    SQL;
    $resC031 = mysqli_query($sqlC031);
    if (!$resC031) {
        throw new \RuntimeException('Erro ao consultar C031: ' . mysql_error());
    }
    $g['c031'] = $g['C031'] = array();
    while ($C031 = mysql_fetch_assoc($resC031)) {
        $g['c031'][$C031['C031_Campo']] = $C031['C031_Valor'];
        $g['C031'][$C031['C031_Campo']] = $C031['C031_Valor'];
    }

    $sqlC007 = <<<SQL
        SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}' AND C007_Ativo = 'S'
    SQL;
    $resC007 = mysqli_query($sqlC007);
    if (!$resC007) {
        throw new \RuntimeException('Erro ao consultar C007: ' . mysql_error());
    }
    $C007 = mysql_fetch_assoc($resC007);
    if (empty($C007['C007_Id'])) {
        throw new \RuntimeException("Usuário C007 {$g['usuarioAtual']} não encontrado ou inativo.");
    }
    $g['c007'] = $g['C007'] = $C007;

    $sqlC019 = <<<SQL
        SELECT C019_Id, C019_C004_Id, C019_C021_Id FROM C019 WHERE C019_C007_Id = '{$g['usuarioAtual']}'
    SQL;
    $resC019 = mysqli_query($sqlC019);
    if (!$resC019) {
        throw new \RuntimeException('Erro ao consultar C019: ' . mysql_error());
    }
    $g['c019'] = $g['C019'] = array();
    while ($C019 = mysql_fetch_assoc($resC019)) {
        $g['c019'][$C019['C019_C004_Id']] = true;
        $g['C019'][$C019['C019_C004_Id']] = true;
        if ((int) $C019['C019_C004_Id'] === $C004_Id) {
            $g['perfilUsuario'] = (int) $C019['C019_C021_Id'];
        }
    }
    if (empty($g['perfilUsuario'])) {
        throw new \RuntimeException("Usuário {$g['usuarioAtual']} não possui vínculo C019 com a empresa {$C004_Id}.");
    }

    $sqlC021 = <<<SQL
        SELECT * FROM C021 WHERE C021_Id = '{$g['perfilUsuario']}'
    SQL;
    $resC021 = mysqli_query($sqlC021);
    if (!$resC021) {
        throw new \RuntimeException('Erro ao consultar C021: ' . mysql_error());
    }
    $C021 = mysql_fetch_assoc($resC021);
    if (empty($C021['C021_Id'])) {
        throw new \RuntimeException("Perfil C021 {$g['perfilUsuario']} não encontrado.");
    }
    $g['c021'] = $g['C021'] = $C021;

    $sqlC030 = <<<SQL
        SELECT C030_C029_Id FROM C030 WHERE C030_C021_Id = '{$g['perfilUsuario']}' AND C030_Acesso_Liberado = 'S'
    SQL;
    $resC030 = mysqli_query($sqlC030);
    if (!$resC030) {
        throw new \RuntimeException('Erro ao consultar C030: ' . mysql_error());
    }
    $g['c029Ids'] = array();
    while ($C030 = mysql_fetch_assoc($resC030)) {
        $g['c029Ids'][$C030['C030_C029_Id']] = true;
    }

    // O carregador do Hardness usa estes valores globais ao declarar as classes.
    error_log("QueueNF contexto: Q001={$Q001['Q001_Id']} | banco={$Q001['Q001_Database']}"
        . " | empresa={$C004_Id} | T007={$T007_Id} | usuarioQ001={$Q001['Q001_C007_Id']}"
        . " | usuarioEfetivo={$g['usuarioAtual']} | pathOrigem={$pathOrigem} | pathRaiz={$pathRaiz}"
        . " | pathDados={$pathDados} | pathDadosReal={$pathDadosReal}"
        . " | pathDadosUsuario={$g['pathDadosUsuario']} | IDE=ide/");
    require_once('bibliotecas/classes/VEN003.php');
    require_once('bibliotecas/classes/VEN012.php');
    require_once('bibliotecas/classes/EST004.php');
    $g['conf'] = new confSistema();

    return array('C004' => $C004, 'C020' => $C020);
}

function registrarErroContextoQueueNF($Q001, $notaConfirmada, $erro) {
    global $g;

    $usuario = !empty($Q001['Q001_C007_Id']) ? $Q001['Q001_C007_Id'] : 63;
    $log = "QueueNF contexto: Q001={$Q001['Q001_Id']} | banco={$Q001['Q001_Database']}"
        . " | empresa={$Q001['Q001_C004_Id']} | T007={$Q001['Q001_T007_Id']}"
        . " | usuarioQ001={$Q001['Q001_C007_Id']} | usuarioEfetivo={$usuario}"
        . " | pathOrigem={$Q001['Q001_Path']} | pathRaiz=" . getcwd()
        . " | pathDados=" . (isset($g['pathDados']) ? $g['pathDados'] : '')
        . " | erro={$erro}";
    error_log($log);

    if (!$notaConfirmada) {
        return;
    }

    $T007_Id = (int) $Q001['Q001_T007_Id'];
    $extra = mysql_real_escape_string((string) $erro);
    $sqlT007 = <<<SQL
        UPDATE T007 SET T007_TX_Resposta_Codigo = '123', T007_TX_Resposta_Extra = '{$extra}', T007_TX_Flag_Erro = '1' WHERE T007_Id = '{$T007_Id}'
    SQL;
    if (!mysqli_query($sqlT007)) {
        error_log("QueueNF contexto: erro ao atualizar T007 {$T007_Id}: " . mysql_error());
    }
}
