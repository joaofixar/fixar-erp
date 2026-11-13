<?php

namespace hardness;

use NFePHP\DA\NFe\Danfce;

class DanfeContingencia
{
    public static function gerarDanfeNFCeOffline($xml, $caminhoLogoPNG): string
    {
        try {
            // $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(realpath(__DIR__ . '/../images/logo.jpeg')));
            // $logo = realpath(__DIR__ . '/../images/logo-nfce.png');

            $danfce = new Danfce($xml);
            $danfce->debugMode(false); // seta modo debug, deve ser false em produção
            $danfce->setPaperWidth(80); // seta a largura do papel em mm max=80 e min=58
            $danfce->setMargins(2); // seta as margens
            $danfce->setDefaultFont('arial'); // altera o font pode ser 'times' ou 'arial'
            $danfce->setOffLineDoublePrint(true); // ativa ou desativa a impressão conjunta das via do consumidor e da via do estabelecimento qnado a nfce for emitida em contingência OFFLINE
            // $danfce->setPrintResume(true); //ativa ou desativa a impressao apenas do resumo
            // $danfce->setViaEstabelecimento(); //altera a via do consumidor para a via do estabelecimento, quando a NFCe for emitida em contingência OFFLINE
            // $danfce->setAsCanceled(); //força marcar nfce como cancelada
            $danfce->creditsIntegratorFooter('HARDNESS SISTEMAS - www.hardness.com.br', false);

            return $danfce->render($caminhoLogoPNG);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
