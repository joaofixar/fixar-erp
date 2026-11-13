<?php
namespace hardness;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

function painelPersonalizadoAtalhoInicio()
{
    $contexto = painelPersonalizadoContexto();
    $config = painelPersonalizadoConfiguracao($contexto['empresa']);
    if (!painelPersonalizadoPermitidos($config, $contexto)) return '';
    $token = painelPersonalizadoCriarTokenSessao();
    if ($token === false) return '';
    $url = htmlspecialchars('/hardness3/painel.php#session=' . $token, ENT_QUOTES, 'UTF-8');
    return <<<HTML
<a id="painelPersonalizadoAcessoInicio" href="{$url}" target="_blank" rel="noopener noreferrer" title="Abrir painéis de controle em uma nova aba">
    <span class="material-icons painel-atalho-icone" aria-hidden="true">tv</span>
    <span class="painel-atalho-texto"><strong>Painel TV</strong><small>Controles para TV</small></span>
    <span class="material-icons" aria-hidden="true">open_in_new</span>
</a>
<style>
#painelPersonalizadoAcessoInicio { display: flex !important; flex-direction: row !important; align-items: center !important; gap: 14px; min-width: 260px; padding: 18px 21px; border: 1px solid #087c83; border-radius: 12px; background: #fff; box-shadow: 0 12px 30px rgba(25,55,100,.16); color: #075b65 !important; text-decoration: none !important; text-shadow: none !important; font: 800 19px/1.1 Arial, sans-serif; }
#painelPersonalizadoAcessoInicio:hover { background: #eaf8f7; }
#painelPersonalizadoAcessoInicio .painel-atalho-icone { display: flex !important; align-items: center; justify-content: center; width: 43px; height: 43px; border-radius: 10px; background: #e4f5f2; font-size: 25px; }
#painelPersonalizadoAcessoInicio .painel-atalho-texto { display: flex !important; flex: 1; flex-direction: column !important; gap: 3px; }
#painelPersonalizadoAcessoInicio strong { color: #075b65 !important; }
#painelPersonalizadoAcessoInicio small { font-size: 12px; font-weight: 600; color: #617495 !important; }
</style>
HTML;
}
