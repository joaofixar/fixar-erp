<?php
namespace hardness;

$data = '2026-01-15';

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
            15/01/2026 - Reforma Tributária: Novos campos de tributação regular $videonovo
        </div>
        <div align='center' id='melhoriaMinimizar41' style='text-align=center;cursor:pointer;margin:0px;padding:5px;font-size:16px;font-weight:bold'>-</div>
        <iframe id='melhoriaVideo41' width="832" height="400" src="https://www.youtube.com/embed/0Q0WUbuRwAg?si=fOFmLqeR51xoFn9O" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>

EOT;
