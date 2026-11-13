<?php
namespace hardness;

/**
 * Ações
 */
switch ($g['r'][3]) {
    case 'etc018OutroLog':
        $arquivo = Personalizacoes::getDirLogsCrontab() . $r_tabela . '-' . $r_acaoId . '.log';
        $conteudo = shell_exec("tail -n 100 {$arquivo}");
        echo <<<EOT
<pre>
{$conteudo}
</pre>
EOT;

    break;
}