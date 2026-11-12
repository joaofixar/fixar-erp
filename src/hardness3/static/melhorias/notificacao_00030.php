<?php
namespace hardness;

$data = '2024-03-04';

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
            04/03/2024 - ICMS - Alterações alíquotas ICMS para 2024. $videonovo
        </div>
        <div align='center' id='melhoriaMinimizar30' style='text-align=center;cursor:pointer;margin:0px;padding:5px;font-size:16px;font-weight:bold'>-</div>
        <iframe id='melhoriaVideo30' width="832" height="400" src="https://www.youtube.com/embed/vcJj2_8t9k0?si=UPqm3VPRas1YHos2" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
EOT;




