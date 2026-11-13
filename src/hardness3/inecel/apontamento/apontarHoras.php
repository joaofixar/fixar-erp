<?php
namespace hardness;

header("X-Robots-Tag: noindex, nofollow", true);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./index.php');
    exit;
}

if (empty($_SESSION['Usuario_Atual'])) {
    header('Location: ./index.php');
    exit;
}

$g = array(
    'usuarioAtual' => $_SESSION['Usuario_Atual'],
    'empresaAtual' => isset($_SESSION['Empresa_Atual']) ? $_SESSION['Empresa_Atual'] : null,
);

require_once('../../bibliotecas/confUsuario.php');
include_once('../../configuracoes/sysConfig.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once('../../bibliotecas/funcoesGerais.php');

function horasDb(): \mysqli
{
    global $g;
    return $g['conexaoBanco'];
}

function horasQuery($sql)
{
    return \mysqli_query(horasDb(), $sql);
}

function horasExec($sql)
{
    $resultado = horasQuery($sql);
    if ($resultado === false) {
        throw new \Exception(\mysqli_error(horasDb()));
    }
    return $resultado;
}

function horasBuscarLinhas($sql)
{
    $resultado = horasQuery($sql);
    $linhas = array();
    if ($resultado) {
        while ($linha = mysql_fetch_assoc($resultado)) {
            $linhas[] = $linha;
        }
    }
    return $linhas;
}

function horasBuscarUmaLinha($sql)
{
    $resultado = horasQuery($sql);
    if (!$resultado || mysql_num_rows($resultado) <= 0) {
        return null;
    }
    return mysql_fetch_assoc($resultado);
}

/**
 * Quando a OP do item tem um plano de corte (SMT134) vinculado, a propria
 * tela apontar.php e' quem controla a quantidade produzida/refugada e gera
 * o lancamento em T257 - entao o apontamento de horas nao deve controlar
 * saldo/quantidade nem gerar producao, so registrar o tempo e o que o
 * usuario apontar livremente.
 */
function horasOpPossuiPlanoCorte(int $itemId): bool
{
    if ($itemId <= 0) {
        return false;
    }

    $item = horasBuscarUmaLinha("SELECT T256_T255_Id FROM T256 WHERE T256_Id = '{$itemId}' LIMIT 1");
    if (!$item) {
        return false;
    }

    $plano = horasBuscarUmaLinha("SELECT SMT134_Id FROM SMT134 WHERE SMT134_T255_Id = '{$item['T256_T255_Id']}' LIMIT 1");
    return !empty($plano);
}

function horasFormatarDataHora($valor)
{
    if (empty($valor) || $valor === '0000-00-00 00:00:00') {
        return '-';
    }
    $timestamp = strtotime((string) $valor);
    return $timestamp ? date('d/m/Y H:i:s', $timestamp) : '-';
}

function horasFormatarDuracao($inicio, $fim)
{
    if (empty($inicio) || empty($fim)) {
        return '-';
    }
    $inicioTs = strtotime((string) $inicio);
    $fimTs = strtotime((string) $fim);
    if (!$inicioTs || !$fimTs || $fimTs < $inicioTs) {
        return '-';
    }
    $segundos = $fimTs - $inicioTs;
    $horas = floor($segundos / 3600);
    $minutos = floor(($segundos % 3600) / 60);
    $segs = $segundos % 60;
    return sprintf('%02d:%02d:%02d', $horas, $minutos, $segs);
}

function horasFlash($tipo = null, $mensagem = null)
{
    if ($tipo !== null && $mensagem !== null) {
        $_SESSION['apontamento_horas_flash'] = array('tipo' => $tipo, 'mensagem' => $mensagem);
        return null;
    }
    if (empty($_SESSION['apontamento_horas_flash'])) {
        return null;
    }
    $flash = $_SESSION['apontamento_horas_flash'];
    unset($_SESSION['apontamento_horas_flash']);
    return $flash;
}

function horas_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Gera o mesmo lancamento de producao (INSERT em T257/T257A) que a tela
 * apontar.php faz manualmente (ver apontamentoExecutarOutroInclusao em
 * inecel/apontamento/apontar.php). Chamada a cada finalizacao (mesmo
 * parcial) do ultimo processo do item, com a quantidade apontada apenas
 * daquela interacao (nao a soma acumulada), para que cada apontamento
 * parcial vire seu proprio lancamento de producao. Os campos que a tela de
 * apontar.php permite deixar em branco (peso, pedido, qtde de chapa,
 * observacao) vao vazios/zero aqui tambem. As triggers de T257 ja
 * recalculam T256_Quantidade_Produzida/Saldo, T255 e D009_Quantidade_OP
 * sozinhas.
 */
function horasLancarProducaoUltimoProcesso(int $t256Id, float $qtdeApontar): void
{
    global $g;

    if ($t256Id <= 0 || $qtdeApontar <= 0) {
        return;
    }

    $usuarioAtual = (int) ($g['usuarioAtual'] ?? 0);

    $dadosOP = horasBuscarUmaLinha("
        SELECT T256.T256_T255_Id, D001A.D001A_Tipo_Controle
        FROM T256
        LEFT JOIN D009 ON D009.D009_Id = T256.T256_D009_Id
        LEFT JOIN D001 ON D001.D001_Id = D009.D009_D001_Id
        LEFT JOIN D001A ON D001A.D001A_D001_Id = D001.D001_Id
        WHERE T256.T256_Id = '{$t256Id}'
        LIMIT 1
    ");
    if (!$dadosOP) {
        throw new \Exception('Item da OP nao encontrado para lancar a producao.');
    }

    $t255Id = (int) $dadosOP['T256_T255_Id'];
    $tipoControle = (string) $dadosOP['D001A_Tipo_Controle'];

    $possuiPlanoCorte = horasBuscarUmaLinha("SELECT SMT134_Id FROM SMT134 WHERE SMT134_T255_Id = '{$t255Id}' LIMIT 1");
    if (!$possuiPlanoCorte) {
        $totaisItem = horasBuscarUmaLinha("
            SELECT
                IFNULL(T256_Quantidade, 0) AS T256_Quantidade,
                IFNULL((SELECT SUM(T257_Quantidade) FROM T257 WHERE T257_T256_Id = '{$t256Id}'), 0) AS Quantidade_Apontada
            FROM T256
            WHERE T256_Id = '{$t256Id}'
        ");
        $quantidadePlanejada = $totaisItem ? (float) $totaisItem['T256_Quantidade'] : 0.0;
        $quantidadeJaApontada = $totaisItem ? (float) $totaisItem['Quantidade_Apontada'] : 0.0;
        if (($quantidadeJaApontada + $qtdeApontar) > $quantidadePlanejada + 0.001) {
            throw new \Exception('Nao e possivel lancar producao maior que a quantidade da OP (' . number_format($quantidadePlanejada, 3, ',', '.') . ').');
        }
    }

    $dadosSequencia = horasBuscarUmaLinha("
        SELECT COALESCE(MAX(T257A.T257A_Sequencia), 0) AS UltimaSequencia
        FROM T257A
        LEFT JOIN T257 ON T257A_T257_Id = T257_Id
        LEFT JOIN T256 ON T257_T256_Id = T256_Id
        WHERE T256_T255_Id = '{$t255Id}'
    ");
    $sequencia = (int) ($dadosSequencia['UltimaSequencia'] ?? 0) + 1;
    $lote = ($tipoControle === 'D') ? ($t255Id . ' - ' . $sequencia) : ' ';
    $loteSql = str_replace("'", "''", $lote);
    $quantidadeSql = sprintf('%.4F', $qtdeApontar);

    horasExec("
        INSERT INTO T257 (T257_T256_Id, T257_C007_Id, T257_T066_Id, T257_Data_Producao, T257_Quantidade)
        VALUES ('{$t256Id}', '{$usuarioAtual}', '0', current_date(), '{$quantidadeSql}')
    ");

    $t257Id = (int) horasDb()->insert_id;
    if ($t257Id <= 0) {
        throw new \Exception('Nao foi possivel identificar o apontamento de producao gravado.');
    }

    horasExec("
        INSERT INTO T257A (T257A_T257_Id, T257A_Flag_Inserir_Producao_Individual, T257A_Lote, T257A_Sequencia, T257A_Peso, T257A_T005_Id, T257A_Qtde_Chapa, T257A_Observacao)
        VALUES ('{$t257Id}', 'N', '{$loteSql}', '{$sequencia}', '0', '0', '0', '')
    ");
}

/**
 * A quantidade alvo do 1o processo (menor T262_Ordem) do item e' a
 * quantidade do item (T256_Quantidade). Dos processos seguintes e' a
 * quantidade apontada (boa) no processo anterior da sequencia, ja que o
 * material refugado nao segue para a proxima etapa.
 */
function horasQuantidadeAlvoProcesso(int $t262Id, int $itemId, float $quantidadeItemTotal): float
{
    $processoAtual = horasBuscarUmaLinha("SELECT T262_Ordem FROM T262 WHERE T262_Id = '{$t262Id}' LIMIT 1");
    if (!$processoAtual) {
        return $quantidadeItemTotal;
    }

    // Desempata por T262_Id quando T262_Ordem estiver zerada/repetida entre
    // processos do mesmo item (dado mal cadastrado), mantendo a mesma ordem
    // usada na tela (T262_Ordem ASC, T262_Id ASC).
    $processoAnterior = horasBuscarUmaLinha("
        SELECT T262_Id
        FROM T262
        WHERE T262_T256_Id = '{$itemId}'
          AND (T262_Ordem < '{$processoAtual['T262_Ordem']}'
               OR (T262_Ordem = '{$processoAtual['T262_Ordem']}' AND T262_Id < '{$t262Id}'))
        ORDER BY T262_Ordem DESC, T262_Id DESC
        LIMIT 1
    ");
    if (!$processoAnterior) {
        return $quantidadeItemTotal;
    }

    $somaAnterior = horasBuscarUmaLinha("
        SELECT COALESCE(SUM(SMT009_Quantidade_Apontada), 0) AS Soma_Apontada
        FROM SMT009
        WHERE SMT009_T262_Id = '{$processoAnterior['T262_Id']}'
    ");

    return (float) $somaAnterior['Soma_Apontada'];
}

/**
 * Um processo so pode ser marcado como totalmente concluido ('B') se nao
 * houver processo anterior (e' o 1o da sequencia) ou se o processo anterior
 * ja estiver ele mesmo concluido ('B'). Enquanto o anterior ainda tiver
 * saldo, ele pode liberar mais quantidade depois, entao o processo atual
 * fica como parado ('P') mesmo tendo consumido tudo que foi liberado ate
 * agora.
 */
function horasProcessoAnteriorConcluido(int $t262Id, int $itemId): bool
{
    $processoAtual = horasBuscarUmaLinha("SELECT T262_Ordem FROM T262 WHERE T262_Id = '{$t262Id}' LIMIT 1");
    if (!$processoAtual) {
        return true;
    }

    $processoAnterior = horasBuscarUmaLinha("
        SELECT T262_Id
        FROM T262
        WHERE T262_T256_Id = '{$itemId}'
          AND (T262_Ordem < '{$processoAtual['T262_Ordem']}'
               OR (T262_Ordem = '{$processoAtual['T262_Ordem']}' AND T262_Id < '{$t262Id}'))
        ORDER BY T262_Ordem DESC, T262_Id DESC
        LIMIT 1
    ");
    if (!$processoAnterior) {
        return true;
    }

    $statusAnterior = horasBuscarUmaLinha("SELECT T262A_Status FROM T262A WHERE T262A_T262_Id = '{$processoAnterior['T262_Id']}' LIMIT 1");
    return $statusAnterior && $statusAnterior['T262A_Status'] === 'B';
}

function horas_company_rows(): array
{
    $usuarioId = (int) ($_SESSION['Usuario_Atual'] ?? 0);
    if ($usuarioId <= 0) {
        return array();
    }

    $sql = "
        SELECT C019.C019_C004_Id, C019.C019_C021_Id, C004.C004_Nome_Empresa, C004.C004_Nome_Abreviado, C004.C004_Caminho_Sistema, C004.C004_Banco_Configuracao
        FROM C019
        INNER JOIN C004 ON C004.C004_Id = C019.C019_C004_Id
        WHERE C019.C019_C007_Id = {$usuarioId}
          AND C004.C004_Flag_Ativo = 'S'
        ORDER BY C004.C004_Ordem_Empresa ASC, C004.C004_Id ASC
    ";

    return horasBuscarLinhas($sql);
}

function horas_company_theme(int $empresaId): array
{
    $themes = array(
        array('bg' => '#eef6ff', 'panel' => '#ffffff', 'line' => '#c9dffc', 'primary' => '#155eef', 'accent' => '#dceaff'),
        array('bg' => '#f3fbf5', 'panel' => '#ffffff', 'line' => '#cfe8d5', 'primary' => '#168a4a', 'accent' => '#dff5e6'),
        array('bg' => '#fff8ef', 'panel' => '#ffffff', 'line' => '#f0dac0', 'primary' => '#b35a00', 'accent' => '#ffe8cf'),
        array('bg' => '#fff1f3', 'panel' => '#ffffff', 'line' => '#f1ccd4', 'primary' => '#c23d5a', 'accent' => '#ffdfe5'),
        array('bg' => '#f5f3ff', 'panel' => '#ffffff', 'line' => '#ddd7fb', 'primary' => '#6941c6', 'accent' => '#ebe5ff'),
        array('bg' => '#eefaf9', 'panel' => '#ffffff', 'line' => '#c8e9e4', 'primary' => '#0f8b8d', 'accent' => '#d7f4ef'),
    );

    return $themes[$empresaId % count($themes)];
}

function horas_selected_company_name(array $companies): string
{
    $empresaAtual = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    foreach ($companies as $company) {
        if ((int) $company['C019_C004_Id'] === $empresaAtual) {
            return (string) ($company['C004_Nome_Abreviado'] ?: $company['C004_Nome_Empresa']);
        }
    }

    return (string) ($_SESSION['Nome_Empresa_Atual'] ?? 'Empresa');
}

/**
 * SMT009_C007_Id grava o usuario logado (C007_Id da sessao) - nao ha
 * selecao manual de funcionario nesta tela.
 */

$opId = isset($_GET['op_id']) ? (int) $_GET['op_id'] : 0;
$itemId = isset($_GET['item_id']) ? (int) $_GET['item_id'] : 0;

/**
 * Ações (POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'iniciar') {
    $itemPostId = isset($_POST['item_id']) ? (int) $_POST['item_id'] : 0;
    $opPostId = isset($_POST['op_id']) ? (int) $_POST['op_id'] : 0;
    $t262Id = isset($_POST['t262_id']) ? (int) $_POST['t262_id'] : 0;
    $d079Id = isset($_POST['d079_id']) ? (int) $_POST['d079_id'] : 0;
    $usuarioLogadoId = (int) $_SESSION['Usuario_Atual'];

    if ($t262Id <= 0 || $usuarioLogadoId <= 0) {
        horasFlash('erro', 'Nao foi possivel identificar o usuario logado.');
    } else {
        $processo = horasBuscarUmaLinha("SELECT T262_Id FROM T262 WHERE T262_Id = '{$t262Id}' AND T262_T256_Id = '{$itemPostId}' LIMIT 1");
        $processoStatus = horasBuscarUmaLinha("SELECT T262A_Status FROM T262A WHERE T262A_T262_Id = '{$t262Id}' LIMIT 1");
        $emAberto = horasBuscarUmaLinha("SELECT SMT009_Id FROM SMT009 WHERE SMT009_T262_Id = '{$t262Id}' AND SMT009_Data_Hora_Fim IS NULL LIMIT 1");
        $maquinaEscolhida = $d079Id > 0 ? horasBuscarUmaLinha("SELECT D079_Id FROM D079 WHERE D079_Id = '{$d079Id}' LIMIT 1") : null;

        $opTemPlanoCorteIniciar = horasOpPossuiPlanoCorte($itemPostId);

        $saldoIniciar = 1.0;
        if (!$opTemPlanoCorteIniciar) {
            $itemIniciar = horasBuscarUmaLinha("SELECT T256_Quantidade FROM T256 WHERE T256_Id = '{$itemPostId}' LIMIT 1");
            $quantidadeItemTotalIniciar = $itemIniciar ? (float) $itemIniciar['T256_Quantidade'] : 0.0;
            $quantidadeAlvoIniciar = $processo ? horasQuantidadeAlvoProcesso($t262Id, $itemPostId, $quantidadeItemTotalIniciar) : 0.0;
            $somaJaFeitaIniciar = horasBuscarUmaLinha("
                SELECT COALESCE(SUM(SMT009_Quantidade_Apontada), 0) + COALESCE(SUM(SMT009_Quantidade_Refugada), 0) AS Soma
                FROM SMT009
                WHERE SMT009_T262_Id = '{$t262Id}'
            ");
            $saldoIniciar = $quantidadeAlvoIniciar - ($somaJaFeitaIniciar ? (float) $somaJaFeitaIniciar['Soma'] : 0.0);
        }

        if (!$processo) {
            horasFlash('erro', 'Processo nao encontrado para este item.');
        } else if ($processoStatus && $processoStatus['T262A_Status'] === 'B') {
            horasFlash('erro', 'Este processo ja foi finalizado e nao aceita novos apontamentos.');
        } else if ($emAberto) {
            horasFlash('erro', 'Ja existe um apontamento em andamento para este processo.');
        } else if ($saldoIniciar <= 0.001) {
            horasFlash('erro', 'Nao ha saldo liberado para produzir neste processo. Aguarde o processo anterior liberar mais quantidade.');
        } else if (!$maquinaEscolhida) {
            horasFlash('erro', 'Selecione a maquina em que esta trabalhando.');
        } else {
            try {
                horasExec("INSERT INTO SMT009 (SMT009_T262_Id, SMT009_Data_Hora_Inicio, SMT009_C007_Id, SMT009_D079_Id) VALUES ('{$t262Id}', NOW(), '{$usuarioLogadoId}', '{$d079Id}')");
                horasFlash('sucesso', 'Apontamento de horas iniciado.');
            } catch (\Exception $e) {
                horasFlash('erro', 'Nao foi possivel iniciar o apontamento.');
            }
        }
    }

    header('Location: ./apontarHoras.php?op_id=' . $opPostId . '&item_id=' . $itemPostId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'finalizar') {
    $itemPostId = isset($_POST['item_id']) ? (int) $_POST['item_id'] : 0;
    $opPostId = isset($_POST['op_id']) ? (int) $_POST['op_id'] : 0;
    $smt009Id = isset($_POST['smt009_id']) ? (int) $_POST['smt009_id'] : 0;
    $smt012Id = isset($_POST['smt012_id']) && $_POST['smt012_id'] !== '' ? (int) $_POST['smt012_id'] : 0;
    $quantidadeApontada = isset($_POST['quantidade_apontada']) ? (float) str_replace(',', '.', trim((string) $_POST['quantidade_apontada'])) : 0.0;
    $quantidadeRefugada = isset($_POST['quantidade_refugada']) ? (float) str_replace(',', '.', trim((string) $_POST['quantidade_refugada'])) : 0.0;
    $quantidadeApontada = max(0.0, $quantidadeApontada);
    $quantidadeRefugada = max(0.0, $quantidadeRefugada);

    $registro = horasBuscarUmaLinha("
        SELECT SMT009.SMT009_Id, SMT009.SMT009_T262_Id
        FROM SMT009
        INNER JOIN T262 ON T262.T262_Id = SMT009.SMT009_T262_Id
        WHERE SMT009.SMT009_Id = '{$smt009Id}'
          AND T262.T262_T256_Id = '{$itemPostId}'
          AND SMT009.SMT009_Data_Hora_Fim IS NULL
        LIMIT 1
    ");

    $opTemPlanoCorte = horasOpPossuiPlanoCorte($itemPostId);

    $quantidadeAlvo = 0.0;
    $quantidadeSoma = 0.0;
    $motivoObrigaQuantidade = false;
    if (!$opTemPlanoCorte) {
        $itemFinalizar = horasBuscarUmaLinha("SELECT T256_Quantidade FROM T256 WHERE T256_Id = '{$itemPostId}' LIMIT 1");
        $quantidadeItemTotal = $itemFinalizar ? (float) $itemFinalizar['T256_Quantidade'] : 0.0;
        $quantidadeAlvo = $registro ? horasQuantidadeAlvoProcesso((int) $registro['SMT009_T262_Id'], $itemPostId, $quantidadeItemTotal) : $quantidadeItemTotal;

        $quantidadeSoma = $quantidadeApontada + $quantidadeRefugada;
        if ($registro) {
            $somaAnterior = horasBuscarUmaLinha("
                SELECT COALESCE(SUM(SMT009_Quantidade_Apontada), 0) AS Soma_Apontada, COALESCE(SUM(SMT009_Quantidade_Refugada), 0) AS Soma_Refugada
                FROM SMT009
                WHERE SMT009_T262_Id = '{$registro['SMT009_T262_Id']}'
                  AND SMT009_Id != '{$smt009Id}'
            ");
            $quantidadeSoma += (float) $somaAnterior['Soma_Apontada'] + (float) $somaAnterior['Soma_Refugada'];
        }

        // Por padrao (sem motivo, ou motivo nao encontrado) a quantidade e
        // obrigatoria. Um motivo com SMT012_Obriga_Apontar_Quantidade = 'N'
        // libera finalizar com quantidade apontada e refugada zeradas (ex.:
        // maquina quebrou antes de produzir nada).
        $motivoObrigaQuantidade = true;
        if ($smt012Id > 0) {
            $motivoSelecionado = horasBuscarUmaLinha("SELECT SMT012_Obriga_Apontar_Quantidade FROM SMT012 WHERE SMT012_Id = '{$smt012Id}' LIMIT 1");
            $motivoObrigaQuantidade = !$motivoSelecionado || $motivoSelecionado['SMT012_Obriga_Apontar_Quantidade'] !== 'N';
        }
    }

    if (!$registro) {
        horasFlash('erro', 'Apontamento em andamento nao encontrado.');
    } else if (!$opTemPlanoCorte && $motivoObrigaQuantidade && $quantidadeApontada <= 0 && $quantidadeRefugada <= 0) {
        horasFlash('erro', 'Informe a quantidade apontada ou a quantidade refugada (nao pode deixar os dois zerados).');
    } else if (!$opTemPlanoCorte && $quantidadeSoma > $quantidadeAlvo + 0.001) {
        horasFlash('erro', 'A soma de todos os apontamentos deste processo (apontada + refugada) nao pode ultrapassar a quantidade deste processo (' . number_format($quantidadeAlvo, 3, ',', '.') . '). Soma informada: ' . number_format($quantidadeSoma, 3, ',', '.') . '.');
    } else if (!$opTemPlanoCorte && $quantidadeSoma < $quantidadeAlvo - 0.001 && $smt012Id <= 0) {
        horasFlash('erro', 'Como a quantidade apontada ainda nao completa o total deste processo, e obrigatorio informar o motivo da parada.');
    } else {
        $motivoSql = $smt012Id > 0 ? "'{$smt012Id}'" : 'NULL';
        if ($opTemPlanoCorte) {
            // Com plano de corte, quem controla quantidade/produzido e' a
            // tela apontar.php - aqui so registra o que o usuario informar.
            $statusProcesso = $smt012Id > 0 ? 'P' : 'B';
        } else {
            $statusProcesso = 'P';
            if ($quantidadeSoma >= $quantidadeAlvo - 0.001) {
                // So fecha ('B') se nao houver processo anterior pendente: se o
                // anterior ainda tiver saldo, ele pode liberar mais quantidade
                // depois, entao este processo continua aberto mesmo tendo
                // consumido tudo que foi liberado ate agora.
                $statusProcesso = horasProcessoAnteriorConcluido((int) $registro['SMT009_T262_Id'], $itemPostId) ? 'B' : 'P';
            }
        }
        $quantidadeApontadaSql = sprintf('%.4F', $quantidadeApontada);
        $quantidadeRefugadaSql = sprintf('%.4F', $quantidadeRefugada);
        horasExec("START TRANSACTION");
        try {
            horasExec("UPDATE SMT009 SET SMT009_Data_Hora_Fim = NOW(), SMT009_SMT012_Id = {$motivoSql}, SMT009_Quantidade_Apontada = {$quantidadeApontadaSql}, SMT009_Quantidade_Refugada = {$quantidadeRefugadaSql} WHERE SMT009_Id = '{$smt009Id}'");
            horasExec("UPDATE T262A SET T262A_Status = '{$statusProcesso}' WHERE T262A_T262_Id = '{$registro['SMT009_T262_Id']}'");

            // Com plano de corte, a producao (T257/T257A) e' gerada pela tela
            // apontar.php, entao o apontamento de horas nao deve lancar nada.
            if (!$opTemPlanoCorte) {
                // Se o processo finalizado for o ultimo da sequencia do item,
                // gera um lancamento de producao (igual a tela apontar.php) a
                // cada interacao/finalizacao (mesmo parcial), usando so a
                // quantidade apontada desta finalizacao (nao a soma
                // acumulada), para nao duplicar contagem entre finalizacoes
                // parciais sucessivas. Desempata por T262_Id (igual
                // horasQuantidadeAlvoProcesso) para nao marcar mais de um
                // processo como "ultimo" quando T262_Ordem vier
                // zerada/repetida entre os processos do item.
                $ultimoProcesso = horasBuscarUmaLinha("
                    SELECT T262_Id
                    FROM T262
                    WHERE T262_T256_Id = '{$itemPostId}'
                    ORDER BY T262_Ordem DESC, T262_Id DESC
                    LIMIT 1
                ");
                $ehUltimoProcesso = $ultimoProcesso
                    && (int) $ultimoProcesso['T262_Id'] === (int) $registro['SMT009_T262_Id'];

                if ($ehUltimoProcesso) {
                    horasLancarProducaoUltimoProcesso($itemPostId, $quantidadeApontada);
                }
            }

            horasExec("COMMIT");
            horasFlash('sucesso', 'Apontamento de horas finalizado.');
        } catch (\Exception $e) {
            horasExec("ROLLBACK");
            horasFlash('erro', 'Nao foi possivel finalizar o apontamento: ' . $e->getMessage());
        }
    }

    header('Location: ./apontarHoras.php?op_id=' . $opPostId . '&item_id=' . $itemPostId);
    exit;
}

/**
 * Dados da tela
 */
$item = horasBuscarUmaLinha("
    SELECT
        T256_Id,
        T256_T255_Id,
        T256_Quantidade,
        D001_Codigo_Produto,
        D001_Descricao_Produto,
        D037_Unidade
    FROM T256
    LEFT JOIN D009 ON D009_Id = T256_D009_Id
    LEFT JOIN D049 ON D049_Id = D009_D049_Id
    LEFT JOIN D001 ON D001_Id = D049_D001_Id
    LEFT JOIN D037 ON D037_Id = D001_D037_Id
    WHERE T256_Id = '{$itemId}'
    LIMIT 1
");

if ($item && $opId <= 0) {
    $opId = (int) $item['T256_T255_Id'];
}

$processos = array();
$motivos = array();
$maquinas = array();
$historico = array();
$opTemPlanoCorte = $item ? horasOpPossuiPlanoCorte((int) $item['T256_Id']) : false;

if ($item) {
    $processos = horasBuscarLinhas("
        SELECT
            T262.T262_Id,
            T262.T262_Ordem,
            T262.T262_Descricao,
            D079.D079_Nome AS Maquina,
            COALESCE(SMT006.SMT006_Descricao, T262.T262_Descricao) AS Operacao,
            aberto.SMT009_Id AS Aberto_Id,
            aberto.SMT009_Data_Hora_Inicio AS Aberto_Inicio,
            abertoUsuario.C007_Primeiro_Nome AS Aberto_Usuario,
            T262A.T262A_Status AS Status,
            COALESCE((
                SELECT SUM(s.SMT009_Quantidade_Apontada)
                FROM SMT009 s
                WHERE s.SMT009_T262_Id = T262.T262_Id
            ), 0) AS Quantidade_Apontada,
            COALESCE((
                SELECT SUM(s.SMT009_Quantidade_Refugada)
                FROM SMT009 s
                WHERE s.SMT009_T262_Id = T262.T262_Id
            ), 0) AS Quantidade_Refugada
        FROM T262
        LEFT JOIN D079 ON D079_Id = T262_D079_Id
        LEFT JOIN SMT006 ON SMT006.SMT006_Descricao = T262.T262_Descricao
        LEFT JOIN SMT009 aberto ON aberto.SMT009_T262_Id = T262.T262_Id AND aberto.SMT009_Data_Hora_Fim IS NULL
        LEFT JOIN C007 abertoUsuario ON abertoUsuario.C007_Id = aberto.SMT009_C007_Id
        LEFT JOIN T262A ON T262A.T262A_T262_Id = T262.T262_Id
        WHERE T262.T262_T256_Id = '{$itemId}'
        GROUP BY T262.T262_Id
        ORDER BY T262.T262_Ordem ASC, T262.T262_Id ASC
    ");

    // O total do 1o processo e a quantidade do item; dos processos seguintes
    // e a quantidade apontada (boa) no processo anterior da sequencia, ja
    // que o material refugado nao segue para a proxima etapa.
    foreach ($processos as $indiceProcesso => $itemProcesso) {
        if ($indiceProcesso === 0) {
            $processos[$indiceProcesso]['Quantidade_Total'] = (float) ($item['T256_Quantidade'] ?? 0);
        } else {
            $processos[$indiceProcesso]['Quantidade_Total'] = (float) $processos[$indiceProcesso - 1]['Quantidade_Apontada'];
        }
    }

    $motivos = horasBuscarLinhas("
        SELECT SMT012_Id, SMT012_Descricao, SMT012_Cor, SMT012_Obriga_Apontar_Quantidade
        FROM SMT012
        ORDER BY SMT012_Descricao ASC
    ");

    $maquinas = horasBuscarLinhas("
        SELECT D079_Id, D079_Nome
        FROM D079
        ORDER BY D079_Nome ASC
    ");

    $historico = horasBuscarLinhas("
        SELECT
            SMT009.SMT009_Id,
            SMT009.SMT009_Data_Hora_Inicio,
            SMT009.SMT009_Data_Hora_Fim,
            C007.C007_Primeiro_Nome,
            SMT012.SMT012_Descricao,
            T262.T262_Ordem,
            T262.T262_Descricao,
            D079.D079_Nome AS Maquina,
            COALESCE(SMT006.SMT006_Descricao, T262.T262_Descricao) AS Operacao
        FROM SMT009
        INNER JOIN T262 ON T262.T262_Id = SMT009.SMT009_T262_Id
        LEFT JOIN D079 ON D079_Id = T262_D079_Id
        LEFT JOIN SMT006 ON SMT006.SMT006_Descricao = T262.T262_Descricao
        LEFT JOIN C007 ON C007.C007_Id = SMT009.SMT009_C007_Id
        LEFT JOIN SMT012 ON SMT012_Id = SMT009.SMT009_SMT012_Id
        WHERE T262.T262_T256_Id = '{$itemId}'
        ORDER BY SMT009.SMT009_Id DESC
    ");
}

$companies = horas_company_rows();
$usuario = $_SESSION['Primeiro_Nome_Usuario'] ?? $_SESSION['Nome_Usuario'] ?? 'Usuario';
$empresa = $_SESSION['Nome_Empresa_Atual'] ?? 'Empresa';
$empresaAtualId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$companyTheme = horas_company_theme($empresaAtualId);
$empresaCurta = horas_selected_company_name($companies);
$flash = horasFlash();

$produtoLinha = '';
if ($item) {
    $produtoLinha = $item['D001_Codigo_Produto'] . ' - ' . $item['D001_Descricao_Produto'];
    if (!empty($item['D037_Unidade'])) {
        $produtoLinha .= ' / ' . $item['D037_Unidade'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apontamento de Horas OP</title>
    <style>
        :root{--bg:<?php echo horas_h($companyTheme['bg']); ?>;--card:<?php echo horas_h($companyTheme['panel']); ?>;--line:<?php echo horas_h($companyTheme['line']); ?>;--text:#20324d;--muted:#697c96;--primary:<?php echo horas_h($companyTheme['primary']); ?>;--shadow:0 18px 40px rgba(17,47,84,.12);--success-bg:#ebfaf1;--success-text:#216f4b;--danger-bg:#fff0f0;--danger-text:#a53a3a;--warn-bg:#fff7e0;--warn-text:#8a5a00}
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;background:linear-gradient(180deg,#f4f7fb 0%,var(--bg) 100%);color:var(--text)}
        .page{max-width:1420px;margin:0 auto;padding:16px}
        .window{background:var(--card);border:1px solid #bcc9d6;border-radius:12px;overflow:hidden;box-shadow:var(--shadow)}
        .window-bar{background:var(--primary);color:#fff;font-weight:700;font-size:15px;padding:9px 12px}
        .window-body{padding:16px;background:#f5f7fa}
        .top-meta{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:10px;flex-wrap:wrap}
        .top-actions{display:flex;gap:8px;flex-wrap:wrap}
        .button{border:1px solid #6c7d92;border-radius:6px;padding:8px 14px;background:#fff;color:var(--text);font-size:13px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}
        .button.primary{background:var(--primary);border-color:var(--primary);color:#fff}
        .button.danger{background:#c23d3d;border-color:#c23d3d;color:#fff}
        .button:disabled{opacity:.5;cursor:default}
        .userbox{font-size:13px;color:var(--muted)}
        .flash{margin-bottom:12px;padding:12px 14px;border-radius:8px;font-size:14px}
        .flash.sucesso{background:var(--success-bg);color:var(--success-text);border:1px solid #b9e7cc}
        .flash.erro{background:var(--danger-bg);color:var(--danger-text);border:1px solid #f0c4c4}
        .produto-bar{width:100%;height:24px;border:1px solid #bfc9d6;border-radius:4px;background:#fff;padding:2px 8px;font-size:13px;color:#3d4e68;margin-bottom:14px;line-height:24px}
        .processos{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:12px;margin-bottom:18px}
        .processo-card{background:#fff;border:1px solid #c9d3df;border-radius:10px;padding:12px 14px}
        .processo-card h3{margin:0 0 4px;font-size:14px}
        .processo-card p{margin:0 0 10px;font-size:12px;color:var(--muted)}
        .processo-qtd{display:grid;grid-template-columns:1fr 1fr;gap:2px 10px;font-size:12px;color:var(--muted);margin-bottom:10px}
        .processo-qtd strong{color:var(--text)}
        .processo-card form{display:flex;gap:6px;flex-wrap:wrap;align-items:center}
        .processo-card select{flex:1;min-width:120px;height:30px;border:1px solid #bfc9d6;border-radius:4px;padding:2px 6px;font-size:13px}
        .processo-card.processo-finalizado{background:var(--success-bg);border-color:#b9e7cc}
        .processo-card.processo-parado{background:var(--danger-bg);border-color:#f0c4c4}
        .badge-aberto{display:inline-block;padding:3px 8px;border-radius:999px;background:var(--warn-bg);color:var(--warn-text);font-size:12px;margin-bottom:8px}
        .badge-finalizado{display:inline-block;padding:3px 8px;border-radius:999px;background:var(--success-text);color:#fff;font-size:12px;margin-bottom:0}
        .grid-wrap{background:#fff;border:1px solid #c9d3df;border-radius:8px;overflow:hidden}
        .table-scroll{overflow-x:auto}
        table{width:100%;min-width:960px;border-collapse:collapse}
        thead th{background:#eef2f7;border:1px solid #c9d3df;padding:12px 8px;font-size:13px;color:#4b5563;text-align:center}
        tbody td{border:1px solid #d9e1ea;padding:10px 8px;font-size:13px;background:#fff}
        tbody tr:nth-child(even) td{background:#fafbfd}
        .center{text-align:center}
        .empty{padding:32px 20px;text-align:center;color:var(--muted);background:#fff}
        .section-title{margin:18px 0 10px;font-size:15px;font-weight:700}
        .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.45);display:none;align-items:center;justify-content:center;z-index:1000;padding:16px}
        .modal-overlay.active{display:flex}
        .modal-box{background:#fff;border-radius:10px;padding:20px;max-width:420px;width:100%;box-shadow:0 20px 60px rgba(15,23,42,.25)}
        .modal-box h3{margin:0 0 14px;font-size:16px}
        .modal-field{margin-bottom:12px;display:flex;flex-direction:column;gap:4px}
        .modal-field label{font-size:12px;color:var(--muted)}
        .modal-field input,.modal-field select{height:34px;border:1px solid #bfc9d6;border-radius:6px;padding:4px 8px;font-size:13px}
        .modal-error{color:var(--danger-text);font-size:12px;min-height:16px;margin-top:-4px}
        .modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:10px}
        .maquina-lista{max-height:240px;overflow-y:auto;border:1px solid #bfc9d6;border-radius:6px;margin-bottom:10px}
        .maquina-item{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:9px 12px;font-size:13px;cursor:pointer;border-bottom:1px solid #e5e9ef;border-left:4px solid transparent}
        .maquina-item:last-child{border-bottom:none}
        .maquina-item:hover{background:#f5f7fa}
        .maquina-item-info{display:flex;align-items:center;gap:10px}
        .maquina-item-id{color:var(--muted);font-size:12px}
        .maquina-item-check{opacity:0;color:var(--primary);font-weight:700;font-size:16px;line-height:1}
        .maquina-item.selecionado{background:var(--accent);border-left-color:var(--primary)}
        .maquina-item.selecionado .maquina-item-nome{color:var(--primary);font-weight:700}
        .maquina-item.selecionado .maquina-item-id{color:var(--primary)}
        .maquina-item.selecionado .maquina-item-check{opacity:1}
        .maquina-item-vazio{padding:12px;font-size:13px;color:var(--muted);text-align:center}
        @media (max-width:760px){.top-meta{flex-direction:column;align-items:flex-start}}
    </style>
</head>
<body>
<?php if ($item): ?>
<div class="page">
    <div class="window">
        <div class="window-bar">APONTAMENTO DE HORAS OP</div>
        <div class="window-body">
            <div class="top-meta">
                <div class="top-actions">
                    <a class="button" href="./itensOp.php?op_id=<?php echo (int) $opId; ?>">Voltar para Itens da OP</a>
                    <a class="button" href="./OrdemProducao.php">Voltar para Lista de OPs</a>
                    <a class="button" href="./apontarHoras.php?logout=1">Sair</a>
                </div>
                <div class="userbox">
                    <strong><?php echo horas_h($usuario); ?></strong>
                    <div><?php echo horas_h($empresa); ?><?php echo !empty($empresaCurta) ? ' | ' . horas_h($empresaCurta) : ''; ?> | OP: <?php echo (int) $opId; ?></div>
                </div>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="flash <?php echo horas_h($flash['tipo']); ?>"><?php echo horas_h($flash['mensagem']); ?></div>
            <?php endif; ?>

            <input class="produto-bar" type="text" value="<?php echo horas_h($produtoLinha); ?>" readonly>

            <div class="section-title">Processos do item</div>
            <?php if (empty($processos)): ?>
                <div class="empty">Nenhum processo cadastrado para este item.</div>
            <?php else: ?>
                <div class="processos">
                    <?php foreach ($processos as $processo): ?>
                        <?php
                            $statusProcesso = $processo['Status'] ?? null;
                            $cardClass = 'processo-card';
                            if ($statusProcesso === 'B') {
                                $cardClass .= ' processo-finalizado';
                            } elseif ($statusProcesso === 'P') {
                                $cardClass .= ' processo-parado';
                            }
                            $processoQtdTotal = (float) $processo['Quantidade_Total'];
                            $processoQtdApontada = (float) $processo['Quantidade_Apontada'];
                            $processoQtdRefugada = (float) $processo['Quantidade_Refugada'];
                            $processoQtdSaldo = max(0, $processoQtdTotal - $processoQtdApontada - $processoQtdRefugada);
                        ?>
                        <div class="<?php echo $cardClass; ?>">
                            <h3><?php echo (int) $processo['T262_Ordem']; ?> - <?php echo horas_h($processo['Operacao'] ?: 'Sem operacao'); ?></h3>
                            <p><?php echo horas_h($processo['Maquina'] ?: 'Sem maquina'); ?></p>
                            <?php if (!$opTemPlanoCorte): ?>
                                <div class="processo-qtd">
                                    <span>Total: <strong><?php echo horas_h(number_format($processoQtdTotal, 3, ',', '.')); ?></strong></span>
                                    <span>Saldo: <strong><?php echo horas_h(number_format($processoQtdSaldo, 3, ',', '.')); ?></strong></span>
                                    <span>Apontada: <strong><?php echo horas_h(number_format($processoQtdApontada, 3, ',', '.')); ?></strong></span>
                                    <span>Refugada: <strong><?php echo horas_h(number_format($processoQtdRefugada, 3, ',', '.')); ?></strong></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($statusProcesso === 'B'): ?>
                                <div class="badge-finalizado">Processo finalizado</div>
                            <?php elseif (!empty($processo['Aberto_Id'])): ?>
                                <div class="badge-aberto">Em andamento: <?php echo horas_h($processo['Aberto_Usuario']); ?> desde <?php echo horas_h(horasFormatarDataHora($processo['Aberto_Inicio'])); ?></div>
                                <button type="button" class="button danger" onclick="horasAbrirFinalizar(<?php echo (int) $processo['Aberto_Id']; ?>, <?php echo json_encode($processoQtdApontada + $processoQtdRefugada); ?>, <?php echo json_encode($processoQtdTotal); ?>)">Finalizar</button>
                            <?php elseif (!$opTemPlanoCorte && $processoQtdSaldo <= 0.001): ?>
                                <div class="badge-aberto">Aguardando saldo do processo anterior</div>
                            <?php else: ?>
                                <span>Usuario: <strong><?php echo horas_h($usuario); ?></strong></span>
                                <button type="button" class="button primary" onclick="horasAbrirIniciar(<?php echo (int) $processo['T262_Id']; ?>)">Iniciar</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="section-title">Historico de apontamentos</div>
            <div class="grid-wrap">
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Processo</th>
                                <th>Maquina</th>
                                <th>Usuario</th>
                                <th>Inicio</th>
                                <th>Fim</th>
                                <th>Duracao</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historico)): ?>
                                <tr><td class="empty" colspan="7">Ainda nao existem apontamentos de horas para este item.</td></tr>
                            <?php else: ?>
                                <?php foreach ($historico as $linha): ?>
                                    <tr>
                                        <td class="center"><?php echo (int) $linha['T262_Ordem']; ?> - <?php echo horas_h($linha['Operacao'] ?: '-'); ?></td>
                                        <td class="center"><?php echo horas_h($linha['Maquina'] ?: '-'); ?></td>
                                        <td><?php echo horas_h($linha['C007_Primeiro_Nome'] ?: '-'); ?></td>
                                        <td class="center"><?php echo horas_h(horasFormatarDataHora($linha['SMT009_Data_Hora_Inicio'])); ?></td>
                                        <td class="center"><?php echo horas_h(horasFormatarDataHora($linha['SMT009_Data_Hora_Fim'])); ?></td>
                                        <td class="center"><?php echo horas_h(horasFormatarDuracao($linha['SMT009_Data_Hora_Inicio'], $linha['SMT009_Data_Hora_Fim'])); ?></td>
                                        <td><?php echo horas_h($linha['SMT012_Descricao'] ?: '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="page"><div class="window"><div class="window-body"><div class="empty">Item nao encontrado para esta OP.</div></div></div></div>
<?php endif; ?>

<div class="modal-overlay" id="horasModalIniciar">
    <div class="modal-box">
        <h3>Selecionar maquina</h3>
        <form method="post" id="horasFormIniciar" action="./apontarHoras.php?op_id=<?php echo (int) $opId; ?>&item_id=<?php echo (int) $itemId; ?>" onsubmit="return horasValidarIniciar(event)">
            <input type="hidden" name="acao" value="iniciar">
            <input type="hidden" name="item_id" value="<?php echo (int) $itemId; ?>">
            <input type="hidden" name="op_id" value="<?php echo (int) $opId; ?>">
            <input type="hidden" name="t262_id" id="horasIniciarT262Id" value="">
            <input type="hidden" name="d079_id" id="horasIniciarD079Id" value="">
            <div class="modal-field">
                <label for="horasIniciarBusca">Buscar maquina (numero ou nome)</label>
                <input type="text" id="horasIniciarBusca" placeholder="Digite o numero ou o nome da maquina" oninput="horasFiltrarMaquinas()" autocomplete="off">
            </div>
            <div class="maquina-lista" id="horasIniciarLista">
                <?php foreach ($maquinas as $maquina): ?>
                    <div
                        class="maquina-item"
                        data-id="<?php echo (int) $maquina['D079_Id']; ?>"
                        data-busca="<?php echo horas_h(strtolower($maquina['D079_Id'] . ' ' . $maquina['D079_Nome'])); ?>"
                        onclick="horasSelecionarMaquina(this)"
                    >
                        <span class="maquina-item-info">
                            <span class="maquina-item-id">#<?php echo (int) $maquina['D079_Id']; ?></span>
                            <span class="maquina-item-nome"><?php echo horas_h($maquina['D079_Nome']); ?></span>
                        </span>
                        <span class="maquina-item-check">&#10003;</span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($maquinas)): ?>
                    <div class="maquina-item-vazio">Nenhuma maquina cadastrada.</div>
                <?php endif; ?>
            </div>
            <div class="modal-error" id="horasIniciarErro"></div>
            <div class="modal-actions">
                <button type="button" class="button" onclick="horasFecharIniciar()">Cancelar</button>
                <button type="submit" class="button primary">Confirmar</button>
            </div>
        </form>
    </div>
</div>
<div class="modal-overlay" id="horasModalFinalizar">
    <div class="modal-box">
        <h3>Finalizar apontamento</h3>
        <form method="post" id="horasFormFinalizar" action="./apontarHoras.php?op_id=<?php echo (int) $opId; ?>&item_id=<?php echo (int) $itemId; ?>" onsubmit="return horasValidarFinalizar(event)">
            <input type="hidden" name="acao" value="finalizar">
            <input type="hidden" name="item_id" value="<?php echo (int) $itemId; ?>">
            <input type="hidden" name="op_id" value="<?php echo (int) $opId; ?>">
            <input type="hidden" name="smt009_id" id="horasFinalizarSmt009Id" value="">
            <div class="modal-field">
                <label for="horasFinalizarMotivo" id="horasFinalizarMotivoLabel"><?php echo $opTemPlanoCorte ? 'Motivo da parada (opcional)' : 'Motivo da parada (obrigatorio se nao completar a quantidade do item)'; ?></label>
                <select name="smt012_id" id="horasFinalizarMotivo" onchange="horasAtualizarObrigaQuantidade()">
                    <option value="" data-obriga="S">Motivo da parada</option>
                    <?php foreach ($motivos as $motivo): ?>
                        <option value="<?php echo (int) $motivo['SMT012_Id']; ?>" data-obriga="<?php echo horas_h($motivo['SMT012_Obriga_Apontar_Quantidade'] === 'N' ? 'N' : 'S'); ?>"><?php echo horas_h($motivo['SMT012_Descricao']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!$opTemPlanoCorte): ?>
                <div class="modal-field">
                    <label for="horasFinalizarApontada">Quantidade apontada</label>
                    <input type="number" name="quantidade_apontada" id="horasFinalizarApontada" min="0" step="0.0001" value="0">
                </div>
                <div class="modal-field">
                    <label for="horasFinalizarRefugada">Quantidade refugada</label>
                    <input type="number" name="quantidade_refugada" id="horasFinalizarRefugada" min="0" step="0.0001" value="0">
                </div>
                <div class="modal-field">
                    <span id="horasFinalizarInfoQuantidade"></span>
                </div>
            <?php endif; ?>
            <div class="modal-error" id="horasFinalizarErro"></div>
            <div class="modal-actions">
                <button type="button" class="button" onclick="horasFecharFinalizar()">Cancelar</button>
                <button type="submit" class="button danger">Confirmar</button>
            </div>
        </form>
    </div>
</div>
<script>
function horasAbrirIniciar(t262Id) {
    document.getElementById('horasIniciarT262Id').value = t262Id;
    document.getElementById('horasIniciarD079Id').value = '';
    document.getElementById('horasIniciarBusca').value = '';
    document.getElementById('horasIniciarErro').textContent = '';
    var itens = document.querySelectorAll('#horasIniciarLista .maquina-item');
    itens.forEach(function (item) {
        item.classList.remove('selecionado');
        item.style.display = '';
    });
    document.getElementById('horasModalIniciar').classList.add('active');
}
function horasFecharIniciar() {
    document.getElementById('horasModalIniciar').classList.remove('active');
}
function horasFiltrarMaquinas() {
    var termo = document.getElementById('horasIniciarBusca').value.trim().toLowerCase();
    var itens = document.querySelectorAll('#horasIniciarLista .maquina-item');
    itens.forEach(function (item) {
        var busca = item.getAttribute('data-busca') || '';
        item.style.display = (termo === '' || busca.indexOf(termo) !== -1) ? '' : 'none';
    });
}
function horasSelecionarMaquina(elemento) {
    var itens = document.querySelectorAll('#horasIniciarLista .maquina-item');
    itens.forEach(function (item) { item.classList.remove('selecionado'); });
    elemento.classList.add('selecionado');
    document.getElementById('horasIniciarD079Id').value = elemento.getAttribute('data-id');
    document.getElementById('horasIniciarErro').textContent = '';
}
function horasValidarIniciar(event) {
    var d079Id = document.getElementById('horasIniciarD079Id').value;
    if (!d079Id) {
        document.getElementById('horasIniciarErro').textContent = 'Selecione uma maquina.';
        event.preventDefault();
        return false;
    }
    return true;
}
var HORAS_OP_TEM_PLANO_CORTE = <?php echo json_encode((bool) $opTemPlanoCorte); ?>;
var horasFinalizarSomaAnterior = 0;
var horasFinalizarQuantidadeAlvo = 0;
function horasFormatarQtd(valor) {
    return valor.toFixed(3).replace('.', ',');
}
function horasAbrirFinalizar(smt009Id, somaAnterior, quantidadeAlvo) {
    horasFinalizarSomaAnterior = somaAnterior || 0;
    horasFinalizarQuantidadeAlvo = quantidadeAlvo || 0;
    document.getElementById('horasFinalizarSmt009Id').value = smt009Id;
    document.getElementById('horasFinalizarMotivo').value = '';
    document.getElementById('horasFinalizarErro').textContent = '';
    // Com plano de corte esses campos nao sao renderizados (a tela
    // apontar.php controla a quantidade), entao so mexe neles se existirem.
    var apontadaInput = document.getElementById('horasFinalizarApontada');
    var refugadaInput = document.getElementById('horasFinalizarRefugada');
    var infoQuantidade = document.getElementById('horasFinalizarInfoQuantidade');
    if (apontadaInput) {
        apontadaInput.value = '0';
    }
    if (refugadaInput) {
        refugadaInput.value = '0';
    }
    if (infoQuantidade) {
        infoQuantidade.textContent =
            'Quantidade deste processo: ' + horasFormatarQtd(horasFinalizarQuantidadeAlvo) +
            ' | Ja apontado neste processo: ' + horasFormatarQtd(horasFinalizarSomaAnterior) +
            ' | Falta: ' + horasFormatarQtd(Math.max(0, horasFinalizarQuantidadeAlvo - horasFinalizarSomaAnterior));
    }
    document.getElementById('horasModalFinalizar').classList.add('active');
}
function horasFecharFinalizar() {
    document.getElementById('horasModalFinalizar').classList.remove('active');
}
function horasMotivoObrigaQuantidade() {
    var motivoSelect = document.getElementById('horasFinalizarMotivo');
    var opcaoSelecionada = motivoSelect.options[motivoSelect.selectedIndex];
    return !opcaoSelecionada || opcaoSelecionada.getAttribute('data-obriga') !== 'N';
}
function horasAtualizarObrigaQuantidade() {
    document.getElementById('horasFinalizarErro').textContent = '';
}
function horasValidarFinalizar(event) {
    if (HORAS_OP_TEM_PLANO_CORTE) {
        // Com plano de corte, a tela apontar.php e' quem controla a
        // quantidade produzida/refugada - aqui aceita qualquer valor.
        return true;
    }
    var apontadaInput = document.getElementById('horasFinalizarApontada');
    var refugadaInput = document.getElementById('horasFinalizarRefugada');
    var motivoSelect = document.getElementById('horasFinalizarMotivo');
    var apontada = parseFloat(String(apontadaInput.value || '0').replace(',', '.')) || 0;
    var refugada = parseFloat(String(refugadaInput.value || '0').replace(',', '.')) || 0;
    if (horasMotivoObrigaQuantidade() && apontada <= 0 && refugada <= 0) {
        document.getElementById('horasFinalizarErro').textContent = 'Informe a quantidade apontada ou a quantidade refugada (nao pode deixar os dois zerados).';
        event.preventDefault();
        return false;
    }
    var somaTotal = horasFinalizarSomaAnterior + apontada + refugada;
    if (somaTotal > horasFinalizarQuantidadeAlvo + 0.001) {
        document.getElementById('horasFinalizarErro').textContent = 'A soma de todos os apontamentos deste processo (apontada + refugada) nao pode ultrapassar a quantidade deste processo (' + horasFormatarQtd(horasFinalizarQuantidadeAlvo) + '). Soma informada: ' + horasFormatarQtd(somaTotal) + '.';
        event.preventDefault();
        return false;
    }
    if (somaTotal < horasFinalizarQuantidadeAlvo - 0.001 && !motivoSelect.value) {
        document.getElementById('horasFinalizarErro').textContent = 'Como a quantidade apontada ainda nao completa o total do item, selecione o motivo da parada.';
        event.preventDefault();
        return false;
    }
    return true;
}
</script>
</body>
</html>
