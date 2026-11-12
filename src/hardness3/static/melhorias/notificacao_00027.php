<?php
namespace hardness;

$data = '2023-03-01';

$diferenca = strtotime(date("Y-m-d")) - strtotime($data);
$dias = floor($diferenca / (60 * 60 * 24));

$videonovo = "";
if ($dias<=7) {
    $videonovo = "<blink><font style='background:red;color:white;font-weight:bold;padding:3px;border-radius: .425rem;'>Novo!</font></blink>";
}

echo <<<EOT
    <div class='notificacao'>
        <div style='margin:0px;padding:5px'>
            01/03/2023 - Alterações na alíquota do ICMS para alguns estados em 2023. $videonovo
        </div>
        <div align='center' id='melhoriaMinimizar27' style='text-align=center;cursor:pointer;margin:0px;padding:5px;font-size:16px;font-weight:bold'>-</div>
        <iframe id='melhoriaVideo27' width="832" height="400" src="https://www.youtube.com/embed/y0SbsJu2aTw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    </div>
EOT;




