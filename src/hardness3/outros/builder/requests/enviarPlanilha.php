<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

// Criação da tabela
if ($_GET["enviar"]) {
    $uploadId = uniqid();
    $nome = basename($_FILES['fileToUpload']['name']);
    $uploaddir = $g['pathDadosArquivos'];
    $uploadfile = $uploaddir . '/' . $uploadId;

    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadfile)) {
        // Nova planilha
        $planilhaId = uniqid();
        $g['projeto']['planilhas'][$planilhaId] = [
            "id" => $planilhaId,
            "nomeArquivo" => $nome,
            "arquivoId" => $uploadId,
        ];

        // Cria (e associa) automaticamente uma entidade para cada arquivo enviado
        $entidadeId = uniqid();
        $g['projeto']['entidades'][$entidadeId] = [
            'id' => $entidadeId,
            'nome' => Util::padronizarNome(preg_replace('/\.csv$/', '', $nome)),
            'auditoria' => 'S'
        ];
        $g['projeto']['planilhas'][$planilhaId]['entidade'] = $entidadeId;

        Util::salvarProjeto();
        //echo "carregarPlanilha('{$uploadId}', '{$nome}');";
        echo "carregarListarPlanilhas(); carregarNomesEntidades()";
    } else {
        echo "alert('Erro ao enviar o arquivo.');";
    }
    die();
}
?>
<form action="#" class="dropzone" id="upload"></form>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    planilhaAberta = '';
    carregarListarPlanilhas();

    const uploader = new Dropzone('#upload', {
        url: '/hardness3/outros/builder/requests/enviarPlanilha.php?enviar=true',
        dictDefaultMessage: "Clique ou arraste um ou mais planilhas.",
        paramName: "fileToUpload",
        // maxFilesize: 10, // MB
        // maxFiles: 1,
        timeout: 0,
        success: function(file, response) {
            eval(response);
        }
    });
} </script></div>

<br /><br />

<div id="listarPlanilhas"></div>

<br /><br />

<div id="editarPlanilha"></div>
