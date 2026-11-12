<?php
namespace hardness;

$D001_Id  = $_REQUEST['D001Id'];
$nomeArquivo = $_REQUEST['NomeArq'];
$urlRaiz = $confUsuario['urlRaiz'];
$dbDatabase = $confUsuario['dbDatabase'];

$urlImagem = "dados_usuarios/{$dbDatabase}/produtos/{$D001_Id}/fotos/{$nomeArquivo}";

if (file_exists($urlImagem)) {
    $fp = fopen($urlImagem, 'rb');

    header("Content-Type: image/png");
    header("Content-Length: " . filesize($urlImagem));

    fpassthru($fp);
} else {
    return http_response_code(404);
}

// http://localhost/outros/buscarArquivos/imagensProdutos?ajax=true&noEmp=true&p=1&u=63&D001Id=79129&NomeArq=23.png
// http://grafer.smartness.com.br//hardness3/dados_usuarios/e012/produtos/79129/fotos/23.png