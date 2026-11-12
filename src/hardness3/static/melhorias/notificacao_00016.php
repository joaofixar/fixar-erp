<?php
namespace hardness;

$data = '2022-07-25';

$diferenca = strtotime(date("Y-m-d")) - strtotime($data);
$dias = floor($diferenca / (60 * 60 * 24));

$videonovo = "";
if ($dias<=7) {
    $videonovo = "<blink><font style='background:red;color:white;font-weight:bold;padding:3px;border-radius: .425rem;'>Novo!</font></blink>";
}

echo <<<EOT
    <div class='notificacao'>
        <div style='margin:0px;padding:5px'>
            25/07/2022 - Melhoria na baixa de títulos. $videonovo
        </div>
        <div align='center' id='melhoriaMinimizar16' style='text-align=center;cursor:pointer;margin:0px;padding:5px;font-size:16px;font-weight:bold'>-</div>
        <iframe id='melhoriaVideo16' width="832" height="400" src="https://www.youtube.com/embed/--NRbHdUd5o" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
EOT;




