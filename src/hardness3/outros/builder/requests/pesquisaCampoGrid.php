<?php
namespace hardness;
session_write_close();

require_once('../../../bibliotecas/confUsuario.php');
require_once('../../../bibliotecas/conexaoBanco.php');
include_once("../../../configuracoes/sysConfig.php");
require_once('../../../bibliotecas/funcoesGerais.php');

require_once('../classes/modelos/Campo.php');

$busca = str_replace(" ", "%", $_GET["q"]);
$tabelasEmUso = json_decode($_GET["tabelas"]);
$tabelasEmUso_ = implode(',', array_map(function($item) { return "'{$item}'"; }, $tabelasEmUso));

// Tabelas permitidas (JOINs possíveis)
$query = <<<EOT
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE FROM information_schema.columns
WHERE TABLE_SCHEMA = '{$confUsuario["dbDatabase"]}' AND TABLE_NAME IN ({$tabelasEmUso_}) AND COLUMN_NAME LIKE '%_Id'
ORDER BY TABLE_NAME,ORDINAL_POSITION;
EOT;
$tabelasPermitidas = [];
$tabelasPermitidasJoin = [];
foreach (mysqli_fetch_all(mysqli_query($query), MYSQLI_ASSOC) as $item) {
    $tabela = $item['COLUMN_NAME'];
    $ehChavePrimaria = preg_match("/^[^_]+_Id$/", $tabela, $matches) ? true : false;
    if ($ehChavePrimaria) {
        $tabela = preg_replace("/_Id$/", "", $tabela);
    } else {
        preg_match("/^(?<join>.*)_(?<tabela>.+)_Id$/", $tabela, $matches);
        $tabela = $matches["tabela"];
        if (!$tabelasPermitidasJoin[$tabela]) {
            $tabelasPermitidasJoin[$tabela] = $matches["join"];
        }
    }
    if (!in_array($tabela, $tabelasPermitidas, true)) {
        array_push($tabelasPermitidas, $tabela);
    }
}
$tabelasPermitidas_ = implode(',', array_map(function($item) { return "'{$item}'"; }, $tabelasPermitidas));

// Busca
$extra = empty($tabelasPermitidas) ? '' : "AND TABLE_NAME IN ({$tabelasPermitidas_})";
$query = <<<EOT
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE FROM information_schema.columns
WHERE TABLE_SCHEMA = '{$confUsuario["dbDatabase"]}' AND COLUMN_NAME LIKE '%{$busca}%' {$extra}
ORDER BY TABLE_NAME,ORDINAL_POSITION;
EOT;
$resultado = [];
foreach (mysqli_fetch_all(mysqli_query($query), MYSQLI_ASSOC) as $busca) {
    $resultado[] = [
        "campo" => $busca["COLUMN_NAME"],
        "tipo" => Campo::tipoMysqlCampo($busca["COLUMN_TYPE"]),
        "tabela" => strtoupper($busca["TABLE_NAME"]),
        "join" => isset($tabelasPermitidasJoin[strtoupper($busca["TABLE_NAME"])]) ? $tabelasPermitidasJoin[strtoupper($busca["TABLE_NAME"])] : null
    ];
}

echo json_encode($resultado);
die();
