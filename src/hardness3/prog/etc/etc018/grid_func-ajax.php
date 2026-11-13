<?php
namespace hardness;

if ($g['modoDesenvolvedor']) {
    Personalizacoes::$rootDir = 'ide/';
} else if ($g['modoDesenvolvedorApp']) {
    Personalizacoes::$rootDir = Apps::getDirEditor();
}

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]) {
    case 'personalizarCrontab':
        // Gerar ID único
        $sql = "UPDATE D183 SET D183_Uuid = UUID() WHERE D183_Id = '{$r_acaoId}' AND D183_Uuid IS NULL";
        mysql_query($sql);

        // Registro
        $sql = "SELECT * FROM D183 WHERE D183_Id = '{$r_acaoId}'";
        $D183 = mysql_fetch_assoc(mysql_query($sql));

        // Verificar se já foi adicionado
        $sql = "SELECT * FROM C039 WHERE C039_Uuid = '{$D183['D183_Uuid']}'";
        $C039 = mysql_fetch_assoc(mysql_query($sql));
        if (!empty($C039)) {
            die('Erro: tentando adicionar registro já existente.');
        }

        // Duplicar
        $sql = "INSERT INTO C039 (
            C039_Nome,
            C039_Codigo,
            C039_Codigo_Parametros,
            C039_Codigo_Sql,
            C039_Crontab,
            C039_Crontab_Extra,
            C039_Flag_Execucao,
            C039_Ultima_Execucao,
            C039_Grupo_Execucao,
            C039_Criacao,
            C039_Uuid
        ) VALUES (
            '" . mysql_real_escape_string($D183['D183_Nome']) . "',
            '" . mysql_real_escape_string($D183['D183_Codigo']) . "',
            '" . mysql_real_escape_string($D183['D183_Codigo_Parametros']) . "',
            '" . mysql_real_escape_string($D183['D183_Codigo_Sql']) . "',
            '" . mysql_real_escape_string($D183['D183_Crontab']) . "',
            '" . mysql_real_escape_string($D183['D183_Crontab_Extra']) . "',
            '" . mysql_real_escape_string($D183['D183_Flag_Execucao']) . "',
            '" . mysql_real_escape_string($D183['D183_Ultima_Execucao']) . "',
            '" . mysql_real_escape_string($D183['D183_Grupo_Execucao']) . "',
            '" . mysql_real_escape_string($D183['D183_Criacao']) . "',
            '" . mysql_real_escape_string($D183['D183_Uuid']) . "'
        )";
        mysql_query($sql);
        break;

    case 'executarCrontab':
        $resposta['data'] = '';
        $tabela = $r_tabela;
        $acaoId = $r_acaoId;
        $agora = new \DateTime();

        $crontab = mysql_fetch_assoc(mysql_query("SELECT * FROM {$tabela} WHERE {$tabela}_Id = '{$acaoId}'"));
        if (empty($crontab)) {
            $resposta['data'] .= "Crontab não encontrado, por favor, entre em contato com o suporte técnico.\n";
        } else {
            // Código igual ao: scripts/crontab.php

            // Executar
            $resposta['data'] .= "Executando {$confUsuario['dbDatabase']} - {$crontab[$tabela.'_Id']} - {$crontab[$tabela.'_Codigo']}...\n";
            mysql_query("UPDATE {$tabela} SET {$tabela}_Flag_Execucao = 'S' WHERE {$tabela}_Id = '{$crontab[$tabela.'_Id']}'");
            $retorno = executarCodigo($crontab[$tabela . '_Codigo'], array(), $crontab[$tabela . '_Codigo_Sql']);
            $retorno = is_array($retorno) ? print_r($retorno, true) : $retorno;

            // Registro e log
            $resposta['data'] .= "Data [{$agora->format('Y-m-d H:i:s')}] DB [{$confUsuario['dbDatabase']}] {$tabela}_Id [{$crontab[$tabela.'_Id']}] Código [{$crontab[$tabela.'_Codigo']}]: " . $retorno . "\n";
            mysql_query("UPDATE {$tabela} SET {$tabela}_Flag_Execucao = 'N', {$tabela}_Ultima_Execucao = NOW() WHERE {$tabela}_Id = '{$crontab[$tabela.'_Id']}'");
            file_put_contents(Personalizacoes::getDirLogsCrontab() . $tabela . '-' . $crontab[$tabela . '_Id'] . '.log', "Data [{$agora->format('Y-m-d H:i:s')}] DB [{$confUsuario['dbDatabase']}] {$tabela}_Id [{$crontab[$tabela.'_Id']}] Código [{$crontab[$tabela.'_Codigo']}]: " . $retorno . "\n", FILE_APPEND);
            $resposta['data'] = '<pre>' . htmlentities($resposta['data']) . '</pre>';
        }
        break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
