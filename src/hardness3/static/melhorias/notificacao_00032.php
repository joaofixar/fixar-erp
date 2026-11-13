<?php
namespace hardness;

$data = '2024-09-19';

$diferenca = strtotime(date("Y-m-d")) - strtotime($data);
$dias = floor($diferenca / (60 * 60 * 24));

$videonovo = "";
$destaque = "";
if ($dias<=7) {
    $videonovo = "<blink><font style='background:red;color:white;font-weight:bold;padding:3px;border-radius: .425rem;'>Novo!</font></blink>";
    $destaque = "font-weight:bold;";
}

echo <<<EOT
    <div class='notificacao'>
        <div style='margin:0px;padding:5px;$destaque'>
            19/09/2024 - Estoque - APP RMA 2.0. $videonovo
        </div>
        <div align='center' id='melhoriaMinimizar32' style='text-align=center;cursor:pointer;margin:0px;padding:5px;font-size:16px;font-weight:bold'>-</div>
        <iframe id='melhoriaVideo32' width="832" height="400" src="https://www.youtube.com/embed/gQ0bCJZmVoA?si=dFhvD-ULWfnPeSl7" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
EOT;




