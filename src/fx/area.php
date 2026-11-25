<?php
namespace hardness;

// A faixa vazia mostra os avisos no filtro de hoje e as notas publicadas.
// A nota mora em notas.json até existir a tela de publicar. Vídeo não entra.

function fxBancoTemAvisoLocal(string $banco): bool
{
    return $banco !== 'e001';
}

function fxUrlImagemAviso(string $raiz, string $web, string $banco, string $arquivo): string
{
    if ($arquivo === '') {
        return '';
    }
    return $raiz . $web . 'dados_usuarios/' . $banco . '/imagens/avisos/' . $arquivo;
}

function fxSqlAvisos(int $perfil, int $empresa, int $usuario): string
{
    return "SELECT C032_Id, C032_Aviso, C032_Imagem, C007_Nome
        FROM C032
        LEFT JOIN C036 ON C036_C032_Id = C032_Id
        LEFT JOIN C007 ON C007_Id = {$usuario}
        WHERE C032_Flag_Ativo = 'S'
          AND (C036_C021_Id = {$perfil} OR IFNULL(C036_C021_Id, 0) = 0)
          AND (C036_C004_Id = {$empresa} OR IFNULL(C036_C004_Id, 0) = 0)
          AND (C036_C007_Id = {$usuario} OR IFNULL(C036_C007_Id, 0) = 0)
          AND (CURDATE() >= C036_Data_Inicial OR IFNULL(C036_Data_Inicial, '0000-00-00') = '0000-00-00')
          AND (CURDATE() <= C036_Data_Final OR IFNULL(C036_Data_Final, '0000-00-00') = '0000-00-00')
        GROUP BY C032_Id, C032_Aviso, C007_Nome";
}

function fxTextoAviso(string $html, string $nome): string
{
    return str_replace('[NOME_USUARIO]', $nome, $html);
}

function fxLerNotas(string $json): array
{
    $lista = json_decode($json, true);
    if (!is_array($lista)) {
        return array();
    }
    $notas = array();
    foreach ($lista as $item) {
        if (!is_array($item) || !empty($item['video'])) {
            continue;
        }
        $data = isset($item['data']) ? (string) $item['data'] : '';
        $titulo = isset($item['titulo']) ? trim((string) $item['titulo']) : '';
        $texto = isset($item['texto']) ? trim((string) $item['texto']) : '';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) || $titulo === '' || $texto === '') {
            continue;
        }
        $notas[] = array('data' => $data, 'titulo' => $titulo, 'texto' => $texto);
    }
    usort($notas, function ($a, $b) {
        return strcmp($b['data'], $a['data']);
    });
    return $notas;
}

function fxEscapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function fxDataVisivel(string $iso): string
{
    $partes = explode('-', $iso);
    return $partes[2] . '/' . $partes[1] . '/' . $partes[0];
}

function fxHtmlArea(array $avisos, array $notas): string
{
    $html = '';
    foreach ($avisos as $aviso) {
        $texto = isset($aviso['texto']) ? (string) $aviso['texto'] : '';
        $imagem = isset($aviso['imagem']) ? (string) $aviso['imagem'] : '';
        if ($texto === '' && $imagem === '') {
            continue;
        }
        $fundo = $imagem === '' ? '' : ' style="background-image:url(' . fxEscapar($imagem) . ')"';
        $html .= '<div data-fx-aviso' . $fundo . '>' . $texto . '</div>';
    }
    foreach ($notas as $nota) {
        $html .= '<article data-fx-nota>'
            . '<time datetime="' . fxEscapar($nota['data']) . '">' . fxEscapar(fxDataVisivel($nota['data'])) . '</time>'
            . '<h2>' . fxEscapar($nota['titulo']) . '</h2>'
            . '<p>' . nl2br(fxEscapar($nota['texto']), false) . '</p>'
            . '</article>';
    }
    return $html;
}

function fxAreaTrabalho($g): string
{
    global $confUsuario;
    if (empty($g['usuarioAtual'])) {
        return '';
    }
    $avisos = array();
    $banco = isset($confUsuario['dbDatabase']) ? (string) $confUsuario['dbDatabase'] : '';
    if (fxBancoTemAvisoLocal($banco) && function_exists('mysql_query')) {
        $sql = fxSqlAvisos(
            (int) $g['perfilUsuario'],
            (int) $g['empresaAtual'],
            (int) $g['usuarioAtual']
        );
        $res = mysql_query($sql);
        if ($res) {
            $raiz = isset($confUsuario['urlRaiz']) ? (string) $confUsuario['urlRaiz'] : '';
            $web = isset($g['pathWeb']) ? (string) $g['pathWeb'] : '';
            while ($row = mysql_fetch_assoc($res)) {
                $nome = isset($row['C007_Nome']) ? (string) $row['C007_Nome'] : '';
                $bruto = isset($row['C032_Aviso']) ? (string) $row['C032_Aviso'] : '';
                $arquivo = isset($row['C032_Imagem']) ? (string) $row['C032_Imagem'] : '';
                $avisos[] = array(
                    'texto' => fxTextoAviso($bruto, $nome),
                    'imagem' => fxUrlImagemAviso($raiz, $web, $banco, $arquivo),
                );
            }
        }
    }
    $caminho = __DIR__ . '/notas.json';
    $json = is_file($caminho) ? (string) file_get_contents($caminho) : '[]';
    return fxHtmlArea($avisos, fxLerNotas($json));
}
