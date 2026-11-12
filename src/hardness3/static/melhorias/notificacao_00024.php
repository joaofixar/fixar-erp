<?php
namespace hardness;

$data = '2022-09-26';

$diferenca = strtotime(date("Y-m-d")) - strtotime($data);
$dias = floor($diferenca / (60 * 60 * 24));

$videonovo = "";
if ($dias<=7) {
    $videonovo = "<blink><font style='background:red;color:white;font-weight:bold;padding:3px;border-radius: .425rem;'>Novo!</font></blink>";
}

echo <<<EOT
    <div class='notificacao'>
        <div style='margin:0px;padding:5px'>
            26/09/2022 - Cadastro de Cliente/Fornecedor - Consulta Sintegra. $videonovo
        </div>
        <div align='center' id='melhoriaMinimizar24' style='text-align=center;cursor:pointer;margin:0px;padding:5px;font-size:16px;font-weight:bold'>-</div>
        <iframe id='melhoriaVideo24' width="832" height="400" src="https://www.youtube.com/embed/ZvDO9baGVcw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
EOT;




