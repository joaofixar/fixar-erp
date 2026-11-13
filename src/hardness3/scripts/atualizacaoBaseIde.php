<?php
namespace hardness;
set_time_limit(0);
ini_set('default_charset', 'utf-8');
carregarBibliotecaFramework('bibliotecas/funcoesGerais.php');

// para previnir a re-declaração das funções ao executar a atualização base para todas as empresas
if (isset($atualizacaoTodasEmpresas)) {
    require_once('/var/www/eurosul.hardness.com.br/hardness3/scripts/atualizacaoBase-funcoes.php');
} else {
    require_once('scripts/atualizacaoBase-funcoes.php');
}

// Limpar tabelas CRONTAB (serão reinseridas abaixo)
if (!$g['atualizacaoBaseSomenteApps']) {
    mysql_query("DELETE FROM D183 WHERE D183_Desenvolvimento IS NULL OR D183_Desenvolvimento = 0");
} else if ($g['modoDesenvolvedorApp']) {
    // Só é removido quando publicar o App, até lá não muda
    //mysql_query("DELETE FROM D183 WHERE D183_Desenvolvimento = 1 AND D183_App_Id = '{$g['modoDesenvolvedorApp']}'");
} else {
    mysql_query("DELETE FROM D183 WHERE (D183_Desenvolvimento IS NULL OR D183_Desenvolvimento = 0) AND D183_App_Id IS NOT NULL AND D183_App_Id != ''");
}

// Tabelas/índices existentes hoje
$tabelas = array();
$indices = array();
$query = "SHOW TABLES";
$tabelas_ = array_map(function ($a) {
    return $a[0];
}, mysqli_fetch_all(mysqli_query($query)));
$tabelasFiltro = array_filter($tabelas_, function ($a) {
    global $g;
    return preg_match('/^[A-Z]{3}[0-9]{3}$/i', $a) || preg_match('/^[A-Z][0-9]{3}A$/i', $a);
});
foreach ($tabelasFiltro as $tabela) {
    // Campos
    $tabela = strtoupper($tabela);
    $query = "SHOW FULL COLUMNS FROM `{$confUsuario["dbDatabase"]}`.`{$tabela}`";
    $campos = mysqli_fetch_all(mysqli_query($query), MYSQLI_ASSOC);
    foreach ($campos as $campo) {
        $tabelas[$tabela][$campo['Field']] = $campo;
    }
    // Índices
    $query = "SHOW INDEX FROM `{$confUsuario["dbDatabase"]}`.{$tabela}";
    $indices_ = [];
    $res = mysqli_query($query);
    while ($index = mysqli_fetch_assoc($res)) {
        if ($index['Key_name'] == 'PRIMARY') {
            continue;
        }
        if (!$indices_[$index['Key_name']]) {
            $indices_[$index['Key_name']] = array(
                'nome' => $index['Key_name'],
                'campos' => array(),
                'unique' => $index['Non_unique'] == 0
            );
        }
        $indices_[$index['Key_name']]['campos'][] = $index['Column_name'];
    }
    $indices[$tabela] = $indices_;
}

