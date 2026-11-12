<?php
namespace hardness;
set_time_limit(0);
ini_set('default_charset', 'utf-8');
carregarBibliotecaFramework('bibliotecas/funcoesGerais.php');

if (isset($atualizacaoTodasEmpresas)) {
    require_once('/var/www/eurosul.hardness.com.br/hardness3/scripts/atualizacaoBase-funcoes.php');
} else {
    require_once('scripts/atualizacaoBase-funcoes.php');
}

include('scripts/atualizacaoBase-rotinas-lista.php');
include('scripts/atualizacaoBase-globais-lista.php');

echo <<<HTML
<div style="margin:15px; padding:14px 18px; border:1px solid #cfe3c1; background:#eef8e7; color:#2f5d1d; border-radius:6px; font-family:Arial,Helvetica,sans-serif; font-size:14px;">
    Atualização concluída com sucesso. Functions, procedures e triggers foram atualizadas.
</div>
HTML;
