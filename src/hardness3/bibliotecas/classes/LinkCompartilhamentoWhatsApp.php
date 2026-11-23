<?php
namespace hardness;

class LinkCompartilhamentoWhatsApp
{
    public static function gerar($urlRaiz, $arquivo)
    {
        // Usa um domínio completo nos testes locais para favorecer a identificação do link.
        // localtest.me resolve para 127.0.0.1: o PDF continua no próprio computador.
        $urlRaiz = preg_replace('~^(https?://)(?:localhost|127\.0\.0\.1|\[::1\])(?=[:/]|$)~i', '${1}localtest.me', trim($urlRaiz));
        return rtrim($urlRaiz, '/') . '/hardness3/' . ltrim($arquivo, '/');
    }
}