for ($i = 0; $i < 3; $i++) {
    if ($i === 0) {
        $dir = 'ide/';
    } else if ($i === 1) {
        $dir = Apps::getDirAtivos();
    } else {
        $dir = Apps::getDirEditor();
    }
    if (
        !$dir
        || ($g['atualizacaoBaseSomenteApps'] && $i === 0)
        || ($g['modoDesenvolvedorApp'] && $i === 1)
    ) {
        continue;
    }
    // Tabelas, campos e índices
    if (file_exists("{$dir}personalizacaoBanco/atualizacaoBaseIde.txt")) {
        // Tabelas script IDE
        $atualizacaoBaseIde = file("{$dir}personalizacaoBanco/atualizacaoBaseIde.txt");
        foreach ($atualizacaoBaseIde as $linha) {
            $linha = trim($linha);
            $item = explode("\t", $linha);
            if ($item[0] == 'TABELA') {
                // Nova tabela
                if (!isset($tabelas[$item[1]])) {
                    $query = $item[2];
                    //echo $query . "\n<br/>";
                    mysql_query($query);
                }
            }
            if ($item[0] == 'CAMPO') {
                // Campos novos ou alteração
                if (!isset($tabelas[$item[1]][$item[2]])) {
                    $query = "ALTER TABLE {$item[1]} ADD {$item[2]} $item[3]";
                } else {
                    $query = "ALTER TABLE {$item[1]} MODIFY {$item[2]} $item[3]";
                }
                //echo $query . "\n<br/>";
                mysql_query($query);
            }
            if ($item[0] == 'INDEX') {
                /*
                Exemplo:
                [0] => INDEX
                [1] => HRD001
                [2] => UNIQUE               // Pode ser: (vazio), UNIQUE ou DROP
                [3] => I_HRD001_CEP
                [4] => HRD001_CEP
                */
                // Índice
                $existe = false;
                $criar = false;
                if ($indices[$item[1]][$item[3]]) {
                    if ($item[2] == 'DROP') {
                        $query = "DROP INDEX {$item[3]} ON `{$confUsuario["dbDatabase"]}`.{$item[1]}";
                        mysql_query($query);
                    } else {
                        $existe = true;
                        $index = $indices[$item[1]][$item[3]];
                        if (
                            ($item[2] == 'UNIQUE' && !$index['unique'])
                            || (empty($item[2]) && $index['unique'])
                            || ($item[4] != implode(', ', $index['campos']))
                        ) {
                            $criar = true;
                        }
                    }
                } else {
                    $criar = $item[2] != 'DROP';
                }
                if ($criar) {
                    if ($existe) {
                        $query = "DROP INDEX {$item[3]} ON `{$confUsuario["dbDatabase"]}`.{$item[1]}";
                        mysql_query($query);
                    }
                    $query = "CREATE {$item[2]} INDEX {$item[3]} ON `{$confUsuario["dbDatabase"]}`.{$item[1]} ({$item[4]})";
                    mysql_query($query);
                }
            }
            if ($item[0] == 'INSERT') {
                $into = '';
                $values = '';
                $valores = unserialize(base64_decode($item[2]));
                // Tratamento especial para tabela de crontab de apps (modo desenvolvimento): não substituir
                if ($item[1] == 'D183') {
                    if ($g['modoDesenvolvedorApp']) {
                        $valores['D183_Desenvolvimento'] = 1;
                    }
                    if ($valores['D183_Desenvolvimento'] == '1') {
                        $sql = "SELECT D183_Id FROM D183 WHERE D183_App_Id = '{$valores['D183_App_Id']}' AND D183_Desenvolvimento = 1 AND D183_Nome = '{$valores['D183_Nome']}'";
                        $D183 = mysql_fetch_assoc(mysql_query($sql));
                        if (!empty($D183)) {
                            continue;
                        }
                    }
                }
                // Loop
                foreach ($valores as $key => $value) {
                    // Tratamento especial para tabela de crontab de apps: IDs podem conflitar
                    if ($item[1] == 'D183' && $key == 'D183_Id') { continue; }
                    $into .= $into ? ", {$key}" : $key;
                    $values .= $values ? ", '" . mysql_real_escape_string($value) . "'" : "'" . mysql_real_escape_string($value) . "'";
                }
                $sql = "INSERT INTO {$item[1]} ({$into}) VALUES ({$values})";
                mysql_query($sql);
            }
        }
    }

    // Triggers, procedures e functions
    foreach (scandir("{$dir}personalizacaoBanco/codigos") as $arquivo) {
        if ($arquivo == '.' || $arquivo == '..') {
            continue;
        }
        $arquivo = explode('-', $arquivo, 2);
        Atualizar_Rotina($arquivo[0], str_replace('.sql', '', $arquivo[1]));
    }
}

if (file_exists('ide/personalizacaoBanco/atualizacaoBase.php')) {
    require_once('ide/personalizacaoBanco/atualizacaoBase.php');
}

//// Fórmulas: criação das procedures
//$tabelas = Personalizacoes::formulasListarTabelas();
//$tabelasCriadas = array();
//foreach ($tabelas as $tabela) {
//    $tabelasCriadas[] = strtoupper($tabela);
//    mysqli_query("DROP PROCEDURE IF EXISTS `{$tabela}_Formulas`;");
//    $procedure = Personalizacoes::formulasGerarProcedure($tabela);
//    if ($procedure) {
//        mysqli_query($procedure);
//    }
//}
//apc_delete("cacheFormulas-{$confUsuario['dbDatabase']}");
//// Fórmulas: limpar não mais utilizadas
//foreach ($tabelas_ as $tabela) {
//    $tabela = strtoupper($tabela);
//    if (!in_array($tabela, $tabelasCriadas)) {
//        mysqli_query("DROP PROCEDURE IF EXISTS `{$tabela}_Formulas`;");
//    }
//}