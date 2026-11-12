<?php
 $conf['restricaoIncluirUpdate']['T202_T004_Id'] = <<<'EOT'
    $retorno = rVerificaFK('T004_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T202_Descricao'] = <<<'EOT'
    $retorno = rRegx('naoVazio', $valor);
EOT;
?>