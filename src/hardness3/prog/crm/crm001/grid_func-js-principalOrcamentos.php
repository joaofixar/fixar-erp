<?php
namespace hardness;
global $g;

echo <<<EOT
    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        $('#ven001GridPrincipalOrcamentos, #{$g['divId']} button[botaoid="ven001GridPrincipalOrcamentos"]').unbind('click').unbind('click').bind('click', function(data) {
            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '', [750,650]);
        });
        $('#ven001GridPrincipalOrcamentosConsumidor, #{$g['divId']} button[botaoid="ven001GridPrincipalOrcamentosConsumidor"]').unbind('click').unbind('click').bind('click', function(data) {
            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&consumidor=true', [650,550]);
        });
    } </script></div>
EOT;
